<?php
include "../../../model/model.php";
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
global $currency;
$to_currency_id = $currency;

$query = "select * from b2c_quotations where 1 and status=0 ";
if ($from_date != '' && $to_date != "") {

	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));

	$query .= " and DATE(created_at) between '$from_date' and '$to_date' ";
}
$query .= " order by entry_id desc ";

$count = 0;
$row_quotation1 = mysqlQuery($query);
$array_s = array();
$temp_arr = array();
while ($row_quotation = mysqli_fetch_assoc($row_quotation1)) {

	$quotation_cost = 0;
	$total_cost1 = 0;
	$entry_id = $row_quotation['entry_id'];
	$quotation_date = $row_quotation['created_at'];
	$yr = explode("-", $quotation_date);
	$year = $yr[0];
	$pax = intval($row_quotation['adults']) + intval($row_quotation['chwob']) + intval($row_quotation['chwb']) + intval($row_quotation['extra_bed']) + intval($row_quotation['infant']);
	if ($row_quotation['type'] == '1') {

		$sq_package_name = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$row_quotation[package_id]'"));
		$package_name = $sq_package_name['package_name'];
		$q = "select * from custom_package_tariff where (`from_date` <= '$row_quotation[travel_from_date]' and `to_date` >= '$row_quotation[travel_from_date]') and (`min_pax` <= '$pax' and `max_pax` >= '$pax') and `package_id`='$row_quotation[package_id]' and `hotel_type`='$row_quotation[package_type]' ";
		$sq_tariff = mysqlQuery($q);
		$currency_id = $sq_package_name['currency_id'];
		while ($row_tariff = mysqli_fetch_assoc($sq_tariff)) {

			$total_cost1 = ($row_quotation['adults'] * (float)($row_tariff['cadult'])) + ($row_quotation['chwob'] * (float)($row_tariff['ccwob'])) + ($row_quotation['chwb'] * (float)($row_tariff['ccwb'])) + ($row_quotation['infant'] * (float)($row_tariff['cinfant'])) + ($row_quotation['extra_bed'] * (float)($row_tariff['cextra']));
		}
		if ($total_cost1 == 0) {
			$quotation_cost = 'Price On Request';
		} else {

			$quotation_cost = currency_conversion($currency_id, $to_currency_id, $total_cost1);
			$quotation_cost .= ' (+Tax)';
		}
	} else {

		$sq_package_name = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id = '$row_quotation[package_id]'"));
		$package_name = $sq_package_name['tour_name'];

		$total_pax = intval($sq_quotation['adults']) + intval($sq_quotation['chwob']) + intval($sq_quotation['chwb']) + intval($sq_quotation['infant']);
		if ($total_pax > 1) {
			$adult_cost_total = intval($row_quotation['adults']) * (float)($sq_package_name['adult_cost']);
			$child_without_cost_total = intval($row_quotation['chwob']) * (float)($sq_package_name['child_without_cost']);
			$child_with_cost_total = intval($row_quotation['chwb']) * (float)($sq_package_name['child_with_cost']);
			$with_bed_cost_total = intval($row_quotation['extra_bed']) * (float)($sq_package_name['with_bed_cost']);
			$infant_cost_total = intval($row_quotation['infant']) * (float)($sq_package_name['infant_cost']);
		} else {
			$adult_cost_total = intval($row_quotation['adults']) * (float)($sq_package_name['single_person_cost']);
		}


		$currency_id = $currency;
		$total_cost1 = (float)($adult_cost_total) + (float)($child_without_cost_total) + (float)($child_with_cost_total) + (float)($infant_cost_total) + (float)($with_bed_cost_total);

		if ($total_cost1 == 0) {
			$quotation_cost = 'Price On Request';
		} else {
			$quotation_cost = currency_conversion($currency_id, $to_currency_id, $total_cost1);
			$quotation_cost .= ' (+Tax)';
		}
	}
	$invoice_no = get_quotation_id($entry_id, $year);
	$url1 = BASE_URL . "model/app_settings/print_html/quotation_html/quotation_html_5/b2c_quotation_html.php?quotation_id=$entry_id";

	$service = ($row_quotation['type'] == '1') ? 'Holiday' : 'Group Tour';

	$temp_arr = array("data" => array(
		(int)(++$count),
		$service,
		get_quotation_id($row_quotation['entry_id'], $year),
		get_datetime_user($row_quotation['created_at']),
		$row_quotation['name'],
		$package_name,
		$quotation_cost,
		'<a data-toggle="tooltip" onclick="loadOtherPage(\'' . $url1 . '\')" class="btn btn-info btn-sm" title="Download Quotation PDF"><i class="fa fa-print"></i></a>
		<button style="display:inline-block" class="btn btn-danger btn-sm" onclick="delete_quotation(' . $row_quotation['entry_id'] . ')" title="Delete Quotation" data-toggle="tooltip"><i class="fa fa-trash" aria-hidden="true"></i></button>
		'

	), "bg" => $bg);
	array_push($array_s, $temp_arr);
}
echo json_encode($array_s);
