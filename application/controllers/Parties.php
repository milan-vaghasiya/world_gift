<?php
class Parties extends MY_Controller
{
    private $indexPage = "party/index";
    private $partyForm = "party/form";
    private $automotiveArray = ["1" => 'Yes', "2" => "No"];
    private $contactForm = "party/contact_form";
    private $personalForm = "party/personal_details";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Parties";
        $this->data['headData']->controller = "parties";
        $this->data['suppliedTypes'] = array('Goods', 'Services', 'Goods,Services');
        $this->data['vendorTypes'] = array('Manufacture', 'Service');
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "parties";
        $this->data['party_category'] = 1;
        $this->data['tableHeader'] = getSalesDtHeader("customer");
        $this->load->view($this->indexPage, $this->data);
    }

    public function vendor()
    {
        $this->data['headData']->pageUrl = "parties/vendor";
        $this->data['party_category'] = 2;
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function supplier()
    {
        $this->data['headData']->pageUrl = "parties/supplier";
        $this->data['party_category'] = 3;
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows()
    {
        $result = $this->party->getDTRows($this->input->post());
        
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addParty($party_category)
    {
        $this->data['party_category'] = $party_category;
        $this->data['currencyData'] = $this->party->getCurrency();
        $this->data['countryData'] = $this->party->getCountries();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['processDataList'] = array();//$this->process->getProcessList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['state'] = $this->party->getStates(101, 4030)['result'];
        $this->data['city'] = $this->party->getCities(4030, 133679)['result'];
        $this->load->view($this->partyForm, $this->data);
    }

    //Updated By Meghavi 15-03-2022
    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if (empty($data['party_phone']))
            $errorMessage['party_phone'] = "Contact No. is required.";            
        if($data['party_category'] != 1) {
            if (empty($data['supplied_types']))
                $errorMessage['supplied_types'] = 'Supplied Types are required.';
        }      
           
        if($data['gst_status'] == 1 || $data['gst_status'] == 3) {
            if (empty($data['gstin']))
                $errorMessage['gstin'] = 'Gst Number is required.';
        }  
        if(empty($data['city_id'])) {
            if (!empty($data['ctname'])){
                if(empty($data['country_id'])){
                    $errorMessage['country_id'] = 'Country is required.';
                }
                if(empty($data['state_id'])){
                    $errorMessage['state_id'] = 'State is required.';
                }
                if(!empty($data['state_id']) && !empty($data['country_id'])){
                    $data['city_id'] = $this->party->saveCity($data['ctname'], $data['state_id'], $data['country_id']);
                }
            }
        }
        unset($data['statename'], $data['processSelect']);unset($data['ctname']);
        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $data['party_name'] = ucwords($data['party_name']);
            $this->printJson($this->party->save($data));
        endif;
    }

    //Updated By Meghavi 15-03-2022
    public function edit()
    {
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $result->state = $this->party->getStates($result->country_id, $result->state_id)['result'];
        $result->city = $this->party->getCities($result->state_id, $result->city_id)['result'];
        $this->data['dataRow'] = $result;
        // $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['currencyData'] = $this->party->getCurrency();
        $this->data['countryData'] = $this->party->getCountries();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->load->view($this->partyForm, $this->data);
    }

    public function partyDetails()
    {
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $this->printJson($result);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else :
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getStates()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else :
            $this->printJson($this->party->getStates($id));
        endif;
    }

    public function getCities()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else :
            $this->printJson($this->party->getCities($id));
        endif;
    }

    public function partyApproval()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->party->getParty($id);
        $this->load->view("party/approval_form", $this->data);
    }

    public function savePartyApproval()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['approved_date']))
            $errorMessage['approved_date'] = "Approved Date is required.";
        if (empty($data['approved_by']))
            $errorMessage['approved_by'] = "Approved By is required.";
        if (empty($data['approved_base']))
            $errorMessage['approved_base'] = "Approved Base is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $data['approved_date'] = (!empty($data['approved_date'])) ? date('Y-m-d', strtotime($data['approved_date'])) : null;
            $this->printJson($this->party->savePartyApproval($data));
        endif;
    }


    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function getGstDetail()
    {
        $party_id = $this->input->post('id');
        $result = $this->party->getParty($party_id);

        $this->data['json_data'] = json_decode($result->json_data);
        $this->data['party_id'] = $party_id;
        $this->load->view($this->contactForm, $this->data);
    }
    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function saveGst()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['gstin']))
            $errorMessage['gstin'] = "GST is required.";
        if (empty($data['delivery_address']))
            $errorMessage['delivery_address'] = "Address is required.";
        if (empty($data['delivery_pincode']))
            $errorMessage['delivery_pincode'] = "Pincode is required.";
        if (empty($data['party_address']))
            $errorMessage['party_address'] = "Party Address is required.";
        if (empty($data['party_pincode']))
            $errorMessage['party_pincode'] = "Party Pincode is required.";
        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :

            $response = $this->party->saveGst($data);

            $result = $this->party->getParty($data['party_id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\')"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $data['party_id']]);
        endif;
    }
    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function deleteGst()
    {
        $party = $this->input->post();
        if (empty($party['id'])) :
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else :
            $this->party->deleteGst($party['id'], $party['gstin']);

            $result = $this->party->getParty($party['id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\');"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $party['id']]);
        endif;
    }

    /*Created By : Avruti @21-3-2022 */
    public function getPersonalDetail(){
        $party_id = $this->input->post('id');
        $result = $this->party->getParty($party_id); 
        $this->data['party_id'] = $party_id;
        $this->data['dataRow'] = $result; 
        $this->data['personalData'] = $this->party->getPersonalDataList($party_id);
        $this->load->view($this->personalForm,$this->data);
    }

    public function savePersonalDetail(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['special_date']))
			$errorMessage['special_date'] = "Date is required.";
		if(empty($data['name']))
			$errorMessage['name'] = "Name is required.";
       
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $response = $this->party->savePersonalDetail($data);
            $result = $this->party->getPersonalDataList($data['party_id']);
            $i=1;$tbodyData="";
            if(!empty($result)) :
                foreach ($result as $row) :
                    $type = ($row->type == 1) ? 'Birthday' : 'Anniversary';
                    $relation = '';
                    if($row->relation == 1):
                        $relation = 'Self';
                    elseif($row->relation == 2):
                        $relation = 'Daughter';
                    elseif($row->relation == 3):
                        $relation = 'Son';
                    elseif($row->relation == 4):
                        $relation = 'Friend';
                    elseif($row->relation == 5):
                        $relation = 'Spouse';
                    else:
                        $relation = 'Cousin';
                    endif;    
                    $deleteParam = $row->id.','.$data['party_id'].",'Personal Data'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->special_date.'</td>
                            <td>'.$row->name.'</td>
                            <td>'.$type.'</td>
                            <td>'.$relation.'</td>
                            <td class="text-center">';
                                $tbodyData.= '<a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashPersonalDetail('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                    $tbodyData.='</td></tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "partyId"=>$data['party_id']]);
		
        endif;
    }

    public function deletePersonalDetail(){
        $id = $this->input->post('id');
        $party_id = $this->input->post('party_id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->party->deletePersonalDetail($id);

            $result = $this->party->getPersonalDataList($party_id);
            $i=1;$tbodyData=""; 
            if(!empty($result)) :
                foreach ($result as $row) :
                    $type = ($row->type == 1) ? 'Birthday' : 'Anniversary';
                    $relation = '';
                    if($row->relation == 1):
                        $relation = 'Self';
                    elseif($row->relation == 2):
                        $relation = 'Daughter';
                    elseif($row->relation == 3):
                        $relation = 'Son';
                    elseif($row->relation == 4):
                        $relation = 'Friend';
                    elseif($row->relation == 5):
                        $relation = 'Spouse';
                    else:
                        $relation = 'Cousin';
                    endif;    
                    $deleteParam = $row->id.",'Personal Data'";
                    $tbodyData.= '<tr>
                            <td>'.$i.'</td>
                            <td>'.$row->special_date.'</td>
                            <td>'.$row->name.'</td>
                            <td>'.$type.'</td>
                            <td>'.$relation.'</td>
                            <td class="text-center">';
                                $tbodyData.= '<a class="btn btn-outline-danger btn-delete" href="javascript:void(0)" onclick="trashPersonalDetail('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
                    $tbodyData.='</td></tr>'; $i++;
                endforeach;
            else:
                $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
            endif;
			$this->printJson(['status'=>1, "tbodyData"=>$tbodyData, "partyId"=>$party_id]);
        endif;
    }
}
