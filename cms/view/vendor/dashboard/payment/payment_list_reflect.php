<?php
include "../../../../model/model.php";
include_once('../../inc/vendor_generic_functions.php');
global $modify_entries_switch;
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];

$branch_status = $_POST['branch_status']; 
$vendor_type = $_POST['vendor_type'];
$vendor_type_id = $_POST['vendor_type_id'];
$estimate_type = $_POST['estimate_type'];
$estimate_type_id = $_POST['estimate_type_id'];
$array_s = array();
$temp_arr = array();
$query = "select * from vendor_payment_master where payment_amount!='0' and delete_status='0'";
if($financial_year_id!=""){
	$query .= " and financial_year_id='$financial_year_id'";
}
if($vendor_type!=""){
	$query .= " and vendor_type='$vendor_type'";
}
if($vendor_type_id!=""){
	$query .= " and vendor_type_id='$vendor_type_id'";
}
if($estimate_type!=""){
	$query .= " and estimate_type='$estimate_type'";
}
if($estimate_type_id!=""){
	$query .= " and estimate_type_id='$estimate_type_id'";
}

include "../../../../model/app_settings/branchwise_filteration.php";
// $query .= " order by payment_id desc ";
$total_paid_amt = 0;
$count = 0;

$sq_payment = mysqlQuery($query);		
$sq_pending_amount=0;
$sq_cancel_amount=0;
$sq_paid_amount=0;
$total_payment=0;
while($row_payment = mysqli_fetch_assoc($sq_payment)){
	$vendor_type_val = get_vendor_name($row_payment['vendor_type'], $row_payment['vendor_type_id']);

	$total_payment = $total_payment + $row_payment['payment_amount'];
	$estimate_type_val = get_estimate_type_name($row_payment['estimate_type'], $row_payment['estimate_type_id']);
	if($row_payment['clearance_status']=="Pending"){ 
		$bg='warning';
		$sq_pending_amount = $sq_pending_amount + $row_payment['payment_amount'];
	}
	else if($row_payment['clearance_status']=="Cancelled"){ 
		$bg='danger';
		$sq_cancel_amount = $sq_cancel_amount + $row_payment['payment_amount'];
	}
	else{
		$bg = '';
	}
	

	if($row_payment['payment_evidence_url']!=""){
		$url = explode('uploads', $row_payment['payment_evidence_url']);
		$url = BASE_URL.'uploads'.$url[1];
	}
	else{
		$url = "";
	}
	if($url!=""){
		$evidence = '<a class="btn btn-info btn-sm" href="'. $url .'" download data-toggle="tooltip" title="Download Payment Evidence slip"><i class="fa fa-download"></i></a>';
	}else{
		$evidence = '';
	}
	if($row_payment['payment_mode']!='Debit Note'){
		$update_btn = '<button class="btn btn-info btn-sm" onclick="payment_update_modal('.$row_payment['payment_id'].')"  title="Update Details" id="updatep2_btn-'. $row_payment['payment_id'] .'"><i class="fa fa-pencil-square-o"></i></button>';
	}else{
		$update_btn = '';
	}
	$row_estimate = mysqli_fetch_assoc(mysqlQuery("select estimate_type,estimate_type_id from vendor_payment_master where estimate_id='$row_payment[estimate_id]'"));
	$to_date = '';
	if($row_estimate['estimate_type'] == 'Group Tour'){

		$sq_booking = mysqli_fetch_assoc(mysqlQuery("select tour_group_id from tourwise_traveler_details where id='$row_estimate[estimate_type_id]'"));
		$sq_group = mysqli_fetch_assoc(mysqlQuery("select to_date from tour_groups where group_id='$sq_booking[tour_group_id]'"));
		$to_date = $sq_group['to_date'];
	}else if($estimate_type == 'Package Tour'){
		$sq_booking = mysqli_fetch_assoc(mysqlQuery("select tour_to_date from package_tour_booking_master where booking_id='$row_estimate[estimate_type_id]'"));
		$to_date = $sq_booking['tour_to_date'];
	}
	$today = date('Y-m-d');
	if(isset($to_date) && $to_date < $today && $modify_entries_switch == 'No' && $role != 'Admin' && $role != 'Branch Admin'){
		$update_btn = '';
	}


$currency_code_1 = $row_payment['currency_code'];

	$currency_amount1 = currency_conversion($currency,$currency_code_1,$row_payment['payment_amount']);
		if($currency_code_1!='0' && $currency != $currency_code_1){
			$currency_amount = ' ('.$currency_amount1.')';
		}else{
			$currency_amount = '';
		}

$payment_date = $row_payment['payment_date'];
		$yr = explode("-", $payment_date);
$year = $yr[0];
		
$payment_id = $row_payment['payment_id'];
		

	$temp_arr = array( "data" => array(
		(int)(++$count),
        get_vendor_payment_id($payment_id, $year),
		($row_payment['estimate_type'] =='')? 'NA': $row_payment['estimate_type'],
		($estimate_type_val == '') ? 'NA'  : $estimate_type_val ,
		$row_payment['vendor_type'],
		$vendor_type_val,
		date('d-m-Y', strtotime($row_payment['payment_date'])),
		$row_payment['payment_amount'].$currency_amount,
		$row_payment['payment_mode'],
		$row_payment['bank_name'],
		$row_payment['transaction_id'],
		''.$evidence.$update_btn.'<button class="'.$delete_flag.' btn btn-danger btn-sm" onclick="payment_delete_entry('.$row_payment['payment_id'].')" title="Delete Entry"><i class="fa fa-trash"></i></button>'
		), "bg" =>$bg);
	array_push($array_s,$temp_arr); 
	
}
$footer_data = array("footer_data" => array(
	'total_footers' => 5,
			
	'foot0' => "Total Amount : ".number_format($total_payment, 2),
	'col0' => 3,
	'class0' => "text-right info",
	
	'foot1' => "Pending Clearance : ".number_format($sq_pending_amount, 2),
	'col1' => 3,
	'class1' => "text-right warning",

	'foot2' => "Total Cancel : ".number_format($sq_cancel_amount, 2),
	'col2' => 2,
	'class2' => "text-right danger",

	'foot3' => "Total Paid : ".number_format(($total_payment - $sq_pending_amount - $sq_cancel_amount),2),
	'col3' => 2,
	'class3' => "text-right success",

	'foot4' => "",
	'col4' => 2,
	'class4' => "text-right success"
	)
);
array_push($array_s, $footer_data);
echo json_encode($array_s);
?>
	


