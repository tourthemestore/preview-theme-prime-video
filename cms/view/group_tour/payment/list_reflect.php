<?php
global $modify_entries_switch;
include "../../../model/model.php";
$tour_id = $_POST['tour_id'];
$group_id = $_POST['group_id'];
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$booking_id = $_POST['booking_id'];
$payment_mode = $_POST['payment_mode'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$branch_status = isset($_POST['branch_status']) ? $_POST['branch_status'] : '';

$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$ar = array();

$count = 0;
$count1=0;
$total_pending = 0;
$total_cancelled = 0;
$total = 0;

$array_s = array();
$temp_arr = array();
$footer_data = array();

$query = "select * from payment_master where 1 ";

if($tour_id!=""){

$query .=" and tourwise_traveler_id in (select id from tourwise_traveler_details where tour_id='$tour_id')";

}

if($group_id!=""){

$query .=" and tourwise_traveler_id in (select id from tourwise_traveler_details where tour_group_id='$group_id')";

}

if($customer_id!=""){

$query .=" and tourwise_traveler_id in (select id from tourwise_traveler_details where customer_id='$customer_id')";

}

if($booking_id!=""){

$query .=" and tourwise_traveler_id='$booking_id'";

}

if($payment_mode!=""){

$query .=" and payment_mode='$payment_mode'";

}

if($from_date!="" && $to_date!=""){

$from_date = get_date_db($from_date);

$to_date = get_date_db($to_date);

$query .=" and date between '$from_date' and '$to_date'";

}

if($financial_year_id!=""){

$query .=" and financial_year_id='$financial_year_id'";

}		

if($cust_type != ""){

$query .= " and tourwise_traveler_id in (select id from tourwise_traveler_details where customer_id in ( select customer_id from customer_master where type='$cust_type' ))";

}

if($company_name != ""){

$query .= " and tourwise_traveler_id in (select id from tourwise_traveler_details where customer_id in ( select customer_id from customer_master where company_name='$company_name' ))";

}
if($role == "B2b"){
$query .= " and tourwise_traveler_id in (select tourwise_traveler_id from tourwise_traveler_details where emp_id ='$emp_id')";
}
include "../../../model/app_settings/branchwise_filteration.php";
// $query .=" order by payment_id desc";
$sq_payment = mysqlQuery($query);

while($row_payment = mysqli_fetch_assoc($sq_payment)){

if($row_payment['amount'] != '0.00'){

		$sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$row_payment[tourwise_traveler_id]'"));
		$sq_pay = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum ,sum(credit_charges) as sumc , currency_code from payment_master where clearance_status!='Cancelled' and tourwise_traveler_id='$row_payment[tourwise_traveler_id]'"));
		$total_sale = $sq_booking['net_total']+$sq_pay['sumc'];
		$total_pay_amt = $sq_pay['sum']+$sq_pay['sumc'];

		$pass_count = mysqli_num_rows(mysqlQuery("select * from travelers_details where traveler_group_id='$sq_booking[traveler_group_id]'"));
		$cancelpass_count = mysqli_num_rows(mysqlQuery("select * from travelers_details where traveler_group_id='$sq_booking[traveler_group_id]' and status='Cancel'"));    
		
		if($sq_booking['tour_group_status'] == 'Cancel'){
			//Group Tour cancel
			$cancel_tour_count2=mysqli_num_rows(mysqlQuery("SELECT * from refund_tour_estimate where tourwise_traveler_id='$sq_booking[id]'"));
			if($cancel_tour_count2 >= '1'){
				$cancel_tour=mysqli_fetch_assoc(mysqlQuery("SELECT * from refund_tour_estimate where tourwise_traveler_id='$sq_booking[id]'"));
				$cancel_amount = ($cancel_tour_count2 > 0) ? $cancel_tour['cancel_amount'] : 0;
			}
			else{ $cancel_amount = 0; }
		}
		else{
			// Group booking cancel
			$cancel_esti_count1=mysqli_num_rows(mysqlQuery("SELECT * from refund_traveler_estimate where tourwise_traveler_id='$sq_booking[id]'"));
			if($pass_count==$cancelpass_count){
				$cancel_esti1=mysqli_fetch_assoc(mysqlQuery("SELECT * from refund_traveler_estimate where tourwise_traveler_id='$sq_booking[id]'"));
				$cancel_amount = ($cancel_esti_count1 > 0) ? $cancel_esti1['cancel_amount'] : 0;
			}
			else{ $cancel_amount = 0; }
		}
		
		$cancel_amount = ($cancel_amount == '')?'0':$cancel_amount;
		if($sq_booking['tour_group_status'] == 'Cancel'){
			if($cancel_amount > $total_pay_amt){
				$balance_amount = $cancel_amount - $total_pay_amt;
			}
			else{
				$balance_amount = 0;
			}
		}else{
			if($pass_count==$cancelpass_count){
				if($cancel_amount > $total_pay_amt){
					$balance_amount = $cancel_amount - $total_pay_amt;
				}
				else{
					$balance_amount = 0;
				}
			}
			else{
				$balance_amount = $total_sale - $total_pay_amt;
			}
		}

		$date = $sq_booking['form_date'];
		$yr = explode("-", $date);
		$year =$yr[0];
		$date1 = $row_payment['date'];
		$yr1 = explode("-", $date1);
		$year1 = $yr1[0];
		$sq_tour = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$sq_booking[tour_id]'"));

		$sq_group = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$sq_booking[tour_group_id]'"));

		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_booking[customer_id]'"));
		if($sq_customer['type'] == 'Corporate'||$sq_customer['type'] == 'B2B'){
			$customer_name = $sq_customer['company_name'];
		}else{
			$customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
		}

		$tour = $sq_tour['tour_name'];
		$group = get_date_user($sq_group['from_date']).' to '.get_date_user($sq_group['to_date']);
		$bg = "";

		if($row_payment['clearance_status']=="Pending"){
			$bg = "warning";
			$total_pending = $total_pending+$row_payment['amount']+$row_payment['credit_charges'];
		}
		else if($row_payment['clearance_status']=="Cancelled"){
			$bg = "danger";
			$total_cancelled = $total_cancelled + $row_payment['amount'] + $row_payment['credit_charges'];
		}
		else if($row_payment['clearance_status']=="Cleared"){
			$bg = "success";
		}
		else{
			$bg = '';
		}

		$total = $total+$row_payment['amount']+$row_payment['credit_charges'];

		$payment_id_name = "Group Payment ID";

		$payment_id = get_group_booking_payment_id($row_payment['payment_id'],$year1);

		$receipt_date = date('d-m-Y');

		$booking_id = get_group_booking_id($row_payment['tourwise_traveler_id'],$year);

		$customer_id = $sq_booking['customer_id'];

		$booking_name = "Group Booking";

		$travel_date = get_date_user($sq_group['from_date']);

		$payment_amount = $row_payment['amount']+$row_payment['credit_charges'];

		$payment_mode1 = $row_payment['payment_mode'];

		$transaction_id = $row_payment['transaction_id'];

		$payment_date = get_date_user($row_payment['date']);

		$bank_name = $row_payment['bank_name'];

		$confirm_by = $sq_booking['emp_id'];

		$group = get_date_user($sq_group['from_date']).' to '.get_date_user($sq_group['to_date']);

		$receipt_type = ($row_payment['payment_for']=='Travelling') ? "Travel Receipt" : "Group Tour Receipt";

		if($row_payment['currency_code'] ==''){
			$currency_code_2 = $sq_booking['currency_code'];
		  }
		  else{
			$currency_code_2= $row_payment['currency_code'];
		  }
		  $pay_id= $row_payment['payment_id'];
		   $net_amt= $total_sale;

		$url1 = BASE_URL."model/app_settings/print_html/receipt_html/receipt_body_html.php?payment_id_name=$payment_id_name&payment_id=$payment_id&receipt_date=$receipt_date&booking_id=$booking_id&customer_id=$customer_id&booking_name=$booking_name&travel_date=$travel_date&payment_amount=$payment_amount&transaction_id=$transaction_id&payment_date=$payment_date&bank_name=$bank_name&confirm_by=$confirm_by&receipt_type=$receipt_type&payment_mode=$payment_mode1&branch_status=$branch_status&outstanding=$balance_amount&tour=$tour&table_name=payment_master&customer_field=tourwise_traveler_id&in_customer_id=$row_payment[tourwise_traveler_id]&currency_code=$currency_code_2&status=$row_payment[status]&pay_id=$pay_id&net_amt=$net_amt";
		$checshow = '';		
		if($payment_mode=="Cash" || $payment_mode=="Cheque"){
		
			$checshow = '<input type="checkbox" id="chk_receipt_'.$count.'" name="chk_receipt" data-amount="'. $row_payment['amount'] .'" data-payment-id="'. $row_payment['payment_id'] .'" data-offset="'. $count .'">';
		}

		$payshow = "";
		if($payment_mode=="Cheque"){
			$payshow = '<input type="text" id="branch_name_'.$count.'" name="branch_name_d" class="form-control" placeholder="Branch Name" style="width:120px">';
		}

		if($row_payment['payment_mode'] == 'Credit Note' || ($row_payment['payment_mode'] == 'Credit Card' && $row_payment['clearance_status']=="Cleared")){
			$edit_btn = '';
			$delete_btn = '';
		}else{
			$edit_btn = '<button class="btn btn-info btn-sm" data-toggle="tooltip" onclick="update_modal('.$row_payment['payment_id'].')" title="Update Details" id="updater-'.$row_payment['payment_id'].'"><i class="fa fa-pencil-square-o"></i></button>';
			$delete_btn = '<button class="'.$delete_flag.' btn btn-danger btn-sm" onclick="p_delete_entry('.$row_payment['payment_id'].')" title="Delete Entry"><i class="fa fa-trash"></i></button>';
		}
		if ($row_payment['clearance_status']=="Cancelled"){
			$edit_btn = '';
		}
		$to_date = $sq_group['to_date'];
		$today = date('Y-m-d');
		if($to_date < $today && $modify_entries_switch == 'No' && $role != 'Admin' && $role != 'Branch Admin'){
			$edit_btn = '';
		}

		// currency conversion
		if($row_payment['currency_code'] ==''){
			$currency_code_1 = $sq_booking['currency_code'];
		  }
		  else{
			$currency_code_1= $row_payment['currency_code'];
		  }
		// currency conversion
		$currency_amount1 = currency_conversion($currency,$currency_code_1,$payment_amount);
		if($currency_code_1 !='0' && $currency != $currency_code_1){
			$currency_amount = ' ('.$currency_amount1.')';
		}else{
			$currency_amount = '';
		}
		$temp_arr = array( "data" => array(
			(int)(++$count),
			$checshow,
			$payment_id,
			get_group_booking_id($row_payment['tourwise_traveler_id'],$year),
			$customer_name,
			$tour,
			$group,
			$payment_mode1,
			get_date_user($row_payment['date']),
			$payshow,
			number_format($payment_amount,2).$currency_amount,
			'<a onclick="loadOtherPage(\''. $url1 .'\')" class="btn btn-info btn-sm" title="Download Receipt"><i class="fa fa-print"></i></a>
			'.$edit_btn.$delete_btn
		), "bg" =>$bg );
		array_push($array_s,$temp_arr); 
		}
}
$footer_data = array("footer_data" => array(
	'total_footers' => 4,
	'foot0' => "Total Amount: ".number_format($total, 2),
	'col0' => 3,
	'class0' => "info",
	'foot1' => "Pending Clearance : ".number_format($total_pending, 2),
	'col1' => 3,
	'class1' => "warning",
	'foot2' =>  "CANCELLED : ".number_format($total_cancelled, 2),
	'col2' => 3,
	'class2' => "danger",
	'foot3' => "Total Paid : ".number_format(($total-$total_pending-$total_cancelled), 2),
	'col3' => 3,
	'class3' => "success"
	)
);
array_push($array_s, $footer_data);
echo json_encode($array_s);	
?>