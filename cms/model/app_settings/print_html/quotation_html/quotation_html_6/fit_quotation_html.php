<?php
//Generic Files
include "../../../../model.php";
include "printFunction.php";
global $app_quot_img, $similar_text, $quot_note, $currency, $tcs_note, $app_quot_format;

$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select * from branch_assign where link='package_booking/quotation/home/index.php'"));
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

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));
$tcs_note_show = ($sq_quotation['booking_type'] != 'Domestic') ? $tcs_note : '';
$sq_package_name = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));

$sq_terms_cond_count = mysqli_num_rows(mysqlQuery("select dest_id from terms_and_conditions where type='Package Quotation' and dest_id='$sq_package_name[dest_id]' and active_flag ='Active'"));
$dest_id = ($sq_terms_cond_count != 0) ? $sq_package_name['dest_id'] : 0;
$sq_terms_cond = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='Package Quotation' and dest_id='$dest_id' and active_flag ='Active'"));

$sq_dest = mysqli_fetch_assoc(mysqlQuery("select link from video_itinerary_master where dest_id = '$sq_package_name[dest_id]'"));
$sq_transport = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));
$sq_costing = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order"));
$sq_package_program = mysqlQuery("select * from  package_quotation_program where quotation_id='$quotation_id'");

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

$basic_cost = $sq_costing['basic_amount'];
$service_charge = $sq_costing['service_charge'];
$tour_cost = $basic_cost + $service_charge;
$service_tax_amount = 0;
$tax_show = '';
$bsmValues = json_decode($sq_costing['bsmValues']);
?>
<style>
  .package_costing table tr:nth-child(even) {
    background-color: #51514f !important;
  }
</style>

<!-- landingPage -->
<section class="landingSec main_block">

  <div class="landingPageTop main_block">

    <img src="<?= getFormatImg($app_quot_format, $sq_package_name['dest_id']) ?>" class="img-responsive">
    <span class="landingPageId"><?= get_quotation_id($quotation_id, $year) ?></span>
    <h1 class="landingpageTitle"><?= $sq_package_name['package_name'] ?><?= ' (' . $sq_package_name['package_code'] . ')' ?></h1>


    <div class="packageDeatailPanel">
      <div class="landigPageCustomer">
        <h3 class="customerFrom">PREPARED FOR :</h3>
        <span class="customerName"><em><i class="fa fa-user"></i></em> : <?= $sq_quotation['customer_name'] ?></span><br>
        <span class="customerMail"><em><i class="fa fa-envelope"></i></em> : <?= $sq_quotation['email_id'] ?></span><br>
        <span class="customerMobile"><em><i class="fa fa-phone"></i></em> : <?= $sq_quotation['mobile_no'] ?></span>
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
            <p>TRAVEL DATE : <?= get_date_user($sq_quotation['from_date']) . ' To ' . get_date_user($sq_quotation['to_date']) ?></p>
          </div>
        </div>

        <div class="detailBlock">
          <div class="detailBlockIcon">
            <i class="fa fa-hourglass-half"></i>
          </div>
          <div class="detailBlockContent">
            <p>DURATION : <?php echo $sq_quotation['total_days'] . 'N/' . ($sq_quotation['total_days'] + 1) . 'D' ?></p>
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
      </div>
    </div>

  </div>
</section>

<!-- Count queries -->
<?php
$sq_package_count = mysqli_num_rows(mysqlQuery("select * from  package_quotation_program where quotation_id='$quotation_id'"));
$sq_hotel_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'"));
$sq_transport_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));
$sq_train_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'"));
$sq_plane_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'"));
$sq_cruise_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'"));
$sq_exc_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'"));

$overall_count = 0;
if ($sq_train_count > 0) $overall_count++;
if ($sq_plane_count > 0) $overall_count++;
if ($sq_cruise_count > 0) $overall_count++;
if ($sq_hotel_count > 0) $overall_count++;
if ($sq_transport_count > 0) $overall_count++;
if ($sq_exc_count > 0) $overall_count++;
?>

<!-- traveling Information -->
<?php if ($sq_hotel_count != 0 || $sq_plane_count != 0 || $sq_transport_count != 0) {
?>
  <!-- traveling Information -->
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="travelingDetails main_block mg_tp_30 pageSectionInner">
      <!-- hotel -->
      <?php
      $package_type_count = 1;
      if ($sq_hotel_count != 0) {
        $sq_package_type = mysqlQuery("select DISTINCT(package_type) from package_tour_quotation_hotel_entries where quotation_id='$quotation_id' order by '$sq_costing[sort_order]'");
        while ($row_hotel1 = mysqli_fetch_assoc($sq_package_type)) {

          $sq_package_type1 = mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id' and package_type='$row_hotel1[package_type]' order by package_type");
          if ($package_type_count == '4' || $package_type_count == '7' || $package_type_count == '10') {

      ?>
            <section class="pageSection main_block">
              <!-- background Image -->
              <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
              <section class="travelingDetails main_block mg_tp_30 pageSectionInner">
              <?php } ?>
              <section class="transportDetailsPanel transportDetailsLeftPanel main_block side_pad">
                <h6 class="text-center">PACKAGE TYPE - <?= $row_hotel1['package_type'] ?></h6>
                <div class="travsportInfoBlock">
                  <div class="transportIcon">
                    <img src="<?= BASE_URL ?>images/quotation/p4/TI_hotel.png" class="img-responsive">
                  </div>
                  <div class="transportDetails">
                    <div class="col-md-12 no-pad">
                      <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
                        <table class="table tableTrnasp no-marg" id="tbl_emp_list">
                          <thead>
                            <tr class="table-heading-row">
                              <th>City</th>
                              <th>Hotel Name</th>
                              <th>Check_IN</th>
                              <th>Check_OUT</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            while ($row_hotel = mysqli_fetch_assoc($sq_package_type1)) {

                              $hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_hotel[hotel_name]'"));
                              $city_name = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotel[city_name]'"));
                            ?>
                              <tr>
                                <td><?php echo $city_name['city_name']; ?></td>
                                <td><?php echo $hotel_name['hotel_name'] . $similar_text; ?></td>
                                <td><?= get_date_user($row_hotel['check_in']) ?></td>
                                <td><?= get_date_user($row_hotel['check_out']) ?></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
              <?php
              if ($package_type_count == '3' || $package_type_count == '6' || $package_type_count == '9') { ?>
              </section>
            </section>
      <?php  }
              $package_type_count++;
            }
          }
      ?>
      <!-- Flight -->
      <?php
      if ($sq_plane_count > 0) { ?>
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
                    $sq_plane = mysqlQuery("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'");
                    while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
                      $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'"));
                      $airline = ($row_plane['airline_name'] != '') ? $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')' : 'NA';
                    ?>
                      <tr>
                        <td><?= $row_plane['from_location'] ?></td>
                        <td><?= $row_plane['to_location'] ?></td>
                        <td><?= $airline ?></td>
                        <td><?= $row_plane['class'] ?></td>
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
      if ($sq_transport_count > 0) { ?>
        <!-- transport -->
        <section class="transportDetailsPanel transportDetailsLeftPanel main_block side_pad">
          <div class="travsportInfoBlock">
            <div class="transportIcon">
              <img src="<?= BASE_URL ?>images/quotation/p4/TI_car.png" class="img-responsive">
            </div>
            <div class="transportDetails">
              <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
                <table class="table no-marg tableTrnasp">
                  <thead>
                    <tr class="table-heading-row">
                      <th>VEHICLE</th>
                      <th>START_DATE</th>
                      <th>END_DATE</th>
                      <th>PICKUP</th>
                      <th>DROP</th>
                      <th>S_duration</th>
                      <th>VEHICLES</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $count = 0;
                    $sq_hotel = mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'");
                    while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) {
                      $transport_name = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id='$row_hotel[vehicle_name]'"));
                      // Pickup
                      if ($row_hotel['pickup_type'] == 'city') {
                        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[pickup]'"));
                        $pickup = $row['city_name'];
                      } else if ($row_hotel['pickup_type'] == 'hotel') {
                        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[pickup]'"));
                        $pickup = $row['hotel_name'];
                      } else {
                        $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[pickup]'"));
                        $airport_nam = clean($row['airport_name']);
                        $airport_code = clean($row['airport_code']);
                        $pickup = $airport_nam . " (" . $airport_code . ")";
                      }
                      //Drop-off
                      if ($row_hotel['drop_type'] == 'city') {
                        $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[drop]'"));
                        $drop = $row['city_name'];
                      } else if ($row_hotel['drop_type'] == 'hotel') {
                        $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[drop]'"));
                        $drop = $row['hotel_name'];
                      } else {
                        $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[drop]'"));
                        $airport_nam = clean($row['airport_name']);
                        $airport_code = clean($row['airport_code']);
                        $drop = $airport_nam . " (" . $airport_code . ")";
                      }
                    ?>
                      <tr>
                        <td><?= $transport_name['vehicle_name'] . $similar_text ?></td>
                        <td><?= date('d-m-Y', strtotime($row_hotel['start_date'])) ?></td>
                        <td><?= date('d-m-Y', strtotime($row_hotel['end_date'])) ?></td>
                        <td><?= $pickup ?></td>
                        <td><?= $drop ?></td>
                        <td><?= $row_hotel['service_duration'] ?></td>
                        <td><?= $row_hotel['vehicle_count'] ?></td>
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
<?php } ?>
<!-- traveling Information -->
<?php if ($sq_train_count != 0 || $sq_cruise_count != 0 || $sq_exc_count != 0) { ?>
  <!-- traveling Information -->
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="travelingDetails main_block mg_tp_30 pageSectionInner">
      <!-- Train -->
      <?php if ($sq_train_count > 0) { ?>
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
                      <th>Departure_D/T</th>
                      <th>Arrival_D/T</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sq_train = mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'");
                    while ($row_train = mysqli_fetch_assoc($sq_train)) {
                    ?>
                      <tr>
                        <td><?= $row_train['from_location'] ?></td>
                        <td><?= $row_train['to_location'] ?></td>
                        <td><?php echo ($row_train['class'] != '') ? $row_train['class'] : 'NA'; ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row_train['departure_date'])) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row_train['arrival_date'])) ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>
      <?php } ?>
      <!-- Cruise -->
      <?php
      if ($sq_cruise_count > 0) { ?>
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
                      <th>Departure_D/T</th>
                      <th>Arrival_D/T</th>
                      <th>Route</th>
                      <th>Cabin</th>
                      <th>Sharing</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sq_cruise = mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'");
                    while ($row_cruise = mysqli_fetch_assoc($sq_cruise)) {
                    ?>
                      <tr>
                        <td><?= date('d-m-Y H:i', strtotime($row_cruise['dept_datetime'])) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row_cruise['arrival_datetime'])) ?></td>
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
      <!-- excursion -->
      <?php
      if ($sq_exc_count > 0) { ?>
        <section class="transportDetailsPanel transportDetailsLeftPanel main_block side_pad">
          <div class="travsportInfoBlock">
            <div class="transportIcon">
              <img src="<?= BASE_URL ?>images/quotation/p4/TI_excursion.png" class="img-responsive">
            </div>

            <div class="transportDetails">
              <div class="table-responsive" style="margin-top:1px;margin-right: 1px;">
                <table class="table no-marg tableTrnasp">
                  <thead>
                    <tr class="table-heading-row">
                      <th>City </th>
                      <th>Activity_D/T</th>
                      <th>Activity Name</th>
                      <th>Transfer Option</th>
                      <th>Adult</th>
                      <th>CWB</th>
                      <th>CWOB</th>
                      <th>Infant</th>
                      <th>Vehicle</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $count = 0;
                    $sq_ex = mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'");
                    while ($row_ex = mysqli_fetch_assoc($sq_ex)) {
                      $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_ex[city_name]'"));
                      $sq_ex_name = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_ex[excursion_name]'"));
                    ?>
                      <tr>
                        <td><?= $sq_city['city_name'] ?></td>
                        <td><?= get_datetime_user($row_ex['exc_date']) ?></td>
                        <td><?= $sq_ex_name['excursion_name'] ?></td>
                        <td><?= $row_ex['transfer_option'] ?></td>
                        <td><?= $row_ex['adult'] ?></td>
                        <td><?= $row_ex['chwb'] ?></td>
                        <td><?= $row_ex['chwob'] ?></td>
                        <td><?= $row_ex['infant'] ?></td>
                        <td><?= $row_ex['vehicles'] ?></td>
                      </tr>
                    <?php }  ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>
      <?php } ?>

    </section>
  </section>
<?php } ?>
<!-- Itinerary -->
<?php
$count = 1;
$i = 0;
$dates = (array) get_dates_for_package_itineary($quotation_id);
$checkPageEnd = 0;
while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {

  $date_format = isset($dates[$i]) ? $dates[$i] : 'NA';
  $sq_day_image = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_images where quotation_id='$row_itinarary[quotation_id]'"));
  $day_url1 = explode(',', $sq_day_image['image_url']);
  $daywise_image = 'http://itourscloud.com/quotation_format_images/dummy-image.jpg';
  for ($count1 = 0; $count1 < sizeof($day_url1); $count1++) {
    $day_url2 = explode('=', $day_url1[$count1]);
    if (isset($day_url2[1]) && $day_url2[1] == $row_itinarary['day_count'] && isset($day_url2[0]) && $day_url2[0] == $row_itinarary['package_id']) {
      $daywise_image = $day_url2[2];
    }
  }
  if ($checkPageEnd % 1 == 0 || $checkPageEnd == 0) {
    $go = $checkPageEnd + 0;
    $flag = 0;
?>
    <section class="pageSection main_block">
      <!-- background Image -->
      <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">

      <section class="itinerarySec main_block side_pad mg_tp_30 pageSectionInner">
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
    // if($count%2!=0){
      ?>
      <ul class="print_itinenary no-pad no-marg">
        <li class="print_single_itinenary topItinerary">
          <div class="itineraryContent">
            <div class="itneraryImg">
              <img src="<?= $daywise_image ?>" class="img-responsive">
            </div>
            <div class="itneraryText">
              <div class="itneraryDayInfo">
                <i class="fa fa-map-marker" aria-hidden="true"></i><span> Day <?= $count ?> <small> (<?= $date_format ?>) </small> : <?= $row_itinarary['attraction'] ?> </span>
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
        // }
        if ($go == $checkPageEnd) {
          $flag = 1;
        ?>
      </ul>
      </section>
    </section>
  <?php
        }
        $count++;
        $i++;
        $checkPageEnd++;
      }
      if ($flag == 0) {
  ?>
  </ul>
  </section>
  </section>
<?php } ?>

<?php if ($sq_quotation['inclusions'] != '' && $sq_quotation['inclusions'] != ' ' && $sq_quotation['inclusions'] != '<div><br></div>') { ?>
  <!-- Inclusion -->
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

      <?php if ($sq_quotation['inclusions'] != '' && $sq_quotation['inclusions'] != ' ' && $sq_quotation['inclusions'] != '<div><br></div>') { ?>
        <!-- Inclusion -->
        <div class="row side_pad">
          <div class="col-md-12 mg_tp_30">
            <div class="incluExcluTermsTabPanel inclusions main_block">
              <h3 class="incexTitle">INCLUSIONS</h3>
              <div class="tabContent">
                <div class="real_text"><?= $sq_quotation['inclusions'] ?></div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </section>
  </section>
<?php } ?>

<?php if ($sq_quotation['exclusions'] != '' && $sq_quotation['exclusions'] != ' ' && $sq_quotation['exclusions'] != '<div><br></div>') { ?>
  <!-- Inclusion -->
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

      <?php if ($sq_quotation['exclusions'] != '' && $sq_quotation['exclusions'] != ' ' && $sq_quotation['exclusions'] != '<div><br></div>') { ?>
        <!-- Exclusion -->
        <div class="row side_pad">
          <div class="col-md-12 mg_tp_30">
            <div class="incluExcluTermsTabPanel exclusions main_block">
              <h3 class="incexTitle">EXCLUSIONS</h3>
              <div class="tabContent">
                <div class="real_text"><?= $sq_quotation['exclusions'] ?></div>
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
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

      <!-- Terms and Conditions -->
      <?php
      if (isset($sq_terms_cond['terms_and_conditions'])) { ?>
        <div class="row side_pad mg_tp_10">
          <div class="col-md-12">
            <div class="incluExcluTermsTabPanel inclusions main_block">
              <h3 class="incexTitle">TERMS AND CONDITIONS</h3>
              <div class="tabContent">
                <pre class="real_text"><?php echo $sq_terms_cond['terms_and_conditions']; ?></pre>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </section>
  </section>
<?php } ?>
<?php if ($sq_package_name['note'] != '' || $quot_note != '') { ?>
  <section class="pageSection main_block">
    <!-- background Image -->
    <img src="<?= BASE_URL ?>images/quotation/p6/pageBGF.jpg" class="img-responsive pageBGImg">
    <section class="incluExcluTerms main_block side_pad mg_tp_30 pageSectionInner">

      <?php
      if ($sq_package_name['note'] != '') { ?>
        <!-- Note -->
        <div class="row side_pad">
          <div class="col-md-12 mg_tp_30">
            <div class="incluExcluTermsTabPanel inclusions main_block">
              <h3 class="incexTitle">NOTE</h3>
              <div class="tabContent">
                <pre class="real_text"><?php echo $sq_package_name['note']; ?></pre>
              </div>
            </div>
          </div>
        </div>
      <?php }
      if ($quot_note != '') { ?>
        <pre class="real_text"><?php echo $quot_note; ?></pre>
      <?php } ?>
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
      <div class="col-md-3 passengerPanel endPagecenter mg_bt_30">
        <h3 class="endingPageTitle text-center">TOTAL GUEST</h3>
        <div class="icon">
          <img src="<?= BASE_URL ?>images/quotation/p4/adult.png" class="img-responsive">
          <h4 class="no-marg">Adult : <?= $sq_quotation['total_adult'] ?></h4>
          <i class="fa fa-plus"></i>
        </div>
        <div class="icon">
          <img src="<?= BASE_URL ?>images/quotation/p4/child.png" class="img-responsive">
          <h4 class="no-marg">CWB/CWOB/Infant : <?= $sq_quotation['children_with_bed'] + $sq_quotation['children_without_bed'] + $sq_quotation['total_infant'] ?></h4>
          <i class="fa fa-plus"></i>
        </div>
        <?php
        if (check_qr()) { ?>
          <div class="icon">
            <!-- <img src="<?= get_qr('Landscape Advanced') ?>images/quotation/p4/infant.png" class="img-responsive"> -->
            <?= get_qr('Landscape Advanced') ?>
            <h4 class="no-marg">Scan And Pay </h4>
          </div>
        <?php } ?>
      </div>
      <div class="col-md-9">
        <!-- Costing -->
        <div class="col-md-12 constingPanel constingBankingPanel mg_bt_30">
          <h3 class="costBankTitle text-center">COSTING DETAILS</h3>
          <?php
          $discount1 = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['discount']);
          if ($sq_quotation['discount'] != 0) {
            $discount = ' (Applied Discount : ' . $discount1 . ')';
          } else {
            $discount = '';
          }
          ?><p class="text-center costBankTitle"><?= $discount ?></p>
          <!-- Group costing -->
          <?php
          if ($sq_quotation['costing_type'] == 1) { ?>

            <div class="travsportInfoBlock1">
              <div class="transportDetails_costing">
                <div class="table-responsive">
                  <table class="table table-bordered tableTrnasp no-marg" style="width:100% !important;" id="tbl_emp_list">
                    <thead>
                      <tr class="table-heading-row">
                        <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Package Type</th>
                        <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Tour Cost</th>
                        <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Tax</th>
                        <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Tcs</th>
                        <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">TRAVEL/OTHER</th>
                        <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Total Cost</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order");
                      while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {
                        $basic_cost = $sq_costing['basic_amount'];
                        $service_charge = $sq_costing['service_charge'];
                        $service_tax_amount = 0;
                        $tax_show = '';
                        $bsmValues = json_decode($sq_costing['bsmValues'], true);
                        $discount_in = $sq_costing['discount_in'];
                        $discount = $sq_costing['discount'];
                        if ($discount_in == 'Percentage') {
                          $act_discount = (float)($service_charge) * (float)($discount) / 100;
                        } else {
                          $act_discount = ($service_charge != 0) ? $discount : 0;
                        }
                        $service_charge = $service_charge - (float)($act_discount);
                        $tour_cost = $basic_cost + $service_charge;
                        $name = '';
                        if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
                          $service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
                          for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                            $service_tax = explode(':', $service_tax_subtotal1[$i]);
                            $service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
                            $name .= $service_tax[0] . $service_tax[1] . ', ';
                          }
                        }

                        if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
                          $tcsper = $bsmValues[0]['tcsper'];
                          $tcsvalue = $bsmValues[0]['tcsvalue'];
                        } else {
                          $tcsper = 0;
                          $tcsvalue = 0;
                        }
                        $tcs_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);

                        $service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);
                        $quotation_cost = (float)($basic_cost) + (float)($service_charge) + (float)($service_tax_amount) + (float)($sq_quotation['train_cost']) + (float)($sq_quotation['cruise_cost']) + (float)($sq_quotation['flight_cost']) + (float)($sq_quotation['visa_cost']) + (float)($sq_quotation['guide_cost']) + (float)($sq_quotation['misc_cost']) + (float)($tcsvalue);
                        // $quotation_cost = ceil($quotation_cost);

                        // $quotation_cost = floor($quotation_cost);
                        ////////////////Currency conversion ////////////
                        $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
                        $act_tour_cost = (float)($quotation_cost) - (float)($service_charge) + (float)($sq_costing['service_charge']);
                        // $act_tour_cost = ceil($act_tour_cost);
                        $act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $act_tour_cost) : '';

                        $newBasic = currency_conversion($currency, $sq_quotation['currency_code'], $tour_cost);
                        $travel_cost = (float)($sq_quotation['train_cost']) + (float)($sq_quotation['flight_cost']) + (float)($sq_quotation['cruise_cost']) + (float)($sq_quotation['visa_cost']) + (float)($sq_quotation['guide_cost']) + (float)($sq_quotation['misc_cost']);
                        $travel_cost = currency_conversion($currency, $sq_quotation['currency_code'], $travel_cost);
                      ?>
                        <tr>
                          <td style="font-size: 14px !important; padding: 5px 18px !important;"><?php echo $sq_costing['package_type'] ?></td>
                          <td style="font-size: 14px !important; padding: 5px 18px !important;"><?= $newBasic ?></td>
                          <td style="font-size: 14px !important; padding: 5px 18px !important;"><?= str_replace(',', '', $name) . $service_tax_amount_show ?></td>
                          <td style="font-size: 14px !important; padding: 8px  20px !important;">Tcs:(<?= $tcsper ?>%)<br><?= $tcs_amount_show ?></td>
                          <td style="font-size: 14px !important; padding: 5px 18px !important;"><?= $travel_cost ?></td>
                          <td style="font-size: 14px !important; padding: 5px 18px !important;"><?= $currency_amount1 . ' <s style="white-space:nowrap !important;color: white !important;">' . $act_tour_cost_camount . '</s>' ?></td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div><!-- group Costing End -->
            <?php
          } else {
            $sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id'  order by sort_order");
            while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

              $service_charge = $sq_costing['service_charge'];
              $discount_in = $sq_costing['discount_in'];
              $discount = $sq_costing['discount'];
              if ($discount_in == 'Percentage') {
                $act_discount = (float)($service_charge) * (float)($discount) / 100;
              } else {
                $act_discount = ($service_charge != 0) ? $discount : 0;
              }
              $service_charge = $service_charge - (float)($act_discount);
              $total_pax = (float)($sq_quotation['total_adult']) + (float)($sq_quotation['children_with_bed']) + (float)($sq_quotation['children_without_bed']) + (float)($sq_quotation['total_infant']);
              $per_service_charge = (float)($service_charge) / (float)($total_pax);
              $o_per_service_charge = (float)($sq_costing['service_charge']) / (float)($total_pax);

              $adult_cost = ($sq_quotation['total_adult'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
              $child_with = ($sq_quotation['children_with_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_with'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
              $child_without = ($sq_quotation['children_without_bed'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['child_without'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);
              $infant_cost = ($sq_quotation['total_infant'] != '0') ? currency_conversion($currency, $sq_quotation['currency_code'], ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)))) : currency_conversion($currency, $sq_quotation['currency_code'], 0);

              // Without currency
              $adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
              $child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
              $child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
              $infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;
              $o_adult_costw = ($sq_quotation['total_adult'] != '0') ? ((float)($sq_costing['adult_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_adult'])) : 0;
              $o_child_withw = ($sq_quotation['children_with_bed'] != '0') ? ((float)($sq_costing['child_with'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_with_bed'])) : 0;
              $o_child_withoutw = ($sq_quotation['children_without_bed'] != '0') ? ((float)($sq_costing['child_without'] + (float)($o_per_service_charge)) * intval($sq_quotation['children_without_bed'])) : 0;
              $o_infant_costw = ($sq_quotation['total_infant'] != '0') ? ((float)($sq_costing['infant_cost'] + (float)($o_per_service_charge)) * intval($sq_quotation['total_infant'])) : 0;

              $service_tax_amount = 0;
              $tax_show = '';
              $bsmValues = json_decode($sq_costing['bsmValues'], true);
              $name = '';
              if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
                $service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
                for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                  $service_tax = explode(':', $service_tax_subtotal1[$i]);
                  $service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
                  $name .= $service_tax[0] . $service_tax[1] . ', ';
                }
              }
              if (isset($bsmValues[0]['tcsper']) && $bsmValues[0]['tcsper'] != 'NaN') {
                $tcsper = $bsmValues[0]['tcsper'];
                $tcsvalue = $bsmValues[0]['tcsvalue'];
              } else {
                $tcsper = 0;
                $tcsvalue = 0;
              }
              $service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);

              $total_child = (float)($sq_quotation['children_with_bed']) + (float)($sq_quotation['children_without_bed']);

              $quotation_cost = (float)($adult_costw) + (float)($child_withw) + (float)($child_withoutw) + (float)($infant_costw);
              $o_quotation_cost = (float)($o_adult_costw) + (float)($o_child_withw) + (float)($o_child_withoutw) + (float)($o_infant_costw);

              $other_cost = $service_tax_amount + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'];
              $travel_cost = ($sq_plane_count > 0) ? $sq_quotation['flight_ccost'] + $sq_quotation['flight_icost'] + $sq_quotation['flight_acost'] : 0;
              $travel_cost += ($sq_train_count > 0) ? $sq_quotation['train_ccost'] + $sq_quotation['train_icost'] + $sq_quotation['train_acost'] : 0;
              $travel_cost += ($sq_cruise_count > 0) ?  $sq_quotation['cruise_acost'] + $sq_quotation['cruise_icost'] + $sq_quotation['cruise_ccost'] : 0;


              $train_cost_a = $sq_quotation['train_acost'] * intval($sq_quotation['total_adult']);
              $train_cost_cw = $sq_quotation['train_ccost'] * intval($sq_quotation['children_with_bed']);
              $train_cost_cwo =  $sq_quotation['train_ccost'] * intval($sq_quotation['children_without_bed']);
              $train_cost_i = $sq_quotation['train_icost'] * intval($sq_quotation['total_infant']);

              $train_total_cost = ($sq_train_count > 0) ? $train_cost_a + $train_cost_cw + $train_cost_cwo + $train_cost_i : 0;
              // flight cost
              $flight_cost_a = $sq_quotation['flight_acost'] * intval($sq_quotation['total_adult']);
              $flight_cost_cw = $sq_quotation['flight_ccost'] * intval($sq_quotation['children_with_bed']);
              $flight_cost_cwo =  $sq_quotation['flight_ccost'] * intval($sq_quotation['children_without_bed']);
              $flight_cost_i = $sq_quotation['flight_icost'] * intval($sq_quotation['total_infant']);

              $flight_total_cost = ($sq_plane_count > 0) ? $flight_cost_a + $flight_cost_cw + $flight_cost_cwo + $flight_cost_i : 0;
              // Cruise cost


              $cruise_cost_a = $sq_quotation['cruise_acost'] * intval($sq_quotation['total_adult']);
              $cruise_cost_cw = $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_with_bed']);
              $cruise_cost_cwo =  $sq_quotation['cruise_ccost'] * intval($sq_quotation['children_without_bed']);
              $cruise_cost_i = $sq_quotation['cruise_icost'] * intval($sq_quotation['total_infant']);

              $cruise_total_cost = ($sq_cruise_count > 0) ? $cruise_cost_a + $cruise_cost_cw + $cruise_cost_cwo + $cruise_cost_i : 0;



              $quotation_cost = (float)($quotation_cost) + (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
              // $quotation_cost = ceil($quotation_cost);
              $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);

              $tcs_show1 = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);
              // $o_quotation_cost = (float)($o_quotation_cost) + (float)($travel_cost) + (float)($other_cost);
              $o_quotation_cost = (float)($o_quotation_cost) +  (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);


              // $o_quotation_cost = ceil($o_quotation_cost);
              $act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $o_quotation_cost) : '';
            ?>
              <div class="travsportInfoBlock1 mg_bt_30">
                <div class="transportDetails_costing package_costing">
                  <h5 style="margin:0px 2px 10px 10px!important;" class="endingPageTitle"><?= $sq_costing['package_type'] . ' (' . $currency_amount1 . ' <s style="color: white !important;">' . $act_tour_cost_camount . '</s>)' ?></h5>
                  <div class="table-responsive">
                    <table class="table no-marg table-bordered tableTrnasp" id="tbl_emp_list">
                      <thead>
                        <tr class="table-heading-row">
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">ADULT</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">CWB</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">CWOB</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">INFANT</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">TAX</th>
                          <th style="font-size: 16px !important; font-weight: 500 !important; padding: 8px  20px !important;">TCS</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Visa</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Guide</th>
                          <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Misc</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><?= $adult_cost ?></td>
                          <td><?= $child_with ?></td>
                          <td><?= $child_without  ?></td>
                          <td><?= $infant_cost ?></td>
                          <td style="color: #fff !important;"><?= str_replace(',', '', $name) .  $service_tax_amount_show ?></td>
                          <td>Tcs:(<?= $tcsper ?>%)<br><?= $tcs_show1 ?></td>
                          <td><?= currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['visa_cost']) ?></td>
                          <td><?= currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['guide_cost'])  ?></td>
                          <td><?= currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['misc_cost'])  ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php
              if ($sq_plane_count > 0 || $sq_train_count > 0 || $sq_cruise_count > 0) { ?>
                <div class="travsportInfoBlock1 mg_bt_30">
                  <div class="transportDetails_costing package_costing">
                    <div class="table-responsive">
                      <table class="table table-bordered no-marg tableTrnasp" id="tbl_emp_list">
                        <thead>
                          <tr class="table-heading-row">
                            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Travel_Type</th>
                            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Adult(PP)</th>
                            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Child(PP)</th>
                            <th style="font-size: 16px !important; font-weight: 600 !important; padding: 8px  20px !important;">Infant(PP)</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if ($sq_plane_count > 0) { ?>
                            <tr>
                              <td><?= 'Flight' ?></td>
                              <td><?= $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                              <td><?= ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))  ?></td>
                              <td><?= $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['flight_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0))  ?></td>
                            </tr>
                          <?php }
                          if ($sq_train_count > 0) { ?>
                            <tr>
                              <td><?= 'Train' ?></td>
                              <td><?= $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                              <td><?= ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                              <td><?= $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['train_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                            </tr>
                          <?php }
                          if ($sq_cruise_count > 0) { ?>
                            <tr>
                              <td><?= 'Cruise' ?></td>
                              <td><?= $sq_quotation['total_adult'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_acost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                              <td><?= ($sq_quotation['children_with_bed'] != 0 || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                              <td><?= $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
          <?php }
            }
          } ?>
          <?php
          // if ($tcs_note_show != '') { 
          ?>
          <!-- <p style="margin-left:10px!important;" class="costBankTitle"> -->
          <?php // $tcs_note_show 
          ?></p>
          <?php //} 
          ?>
          <?php
          if ($sq_quotation['other_desc'] != '') { ?>
            <p style="margin-left:10px!important;" class="costBankTitle">MISCELLANEOUS DESCRIPTION: <?= $sq_quotation['other_desc'] ?></p>
          <?php } ?>
          <!-- Per person costing End -->
        </div>


      </div>
    </div>

  </section>
</section>

<section class="pageSection main_block">
  <!-- background Image -->
  <img src="<?= BASE_URL ?>images/quotation/p6/pageBG.jpg" class="img-responsive pageBGImg">
  <section class="contactSection main_block mg_tp_30 pageSectionInner">
    <!-- Bank Detail -->
    <div class="col-md-12 constingBankingPanel BankingPanel">
      <h3 class="costBankTitle text-center">BANK DETAILS</h3>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/bankName.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting  ?></h4>
        <p>BANK NAME</p>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/branchName.png" class="img-responsive"> </div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?></h4>
        <p>BRANCH</p>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accName.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name  ?></h4>
        <p>A/C TYPE</p>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/accNumber.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no  ?></h4>
        <p>A/C NO</p>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?></h4>
        <p>BANK ACCOUNT NAME</p>
      </div>
      <div class="col-md-4 text-center mg_bt_30">
        <div class="icon"><img src="<?= BASE_URL ?>images/quotation/p4/code.png" class="img-responsive"></div>
        <h4 class="no-marg"><?= ($sq_bank_count > 0 || $sq_bank_branch['swift_code'] != '') ? strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?></h4>
        <p>SWIFT CODE</p>
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