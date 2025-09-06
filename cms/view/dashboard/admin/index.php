<?php
$financial_year_id = $_SESSION['financial_year_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = 'yes';
$enquiry_count = mysqli_fetch_array(mysqlQuery("SELECT COUNT(*) as count FROM enquiry_master WHERE status != 'Disabled' AND financial_year_id = '$financial_year_id'"))['count'];


$converted_count = 0;
$closed_count = 0;
$infollowup_count = 0;
$followup_count = 0;

$closed_count = $converted_count = $followup_count = $infollowup_count = 0;

$query = "
    SELECT eme.followup_status
    FROM enquiry_master em
    JOIN (
        SELECT enquiry_id, MAX(entry_id) AS latest_entry_id
        FROM enquiry_master_entries
        GROUP BY enquiry_id
    ) latest_entry ON em.enquiry_id = latest_entry.enquiry_id
    JOIN enquiry_master_entries eme ON eme.entry_id = latest_entry.latest_entry_id
    WHERE em.status != 'Disabled' AND em.financial_year_id = '$financial_year_id'
";

$result = mysqlQuery($query);
while ($row = mysqli_fetch_assoc($result)) {
    switch ($row['followup_status']) {
        case "Dropped":
            $closed_count++;
            break;
        case "Converted":
            $converted_count++;
            break;
        case "Active":
            $followup_count++;
            break;
        case "In-Followup":
            $infollowup_count++;
            break;
    }
}

?>
<!-- Followup History Div -->
<div id="id_proof1"></div>
<div id="id_proof2"></div>
<div id="payment_summary_html"></div>
<div class="app_panel">
	<div class="dashboard_panel panel-body">

		<!-- Enquiry widgets -->
		<div class="dashboard_enqury_widget_panel main_block mg_bt_25">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="single_enquiry_widget main_block blue_enquiry_widget mg_bt_10_sm_xs" onclick="window.open('<?= BASE_URL ?>view/attractions_offers_enquiry/enquiry/index.php', 'My Window');">
						<div class="col-xs-4 text-left">
							<span class="dashboard-card-icon">
								<i class="fa fa-cubes"></i>
							</span>
						</div>
						<div class="col-xs-8 text-right">
							<span class="single_enquiry_widget_amount dashboard-counter" data-max="<?php echo $enquiry_count; ?>"></span>
						</div>
						<div class="col-sm-12 single_enquiry_widget_amount">
							Total Enquiries
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="single_enquiry_widget main_block yellow_enquiry_widget mg_bt_10_sm_xs" onclick="window.open('<?= BASE_URL ?>view/attractions_offers_enquiry/enquiry/index.php', 'My Window');">
						<div class="row">
							<div class="col-12 col-md-6">
								<div class="col-xs-4 text-left">
									<span class="dashboard-card-icon">
										<i class="fa fa-folder-o"></i>
									</span>
								</div>
								<div class="col-xs-8 text-right">
									<span class="single_enquiry_widget_amount dashboard-counter" data-max="<?php echo $followup_count; ?>"></span>
								</div>
								<div class="col-sm-12 single_enquiry_widget_amount">
									Active
								</div>
							</div>
							<div class="col-12 col-md-6" onclick="window.open('<?= BASE_URL ?>view/attractions_offers_enquiry/enquiry/index.php', 'My Window');">
								<div class="col-xs-4 text-left">
									<span class="dashboard-card-icon">
										<i class="fa fa-folder-open-o"></i>
									</span>
								</div>
								<div class="col-xs-8 text-right">
									<span class="single_enquiry_widget_amount dashboard-counter" data-max="<?php echo $infollowup_count; ?>"></span>
								</div>
								<div class="col-sm-12 single_enquiry_widget_amount" >
									In-Followup
								</div>

							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="single_enquiry_widget main_block green_enquiry_widget" onclick="window.open('<?= BASE_URL ?>view/attractions_offers_enquiry/enquiry/index.php', 'My Window');">
						<div class="col-xs-4 text-left">
							<span class="dashboard-card-icon">
								<i class="fa fa-check-square-o"></i>
							</span>
						</div>
						<div class="col-xs-8 text-right">
							<span class="single_enquiry_widget_amount dashboard-counter" data-max="<?php echo $converted_count; ?>"></span>
						</div>
						<div class="col-sm-12 single_enquiry_widget_amount">
							Converted
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-3">
					<div class="single_enquiry_widget main_block red_enquiry_widget" onclick="window.open('<?= BASE_URL ?>view/attractions_offers_enquiry/enquiry/index.php', 'My Window');">
						<div class="col-xs-4 text-left">
							<span class="dashboard-card-icon">
								<i class="fa fa-trash-o" aria-hidden="true"></i>
							</span>
						</div>
						<div class="col-xs-8 text-right">
							<span class="single_enquiry_widget_amount dashboard-counter" data-max="<?php echo $closed_count; ?>"></span>
						</div>
						<div class="col-sm-12 single_enquiry_widget_amount">
							Dropped Enquiries
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Enquiry widgets End -->

		<!-- Sale and Purchase Summary -->
		<?php
		//Sale
		$total_sale = 0;
		$credit = 0;
		$debit = 0;
		$sq_query = mysqlQuery("select * from ledger_master where group_sub_id in('87','99','17','3','5','93','6')");
		while ($row_query = mysqli_fetch_assoc($sq_query)) {
			$sq_trans_credit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Credit' and gl_id='$row_query[ledger_id]' and row_specification='sales' and financial_year_id='$financial_year_id'"));
			$credit += ($sq_trans_credit['sum'] == "") ? 0 : $sq_trans_credit['sum'];

			$sq_trans_debit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Debit' and gl_id='$row_query[ledger_id]' and row_specification='sales' and financial_year_id='$financial_year_id'"));
			$debit += ($sq_trans_debit['sum'] == "") ? 0 : $sq_trans_debit['sum'];
		}
		if ($debit > $credit) {
			$balance1 =  $debit - $credit;
		} else {
			$balance1 =  $credit - $debit;
		}
		$total_sale = $balance1;

		//Sale Return
		$total_sale_return = 0;
		$credit = 0;
		$debit = 0;
		$sq_query = mysqlQuery("select * from ledger_master where group_sub_id='89'");
		while ($row_query = mysqli_fetch_assoc($sq_query)) {
			$sq_trans_credit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Credit' and gl_id='$row_query[ledger_id]' and financial_year_id='$financial_year_id'"));
			$credit += ($sq_trans_credit['sum'] == "") ? 0 : $sq_trans_credit['sum'];

			$sq_trans_debit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Debit' and gl_id='$row_query[ledger_id]' and financial_year_id='$financial_year_id'"));
			$debit += ($sq_trans_debit['sum'] == "") ? 0 : $sq_trans_debit['sum'];
		}
		if ($debit > $credit) {
			$balance =  $debit - $credit;
		} else {
			$balance =  $credit - $debit;
		}
		$total_sale_return = $balance;
		//sale - sale return(cancel)
		$actual_sale = $total_sale - $total_sale_return;

		//Receipts
		$total_rec = 0;
		$credit1 = 0;
		$debit1 = 0;
		$sq_query = mysqlQuery("select * from ledger_master where group_sub_id in ('24', '47')");
		while ($row_query = mysqli_fetch_assoc($sq_query)) {
			//bank amount
			$sq_trans_credit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Credit' and gl_id='$row_query[ledger_id]' and row_specification in('sales','sale advance') and financial_year_id='$financial_year_id' and clearance_status!='Cancelled'"));
			$credit1 += ($sq_trans_credit['sum'] == "") ? 0 : $sq_trans_credit['sum'];

			$sq_trans_debit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Debit' and gl_id='$row_query[ledger_id]' and row_specification in('sales','sale advance') and financial_year_id='$financial_year_id' and clearance_status!='Cancelled'"));
			$debit1 += ($sq_trans_debit['sum'] == "") ? 0 : $sq_trans_debit['sum'];
		}
		if ($debit1 > $credit1) {
			$balance =  $debit1 - $credit1;
			$total_rec += $balance;
		} else {
			$balance =  $credit1 - $debit1;
			$total_rec -= $balance;
		}

		//cash amount
		$sq_trans_credit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Credit' and gl_id='20' and row_specification in('sales','sale advance') and financial_year_id='$financial_year_id'"));
		$credit = ($sq_trans_credit['sum'] == "") ? 0 : $sq_trans_credit['sum'];

		$sq_trans_debit = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from finance_transaction_master where payment_side='Debit' and gl_id='20' and row_specification in('sales','sale advance') and financial_year_id='$financial_year_id'"));
		$debit = ($sq_trans_debit['sum'] == "") ? 0 : $sq_trans_debit['sum'];

		if ($debit > $credit) {
			$balance =  $debit - $credit;
			$total_rec += $balance;
		} else {
			$balance =  $credit - $debit;
			$total_rec -= $balance;
		}
		?>
		<div class="dashboard_widget_panel main_block mg_bt_25">
			<div class="row">

				<div class="col-md-6">
					<div class="dashboard_widget main_block mg_bt_10_xs dashboard-sale-card dashboard-summary-card">
						<div class="dashboard_widget_title_panel main_block">
							<div class="dashboard_widget_icon">
								<i class="fa fa-tag" aria-hidden="true"></i>
							</div>
							<div class="dashboard_widget_title_text" onclick="window.open('<?= BASE_URL ?>view/reports/reports_homepage.php', 'My Window');">
								<h3> Sale Summary</a></h3>
							</div>
						</div>
						<div class="dashboard_widget_conetent_panel main_block">
							<div class="col-sm-6">
								<div class="dashboard_widget_single_conetent">
									<div class="dashboard-summary-amount">
										<span class="dashboard-summary-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
										<span class="dashboard_widget_conetent_amount"><?php echo number_format($actual_sale, 2); ?></span>
									</div>
									<span class="dashboard_widget_conetent_text">Total Sale</span>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="dashboard_widget_single_conetent">
									<div class="dashboard-summary-amount">
										<span class="dashboard-summary-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
										<span class="dashboard_widget_conetent_amount"><?php echo number_format($total_rec, 2); ?></span>
									</div>
									<span class="dashboard_widget_conetent_text ">Total Received</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php
				$sq_puchase1 = mysqlQuery("select * from vendor_estimate where financial_year_id='$financial_year_id' and delete_status='0' and status!='Cancel' ");
				$actual_purchase = 0;
				while($sq_puchase = mysqli_fetch_assoc($sq_puchase1)){

					if($sq_puchase['purchase_return'] == 0){
						$actual_purchase += $sq_puchase['net_total'];
					}
					else if($sq_puchase['purchase_return'] == 2){
						$cancel_estimate = json_decode($sq_puchase['cancel_estimate']);
						$p_purchase = ($sq_puchase['net_total'] - floatval($cancel_estimate[0]->net_total));
						$actual_purchase += $p_purchase;
					}
				}
				$sq_puchase_p = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as payment_amount from vendor_payment_master where financial_year_id='$financial_year_id' and clearance_status!='Cancelled'"));
				$sq_puchase_r = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as payment_amount from vendor_refund_master where financial_year_id='$financial_year_id' and clearance_status!='Cancelled'"));
				$total_rec = $sq_puchase_p['payment_amount'] - $sq_puchase_r['payment_amount'];
				?>
				<div class="col-md-6">
					<div class="dashboard_widget main_block dashboard-purchase-card dashboard-summary-card">
						<div class="dashboard_widget_title_panel main_block">
							<div class="dashboard_widget_icon">
								<i class="fa fa-shopping-cart" aria-hidden="true"></i>
							</div>
							<div class="dashboard_widget_title_text" onclick="window.open('<?= BASE_URL ?>view/vendor/dashboard/index.php', 'My Window');">
								<h3>Purchase Summary</a></h3>
							</div>
						</div>
						<div class="dashboard_widget_conetent_panel main_block">
							<div class="col-sm-6">
								<div class="dashboard_widget_single_conetent">
									<div class="dashboard-summary-amount">
										<span class="dashboard-summary-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
										<span class="dashboard_widget_conetent_amount"><?php echo number_format($actual_purchase, 2); ?></span>
									</div>
									<span class="dashboard_widget_conetent_text ">Total Purchase</span>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="dashboard_widget_single_conetent">
									<div class="dashboard-summary-amount">
										<span class="dashboard-summary-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
										<span class="dashboard_widget_conetent_amount"><?php echo number_format($total_rec, 2); ?></span>
									</div>
									<span class="dashboard_widget_conetent_text ">Total Paid</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Sale and Purchase Summary End-->
		<!-- dashboard_tab -->
		<div class="row">
			<div class="col-md-12">
				<div class="dashboard_tab text-center main_block">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs responsive" role="tablist">
						<li role="presentation" class="active"><a href="#enquiry_tab" aria-controls="enquiry_tab" role="tab" data-toggle="tab">Followups</a></li>
						<li role="presentation"><a href="#oncoming_tab" aria-controls="oncoming_tab" role="tab" data-toggle="tab">Tour Summary</a></li>
						<li role="presentation"><a href="#itinerary_tab" aria-controls="itinerary_tab" role="tab" data-toggle="tab">Tour Itinerary</a></li>
						<li role="presentation"><a href="#reminder_tab" aria-controls="reminder_tab" role="tab" data-toggle="tab">Reminders</a></li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content responsive main_block mg_bt_150">

						<!-- reminders tab  -->
						<div role="tabpanel" class="tab-pane" id="reminder_tab">
							<div class="row">
								<div class="col-md-12 col-sm-12 mg_bt_10"></div>
								<div class="col-md-2 col-sm-6 mg_bt_10" style="margin-left:1000px !important;">
									<select id="reminder_option" name="reminder_option" title="Reminder Type" onchange="get_reminders(this.value);">
										<option value="Payment">Payment</option>
										<option value="Common">Common</option>
									</select>
								</div>
							</div>
							<div id='reminders_data'></div>
						</div>
						<!-- reminders summary End -->

						<!-- Ongoing  -->
						<div role="tabpanel" class="tab-pane" id="oncoming_tab">
							<div class="row">
								<div class="col-md-12 col-sm-12 mg_bt_10"></div>
								<div class="col-md-2 col-sm-6 mg_bt_10" style="margin-left:720px !important;">
									<input type="text" id="tfrom_date_filter" name="tfrom_date_filter" placeholder="Travel From Date" title="Travel From Date" onchange="get_to_date(this.id,'tto_date_filter')">
								</div>
								<div class="col-md-2 col-sm-6 mg_bt_10">
									<input type="text" id="tto_date_filter" name="tto_date_filter" placeholder="Travel To Date" title="Travel To Date" onchange="validate_validDate('tfrom_date_filter','tto_date_filter')">
								</div>
								<div class="col-md-1 text-left col-sm-6 mg_bt_10">
									<button class="btn btn-excel btn-sm" onclick="ongoing_tours_reflect()" data-toggle="tooltip" title="" data-original-title="Proceed"><i class="fa fa-arrow-right"></i></button>
								</div>
							</div>
							<div id='ongoing_tours_data'></div>
						</div>
						<!-- Ongoing Tours summary End -->

						<!-- itinerary tab  -->

						<div role="tabpanel" class="tab-pane" id="itinerary_tab">
							<div class="row">
								<div class="col-md-12 col-sm-12 mg_bt_10"></div>
								<div class="col-md-12 text-right">
									<div class="form-inline">
										<div class="form-group">
											<input type="text" id="itinerary_from_date_filter" name="itinerary_from_date_filter" class="form-control" placeholder="*Date" title="Date" value="<?= date('d-m-Y') ?>">
										</div>
										<button class="btn btn-excel btn-sm ml-2" onclick="itinerary_reflect()" data-toggle="tooltip" title="Proceed">
											<i class="fa fa-arrow-right"></i>
										</button>
									</div>
								</div>
							</div>
							<div id="itinerary_data"></div>
						</div>

						<!-- itinerary summary End -->

						<!--  FIT Summary -->
						<div role="tabpanel" class="tab-pane" id="fit_tab">
							<?php
							$count = 0;
							$bg = '';
							$query = mysqli_fetch_assoc(mysqlQuery("select max(booking_id) as booking_id from package_tour_booking_master where 1"));
							$sq_entry = mysqlQuery("select * from package_travelers_details where booking_id='$query[booking_id]'");
							?>
							<div class="dashboard_table dashboard_table_panel main_block mg_bt_25">
								<div class="row text-left">
									<div class="">
										<div class="dashboard_table_heading main_block">
											<div class="col-md-2">
												<h3>Package Tours</h3>
											</div>
											<div class="col-md-3 col-sm-4 col-md-push-7">
												<select style="border-color: #009898; width: 100%;" id="package_booking_id" onchange="package_list_reflect(this.id)">
													<?php
													$query = "select * from package_tour_booking_master where 1 and delete_status='0' and financial_year_id='$financial_year_id'";
													include "../../../model/app_settings/branchwise_filteration.php";
													$query .= " order by booking_id desc";
													$sq_booking = mysqlQuery($query);
													while ($row_booking = mysqli_fetch_assoc($sq_booking)) {
														$date = $row_booking['booking_date'];
														$yr = explode("-", $date);
														$year = $yr[0];
														$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
														if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
													?>
															<option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'], $year) . "-" . " " . $sq_customer['company_name']; ?></option>
														<?php } else { ?>
															<option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'], $year) . "-" . " " . $sq_customer['first_name'] . " " . $sq_customer['last_name']; ?></option>
													<?php
														}
													} ?>
												</select>
											</div>
											<div id="package_div_list">
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
						<!--  FIT Summary End -->
						<!--  GIT Summary -->
						<div role="tabpanel" class="tab-pane" id="git_tab">
							<?php
							$count = 0;
							$bg = '';
							$query = mysqli_fetch_assoc(mysqlQuery("select max(id) as booking_id from tourwise_traveler_details"));
							$sq_package = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$query[booking_id]' and financial_year_id='$financial_year_id' and delete_status='0'"));
							$sq_tour_name = mysqli_fetch_assoc(mysqlQuery("select  * from tour_master where tour_id = '$sq_package[tour_id]'"));
							$sq_traveler_personal_info = mysqli_fetch_assoc(mysqlQuery("select * from traveler_personal_info where tourwise_traveler_id='$query[booking_id]'"));
							?>
							<div class="dashboard_table dashboard_table_panel main_block mg_bt_25">
								<div class="row text-left">
									<div class="">
										<div class="dashboard_table_heading main_block">
											<div class="col-md-2">
												<h3>Group Tours</h3>
											</div>
											<div class="col-md-3 col-sm-4 col-md-push-7">
												<select style="border-color: #009898; width: 100%;" id="group_booking_id" onchange="group_list_reflect(this.id)">
													<?php
													$query = "select * from tourwise_traveler_details where 1 and financial_year_id='$financial_year_id' and delete_status='0'";
													include "../../../model/app_settings/branchwise_filteration.php";
													$query .= " order by id desc";
													$sq_booking = mysqlQuery($query);
													while ($row_booking = mysqli_fetch_assoc($sq_booking)) {

														$date = $row_booking['form_date'];
														$yr = explode("-", $date);
														$year = $yr[0];

														$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
														if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
													?>
															<option value="<?php echo $row_booking['id'] ?>"><?php echo get_group_booking_id($row_booking['id'], $year) . "-" . " " . $sq_customer['company_name']; ?></option>
														<?php } else { ?>

															<option value="<?= $row_booking['id'] ?>"><?= get_group_booking_id($row_booking['id'], $year) ?> : <?= $sq_customer['first_name'] . ' ' . $sq_customer['last_name'] ?></option>
													<?php
														}
													} ?>
												</select>
											</div>
											<div id="group_div_list">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!--  GIT Summary End -->
						<!-- Enquiry & Followup summary -->
						<div role="tabpanel" class="tab-pane active" id="enquiry_tab">
							<!-- <div class="dashboard_table dashboard_table_panel main_block"> -->
							
  						<div class="row text-right">
							<div class="col-md-5 text-left">
							</div>
	
							<div class="col-md-2 col-sm-6 mg_bt_10 text-left">
						
							</div>
						<!-- </div>
						</div> -->

									<div class="col-md-2 col-sm-6 mg_bt_10">
										<input type="text" id="followup_from_date_filter" name="followup_from_date_filter" style="width:200px;" placeholder="Followup From D/T" title="Followup From D/T" onchange="get_to_datetime(this.id,'followup_to_date_filter')">
									</div>
									<div class="col-md-2 col-sm-6 mg_bt_10">
										<input type="text" id="followup_to_date_filter" name="followup_to_date_filter" placeholder="Followup To D/T" title="Followup To D/T" onchange="validate_validDatetime('followup_from_date_filter','followup_to_date_filter')">
									</div>
									<div class="col-md-1 text-left col-sm-6 mg_bt_10">
										<button class="btn btn-excel btn-sm" id="followup_reflect1" onclick="followup_reflect()" data-toggle="tooltip" title="" data-original-title="Proceed"><i class="fa fa-arrow-right"></i></button>
									</div>
								</div>
									<div id='followup_data'></div>
							<!-- </div> -->
						</div>
					</div>
					<!-- Enquiry & Followup summary End -->
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<!-- ====================================================== Row 6 end ============================================================= -->

<script type="text/javascript">
	$('#group_booking_id,#package_booking_id').select2();
	$('#tfrom_date_filter,#tto_date_filter,#itinerary_from_date_filter').datetimepicker({
		format: 'd-m-Y',
		timepicker:false
	});
	$('#followup_from_date_filter, #followup_to_date_filter').datetimepicker({
		format: 'd-m-Y H:i'
	});
	followup_reflect();
	function followup_reflect(page = 1) {
		var from_date = $('#followup_from_date_filter').val();
		var to_date = $('#followup_to_date_filter').val();
		

		$.post('admin/followup_list_reflect.php', {
			from_date: from_date,
			to_date: to_date,
			page: page
		}, function(data) {
			$('#followup_data').html(data);
		});
	}

	itinerary_reflect();
	function itinerary_reflect() {
		var from_date = $('#itinerary_from_date_filter').val();
		$.post('itinerary/index.php', { date: from_date }, function(data) {
			$('#itinerary_data').html(data);
		});
	}
	ongoing_tours_reflect();
	function ongoing_tours_reflect() {

		var from_date = $('#tfrom_date_filter').val();
		var to_date = $('#tto_date_filter').val();
		$.post('../dashboard/tour_summary.php', {from_date: from_date, to_date: to_date }, function(data) {
			$('#ongoing_tours_data').html(data);
		});
	}

	function send_sms(id, tour_type, emp_id, contact_no, name) {

		var base_url = $('#base_url').val();
		var draft = "Dear " + name + ",We hope that you are enjoying your trip. It will be a great source of input from you, if you can share your tour feedback with us, so that we can serve you even better.Thank you."
		$('#send_btn').button('loading');
		$.ajax({
			type: 'post',
			url: base_url + 'controller/dashboard_sms_send.php',
			data: {
				draft: draft,
				enquiry_id: id,
				mobile_no: contact_no
			},
			success: function(message) {
				msg_alert("Feedback sent successfully");
				$('#send_btn').button('reset');
			}
		});
		console.log(contact_no);
		web_whatsapp_open(contact_no, name);
	}

	function web_whatsapp_open(mobile_no, name) {

		var app_name = $('#app_name').val();
		var app_contact_no = $('#app_contact_no').val();
		var link = 'https://web.whatsapp.com/send?phone=' + mobile_no + '&text=Dear%20' + encodeURI(name) + ',%0aWe%20hope%20that%20you%20are%20enjoying%20your%20trip.%20It%20will%20be%20a%20great%20source%20of%20input%20from%20you,%20if%20you%20can%20share%20your%20tour%20feedback%20with%20us,%20so%20that%20we%20can%20serve%20you%20even%20better.%0aThank%20you,%0a'+app_name+' ';
    	link += encodeURIComponent(app_contact_no);
		window.open(link);
	}

	function whatsapp_wishes(number, name) {
		
		var app_name = $('#app_name').val();
		var app_contact_no = $('#app_contact_no').val();
		var msg = encodeURI("Dear " + name + ",\nMay this trip turns out to be a wonderful treat for you and may you create beautiful memories throughout this trip to cherish forever. Wish you a very happy and safe journey!!\nThank you,\n"+app_name+" ");
    	msg += encodeURIComponent(app_contact_no);
		window.open('https://web.whatsapp.com/send?phone=' + number + '&text=' + msg);
	}
	function view_payment_summary(count, booking_id, tour_type){
		
		$('#payment-' + count).prop('disabled',true);
		$('#payment-' + count).button('loading');
		$.post('../dashboard//view_payment_smmary.php', {
			count: count,
			booking_id: booking_id,
			tour_type: tour_type
		}, function(data) {
			$('#payment-' + count).prop('disabled',false);
			$('#payment-' + count).button('reset');
			$('#payment_summary_html').html(data);
		});
	}
	function checklist_update(count, booking_id, tour_type, aemp_id) {
		$('#checklist-' + count).button('loading');
		$.post('admin/update_checklist.php', {
			booking_id: booking_id,
			tour_type: tour_type,
			aemp_id: aemp_id
		}, function(data) {
			$('#checklist-' + count).button('reset');
			$('#id_proof2').html(data);
		});
	}

	function package_list_reflect() {
		var booking_id = $('#package_booking_id').val();
		$.post('admin/package_list_reflect.php', {
			booking_id: booking_id
		}, function(data) {
			$('#package_div_list').html(data);
		});
	}
	package_list_reflect();

	function group_list_reflect() {
		var booking_id = $('#group_booking_id').val();
		$.post('admin/group_list_reflect.php', {
			booking_id: booking_id
		}, function(data) {
			$('#group_div_list').html(data);
		});

	}
	group_list_reflect();

	function display_history(enquiry_id) {

		$('#history-' + enquiry_id).button('loading');
		$.post('admin/followup_history.php', {
			enquiry_id: enquiry_id
		}, function(data) {
			$('#id_proof1').html(data);
			$('#history-' + enquiry_id).button('reset');
		});
	}

	function Followup_update(enquiry_id) {
		$('#update-' + enquiry_id).button('loading');
		$.post('admin/followup_update.php', {
			enquiry_id: enquiry_id
		}, function(data) {
			$('#id_proof2').html(data);
			$('#update-' + enquiry_id).button('reset');
		});
	}

	function followup_type_reflect(followup_status) {
		$.post('admin/followup_type_reflect.php', {
			followup_status: followup_status
		}, function(data) {
			$('#followup_type').html(data);
		});
	}
	function get_reminders(type){

		if(type == 'Payment'){
			var url ='payment_index.php';
		}else{
			var url ='common_index.php';
		}
		$.post('../dashboard/reminders/'+url, { }, function(data) {
			$('#reminders_data').html(data);
		});
	}
	get_reminders('Payment');
</script>

<script type="text/javascript">
	(function($) {
		fakewaffle.responsiveTabs(['xs', 'sm']);
	})(jQuery);
</script>

<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<script src="js/admin_dashboard.js"></script>