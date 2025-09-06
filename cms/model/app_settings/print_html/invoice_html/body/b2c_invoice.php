<?php
//Generic Files
include "../../../../model.php";
include "../../print_functions.php";
require("../../../../../classes/convert_amount_to_word.php");

global $currency;
//Parameters
$invoice_no = $_GET['invoice_no'];
$invoice_date = $_GET['invoice_date'];
$customer_id = $_GET['customer_id'];
$service_name = $_GET['service_name'];
$total_pax = $_GET['total_pax'];
$credit_card_charges = $_GET['credit_card_charges'];
$sac_code = $_GET['sac_code'];
$tour_name = $_GET['tour_name'];
$booking_id = $_GET['booking_id'];
$total_cost = $_GET['total_cost'];
$tax_amount = $_GET['tax_amount'];
$tax = $_GET['tax'];
$grand_total = $_GET['grand_total'];
$coupon_amount = $_GET['coupon_amount'];
$net_total = $_GET['net_total'];
$paid_amount = $_GET['paid_amount'];
$bg = $_GET['bg'];
$canc_amount = $_GET['canc_amount'];
$bal_amount = $_GET['bal_amount'];
$branch_admin_id = ($_SESSION['branch_admin_id'] != '') ? $_SESSION['branch_admin_id'] : 1;

$net_total1 = $net_total;

$charge = ($credit_card_charges != '') ? $credit_card_charges : 0;
$coupon_amount = ($coupon_amount != '') ? $coupon_amount : 0;

if ($service_name == 'Package Invoice') {
  $sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' and delete_status='0'"));
} else {
  $sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$booking_id' and delete_status='0'"));
}

// $total_balance = (float)($net_total) - (float)($paid_amount);
$total_balance1 = currency_conversion($currency, $currency, $total_balance);
$charge = currency_conversion($currency, $currency, $charge);
$total_cost = currency_conversion($currency, $currency, $total_cost);
$tax_amount = currency_conversion($currency, $currency, $tax_amount);
$grand_total = currency_conversion($currency, $currency, $grand_total);
$coupon_amount = currency_conversion($currency, $currency, $coupon_amount);
$net_total = currency_conversion($currency, $currency, $net_total);
$paid_amount = currency_conversion($currency, $currency, $paid_amount);

$net_total1 = (float)(str_replace(",", "", $net_total1));
$amount_in_word = $amount_to_word->convert_number_to_words($net_total1);
//Header
if ($app_invoice_format == "Standard") {
  include "../headers/standard_header_html.php";
}
if ($app_invoice_format == "Regular") {
  include "../headers/regular_header_html.php";
}
if ($app_invoice_format == "Advance") {
  include "../headers/advance_header_html.php";
}
?>
<section class="no-pad main_block">
  <?php if ($service_name == 'Activity Invoice') { ?>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Activity Name : <?= $tour_name ?> </span></p>
    </div>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Total Guest(s) : <?= $total_pax ?> </span></p>
    </div>
  <?php } else if ($service_name == 'Transfer Invoice') { ?>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Transfer Name : <?= $tour_name ?> </span></p>
    </div>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Total Pass(s) : <?= $total_pax ?> </span></p>
    </div>
  <?php } else if ($service_name == 'Hotel Invoice') { ?>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Hotel Name : <?= $tour_name ?> </span></p>
    </div>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Total Room(s) : <?= $total_pax ?> </span></p>
    </div>
  <?php } else { ?>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Tour Name : <?= $tour_name ?> </span></p>
    </div>
    <div class="col-md-6 mg_tp_10">
      <p class="border_lt"><span class="font_5">Total Guest(s) : <?= $total_pax ?> </span></p>
    </div>
  <?php } ?>
</section>

<section class="print_sec main_block">
  <!-- invoice_receipt_body_calculation -->
  <div class="row">
    <div class="col-md-12">
      <div class="main_block inv_rece_calculation border_block">
        <div class="col-md-6">
          <p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?= $total_cost ?></span></p>
        </div>
        <div class="col-md-6">
          <p class="border_lt"><span class="font_5">CREDIT CARD CHARGES </span><span class="float_r"><?= $charge ?></span></p>
        </div>
        <div class="col-md-6">
          <p class="border_lt"><span class="font_5">TAX</span><span class="float_r"><?= $tax . ' ' . $tax_amount ?></span></p>
        </div>
        <div class="col-md-6">
          <p class="border_lt"><span class="font_5">ADVANCE PAID </span><span class="font_5 float_r"><?= $paid_amount ?></span></p>
        </div>
        <div class="col-md-6">
          <p class="border_lt"><span class="font_5">COUPON AMOUNT </span><span class="float_r"><?= $coupon_amount ?></span></p>
        </div>
        <?php
        if ($bg != '') { ?>
          <div class="col-md-6">
            <p class="border_lt"><span class="font_5">CANCELLATION CHARGES </span><span class="font_5 float_r"><?= $currency_code . " " . number_format($canc_amount, 2) ?></span></p>
          </div>
          <div class="col-md-6">
            <p class="border_lt"><span class="font_5">TOTAL </span><span class="font_5 float_r"><?= $net_total ?></span></p>
          </div>
        <?php } ?>
        <?php
        // if($bg != ''){ 
        ?>
        <div class="col-md-6">
          <p class="border_lt"><span class="font_5">CURRENT DUE </span><span class="font_5 float_r"><?= $currency_code . " " . number_format((float)($bal_amount), 2) ?></span></p>
        </div>
        <?php //} 
        ?>
        <?php
        if ($bg == '') { ?>
          <!-- <div class="col-md-6"></div> -->
          <div class="col-md-6">
            <p class="border_lt"><span class="font_5">TOTAL </span><span class="font_5 float_r"><?= $net_total ?></span></p>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>

<!-- invoice_receipt_body_calculation -->
<?php
//Footer
include "../generic_footer_html.php"; ?>