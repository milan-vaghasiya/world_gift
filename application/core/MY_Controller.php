<?php 
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class MY_Controller extends CI_Controller{
	
	
	public function __construct(){
		parent::__construct();
		//echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
		$this->isLoggedin();
		$this->data['headData'] = new StdClass;
		$this->load->library('form_validation');
		
		$this->load->model('masterModel');
		$this->load->model('DashboardModel','dashboard');
		$this->load->model('TermsModel','terms');
		$this->load->model('MasterOptionsModel', 'masterOption');
		$this->load->model('StoreModel','store');
		$this->load->model('PartyModel','party');
		$this->load->model('ItemModel','item');
		$this->load->model('ItemCategoryModel','itemCategory');
		$this->load->model('PurchaseRequestModel','purchaseRequest');
		$this->load->model('PurchaseEnquiryModel','purchaseEnquiry');
		$this->load->model('PurchaseOrderModel','purchaseOrder');
		$this->load->model('PurchaseInvoiceModel','purchaseInvoice');		

		$this->load->model('SalesEnquiryModel','salesEnquiry');
		$this->load->model('SalesOrderModel','salesOrder');
		$this->load->model('DeliveryChallanModel','challan');
		$this->load->model('SalesInvoiceModel','salesInvoice');
		$this->load->model('SalesQuotationModel','salesQuotation');
		$this->load->model('ReportModel','reportModel');
		$this->load->model('TransactionMainModel','transModel');
		$this->load->model('ProformaInvoiceModel','proformaInv');

		$this->load->model('StockVerificationModel', 'stockVerify');
		$this->load->model('ShiftModel', 'shiftModel');
		$this->load->model('StockJournalModel', 'stockJournal');
		$this->load->model('FamilyGroupModel','familyGroup');
		
		$this->load->model('MainMenuConfModel','mainMenuConf');
		$this->load->model('SubMenuConfModel','subMenuConf');
		
		/*** Account Model ***/
		$this->load->model('LedgerModel','ledger');
		$this->load->model('PaymentVoucherModel','paymentVoucher');
		$this->load->model('GroupModel','group');

		/***  Report Model ***/
		$this->load->model('report/StoreReportModel', 'storeReportModel');
		$this->load->model('report/SalesReportModel', 'salesReportModel');
		$this->load->model('report/PurchaseReportModel', 'purchaseReport');
		$this->load->model('report/AccountingReportModel', 'accountingReport');
		$this->load->model('report/GstReportModel', 'gstReport');
		$this->load->model('ProductReporModel', 'productReporModel');
		
		/*** HR Model ***/
		$this->load->model('hr/DepartmentModel','department');
		$this->load->model('hr/EmployeeModel','employee');
		$this->load->model('hr/AttendanceModel','attendance');
		$this->load->model('hr/LeaveModel','leave');
		$this->load->model('hr/LeaveSettingModel','leaveSetting');
		$this->load->model('hr/LeaveApproveModel','leaveApprove');
		$this->load->model('hr/PayrollModel','payroll');
		$this->load->model('PermissionModel','permission');
		$this->load->model('hr/ManualAttendanceModel','manualAttendance');
		$this->load->model('hr/ExtraHoursModel','extraHours');
		$this->load->model('hr/DesignationModel','designation');
		$this->load->model('hr/BiometricModel','biometric');
		
		$this->load->model('ExpenseMasterModel','expenseMaster');
		$this->load->model('TaxMasterModel','taxMaster');
		$this->load->model('DebitNoteModel','debitNote');
		$this->load->model('CreditNoteModel','creditNote');
		$this->load->model('GstExpenseModel','gstExpense');
		$this->load->model('JournalEntryModel','journalEntry');
		$this->load->model('ContactDirectoryModel','contactDirectory');
		$this->load->model('OffersModel','offers');
		$this->load->model('ProductionLogModel','productionLog');		
		$this->load->model('HsnModel','hsn');

		$this->load->model('EbillModel','ebill');

		$this->data['currentFormDate'] = $this->session->userdata("currentFormDate");
		$this->financialYearList = $this->getFinancialYearList($this->session->userdata('issueDate'));	

		$this->setSessionVariables('store,party,item,itemCategory,purchaseEnquiry,purchaseOrder,purchaseInvoice,salesEnquiry,salesOrder,salesInvoice,reportModel,department,employee,attendance,leave,leaveSetting,leaveApprove,payroll,purchaseRequest,transModel,masterOption,stockVerify,shiftModel,proformaInv,storeReportModel,salesReportModel,purchaseReport,permission,stockJournal,ledger,familyGroup,paymentVoucher,mainMenuConf,subMenuConf,group,expenseMaster,taxMaster,debitNote,creditNote,biometric,designation,extraHours,manualAttendance,gstExpense,journalEntry,accountingReport,contactDirectory,productionLog,gstReport,hsn,ebill');
		
		$this->data['stockTypes'] = [-1=>'Opening Stock',0=>'',1=>'GRN', 2=>'Purchase Invoice', 3=>'Material Issue', 4=>'Delivery Challan', 5=>'Sales Invoice', 6=>'Manual Manage Stock', 7=>'Production Finish', 8 =>'Visual Inspection', 9 =>'Store Transfer', 10=>'Return Stock From Production', 11=>'In Challan', 12=>'Out Challan', 13=>'Tools Issue', 14 =>'Stock Journal', 15 =>'Packing Material', 16 =>'Packing Product', 17 =>'Rejection Scrap', 18 =>'Production Scrap', 19 =>'Credit Note', 20 =>'Debit Note', 21=>'Stock Verification', 22 =>'Stock Verification', 23 =>'Process Movement', 24 =>'Production Rejection', 25 =>'Proforma Invoice', 99=>'Stock Adjustment'];
	}

	public function setSessionVariables($modelNames)
	{
		$this->data['dates'] = explode(' AND ',$this->session->userdata('financialYear'));
		$this->shortYear = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
		$this->startYear = date('Y',strtotime($this->data['dates'][0]));
		$this->endYear = date('Y',strtotime($this->data['dates'][1]));
		$this->startYearDate = $this->data['startYearDate'] = date('Y-m-d',strtotime($this->data['dates'][0]));
		$this->endYearDate = $this->data['endYearDate'] = date('Y-m-d',strtotime($this->data['dates'][1]));
		
		
		$this->loginId = $this->session->userdata('loginId');
		$this->userName = $this->session->userdata('user_name');
		$this->userRole = $this->session->userdata('role');
		$this->userRoleName = $this->session->userdata('roleName');
		
		$this->RTD_STORE = $this->session->userdata('RTD_STORE');
		$this->GIF_STORE = $this->session->userdata('GIF_STORE');
		//$this->PKG_STORE = $this->session->userdata('PKG_STORE');
		//$this->PROD_STORE = $this->session->userdata('PROD_STORE');
		$this->CMID = $this->data['CMID'] = $this->session->userdata('CMID');
		
		$models = explode(',',$modelNames);
		foreach($models as $modelName):
			$modelName = trim($modelName);
			$this->{$modelName}->loginID = $this->session->userdata('loginId');
			$this->{$modelName}->userName = $this->session->userdata('user_name');
			$this->{$modelName}->userRole = $this->session->userdata('role');
			$this->{$modelName}->userRoleName = $this->session->userdata('roleName');
			
			$this->{$modelName}->dates = $this->data['dates'];
			$this->{$modelName}->shortYear = date('y',strtotime($this->data['dates'][0])).'-'.date('y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYear = date('Y',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYear = date('Y',strtotime($this->data['dates'][1]));
			$this->{$modelName}->startYearDate = date('Y-m-d',strtotime($this->data['dates'][0]));
			$this->{$modelName}->endYearDate = date('Y-m-d',strtotime($this->data['dates'][1]));
			
			$this->{$modelName}->RTD_STORE = $this->session->userdata('RTD_STORE');
			$this->{$modelName}->GIF_STORE = $this->session->userdata('GIF_STORE');
			//$this->{$modelName}->PKG_STORE = $this->session->userdata('PKG_STORE');
			//$this->{$modelName}->PROD_STORE = $this->session->userdata('PROD_STORE');
		    $this->{$modelName}->CMID = $this->session->userdata('CMID');
		endforeach;
		return true;
	}
	
	public function getFinancialYearList($issueDate){
		$startYear  = ((int)date("m",strtotime($issueDate)) >= 4) ? date("Y",strtotime($issueDate)) : (int)date("Y",strtotime($issueDate)) - 1;
		$endYear  = ((int)date("m") >= 4) ? date("Y") + 1 : (int)date("Y");
		
		$startDate = new DateTime($startYear."-04-01");
		$endDate = new DateTime($endYear."-03-31");
		$interval = new DateInterval('P1Y');
		$daterange = new DatePeriod($startDate, $interval ,$endDate);
		$fyList = array();$val="";$label="";
		foreach($daterange as $dates)
		{
			$start_date = date("Y-m-d H:i:s",strtotime("01-04-".$dates->format("Y")." 00:00:00"));
			$end_date = date("Y-m-d H:i:s",strtotime("31-03-".((int)$dates->format("Y") + 1)." 23:59:59"));
			
			$val = $start_date." AND ".$end_date;
			$label = 'Year '.date("Y",strtotime($start_date)).'-'.date("Y",strtotime($end_date));
			$fyList[] = ["label" => $label, "val" => $val];
		}
		return $fyList;
	}
	
	public function isLoggedin(){
		if(!$this->session->userdata("LoginOk")):
			//redirect( base_url() );
			echo '<script>window.location.href="'.base_url().'";</script>';
		endif;
		return true;
	}
	
	public function printJson($data){
		print json_encode($data);exit;
	}

	public function printDecimal($val){
		return number_format($val,0,'','');
	}
	
	public function checkGrants($url){
		$empPer = $this->session->userdata('emp_permission');
		if(!array_key_exists($url,$empPer)):
			redirect(base_url('error_403'));
		endif;
		return true;
	}
	
	/**** Generate QR Code ****/
	public function getQRCode($qrData,$dir,$file_name){
		if(isset($qrData) AND isset($file_name))
		{
			$file_name .= '.png';
			/* Load QR Code Library */
			$this->load->library('ciqrcode');
			
			if (!file_exists($dir)) {mkdir($dir, 0775, true);}

			/* QR Configuration  */
			$config['cacheable']    = true;
			$config['imagedir']     = $dir;
			$config['quality']      = true;
			$config['size']         = '1024';
			$config['black']        = array(255,255,255);
			$config['white']        = array(255,255,255);
			$this->ciqrcode->initialize($config);
	  
			/* QR Data  */
			$params['data']     = $qrData;
			$params['level']    = 'L';
			$params['size']     = 10;
			$params['savename'] = FCPATH.$config['imagedir']. $file_name;
			
			$this->ciqrcode->generate($params);

			return $dir. $file_name;
        }
		else
		{
			return '';
		}
	}
	
	public function importExcelFile($file,$path,$sheetName){
		$item_excel = '';
		if(isset($file['name']) || !empty($file['name']) ):
			$this->load->library('upload');
			$_FILES['userfile']['name']     = $file['name'];
			$_FILES['userfile']['type']     = $file['type'];
			$_FILES['userfile']['tmp_name'] = $file['tmp_name'];
			$_FILES['userfile']['error']    = $file['error'];
			$_FILES['userfile']['size']     = $file['size'];
			
			$imagePath = realpath(APPPATH . '../assets/uploads/'.$path);
			$config = ['file_name' => "".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path' =>$imagePath];

			$this->upload->initialize($config);
			if (!$this->upload->do_upload()):
				$errorMessage['item_excel'] = $this->upload->display_errors();
				$this->printJson(["status"=>0,"message"=>$errorMessage]);
			else:
				$uploadData = $this->upload->data();
				$item_excel = $uploadData['file_name'];
			endif;
			if(!empty($item_excel)):
				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($imagePath.'/'.$item_excel);
				$fileData = array($spreadsheet->getSheetByName($sheetName)->toArray(null,true,true,true));
				return $fileData;
			else:
				return ['status'=>0,'message'=>'Data not found...!'];
			endif;
		else:
			return ['status'=>0,'message'=>'Please Select File!'];
		endif;
    }
}
?>