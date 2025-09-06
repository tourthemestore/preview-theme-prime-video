<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";

$quotation_id = $_GET['quotation_id'];
global $app_quot_img, $currency, $app_quot_format;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/group_tour/index.php'"));
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

$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Group Quotation' and active_flag ='Active'"));

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from group_tour_quotation_master where quotation_id='$quotation_id'"));
$sq_package_program = mysqlQuery("select * from group_tour_program where tour_id ='$sq_quotation[tour_group_id]'");
$sq_tour = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$sq_quotation[tour_group_id]'"));
$sq_dest = mysqli_fetch_assoc(mysqlQuery("select link from video_itinerary_master where dest_id = '$sq_tour[dest_id]'"));
$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year = $yr[0];

if ($sq_emp_info['first_name'] == '') {
  $emp_name = 'Admin';
} else {
  $emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
}
$tour_cost = $sq_quotation['tour_cost'];
////////////////Currency conversion ////////////
$currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['quotation_cost']);
?>

<style>
  .package_costing table tr:nth-child(even) {
    background-color: #efefef !important;
  }

  .tableTrnasp tr td{
    font-size: 13px !important;
  }
  
  .font-package{
     font-weight: 500;
    border: 2px solid <?= $theme_color ?>;
    text-align: center;
    font-size: 16px;
    margin-bottom: 10px;
    display: inline-block;         
    padding: 8px 15px;
    border-radius: 20px;
    
    
  }
  .itneraryDayPlan p{
    width: 100%;
    font-size: 16px;
    line-height: 25px;
  }
 .border-table td {
    border: 1px solid  #ccc !important;
    padding: 8px;

  }
  .footer-image {
    position: absolute;
    bottom: 10px;
    left: 0;
    width: 100%;
    text-align: center;
  }

  .footer-image img {
    width: 100%;
  }
</style>
<!-- landingPage -->
<section class="landingSec main_block">
  <div class="col-md-8 no-pad">
    <img src="<?= getFormatImg($app_quot_format, $sq_tour['dest_id']) ?>" class="img-responsive">
    <span class="landingPageId"><?= get_quotation_id($quotation_id, $year) ?></span>
  </div>
  <div class="col-md-4 no-pad">
  </div>
  <h1 class="landingpageTitle"><?= $sq_quotation['tour_name'] ?></h1>
  <div class="packageDeatailPanel">
    <div class="landingPageBlocks">

      <div class="detailBlock">
        <div class="detailBlockIcon">
          <i class="fa fa-calendar"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?= get_date_user($sq_quotation['quotation_date']) ?></h3>
          <span class="contentLabel">QUOTATION DATE</span>
        </div>
      </div>

      <div class="detailBlock">
        <div class="detailBlockIcon">
          <i class="fa fa-calendar"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?= get_date_user($sq_quotation['from_date']) . ' To ' . get_date_user($sq_quotation['to_date']) ?></h3>
          <span class="contentLabel">TOUR DATE</span>
        </div>
      </div>

      <div class="detailBlock">
        <div class="detailBlockIcon">
          <i class="fa fa-hourglass-half"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?php echo ($sq_quotation['total_days']) . 'N/' . ($sq_quotation['total_days'] + 1) . 'D' ?></h3>
          <span class="contentLabel">DURATION</span>
        </div>
      </div>

      <div class="detailBlock">
        <div class="detailBlockIcon">
          <i class="fa fa-users"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?= $sq_quotation['total_passangers'] ?></h3>
          <span class="contentLabel">TOTAL GUEST</span>
        </div>
      </div>

      <div class="detailBlock">
        <div class="detailBlockIcon">
          <i class="fa fa-tag"></i>
        </div>
        <div class="detailBlockContent">
          <h3 class="contentValue"><?= $currency_amount1 ?></h3>
          <span class="contentLabel">PRICE</span>
        </div>
      </div>
    </div>
    <div class="landigPageCustomer">
      <h3 class="customerFrom">PREPARED FOR</h3>
      <span class="customerName"><em><i class="fa fa-user"></i></em> : <?= $sq_quotation['customer_name'] ?></span><br>
      <span class="customerMail"><em><i class="fa fa-envelope"></i></em> : <?= $sq_quotation['email_id'] ?></span><br>
      <span class="customerMail"><em><i class="fa fa-phone"></i></em> : <?= $sq_quotation['mobile_number'] ?></span><br>
    </div>
  </div>
</section>



<!-- traveling Information -->
<section class="pageSection main_block">
  <!-- background Image -->
  <img src="<?= BASE_URL ?>images/quotation/p5/pageBGF.jpg" class="img-responsive pageBGImg">

  <section class="travelingDetails main_block mg_tp_30 pageSectionInner">
    <?php
    $checkPageEnd=0;
     if ($checkPageEnd == 0 && $sq_dest['link'] != '') { ?>
          <div class="vitinerary_div" style="margin-bottom:20px!important;">
            <h6>Destination Guide Video</h6>
            <img src="<?php echo BASE_URL . 'images/quotation/youtube-icon.png'; ?>" class="itinerary-img img-responsive"><br />
            <a href="<?= $sq_dest['link'] ?>" class="no-marg" target="_blank"></a>
          </div>
        <?php } ?>
     <?php
    $sq_h_count = mysqli_num_rows(mysqlQuery("select * from group_tour_hotel_entries where tour_id='$sq_quotation[tour_group_id]'"));
    if ($sq_h_count > 0) {  ?>
      <!-- Train -->
      <section class="transportDetailsPanel transportDetailsleft main_block mg_tp_10">
        <div class="travsportInfoBlock">
          <div class="transportIcon">
            <img src="<?= BASE_URL ?>images/quotation/p4/TI_hotel.png" class="img-responsive">
          </div>
          <div class="transportDetails">
            <div class="table-responsive">
              <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th style="font-size: 13px !important;">City Name</th>
                    <th style="font-size: 13px !important;">Hotel Name</th>
                    <th style="font-size: 13px !important;">Hotel Category</th>
                    <th style="font-size: 13px !important;">Total Nights</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $count = 0;
                  $sq_hotel = mysqlQuery("select * from group_tour_hotel_entries where tour_id='$sq_quotation[tour_group_id]'");
                  while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
                  ?>
                    <tr>
                      <td><?php
                          $city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id = " . $row_hotel['city_id']));
                          echo $city['city_name'] ?></td>
                      <td><?php
                          $hotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id = " . $row_hotel['hotel_id']));
                          echo $hotel['hotel_name'] ?></td>
                      <td><?= $row_hotel['hotel_type'] ?></td>
                      <td><?= $row_hotel['total_nights'] ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    <?php } ?>
    <?php
    $sq_plane_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_plane_entries where quotation_id='$quotation_id'"));
    if ($sq_plane_count > 0) {
    ?>
      <!-- Flight -->
      <section class="transportDetailsPanel transportDetailsleft main_block mg_tp_10">
        <div class="travsportInfoBlock">
          <div class="transportIcon">
            <div class="transportIcomImg">
              <img src="<?= BASE_URL ?>images/quotation/p4/TI_flight.png" class="img-responsive">
            </div>
          </div>
          <div class="transportDetails">
            <div class="table-responsive">
              <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th style="font-size: 13px !important;">From_Sector</th>
                    <th style="font-size: 13px !important;">To_Sector</th>
                    <th style="font-size: 13px !important;">Airline</th>
                    <th style="font-size: 13px !important;">Departure_D/T</th>
                    <th style="font-size: 13px !important;">Arrival_D/T</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sq_plane = mysqlQuery("select * from group_tour_quotation_plane_entries where quotation_id='$quotation_id'");
                  while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
                    $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'"));
                  ?>
                    <tr>
                      <td><?= $row_plane['from_location'] ?></td>
                      <td><?= $row_plane['to_location'] ?></td>
                      <td><?= $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')' ?></td>
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
    <?php } ?>
    <?php
    $sq_train_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_train_entries where quotation_id='$quotation_id'"));
    if ($sq_train_count > 0) { ?>
      <!-- Train -->
      <section class="transportDetailsPanel transportDetailsleft main_block mg_tp_10">
        <div class="travsportInfoBlock">
          <div class="transportIcon">
            <img src="<?= BASE_URL ?>images/quotation/p4/TI_train.png" class="img-responsive">
          </div>
          <div class="transportDetails">
            <div class="table-responsive">
              <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th style="font-size: 13px !important;">From_Location</th>
                    <th style="font-size: 13px !important;">To_Location</th>
                    <th style="font-size: 13px !important;">Class</th>
                    <th style="font-size: 13px !important;">Departure_D/T</th>
                    <th style="font-size: 13px !important;">Arrival_D/T</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sq_train = mysqlQuery("select * from group_tour_quotation_train_entries where quotation_id='$quotation_id'");
                  while ($row_train = mysqli_fetch_assoc($sq_train)) {
                  ?>
                    <tr>
                      <td><?= $row_train['from_location'] ?></td>
                      <td><?= $row_train['to_location'] ?></td>
                      <td><?= $row_train['class'] ?></td>
                      <td><?= get_datetime_user($row_train['departure_date']) ?></td>
                      <td><?= get_datetime_user($row_train['arrival_date']) ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    <?php } ?>
   
    <?php
    $sq_cr_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_cruise_entries where quotation_id='$quotation_id'"));
    if ($sq_cr_count > 0) { ?>
      <!-- Cruise -->
      <section class="transportDetailsPanel transportDetailsleft main_block mg_tp_10">
        <div class="travsportInfoBlock">
          <div class="transportIcon">
            <img src="<?= BASE_URL ?>images/quotation/p4/TI_cruise.png" class="img-responsive">
          </div>

          <div class="transportDetails">
            <div class="table-responsive">
              <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th style="font-size: 13px !important;">Departure_D/T</th>
                    <th style="font-size: 13px !important;">Arrival_D/T</th>
                    <th style="font-size: 13px !important;">Route</th>
                    <th style="font-size: 13px !important;">Cabin</th>
                    <th style="font-size: 13px !important;">Sharing</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sq_cruise = mysqlQuery("select * from group_tour_quotation_cruise_entries where quotation_id='$quotation_id'");
                  while ($row_cruise = mysqli_fetch_assoc($sq_cruise)) {
                  ?>
                    <tr>
                      <td><?= get_datetime_user($row_cruise['dept_datetime']) ?></td>
                      <td><?= get_datetime_user($row_cruise['arrival_datetime']) ?></td>
                      <td><?= $row_cruise['route'] ?></td>
                      <td><?= $row_cruise['cabin'] ?></td>
                      <td><?= $row_cruise['sharing'] ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    <?php } ?>
  </section>
</section>



<!-- Itinerary -->
<?php
$count = 1;
$i = 0;
$dates = (array) get_dates_for_tour_itineary($quotation_id);
$checkPageEnd = 0;
while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {
  
  $date_format = isset($dates[$i]) ? $dates[$i] : 'NA';
  if ($checkPageEnd % 100 == 0 || $checkPageEnd == 0) {
    $go = $checkPageEnd + 1;
    $flag = 0;
?>
    <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p5/pageBGF.jpg" class="img-responsive pageBGImg">

      <section class="itinerarySec pageSectionInner main_block mg_tp_30">
        <?php if ($checkPageEnd == 0 && $sq_dest['link'] != '') { ?>
          <!-- <div class="vitinerary_div" style="margin-bottom:20px!important;">
            <h6>Destination Guide Video</h6>
            <img src="<?php echo BASE_URL . 'images/quotation/youtube-icon.png'; ?>" class="itinerary-img img-responsive"><br />
            <a href="<?= $sq_dest['link'] ?>" class="no-marg" target="_blank"></a>
          </div> -->
          <div style="text-align: center;">
            <div class="font-package" style="margin-bottom: 20px;">
                Day Wise Itinerary
              </div>
          </div>
        <?php } ?>
      <?php
    }
    $itinerarySide = "leftItinerary";
      ?>
      <section class="print_single_itinenary <?= $itinerarySide ?>">
        <div class="itneraryImg">
          <div class="itneraryImgblock">
            <?php
            if ($row_itinarary['daywise_images'] != "") {
              $img = $row_itinarary['daywise_images'];
              $pos = strstr($img, 'uploads');
              if ($pos != false) {
                $newUrl1 = preg_replace('/(\/+)/', '/', $img);
                $img = BASE_URL . str_replace('../', '', $newUrl1);
              }
            } else
              $img = "http://itourscloud.com/destination_gallery/asia/singapore/Asia_Singapore_Four.jpg";
            ?>
            <img src="<?= $img ?>" class="img-responsive">
          </div>
          <!-- <div class="itneraryDayAccomodation">
            <span><i class="fa fa-bed"></i> : <?= $row_itinarary['stay'] ?></span>
            <span><i class="fa fa-cutlery"></i> : <?= $row_itinarary['meal_plan'] ?></span>
          </div> -->
        </div>
        <div class="itneraryText">
          <div class="itneraryDayInfo">
            <i class="fa fa-map-marker" aria-hidden="true"></i><span> Day <?= $count ?> <b>(<?php echo $date_format ?>) </b>: <?= $row_itinarary['attraction'] ?> </span>
          </div>
          <div style="display: flex; gap: 30px;" class="itneraryDayAccomodation">
          <span><i class="fa fa-bed"></i> : <?= $row_itinarary['stay'] ?></span>
          <span><i class="fa fa-cutlery"></i> : <?= $row_itinarary['meal_plan'] ?></span>
          </div>
          <div class="itneraryDayPlan">
            <p><?= $row_itinarary['day_wise_program'] ?></p>
          </div>
        </div>
      </section>

      <?php
      if ($go == $checkPageEnd) {
        $flag = 1;
      ?>

      </section>
    </section>
  <?php
      }
      $count++; $i++;
      $checkPageEnd++;
    }
    if ($flag == 0) {
  ?>
  </section>
  </section>
<?php  } ?>

<!-- Inclusion -->
<?php if (($sq_quotation['incl'] != '' && $sq_quotation['incl'] != ' ') || ($sq_quotation['excl'] != '' && $sq_quotation['excl'] != ' ')) { ?>
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p5/pageBGF.jpg" class="img-responsive pageBGImg">

    <section class="incluExcluTerms pageSectionInner main_block mg_tp_30">

      <!-- Inclusion -->
      <?php if (isset($sq_quotation['incl'])) { ?>
        <div class="row">
          <div class="col-md-12 mg_tp_30 mg_bt_30">
            <div class="incluExcluTermsTabPanel inclusions main_block">
              <h3 class="incexTitle">INCLUSIONS</h3>
              <div class="tabContent">
                <div class="real_text"><?= $sq_quotation['incl'] ?></div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
      <!--  Exclusion -->
      <?php if (isset($sq_quotation['excl'])) { ?>
        <div class="row">
          <div class="col-md-12 mg_tp_30 mg_bt_30">
            <div class="incluExcluTermsTabPanel exclusions main_block">
              <h3 class="incexTitle">EXCLUSIONS</h3>
              <div class="tabContent">
                <div class="real_text"><?= $sq_quotation['excl'] ?></div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </section>
  </section>
<?php } ?>


<!-- Terms and Conditions -->
<?php if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p5/pageBGF.jpg" class="img-responsive pageBGImg">

    <section class="incluExcluTerms pageSectionInner main_block mg_tp_30">

      <!-- Terms and Conditions -->
      <div class="row">

        <div class="col-md-12">
          <div class="incluExcluTermsTabPanel exclusions main_block">
            <h3 class="incexTitle">TERMS AND CONDITIONS</h3>
            <div class="tabContent">
              <pre class="real_text"><?php echo $sq_terms_cond['terms_and_conditions']; ?></pre>
            </div>
          </div>
        </div>
      </div>
    </section>
  </section>
<?php } ?>



<!-- Costing & Banking Page -->
<section class="endPageSection main_block mg_tp_30">

  <div class="row">

    <!-- Guest Detail -->
    <div class="col-md-12 passengerPanel endPagecenter mg_bt_30">
      <h3 class="endingPageTitle text-center">TOTAL GUEST</h3>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="iconPassengerBlock">
          <div class="iconPassengerSide leftSide"></div>
          <div class="iconPassenger">
            <img src="<?= BASE_URL ?>images/quotation/p4/adult.png" class="img-responsive">
            <h4 class="no-marg">Adult : <?= $sq_quotation['total_adult']+$sq_quotation['single_person'] ?></h4>
          </div>
          <div class="iconPassengerSide rightSide"></div>
        </div>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="iconPassengerBlock">
          <div class="iconPassengerSide leftSide"></div>
          <div class="iconPassenger">
            <img src="<?= BASE_URL ?>images/quotation/p4/child.png" class="img-responsive">
            <h4 class="no-marg">Children : <?= $sq_quotation['total_children'] ?></h4>
          </div>
          <div class="iconPassengerSide rightSide"></div>
          <i class="fa fa-plus"></i>
        </div>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="iconPassengerBlock">
          <div class="iconPassengerSide leftSide"></div>
          <div class="iconPassenger">
            <img src="<?= BASE_URL ?>images/quotation/p4/infant.png" class="img-responsive">
            <h4 class="no-marg">Infant : <?= $sq_quotation['total_infant'] ?></h4>
          </div>
          <div class="iconPassengerSide rightSide"></div>
          <i class="fa fa-plus"></i>
        </div>
      </div>
    </div>

  </div>
  <?php
  $tour_cost1 = $sq_quotation['tour_cost'];
  $service_charge = $sq_quotation['service_charge'];
  $tour_cost = $tour_cost1 + $service_charge;
  $service_tax_amount = 0;
  $tax_show = '';$name = '';
  // $bsmValues = json_decode($sq_quotation['bsm_values']);

// tcs

$bsmValues = json_decode($sq_quotation['bsm_values'],true);

// tcs
        if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
          $tcsper = $bsmValues[0]['tcsper'];
          $tcsvalue = $bsmValues[0]['tcsvalue'];
      } else {
          $tcsper = 0;
          $tcsvalue = 0;
      }
      
      $tcs_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);



  if ($sq_quotation['service_tax_subtotal'] !== 0.00 && ($sq_quotation['service_tax_subtotal']) !== '') {
    $service_tax_subtotal1 = explode(',', $sq_quotation['service_tax_subtotal']);
    for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
      $service_tax = explode(':', $service_tax_subtotal1[$i]);
      $service_tax_amount +=  $service_tax[2];
      $name .= $service_tax[0]  . $service_tax[1] . ', ';
    }
  }
  $service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);
  if ($bsmValues[0]->service != '') {   //inclusive service charge
    $newBasic = $tour_cost + $service_tax_amount;
    $tax_show = '';
  } else {
    // $tax_show = $service_tax_amount;
    $tax_show =  rtrim($name, ', ') . ' : ' . ($service_tax_amount);
    $newBasic = $tour_cost;
  }

  ////////////Basic Amount Rules
  if ($bsmValues[0]->basic != '') { //inclusive markup
    $newBasic = $tour_cost + $service_tax_amount;
    $tax_show = '';
  }
  $newBasic1 = currency_conversion($currency, $sq_quotation['currency_code'], $newBasic);
  ?>
  <h3 class="endingPageTitle text-center no-pad">COSTING DETAILS</h3>
  <div class="travsportInfoBlock1">
  <div class="transportDetails_costing package_costing">
    <div class="table-responsive">
      <table class="table no-marg tableTrnasp border-table">
        <thead>
          <tr class="table-heading-row">
            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px 15px !important;">Tour Cost</th>
            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px 20px !important;">Tax</th>
            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px 30px !important;">TCS</th>
            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px 20px !important;">Quotation Cost</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="font-size: 14px !important; padding: 8px 15px !important;">
              <?= $newBasic1 ?>
            </td>
            <td style="font-size: 14px !important; padding: 8px 20px !important;">
              <?= str_replace(',', '', $name) . $service_tax_amount_show ?>
            </td>
            <td style="font-size: 14px !important; padding: 8px 30px !important;">
              TCS<?php if ($tcsper >= 1) {
                echo " (" . $tcsper . "%)";
              } ?>: <?= $tcs_amount_show ?>
            </td>
            <td style="font-size: 14px !important; padding: 8px 20px !important;">
              <?= $currency_amount1 ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>



  <!-- Bank Detail -->
  <div class="row constingBankingPanelRow" style="margin-top: 40px;">
    <!-- Bank Detail -->
    <div class="col-md-12 constingBankingPanel BankingPanel" >
      <h3 class="costBankTitle text-center">BANK DETAILS</h3>
      <div class="col-md-4 text-center no-pad constingBankingwhite">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p5/bankName.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?></h4>
        <p>BANK NAME</p>
      </div>
      <div class="col-md-4 text-center no-pad">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/branchName.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?></h4>
        <p>BRANCH</p>
      </div>
      <div class="col-md-4 text-center no-pad constingBankingwhite">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p5/accName.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name  ?></h4>
        <p>A/C TYPE</p>
      </div>
      <div class="col-md-4 text-center no-pad">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accNumber.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></h4>
        <p>A/C NO</p>
      </div>
      <div class="col-md-4 text-center no-pad constingBankingwhite">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p5/code.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></h4>
        <p>BANK ACCOUNT NAME</p>
      </div>
      <div class="col-md-4 text-center no-pad">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></h4>
        <p>SWIFT CODE</p>
      </div>
      <?php
      if (check_qr()) { ?>
        <div class="col-md-12 text-center" style="margin-top:20px; margin-bottom:20px;">
          <?= get_qr('Protrait Advance') ?>
          <br>
          <h4 class="no-marg">Scan & Pay </h4>
        </div>
      <?php } ?>
    </div>
  </div>

</section>

<!--Contact Page -->
<section class="pageSection main_block">
  <!-- background Image -->
  <img src="<?= BASE_URL ?>images/quotation/p5/pageBGF.jpg" class="img-responsive pageBGImg">

  <section class="contactSection main_block mg_tp_30 text-center pageSectionInner">
    <div class="companyLogo">
      <img src="<?= $admin_logo_url ?>">
    </div>
    <div class="companyContactDetail">
      <h3><?= $app_name ?></h3>
      <?php //if($app_address != ''){
      ?>
      <div class="contactBlock">
        <i class="fa fa-map-marker"></i>
        <p><?php echo ($branch_status == 'yes' && $role != 'Admin') ? $branch_details['address1'] . ',' . $branch_details['address2'] . ',' . $branch_details['city'] : $app_address; ?></p>
      </div>
      <?php //}
      ?>
      <?php //if($app_contact_no != ''){
      ?>
      <div class="contactBlock">
        <i class="fa fa-phone"></i>
        <p><?php echo ($branch_status == 'yes' && $role != 'Admin') ? $branch_details['contact_no']  : $app_contact_no; ?></p>
      </div>
      <?php //}
      ?>
      <?php //if($app_email_id != ''){
      ?>
      <div class="contactBlock">
        <i class="fa fa-envelope"></i>
        <p><?php echo ($branch_status == 'yes' && $role != 'Admin' && $branch_details['email_id'] != '') ? $branch_details['email_id'] : $app_email_id; ?></p>
      </div>
      <?php //}
      ?>
      <?php if ($app_website != '') { ?>
        <div class="contactBlock">
          <i class="fa fa-globe"></i>
          <p><?php echo $app_website; ?></p>
        </div>
      <?php } ?>
      <div class="contactBlock">
        <i class="fa fa-pencil-square-o"></i>
        <p>PREPARED BY : <?= $emp_name ?></p>
      </div>
    </div>
  </section>
  <div class="footer-image">
      <img src="../../../../../images/quotation/botton-pdf-img.png" alt="Bottom Image" />
    </div>
</section>

</body>

</html>