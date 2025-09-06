<?php
include "../../../../model/model.php";
/*======******Header******=======*/
require_once('../../../layouts/admin_header.php');
include_once('../inc/quotation_hints_modal.php');
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='package_booking/quotation/home/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
$financial_year_id = $_SESSION['financial_year_id'];
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">
<?= begin_panel('Package Tour Quotation', 40) ?>
<div class="app_panel_content">
    <div class="row">
        <div class="col-md-12">
            <div id="div_id_proof_content"> </div>
            <div class="row mg_bt_20">
                <div class="col-xs-12 text-right">
                    <form action="save/index.php" method="POST">
                        <button class="btn btn-info btn-sm ico_left" id="quot_save"><i class="fa fa-plus"></i>&nbsp;&nbsp;Quotation</button>
                    </form>
                </div>
            </div>

            <div class="app_panel_content Filter-panel">
                <div class="row">
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <select name="quotation_id" id="quotation_id" title="Select Quotation" style="width:100%">
                            <option value="">Select Quotation</option>
                            <?php
                            $query = "select * from package_tour_quotation_master where 1 and financial_year_id='$financial_year_id' and status='1' ";
                            if ($role == 'B2b' || $role == 'Sales' || $role == 'Backoffice') {
                                $query .= " and emp_id='$emp_id'";
                            }
                            if ($branch_status == 'yes' && $role != 'Admin') {
                                $query .= " and branch_admin_id = '$branch_admin_id'";
                            }
                            if ($branch_status == 'yes' && $role == 'Branch Admin') {
                                $query .= " and branch_admin_id='$branch_admin_id'";
                            }
                            $query .= " order by quotation_id desc";
                            $sq_quotation = mysqlQuery($query);
                            while ($row_quotation = mysqli_fetch_assoc($sq_quotation)) {

                                $quotation_date = $row_quotation['quotation_date'];
                                $yr = explode("-", $quotation_date);
                                $year = $yr[0];
                            ?>
                                <option value="<?= $row_quotation['quotation_id'] ?>">
                                    <?= get_quotation_id($row_quotation['quotation_id'], $year) ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <input type="text" id="from_date_filter" name="from_date_filter" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date_filter');">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <input type="text" id="to_date_filter" name="to_date_filter" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date_filter' , 'to_date_filter');">
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <select name="booking_type_filter" id="booking_type_filter" title="Tour Type" onchange="get_tour_typewise_packages(this.id);">
                            <option value="">Tour Type</option>
                            <option value="Domestic">Domestic</option>
                            <option value="International">International</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 col-xs-12 mg_bt_10_xs">
                        <select name="tour_name" id="tour_name_filter" title="Package Name" style="width:100%">
                            <option value="">Package Name</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <select name="status" id="status" title="Status" style="width:100%">
                            <option value="">Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="row mg_tp_10">
                    <?php if ($role == 'Admin') { ?>
                        <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10_xs">
                            <select name="branch_id_filter" id="branch_id_filter1" title="Branch Name" style="width: 100%">
                                <?php get_branch_dropdown($role, $branch_admin_id, $branch_status) ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="col-md-3 col-sm-6 mg_bt_10_xs">
                        <select name="financial_year_id_filter" id="financial_year_id_filter" title="Select Financial Year">
                            <?php
                            $sq_fina = mysqli_fetch_assoc(mysqlQuery("select * from financial_year where financial_year_id='$financial_year_id'"));
                            $financial_year = get_date_user($sq_fina['from_date']).'&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;'.get_date_user($sq_fina['to_date']);
                            ?>
                            <option value="<?= $sq_fina['financial_year_id'] ?>"><?= $financial_year  ?></option>
                            <?php echo get_financial_year_dropdown_filter($financial_year_id); ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_tp_10">
                        <button class="btn btn-sm btn-info ico_right" onclick="quotation_list_reflect()">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <div id="div_quotation_list_reflect" class="main_block loader_parent">
                <div class="row mg_tp_20">
                    <div class="col-md-12 no-pad">
                        <div class="table-responsive">
                            <table id="package_table" class="table table-hover" style="width:100% !important;margin: 20px 0 !important;">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="div_quotation_form"></div>
            <div id="div_quotation_save"></div>
            <div id="backoffice_mail"></div>
            <div id="view_request"></div>
            
            
<!-- Modal Structure -->
<div class="modal fade" id="contentModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">HTML Content Preview</h5>
                 <button id="download-button" class="btn btn-success">Download as Word Document</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            <div class="modal-body">
                <div id="preview"></div>
            </div>
            <div class="modal-footer">
              
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
</div>
<?= end_panel() ?>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>
<script>
    $('#quotation_id,#tour_name_filter').select2();
    $('#from_date_filter, #to_date_filter').datetimepicker({
        timepicker: false,
        format: 'd-m-Y'
    });
    $('[data-toggle="tooltip"]').tooltip();

    var column = [{
            title: "S_No."
        },
        {
            title: "QUOTATION_ID"
        },
        {
            title: "Tour_Name"
        },
        {
            title: "Customer"
        },
        {
            title: "Quotation_Date"
        },
        {
            title: "Amount"
        },
        {
            title: "Created_by"
        },
        {
            title: "Actions",
            className: "text-center action_width"
        }
    ];

    function view_request(quot_id) {
        $('#view_req' + quot_id).prop('disabled', true);
        $('#view_req' + quot_id).button('loading');
        $.post('hotel_availability/index.php', {
            quot_id: quot_id
        }, function(data) {
            $('#view_request').html(data);
            $('#view_req' + quot_id).button('reset');
            $('#view_req' + quot_id).prop('disabled', false);
        });
    }

function view_request(quot_id){
	$('#view_req'+quot_id).prop('disabled',true);
	$('#view_req'+quot_id).button('loading');
	$.post('hotel_availability/index.php', {quot_id : quot_id }, function(data){
		$('#view_request').html(data);
		$('#view_req'+quot_id).button('reset');
        $('#view_req'+quot_id).prop('disabled',false);
	});
}
function quotation_list_reflect() {
    $('#div_quotation_list_reflect').append('<div class="loader"></div>');
    var from_date = $('#from_date_filter').val();
    var to_date = $('#to_date_filter').val();
    var booking_type = $('#booking_type_filter').val();
    var package_id = $('#tour_name_filter').val();
    var quotation_id = $('#quotation_id').val();
    var branch_status = $('#branch_status').val();
    var branch_id = $('#branch_id_filter1').val();
    var status = $('#status').val();
    var financial_year_id_filter = $('#financial_year_id_filter').val();

    $.post('quotation_list_reflect.php', {
        from_date: from_date,
        to_date: to_date,
        booking_type: booking_type,
        package_id: package_id,
        quotation_id: quotation_id,
        branch_status: branch_status,
        branch_id: branch_id,
        status: status,
        financial_year_id:financial_year_id_filter
    }, function(data) {
        pagination_load(data, column, true, false, 20, 'package_table');
        $('.loader').remove();
    })
}
quotation_list_reflect();

    function quotation_clone(quotation_id) {
        var base_url = $('#base_url').val();
        $('#vi_confirm_box').vi_confirm_box({
            callback: function(data1) {
                if (data1 == "yes") {
                    $.ajax({
                        type: 'post',
                        url: base_url + 'controller/package_tour/quotation/quotation_clone.php',
                        data: {
                            quotation_id: quotation_id
                        },
                        success: function(result) {
                            msg_alert(result);
                            console.log(result);
                            quotation_list_reflect();
                        }
                    });
                }
            }
        });
    }

    function quotation_email_send(btn_id, quotation_id, email_id, mobile_no) {
        $('#' + btn_id).button('loading');
        var base_url = $('#base_url').val();
        $.post('send_quotation.php', {
            email_id: email_id,
            mobile_no: mobile_no
        }, function(data) {
            $('#div_quotation_form').html(data);
            $('#' + btn_id).button('reset');
        });

    }

    function get_tour_typewise_packages(tour_type) {

        var tour_type = $('#' + tour_type).val();
        $.post('get_tour_typewise_packages.php', {
            tour_type: tour_type
        }, function(data) {
            $('#tour_name_filter').html(data);
        });
    }
    get_tour_typewise_packages('booking_type_filter');

function quotation_email_send_backoffice_modal(quotation_id) {
    
	$('#email_backoffice_btn-'+quotation_id).prop('disabled',true);
	$('#email_backoffice_btn-'+quotation_id).button('loading');
    $.post('backoffice_mail.php', {
        quotation_id: quotation_id
    }, function(data) {
        $('#backoffice_mail').html(data);
        $('#email_backoffice_btn-'+quotation_id).prop('disabled',false);
        $('#email_backoffice_btn-'+quotation_id).button('reset');
    });
}

function save_modal() {
    var branch_status = $('#branch_status').val();
    $('#quot_save').button('loading');
    $.post('save/index.php', {
        branch_status: branch_status
    }, function(data) {
        $('#div_quotation_save').html(data);
        $('#quot_save').button('reset');
    });
}
</script>
<style>
    .action_width {
        width: 250px;
        display: flex;
    }

    tr.warning {
        background-color: #fcf8e3;
    }

    .table-hover>tbody>tr.warning:hover {
        background-color: #faf2cc;
    }
</style>
<?php
/*======******Footer******=======*/
require_once('../../../layouts/admin_footer.php');
?>