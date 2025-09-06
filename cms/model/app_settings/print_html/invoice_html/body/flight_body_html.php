<?php
//Generic Files
include "../../../../model.php"; 
include "../../print_functions.php";
require("../../../../../classes/convert_amount_to_word.php"); 

//Parameters
global $currency;
$invoice_no = $_GET['invoice_no'];
$ticket_id = $_GET['ticket_id'];
$invoice_date = $_GET['invoice_date'];
$customer_id = $_GET['customer_id'];
$service_name = $_GET['service_name'];
$basic_cost1 = $_GET['basic_cost'];
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
$cancel_type = $_GET['cancel_type'];
$ticket_type =$_GET['ticket_type'];


$charge = ($credit_card_charges!='') ? $credit_card_charges : 0;
$balance_amount = ($balance_amount < 0) ? 0 : $balance_amount;

$sq_passenger_count = mysqli_fetch_assoc(mysqlQuery("select count(*) as cnt from ticket_master_entries where ticket_id = '$ticket_id'"));
$sq_fields = mysqli_fetch_assoc(mysqlQuery("select * from ticket_master where ticket_id = '$ticket_id' and delete_status='0'"));
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : $sq_fields['branch_admin_id'];
$bsmValues = json_decode($sq_fields['bsm_values']);

$other_tax = $sq_fields['other_taxes'];
$yq_tax = $sq_fields['yq_tax'];
$tax_show = '';
$newBasic = $basic_cost1;
$newSC = $sq_fields['service_charge'];
$service_charge = $sq_fields['service_charge'];

if($service_charge_switch == 'No'){
  $basic_service_amt = floatval($newBasic) + floatval($newSC);
}


  $currency_code1 =$sq_fields['currency_code'];
//////////////////Service Charge Rules
$service_tax_amount = 0;
$name = '';
if($sq_fields['service_tax_subtotal'] !== 0.00 && ($sq_fields['service_tax_subtotal']) !== ''){
  $service_tax_subtotal1 = explode(',',$sq_fields['service_tax_subtotal']);
  for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
    $service_tax = explode(':',$service_tax_subtotal1[$i]);
    $service_tax_amount +=  $service_tax[2];
    $name .= $service_tax[0]  . $service_tax[1] .', ';
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
if($sq_fields['service_tax_markup'] !== 0.00 && $sq_fields['service_tax_markup'] !== ""){
  $service_tax_markup1 = explode(',',$sq_fields['service_tax_markup']);
  for($i=0;$i<sizeof($service_tax_markup1);$i++){
    $service_tax = explode(':',$service_tax_markup1[$i]);
    $markupservice_tax_amount += $service_tax[2];
  }
}

$markupservice_tax_amount_show = currency_conversion($currency,$currency_code1,$markupservice_tax_amount);

if($bsmValues[0]->markup != ''){ //inclusive markup
  $newBasic = $basic_cost1 + $sq_fields['markup'] + $markupservice_tax_amount;
  $tax_show= '';
}
else{
  $newBasic = $basic_cost1;
  $newSC = $service_charge ;
}
///////////Basic Amount Rules
if($bsmValues[0]->basic != ''){ //inclusive markup
  $newBasic = $basic_cost1 + $service_tax_amount + $sq_fields['markup'] + $markupservice_tax_amount;
}
$other_charges = $markupservice_tax_amount + $sq_fields['markup'];

//Header
if($app_invoice_format == "Standard"){include "../headers/standard_header_html.php"; }
if($app_invoice_format == "Regular"){include "../headers/regular_header_html.php"; }
if($app_invoice_format == "Advance"){include "../headers/advance_header_html.php"; }

$net_amount1 =  $basic_cost1 + $sq_fields['service_charge'] + $sq_fields['markup'] + $other_tax + $yq_tax + $markupservice_tax_amount + $service_tax_amount - $sq_fields['basic_cost_discount'] - $sq_fields['tds'] ;
// + $sq_fields['roundoff']; 


$roundoff = $sq_fields['roundoff'];

	if($roundoff < 0){
		// Only add roundoff if it's negative
		$net_amount1_1 = $net_amount1+abs($roundoff) ;
		
	} else {
	

		$net_amount1_1 = $net_amount1 -  abs($roundoff);
	}

$word_amount =  $net_amount1;
// $amount_in_word = $amount_to_word->convert_number_to_words($word_amount);

$net_total1 = currency_conversion($currency,$sq_fields['currency_code'],$word_amount);
$amount_in_word = $amount_to_word->convert_number_to_words($net_total1,$currency_code1);

$sq_flight = mysqli_fetch_assoc(mysqlQuery("select first_name,last_name from ticket_master_entries where ticket_id='$ticket_id' "));
$guest_name = $sq_flight['first_name'].' '.$sq_flight['last_name'];
?>

<div class="row mg_tp_20">
  <div class="col-md-12"><p class="border_lt"><span class="font_5">PASSENGER:  <?= $guest_name ?></span></p></div>
</div>
<!-- invoice_receipt_body_table-->
  <div class="main_block inv_rece_table">
    <div class="row">
      <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-bordered no-marg" id="tbl_emp_list" style="padding: 0 !important;">
          <thead>
            <tr class="table-heading-row">
              <th class="font_s_12">S.NO</th>
              <th class="font_s_12">NAME</th>
              <th class="font_s_12">From_To_SECTOR</th>
              <th class="font_s_12">Departure_date</th>
              <th class="font_s_12">Time</th>
              <th class="font_s_12">Airline_PNR</th>
              <th class="font_s_12">FLIGHT_NO</th>
              <th class="font_s_12">Ticket_NO</th>
            </tr>
          </thead>
          <tbody>   
          <?php 
          $count = 1;
          $sq_passenger = mysqlQuery("select * from ticket_master_entries where ticket_id = '$ticket_id'");
          while($row_passenger = mysqli_fetch_assoc($sq_passenger))
          {
            $bg1 = ($row_passenger['status'] == 'Cancel') ? ' (Cancelled)' : '';
            ?>
            <tr class="odd">
              <td><?php echo $count; ?></td>
              <td><?php echo $row_passenger['first_name'].' '.$row_passenger['middle_name'].' '.$row_passenger['last_name'].$bg1; ?></td>
            <?php
            $sq_dest1 = mysqlQuery("select * from ticket_trip_entries where passenger_id = '$row_passenger[entry_id]'");
            $dep_final = '';
            $flight_no = '';
            $dep_time = '';
            $time = '';
            $pnr = $row_passenger['gds_pnr'].'<br>';
            while($sq_dest = mysqli_fetch_assoc($sq_dest1)){
              

              $sectors_dep = explode('(',$sq_dest['departure_city']);
              $sectors_dep = $sectors_dep[sizeof($sectors_dep)-1];
              $sectors_ar = explode('(',$sq_dest['arrival_city']);
              $sectors_ar = $sectors_ar[sizeof($sectors_ar)-1];
              $dep_time .= date("d-m-Y", strtotime($sq_dest['departure_datetime'])).'<br>';
              $time .= date("H:i", strtotime($sq_dest['departure_datetime'])).'<br>';
              $flight_no .=  $sq_dest['flight_no'].'<br>';
              
              $cancel_clr = ($sq_dest['status'] == 'Cancel') ? '<span style="color:red !important;">'.$sectors_dep.' - '.$sectors_ar.' [Cancelled]</span>' : $sectors_dep.' - '.$sectors_ar;

              $dep_final .= $cancel_clr.' ,<br>';   
            }
            ?>
              <td><?php echo rtrim(str_replace(array( '(', ')' ), '', $dep_final),', <br>') ?></td>
              <td><?php echo $dep_time; ?></td>
              <td><?php echo $time; ?></td>
              <td style="text-transform: uppercase;"><?php echo $pnr; ?></td>
              <td><?php echo $flight_no; ?></td>
              <td><?php echo strtoupper($row_passenger['ticket_no']) ?></td>
              </tr>
          <?php $count++; } ?>
          </tbody>
        </table>
      </div>
    </div>
    </div>
  </div>

<!-- invoice_receipt_body_calculation -->
<section class="print_sec main_block">
  <div class="row">
    <div class="col-md-12">
      <div class="main_block inv_rece_calculation border_block">
        <?php if($service_charge_switch == 'No'){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1 ,$basic_service_amt); ?></span></p></div>
        <?php }else{ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">BASIC AMOUNT </span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1 ,$newBasic); ?></span></p></div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">ROUND OFF </span><span class="float_r"><?php  echo currency_conversion($currency,$currency_code1 ,'0.00'); ?></span></p></div> 
        <div class="col-md-6"><p class="border_lt"><span class="font_5">OTHER CHARGES AND TAXES </span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1 ,$other_charges); ?></span></p></div> 
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TOTAL </span><span class="font_5 float_r"><?php echo currency_conversion($currency,$currency_code1 ,$net_amount1); ?></span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">OTHER TAX + YQ</span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1 ,$sq_fields['other_taxes'] + $sq_fields['yq_tax']); ?></span></p></div>  
        <div class="col-md-6"><p class="border_lt"><span class="font_5">CREDIT CARD CHARGES </span><span class="float_r"><?= currency_conversion($currency,$currency_code1 , $charge)?></span></p></div>
        <?php if($service_charge_switch == 'Yes'){ ?>
          <div class="col-md-6">
              <p class="border_lt"><span class="font_5">SERVICE CHARGE </span><span class="float_r"><?php echo currency_conversion($currency,$currency_code1 ,$newSC); ?></span></p>
          </div>
        <?php }else{ ?>
          <div class="col-md-6">
              <p class="border_lt"><span class="font_5"> </span><span class="float_r"></span></p>
          </div>
        <?php } ?>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">ADVANCE PAID </span><span class="font_5 float_r"><?php echo currency_conversion($currency,$currency_code1 ,$total_paid); ?></span></p></div>  
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TAX</span><span class="float_r"><?= $tax_show1 ?></span></p></div> 
        <?php
        if($bg != ''){ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">CANCELLATION CHARGES</span><span class="float_r"><?= currency_conversion($currency,$currency_code1 ,$canc_amount) ?></span></p></div>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">DISCOUNT</span><span class="float_r"><?= currency_conversion($currency,$currency_code1 ,$sq_fields['basic_cost_discount']) ?></span></p></div>
            
            <div class="col-md-6"><p class="border_lt"><span class="font_5">CURRENT DUE </span><span class="font_5 float_r"><?php echo currency_conversion($currency,$currency_code1 ,$balance_amount); ?></span></p></div>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">TDS</span><span class="float_r"><?= currency_conversion($currency,$currency_code1 ,$sq_fields['tds']) ?></span></p></div> 
          <?php
        }
        else{ ?>
          <div class="col-md-6"><p class="border_lt"><span class="font_5">CURRENT DUE </span><span class="font_5 float_r"><?php echo currency_conversion($currency,$currency_code1 ,$balance_amount); ?></span></p></div> 
        <div class="col-md-6"><p class="border_lt"><span class="font_5">DISCOUNT</span><span class="float_r"><?= currency_conversion($currency,$currency_code1 ,$sq_fields['basic_cost_discount']) ?></span></p></div>
        <div class="col-md-6"><p><span class="font_5">&nbsp;</span><span class="float_r">&nbsp;</span></p></div>
        <div class="col-md-6"><p class="border_lt"><span class="font_5">TDS</span><span class="float_r"><?= currency_conversion($currency,$currency_code1 ,$sq_fields['tds']) ?></span></p></div> 
        <?php } ?>
      </div>
    </div>
  </div>
</section>

<?php 
//Footer
include "../generic_footer_html.php"; ?>