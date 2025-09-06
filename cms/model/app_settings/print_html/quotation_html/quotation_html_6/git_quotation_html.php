<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";
global $app_quot_img, $currency,$app_quot_format;

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

$quotation_id = $_GET['quotation_id'];

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

<!-- landingPage -->
<section class="landingSec main_block">

  <div class="landingPageTop main_block">

    <img src="<?= getFormatImg($app_quot_format, $sq_tour['dest_id']) ?>" class="img-responsive">
    <span class="landingPageId"><?= get_quotation_id($quotation_id, $year) ?></span>
    <h1 class="landingpageTitle"><?= $sq_quotation['tour_name'] ?></h1>


    <div class="packageDeatailPanel">
      <div class="landigPageCustomer">
        <h3 class="customerFrom">PREPARED FOR :</h3>
        <span class="customerName"><em><i class="fa fa-user"></i></em> : <?= $sq_quotation['customer_name'] ?></span><br>
        <span class="customerMail"><em><i class="fa fa-envelope"></i></em> : <?= $sq_quotation['email_id'] ?></span><br>
        <span class="customerMail"><em><i class="fa fa-phone"></i></em> : <?= $sq_quotation['mobile_number'] ?></span><br>
      </div>

      <div class="landingPageBlocks">

        <div class="detailBlock">
          <div class="detailBlockIcon">
            <i class="fa fa-calendar"></i>
          </div>
          <div class="detailBlockContent">
            <p>QUOTATION DATE : <?= get_date_user($sq_quotation['quotation_date']) ?></p>
          </div>
        </div>
        <div class="detailBlock">
          <div class="detailBlockIcon">
            <i class="fa fa-calendar"></i>
          </div>
          <div class="detailBlockContent">
            <p>TOUR DATE : <?= get_date_user($sq_quotation['from_date']) . ' To ' . get_date_user($sq_quotation['to_date']) ?></p>
          </div>
        </div>

        <div class="detailBlock">
          <div class="detailBlockIcon">
            <i class="fa fa-hourglass-half"></i>
          </div>
          <div class="detailBlockContent">
            <p>DURATION : <?php echo ($sq_quotation['total_days']) . 'N/' . ($sq_quotation['total_days'] + 1) . 'D' ?></p>
          </div>
        </div>

        <div class="detailBlock">
          <div class="detailBlockIcon">
            <i class="fa fa-users"></i>
          </div>
          <div class="detailBlockContent">
            <p>TOTAL GUEST : <?= $sq_quotation['total_passangers'] ?></p>
          </div>
        </div>

        <div class="detailBlock">
          <div class="detailBlockIcon">
            <i class="fa fa-tag"></i>
          </div>
          <div class="detailBlockContent">
            <p>PRICE : <?= $currency_amount1 ?></p>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
<!-- Itinerary -->
<?php
$count = 1;
$i = 0;
$dates = (array) get_dates_for_tour_itineary($quotation_id); 
$checkPageEnd = 0;
while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {
  
  $date_format = isset($dates[$i]) ? $dates[$i] : 'NA';
  if ($checkPageEnd % 1 == 0 || $checkPageEnd == 0) {
    $go = $checkPageEnd + 0;
    $flag = 0;
?>
    <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">

      <section class="itinerarySec main_block side_pad mg_tp_30 pageSectionInner">

        <ul class="print_itinenary no-pad no-marg">
          <?php if ($count == 1) { ?>
            <div class="mg-bt-30">
              <div class="vitinerary_div">
                <h6>Destination Guide Video</h6>
                <img src="<?php echo BASE_URL . 'images/quotation/youtube-icon.png'; ?>" class="itinerary-img img-responsive"><br />
                <a href="<?= $sq_dest['link'] ?>" class="no-marg" target="_blank"></a>
              </div>
            </div>
          <?php } ?>
        <?php
      }
        ?>
        <li class="print_single_itinenary topItinerary">
          <div class="itineraryContent">
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
            <div class="itneraryImg">
              <img src="<?= $img ?>" class="img-responsive">
            </div>
            <div class="itneraryText">
              <div class="itneraryDayInfo">
                <i class="fa fa-map-marker" aria-hidden="true"></i><span> Day <?= $count ?> <b>(<?php echo $date_format ?>) </b>: <?= $row_itinarary['attraction'] ?> </span>
              </div>
              <div class="itneraryDayPlan">
                <p><?= $row_itinarary['day_wise_program'] ?></p>
              </div>
              <div class="itneraryDayAccomodation">
                <span><i class="fa fa-bed"></i> : <?= $row_itinarary['stay'] ?></span>
                <span><i class="fa fa-cutlery"></i> : <?= $row_itinarary['meal_plan'] ?></span>
              </div>
            </div>
          </div>
        </li>

        <?php
        if ($go == $checkPageEnd) {
          $flag = 1;
        ?>
        </ul>
      </section>
    </section>
  <?php
        }
        $count++; $i++;
        $checkPageEnd++;
      }
      if ($flag == 0) {
  ?>
  </ul>
  </section>
  </section>
<?php } ?>

<!-- traveling Information -->
<section class="pageSection main_block">
  <!-- background Image -->
  <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
  <section class="travelingDetails main_block mg_tp_30 pageSectionInner">
    <?php
    $sq_plane_c = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_plane_entries where quotation_id='$quotation_id'"));
    if ($sq_plane_c != 0) { ?>
      <!-- Flight -->
      <section class="transportDetailsPanel transportDetailsLeftPanel main_block side_pad">
        <div class="travsportInfoBlock">
          <div class="transportIcon">
            <img src="<?= BASE_URL ?>images/quotation/p4/TI_flight.png" class="img-responsive">
          </div>
          <div class="transportDetails">
            <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
              <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>From_SECTOR</th>
                    <th>To_SECTOR</th>
                    <th>Airline</th>
                    <th>Departure_D/T</th>
                    <th>Arrival_d/T</th>
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
    if($sq_train_count>0){ ?>
    <!-- Train -->
    <section class="transportDetailsPanel transportDetailsLeftPanel main_block side_pad">
      <div class="travsportInfoBlock">
        <div class="transportIcon">
          <img src="<?= BASE_URL ?>images/quotation/p4/TI_train.png" class="img-responsive">
        </div>
        <div class="transportDetails">
          <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
            <table class="table tableTrnasp no-marg" id="tbl_emp_list">
              <thead>
                <tr class="table-heading-row">
                  <th>From_Location</th>
                  <th>To_Location</th>
                  <th>Class</th>
                  <th>Departure_D/t</th>
                  <th>Arrival_D/T</th>
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
      $sq_h_count = mysqli_num_rows(mysqlQuery("select * from group_tour_hotel_entries where tour_id='$sq_quotation[tour_group_id]'"));
      if($sq_h_count>0){ ?>
      <!-- hotel -->
      <section class="transportDetailsPanel transportDetailsLeftPanel main_block side_pad">
        <div class="travsportInfoBlock">
          <div class="transportIcon">
            <img src="<?= BASE_URL ?>images/quotation/p4/TI_hotel.png" class="img-responsive">
          </div>
          <div class="transportDetails">
            <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
              <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>City Name</th>
                    <th>Hotel Name</th>
                    <th>Hotel Category</th>
                    <th>Total Nights</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $count = 0;
                  $sq_hotel = mysqlQuery("select * from group_tour_hotel_entries where tour_id='$sq_quotation[tour_group_id]'");
                  while($row_hotel = mysqli_fetch_assoc($sq_hotel))
                  {
                    ?>
                    <tr>
                      <td><?php
                      $city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id = ".$row_hotel['city_id']));
                      echo $city['city_name'] ?></td>
                      <td><?php
                      $hotel = mysqli_fetch_assoc(mysqlQuery("select hotel_name from hotel_master where hotel_id = ".$row_hotel['hotel_id']));
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
    $sq_cr_count = mysqli_num_rows(mysqlQuery("select * from group_tour_quotation_cruise_entries where quotation_id='$quotation_id'"));
    if($sq_cr_count>0){ ?>
    <!-- Cruise -->
    <section class="transportDetailsPanel transportDetailsLeftPanel transportDetailsLastPanel main_block side_pad">
      <div class="travsportInfoBlock">
        <div class="transportIcon">
          <img src="<?= BASE_URL ?>images/quotation/p4/TI_cruise.png" class="img-responsive">
        </div>

        <div class="transportDetails">
          <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
            <table class="table tableTrnasp no-marg" id="tbl_emp_list">
              <thead>
                <tr class="table-heading-row">
                  <th>Departure_D/t</th>
                  <th>Arrival_D/T</th>
                  <th>Route</th>
                  <th>Cabin</th>
                  <th>Sharing</th>
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
    <?php }
    ?>
  </section>


  <!-- Inclusion -->
  <?php if ($sq_quotation['incl'] != '' || $sq_quotation['excl'] != '') { ?>
    <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
      <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

        <!-- Inclusion Exclusion -->
        <div class="row side_pad">
          <div class="col-md-10 mg_tp_30">
            <div class="incluExcluTermsTabPanel inclusions main_block">
              <h3 class="incexTitle">INCLUSIONS</h3>
              <div class="tabContent">
                <pre class="real_text"><?= $sq_quotation['incl'] ?></pre>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>
  <?php } ?>


  <!-- Exclusion -->
  <?php if ($sq_quotation['excl'] != '') { ?>
    <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
      <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

        <!-- Exclusion -->
        <div class="row side_pad">
          <div class="col-md-10 mg_tp_30">
            <div class="incluExcluTermsTabPanel exclusions main_block">
              <h3 class="incexTitle">EXCLUSIONS</h3>
              <div class="tabContent">
                <pre class="real_text"><?= $sq_quotation['excl'] ?></pre>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>
  <?php } ?>

  <!-- Terms and Conditions -->
  <?php if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
    <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
      <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

        <!-- Terms and Conditions -->
        <div class="row side_pad">
          <div class="col-md-10 mg_tp_30">
            <div class="termsConditions main_block">
              <h3 class="termsConditionsTitle">TERMS AND CONDITIONS</h3>
              <div class="tncContent">
                <pre class="real_text"><?php echo $sq_terms_cond['terms_and_conditions']; ?></pre>
              </div>
            </div>
          </div>
        </div>

      </section>
    </section>
  <?php } ?>



  <!-- Costing & Banking Page -->
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="endPageSection main_block mg_tp_30 pageSectionInner">

      <div class="row">

        <!-- Guest Detail -->
        <div class="col-md-4 passengerPanel endPagecenter mg_bt_30">
          <h3 class="endingPageTitle text-center">TOTAL GUEST</h3>
          <div class="icon">
            <img src="<?= BASE_URL ?>images/quotation/p4/adult.png" class="img-responsive">
            <h4 class="no-marg">Adult : <?= $sq_quotation['total_adult']+$sq_quotation['single_person'] ?></h4>
            <i class="fa fa-plus"></i>
          </div>
          <div class="icon">
            <img src="<?= BASE_URL ?>images/quotation/p4/child.png" class="img-responsive">
            <h4 class="no-marg">Child/Infant : <?= (int) $sq_quotation['total_children'] + (int) $sq_quotation['total_infant'] ?></h4>
            <i class="fa fa-plus"></i>
          </div>
          <?php
          if (check_qr()) { ?>
            <div class="icon">
              <!-- <img src="<?= BASE_URL ?>images/quotation/p4/infant.png" class="img-responsive"> -->
              <?= get_qr('Landscape Advanced') ?>
              <h4 class="no-marg">Scan & Pay</h4>
            </div>
          <?php } ?>
        </div>
        <?php
        $tour_cost1 = $sq_quotation['tour_cost'];
        $service_charge = $sq_quotation['service_charge'];
        $tour_cost = $tour_cost1 + $service_charge;
        $service_tax_amount = 0;
        $tax_show = '';
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



        $name = '';
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
        <div class="col-md-8">
          <!-- Costing -->
          <div class="col-md-12 constingBankingPanel constingPanel">
            <h3 class="costBankTitle text-center">COSTING DETAILS</h3>
            <div class="col-md-4 text-center mg_bt_30">
              <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/tourCost.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $newBasic1 ?></h4>
              <p>TOUR COST</p>
            </div>
            <div class="col-md-4 text-center mg_bt_30">
              <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/tax.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= str_replace(',', '', $name) . $service_tax_amount_show ?></h4>
              <p>TAX</p>
            </div>

            <div class="col-md-4 text-center mg_bt_30">
              <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/tax.png" class="img-responsive"></div>
              <h4 class="no-marg"><span style="color:white !important;">Tcs<?php if($tcsper>=1){ echo "(".$tcsper."%)"; } ?>:
                </span><?=  $tcs_amount_show?></h4>
              <p>TCS</p>
            </div>

            <div class="col-md-4 text-center mg_bt_30">
              <div class="icon main_block"><img src="<?= BASE_URL ?>images/quotation/p4/quotationCost.png" class="img-responsive"></div>
              <h4 class="no-marg"><?= $currency_amount1 ?></h4>
              <p>QUOTATION COST</p>
            </div>
          </div>



          <!-- Bank Detail -->
          <div class="col-md-12 constingBankingPanel BankingPanel">
            <h3 class="costBankTitle text-center">BANK DETAILS</h3>
              <div class="col-md-4 text-center mg_bt_30">
                <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/bankName.png" class="img-responsive"></div>
                <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting  ?></h4>
                <p>BANK NAME</p>
              </div>
              <div class="col-md-4 text-center mg_bt_30">
                <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/branchName.png" class="img-responsive"> </div>
                <h4 class="no-marg"><?=  ($sq_bank_count>0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?></h4>
                <p>BRANCH</p>
              </div>
              <div class="col-md-4 text-center mg_bt_30">
                <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accName.png" class="img-responsive"></div>
                <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name  ?></h4>
                <p>A/C TYPE</p>
              </div>
              <div class="col-md-4 text-center mg_bt_30">
                <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accNumber.png" class="img-responsive"></div>
                <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></h4>
                <p>A/C NO</p>
              </div>
              <div class="col-md-4 text-center mg_bt_30">
                <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
                <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></h4>
                <p>BANK ACCOUNT NAME</p>
              </div>
              <div class="col-md-4 text-center mg_bt_30">
                <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
                <h4 class="no-marg"><?= ($sq_bank_count>0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></h4>
                <p>SWIFT CODE</p>
              </div>
          </div>

        </div>

      </div>

    </section>
  </section>

  <!-- Costing & Banking Page -->
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBG.jpg" class="img-responsive pageBGImg">
    <section class="contactSection main_block mg_tp_30 pageSectionInner">
      <div class="contactPanel">
        <div class="companyLogo">
          <img src="<?= $admin_logo_url ?>">
        </div>
        <div class="companyContactDetail">
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
      </div>
    </section>
  </section>

  </body>

  </html>