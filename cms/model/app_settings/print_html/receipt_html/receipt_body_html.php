<?php
//Generic Files
include "../../../model.php";
include "../print_functions.php";
require("../../../../classes/convert_amount_to_word.php");
global $currency;

$payment_id_name = isset($_GET['payment_id_name']) ? $_GET['payment_id_name'] : '';
$payment_id = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
$receipt_date = isset($_GET['receipt_date']) ? $_GET['receipt_date'] : '';
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$booking_name = isset($_GET['booking_name']) ? $_GET['booking_name'] : '';
$travel_date = isset($_GET['travel_date']) ? $_GET['travel_date'] : '';
$payment_amount = isset($_GET['payment_amount']) ? $_GET['payment_amount'] : 0;
$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';
$payment_date = isset($_GET['payment_date']) ? $_GET['payment_date'] : '';
$payment_mode = isset($_GET['payment_mode']) ? $_GET['payment_mode'] : '';
$bank_name = isset($_GET['bank_name']) ? $_GET['bank_name'] : '';
$confirm_by = isset($_GET['confirm_by']) ? $_GET['confirm_by'] : '';
$receipt_type = isset($_GET['receipt_type']) ? $_GET['receipt_type'] : '';
$outstanding = isset($_GET['outstanding']) ? (float)($_GET['outstanding']) : 0;
$currency_code = isset($_GET['currency_code']) ? $_GET['currency_code'] : $currency;
$tour = isset($_GET['tour']) ? $_GET['tour'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$net_amt =$_GET['net_amt'];

$pay_id =$_GET['pay_id'];
$pass_name = '';



if ($booking_name == 'Hotel Booking') {
  $pass_name = isset($_GET['pass_name']) ? $_GET['pass_name'] : '';
}
if ($booking_name == 'Flight Ticket Booking') {
  $pass_name = isset($_GET['pass_name']) ? $_GET['pass_name'] : '';
}
// This is new customization for displaying history
$table_name = $_GET['table_name'];
$inside_customer_id = $_GET['in_customer_id'];
$customer_field = $_GET['customer_field'];
if ($table_name != '') {
  $values_query = "select * from $table_name where 1 ";
  if ($table_name != "payment_master" && $table_name != "package_payment_master") {
    $date_key = "payment_date";
    $amount_key = "payment_amount";
    $credit_charges = "credit_charges";
  } else {
    $date_key = "date";
    $amount_key = "amount";
    $credit_charges = "credit_charges";
  }
  if ($table_name != 'receipt_payment_master') {

    $values_query .= " and $customer_field = '$inside_customer_id' and clearance_status!='Pending' and clearance_status!='Cancelled'";
  } else {
    $values_query .= " and id='$booking_id'";
    $credit_charges = "";
  }
  $values_query .= " and payment_id <= '$pay_id'";

  $values_query .= " and $amount_key !='0' order by $date_key desc";
}


$total_payment = 0; // Initialize total

$values_fetch = mysqlQuery($values_query);
while ($rows = mysqli_fetch_assoc($values_fetch)) {

    $credit_charges_val = isset($rows[$credit_charges]) ? $rows[$credit_charges] : 0;

    if ($receipt_type == 'Hotel Receipt' || $receipt_type == 'Tour Receipt' || $receipt_type == 'Activity Receipt' || $receipt_type == 'Visa Receipt') {
        $converted_amt = $rows[$amount_key] + $credit_charges_val;
        $payment_amount1 = currency_conversion($currency, $currency_code, $converted_amt);
        $total_payment += $converted_amt; // Sum actual amount before currency conversion
    } else {
        if ($receipt_type == 'B2B Sale Receipt') {
            $payment_amount1 = number_format($rows[$amount_key], 2);
            $total_payment += $rows[$amount_key];
        } else {
            $converted_amt = $rows[$amount_key] + $credit_charges_val;
            $payment_amount1 = number_format($converted_amt, 2);
            $total_payment += $converted_amt;
        }
    }}
    $bal_amount= $net_amt-$total_payment;
//***END****/
if ($receipt_type == 'Hotel Receipt' || $receipt_type == 'Tour Receipt' || $receipt_type == 'Activity Receipt' || $receipt_type == 'Visa Receipt' || $receipt_type =='Package Tour Receipt' ||  $receipt_type =='Group Tour Receipt' || $receipt_type =='Flight Ticket Receipt' || $receipt_type == 'Car Rental Receipt' || $receipt_type == 'Bus Receipt' || trim($receipt_type) == 'Miscellaneous Receipt') {

  $payment_amount1 = currency_conversion($currency, $currency_code, $payment_amount);
  $outstanding1 = currency_conversion($currency, $currency_code, $bal_amount);
  $amount_in_word = $amount_to_word->convert_number_to_words($payment_amount, $currency_code);
} else {
  $payment_amount1 = number_format($payment_amount, 2);
  $outstanding1 = number_format($bal_amount, 2);
  $amount_in_word = $amount_to_word->convert_number_to_words($payment_amount);
}

if ($payment_mode == 'Cheque') {
  $payment_mode1 = $payment_mode . '(' . $transaction_id . ')';
} else {
  $payment_mode1 = $payment_mode;
}

$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
$sq_role = mysqli_fetch_assoc(mysqlQuery("select emp_id from roles where id='$confirm_by'"));

if ($confirm_by == '' || $confirm_by == 0) {
  $booking_by = $app_name;
} else {
  $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$confirm_by'"));
  $booking_by = $sq_emp['first_name'] . ' ' . $sq_emp['last_name'];
}
include "standard_header_html.php";
?>

<section class="print_sec_bt_s main_block">
  <!-- invoice_receipt_body_table-->
  <div class="border_block inv_rece_back_detail">
    <div class="row">
      <div class="col-md-4">
        <p class="border_lt"><span class="font_5">Receipt Amount : </span><?php echo $payment_amount1; ?></p>
      </div>
      <div class="col-md-4">
        <p class="border_lt"><span class="font_5">Payment Date :
          </span><?php echo get_date_user($payment_date); ?></p>
      </div>
      <div class="col-md-4">
        <p class="border_lt"><span class="font_5">Payment Mode : </span><?php echo $payment_mode1; ?></p>
      </div>
      <?php if ($status != 'Cancelled' && $outstanding > 0) { ?><div class="col-md-4">
          <p class="border_lt"><span class="font_5">Balance : </span><?php
         
            echo  $outstanding1; ?></p>
        </div><?php } ?>
      <div class="col-md-4">
        <p class="border_lt"><span class="font_5">For Services : </span><?php echo $booking_name; ?></p>
      </div>
      <?php if ($tour != '') { ?><div class="col-md-4">
          <p class="border_lt"><span class="font_5">Tour Name : </span><?php echo $tour; ?></p>
        </div><?php } ?>

      <div class="col-md-6 hidden">
        <p class="border_lt"><span class="font_5">Travel Date : </span><?php echo $travel_date; ?></p>
      </div>
    </div>
  </div>
</section>
<?php
//Footer
include "generic_footer_html.php"; ?>