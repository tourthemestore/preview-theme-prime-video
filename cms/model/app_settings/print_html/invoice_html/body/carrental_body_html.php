<?php
//Generic Files
include "../../../../model.php"; 
include "../../print_functions.php";
require("../../../../../classes/convert_amount_to_word.php"); 

//Parameters
$invoice_no = $_GET['invoice_no'];
$booking_id = $_GET['booking_id'];
$invoice_date = $_GET['invoice_date'];
$customer_id = $_GET['customer_id'];
$service_name = $_GET['service_name'];
$basic_cost1 = $_GET['basic_cost'];
$service_charge = isset($_GET['service_charge']) ? $_GET['service_charge'] : '';
$taxation_type = $_GET['taxation_type'];
$service_tax_per = $_GET['service_tax_per'];

$net_amount = $_GET['net_amount'];
$bank_name = isset($_GET['bank_name']) ? $_GET['bank_name'] : '';
$total_paid = $_GET['total_paid'];
$balance_amount = $_GET['balance_amount'];
$sac_code = $_GET['sac_code'];
$credit_card_charges = $_GET['credit_card_charges'];
$bg = $_GET['bg'];
$canc_amount = $_GET['canc_amount'];

$charge = ($credit_card_charges!='') ? $credit_card_charges : 0 ;
$balance_amount = ($balance_amount < 0) ? 0 : $balance_amount;


  

// $amount_in_word = $amount_to_word->convert_number_to_words($net_amount);
$sq_car = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'"));
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : $sq_car['branch_admin_id'];
$sq_count = mysqli_num_rows(mysqlQuery("select * from car_rental_booking_vehicle_entries where booking_id='$booking_id'"));

$currency_code1 =$sq_car['currency_code'];

$roundoff= $sq_car['roundoff'];


if($roundoff < 0){
  // Only add roundoff if it's negative
  $total_amt =$net_amount+  abs($roundoff);

 
  
} else {
  $total_amt =$net_amount - abs($roundoff);
  

}




$net_total1 = currency_conversion($currency,$sq_hotel['currency_code'],$total_amt);
$amount_in_word = $amount_to_word->convert_number_to_words($net_total1,$currency_code1);





// $amount_in_word = $amount_to_word->convert_number_to_words( $total_amt);

$tax_show = '';
$newBasic = $basic_cost1;
$newSC = $sq_car['service_charge'];

if($service_charge_switch == 'No'){
  $basic_service_amt1 = floatval($newBasic) + floatval($newSC);
}

$service_charge = $sq_car['service_charge'];
$bsmValues = json_decode($sq_car['bsm_values']);
//////////////////Service Charge Rules
$service_tax_amount = 0;
$name = '';
if($sq_car['service_tax_subtotal'] !== 0.00 && ($sq_car['service_tax_subtotal']) !== ''){
  $service_tax_subtotal1 = explode(',',$sq_car['service_tax_subtotal']);
  for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
    $service_tax = explode(':',$service_tax_subtotal1[$i]);
    $service_tax_amount +=  $service_tax[2];
    $name .= $service_tax[0] . ' ' .$service_tax[1].',';
  }
}

$service_tax_amount_show = currency_conversion($currency,$currency_code1,$service_tax_amount);

if($bsmValues[0]->service != ''){ //inclusive service charge
  $newBasic = $basic_cost1;
  $newSC = $service_tax_amount + $service_charge;
}
else{
  $tax_show =  rtrim($name, ', ').' : ' . number_format($service_tax_amount,2);
  $newSC = $service_charge;

  $tax_show1= rtrim($name, ', ').' : ' . $service_tax_amount_show;
}
////////////////////Markup Rules
$markupservice_tax_amount = 0;
if($sq_car['markup_cost_subtotal'] !== 0.00 && $sq_car['markup_cost_subtotal'] !== ""){
  $service_tax_markup1 = explode(',',$sq_car['markup_cost_subtotal']);
  for($i=0;$i<sizeof($service_tax_markup1);$i++){
    $service_tax = explode(':',$service_tax_markup1[$i]);
    $markupservice_tax_amount += $service_tax[2];
  }
}

$markupservice_tax_amount_show = currency_conversion($currency,$currency_code1,$markupservice_tax_amount);

if($bsmValues[0]->markup != ''){ //inclusive markup
  $newBasic = $basic_cost1 + $sq_car['markup_cost'] + $markupservice_tax_amount;
}
else{
  $newBasic = $basic_cost1;
  $newSC = $service_charge;
  // $tax_show = rtrim($name, ', ') .' : ' . ($markupservice_tax_amount + $service_tax_amount);
}
////////////Basic Amount Rules
if($bsmValues[0]->basic != ''){ //inclusive markup
  $newBasic = $basic_cost1 + $service_tax_amount + $sq_car['markup_cost'] + $markupservice_tax_amount;
}
$other_charges = $markupservice_tax_amount + $sq_car['markup_cost'];




  $service_tax_amount_show = explode(' ',$service_tax_amount_show);
      $service_tax_amount_show1 = str_replace(',','',$service_tax_amount_show[1]);

//Header
if($app_invoice_format == "Standard"){include "../headers/standard_header_html.php"; }
if($app_invoice_format == "Regular"){include "../headers/regular_header_html.php"; }
if($app_invoice_format == "Advance"){include "../headers/advance_header_html.php"; }
?>

<hr class="no-marg">
<!-- <div class="col-md-12 mg_tp_20"><p class="border_lt"><span class="font_5">  GUEST NAME  : <?= $sq_car['pass_name'] ?></span></p></div>
<?php if($sq_count != 0){?>
<div class="main_block inv_rece_table main_block">
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive">
        <table class="table table-bordered no-marg" id="tbl_emp_list" style="padding: 0 !important;">
          <thead>
            <tr class="table-heading-row">
              <th>SR.NO</th>
              <th>Vehicle_name</th>
              <th>Vehicle_no</th>
              <th>Rate_PER_Km</th>
              <th>Extra_KM</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $count = 1;
          $sq_vehicle_entries = mysqlQuery("select * from car_rental_booking where booking_id='$booking_id' and delete_status='0'");
          while($row_vehicle = mysqli_fetch_assoc($sq_vehicle_entries)){              
            $sq_vehicle1 = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_booking_vehicle_entries where booking_id='$booking_id'"));
            $sq_vehicle = mysqli_fetch_assoc(mysqlQuery("select * from car_rental_vendor_vehicle_entries where vehicle_id='$sq_vehicle1[vehicle_id]'"));
            if($sq_vehicle['vehicle_name']!=''){
            ?>
            <tr class="odd">
              <td><?php echo $count; ?></td>
              <td><?php echo $sq_vehicle['vehicle_name']; ?></td>
              <td><?= $sq_vehicle['vehicle_no'] ?></td>
              <td><?php echo ($row_vehicle['rate_per_km']); ?></td>
              <td><?php echo $row_vehicle['extra_km']; ?></td>
            </tr>
            <?php }
              $count++;
            } ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div> -->
<?php } ?>
<section class="print_sec main_block">

<!-- invoice_receipt_body_calculation -->
  <?php
  if($bg != ''){
    $balance = ($total_paid > $canc_amount) ? 0 : floatval($canc_amount) - floatval($total_paid);
  }else{
    $balance = floatval($total_amt) - floatval($total_paid);
  }
  ?>
  <div class="row">
    <div class="col-md-12">
      <div class="main_block inv_rece_calculation border_block">
        <?php
        if($service_charge_switch == 'No'){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?=  currency_conversion($currency,$currency_code1 ,$basic_service_amt1) ?></span></p></div>
        <?php } else{ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?= currency_conversion($currency,$currency_code1,$newBasic) ?></span></p></div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TOTAL </span><span class="font_5 float_r"><?= currency_conversion($currency,$currency_code1,$total_amt) ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">OTHER CHARGES AND TAXES </span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1,$other_charges) ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">CREDIT CARD CHARGES </span><span class="float_r"><?= currency_conversion($currency,$currency_code1 ,$charge)?></span></p></div>
        <?php
        if($service_charge_switch == 'Yes'){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">SERVICE CHARGE </span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1,$newSC); ?></span></p></div>
        <?php }else{ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5"> </span><span class="float_r"></span></p></div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">ADVANCE PAID </span><span class="font_5 float_r"><?= currency_conversion($currency,$currency_code1 ,floatval($total_paid)+floatval($charge)) ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TAX</span><span class="float_r"><?= $tax_show1 ?></span></p></div>
        <?php
        if($bg != ''){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">CANCELLATION CHARGES</span><span class="float_r"><?= currency_conversion($currency,$currency_code1,$canc_amount) ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">ROUND OFF</span><span class="float_r"><?= currency_conversion($currency,$currency_code1,'0.00') ?></span></p></div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">CURRENT DUE </span><span class="font_5 float_r"><?= currency_conversion($currency,$currency_code1 ,$balance_amount) ?></span></p></div>
        <?php
        if($bg == ''){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">ROUND OFF</span><span class="float_r"><?= currency_conversion($currency,$currency_code1,$sq_car['roundoff']) ?></span></p></div>
        <?php } ?>
      </div>
    </div>
  </div>

</section>
<?php 
//Footer
include "../generic_footer_html.php"; ?>