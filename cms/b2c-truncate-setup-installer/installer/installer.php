<?php
$database_name = $_POST['database_name'];
$username = $_POST['username'];
$password = $_POST['password'];

$conn = new mysqli('localhost', $username, $password, $database_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = $conn->query("delete from b2c_career where entry_id>'5'");
$query = $conn->query("delete from b2c_career where entry_id>'5'");
$table_exclude = array('state_and_cities', 'user_assigned_roles', 'roles', 'role_master', 'travel_station_master', 'bus_master', 'tour_budget_type', 'bank_name_master', 'bank_list_master', 'city_master', 'currency_name_master', 'vendor_type_master', 'estimate_type_master', 'airport_list_master', 'references_master', 'country_state_list', 'country_list_master', 'gallary_master', 'destination_master', 'airline_master', 'airport_master', 'visa_crm_master', 'visa_type_master', 'sac_master', 'state_master', 'generic_count_master', 'office_expense_type', 'branch_assign', 'ledger_master', 'group_master', 'head_master', 'subgroup_master', 'cms_master', 'cms_master_entries', 'fixed_asset_master', 'app_settings', 'modulewise_video_master', 'meal_plan_master', 'room_category_master', 'hotel_type_master', 'b2b_settings', 'b2b_settings_second', 'vehicle_type_master', 'default_package_images', 'tax_conditions', 'other_charges_master', 'ticket_master_airfile', 'ticket_entries_airfile', 'ticket_trip_entries_airfile', 'video_itinerary_master', 'b2b_transfer_master', 'tax_master', 'tax_master_rules', 'other_master_rules', 'financial_year', 'locations', 'branches', 'emp_master', 'itinerary_master', 'tcs_master', 'hotel_master', 'hotel_vendor_images_entries', 'custom_package_master', 'custom_package_program', 'custom_package_hotels', 'custom_package_transport', 'custom_package_images', 'roe_master', 'service_duration_master', 'format_image_master', 'generic_settings', 'hotel_master', 'vendor_login', 'terms_and_conditions', 'inclusions_exclusions_master', 'format_image_master', 'excursion_master_tariff', 'excursion_master_tariff_basics', 'excursion_master_images', 'excursion_master_offers', 'checklist_entities', 'to_do_entries', 'b2c_generic_settings', 'b2c_settings', 'b2c_color_scheme', 'b2c_meta_tags', 'b2c_testimonials', 'b2c_awards', 'b2c_blogs', 'b2c_career', 'b2c_services', 'b2c_team_details');

// HOTEL
// 1.hotel_master,2.hotel_vendor_images_entries,
// PACKAGE TOUR
// 1.custom_package_master, 2.custom_package_program, 3.custom_package_hotels, 4.custom_package_transport, 5.custom_package_images, 6.ledger_master

$sq_list_table = $conn->query("show tables");
while ($row = $sq_list_table->fetch_assoc()) {

    $table_name = $row['Tables_in_' . $database_name];

    if (isset($table_name) && !in_array($table_name, $table_exclude)) {
        $query = $conn->query("truncate $table_name");
    }
}

$query = $conn->query("delete from role_master where role_id not in ('1', '2', '3','4','5','6','7')");
$query = $conn->query("delete from references_master where reference_id not in ('1', '2', '3','4','5','6','7','8','9','10','11','12','13')");
$query = $conn->query("delete from roles where id!='1'");
$query = $conn->query("update generic_count_master set a_enquiry_count = '0', a_temp_enq_count='0', a_task_count='0', a_temp_task_count='0', invoice_format='Standard', a_temp_leave_count='0',a_leave_count='0',b_temp_task_count='0',b_task_count='0',b_temp_enq_count='0',b_enquiry_count='0' where id='1'");

$query = $conn->query("delete from office_expense_type where expense_type_id >= '21'");
// $query = $conn->query("delete from ledger_master where ledger_id >= '233'");
$query = $conn->query("delete from group_master where group_id >= '22'");
$query = $conn->query("delete from head_master where head_id >= '14'");
$query = $conn->query("delete from subgroup_master where subgroup_id >= '114'");
$query = $conn->query("delete from gallary_master where entry_id >= '1086'");
$query = $conn->query("delete from sac_master where sac_id >= '13'");
$query = $conn->query("delete from visa_type_master where visa_type_id >= '12'");
$query = $conn->query("delete from b2b_transfer_master where entry_id>'22'");

//Activity details
$query = $conn->query("delete from excursion_master_tariff where entry_id>'117'");
$query = $conn->query("delete from excursion_master_tariff_basics where entry_id>'117'");
$query = $conn->query("delete from excursion_master_images where exc_id>'117'");
$query = $conn->query("delete from excursion_master_offers where exc_id>'117'");

$query = $conn->query("delete from checklist_entities where entity_id>'77'");

$query = $conn->query("delete from terms_and_conditions where terms_and_conditions_id>'15'");

$query = $conn->query("delete from inclusions_exclusions_master where inclusion_id>'4'");

$query = $conn->query("delete from vendor_login where login_id>'881'");

$query = $conn->query("delete from to_do_entries where id>'911'");

$query = $conn->query("delete from city_master where city_id>'9963'");


$query = $conn->query("delete from financial_year where financial_year_id>='2'");
$query = $conn->query("delete from locations where location_id>='2'");
$query = $conn->query("delete from branches where branch_id>='2'");
$query = $conn->query("delete from emp_master where emp_id>='2'");
$query = $conn->query("delete from roe_master where entry_id>='2'");
$query = $conn->query("update emp_master set location_id = '1' and branch_id='1' where emp_id='1'");

// //////////////Ready data for hotel,packages START//////////////////////
// HOTEL(// 1.hotel_master,2.hotel_vendor_images_entries)
// PACKAGE TOUR
// 1.custom_package_master, 2.custom_package_program, 3.custom_package_hotels, 4.custom_package_transport, 5.custom_package_images, 6.ledger_master
$query = $conn->query("delete from hotel_master where hotel_id>='882'");
$query = $conn->query("delete from hotel_vendor_images_entries where hotel_id>='882'");
$query = $conn->query("delete from custom_package_master where package_id>='29'");

$query = $conn->query("delete from custom_package_program where package_id>='29'");
$query = $conn->query("delete from custom_package_hotels where package_id>='29'");
$query = $conn->query("delete from custom_package_transport where package_id>='29'");
$query = $conn->query("delete from custom_package_images where package_id>='29'");

$query = $conn->query("delete from ledger_master where ledger_id >= '1114'");
// /////////////////////////END///////////////////////////////////////////

$query = $conn->query("UPDATE `ledger_master` SET `balance`=0 WHERE 1");

$app_date = date('Y');
$query = $conn->query("update app_settings set app_version = '$app_date',bank_name='',acc_name='',bank_branch_name='',bank_acc_no='',bank_ifsc_code='',bank_account_name='',bank_swift_code='' where setting_id='1'");

$query = $conn->query("update b2c_settings set popular_tours='',coupon_codes='',gallery='' where setting_id='1'");

// B2C table list : b2c_settings  //b2c_color_scheme //b2c_testimonials //b2c_awards //b2c_blogs //b2c_meta_tags //b2c_career //b2c_services // b2c_team_details
$query = $conn->query("delete from b2c_testimonials where entry_id>'6'");
$query = $conn->query("delete from b2c_awards where entry_id>'8'");
$query = $conn->query("delete from b2c_blogs where entry_id>'6'");
$query = $conn->query("delete from b2c_career where entry_id>'5'");
$query = $conn->query("delete from b2c_services where entry_id>'6'");
$query = $conn->query("delete from b2c_team_details where entry_id>'6'");
$conn->close();

function deleteDirectory($dirPath)
{
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                    deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        reset($objects);
        rmdir($dirPath);
    }
}

// //Truncate uploads directory
// $path = ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "uploads";
// $dir = new DirectoryIterator($path);
// foreach ($dir as $fileinfo) {
//     if ($fileinfo->isDir() && !$fileinfo->isDot()) {
//         $new_dir = $path . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
//         deleteDirectory($new_dir);
//     }
// }
unlink('../../view/cache_data.txt');
echo "All data is truncated";
