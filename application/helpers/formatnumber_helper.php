<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
     
function absNumber($number){
	return rtrim((strpos($number,".") !== false ? rtrim($number, "0") : $number),".");
}

function nestedTextCase($value,$case="U") {
	if (is_array($value)) {
		return array_map('nestedTextCase', $value);
	}
	if($case=="L"){return strtolower($value);}else{return strtoupper($value);}
}

function numberOfDecimals($value){
	if ((int)$value == $value)
	{
		return 0;
	}
	else if (! is_numeric($value))
	{
		// throw new Exception('numberOfDecimals: ' . $value . ' is not a number!');
		return false;
	}

	return strlen($value) - strrpos($value, '.') - 1;
}

function numToWordCurrency($number,$formatType="SPELLOUT",$currencyType=""){
    if(empty($currencyType)){$currencyType=locale_get_default();}
    if($formatType == "SPELLOUT"):
        $currencyFormat = new \NumberFormatter( $currencyType, \NumberFormatter::SPELLOUT );
    else:
        //$currencyFormat = new \NumberFormatter( $currencyType, \NumberFormatter::CURRENCY );
        $currencyFormat = new NumberFormatter($locale = 'en_IN', NumberFormatter::SPELLOUT);
    endif;
    return  ucwords($currencyFormat->format($number));
}

function formatDecimal($num,$format=''){
    return number_format($num,2);
    if(empty($format))
    {
        $nums = explode(".",$num); // 10000
        if(count($nums)>2){return "0";}
        else
        {
            if(count($nums)==1){$nums[1]="00";}
            $num = $nums[0];$explrestunits = "" ;
            if(strlen($num)>3){
                $lastthree = substr($num, strlen($num)-3, strlen($num)); // 000
                $restunits = substr($num, 0, strlen($num)-3); // 10
                $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; 
                $expunit = str_split($restunits, 2);
                for($i=0; $i<sizeof($expunit); $i++){if($i==0){$explrestunits .= (int)$expunit[$i].","; }else{$explrestunits .= $expunit[$i].",";}}
                $thecash = $explrestunits.$lastthree;
            }
            else {$thecash = $num;}
            return $thecash.".".$nums[1]; 
        }
    }
    else{return number_format((floatVal($num)) ,2);}
}

function numToWordEnglish($number){
	$number=number_format($number, 2, '.', '');
	$no=abs($number);
	$no1=$number;
	$hundred = null;
	$digits_1 = strlen($no);
	$i = 0;
	$str = array();
	$words = array('0' => '', '1' => 'One', '2' => 'Two',
	'3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
	'7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
	'10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
	'13' => 'Thirteen', '14' => 'Fourteen',
	'15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
	'18' => 'Eighteen', '19' =>'Nineteen', '20' => 'Twenty',
	'30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
	'60' => 'Sixty', '70' => 'Seventy',
	'80' => 'Eighty', '90' => 'Ninety');
	
	$dictionary  = array(
		0                   => 'Zero',
		1                   => 'One',
		2                   => 'Two',
		3                   => 'Three',
		4                   => 'Four',
		5                   => 'Five',
		6                   => 'Six',
		7                   => 'Seven',
		8                   => 'Eight',
		9                   => 'Nine',
		10                  => 'Ten',
		11                  => 'Eleven',
		12                  => 'Twelve',
		13                  => 'Thirteen',
		14                  => 'Fourteen',
		15                  => 'Fifteen',
		16                  => 'Sixteen',
		17                  => 'Seventeen',
		18                  => 'Eighteen',
		19                  => 'Nineteen',
		20                  => 'Twenty',
		30                  => 'Thirty',
		40                  => 'Fourty',
		50                  => 'Fifty',
		60                  => 'Sixty',
		70                  => 'Seventy',
		80                  => 'Eighty',
		90                  => 'Ninety',
		100                 => 'Hundred',
		1000                => 'Thousand',
		1000000             => 'Million',
		1000000000          => 'Billion',
		1000000000000       => 'Trillion',
		1000000000000000    => 'Quadrillion',
		1000000000000000000 => 'Quintillion'
	);
	
	$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
	while ($i < $digits_1) {
		$divider = ($i == 2) ? 10 : 100;
		$number = floor($no % $divider);
		$no = floor($no / $divider);
		$i += ($divider == 10) ? 1 : 2;
		if ($number) {
			$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
			$str [] = ($number < 21) ? $words[$number] .
				" " . $digits[$counter] . $plural . " " . $hundred
				:
				$words[floor($number / 10) * 10]
				. " " . $words[$number % 10] . " "
				. $digits[$counter] . $plural . " " . $hundred;
		} else $str[] = null;
	}
	$str = array_reverse($str);
	$result = implode('', $str);
  
	$decimal     = ' point ';
	$string1 = $fraction = null;
	
	$hundred = null;
	$str1=array();
	$i = 0;
	$point = "0";
	if (strpos($no1, '.') !== false) {$arno = explode('.',$no1);$point = $arno['1'];}else{$point = "0";}
	
	$fraction=$point;
	if($fraction>0)
	{
		$pl = strlen($point);
		if (null !== $fraction && is_numeric($fraction)) {
			$string1 .= $decimal;
			if($fraction<10){
				$string1 .= " Zero ";
			}
			
			$digits_2=$fraction;
			while ($i < $digits_2) {
				$divider = ($i == 2) ? 10 : 100;
				$number = floor($fraction % $divider);
				$fraction = floor($fraction / $divider);
				$i += ($divider == 10) ? 1 : 2;
				if ($number) {
					$plural = (($counter = count($str1)) && $number > 9) ? 's' : null;
					$hundred = ($counter == 1 && $str1[0]) ? ' and ' : null;
					$str1 [] = ($number < 21) ? $words[$number] .
						" " . $digits[$counter] . $plural . " " . $hundred
						:
						$words[floor($number / 10) * 10]
						. " " . $words[$number % 10] . " "
						. $digits[$counter] . $plural . " " . $hundred;
				} else $str1[] = null;
			}
			$str1 = array_reverse($str1);
			$string1 .= implode('', $str1);
		}
	}
	return $result.$string1." Only"; // writes the final format where $currency is the currency symbol.
}

function numToWordGujarati($number){
	$number=abs($number);
	if (strpos($number, '.') !== false) 
	{
		$arno = explode('.',$number);
		$no = $arno['0'];
		$point = $arno['1'];
	}
	else
	{
		$no = $number;
		$point = "0";
	}
	
	$pl = strlen($point);
	if($pl==1){$point=$point*10;}else{$point=(int)$point;}
	
	$hundred = null;
	$digits_1 = strlen($no);
	$i = 0;
	$str = array();
	$words = array(
			0=> 'શૂન્ય',1=> 'એક',2=> 'બે',3=> 'ત્રણ',4=> 'ચાર',5=> 'પાંચ',6=> 'છ',7=> 'સાત',8=> 'આઠ',9=> 'નવ',10=> 'દસ',11=> 'અગિયાર',12=> 'બાર',13=> 'તેર',
			14=> 'ચવુદ',15=> 'પંદર',16=> 'સોળ',17=> 'સતર',18=> 'અઢાર',19=> 'ઓગણીસ',20=>'વીસ',21=> "એકવીસ",	22=> "બાવીસ",23=> "તેવીસ",24=>  "ચોવીસ",
			25=> "પચ્ચીસ",26 => "છવીસ",27=> "સત્તાવીસ",28=>  "અઠ્ઠાવીસ",29=> "ઓગણત્રીસ",30=>"ત્રીસ",31=> "એકત્રીસ",32=>  "બત્રીસ",33=>  "તેત્રીસ",34 => "ચોત્રીસ",
			35=> "પાંત્રીસ",36=> "છત્રીસ",37=> "સડત્રીસ",38=>  "અડત્રીસ",39 => "ઓગણચાલીસ",40=>"ચાલીસ",41=>  "એકતાલીસ",42=>  "બેતાલીસ",43=> "ત્રેતાલીસ",
			44=> "ચુંમાલીસ",45=>  "પિસ્તાલીસ",46=> "છેતાલીસ",47=>  "સુડતાલીસ",48=> "અડતાલીસ",49=>  "ઓગણપચાસ",50=> "પચાસ",51=> "એકાવન",52=> "બાવન",
			53=>  "ત્રેપન",54=>  "ચોપન",55=>  "પંચાવન",56=> "છપ્પન",57=> "સત્તાવન",58=> "અઠ્ઠાવન",59=>  "ઓગણસાઠ",60=>  "સાઈઠ",61=>  "એકસઠ",62=> "બાસઠ",
			63=> "ત્રેસઠ",64=> "ચોસઠ",65=>  "પાંસઠ",66=>  "છાસઠ",67=>  "સડસઠ",68=>  "સડસઠ",69=>  "અગણોસિત્તેર",70=> "સિત્તેર",71=>  "એકોતેર",72=> "બોતેર ",
			73=> "તોતેર",74=> "ચુમોતેર",75=> "પંચોતેર",76=> "છોતેર",77=> "સિત્યોતેર",78 => "ઇઠ્યોતેર",79=>  "ઓગણાએંસી",80=>  "એંસી",81=>  "એક્યાસી",82=>"બ્યાસી",
			83=>  "ત્યાસી",84=>  "ચોર્યાસી",85=>  "પંચાસી",86=> "છ્યાસી",87=> "સિત્યાસી",88=> "ઈઠ્યાસી",89=>  "નેવ્યાસી",90=>  "નેવું",91=> "એકાણું",92=> "બાણું",
			93=>  "ત્રાણું",94=> "ચોરાણું",95=> "પંચાણું",96=> "છન્નું",97=> "સત્તાણું",98=> "અઠ્ઠાણું",99=> "નવ્વાણું"
	);
	
	$digits = array('', 'સો', 'હજાર', 'લાખ', 'કરોડ','અબજ','ખરવ');
	while ($i < $digits_1) 
	{
		$divider = ($i == 2) ? 10 : 100;
		$number = ($no % $divider);
		$no = ($no / $divider);
		$i += ($divider == 10) ? 1 : 2;
		if ($number) 
		{
			$plural = (($counter = count($str)) && $number > 9) ? '' : null;
			$hundred = ($counter == 1 && $str[0]) ? ' ' : null;
			$str [] = ($number < 100) ? $words[$number] .
				" " . $digits[$counter] . $plural . " " . $hundred
				:
				$words[($number / 10) * 10]
				. " " . $words[$number % 10] . " "
				. $digits[$counter] . $plural . " " . $hundred;
		} else $str[] = null;
	}
	$str = array_reverse($str);
	$result = implode('', $str);
	
	$rval="";
	if($result==""){$result= "શૂન્ય";}
	if($result=="શૂન્ય" and $words[$point]=="શૂન્ય"){$rval="શૂન્ય રૂપિયા";}
	elseif($words[$point]=="શૂન્ય"){$rval=$result . " રૂપિયા પુરા";}else{$rval=$result . " રૂપિયા" . " અને " .$words[$point] . " પૈસા પુરા";}
	//else{$rval=$result . " રૂપિયા" . " અને " .$words[$point] . " પૈસા પુરા";}
	
	return $rval;
	//return str_replace("બે સો","બસો",$rval);
}

function E2G($str){
	$gujdigit=array('૦','૧','૨','૩','૪','૫','૬','૭','૮','૯');
	$engdigit=array('0','1','2','3','4','5','6','7','8','9');
	$k=0;
	foreach($gujdigit as $gd)
	{
		$str=str_replace($engdigit[$k],$gujdigit[$k],$str);$k++;
	}
	return $str;
}

function G2E($str){
	$gujdigit=array('૦','૧','૨','૩','૪','૫','૬','૭','૮','૯');
	$engdigit=array('0','1','2','3','4','5','6','7','8','9');
	$k=0;
	foreach($gujdigit as $gd)
	{
		$str=str_replace($gujdigit[$k],$engdigit[$k],$str);$k++;
	}
	return $str;
}

function getGstType($gstin){
	$gstType = 3;
	if(!empty($gstin))
	{
		if(substr($gstin, 0, 2) != "24"):
			$gstType = 2;
		else:
			$gstType = 1;
		endif;
	}
	return $gstType;
}

function addTimeToDate($date,$time,$dateFormat='Y-m-d H:i:s',$type="H"){
	$newDate = '';
	switch($type)
	{
		case 'H': 
				$newDate .= date($dateFormat,strtotime($date) + ($time * 3600));break;
		case 'M': 
				$newDate .=	date($dateFormat,strtotime($date) + ($time * 3600 * 60));break;
		case 'S': 
				$newDate .=	date($dateFormat,strtotime($date) + ($time * 3600 * 60 * 60));break;
		case 'D': 
				$newDate .=	date($dateFormat,strtotime($date) + ($time * 3600 * 24));break;
	}
	
	return $newDate;
}

function containsWord($str, $word){
	return !!preg_match('#\\b' . preg_quote($word, '#') . '\\b#i', $str);
}

/*** For Reindexing First Array from Second Array ***/
function first(&$arr, $low, $high, $x, $n){
	if ($high >= $low)
	{
		$mid = intval($low + ($high - $low) / 2);
		if (($mid == 0 || $x > $arr[$mid - 1]) && $arr[$mid] == $x){return $mid;}				
		if ($x > $arr[$mid]){return first($arr, ($mid + 1), $high, $x, $n);}
		return first($arr, $low, ($mid - 1), $x, $n);
	}
	return -1;
}

function sortAccording(&$A1, &$A2, $m, $n){
	$temp = array_fill(0, $m, NULL);
	$visited = array_fill(0, $m, NULL);
	for ($i = 0; $i < $m; $i++){$temp[$i] = $A1[$i];$visited[$i] = 0;}
	sort($temp);
 
	$ind = 0;
 
	for ($i = 0; $i < $n; $i++)
	{
		$f = first($temp, 0, $m - 1, $A2[$i], $m);	 
		if ($f == -1) continue;	 
		for ($j = $f; ($j < $m && $temp[$j] == $A2[$i]); $j++){$A1[$ind++] = $temp[$j];$visited[$j] = 1;}
	}
 
	for ($i = 0; $i < $m; $i++){if ($visited[$i] == 0){$A1[$ind++] = $temp[$i];}}
}

function is_valid_gstin($gstin) {
	$regex = "/^([0][1-9]|[1-2][0-9]|[3][0-7])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$/";
	return preg_match($regex, $gstin);
}
	
function convertItemDataArray($mainArray){
	$collection = Array();
	foreach($mainArray["id"] as $key => $value )
	{
		$collection[$key] = ['id' => $value];
		foreach(array_keys($mainArray) as $arrayKeysOfmainArray)
		{
			if(!array_key_exists($arrayKeysOfmainArray, $collection))
			{
				$collection[$key][$arrayKeysOfmainArray] = $mainArray[$arrayKeysOfmainArray][$key];
			}
		}
	}
	return $collection;
}

function decodeURL($url){return json_decode(base64_decode(urldecode($url)));}

/* 
Created By : Milan Chauhan
Created At : 2023-03-28
Use : Get date formate financial year wise 
*/
function getFyDate($format = "Y-m-d",$date=""){
	$CI =& get_instance();
	$dates = explode(' AND ',$CI->session->userdata('financialYear'));

	$startYearDate = new DateTime($dates[0]);
	$endYearEnd = new DateTime($dates[1]);
	$interval = DateInterval::createFromDateString('1 month');
	$daterange = new DatePeriod($startYearDate, $interval ,$endYearEnd);

	$list=array();
	foreach($daterange as $row):
		$list[strval($row->format("m"))] = $row->format("m-Y");
	endforeach;

	$day = (!empty($date))?date('d',strtotime($date)):date('d');
	$month = (!empty($date))?date('m',strtotime($date)):date('m');
	$time = (!empty($date))?date('H:i:s',strtotime($date)):date('H:i:s');

	return date($format,strtotime($day.'-'.$list[strval($month)].' '.$time));
}
?>
