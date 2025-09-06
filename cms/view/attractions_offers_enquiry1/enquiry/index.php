<?php
	include "../../../model/model.php";
	/*======******Header******=======*/
	require_once('../../layouts/admin_header.php');
	$branch_admin_id = $_SESSION['branch_admin_id'];
	$q = "select * from branch_assign where link='attractions_offers_enquiry/enquiry/index.php'";
	
	$sq_count = mysqli_num_rows(mysqlQuery($q));
	$sq = mysqli_fetch_assoc(mysqlQuery($q));
	$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
	
	$login_id = $_SESSION['login_id'];
	$role = $_SESSION['role'];
	$emp_id = $_SESSION['emp_id'];
	$financial_year_id = $_SESSION['financial_year_id'];
	
	
	// include_once('enquiry_master_save.php');
	?>
<style>
	#enq_table tbody tr[data-bg="danger"] {
	background-color: rgb(255, 231, 233) !important;
	}
	#enq_table tbody tr[data-bg="success"] {
	background-color: rgb(222, 252, 222) !important;
	}
	#enq_table tbody tr[data-bg="danger"]:hover {
	background-color: rgb(243, 209, 212) !important;
	}
	#enq_table tbody tr[data-bg="success"]:hover {
	background-color: rgb(201, 243, 194) !important;
	}
</style>

 
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">
<input type="hidden" id="financial_year_id" name="financial_year_id" value="<?= $financial_year_id ?>">
<?= begin_panel('Enquiry', 39) ?>
<div class="row">
	<div class="col-sm-8">
		<button class="btn btn-info btn-sm ico_left pull-left" title="Download CSV Format" style="margin-right:10px"
			onclick="display_format_modal();"><i class="fa fa-download"></i>&nbsp;&nbsp;CSV Format</button>
		<div class="div-upload" id="div_upload_button" title="Upload CSV Format">
			<div id="enquiry_csv_upload" class="upload-button1" title="Upload CSV Format"><span>CSV</span></div>
			<span id="id_proof_status"></span>
			<ul id="files"></ul>
			<input type="hidden" id="txt_enquiry_csv_upload_dir" name="txt_enquiry_csv_upload_dir">
		</div>
		<button type="button" data-toggle="tooltip" class="btn btn-excel" title="Note : Use CSV Import for Package Booking Enquiries only."><i class="fa fa-question-circle"></i></button>
	</div>
	<div class="col-sm-4 text-right text_left_sm_xs">
		<?php if ($role == 'Admin' || $role == 'Branch Admin') { ?>
		<button class="btn btn-excel btn-sm" onclick="excel_report_followup()" data-toggle="tooltip">
		<a title="Followup Report">
		<i class="fa fa-file-excel-o"></i>
		</a>
		</button>
		<?php } ?>
		<button class="btn btn-excel btn-sm" onclick="excel_report()" data-toggle="tooltip">
		<a title="Enquiry Report">
		<i class="fa fa-file-excel-o"></i>
		</a>
		</button>
		<button class="btn btn-excel btn-sm" id="send_btn" onclick="send();btnDisableEnable(this.id)" data-toggle="tooltip" title=""
			data-original-title="Send Enquiry Form"><i class="fa fa-paper-plane-o"></i></button>
		<button class="btn btn-info btn-sm ico_left" id="btn_save_modal" onclick="save_modal()"><i
			class="fa fa-plus"></i>&nbsp;&nbsp;Enquiry</button>
	</div>
</div>



<!--=======Header panel end======-->
<!-- <div class="app_panel_content"> -->
<div class="div-upload pull-left mg_bt_0 hidden" id="div_upload_button">
	<div id="enq_csv_upload" class="upload-button1"><span>Import Enquiries</span></div>
	<span id="adnary_status"></span>
	<ul id="files"></ul>
	<input type="hidden" id="enq_csv_dir" name="enq_csv_dir">
</div>

<div class="main_block mg_tp_10">
	<!-- <div class="col-md-12"> -->
	<div class="app_panel_content Filter-panel">
		<div class="row">
			<?php
				if ($role == "Admin") {
				?>
			<div class="col-md-3 col-sm-6 mg_bt_10">
				<select name="emp_id_filter" id="emp_id_filter" style="width:100%" title="User">
					<option value="">User</option>
					<?php
						$sq_emp = mysqlQuery("select * from emp_master where emp_id!='0' and active_flag='Active'");
						while ($row_emp = mysqli_fetch_assoc($sq_emp)) {
						?>
					<option value="<?= $row_emp['emp_id'] ?>">
						<?= $row_emp['first_name'] . ' ' . $row_emp['last_name'] ?>
					</option>
					<?php
						}
						?>
				</select>
			</div>
			<?php
				} elseif ($branch_status == 'yes' && $role == 'Branch Admin') {  ?>
			<div class="col-md-3 col-sm-6 mg_bt_10">
				<select name="emp_id_filter" id="emp_id_filter" style="width:100%" title="Users">
					<option value="">User</option>
					<?php
						$query = "select * from emp_master where active_flag='Active' and branch_id='$branch_admin_id' order by first_name asc";
						$sq_emp = mysqlQuery($query);
						while ($row_emp = mysqli_fetch_assoc($sq_emp)) {
						?>
					<option value="<?= $row_emp['emp_id'] ?>">
						<?= $row_emp['first_name'] . ' ' . $row_emp['last_name'] ?>
					</option>
					<?php
						}
						?>
				</select>
			</div>
			<?php } ?>
			<div class="col-md-3 col-sm-6 mg_bt_10">
				<select name="enquiry_status_filter" id="enquiry_status_filter" title="Enquiry Status">
					<option value="">Status</option>
					<option value="Active">Active</option>
					<option value="In-Followup">In-Followup</option>
					<option value="Dropped">Dropped</option>
					<option value="Converted">Converted</option>
				</select>
			</div>
			<div class="col-md-3 col-sm-6 mg_bt_10">
				<input type="text" id="followup_from_date_filter" name="followup_from_date_filter"
					placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'followup_to_date_filter')">
			</div>
			<div class="col-md-3 col-sm-6 mg_bt_10">
				<input type="text" id="followup_to_date_filter" name="followup_to_date_filter" placeholder="To Date"
					title="To Date"
					onchange="validate_validDate('followup_from_date_filter','followup_to_date_filter')">
			</div>
		</div>
		<div class="row">
			<div class="col-md-3 col-sm-6 mg_bt_10_xs">
				<select name="enquiry_type_filter" id="enquiry_type_filter" title="Enquiry For">
					<option value="">Enquiry For</option>
					<option value="Package Booking">Package Booking</option>
					<option value="Group Booking">Group Booking</option>
					<option value="Hotel">Hotel</option>
					<option value="Flight Ticket">Flight Ticket</option>
					<option value="Car Rental">Car Rental</option>
					<option value="Visa">Visa</option>
					<option value="Bus">Bus</option>
					<option value="Train Ticket">Train Ticket</option>
				</select>
			</div>
			<div class="col-md-3 col-sm-6 mg_bt_10_xs">
				<select name="reference_id_filter" id="reference_id_filter" title="Reference">
					<option value="">Reference</option>
					<?php
						$sq_ref = mysqlQuery("select * from references_master where active_flag='Active' order by reference_name");
						while ($row_ref = mysqli_fetch_assoc($sq_ref)) {
						?>
					<option value="<?= $row_ref['reference_id'] ?>"><?= $row_ref['reference_name'] ?></option>
					<?php
						}
						?>
				</select>
			</div>
			<div class="col-md-3 col-sm-6 mg_bt_10_xs">
				<select name="enquiry_filter" id="enquiry_filter" title="Enquiry Type">
					<option value="">Enquiry Type</option>
					<option value="<?= "Strong" ?>">Strong</option>
					<option value="<?= "Hot" ?>">Hot</option>
					<option value="<?= "Cold" ?>">Cold</option>
				</select>
			</div>
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
			<div class="col-md-3 col-sm-6 mg_tp_10" style="margin-top:10px !important;">
				<select name="destination_filter" id="destination_filter" title="Select Destination" class="form-control">
                    <option value="">Select Destination</option>
                    <?php
                    $query = mysqlQuery("SELECT enquiry_content FROM `enquiry_master` 
                        LEFT JOIN enquiry_master_entries AS ef ON enquiry_master.entry_id = ef.entry_id 
                        WHERE enquiry_master.status != 'Disabled'");

                    $tour_names = [];

                    while ($row = mysqli_fetch_assoc($query)) {
                        $enquiry_content = $row['enquiry_content'];
                        $enquiry_content_arr = json_decode($enquiry_content, true);

                        if (is_array($enquiry_content_arr)) {
                            foreach ($enquiry_content_arr as $item) {
                                if (isset($item['name']) && $item['name'] == 'tour_name') {
                                    $tour_name = trim($item['value']);
                                    $key = strtolower($tour_name); // Normalize key for uniqueness

                                    if ($tour_name !== '' && !isset($tour_names[$key])) {
                                        $tour_names[$key] = $tour_name;
                                    }
                                    break; // Stop after finding tour_name
                                }
                            }
                        }
                    }
                    asort($tour_names);
                    foreach ($tour_names as $original_name) {
                        echo "<option value=\"$original_name\">$original_name</option>";
                    }
                    ?>
                </select>

			</div>
			<div class="col-md-3 col-sm-6 mg_tp_10" style="margin-top:10px !important;">
                <select name="landline_no_filter" id="landline_no_filter" title="Select Whatsapp No" class="form-control">
                    <option value="">Select Whatsapp No</option>
                    <?php
                    $query = mysqlQuery("SELECT landline_no FROM `enquiry_master` WHERE status != 'Disabled' AND financial_year_id = '$financial_year_id'");
                    $unique_landlines = [];

                    while ($row = mysqli_fetch_assoc($query)) {
                        $landline_no = trim($row['landline_no']);
                        
                        if ($landline_no === '') continue; // Skip empty values

                        $key = strtolower($landline_no); // Normalize for case-insensitive comparison
                        if (isset($unique_landlines[$key])) continue; // Skip duplicates

                        $unique_landlines[$key] = $landline_no; // Store original for display
                    }

                    // Optional: sort the numbers alphabetically
                    asort($unique_landlines);

                    // Output unique options
                    foreach ($unique_landlines as $number) {
                        echo "<option value=\"$number\">$number</option>";
                    }
                    ?>
                </select>
            </div>

			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" style="margin-top:10px !important;">
				<select name="cust_name_filter" class="form-control" id="cust_name_filter"
					title="Customer Type" style="width: 100%;">
					<option value="">Select Customer</option>
					<?php
						$query = mysqlQuery("SELECT * FROM `enquiry_master` WHERE status != 'Disabled'");
						$names_added = []; // Array to track unique customer names
						
						while ($row = mysqli_fetch_assoc($query)) {
						    $cust_name = trim($row['name']);
						    if ($cust_name == '') continue; // Skip blank names
						    if (in_array(strtolower($cust_name), $names_added)) continue; // Skip duplicates (case-insensitive)
						
						    $names_added[] = strtolower($cust_name); // Store for duplicate checking
						?>
					<option value="<?= $cust_name ?>"><?= $cust_name ?></option>
					<?php
						}
						?>
				</select>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" style="margin-top:10px !important;">
				<select name="followup_status_type_filter" class="form-control" id="followup_status_type_filter"
					title="Customer Type" style="width: 100%;">
					<option value="">Followup Type</option>
					<?php
						$query = mysqlQuery("SELECT * FROM `enquiry_master` left join enquiry_master_entries as ef on enquiry_master.entry_id=ef.entry_id where enquiry_master.status!='Disabled'");
						$names_added = []; // Array to track unique customer names
						
						while ($row = mysqli_fetch_assoc($query)) {
						    $followup_name = $row['followup_type'];
						    if ($followup_name == '') continue; // Skip blank names
						    if (in_array($followup_name, $names_added)) continue; // Skip duplicates (case-insensitive)
						
						    $names_added[] = $followup_name; // Store for duplicate checking
						?>
					<option value="<?= $followup_name ?>"><?= $followup_name ?></option>
					<?php
						}
						?>
				</select>
			</div>
			<div class="col-md-3 col-sm-6 mg_tp_10">
				<button class="btn btn-sm btn-info ico_right" onclick="enquiry_proceed_reflect()">Proceed&nbsp;&nbsp;<i
					class="fa fa-arrow-right"></i></button>
			</div>
		</div>
	</div>
	<!-- </div> -->
</div>

<div id="div_modal"></div>
<div id="send_btn_modal"></div>
<div id="div_enquiries_list" class="main_block loader_parent">
	<div class="row mg_tp_20">
		<div class="col-md-12">
			<div class="col-md-4">
				<p><span style="font-weight:bold; font-size:18px; padding-top:10px;">Enquiry Count:</span><span
					id="enquiry_count" style="font-weight:bold; font-size:18px; margin-top:10px;"></span></p>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<div id="tables_load" class="loader_header">
			<table id="enq_table" class="table table-hover" style="margin: 20px 0 !important;">
			</table>
		</div>
	</div>
	<div id="load_data_message"></div>
</div>


<!-- </div> -->
<?= end_panel() ?>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>
<script>
	$('#followup_from_date_filter, #followup_to_date_filter').datetimepicker({
	    timepicker: false,
	    format: 'd-m-Y'
	});
	$('#emp_id_filter,#landline_no_filter,#cust_name_filter,#followup_status_type_filter,#destination_filter').select2();
	//Enquiry CSV Format File Code
	enquiry_csv_upload();
	
	function enquiry_csv_upload() {
	    var type = "id_proof";
	    var btnUpload = $('#enquiry_csv_upload');
	    var status = $('#id_proof_status');
	    var financial_year = $('#financial_year_id').val();
	    if (financial_year == ''||financial_year == 0) {
	        error_msg_alert("Please select Financial year then add Enquiries! ");
	        return false;
	    }
	    new AjaxUpload(btnUpload, {
	        action: 'upload_enquiry_csv_file.php',
	        name: 'uploadfile',
	        onSubmit: function(file, ext) {
	
	            if (!(ext && /^(csv)$/.test(ext))) {
	
	                // extension is not allowed
	                alert('Only CSV Format files are allowed');
	                return false;
	                if (!confirm('Do you want to import this file?')) {
	                    return false;
	                } else {
	                    status.text('Uploading');
	                }
	            }
	        },
	        onComplete: function(file, response) {
	            //On completion clear the status
	            status.text('');
	            //Add uploaded file to list
	            if (response === "error") {
	                alert("File is not uploaded.");
	            } else {
	                status.text('');
	                document.getElementById("txt_enquiry_csv_upload_dir").value = response;
	                enqiury_from_csv_save();
	            }
	        }
	    });
	}

	
	
	function enqiury_from_csv_save() {
	    var enq_csv_dir = document.getElementById("txt_enquiry_csv_upload_dir").value;
	    var base_url = $('#base_url').val();
	
	    prompt_error_enquiry(enq_csv_dir, '');
	}
	
	
	function prompt_error_enquiry(obj, msg) {
	
	    var status = $('#id_proof_status');
	        $('#vi_confirm_box').vi_confirm_box({
	            message: "Are you sure to save?",
	            callback: function(data1) {
	                if (data1 == "yes") {
	                    actual_enq_save(obj);
	                    status.text('Uploading');
	                } else {
	                    status.text('');
	                    return false;
	                }
	            }
	        });
	}

	
	
	function actual_enq_save(obj) {
	
	    var base_url = $('#base_url').val();
	    $('#app_content_wrap').append('<div class="loader"></div>');
	    var status = $('#id_proof_status');
	    var obj = {
	        obj: obj
	    };
	    $.post(
	        base_url + "controller/attractions_offers_enquiry/enquiry_csv_save.php",
	        obj,
	        function(data) {
	            var msg = data.split('--');
	            let result = msg[0].includes("error");
	            if (result || msg[0] == 'error') {
	                error_msg_alert(msg[1]);
	                status.text('');
	            } else {
	                msg_alert(data);
	                status.text('');
	                enquiry_proceed_reflect();
	                notification_count_update();
	            }
	        });
	}

	
	
	function display_format_modal() {
	    var base_url = $('#base_url').val();
	    window.location = base_url + "images/csv_format/enquiry.csv";
	}
	
	function save_modal() {
	    $('#btn_save_modal').button('loading');
	    var branch_status = $('#branch_status').val();
	    $.post('save_modal.php', {
	        branch_status: branch_status
	    }, function(data) {
	        $('#btn_save_modal').button('reset');
	        $('#div_modal').html(data);
	    });
	}

	
	// var columns = [{
	//         title: "S_No.",
	//         className: "action_width"
	//     },
	//     {
	//         title: "Enquiry_No",
	//         className: "action_width"
	//     },
	//     {
	//         title: "Customer"
	//     },
	//     {
	//         title: "Mobile_No"
	//     },
	//     {
	//         title: "Tour_type"
	//     },
	//     {title:"Destination "},
	//     {
	//         title: "Enquiry_date",
	//         className: "action_width"
	//     },
	//     {
	//         title: "Followup_dateTime",
	//         className: "action_width"
	//     },
	//     {title:"Follow_up_type",
	//          className: "action_width"
	//     },
	//     {
	//         title: "Allocate_To",
	//         className: "action_width"
	//     },
	//     {
	//         title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Actions&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
	//         className: "text-center actions_width"
	//     }
	
	// ];
	
	
	var columns = [
	    { data: "s_no", title: "S_No.", className: "action_width" },
	    { data: "enquiry_no", title: "Enquiry_No", className: "action_width" },
	    { data: "customer", title: "Customer" },
	    { data: "mobile_no", title: "Mobile_No" },
	    { data: "tour_type", title: "Tour_type" },
	    { data: "destination", title: "Destination" },
	    { data: "enquiry_date", title: "Enquiry_date", className: "action_width" },
	    { data: "followup_datetime", title: "Followup_dateTime", className: "action_width" },
	    { data: "follow_up_type", title: "Follow_up_type", className: "action_width" },
	    { data: "allocate_to", title: "Allocate_To", className: "action_width" },
	    {
	        data: "actions",
	        title: "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Actions&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
	        className: "text-center actions_width"
	    }
	];


	
	enquiry_proceed_reflect();
	
	function enquiry_proceed_reflect() {
	    $('#tables_load').append('<div class="loader"></div>');
	    $('#search_table').hide();
	    $('#enquiry_table').show();
	    var enquiry_type = $('#enquiry_type_filter').val();
	    var enquiry = $('#enquiry_filter').val();
	    var enquiry_status = $('#enquiry_status_filter').val();
	    var from_date = $('#followup_from_date_filter').val();
	    var to_date = $('#followup_to_date_filter').val();
	    var reference_id_filter = $('#reference_id_filter').val();
	    var emp_id_filter = $('#emp_id_filter').val();
	    var branch_status = $('#branch_status').val();
	    var financial_year_id_filter = $('#financial_year_id_filter').val();
	
	    var destination_filter =$('#destination_filter').val();
	
	    
	
	//     $.ajax({
	//         url: "enquiry_proceed_reflect.php",
	//         method: "POST",
	//         data: {
	//             enquiry: enquiry,
	//             enquiry_type: enquiry_type,
	//             enquiry_status: enquiry_status,
	//             from_date: from_date,
	//             to_date: to_date,
	//             emp_id_filter: emp_id_filter,
	//             branch_status: branch_status,
	//             reference_id_filter: reference_id_filter,
	//             financial_year_id:financial_year_id_filter,
	//             destination_filter:destination_filter
	//         },
	//         cache: false,
	//         success: function(data) {
	//             setTimeout(() => {
	
	//                 var table = pagination_load(data, columns, true, false, 30,"enq_table") // third parameter is for bg color show yes or 
	//                 $('#enquiry_count').html(table.rows().count());
	//                 $('.loader').remove();
	//             }, 1000);
	//         }
	//     });
	// }
	
	if ( $.fn.DataTable.isDataTable('#enq_table') ) {
	    
	    $('#enq_table').DataTable().clear().destroy();
	}

		
	
	const enquiryTableConfig = {
	         processing: true,
	         serverSide: true,
	         dom: 'lrtip',
	         ajax: function(data, callback, settings) {
	
	          
	             data.enquiry_type = $('#enquiry_type_filter').val();
	             data.enquiry = $('#enquiry_filter').val();
	             data.enquiry_status = $('#enquiry_status_filter').val();
	             data.from_date = $('#followup_from_date_filter').val();
	             data.to_date = $('#followup_to_date_filter').val();
	             data.reference_id_filter = $('#reference_id_filter').val();
	             data.emp_id_filter = $('#emp_id_filter').val();
	             data.branch_status = $('#branch_status').val();
	             data.financial_year_id_filter = $('#financial_year_id_filter').val();
	             data.destination_filter= $('#destination_filter').val();
	
	             data.cust_name_filter = $('#cust_name_filter').val();
	
	              data.landline_no_filter = $('#landline_no_filter').val();
	              data.followup_status_type_filter = $('#followup_status_type_filter').val();
	              
	             jQuery.ajax({
	                 url: "enquiry_proceed_reflect.php",
	                 method: "POST",
	                 data: data,
	                 success: function(res) {
	                    //  console.log(res)
	                     const parsed = JSON.parse(res);
	                     $('#enquiry_count').html(parsed.recordsFiltered);
	                     $('.loader').remove();
	                     callback(parsed);
	                 },
	             });
	         },
	         columns:columns,
	
	
	rowCallback: function(row, data, index) {
	    // Clear any existing bg-* class
	    $(row).removeClass(function(i, className) {
	        return (className.match(/(^|\s)bg-\S+/g) || []).join(' ');
	    });
	
	    // Add new bg-* class and data-bg
	    if (data.bg) {
	        $(row).addClass('bg-' + data.bg).attr('data-bg', data.bg);
	    }
	}
	
	       }
	     const enquiryTableInstance = $("#enq_table").DataTable(
	         enquiryTableConfig
	     );
	     return;
	    }
	
	    
	function enquiry_status_done(enquiry_id) {
	
	    var base_url = $('#base_url').val();
	    $('#vi_confirm_box').vi_confirm_box({
	        callback: function(data1) {
	            if (data1 == "yes") {
	                $.post(base_url + 'controller/attractions_offers_enquiry/enquiry_status.php', {
	                    enquiry_id: enquiry_id
	                }, function(data) {
	                    msg_alert(data);
	                    enquiry_proceed_reflect();
	                })
	            }
	        }
	    });
	}
	
	function enquiry_status_disable(enquiry_id) {
	
	    var base_url = $('#base_url').val();
	    $('#vi_confirm_box').vi_confirm_box({
	        callback: function(data1) {
	            if (data1 == "yes") {
	                $.post(base_url + 'controller/attractions_offers_enquiry/enquiry_status_disable.php', {
	                    enquiry_id: enquiry_id
	                }, function(data) {
	                    msg_alert(data);
	                    enquiry_proceed_reflect();
	                })
	            }
	        }
	    });
	}

	
	/////////////////////////////////////////////////////////// Enquiry Loading Code End//////////////////////////////////////////////////////////////////
	
	function excel_report() {
	    var enquiry_type = $('#enquiry_type_filter').val();
	    var enquiry = $('#enquiry_filter').val();
	    var enquiry_status = $('#enquiry_status_filter').val();
	    var from_date = $('#followup_from_date_filter').val();
	    var to_date = $('#followup_to_date_filter').val();
	    var emp_id_filter = $('#emp_id_filter').val();
	    var reference_id = $('#reference_id_filter').val();
	    var branch_status = $('#branch_status').val();
	    var financial_year_id_filter = $('#financial_year_id_filter').val();
	
	    var destination_filter =$('#destination_filter').val();
	
	    var cust_name_filter = $('#cust_name_filter').val();
	
	     var landline_no_filter = $('#landline_no_filter').val();
	
	     var followup_status_type_filter = $('#followup_status_type_filter').val();
	
	    window.location = 'excel_report.php?enquiry_type=' + enquiry_type + '&enquiry=' + enquiry + '&enquiry_status=' +
	        enquiry_status + '&from_date=' + from_date + '&to_date=' + to_date + '&emp_id_filter=' + emp_id_filter +
	        '&reference_id=' + reference_id + '&branch_status=' + branch_status +'&destination_filter='+destination_filter+ '&financial_year_id_filter='+financial_year_id_filter +'&cust_name_filter=' + cust_name_filter + '&landline_no_filter=' + landline_no_filter +'&followup_status_type_filter='+ followup_status_type_filter;
	}

	
	
	function excel_report_followup() {
	    var emp_id_filter = $('#emp_id_filter').val();
	    var enquiry = $('#enquiry_filter').val();
	    var enquiry_type = $('#enquiry_type_filter').val();
	    var branch_status = $('#branch_status').val();
	    var from_date = $('#followup_from_date_filter').val();
	    var to_date = $('#followup_to_date_filter').val();
	    var enquiry_status = $('#enquiry_status_filter').val();
	    var reference_id = $('#reference_id_filter').val();
	
	
	    // var financial_year_id_filter = $('#financial_year_id_filter').val();
	
	    // var destination_filter =$('#destination_filter').val();
	
	    // var cust_name_filter = $('#cust_name_filter').val();
	
	    //  var enq_no_filter = $('#enq_no_filter').val();
	
	    //  var followup_status_type_filter = $('#followup_status_type_filter').val();
	
	    window.location = 'followup/followup/excel_report.php?branch_status=' + branch_status + '&from_date=' + from_date +
	        '&to_date=' + to_date + '&emp_id_filter=' + emp_id_filter + '&enquiry_status=' + enquiry_status +
	        '&reference_id=' + reference_id + '&enquiry_type=' + enquiry_type + '&enquiry=' + enquiry;
	}

	
	
	function view_modal(enquiry_id) {
		$('#enq_modal_view-'+enquiry_id).prop('disabled',true);
		$('#enq_modal_view-'+enquiry_id).button('loading');
	    $.post('view_modal.php', {
	        enquiry_id: enquiry_id
	    }, function(data) {
	        $('#div_modal').html(data);
	        $('#enq_modal_view-'+enquiry_id).prop('disabled',false);
	        $('#enq_modal_view-'+enquiry_id).button('reset');
	    });
	}
	
	
	function update_modal(enquiry_id) {
		$('#enq_modal_update-'+enquiry_id).prop('disabled',true);
		$('#enq_modal_update-'+enquiry_id).button('loading');
	    var branch_status = $('#branch_status').val();
	    $.post('edit_modal.php', {
	        enquiry_id: enquiry_id,
	        branch_status: branch_status
	    }, function(data) {
	        $('#div_modal').html(data);
	        $('#enq_modal_update-'+enquiry_id).prop('disabled',false);
	        $('#enq_modal_update-'+enquiry_id).button('reset');
	    });
	}
	
	
	function followup_modal(enquiry_id) {
		$('#followup_modal_add-'+enquiry_id).prop('disabled',true);
		$('#followup_modal_add-'+enquiry_id).button('loading');
	    $.post('followup/followup/display_modal.php', {
	        enquiry_id: enquiry_id
	    }, function(data) {
	        $('#div_modal').html(data);
	        $('#followup_modal_add-'+enquiry_id).prop('disabled',false);
	        $('#followup_modal_add-'+enquiry_id).button('reset');
	    });
	}

	
	
	function edit_modal(enquiry_id) {
		$('#enq_modal_update-'+enquiry_id).prop('disabled',true);
		$('#enq_modal_update-'+enquiry_id).button('loading');
	    $.post('edit_modal.php', {
	        enquiry_id: enquiry_id
	    }, function(data) {
	        $('#div_modal').html(data);
	        $('#enq_modal_update-'+enquiry_id).prop('disabled',false);
	        $('#enq_modal_update-'+enquiry_id).button('reset');
	    });
	}
	
	
	function followup_type_reflect(followup_status) {
	    $.post('followup/followup/followup_type_reflect.php', {
	        followup_status: followup_status
	    }, function(data) {
	        $('#followup_type').html(data);
	    });
	}

	

	
	
	function send() {
		$('#send_btn').prop('disabled',true);
		$('#send_btn').button('loading');
	    $.post('send_enq_form.php', {}, function(data) {
	        $('#send_btn_modal').html(data);
	        $('#send_btn').prop('disabled',false);
	        $('#send_btn').button('reset');
	    });
	}
	

	
</script>
<style>
	.actions_width {
	display: flex;
	max-width: 400px !important;
	padding-right: 0px !important;
	}
</style>
<?php
	/*======******Footer******=======*/
	require_once('../../layouts/admin_footer.php');
	die;
?>