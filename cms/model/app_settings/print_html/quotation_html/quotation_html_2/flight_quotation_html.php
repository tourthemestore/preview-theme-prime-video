<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";
global $app_quot_img, $currency, $quot_note;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/car_flight/flight/index.php'"));
$branch_status = $sq['branch_status'];
if ($branch_admin_id != 0) {
  $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_admin_id'"));
  $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
  $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='$branch_admin_id' and active_flag='Active'"));
} else {
  $branch_details = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='1'"));
  $sq_bank_count = mysqli_num_rows(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
  $sq_bank_branch = mysqli_fetch_assoc(mysqlQuery("select * from bank_master where branch_id='1' and active_flag='Active'"));
}

$quotation_id = $_GET['quotation_id'];

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Flight Quotation' and active_flag ='Active'"));

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from flight_quotation_master where quotation_id='$quotation_id'"));
$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));
$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year = $yr[0];
$sq_plane = mysqli_fetch_assoc(mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'"));
if ($sq_emp_info['first_name'] == '') {
  $emp_name = 'Admin';
} else {
  $emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
}

$tax_show = '';
$newBasic = $basic_cost1 = $sq_quotation['subtotal'];
$service_charge = $sq_quotation['service_charge'];
$bsmValues = json_decode($sq_quotation['bsm_values']);
//////////////////Service Charge Rules
$service_tax_amount = 0;
$percent = '';
if ($sq_quotation['service_tax'] !== 0.00 && ($sq_quotation['service_tax']) !== '') {
  $service_tax_subtotal1 = explode(',', $sq_quotation['service_tax']);
  for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
    $service_tax = explode(':', $service_tax_subtotal1[$i]);
    $service_tax_amount +=  $service_tax[2];
    $percent .= $service_tax[0]  . $service_tax[1] . ', ';
  }
}
////////////////////Markup Rules
$markupservice_tax_amount = 0;
if ($sq_quotation['markup_cost_subtotal'] !== 0.00 && $sq_quotation['markup_cost_subtotal'] !== "") {
  $service_tax_markup1 = explode(',', $sq_quotation['markup_cost_subtotal']);
  for ($i = 0; $i < sizeof($service_tax_markup1); $i++) {
    $service_tax = explode(':', $service_tax_markup1[$i]);
    $markupservice_tax_amount += $service_tax[2];
  }
}
// $total_tax_amount_show = currency_conversion($currency, $currency, (float)($service_tax_amount) + (float)($markupservice_tax_amount) + $sq_quotation['roundoff']);


$tax_cost =  (float)($service_tax_amount) + (float)($markupservice_tax_amount) + $sq_quotation['roundoff'];

            $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $tax_cost);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $tax_cost != $currency_amount1) {
	 $total_tax_amount_show =  $currency_amount1 ;
	} else {
	$total_tax_amount_show = $tax_cost;
	}

if (($bsmValues[0]->service != '' || $bsmValues[0]->basic != '')  && $bsmValues[0]->markup != '') {
  $tax_show = '';
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $markupservice_tax_amount + $service_charge + $service_tax_amount;
} elseif (($bsmValues[0]->service == '' || $bsmValues[0]->basic == '')  && $bsmValues[0]->markup == '') {
  $tax_show = $percent . ' ' . ($total_tax_amount_show);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge;
} elseif (($bsmValues[0]->service != '' || $bsmValues[0]->basic != '') && $bsmValues[0]->markup == '') {
  $tax_show = $percent . ' ' . ($markupservice_tax_amount);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge + $service_tax_amount;
} else {
  $tax_show = $percent . ' ' . ($service_tax_amount);
  $newBasic = $basic_cost1 + $sq_quotation['markup_cost'] + $service_charge + $markupservice_tax_amount;
}
// $quotation_cost = currency_conversion($currency, $currency, $sq_quotation['quotation_cost']);


$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['quotation_cost']);

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && $sq_quotation['quotation_cost'] != $currency_amount1) {
	$quotation_cost = $currency_amount1 ;
	} else {
	$quotation_cost = $sq_quotation['quotation_cost'];
	}
?>

<!-- landingPage -->
<section class="landingSec main_block">
  <div class="landingPageTop main_block">
    <img src="<?= $app_quot_img ?>" class="img-responsive">
    <span class="landingPageId"><?= get_quotation_id($quotation_id, $year) ?></span>
  </div>

  <div class="ladingPageBottom main_block side_pad">

    <div class="row">
      <div class="col-md-4">
        <div class="landigPageCustomer mg_tp_20">
          <h3 class="customerFrom">PREPARED FOR</h3>
          <span class="customerName mg_tp_10"><i class="fa fa-user"></i> : <?= $sq_quotation['customer_name'] ?></span><br>
          <span class="customerMail mg_tp_10"><i class="fa fa-envelope"></i> : <?= $sq_quotation['email_id'] ?></span><br>
          <span class="customerMobile mg_tp_10"><i class="fa fa-phone"></i> : <?= $sq_quotation['mobile_no'] ?></span><br>
          <span class="generatorName mg_tp_10">PREPARED BY <?= $emp_name ?></span><br>
        </div>
      </div>
      <div class="col-md-8 text-right">

        <div class="detailBlock text-center">
          <div class="detailBlockIcon detailBlockBlue">
            <i class="fa fa-calendar"></i>
          </div>
          <div class="detailBlockContent">
            <h3 class="contentValue"><?= get_date_user($sq_quotation['quotation_date']) ?></h3>
            <span class="contentLabel">QUOTATION DATE</span>
          </div>
        </div>

        <div class="detailBlock text-center">
          <div class="detailBlockIcon detailBlockYellow">
            <i class="fa fa-users"></i>
          </div>
          <div class="detailBlockContent">
            <h3 class="contentValue"><?= $sq_plane['total_adult'] + $sq_plane['total_child'] + $sq_plane['total_infant'] ?></h3>
            <span class="contentLabel">TOTAL SEATS</span>
          </div>
        </div>

        <div class="detailBlock text-center">
          <div class="detailBlockIcon detailBlockRed">
            <i class="fa fa-tag"></i>
          </div>
          <div class="detailBlockContent">
            <h3 class="contentValue"><?= $quotation_cost ?></h3>
            <span class="contentLabel">PRICE</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- traveling Information -->
<section class="travelingDetails main_block">

  <!-- flight -->
  <section class="transportDetails main_block side_pad mg_tp_30">
    <div class="row">
      <div class="col-md-12">
        <div class="table-responsive mg_tp_30">
          <table class="table table-bordered no-marg" id="tbl_emp_list">
            <thead>
              <tr class="table-heading-row">
                <th>From_Sector</th>
                <th>To_Sector</th>
                <th>Airline</th>
                <th>Class</th>
                <th>Departure_D/T</th>
                <th>Arrival_D/T</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sq_plane = mysqlQuery("select * from flight_quotation_plane_entries where quotation_id='$quotation_id'");
              while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
                $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'"));
              ?>
                <tr>
                  <td><?= $row_plane['from_location'] ?></td>
                  <td><?= $row_plane['to_location'] ?></td>
                  <td><?= ($sq_airline['airline_name'] != '') ? $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')' : 'NA' ?></td>
                  <td><?= ($row_plane['class'] != '') ? $row_plane['class'] : 'NA' ?></td>
                  <td><?= get_datetime_user($row_plane['dapart_time']) ?></td>
                  <td><?= get_datetime_user($row_plane['arraval_time']) ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

</section>

<?php if (isset($sq_terms_cond['terms_and_conditions']) || isset($quot_note)) { ?>
  <section class="incluExcluTerms main_block">
    <!-- Terms and Conditions -->
    <?php if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
      <div class="termsCondition main_block side_pad mg_tp_30">
        <h3 class="nrmTitle">Terms and Conditions</h3>
        <pre class="real_text"><?= $sq_terms_cond['terms_and_conditions'] ?></pre>
      </div>
    <?php  }
    if (isset($quot_note)) { ?>
      <div class="termsCondition main_block side_pad mg_tp_30">
        <pre class="real_text"><?= $quot_note ?></pre>
      </div>
    <?php } ?>
  </section>
<?php } ?>

<!-- Ending Page -->
<section class="incluExcluTerms main_block mg_tp_20">

  <!-- Costing & Bank Detail -->
  <div class="costBankSec main_block mg_tp_20 costing_bank_details_bk">
    <div class="costBankInner main_block side_pad mg_tp_20 mg_bt_20">
      <div class="row">
        <!-- Costing -->
        <div class="col-md-6">
          <h3 class="costBankTitle text-center">COSTING DETAILS</h3>
          <div class="row mg_bt_20">
            <?php
            // $fare_cost = currency_conversion($currency, $currency, ((float)($newBasic)));
              $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], (float)($newBasic));

		if ($sq_quotation['currency_code'] != '0' && $currency != $sq_quotation['currency_code'] && (float)($newBasic) != $currency_amount1) {
	 $fare_cost = $currency_amount1 ;
	} else {
	 $fare_cost = (float)($newBasic);
	}
            ?>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/subtotal.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $fare_cost
               ?></h4>
              <p>TOTAL FARE</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/tax.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $tax_show ?></h4>
              <p>TAX</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/quotationCost.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $quotation_cost ?></h4>
              <p>QUOTATION COST</p>
            </div>
          </div>
        </div>
        <!-- Bank Detail -->
        <div class="col-md-6" style="border-left:1px solid #dddddd;">
          <h3 class="costBankTitle text-center">BANK DETAILS</h3>
          <div class="row mg_bt_20">
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/bankName.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?></h4>
              <p>BANK NAME</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/branchName.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?> </h4>
              <p>BRANCH</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accName.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name ?></h4>
              <p>A/C TYPE</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accNumber.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></h4>
              <p>A/C NO</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></h4>
              <p>BANK ACCOUNT NAME</p>
            </div>
            <div class="col-md-4 text-center">
              <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></h4>
              <p>SWIFT CODE</p>
            </div>
            <?php
            if (check_qr()) { ?>
              <div class="col-md-12 text-center" style="margin-top:20px; margin-bottom:20px;">
                <?= get_qr('Landscape Standard') ?>
                <br>
                <h4 class="no-marg">Scan & Pay </h4>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- contact-detail -->
  <section class="contactsec main_block">
    <div class="row">
      <div class="col-md-7">
        <div class="contactTitlePanel text-center">
          <!-- <h3>Contact Us</h3> -->
          <img src="<?= BASE_URL ?>images/quotation/contactImg.jpg" class="img-responsive">
          <?php if ($app_website != '') { ?><p class="no-marg"><?php echo $app_website; ?></p><?php } ?>
        </div>
      </div>
      <div class="col-md-5">
        <?php //if($app_address != ''){
        ?>
        <div class="contactBlock main_block side_pad mg_tp_20">
          <div class="cBlockIcon"> <i class="fa fa-map-marker"></i> </div>
          <div class="cBlockContent">
            <h5 class="cTitle">Corporate Office</h5>
            <p class="cBlockData"><?php echo ($branch_status == 'yes' && $role != 'Admin') ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address; ?></p>
          </div>
        </div>
        <?php //} 
        ?>
        <?php //if($app_contact_no != ''){
        ?>
        <div class="contactBlock main_block side_pad mg_tp_20">
          <div class="cBlockIcon"> <i class="fa fa-phone"></i> </div>
          <div class="cBlockContent">
            <h5 class="cTitle">Contact</h5>
            <p class="cBlockData"><?php echo ($branch_status == 'yes' && $role != 'Admin') ? $branch_details['contact_no']  : $app_contact_no; ?></p>
          </div>
        </div>
        <?php //} 
        ?>
        <?php //if($app_email_id != ''){
        ?>
        <div class="contactBlock main_block side_pad mg_tp_20">
          <div class="cBlockIcon"> <i class="fa fa-envelope"></i> </div>
          <div class="cBlockContent">
            <h5 class="cTitle">Email Id</h5>
            <p class="cBlockData"><?php echo ($branch_status == 'yes' && $role != 'Admin' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></p>
          </div>
        </div>
        <?php //} 
        ?>

      </div>
    </div>
  </section>

</section>

</body>

</html>