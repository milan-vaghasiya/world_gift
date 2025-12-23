<?php
class BiometricModel extends MasterModel{
    private $deviceMasterTable = "device_master";
    private $devicePunchesTable = "device_punches";
		
	public function syncDeviceData()
	{
        $ddQuery['tableName'] = $this->deviceMasterTable;
        $deviceData = $this->rows($ddQuery);
		if(!empty($deviceData)):
			foreach($deviceData as $row):
				$last_synced = (!empty($row->last_sync_at)) ? date('Y-m-d',strtotime($row->last_sync_at)) : date('Y-m-d',strtotime($row->issued_at));
				
				$begin = new DateTime( date( 'Y-m-d', strtotime( $last_synced . ' -2 day' ) ) );
				$end = new DateTime( date( 'Y-m-d' ));
				$end = $end->modify( '+1 day' ); 
				
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);
				
				foreach($daterange as $date){
					$currentDate =  date("Y-m-d",strtotime($date->format("Y-m-d")));
					$row->Empcode = 'ALL';
					$row->FromDate = date("d/m/Y_00:01",strtotime($date->format("Y-m-d")));
					$row->ToDate = date("d/m/Y_23:59",strtotime($date->format("Y-m-d")));
					$punchData = New StdClass();
					$punchData = $this->callDeviceApi($row);
					
					if(!empty($punchData)):
						$dd1Query['tableName'] = $this->devicePunchesTable;
						$dd1Query['where']['punch_date'] = $currentDate;
						$oldData = $this->row($dd1Query);
						$pnchData = Array();
						
						if(empty($oldData)):
							$pnchData = ['id'=>"",'device_id'=>$row->id, 'punch_date'=>$currentDate, 'punch_data'=>json_encode($punchData),'created_by'=>$this->loginID];
						else:
							$pnchData = ['id'=>$oldData->id, 'punch_date'=>$currentDate, 'punch_data'=>json_encode($punchData)];
						endif;
						$this->store($this->devicePunchesTable,$pnchData,'Attandance');
					endif;	
				}
				$updateSyncStatus = ['id'=>$row->id,'last_sync_at'=>date( 'Y-m-d H:i:s')];
				$this->store($this->deviceMasterTable,$updateSyncStatus,'Attandance');
			endforeach;
			return ['status'=>1,'message'=>'Device Synced successfully.','lastSyncedAt'=>date('j F Y, g:i a'),'field_error'=>0,'field_error_message'=>null];
		else:
			return ['status'=>0,'message'=>'You have no any Devices!','field_error'=>0,'field_error_message'=>null];
		endif;
	}
	
	public function getPunchData($FromDate,$ToDate,$device_id=2)
	{
		$queryData['tableName'] = $this->devicePunchesTable;
		$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
// 		$queryData['where']['device_id'] = $device_id;
        return $this->rows($queryData);
	}
	
	public function getPunchData1($FromDate,$ToDate,$device_id=2)
	{
		$queryData['tableName'] = $this->devicePunchesTable;
		$queryData['customWhere'][] = 'punch_date BETWEEN "'.date('Y-m-d',strtotime($FromDate)).'" AND "'.date('Y-m-d',strtotime($ToDate)).'"';
// 		$queryData['where']['device_id'] = $device_id;
        return $this->rows($queryData);
	}
	
	public function getDeviceData($device_id=1)
	{
		$ddQuery['tableName'] = $this->deviceMasterTable;
		$ddQuery['limit'] = 1;
        return $this->rows($ddQuery);
	}
	
	public function callDeviceApi($deviceData)
	{
		$punchData = New StdClass();
		$curl = curl_init();
		$api_url = "http://api.etimeoffice.com/api/DownloadPunchData?Empcode=".$deviceData->Empcode."&FromDate=".$deviceData->FromDate."&ToDate=".$deviceData->ToDate;
		curl_setopt_array($curl, array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array('Authorization: Basic '.$deviceData->device_token),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		if ($err) {echo "cURL Error #:" . $err;exit;}
		else 
		{
			$resultapi = json_decode($response);
			$punchData = $resultapi->PunchData;
		}
		return $punchData;
	}

}
?>