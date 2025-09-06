<?php
include_once('../../model.php');
global $app_name, $app_contact_no, $app_email_id, $app_landline_no, $app_address, $app_website, $similar_text, $currency;

$quotation_id1 = $_GET['quotation_id'];
$quotation_id = base64_decode($quotation_id1);
$count = 0;

$in = 'in';

$sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_master where quotation_id='$quotation_id'"));

$quotation_date = $sq_quotation['quotation_date'];
$yr = explode("-", $quotation_date);
$year = $yr[0];

$sq_cost =  mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id = '$quotation_id'"));
$sq_tours_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));
$sq_dest = mysqli_fetch_assoc(mysqlQuery("select link from video_itinerary_master where dest_id = '$sq_tours_package[dest_id]'"));

$sq_login = mysqli_fetch_assoc(mysqlQuery("select * from roles where id='$sq_quotation[login_id]'"));
$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$sq_login[emp_id]'"));

if ($sq_emp_info['first_name'] == '') {
  $emp_name = 'Admin';
} else {
  $emp_name = $sq_emp_info['first_name'] . ' ' . $sq_emp_info['last_name'];
}

$sq_costing = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id'"));
$basic_cost = $sq_costing['basic_amount'];
$service_charge = $sq_costing['service_charge'];
$tour_cost = $basic_cost + $service_charge;
$service_tax_amount = 0;
$tax_show = '';
$bsmValues = json_decode($sq_costing['bsmValues']);

$name = '';
if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
  $service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
  for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
    $service_tax = explode(':', $service_tax_subtotal1[$i]);
    $service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
    $name .= $service_tax[0]  . $service_tax[1] . ', ';
  }
}
$service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);
if ($bsmValues[0]->service != '') {   //inclusive service charge
  $newBasic = $tour_cost + $service_tax_amount;
  $tax_show = '';
} else {
  $tax_show =  rtrim($name, ', ') . ' : ' . ($service_tax_amount);
  $newBasic = $tour_cost;
}

////////////Basic Amount Rules
if ($bsmValues[0]->basic != '') { //inclusive markup
  $newBasic = $tour_cost + $service_tax_amount;
  $tax_show = '';
}

if ($bsmValues[0]->tcsvalue != '') {
  $tcsvalue = $bsmValues[0]->tcsvalue;
} else {
  $tcsvalue = '0';
}

$quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $sq_quotation['train_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'] + $tcsvalue;
// $quotation_cost = ceil($quotation_cost);
$quotation_cost1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);

$sq_transport = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));
$sq_package_program = mysqlQuery("select * from  package_quotation_program where quotation_id='$quotation_id'");
$sq_train = mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'");
$sq_plane = mysqlQuery("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'");

$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$sq_quotation[package_id]'"));
$sq_terms = mysqli_fetch_assoc(mysqlQuery("select * from terms_and_conditions where type='FIT Quotation' and active_flag='Active'"));
$sq_plane_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_plane_entries where quotation_id='$quotation_id'"));
$sq_train_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_train_entries where quotation_id='$quotation_id'"));
$sq_cruise_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'"));
?>
<!DOCTYPE html>
<html>

<head>
  <title>Tour Quotation</title>

  <meta property="og:title" content="Tour Operator Software - iTours" />
  <meta property="og:description" content="Welcome to tiTOurs leading tour operator software, CRM, Accounting, Billing, Invocing, B2B, B2C Online Software for all small scale & large scale companies" />
  <meta property="og:url" content="http://www.itouroperatorsoftware.com" />
  <meta property="og:site_name" content="iTour Operator Software" />
  <meta property="og:image" content="http://www.itouroperatorsoftware.com/images/iTours-Tour-Operator-Software-logo.png" />
  <meta property="og:image:width" content="215" />
  <meta property="og:image:height" content="83" />

  <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,500" rel="stylesheet">



  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/font-awesome-4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/jquery-ui.min.css" type="text/css" />

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/bootstrap.min.css">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/owl.carousel.css" type="text/css" />

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/owl.theme.css" type="text/css" />

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/app/app.php">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/app/modules/single_quotation.php">

</head>


<body>

  <header>
    <!-- Header -->
    <nav class="navbar navbar-default">

      <!-- Header-Top -->
      <div class="Header_Top">
        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <ul class="company_contact">
                <li><a href="mailto:email@company_name.com"><i class="fa fa-envelope"></i> <?= $app_email_id; ?></a></li>
                <li><i class="fa fa-mobile"></i> <?= $app_contact_no; ?></li>
                <li><i class="fa fa-phone"></i> <?= $app_landline_no; ?></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header single_quotation_head">
          <a class="navbar-brand" href="http://<?= $app_website ?>"><img src="<?php echo BASE_URL ?>images/Admin-Area-Logo.png" class="img-responsive"></a>
          <div class="logo_right_part">
            <h1><i class="fa fa-pencil-square-o"></i> Tour Quotation</h1>
          </div>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="nav">
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul id="menu-center" class="nav navbar-nav">
              <li class="active"><a href="#0">Package</a></li>
              <li><a href="#1">Costing</a></li>
              <li><a href="#2">Transport</a></li>
              <li><a href="#3">Itinerary</a></li>
              <li><a href="#4">Accommodations</a></li>
              <li><a href="#5">Train</a></li>
              <li><a href="#6">Flight</a></li>
              <li><a href="#11">Cruise</a></li>
              <li><a href="#10">Activity</a></li>
              <li><a href="#7">Incl/Excl</a></li>
            </ul>
          </div><!-- /.navbar-collapse -->
        </div>
      </div><!-- /.container-fluid -->
    </nav>
    <!-- Header-End -->
  </header>





  <!-- Package -->

  <section id="0" class="main_block link_page_section">

    <div class="container">

      <div class="sec_heding">

        <h2>Package Tour Details</h2>

      </div>

      <div class="row">

        <div class="col-md-6 col-xs-12">

          <ul class="pack_info">

            <li><span>Package Name </span>: <?= $sq_tours_package['package_name']; ?> </li>

            <li><span>Quotation ID </span>: <?= get_quotation_id($quotation_id, $year); ?></li>
            <?php
            $discount = currency_conversion($currency, $sq_quotation['currency_code'], $sq_quotation['discount']);
            if ($sq_quotation['discount'] != 0) { ?><li><span>Discount </span>: <?= $discount ?> Applied</li> <?php } ?>
            <li><span>E-mail ID </span>: <?= $sq_quotation['email_id']; ?></li>

          </ul>

        </div>

        <div class="col-md-6 col-xs-12">

          <ul class="pack_info">

            <li><span>Travel Date </span>: <?= get_date_user($sq_quotation['from_date']) . ' To ' . get_date_user($sq_quotation['to_date']); ?></li>
            <li><span>Customer Name </span>: <?= $sq_quotation['customer_name']; ?></li>


          </ul>

        </div>

      </div>

      <div class="row">

        <div class="col-md-12">

          <div class="adolence_info mg_tp_25">

            <ul class="main_block">

              <li class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs"><span>Adult : </span><?= $sq_quotation['total_adult']; ?></li>

              <li class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs sm_r_brd_r8"><span>Infant : </span><?= $sq_quotation['total_infant']; ?></li>

              <li class="col-md-3 col-sm-4 col-xs-12 mg_bt_10_sm_xs"><span>Child With Bed : </span><?= $sq_quotation['children_with_bed']; ?></li>

              <li class="col-md-3 col-sm-4 col-xs-12"><span>Child Without Bed : </span><?= $sq_quotation['children_without_bed']; ?></li>
              <li class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_sm_xs"><span>Total : </span><?= $sq_quotation['total_passangers']; ?></li>


            </ul>

          </div>

        </div>

      </div>

    </div>

  </section>




  <!-- Costing -->

  <section id="1" class="main_block link_page_section">

    <div class="container">

      <?php
      $sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order");
      while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

        if ($sq_quotation['costing_type'] == 2) {

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
          $bsmValues = json_decode($sq_costing['bsmValues']);
          $name = '';
          if ($sq_costing['service_tax_subtotal'] !== 0.00 && ($sq_costing['service_tax_subtotal']) !== '') {
            $service_tax_subtotal1 = explode(',', $sq_costing['service_tax_subtotal']);
            for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
              $service_tax = explode(':', $service_tax_subtotal1[$i]);
              $service_tax_amount = (float)($service_tax_amount) + (float)($service_tax[2]);
              $name .= $service_tax[0] . $service_tax[1] . ', ';
            }
          }

          if ($bsmValues[0]->tcsvalue != '') {
            $tcsvalue = $bsmValues[0]->tcsvalue;
          } else {
            $tcsvalue = '0';
          }

          $tcs_amount_show  = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);

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


          $quotation_cost = (float)($quotation_cost) +  (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost) + (float)($tcsvalue);
          // $quotation_cost = ceil($quotation_cost);
          $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);

          $o_quotation_cost = (float)($o_quotation_cost) +  (float)($train_total_cost) + (float)($flight_total_cost) + (float)($cruise_total_cost) + (float)($other_cost);

          $act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $o_quotation_cost + (float)($tcsvalue)) : ''; ?>

          <div class="sec_heding mg_tp_20">
            <h2>Costing for <?= $sq_costing['package_type'] . ' (' . $currency_amount1 . ' <s>' . $act_tour_cost_camount . '</s>)' ?></h2>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table class="table table-bordered no-marg" id="tbl_emp_list">
                  <thead>
                    <tr class="table-heading-row">
                      <th>Adult</th>
                      <th>Child_With_Bed</th>
                      <th>Child_Without_Bed</th>
                      <th>Infant</th>
                      <th>Total Tax</th>
                      <th>Tcs</th>
                      <th>Visa</th>
                      <th>Guide</th>
                      <th>Miscellaneous</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?= $adult_cost; ?></td>
                      <td><?= $child_with ?></td>
                      <td><?= $child_without; ?></td>
                      <td><?= $infant_cost; ?></td>
                      <td><?= $service_tax_amount_show ?></td>
                      <td><?= $tcs_amount_show   ?></td>
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
            <div class="row mg_tp_10">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table table-bordered no-marg" id="tbl_emp_list">
                    <thead>
                      <tr class="table-heading-row">
                        <th>Travel_Type</th>
                        <th>Adult(PP)</th>
                        <th>Child(PP)</th>
                        <th>Infant(PP)</th>
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
                          <td><?= ($sq_quotation['children_with_bed'] != 0  || $sq_quotation['children_without_bed'] != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_ccost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                          <td><?= $sq_quotation['total_infant'] != 0 ? currency_conversion($currency, $sq_quotation['currency_code'], (float)($sq_quotation['cruise_icost'])) : currency_conversion($currency, $sq_quotation['currency_code'], (float)(0)) ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
      <?php
          }
        }
      } ?>

      <?php
      if ($sq_quotation['costing_type'] == 1) {
      ?>
        <div class="sec_heding">

          <h2>Costing</h2>

        </div>

        <div class="row">

          <div class="col-md-12">

            <div class="adolence_info">

              <?php
              $sq_costing1 = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order");
              while ($sq_costing = mysqli_fetch_assoc($sq_costing1)) {

                $basic_cost = $sq_costing['basic_amount'];
                $service_charge = $sq_costing['service_charge'];
                $service_tax_amount = 0;
                $tax_show = '';
                $bsmValues = json_decode($sq_costing['bsmValues']);
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

                if ($bsmValues[0]->tcsvalue != '') {
                  $tcsvalue = $bsmValues[0]->tcsvalue;
                } else {
                  $tcsvalue = '0';
                }


                $service_tax_amount_show = currency_conversion($currency, $sq_quotation['currency_code'], $service_tax_amount);
                $quotation_cost = $basic_cost + $service_charge + $service_tax_amount + $sq_quotation['train_cost'] + $sq_quotation['cruise_cost'] + $sq_quotation['flight_cost'] + $sq_quotation['visa_cost'] + $sq_quotation['guide_cost'] + $sq_quotation['misc_cost'] + $tcsvalue;
                // $quotation_cost = ceil($quotation_cost);
                ////////////////Currency conversion ////////////
                $currency_amount1 = currency_conversion($currency, $sq_quotation['currency_code'], $quotation_cost);
                $act_tour_cost = (float)($quotation_cost) - (float)($service_charge) + (float)($sq_costing['service_charge']);
                // $act_tour_cost = ceil($act_tour_cost);
                $act_tour_cost_camount = ($discount != 0) ? currency_conversion($currency, $sq_quotation['currency_code'], $act_tour_cost) : '';

                $newBasic = currency_conversion($currency, $sq_quotation['currency_code'], $tour_cost);


                $travel_cost = (float)($sq_quotation['train_cost']) + (float)($sq_quotation['flight_cost']) + (float)($sq_quotation['cruise_cost']) + (float)($sq_quotation['visa_cost']) + (float)($sq_quotation['guide_cost']) + (float)($sq_quotation['misc_cost']);
                $travel_cost = currency_conversion($currency, $sq_quotation['currency_code'], $travel_cost);

                $tcsvalue = currency_conversion($currency, $sq_quotation['currency_code'], $tcsvalue);
              ?>
                <div class="row mg_bt_10">
                  <ul class="main_block">
                    <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10"><span>Package Type :
                      </span><u><?= $sq_costing['package_type'] ?></u></li>
                    <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10"><span>Tour Cost :
                      </span><?= $newBasic ?></li>
                    <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10"><span>TCS :
                      </span><u><?= $tcsvalue ?></u></li>
                    <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10 sm_r_brd_r8"><span>Tax :
                      </span><?= $service_tax_amount_show ?></li>
                    <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10"><span>Travel + Other Cost :
                      </span><?= $travel_cost ?></li>
                    <li class="col-md-4 col-sm-6 col-xs-12 mg_bt_10_xs  highlight"
                      style="font-weight: 600; color: #016d01;"><span class="highlight">Quotation Cost : </span><?= $currency_amount1 . '  <s>' . $act_tour_cost_camount . '</s>' ?></li>
                  </ul>
                </div>
              <?php
              } ?>

            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>
  <!-- Transport -->
  <?php
  $sq_trans_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_transport_entries2 where quotation_id='$quotation_id'"));
  if ($sq_trans_count > 0) {
  ?>

    <section id="2" class="main_block link_page_section">

      <div class="container">

        <div class="sec_heding">

          <h2>Transport</h2>

        </div>

        <div class="row">

          <div class="col-md-12">

            <div class="table-responsive">

              <table class="table table-bordered no-marg" id="tbl_emp_list">

                <thead>
                  <tr class="table-heading-row">
                    <th>VEHICLE</th>
                    <th>START DATE</th>
                    <th>END DATE</th>
                    <th>PICKUP LOCATION</th>
                    <th>DROP LOCATION</th>
                    <th>SERVICE DURATION</th>
                    <th>TOTAL VEHICLES</th>
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
                      <td><?= get_date_user($row_hotel['start_date']) ?></td>
                      <td><?= get_date_user($row_hotel['end_date']) ?></td>
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

      </div>

    </section>

  <?php } ?>



  <!-- Tour Itinenary -->

  <section id="3" class="main_block link_page_section">

    <div class="container">
      <div class="sec_heding">
        <h2>Tour Itinerary</h2>
      </div>
      <div class="row mg_bt_30">
        <div class="col-md-12">
          <div class="adolence_info mg_tp_15">
            <ul class="main_block">
              <li class="col-md-12 col-sm-4 col-xs-12 mg_bt_10_xs"><img src="<?php echo BASE_URL . 'images/quotation/youtube-icon.png'; ?>" class="itinerary-img img-responsive">
                &nbsp;Destination Guide Video :&nbsp;<a href="<?= $sq_dest['link'] ?>" class="no-marg itinerary-link" target="_blank"><?= $sq_dest['link'] ?> </a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12 Itinenary_detail app_accordion">
          <div class="panel-group main_block" id="pkg_accordion" role="tablist" aria-multiselectable="true">
            <?php
            $count = 1;
            $i = 0;
            $dates = (array) get_dates_for_package_itineary($quotation_id);
            while ($row_itinarary = mysqli_fetch_assoc($sq_package_program)) {

              $date_format = isset($dates[$i]) ? $dates[$i] : 'NA';
              $i++;
              $sq_day_image = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_quotation_images where quotation_id='$row_itinarary[quotation_id]' and package_id='$sq_quotation[package_id]'"));
              $day_url1 = explode(',', $sq_day_image['image_url']);
              $daywise_image = 'http://itourscloud.com/quotation_format_images/dummy-image.jpg';
              for ($count1 = 0; $count1 < sizeof($day_url1); $count1++) {
                $day_url2 = explode('=', $day_url1[$count1]);
                if ($day_url2[0] == $sq_quotation['package_id'] && $day_url2[1] == $row_itinarary['day_count']) {
                  $daywise_image = $day_url2[2];
                }
              } ?>
              <div class="panel panel-default main_block">
                <div class="panel-heading main_block" role="tab" id="heading<?= $count; ?>">
                  <div class="Normal collapsed main_block" role="button" data-toggle="collapse" data-parent="#pkg_accordion" href="#collapse<?= $count; ?>" aria-expanded="false" aria-controls="collapse<?= $count; ?>">
                    <div class="col-md-2"><span><em>Day :</em> <?= $count; ?> <?= '(' . $date_format . ')' ?></span></div>
                    <div class="col-md-4" style="line-height: 26px; padding:7px 15px 7px 15px;"><span><em>Attraction :</em> <?= $row_itinarary['attraction']; ?></span></div>
                    <div class="col-md-3"><span><em>Overnight stay :</em> <?= $row_itinarary['stay']; ?></span></div>
                    <div class="col-md-2"><span><em>Meal Plan :</em> <?= $row_itinarary['meal_plan']; ?></span></div>
                  </div>
                </div>
                <div id="collapse<?= $count; ?>" class="panel-collapse <?= $in; ?> collapse main_block" role="tabpanel" aria-labelledby="heading<?= $count; ?>">

                  <div class="panel-body">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                      <div class="Sightseeing_img_block main_block" onclick="display_destination('<?php echo $daywise_image; ?>');">
                        <img src="<?php echo $daywise_image; ?>" class="img-responsive">
                      </div>
                    </div>
                    <pre class="real_text"><?= $row_itinarary['day_wise_program']; ?></pre>
                  </div>
                </div>
              </div>
            <?php $in = '';
              $count++;
            } ?>
          </div>
        </div>
      </div>

      <div id="div_quotation_form1"></div>

      <!-- Accomodations -->

      <?php
      $sq_hotel_count = mysqli_num_rows(mysqlQuery("select DISTINCT(package_type) from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'"));
      if ($sq_hotel_count > 0) { ?>

        <section id="4" class="main_block link_page_section">
          <div class="container">
            <div class="sec_heding">
              <h2>accommodations</h2>
            </div>
            <?php
            $sq_package_type = mysqlQuery("select DISTINCT(package_type) from package_tour_quotation_hotel_entries where quotation_id='$quotation_id'");
            while ($row_hotel1 = mysqli_fetch_assoc($sq_package_type)) {

              $sq_package_type1 = mysqlQuery("select * from package_tour_quotation_hotel_entries where quotation_id='$quotation_id' and package_type='$row_hotel1[package_type]'");
            ?>
              <legend class="text-left"><?= $row_hotel1['package_type'] ?></legend>
              <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12">
                  <?php
                  while ($row_hotel = mysqli_fetch_assoc($sq_package_type1)) {

                    $hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_hotel[hotel_name]'"));
                    $city_name = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotel[city_name]'"));
                    $sq_hotel_count = mysqli_num_rows(mysqlQuery("select * from hotel_vendor_images_entries where hotel_id = '$row_hotel[hotel_name]'"));
                    if ($sq_hotel_count == '0') {
                      $newUrl = BASE_URL . 'images/dummy-image.jpg';
                    } else {
                      $sq_hotel_image1 = mysqli_fetch_assoc(mysqlQuery("select * from hotel_vendor_images_entries where hotel_id = '$row_hotel[hotel_name]'"));
                      $image = $sq_hotel_image1['hotel_pic_url'];
                      $newUrl = preg_replace('/(\/+)/', '/', $image);
                      $newUrl = explode('uploads', $newUrl);
                      $newUrl = BASE_URL . 'uploads' . $newUrl[1];
                    }
                  ?>

                    <div class="col-md-4 col-sm-6 col-xs-12 mg_bt_20">
                      <div class="single_accomodation_hotel mg_bt_10_xs">
                        <div class="acco_hotel_image" style="display: block;cursor: pointer;" onclick="display_gallery('<?php echo $row_hotel['hotel_name']; ?>')">
                          <img src="<?php echo $newUrl; ?>" style="width: 100%;height: 135px;" class="img-responsive">
                        </div>
                        <div class="acco_hotel_detail">
                          <ul class="text-center">
                            <li class="acco-_hotel_name"><?= $hotel_name['hotel_name'] . $similar_text; ?></li>
                            <li class="acco-_hotel_star"><?= $row_hotel['hotel_type']; ?></li>
                            <li class="acco-_hotel_days"><span>Room Category : </span><?= $row_hotel['room_category']; ?></li>
                            <li class="acco-_hotel_city"><span>City : </span><?= $city_name['city_name']; ?></li>
                            <li class="acco-_hotel_days"><span>Check-In : </span><?= get_date_user($row_hotel['check_in']); ?></li>
                            <li class="acco-_hotel_days"><span>Check-Out : </span><?= get_date_user($row_hotel['check_out']); ?></li>
                          </ul>
                        </div>

                        <div class="acco_hotel_btn text-center mg_tp_20">

                          <button type="button" data-toggle="modal" onclick="display_gallery('<?php echo $row_hotel['hotel_name']; ?>')" title="View Gallery">Hotel Gallery</button>

                        </div>
                      </div>
                    </div>
                  <?php
                  }
                  ?>
                </div>
              </div>
            <?php } ?>
          </div>
        </section>
      <?php
      }
      ?>

      <div id="div_quotation_form"></div>

      <!-- Train -->

      <?php
      if ($sq_train_count > 0) {

      ?>

        <section id="5" class="main_block link_page_section">

          <div class="container">

            <div class="sec_heding">

              <h2>Train</h2>

            </div>

            <div class="row">

              <div class="col-md-12">

                <div class="table-responsive">

                  <table class="table table-bordered no-marg" id="tbl_emp_list">

                    <thead>

                      <tr class="table-heading-row">

                        <th>From</th>

                        <th>To</th>

                        <th>Class</th>

                        <th>Departure_Datetime</th>

                        <th>Arrival_DateTime</th>

                      </tr>

                    </thead>

                    <tbody>

                      <?php while ($row_train = mysqli_fetch_assoc($sq_train)) { ?>

                        <tr>

                          <td><?= $row_train['from_location']; ?></td>

                          <td><?= $row_train['to_location']; ?></td>

                          <td><?php echo ($row_train['class'] != '') ? $row_train['class'] : 'NA'; ?></td>

                          <td><?= date('d-m-Y H:i', strtotime($row_train['departure_date'])); ?></td>

                          <td><?= date('d-m-Y H:i', strtotime($row_train['arrival_date'])); ?></td>

                        </tr>

                      <?php } ?>

                    </tbody>

                  </table>

                </div>

              </div>

            </div>

          </div>

        </section>

      <?php } ?>



      <!-- Flight -->

      <?php
      if ($sq_plane_count > 0) { ?>

        <section id="6" class="main_block link_page_section">

          <div class="container">

            <div class="sec_heding">

              <h2>Flight</h2>

            </div>

            <div class="row">

              <div class="col-md-12">

                <div class="table-responsive">

                  <table class="table table-bordered no-marg" id="tbl_emp_list">

                    <thead>

                      <tr class="table-heading-row">

                        <th>From</th>

                        <th>To</th>

                        <th>Airline</th>

                        <th>Class</th>

                        <th>Departure_DateTime</th>

                        <th>Arrival_DateTime</th>

                      </tr>

                    </thead>

                    <tbody>

                      <?php while ($row_plane = mysqli_fetch_assoc($sq_plane)) {
                        $sq_airline = mysqli_fetch_assoc(mysqlQuery("select * from airline_master where airline_id='$row_plane[airline_name]'"));
                        $airline = ($row_plane['airline_name'] != '') ? $sq_airline['airline_name'] . ' (' . $sq_airline['airline_code'] . ')' : 'NA';
                      ?>
                        <tr>

                          <td><?= $row_plane['from_location']; ?></td>

                          <td><?= $row_plane['to_location']; ?></td>

                          <td><?= $airline ?></td>

                          <td><?php echo ($row_plane['class'] != '') ? $row_plane['class'] : 'NA'; ?></td>

                          <td><?= date('d-m-Y H:i', strtotime($row_plane['dapart_time'])); ?></td>

                          <td><?= date('d-m-Y H:i', strtotime($row_plane['arraval_time'])); ?></td>

                        </tr>

                      <?php } ?>

                    </tbody>

                  </table>

                </div>

              </div>

            </div>

          </div>

        </section>

      <?php } ?>


      <!-- Flight -->

      <?php
      if ($sq_cruise_count > 0) { ?>

        <section id="11" class="main_block link_page_section">

          <div class="container">

            <div class="sec_heding">

              <h2>Cruise</h2>

            </div>

            <div class="row">

              <div class="col-md-12">

                <div class="table-responsive">

                  <table class="table table-bordered no-marg" id="tbl_emp_list">

                    <thead>

                      <tr class="table-heading-row">

                        <th>Departure_Datetime</th>
                        <th>Arrival_Datetime</th>
                        <th>Route</th>
                        <th>Cabin</th>
                        <th>Sharing</th>

                      </tr>

                    </thead>

                    <tbody>

                      <?php
                      $sq_train = mysqlQuery("select * from package_tour_quotation_cruise_entries where quotation_id='$quotation_id'");
                      while ($row_train = mysqli_fetch_assoc($sq_train)) {
                      ?>
                        <tr>
                          <td><?= get_datetime_user($row_train['dept_datetime']) ?></td>
                          <td><?= get_datetime_user($row_train['arrival_datetime']) ?></td>
                          <td><?= $row_train['route'] ?></td>
                          <td><?= ($row_train['cabin']) ?></td>
                          <td><?= ($row_train['sharing']) ?></td>
                        </tr>
                      <?php
                      } ?>

                    </tbody>

                  </table>

                </div>

              </div>

            </div>

          </div>

        </section>

      <?php } ?>


      <!-- Activity -->

      <?php

      $sq_ex_count = mysqli_num_rows(mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'"));



      if ($sq_ex_count > 0) { ?>

        <section id="10" class="main_block link_page_section">

          <div class="container">

            <div class="sec_heding">

              <h2>Activity</h2>

            </div>

            <div class="row">

              <div class="col-md-12">

                <div class="table-responsive">

                  <table class="table table-bordered no-marg" id="tbl_emp_list">

                    <thead>

                      <tr class="table-heading-row">
                        <th>Sr.No</th>
                        <th>Activity Date</th>
                        <th>City Name</th>
                        <th>Activity Name</th>
                        <th>Transfer option</th>
                        <th>Adult</th>
                        <th>CWB</th>
                        <th>CWOB</th>
                        <th>Infant</th>
                        <th>Vehicle</th>
                      </tr>

                    </thead>

                    <tbody>

                      <?php
                      $sq_ex = mysqlQuery("select * from package_tour_quotation_excursion_entries where quotation_id='$quotation_id'");
                      $count = 0;
                      while ($row_ex = mysqli_fetch_assoc($sq_ex)) {
                        $count++;
                        $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_ex[city_name]'"));
                        $sq_ex_name = mysqli_fetch_assoc(mysqlQuery("select * from excursion_master_tariff where entry_id='$row_ex[excursion_name]'"));
                      ?>
                        <tr>
                          <td><?= $count; ?></td>
                          <td><?= get_datetime_user($row_ex['exc_date']) ?></td>
                          <td><?= $sq_city['city_name']; ?></td>
                          <td><?= $sq_ex_name['excursion_name']; ?></td>
                          <td><?= $row_ex['transfer_option'] ?></td>
                          <td><?= $row_ex['adult']; ?></td>
                          <td><?= $row_ex['chwb']; ?></td>
                          <td><?= $row_ex['chwob']; ?></td>
                          <td><?= $row_ex['infant']; ?></td>
                          <td><?= $row_ex['vehicles'] ?></td>
                        </tr>
                      <?php } ?>

                    </tbody>

                  </table>

                </div>

              </div>

            </div>

          </div>
        </section>


      <?php } ?>




      <!-- Inclusion --><!-- Exclusion -->

      <section id="7" class="main_block link_page_section">

        <div class="container">

          <div class="row">

            <div class="col-md-12 in_ex_tab">

              <!-- Nav tabs -->
              <ul class="nav nav-tabs responsive" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Inclusions</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Exclusions</a></li>
                <li role="presentation"><a href="#terms" aria-controls="terms" role="tab" data-toggle="tab">Terms & conditions</a></li>
                <li role="presentation"><a href="#note" aria-controls="note" role="tab" data-toggle="tab">Note</a></li>
                <li role="presentation"><a href="#misc_desc" aria-controls="misc_desc" role="tab" data-toggle="tab">Miscellaneous Description</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content responsive">

                <div role="tabpanel" class="tab-pane active" id="home">
                  <pre><?php echo $sq_quotation['inclusions']; ?></pre>
                </div>

                <div role="tabpanel" class="tab-pane" id="profile">
                  <pre><?php echo $sq_quotation['exclusions']; ?></pre>
                </div>

                <div role="tabpanel" class="tab-pane" id="terms">
                  <pre><?php
                        $sq_tnc_count = mysqli_num_rows(mysqlQuery("select terms_and_conditions from terms_and_conditions where type='Package Quotation' and dest_id='$sq_tours_package[dest_id]' and active_flag='Active'"));
                        if ($sq_tnc_count > 0) {
                          $sq_query = mysqli_fetch_assoc(mysqlQuery("select terms_and_conditions from terms_and_conditions where type='Package Quotation' and dest_id='$sq_tours_package[dest_id]' and active_flag='Active'"));
                        } else {
                          $sq_query = mysqli_fetch_assoc(mysqlQuery("select terms_and_conditions from terms_and_conditions where type='Package Quotation' and dest_id='0' and active_flag='Active'"));
                        }

                        echo $sq_query['terms_and_conditions'] ?></pre>
                </div>
                <div role="tabpanel" class="tab-pane" id="note">
                  <pre><?php echo $sq_tours_package['note']; ?></pre>
                </div>
                <div role="tabpanel" class="tab-pane" id="misc_desc">
                  <pre><?php echo $sq_quotation['other_desc']; ?></pre>
                </div>

              </div>



            </div>

          </div>

        </div>

      </section>



      <!-- Feedback -->
      <?php
      $quotation_id = base64_encode($quotation_id);
      ?>
      <section id="8" class="main_block link_page_section">

        <div class="container">

          <div class="feedback_action text-center">

            <div class="row">

              <div class="col-sm-4 col-xs-12">

                <div class="feedback_btn succes mg_bt_20">

                  <button value="Interested in Booking"><a href="template_mail/quotation_email_interested.php?quotation_id=<?php echo $quotation_id; ?>" style="color:#ffffff;text-decoration:none">I'm Interested</a>

                </div>

              </div>

              <div class="col-sm-4 col-xs-12">

                <div class="feedback_btn danger mg_bt_20">

                  <button value="Interested in Booking"><a href="template_mail/quotation_email_not_interested.php?quotation_id=<?php echo $quotation_id; ?>" style="color:#ffffff;text-decoration:none">Not Interested</a>

                </div>

              </div>

              <div class="col-sm-4 col-xs-12">

                <div class="feedback_btn info mg_bt_20">

                  <button type="button" data-toggle="modal" data-target="#feedback_suggestion" title="Write Suggestion">Give Suggestion</button>

                </div>

              </div>

            </div>

          </div>

        </div>

      </section>



      <!-- Footer -->



      <footer class="main_block">

        <div class="footer_part">

          <div class="container">

            <div class="row">

              <div class="col-md-8 col-sm-6 col-xs-12 mg_bt_10_sm_xs">

                <div class="footer_company_cont">

                  <p><i class="fa fa-map-marker"></i> <?php echo $app_address; ?></p>

                </div>

              </div>

              <div class="col-md-4 col-sm-6 col-xs-12">

                <div class="footer_company_cont text-center text_left_sm_xs">

                  <p><i class="fa fa-phone"></i> <?php echo $app_contact_no; ?></p>

                </div>

              </div>

            </div>

          </div>

        </div>

      </footer>





      <div class="modal fade" id="feedback_suggestion" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-md" role="document">

          <div class="modal-content">

            <div class="modal-header">

              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

              <h4 class="modal-title" id="myModalLabel">Suggestion</h4>

            </div>

            <div class="modal-body">

              <textarea class="form-control" placeholder="*Write Suggestion" id="suggestion" rows="5"></textarea>

              <div class="row mg_tp_20 text-center">

                <button class="btn btn-success" id="btn_quotation_send" onclick="multiple_suggestion_mail('<?php echo $quotation_id; ?>');"><i class="fa fa-paper-plane-o"></i>&nbsp;&nbsp;Send</a></button>

              </div>

            </div>

          </div>

        </div>

      </div>





      <!-- Footer-End-->
      <script src="<?php echo BASE_URL ?>js/jquery-3.1.0.min.js"></script>
      <script src="<?php echo BASE_URL ?>js/jquery-ui.min.js"></script>
      <script src="<?php echo BASE_URL ?>js/bootstrap.min.js"></script>
      <script src="<?php echo BASE_URL ?>js/owl.carousel.min.js"></script>
      <script src="<?php echo BASE_URL ?>js/responsive-tabs.js"></script>

      <script type="text/javascript">
        (function($) {
          fakewaffle.responsiveTabs(['xs', 'sm']);
        })(jQuery);
      </script>

      <script type="text/javascript">
        function multiple_suggestion_mail(quotation_id) {

          var base_url = $('#base_url').val();
          var suggestion = $('#suggestion').val();
          if (suggestion == '') {
            alert('Enter suggestion');
            return false;
          }

          $('#btn_quotation_send').button('loading');
          $.ajax({
            type: 'post',
            url: 'template_mail/suggestion_email_send.php',
            data: {
              quotation_id: quotation_id,
              suggestion: suggestion
            },
            success: function(message) {
              alert(message);
              $('#feedback_suggestion').modal('hide');
              $('#btn_quotation_send').button('reset');
            }
          });
        }
      </script>

      <!-- sticky-header -->

      <script type="text/javascript">
        $(document).ready(function() {



          $(window).bind('scroll', function() {



            var navHeight = 159; // custom nav height



            ($(window).scrollTop() > navHeight) ? $('div.nav').addClass('goToTop'): $('div.nav').removeClass('goToTop');



          });



        });



        // Smooth-scroll -->

        $(document).on('click', '#menu-center a', function(event) {

          event.preventDefault();



          $('html, body').animate({

            scrollTop: $($.attr(this, 'href')).offset().top

          }, 500);

        });



        //Active-menu -->

        $("#menu-center a").click(function() {

          $(this).parent().siblings().removeClass('active');

          $(this).parent().addClass('active');

        });



        // Accordion -->

        $('#myCollapsible').collapse({

          toggle: false

        })



        function display_destination(newurl)

        {

          $.post('display_destination_image.php', {
            newurl: newurl
          }, function(data) {

            $('#div_quotation_form1').html(data);

          });



        }

        function display_gallery(hotel_name)

        {

          $.post('display_hotel_gallery.php', {
            hotel_name: hotel_name
          }, function(data) {

            $('#div_quotation_form').html(data);

          });



        }
      </script>



</body>

</html>
<?php
$date = date('d-m-Y H:i');

$content = '

<tr>
            <table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
              <tr><td style="text-align:left;border: 1px solid #888888;">Name</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['customer_name'] . '</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">Quotation Id</td>   <td style="text-align:left;border: 1px solid #888888;" >' . get_quotation_id(base64_decode($quotation_id1), $year) . '</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">On Datetime</td>   <td style="text-align:left;border: 1px solid #888888;">' . $date . '</td></tr>
              <tr><td style="text-align:left;border: 1px solid #888888;">Mobile Number</td>   <td style="text-align:left;border: 1px solid #888888;">' . $sq_quotation['mobile_no'] . '</td></tr>
            </table>
          </tr>';


$subject = 'Customer viewed quotation! (ID : ' . get_quotation_id(base64_decode($quotation_id1), $year) . ' , ' . $sq_quotation['customer_name'] . ' )';
$model->app_email_send('9', 'Admin', $app_email_id, $content, $subject);


?>