<?php 
include_once('../../../model/model.php');
include_once('../../../model/hotel/payment_master.php');
include_once('../../../model/app_settings/transaction_master.php');
include_once('../../../model/app_settings/bank_cash_book_master.php');
include_once('../../../model/app_settings/deleted_entries_save.php');

$payment_master = new payment_master;
$payment_master->payment_delete();
?>