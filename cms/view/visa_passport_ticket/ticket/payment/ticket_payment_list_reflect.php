<?php
include "../../../../model/model.php";

$customer_id = isset($_POST['customer_id']) ?$_POST['customer_id'] : '';
$ticket_id = $_POST['ticket_id'];
$payment_mode = $_POST['payment_mode'];
$financial_year_id = $_SESSION['financial_year_id'];
$payment_from_date = $_POST['payment_from_date'];
$payment_to_date = $_POST['payment_to_date'];
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = isset($_POST['branch_status']) ? $_POST['branch_status'] : '';

$query = "select * from ticket_payment_master where 1 and payment_amount!='0'";		
if($financial_year_id!=""){
	$query .= " and financial_year_id='$financial_year_id'";
}
if($ticket_id!=""){
	$query .= " and ticket_id='$ticket_id'";
}
if($payment_mode!=""){
	$query .= " and payment_mode='$payment_mode'";
}
if($customer_id!=""){
	$query .= " and ticket_id in (select ticket_id from ticket_master where customer_id='$customer_id')";
}
if($payment_from_date!='' && $payment_to_date!=''){
	$payment_from_date = get_date_db($payment_from_date);
	$payment_to_date = get_date_db($payment_to_date);
	$query .=" and payment_date between '$payment_from_date' and '$payment_to_date'";
}
if($cust_type != ""){
	$query .= " and ticket_id in (select ticket_id from ticket_master where customer_id in ( select customer_id from customer_master where type='$cust_type' ))";
}
if($company_name != ""){
	$query .= " and ticket_id in (select ticket_id from ticket_master where customer_id in ( select customer_id from customer_master where company_name='$company_name' ))";
}
if($role == "B2b"){
	$query .= " and ticket_id in (select ticket_id from ticket_master where emp_id ='$emp_id')";
}
if($branch_status=='yes'){
	if($role=='Branch Admin' || $role=='Accountant' || $role_id>'7'){
		$query .= " and branch_admin_id = '$branch_admin_id'";
	}
	elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
		
		if($role == 'Backoffice' && $show_entries_switch == 'Yes'){
			$query .=" and branch_admin_id = '$branch_admin_id'";
		}else{
			$query .= " and ticket_id in (select ticket_id from ticket_master where emp_id='$emp_id')  and branch_admin_id = '$branch_admin_id'";			
		}
	}
}
elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
	if($role == 'Backoffice' && $show_entries_switch == 'Yes'){
		$query .=" and branch_admin_id = '$branch_admin_id'";
	}else{
		$query .= " and ticket_id in (select ticket_id from ticket_master where emp_id='$emp_id')  and branch_admin_id = '$branch_admin_id'";			
	}
}
// $query .= " order by payment_id desc ";
$count = 0;
$sq_pending_amount=0;
$sq_cancel_amount=0;
$sq_paid_amount=0;
$total_payment=0;
$array_s = array();
$temp_arr = array();
$footer_data = array();
$sq_ticket_payment = mysqlQuery($query);

// echo($query);die;
while($row_ticket_payment = mysqli_fetch_assoc($sq_ticket_payment)){

	$count++;
	$sq_pay = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum,sum(credit_charges) as sumc from ticket_payment_master where clearance_status!='Cancelled' and ticket_id='$row_ticket_payment[ticket_id]'"));

	$sq_ticket_info = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id='$row_ticket_payment[ticket_id]' "));
	$total_sale = $sq_ticket_info['ticket_total_cost'] + $sq_pay['sumc'];
	$cancel_amount = $sq_ticket_info['cancel_amount'];

	$total_pay_amt = $sq_pay['sum'] + $sq_pay['sumc'];
	$outstanding =  0;
	if($sq_ticket_info['cancel_type'] == '1'){
		if($total_pay_amt > 0){
			if($cancel_amount >0){
				if($total_pay_amt > $cancel_amount){
					$outstanding = 0;
				}else{
					$outstanding = $cancel_amount - $total_pay_amt;
				}
			}else{
				$outstanding = 0;
			}
		}
		else{
			$outstanding = $cancel_amount;
		}
	}else if($sq_ticket_info['cancel_type'] == '2'||$sq_ticket_info['cancel_type'] == '3'){

		$cancel_estimate_data = json_decode($sq_ticket_info['cancel_estimate']);
		$cancel_estimate = (!isset($cancel_estimate_data)) ? 0 : $cancel_estimate_data[0]->ticket_total_cost;
		$balance_amount = (($total_sale - floatval($cancel_estimate)) + $cancel_amount) - $total_pay_amt;
	}
	else{
		$outstanding = $total_sale - $total_pay_amt;
	}

	$date = $sq_ticket_info['created_at'];
	$yr = explode("-", $date);
	$year = $yr[0];
	$date1 = $row_ticket_payment['payment_date'];
	$yr1 = explode("-", $date1);
	$year1 = $yr1[0];

	$bg='';
	$sq_depa_date = mysqli_fetch_assoc(mysqlQuery("select * from ticket_trip_entries where ticket_id ='$row_ticket_payment[ticket_id]'"));

	$customer_name = '';
	$guest_name = '';
	$sq_customer_count = mysqli_num_rows(mysqlQuery("select * from customer_master where customer_id='$sq_ticket_info[customer_id]'"));
	if($sq_customer_count > 0){
		$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_ticket_info[customer_id]'"));
		$sq_flight = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from ticket_master_entries where ticket_id='$row_ticket_payment[ticket_id]' "));
		$guest_name = (($sq_customer_info['type']=='Corporate' || $sq_customer_info['type'] == 'B2B') && $sq_flight['first_name']!='') ? '('.$sq_flight['first_name'].' '.$sq_flight['last_name'].')' : '';
		if($sq_customer_info['type'] == 'Corporate'||$sq_customer_info['type'] == 'B2B'){
			$customer_name = $sq_customer_info['company_name'];
		}else{
			$customer_name = $sq_customer_info['first_name'].' '.$sq_customer_info['last_name'];
		}
	}

	if($row_ticket_payment['clearance_status']=="Pending"){ 
		$bg='warning';
		$sq_pending_amount = $sq_pending_amount + $row_ticket_payment['payment_amount'] + $row_ticket_payment['credit_charges'];
	}
	else if($row_ticket_payment['clearance_status']=="Cancelled"){ 
		$bg='danger';
		$sq_cancel_amount = $sq_cancel_amount + $row_ticket_payment['payment_amount'] + $row_ticket_payment['credit_charges'];
	}
	else if($row_ticket_payment['clearance_status']=="Cleared"){ 
		$bg='success';
	}
	else if($row_ticket_payment['clearance_status']==""){ 
		$bg='';
	}
	$sq_paid_amount = $sq_paid_amount + $row_ticket_payment['payment_amount'] + $row_ticket_payment['credit_charges'];

	$payment_id_name = "Flight Ticket Payment ID";
	$payment_id = get_ticket_booking_payment_id($row_ticket_payment['payment_id'],$year1);
	$receipt_date = date('d-m-Y');
	$booking_id = get_ticket_booking_id($row_ticket_payment['ticket_id'],$year);
	$customer_id = $sq_ticket_info['customer_id'];
	$booking_name = "Flight Ticket Booking";
	$travel_date = date('d-m-Y',strtotime($sq_depa_date['departure_datetime']));
	$payment_amount = $row_ticket_payment['payment_amount']+$row_ticket_payment['credit_charges'];
	$payment_mode1 = $row_ticket_payment['payment_mode'];
	$transaction_id = $row_ticket_payment['transaction_id'];
	$payment_date = date('d-m-Y',strtotime($row_ticket_payment['payment_date']));
	$bank_name = $row_ticket_payment['bank_name'];
	$receipt_type = "Flight Ticket Receipt";
    $sq_flight = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from ticket_master_entries where ticket_id='$row_ticket_payment[ticket_id]' "));




	if($row_ticket_payment['currency_code'] == ''){
			$currency_code_2 = $sq_ticket_info['currency_code'];
			


		  }
		  else{

				
			$currency_code_2 = $row_ticket_payment['currency_code'];
		  }

		
		  $pay_id= $row_ticket_payment['payment_id'];

  $net_amt= $total_sale;

	$url1 = BASE_URL."model/app_settings/print_html/receipt_html/receipt_body_html.php?payment_id_name=$payment_id_name&payment_id=$payment_id&receipt_date=$receipt_date&booking_id=$booking_id&customer_id=$customer_id&booking_name=$booking_name&travel_date=$travel_date&payment_amount=$payment_amount&transaction_id=$transaction_id&payment_date=$payment_date&bank_name=$bank_name&confirm_by=&receipt_type=$receipt_type&payment_mode=$payment_mode1&branch_status=$branch_status&outstanding=$outstanding&table_name=ticket_payment_master&customer_field=ticket_id&in_customer_id=$row_ticket_payment[ticket_id]&status=$row_ticket_payment[status]&pass_name=$guest_name&currency_code=$currency_code_2&pay_id=$pay_id&net_amt=$net_amt";

	$checkshow = "";
	if($row_ticket_payment['payment_mode']=="Cash" || $row_ticket_payment['payment_mode']=="Cheque"){
		$checkshow = '<input type="checkbox" id="chk_ticket_payment_'.$count.'" name="chk_ticket_payment" value="'. $row_ticket_payment['payment_id'].'">';
	}
	$payshow = "";
	if($payment_mode=="Cheque"){
		
		$payshow = '<input type="text" id="branch_name_'.$count.'" name="branch_name_d" class="form-control" placeholder="Branch Name" style="width:120px">';
		
	}

	if($row_ticket_payment['payment_mode'] == 'Credit Note' || ($row_ticket_payment['payment_mode'] == 'Credit Card' && $row_ticket_payment['clearance_status']=="Cleared")){
		$edit_btn = '';
		$delete_btn = '';
	}else{
		$edit_btn = '<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="ticket_payment_update_modal('.$row_ticket_payment['payment_id'] .')" id="updater-'.$row_ticket_payment['payment_id'] .'" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>';
		$delete_btn = '<button class="'.$delete_flag.' btn btn-danger btn-sm" onclick="p_delete_entry('.$row_ticket_payment['payment_id'].')" title="Delete Entry"><i class="fa fa-trash"></i></button>';
	}
	if ($row_ticket_payment['clearance_status']=="Cancelled"){
		$edit_btn = '';
	}


$total_payment_amount = number_format($row_ticket_payment['payment_amount']+$row_ticket_payment['credit_charges'],2);

	
		

		if($row_ticket_payment['currency_code'] ==''){
			$currency_code_1 = $sq_ticket_info['currency_code'];
		  }
		  else{
			$currency_code_1= $row_ticket_payment['currency_code'];
		  }
		// currency conversion
		$currency_amount1 = currency_conversion($currency,$currency_code_1,$total_payment_amount);
		if($currency_code_1 !='0' && $currency != $currency_code_1){
			$currency_amount = ' ('.$currency_amount1.')';
		}else{
			$currency_amount = '';
		}

	$temp_arr = array( "data" => array(
		(int)($count),
		$checkshow,
		$payment_id,
		get_ticket_booking_id($row_ticket_payment['ticket_id'],$year),
		$customer_name.$guest_name,
		date('d-m-Y', strtotime($row_ticket_payment['payment_date'])),
		$row_ticket_payment['payment_mode'],
		$payshow,
		$total_payment_amount.'<br>'.$currency_amount,
		'<a onclick="loadOtherPage(\''.$url1 .'\')" class="btn btn-info btn-sm" title="Download Receipt"><i class="fa fa-print"></i></a>
		'.$edit_btn.$delete_btn
	), "bg" =>$bg );
	
	array_push($array_s,$temp_arr); 
}

$footer_data = array("footer_data" => array(
	'total_footers' => 4,
	'foot0' => "Paid Amount: ".number_format($sq_paid_amount, 2),
	'col0' => 3,
	'class0' => "",
	'foot1' => "Pending Clearance : ".number_format($sq_pending_amount, 2),
	'col1' => 2,
	'class1' => "warning",
	'foot2' =>  "Cancelled: ".number_format($sq_cancel_amount, 2),
	'col2' => 2,
	'class2' => "danger",
	'foot3' => "Total Payment : ".number_format(($sq_paid_amount - $sq_pending_amount - $sq_cancel_amount), 2),
	'col3' => 2,
	'class3' => "success",
	)
);
array_push($array_s, $footer_data);	
echo json_encode($array_s);
?>