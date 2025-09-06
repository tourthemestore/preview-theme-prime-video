<?php
include "../../../../../model/model.php";

$booking_id = $_POST['booking_id'];
?>
<input type="hidden" id="b_id" value="<?= $booking_id ?>">
<div class="row mg_tp_20">
	<div class="col-md-10  col-md-offset-1 col-sm-12 col-xs-12">
		<div class="widget_parent-bg-img bg-img-red">
			<?php
			$sq_b2c = mysqli_fetch_assoc(mysqlQuery("select * from b2c_sale where booking_id='$booking_id'"));
			$costing_data = json_decode($sq_b2c['costing_data']);
			$total_cost = $costing_data[0]->total_cost;
			$total_tax = $costing_data[0]->total_tax;
			$taxes = explode(',', $total_tax);
			$tax_amount = 0;
			for ($i = 0; $i < sizeof($taxes); $i++) {
				$single_tax = explode(':', $taxes[$i]);
				$tax_amount += (float)($single_tax[1]);
				$temp_tax = explode(' ', $single_tax[1]);
			}
			$grand_total = $costing_data[0]->grand_total;
			$coupon_amount = $costing_data[0]->coupon_amount;
			$coupon_amount = ($coupon_amount != '') ? $coupon_amount : 0;
			$final_total = $costing_data[0]->net_total;

			$sq_payment_info = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum from b2c_payment_master where booking_id='$booking_id' and clearance_status!='Pending' and clearance_status!='Cancelled'"));

			begin_widget();
			$title_arr = array("Basic Amount", "Tax", "Coupon Amount", "Total Amount", "Paid Amount");
			$content_arr = array(number_format($total_cost, 2), number_format($tax_amount, 2), number_format($coupon_amount, 2), number_format($final_total, 2), number_format($sq_payment_info['sum'], 2));
			$percent = ($final_total != '0') ? ($sq_payment_info['sum'] / $final_total) * 100 : 0;
			$percent = number_format($percent, 2);
			$label = "B2C Fee Paid In Percent";
			widget_element($title_arr, $content_arr, $percent, $label);
			end_widget();
			?>
			<input type="hidden" id="total_sale" name="total_sale" value="<?= $final_total ?>">
			<input type="hidden" id="total_paid" name="total_paid" value="<?= $sq_payment_info['sum'] ?>">
			<input type="hidden" id="total_tax" name="total_tax" value="<?= $tax_amount ?>">
		</div>
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12 col-md-offset-5 col-sm-8 col-sm-offset-4 col-xs-12">
		<?php
		if ($sq_b2c['status'] != 'Cancel') { ?>
			<button class="btn btn-danger btn-sm ico_left" onclick="cancel_booking()"><i class="fa fa-times"></i>&nbsp;&nbsp;Cancel Booking</button>
	</div>
<?php } ?>
<input type="hidden" name="tax_type" id="tax_type" value="<?= $total_tax ?>">
</div>
<hr>
<?php
$sq_cancel_count = mysqli_num_rows(mysqlQuery("select * from b2c_sale where booking_id='$booking_id' and status='Cancel'"));
if ($sq_cancel_count > 0) {
	$sq_b2c_info = mysqli_fetch_assoc(mysqlQuery("select * from b2c_sale where booking_id='$booking_id'"));
	if ($sq_b2c_info['estimate_flag'] == "0") {
		$refund_amount = $sq_payment_info['sum'];
	} else {
		$refund_amount = $sq_b2c_info['total_refund_amount'];
	}
?>
	<form id="frm_refund" class="mg_bt_150">

		<div class="row text-center">
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
				<input type="number" name="cancel_amount" id="cancel_amount" class="text-right" placeholder="*Cancel amount(Tax Incl)" title="Cancel amount(Tax Incl)" onchange="validate_balance(this.id);calculate_total_refund()" value="<?= $sq_b2c_info['cancel_amount'] ?>">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
				<select title="Select Tax" id="tax_value" name="tax_value" class="form-control" onchange="calculate_total_refund();">
					<?php
					if ($sq_b2c_info['estimate_flag'] == 0) { ?>
						<option value="">*Select Tax</option>
						<?php get_tax_dropdown('Income') ?>
					<?php } else {
					?>
						<option value="<?= $sq_b2c_info['tax_value'] ?>"><?= $sq_b2c_info['tax_value'] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
				<input type="text" title="Tax Subtotal" id="tour_service_tax_subtotal" name="tour_service_tax_subtotal" value="<?= $sq_b2c_info['tax_amount'] ?>" readonly>
				<input type="hidden" id="ledger_posting" />
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
				<input type="number" name="cancel_amount_exc" id="cancel_amount_exc" class="text-right" placeholder="*Cancellation Charges" title="Cancellation Charges" onchange="validate_balance(this.id);calculate_total_refund()" value="<?= $sq_b2c_info['cancel_amount_exc'] ?>">
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12 mg_tp_10">
				<input type="number" name="total_refund_amount" id="total_refund_amount" class="amount_feild_highlight text-right" placeholder="Total Refund" title="Total Refund" readonly value="<?= $refund_amount ?>">
			</div>
		</div>
		<?php if ($sq_b2c_info['estimate_flag'] == "0") { ?>
			<div class="row mg_tp_20">
				<div class="col-md-4 col-md-offset-4 text-center">
					<button id="btn_refund_save" id="cancel_booking" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
				</div>
			</div>
		<?php } ?>

	</form>
<?php
}
?>
<script>
	function cancel_booking() {
		//Validaion to select complete tour cancellation 
		var booking_id = $('#b_id').val();
		$('#vi_confirm_box').vi_confirm_box({
			message: 'Are you sure?',
			callback: function(data1) {
				if (data1 == "yes") {
					var base_url = $('#base_url').val();
					$('#cancel_booking').button('loading');
					$.post(base_url + 'controller/b2c_settings/b2c/cancel/cancel_booking.php', {
						booking_id: booking_id
					}, function(data) {
						msg_alert(data);
						$('#cancel_booking').button('reset');
						content_reflect();
					});
				}
			}
		});
	}

	function calculate_total_refund() {
		var total_refund_amount = 0;
		var applied_taxes = '';
		var ledger_posting = '';
		var cancel_amount = $('#cancel_amount').val();
		var total_sale = $('#total_sale').val();
		var total_paid = $('#total_paid').val();
		var tax_value = $('#tax_value').val();

		if (cancel_amount == "") {
			cancel_amount = 0;
		}
		if (total_paid == "") {
			total_paid = 0;
		}

		if (parseFloat(cancel_amount) > parseFloat(total_sale)) {
			error_msg_alert("Cancel amount can not be greater than Sale amount");
		}

		if (tax_value != "") {
			var service_tax_subtotal1 = tax_value.split("+");
			for (var i = 0; i < service_tax_subtotal1.length; i++) {
				var service_tax_string = service_tax_subtotal1[i].split(':');
				if (parseInt(service_tax_string.length) > 0) {
					var service_tax_string1 = service_tax_string[1] && service_tax_string[1].split('%');
					service_tax_string1[0] = service_tax_string1[0] && service_tax_string1[0].replace('(', '');
					service_tax = service_tax_string1[0];
				}

				service_tax_string[2] = service_tax_string[2].replace('(', '');
				service_tax_string[2] = service_tax_string[2].replace(')', '');
				service_tax_amount = ((parseFloat(cancel_amount) * parseFloat(service_tax)) / 100).toFixed(2);
				if (applied_taxes == '') {
					applied_taxes = service_tax_string[0] + ':' + service_tax_string[1] + ':' + service_tax_amount;
					ledger_posting = service_tax_string[2];
				} else {
					applied_taxes += ', ' + service_tax_string[0] + ':' + service_tax_string[1] + ':' + service_tax_amount;
					ledger_posting += ', ' + service_tax_string[2];
				}
			}
		}
		$('#tour_service_tax_subtotal').val(applied_taxes);
		var service_tax_subtotal = $('#tour_service_tax_subtotal').val();
		if (service_tax_subtotal == "") {
			service_tax_subtotal = '';
		}
		var service_tax_amount = 0;
		if (parseFloat(service_tax_subtotal) !== 0.0 && service_tax_subtotal !== '') {
			var service_tax_subtotal1 = service_tax_subtotal.split(',');
			for (var i = 0; i < service_tax_subtotal1.length; i++) {
				var service_tax = service_tax_subtotal1[i].split(':');
				service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
			}
		}

		var cancel_amount_exc = parseFloat(cancel_amount) - parseFloat(service_tax_amount);
		var total_refund_amount = parseFloat(total_paid) - parseFloat(cancel_amount);

		if (parseFloat(total_refund_amount) < 0) {
			total_refund_amount = 0;
		}
		$('#cancel_amount_exc').val(cancel_amount_exc);
		$('#ledger_posting').val(ledger_posting);
		$('#total_refund_amount').val(total_refund_amount.toFixed(2));
	}

	$(function() {
		$('#frm_refund').validate({
			rules: {
				refund_amount: {
					required: true,
					number: true
				},
				cancel_amount: {
					required: true,
					number: true
				},
				total_refund_amount: {
					required: true,
					number: true
				},
				tax_value: {
					required: true
				}
			},
			submitHandler: function(form) {

				var booking_id = $('#b_id').val();
				var cancel_amount = $('#cancel_amount').val();
				var total_refund_amount = $('#total_refund_amount').val();
				var total_sale = $('#total_sale').val();
				var total_paid = $('#total_paid').val();
				var total_tax = $('#total_tax').val();
				var tax_value = $('#tax_value').val();
				var tour_service_tax_subtotal = $('#tour_service_tax_subtotal').val();
				var cancel_amount_exc = $('#cancel_amount_exc').val();
				var ledger_posting = $('#ledger_posting').val();

				if (parseFloat(cancel_amount) > parseFloat(total_sale)) {
					error_msg_alert("Cancel amount can not be greater than Sale amount");
					return false;
				}

				var base_url = $('#base_url').val();

				$('#vi_confirm_box').vi_confirm_box({
					message: 'Are you sure?',
					callback: function(data1) {
						if (data1 == "yes") {

							$('#btn_refund_save').button('loading');

							$.ajax({
								type: 'post',
								url: base_url + 'controller/b2c_settings/b2c/cancel/refund_estimate.php',
								data: {
									booking_id: booking_id,
									cancel_amount: cancel_amount,
									total_refund_amount: total_refund_amount,
									final_total: total_sale,
									main_tax_total: total_tax,
									tax_value: tax_value,
									tour_service_tax_subtotal: tour_service_tax_subtotal,
									cancel_amount_exc: cancel_amount_exc,
									ledger_posting: ledger_posting
								},
								success: function(result) {
									msg_alert(result);
									content_reflect();
									$('#btn_refund_save').button('reset');
								},
								error: function(result) {
									console.log(result.responseText);
								}
							});

						}
					}
				});

			}
		});
	});
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>