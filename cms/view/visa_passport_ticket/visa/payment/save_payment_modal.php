<?php
include "../../../../model/model.php";
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='visa_passport_ticket/visa/index.php'"));
$branch_status = $sq['branch_status'];
?>
<input type="hidden" id="branch_admin_id1" name="branch_admin_id1" value="<?= $branch_admin_id ?>">
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">

<div class="modal fade" id="visa_payment_save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Receipt</h4>
      </div>
      <div class="modal-body">
        <form id="frm_visa_payment_save">
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
              <select name="visa_id" id="visa_id" style="width:100%" title="Booking ID" onchange="get_outstanding('visa',this.id);">

                <option value="">*Select Booking ID</option>
                <?php
                $query = "select * from visa_master where 1 and delete_status='0' ";
                if ($customer_id != '') {
                  $query .= " and customer_id='$customer_id'";
                }
                include "../../../../model/app_settings/branchwise_filteration.php";
                $query .= " order by visa_id desc";
                $sq_visa = mysqlQuery($query);
                while ($row_visa = mysqli_fetch_assoc($sq_visa)) {
                  $sq_entries = mysqli_num_rows(mysqlQuery("select * from visa_master_entries where visa_id ='$row_visa[visa_id]'"));
                  $sq_entries_cancel = mysqli_num_rows(mysqlQuery("select * from visa_master_entries where visa_id ='$row_visa[visa_id]' and status='Cancel'"));
                  $booking_date = $row_visa['created_at'];
                  $yr = explode("-", $booking_date);
                  $year = $yr[0];
                  $status = '';
                  $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_visa[customer_id]'"));
                  $cust_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . " " . $sq_customer['last_name'];
                  if ($sq_entries == $sq_entries_cancel) {
                    $status = '(Cancelled)';
                    $sq_payment_total = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from visa_payment_master where visa_id='$row_visa[visa_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
                    $paid_amount = $sq_payment_total['sum'];
                    $canc_amount = $row_visa['cancel_amount'];
                    $balance = ($paid_amount > $canc_amount) ? 0 : (float)($canc_amount) - (float)($paid_amount);
                    if ($balance <= 0) continue;
                  }
                ?>
                  <option value="<?= $row_visa['visa_id'] ?>"><?= get_visa_booking_id($row_visa['visa_id'], $year) . ': ' . $cust_name . ' ' . $status ?></option>
                <?php
                }
                ?>
              </select>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
              <input type="text" id="payment_date" name="payment_date" class="form-control" placeholder="*Date" title="Date" value="<?= date('d-m-Y') ?>" onchange="check_valid_date(this.id)">
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
              <select name="payment_mode" id="payment_mode" class="form-control" title="Mode" onchange="payment_master_toggles(this.id, 'bank_name', 'transaction_id', 'bank_id');get_identifier_block('identifier','payment_mode','credit_card_details','credit_charges');get_credit_card_charges('identifier','payment_mode','payment_amount','credit_card_details','credit_charges')">
                <?php get_payment_mode_dropdown(); ?>
              </select>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
              <input type="text" id="payment_amount" name="payment_amount" class="form-control" placeholder="*Amount" title="Amount" onchange="validate_balance(this.id);payment_amount_validate(this.id,'payment_mode','transaction_id','bank_name','bank_id');get_credit_card_charges('identifier','payment_mode','payment_amount','credit_card_details','credit_charges');">
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
              <input class="hidden form-control" type="text" id="credit_charges" name="credit_charges" title="Credit card charges" disabled>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
              <select class="hidden form-control" id="identifier" onchange="get_credit_card_data('identifier','payment_mode','credit_card_details')" title="Identifier(4 digit)" required>
                <option value=''>*Select Identifier</option>
              </select>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
              <input class="hidden form-control" type="text" id="credit_card_details" name="credit_card_details" title="Credit card details" disabled>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
              <input type="text" id="bank_name" name="bank_name" placeholder="Bank Name" title="Bank Name" class="bank_suggest form-control" readonly>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
              <input type="number" id="transaction_id" name="transaction_id" onchange="validate_specialChar(this.id)" class="form-control" placeholder="Cheque No/ID" title="Cheque No/ID" disabled>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <select name="bank_id" id="bank_id" title="Creditor Bank" class="form-control" disabled>
                <?php get_bank_dropdown(); ?>
              </select>
            </div>
             <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                            <select name="currency_code" id="currency_code1" title="Currency" style="width:100%" data-toggle="tooltip" required>
                                <?php
                                $sq_app_setting = mysqli_fetch_assoc(mysqlQuery("select currency from app_settings"));
                                if ($sq_app_setting['currency'] != '0') {

                                    $sq_currencyd = mysqli_fetch_assoc(mysqlQuery("SELECT `id`,`currency_code` FROM `currency_name_master` WHERE id=" . $sq_app_setting['currency']));
                                ?>
                                    <option value="<?= $sq_currencyd['id'] ?>"><?= $sq_currencyd['currency_code'] ?>
                                    </option>
                                <?php } ?>
                                <option value=''>*Select Currency</option>
                                <?php
                                $sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
                                while ($row_currency = mysqli_fetch_assoc($sq_currency)) {
                                ?>
                                    <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
          </div>
          <div class="row mg_tp_10">
            <div class="col-md-3 col-sm-3">
              <input type="text" id="outstanding" name="outstanding" class="form-control" placeholder="Outstanding" title="Outstanding" readonly />
              <input type="hidden" id="canc_status" name="canc_status" class="form-control" />
            </div>
            <div class="col-md-9 col-sm-9">
              <span style="color: red;line-height: 35px;" data-original-title="" title="" class="note"><?= $txn_feild_note ?></span>
            </div>
          </div>
          <div class="row text-center mg_tp_20">
            <div class="col-xs-12">
              <button id="btn_save_payment" class="btn btn-sm btn-success"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $('#visa_id,#currency_code1').select2();
  $('#visa_payment_save_modal').modal('show');
  $('#payment_date').datetimepicker({
    timepicker: false,
    format: 'd-m-Y'
  });

  $(function() {

    $('#frm_visa_payment_save').validate({
      rules: {
        visa_id: {
          required: true
        },
        payment_date: {
          required: true
        },
        payment_amount: {
          required: true,
          number: true
        },
        payment_mode: {
          required: true
        },
        bank_id: {
          required: function() {
            if ($('#payment_mode').val() != "Cash") {
              return true;
            } else {
              return false;
            }
          }
        },
      },
      submitHandler: function(form) {
        $('#btn_save_payment').prop('disabled', true);
        var visa_id = $('#visa_id').val();
        var payment_date = $('#payment_date').val();
        var payment_amount = $('#payment_amount').val();
        var payment_mode = $('#payment_mode').val();
        var bank_name = $('#bank_name').val();
        var transaction_id = $('#transaction_id').val();
        var bank_id = $('#bank_id').val();
        var branch_admin_id = $('#branch_admin_id1').val();
        var credit_charges = $('#credit_charges').val();
        var credit_card_details = $('#credit_card_details').val();
        var outstanding = $('#outstanding').val();
        var canc_status = $('#canc_status').val();

        var currency_code = $('#currency_code1').val();

        var base_url = $('#base_url').val();
        if (payment_mode == "Credit Note" || payment_mode == "Advance") {
          error_msg_alert("Please select another payment mode.");
          $('#btn_save_payment').prop('disabled', false);
          return false;
        }
        if (parseFloat(payment_amount) > parseFloat(outstanding)) {
          error_msg_alert("Payment amount cannot be greater than outstanding amount.");
          $('#btn_save_payment').prop('disabled', false);
          return false;
        }
        //Validation for booking and payment date in login financial year
        var check_date1 = $('#payment_date').val();
        $.post(base_url + 'view/load_data/finance_date_validation.php', {
          check_date: check_date1
        }, function(data) {
          if (data !== 'valid') {
            error_msg_alert("The Payment date does not match between selected Financial year.");
            $('#btn_save_payment').prop('disabled', false);
            return false;
          } else {
            $('#btn_save_payment').button('loading');

            $.ajax({
              type: 'post',
              url: base_url + 'controller/visa_passport_ticket/visa/visa_master_payment_save.php',
              data: {
                visa_id: visa_id,
                payment_date: payment_date,
                payment_amount: payment_amount,
                payment_mode: payment_mode,
                bank_name: bank_name,
                transaction_id: transaction_id,
                bank_id: bank_id,
                branch_admin_id: branch_admin_id,
                credit_charges: credit_charges,
                credit_card_details: credit_card_details,
                canc_status: canc_status,
                currency_code:currency_code
              },
              success: function(result) {
                $('#btn_save_payment').button('reset');
                $('#btn_save_payment').prop('disabled', false);
                var msg = result.split('-');
                if (msg[0] == 'error') {
                  error_msg_alert(result);
                  $('#btn_save_payment').button('reset');
                  return false;
                } else {
                  msg_alert(result);
                  $('#btn_save_payment').button('reset');
                  $('#btn_save_payment').prop('disabled', false);
                  reset_form('frm_visa_payment_save');
                  $('#visa_payment_save_modal').modal('hide');
                  visa_payment_list_reflect();
                }
              }
            });
            setTimeout(() => {
              if ($('#whatsapp_switch').val() == "on") whatsapp_send_r(visa_id, payment_amount, base_url);
            }, 1000);
          }
        });
      }
    });
  });

  function bank_list_reflect() {
    var base_url = $('#base_url').val();

    $.post(base_url + 'view/load_data/bank_list_json_response.php', {}, function(data) {
      var data = jQuery.parseJSON(data);
      bank_name_autocomplete(data);
    });
  }
  bank_list_reflect();

  function bank_name_autocomplete(data) {
    $('.bank_suggest').each(function() {
      $(this).autocomplete({
        source: data
      });
    });
  }
</script>