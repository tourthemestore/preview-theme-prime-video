<?php
include "../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells, $color)
{
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
            'rgb' => $color
        )
    ));
}

//This array sets the font atrributes
$header_style_Array = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Verdana'
    )
);
$table_header_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    )
);
$content_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    )
);

//This is border array
$borderArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
    ->setLastModifiedBy("Maarten Balliauw")
    ->setTitle("Office 2007 XLSX Test Document")
    ->setSubject("Office 2007 XLSX Test Document")
    ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Test result file");


//////////////////////////****************Content start**************////////////////////////////////
global $currency;
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_GET['branch_status'];

$customer_id = $_GET['customer_id'];
$booking_id = $_GET['booking_id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$cust_type = $_GET['cust_type'];
$company_name = (isset($_GET['company_name'])) ? $_GET['company_name'] : '';
$booker_id = $_GET['booker_id'];
$branch_id = $_GET['branch_id'];

if ($customer_id != "") {
    $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
    if ($sq_customer_info['type'] == 'Corporate' || $sq_customer_info['type'] == 'B2B') {
        $cust_name = $sq_customer_info['company_name'];
    } else {
        $cust_name = $sq_customer_info['first_name'] . ' ' . $sq_customer_info['last_name'];
    }
} else {
    $cust_name = "";
}

if ($booking_id != "") {

    $sq_package_info = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' and delete_status='0'"));
    $date = $sq_package_info['booking_date'];
    $yr = explode("-", $date);
    $year = $yr[0];
    $invoice_id = get_package_booking_id($booking_id, $year);
} else {
    $invoice_id = "";
}

if ($from_date != "" && $to_date != "") {
    $date_str = $from_date . ' to ' . $to_date;
} else {
    $date_str = "";
}
if ($company_name == 'undefined') {
    $company_name = '';
}

if ($booker_id != '') {
    $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$booker_id'"));
    if ($sq_emp['first_name'] == '') {
        $emp_name = 'Admin';
    } else {
        $emp_name = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
    }
}

if ($branch_id != '') {
    $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_id'"));
    $branch_name = $sq_branch['branch_name'] == '' ? 'NA' : $sq_branch['branch_name'];
}
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B2', 'Report Name')
    ->setCellValue('C2', 'Package Tour Summary')
    ->setCellValue('B3', 'Booking ID')
    ->setCellValue('C3', $invoice_id)
    ->setCellValue('B4', 'Customer')
    ->setCellValue('C4', $cust_name)
    ->setCellValue('B5', 'From-To Date')
    ->setCellValue('C5', $date_str)
    ->setCellValue('B6', 'Customer Type')
    ->setCellValue('C6', $cust_type)
    ->setCellValue('B7', 'Company Name')
    ->setCellValue('C7', $company_name)
    ->setCellValue('B8', 'Booked By')
    ->setCellValue('C8', $emp_name)
    ->setCellValue('B9', 'Branch')
    ->setCellValue('C9', $branch_name);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($borderArray);


$query = "select * from package_tour_booking_master where 1 and delete_status='0' ";
if ($customer_id != "") {
    $query .= " and customer_id='$customer_id'";
}
if ($booking_id != "") {
    $query .= " and booking_id='$booking_id'";
}
if ($from_date != "" && $to_date != "") {
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));
    $query .= " and booking_date between '$from_date' and '$to_date'";
}
if ($cust_type != "") {
    $query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
if ($company_name != "") {
    $query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
if ($booker_id != "") {
    $query .= " and emp_id='$booker_id'";
}
if ($branch_id != "") {
    $query .= " and emp_id in(select emp_id from emp_master where branch_id = '$branch_id')";
}
if ($branch_status == 'yes') {
    if ($role == 'Branch Admin') {
        $query .= " and branch_admin_id = '$branch_admin_id'";
    } elseif ($role != 'Admin' && $role != 'Branch Admin') {
        $query .= " and emp_id ='$emp_id'";
    }
}
if ($branch_status == 'yes') {
    if ($role == 'Branch Admin' || $role == 'Accountant' || $role_id > '7') {
        $query .= " and branch_admin_id = '$branch_admin_id'";
    } elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
        $query .= " and emp_id='$emp_id' and branch_admin_id = '$branch_admin_id'";
    }
} elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
    $query .= " and emp_id='$emp_id'";
}
$query .= " order by booking_id desc";

$row_count = 11;

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B' . $row_count, "Sr. No")
    ->setCellValue('C' . $row_count, "Booking ID")
    ->setCellValue('D' . $row_count, "Customer Name")
    ->setCellValue('E' . $row_count, "Mobile")
    ->setCellValue('F' . $row_count, "EMAIL ID")
    ->setCellValue('G' . $row_count, "Total Pax")
    ->setCellValue('H' . $row_count, "Booking Date")
    ->setCellValue('I' . $row_count, "Tour Name")
    ->setCellValue('J' . $row_count, "Tour Date")
    ->setCellValue('K' . $row_count, "Basic Amount")
    ->setCellValue('L' . $row_count, "Service Charge")
    ->setCellValue('M' . $row_count, "Tax")
    ->setCellValue('N' . $row_count, "TCS")
    ->setCellValue('O' . $row_count, "TDS")
    ->setCellValue('P' . $row_count, "Credit Card Charges")
    ->setCellValue('Q' . $row_count, "Sale")
    ->setCellValue('R' . $row_count, "Cancel")
    ->setCellValue('S' . $row_count, "Total")
    ->setCellValue('T' . $row_count, "Paid")
    ->setCellValue('U' . $row_count, "Outstanding Balance")
    ->setCellValue('V' . $row_count, "Due Date")
    ->setCellValue('W' . $row_count, "Purchase")
    ->setCellValue('X' . $row_count, "Purchased From")
    ->setCellValue('Y' . $row_count, "Branch")
    ->setCellValue('Z' . $row_count, "Booked By")
    ->setCellValue('AA' . $row_count, "Incentive");


$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);

$row_count++;

$count = 0;
$total_balance = 0;
$total_refund = 0;
$cancel_total = 0;
$sale_total = 0;
$paid_total = 0;
$balance_total = 0;
// $vendor_name1 = '';

$sq_package = mysqlQuery($query);
while ($row_package = mysqli_fetch_assoc($sq_package)) {

    $vendor_name1 = '';
    
    $date = $row_package['booking_date'];
    $yr = explode("-", $date);
    $year = $yr[0];
    $pass_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_package[booking_id]'"));
    $cancle_count = mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_package[booking_id]' and status='Cancel'"));
    if ($pass_count == $cancle_count) {
        $bg = "danger";
    } else {
        $bg = "#fff";
    }

    $tour_name = $row_package['tour_name'];
    $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_package[emp_id]'"));
    if ($sq_emp['first_name'] == '') {
        $emp_name = 'Admin';
    } else {
        $emp_name = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
    }

    $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$sq_emp[branch_id]'"));
    $branch_name = $sq_branch['branch_name'] == '' ? 'NA' : $sq_branch['branch_name'];
    $sq_total_member = mysqli_num_rows(mysqlQuery("select booking_id from package_travelers_details where booking_id = '$row_package[booking_id]'"));
    $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_package[customer_id]'"));
    $contact_no = $encrypt_decrypt->fnDecrypt($sq_customer_info['contact_no'], $secret_key);
    $email_id = $encrypt_decrypt->fnDecrypt($sq_customer_info['email_id'], $secret_key);
    if ($sq_customer_info['type'] == 'Corporate' || $sq_customer_info['type'] == 'B2B') {
        $customer_name = $sq_customer_info['company_name'];
    } else {
        $customer_name = $sq_customer_info['first_name'] . ' ' . $sq_customer_info['last_name'];
    }

    $total_paid = 0;
    $sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(amount) as sum,sum(credit_charges) as sumc from  package_payment_master where booking_id='$row_package[booking_id]' and clearance_status!='Pending' and  clearance_status!='Cancelled'"));
    $credit_card_charges = $sq_paid_amount['sumc'];
    $total_paid =  $sq_paid_amount['sum'];
    if ($total_paid == '') {
        $total_paid = 0;
    }

    //sale amount
    $tour_fee = $row_package['net_total'];

    //cancel amount
    $row_esti = mysqli_fetch_assoc(mysqlQuery("SELECT * from package_refund_traveler_estimate where booking_id='$row_package[booking_id]'"));
    $tour_esti = $row_esti['cancel_amount'];

    //total amount
    $total_amount = $tour_fee - $tour_esti;

    //balance
    if ($pass_count == $cancle_count) {
        if ($total_paid > 0) {
            if ($tour_esti > 0) {
                if ($total_paid > $tour_esti) {
                    $total_balance = 0;
                } else {
                    $total_balance = $tour_esti - $total_paid;
                }
            } else {
                $total_balance = 0;
            }
        } else {
            $total_balance = $tour_esti;
        }
    } else {
        $total_balance = $total_amount - $total_paid;
    }

    //Footer
    $cancel_total = $cancel_total + $tour_esti;
    $sale_total = $sale_total + $total_amount;
    $paid_total = $paid_total + $sq_paid_amount['sum'];
    $balance_total = $balance_total + $total_balance;
    /////// Purchase ////////
    $total_purchase = 0;
    $purchase_amt = 0;
    $i = 0;
    $p_due_date = '';
    $sq_purchase_count = mysqli_num_rows(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Package Tour' and estimate_type_id='$row_package[booking_id]' and delete_status='0'"));
    if ($sq_purchase_count == 0) {
        $p_due_date = 'NA';
    }
    $sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Package Tour' and estimate_type_id='$row_package[booking_id]' and delete_status='0'");
    while ($row_purchase = mysqli_fetch_assoc($sq_purchase)) {
        if ($row_purchase['purchase_return'] == 0) {
            $total_purchase += $row_purchase['net_total'];
        } else if ($row_purchase['purchase_return'] == 2) {
            $cancel_estimate = json_decode($row_purchase['cancel_estimate']);
            $p_purchase = ($row_purchase['net_total'] - (float)($cancel_estimate[0]->net_total));
            $total_purchase += $p_purchase;
        }


           $row_purchase1 = mysqli_fetch_assoc(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Package Tour' and estimate_type_id='$row_package[booking_id]' and delete_status='0'"));

        $vendor_name = get_vendor_name_report($row_purchase['vendor_type'], $row_purchase['vendor_type_id']);
        if ($vendor_name != '') {
            $vendor_name1 .= $vendor_name . ',';
        }
    }
    $vendor_name1 = substr($vendor_name1, 0, -1);


    // currency_conversion
if($total_purchase ==''){
	$currency_amount_3 =0;
}
else{
$currency_amount3 = currency_conversion($currency,$row_purchase1['currency_code'],$total_purchase);
if($row_purchase1['currency_code'] !='0' && $currency != $row_purchase1['currency_code']){
$currency_amount_3 = ' ('.$currency_amount3.')';
}else{
$currency_amount_3= '';
}
}

    //Service Tax and Markup Tax
    $service_tax_amount = 0;
    if ($row_package['tour_service_tax_subtotal'] !== 0.00 && ($row_package['tour_service_tax_subtotal']) !== '') {
        $service_tax_subtotal1 = explode(',', $row_package['tour_service_tax_subtotal']);
        for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
            $service_tax = explode(':', $service_tax_subtotal1[$i]);
            $service_tax_amount +=  $service_tax[2];
        }
    }
    /////// Incetive ////////
    $sq_incentive = mysqli_fetch_assoc(mysqlQuery("select * from booker_sales_incentive where booking_id='$row_package[booking_id]'"));
    $discount_in = ($row_package['discount_in'] == 'Percentage') ? '%' : '';
    $discount_in = ($row_package['discount'] != 0) ? '(' . $row_package['discount'] . $discount_in . ' Off)' : '';

    $balance_amount = $row_package['net_total'] + $credit_card_charges - $tour_esti;
    // currency conversion
    $currency_amount1 = currency_conversion($currency, $row_package['currency_code'], $balance_amount);
    if ($row_package['currency_code'] != '0' && $currency != $row_package['currency_code']) {
        $currency_amount = ' (' . $currency_amount1 . ')';
    } else {
        $currency_amount = '';
    }
    $cust_user_name = '';
    $sq_quo = mysqli_fetch_assoc(mysqlQuery("select user_id from package_tour_quotation_master where quotation_id='$row_package[quotation_id]'"));
    if ($sq_quo['user_id'] != 0) {
        $row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$sq_quo[user_id]'"));
        $cust_user_name = ' (' . $row_user['name'] . ')';
    }


   $currency_amount2 = currency_conversion($currency,$row_package['currency_code'],$total_paid);
            if($row_package['currency_code'] !='0' && $currency != $row_package['currency_code']){
                $currency_amount_2 = ' ('.$currency_amount2.')';
            }else{
                $currency_amount_2 = '';
            }
        


    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B' . $row_count, ++$count)
        ->setCellValue('C' . $row_count, get_package_booking_id($row_package['booking_id'], $year))
        ->setCellValue('D' . $row_count, $customer_name . $cust_user_name)
        // ->setCellValue('E' . $row_count, $contact_no)
        ->setCellValueExplicit('E' . $row_count, $contact_no, PHPExcel_Cell_DataType::TYPE_STRING)

        ->setCellValue('F' . $row_count, $email_id)
        ->setCellValue('G' . $row_count, $sq_total_member)
        ->setCellValue('H' . $row_count, get_date_user($row_package['booking_date']))
        ->setCellValue('I' . $row_count, ($row_package['tour_name']))
        ->setCellValue('J' . $row_count, get_date_user($row_package['tour_from_date']) . ' To ' . get_date_user($row_package['tour_to_date']))
        ->setCellValue('K' . $row_count, number_format($row_package['basic_amount'], 2))
        ->setCellValue('L' . $row_count, number_format($row_package['service_charge'], 2) . $discount_in)
        ->setCellValue('M' . $row_count, number_format($service_tax_amount, 2))
        ->setCellValue('N' . $row_count, number_format($row_package['tcs_tax'], 2))
        ->setCellValue('O' . $row_count, number_format($row_package['tds'], 2))
        ->setCellValue('P' . $row_count, number_format($credit_card_charges, 2))
        ->setCellValue('Q' . $row_count, number_format($tour_fee, 2))
        ->setCellValue('R' . $row_count, number_format($tour_esti, 2))
        ->setCellValue('S' . $row_count, number_format($total_amount, 2) . $currency_amount)
        ->setCellValue('T' . $row_count, number_format($total_paid, 2).$currency_amount_2)
        ->setCellValue('U' . $row_count, number_format($total_balance, 2))
        ->setCellValue('V' . $row_count, get_date_user($row_package['due_date']))
        ->setCellValue('W' . $row_count, number_format($total_purchase, 2).$currency_amount_3)
        ->setCellValue('X' . $row_count, $vendor_name1)
        ->setCellValue('Y' . $row_count, $branch_name)
        ->setCellValue('Z' . $row_count, $emp_name)
        ->setCellValue('AA' . $row_count, number_format($sq_incentive['incentive_amount'], 2));

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':AA' . $row_count)->applyFromArray($borderArray);

    $row_count++;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B' . $row_count, "")
        ->setCellValue('C' . $row_count, "")
        ->setCellValue('D' . $row_count, "")
        ->setCellValue('E' . $row_count, "")
        ->setCellValue('F' . $row_count, "")
        ->setCellValue('G' . $row_count, "")
        ->setCellValue('H' . $row_count, "")
        ->setCellValue('J' . $row_count, "")
        ->setCellValue('K' . $row_count, "")
        ->setCellValue('L' . $row_count, "")
        ->setCellValue('M' . $row_count, "")
        ->setCellValue('N' . $row_count, "")
        ->setCellValue('O' . $row_count, "")
        ->setCellValue('P' . $row_count, "")
        ->setCellValue('Q' . $row_count, "")
        ->setCellValue('R' . $row_count, 'TOTAL CANCEL : ' . number_format($cancel_total, 2))
        ->setCellValue('S' . $row_count, 'TOTAL SALE :' . number_format($sale_total, 2))
        ->setCellValue('T' . $row_count, 'TOTAL PAID : ' . number_format($paid_total, 2))
        ->setCellValue('U' . $row_count, 'TOTAL BALANCE :' . number_format($balance_total, 2))
        ->setCellValue('V' . $row_count, "");

    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':V' . $row_count)->applyFromArray($header_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B' . $row_count . ':V' . $row_count)->applyFromArray($borderArray);
}

//////////////////////////****************Content End**************////////////////////////////////

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
for ($col = 'A'; $col !== 'N'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="PackageTourSummary(' . date('d-m-Y H:i') . ').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
