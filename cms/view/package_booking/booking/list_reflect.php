<?php
include "../../../model/model.php";
global $currency, $$modify_entries_switch;
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_POST['financial_year_id'];
$branch_status = $_POST['branch_status'];
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$booking_id = $_POST['booking_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_type = $_POST['cust_type'];
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';

$query = "select * from package_tour_booking_master where financial_year_id='$financial_year_id' and delete_status='0' ";
if ($customer_id != "") {
	$query .= " and customer_id='$customer_id'";
}
if ($booking_id != "") {
	$query .= " and booking_id='$booking_id'";
}
if ($from_date != "" && $to_date != "") {
	$from_date = get_date_db($from_date);
	$to_date = get_date_db($to_date);
	$query .= " and date(booking_date) between '$from_date' and '$to_date'";
}
if ($cust_type != "") {
	$query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
if ($company_name != "") {
	$query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
if ($role == "B2b") {
	$query .= " and emp_id ='$emp_id'";
}
include "../../../model/app_settings/branchwise_filteration.php";
$query .= " order by booking_id desc";

$count = 0;
$footer_amount = 0;
$footer_cancel = 0;
$footer_total = 0;
$array_s = array();
$temp_arr = array();
$sq_booking = mysqlQuery($query);

while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
	$sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_booking[emp_id]'"));
	$emp_name = ($row_booking['emp_id'] != 0) ? $sq_emp['first_name'] . ' ' . $sq_emp['last_name'] : 'Admin';

	$pass_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]'"));
	$cancle_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]' and status='Cancel'"));

	$date = $row_booking['booking_date'];
	$yr = explode("-", $date);
	$year = $yr[0];
	$q = "select * from package_refund_traveler_estimate where booking_id='$row_booking[booking_id]'";
	$sq_esti_count = mysqli_num_rows(mysqlQuery($q));
	$sq_esti = mysqli_fetch_assoc(mysqlQuery($q));
	$cancel_amount = ($sq_esti_count > 0) ? $sq_esti['cancel_amount'] : 0;

	$sq_train = mysqli_num_rows(mysqlQuery("select * from package_train_master where booking_id='$row_booking[booking_id]'"));
	$sq_plane = mysqli_num_rows(mysqlQuery("select * from package_plane_master where booking_id='$row_booking[booking_id]'"));
	$sq_visa = $row_booking['visa_amount'];
	$sq_insurance = $row_booking['insuarance_amount'];
	$total_paid = 0;
	$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(amount) as sum,sum(`credit_charges`) as sumc from  package_payment_master where booking_id='$row_booking[booking_id]' and clearance_status!='Cancelled' and clearance_status!='Pending'"));
	$total_paid =  $sq_paid_amount['sum'];
	$credit_card_charges = $sq_paid_amount['sumc'];
	if ($total_paid == '') {
		$total_paid = 0;
	}

	$average = 1;

	if ($sq_train > 0) {
		$average++;
	}
	if ($sq_plane > 0) {
		$average++;
	}
	if ($sq_visa != 0 && $sq_visa != "") {
		$average++;
	}
	if ($sq_insurance != 0 && $sq_insurance != "") {
		$average++;
	}

	//**Service Tax
	$taxation_type = $row_booking['taxation_type'];

	//basic amount
	$train_expense = $row_booking['train_expense'];
	$plane_expense = $row_booking['plane_expense'];
	$cruise_expense = $row_booking['cruise_expense'];
	$visa_amount = $row_booking['visa_amount'];
	$insuarance_amount = $row_booking['insuarance_amount'];
	$tour_subtotal = $row_booking['total_hotel_expense'];
	$basic_cost = $train_expense + $plane_expense + $cruise_expense + $visa_amount + $insuarance_amount + $tour_subtotal;

	//Service charge	
	$train_service_charge = $row_booking['train_service_charge'];
	$plane_service_charge = $row_booking['plane_service_charge'];
	$cruise_service_charge = $row_booking['cruise_service_charge'];
	$visa_service_charge = $row_booking['visa_service_charge'];
	$insuarance_service_charge = $row_booking['insuarance_service_charge'];
	$service_charge = $train_service_charge + $plane_service_charge + $cruise_service_charge + $visa_service_charge + $insuarance_service_charge + $tour_subtotal;

	//service tax
	$train_service_tax = $row_booking['train_service_tax'];
	$plane_service_tax = $row_booking['plane_service_tax'];
	$cruise_service_tax = $row_booking['cruise_service_tax'];
	$visa_service_tax = $row_booking['visa_service_tax'];
	$insuarance_service_tax = $row_booking['insuarance_service_tax'];
	$tour_service_tax = $row_booking['tour_service_tax'];

	//service tax subtotal	
	$train_service_tax_subtotal = $row_booking['train_service_tax_subtotal'];
	$plane_service_tax_subtotal = $row_booking['plane_service_tax_subtotal'];
	$cruise_service_tax_subtotal = $row_booking['cruise_service_tax_subtotal'];
	$visa_service_tax_subtotal = $row_booking['visa_service_tax_subtotal'];
	$insuarance_service_tax_subtotal = $row_booking['insuarance_service_tax_subtotal'];
	$tour_service_tax_subtotal = $row_booking['tour_service_tax_subtotal'];
	$service_tax_subtotal = (float)($train_service_tax_subtotal) + (float)($plane_service_tax_subtotal) + (float)($cruise_service_tax_subtotal) + (float)($visa_service_tax_subtotal) + (float)($insuarance_service_tax_subtotal) + (float)($tour_service_tax_subtotal);

	// Net amount
	$net_amount = 0;
	$tour_total_amount = ($row_booking['actual_tour_expense'] != "") ? $row_booking['actual_tour_expense'] : 0;
	$net_amount  =  $tour_total_amount + $row_booking['total_travel_expense'] - $cancel_amount + $credit_card_charges;

	$tour_date = get_date_user($row_booking['tour_from_date']);
	$tour_to_date = get_date_user($row_booking['tour_to_date']);
	$destination_city = $row_booking['tour_name'];

	$sq_sac = mysqli_fetch_assoc(mysqlQuery("select * from sac_master where service_name='Package Tour'"));
	$sac_code = $sq_sac['hsn_sac_code'];

	$invoice_no = get_package_booking_id($row_booking['booking_id'], $year);
	$booking_id = $row_booking['booking_id'];
	$invoice_date = date('d-m-Y', strtotime($row_booking['booking_date']));
	$customer_id = $row_booking['customer_id'];
	$quotation_id = $row_booking['quotation_id'];
	$service_name = "Package Invoice";
	$tour_name = $row_booking['tour_name'];

	$cust_user_name = '';
	$sq_quo = mysqli_fetch_assoc(mysqlQuery("select user_id from package_tour_quotation_master where quotation_id='$quotation_id'"));
	if ($sq_quo['user_id'] != 0) {
		$row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$sq_quo[user_id]'"));
		$cust_user_name = ' (' . $row_user['name'] . ')';
	}
	if ($pass_count == $cancle_count) {
		$bg = "danger";
		$update_btn = '';
		$delete_btn = '';
		$conf_form = '';
		$service_voucher = '';
	} else {

		$bg = "";
		$update_btn = '
		<form style="display:inline-block" data-toggle="tooltip" action="booking_update/package_booking_master_update.php" id="frm_booking_' . $count . '" method="POST">
			<input type="hidden" id="booking_id" name="booking_id" value="' . $row_booking['booking_id'] . '">
			<input type="hidden" id="branch_status" name="branch_status" value="' . $branch_status . '">
			<button class="btn btn-info btn-sm" data-toggle="tooltip" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>
		</form>';
		$to_date = $row_booking['tour_to_date'];
		$today = date('Y-m-d');
		if ($to_date < $today && $modify_entries_switch == 'No' && $role != 'Admin' && $role != 'Branch Admin') {
			$update_btn = '';
		}
		$delete_btn = '<button class="' . $delete_flag . ' btn btn-danger btn-sm" onclick="delete_entry(' . $row_booking['booking_id'] . ')" title="Delete Entry"><i class="fa fa-trash"></i></button>';
		// Booking Form
		$b_url = BASE_URL . "model/app_settings/print_html/booking_form_html/package_tour.php?booking_id=$row_booking[booking_id]&quotation_id=$quotation_id&branch_status=$branch_status&year=$year&credit_card_charges=$credit_card_charges";
		$conf_form = '<a data-toggle="tooltip" style="display:inline-block" onclick="loadOtherPage(\'' . $b_url . '\')" class="btn btn-info btn-sm" title="Download Confirmation Form" ><i class="fa fa-print"></i></a>';
		$service_voucher = '<button data-toggle="tooltip" title="Download Service Voucher" class="btn btn-info btn-sm" onclick="voucher_modal(' . $row_booking['booking_id'] . ')" id="servoucher_btn-' . $booking_id . '" ><i class="fa fa-print" data-toggle="tooltip"></i></button>';
	}
	$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
	if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
		$customer_name = $sq_customer['company_name'];
	} else {
		$customer_name = $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
	}

	//Passengers
	$adults = mysqli_num_rows(mysqlQuery("select traveler_id from package_travelers_details where booking_id='$row_booking[booking_id]' and status!='Cancel' and adolescence='Adult'"));
	$child = mysqli_num_rows(mysqlQuery("select traveler_id from package_travelers_details where booking_id='$row_booking[booking_id]' and status!='Cancel' and adolescence='Children'"));
	$infants = mysqli_num_rows(mysqlQuery("select traveler_id from package_travelers_details where booking_id='$row_booking[booking_id]' and status!='Cancel' and adolescence='Infant'"));
	//Flights
	$sq_f_count = mysqli_num_rows(mysqlQuery("select * from package_plane_master where booking_id='$row_booking[booking_id]'"));
	$flights = '';
	$count = 1;
	if ($sq_f_count != '0') {
		$sq_entry = mysqlQuery("select * from package_plane_master where booking_id='$row_booking[booking_id]'");
		while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
			$seperator = ($sq_f_count != $count) ? '/ ' : '';
			$flights .= 'From ' . $row_entry['from_location'] . ' To ' . $row_entry['to_location'] . $seperator;
			$count++;
		}
	}
	//Train
	$sq_f_count = mysqli_num_rows(mysqlQuery("select * from package_train_master where booking_id='$row_booking[booking_id]'"));
	$trains = '';
	$count = 1;
	if ($sq_f_count != '0') {
		$sq_entry = mysqlQuery("select * from package_train_master where booking_id='$row_booking[booking_id]'");
		while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
			$seperator = ($sq_f_count != $count) ? '/ ' : '';
			$trains .= 'From ' . $row_entry['from_location'] . ' To ' . $row_entry['to_location'] . $seperator;
			$count++;
		}
	}
	//Cruise
	$sq_f_count = mysqli_num_rows(mysqlQuery("select * from package_cruise_master where booking_id='$row_booking[booking_id]'"));
	$cruises = '';
	if ($sq_f_count != '0') {
		$count = 0;
		$sq_entry = mysqlQuery("select * from package_cruise_master where booking_id='$row_booking[booking_id]'");
		while ($row_entry = mysqli_fetch_assoc($sq_entry)) {
			$count++;
			$cruises .= 'Cabin- ' . $row_entry['cabin'] . ', Route- ' . $row_entry['route'];
			$cruises .= ($count < $sq_f_count) ? ' / ' : '';
		}
	}

	if ($app_invoice_format == 4)
		$url1 = BASE_URL . "model/app_settings/print_html/invoice_html/body/git_fit_tax_invoice.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=$taxation_type&train_expense=$train_expense&plane_expense=$plane_expense&cruise_expense=$cruise_expense&visa_amount=$visa_amount&insuarance_amount=$insuarance_amount&tour_subtotal=$tour_subtotal&train_service_charge=$train_service_charge&plane_service_charge=$plane_service_charge&cruise_service_charge=$cruise_service_charge&visa_service_charge=$visa_service_charge&insuarance_service_charge=$insuarance_service_charge&train_service_tax=$train_service_tax&plane_service_tax=$plane_service_tax&cruise_service_tax=$cruise_service_tax&visa_service_tax=$visa_service_tax&insuarance_service_tax=$insuarance_service_tax&tour_service_tax=$tour_service_tax&train_service_tax_subtotal=$train_service_tax_subtotal&plane_service_tax_subtotal=$plane_service_tax_subtotal&cruise_service_tax_subtotal=$cruise_service_tax_subtotal&visa_service_tax_subtotal=$visa_service_tax_subtotal&insuarance_service_tax_subtotal=$insuarance_service_tax_subtotal&total_paid=$total_paid&net_amount=$net_amount&sac_code=$sac_code&branch_status=$branch_status&pass_count=$pass_count&tour_date=$tour_date&destination_city=$destination_city&booking_id=$row_booking[booking_id]&credit_card_charges=$credit_card_charges";
	else
		$url1 = BASE_URL . "model/app_settings/print_html/invoice_html/body/git_fit_body_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&quotation_id=$quotation_id&service_name=$service_name&taxation_type=$taxation_type&train_expense=$train_expense&plane_expense=$plane_expense&cruise_expense=$cruise_expense&visa_amount=$visa_amount&insuarance_amount=$insuarance_amount&tour_subtotal=$tour_subtotal&train_service_charge=$train_service_charge&plane_service_charge=$plane_service_charge&cruise_service_charge=$cruise_service_charge&visa_service_charge=$visa_service_charge&insuarance_service_charge=$insuarance_service_charge&train_service_tax=$train_service_tax&plane_service_tax=$plane_service_tax&cruise_service_tax=$cruise_service_tax&visa_service_tax=$visa_service_tax&insuarance_service_tax=$insuarance_service_tax&tour_service_tax=$tour_service_tax&train_service_tax_subtotal=$train_service_tax_subtotal&plane_service_tax_subtotal=$plane_service_tax_subtotal&cruise_service_tax_subtotal=$cruise_service_tax_subtotal&visa_service_tax_subtotal=$visa_service_tax_subtotal&insuarance_service_tax_subtotal=$insuarance_service_tax_subtotal&total_paid=$total_paid&net_amount=$net_amount&sac_code=$sac_code&branch_status=$branch_status&tour_name=$tour_name&booking_id=$row_booking[booking_id]&credit_card_charges=$credit_card_charges&credit_card_charges=$credit_card_charges&tcs_tax=$row_booking[tcs_tax]&tcs_per=$row_booking[tcs_per]&tour_date=$tour_date&tour_to_date=$tour_to_date&child=$child&adults=$adults&infants=$infants&flights=$flights&trains=$trains&cruises=$cruises&canc_amount=$cancel_amount&bg=$bg&sub_total=$row_booking[subtotal]";

	$balance_amount = $row_booking['net_total'] + $credit_card_charges - $cancel_amount;
	// currency conversion
	$currency_amount1 = currency_conversion($currency, $row_booking['currency_code'], $balance_amount);
	if ($row_booking['currency_code'] != '0' && $currency != $row_booking['currency_code']) {
		$currency_amount = ' (' . $currency_amount1 . ')';
	} else {
		$currency_amount = '';
	}

	$footer_amount += $row_booking['net_total'] + $credit_card_charges;
	$footer_cancel += $cancel_amount;
	$footer_total += $balance_amount;

	$temp_arr = array("data" => array(
		$row_booking['invoice_pr_id'],
		get_package_booking_id($row_booking['booking_id'], $year),
		$customer_name . $cust_user_name,
		$row_booking['tour_name'],
		number_format($row_booking['net_total'] + $credit_card_charges, 2),
		number_format($cancel_amount, 2),
		number_format($balance_amount, 2) . '<br/>' . $currency_amount,
		$emp_name,
		get_date_user($row_booking['booking_date']),
		$conf_form . '
		<a data-toggle="tooltip" onclick="loadOtherPage(\'' . $url1 . '\')" class="btn btn-info btn-sm" title="Download Invoice"><i class="fa fa-print" data-toggle="tooltip"></i></a>'
			. $service_voucher . $update_btn .
			'<button style="display:inline-block" class="btn btn-info btn-sm" onclick="package_view_modal(' . $row_booking['booking_id'] . ');btnDisableEnable(this.id)" id="package_view_modal_btn-' . $row_booking['booking_id'] . '" title="View Details" data-toggle="tooltip"><i class="fa fa-eye" aria-hidden="true"></i></button>
		' . $delete_btn,

	), "bg" => $bg);
	array_push($array_s, $temp_arr);
}
$footer_data = array(
	"footer_data" => array(
		'total_footers' => 5,
		'foot0' => "Total",
		'col0' => 4,
		'class0' => "text-right",
		'foot1' => number_format($footer_amount, 2),
		'col1' => 1,
		'class1' => "info",
		'foot2' =>  number_format($footer_cancel, 2),
		'col2' => 1,
		'class2' => "danger",
		'foot3' => number_format($footer_total, 2),
		'col3' => 1,
		'class3' => "success",
		'foot4' => '',
		'col4' => 2,
		'class4' => "",
	)
);
array_push($array_s, $footer_data);
echo json_encode($array_s);
