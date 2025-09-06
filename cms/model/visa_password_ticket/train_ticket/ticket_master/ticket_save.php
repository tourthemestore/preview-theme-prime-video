<?php
$flag = true;
class ticket_save
{

	public function ticket_master_save()
	{
		$row_spec = 'sales';
		$customer_id = $_POST['customer_id'];
		$emp_id = $_POST['emp_id'];
		$type_of_tour = $_POST['type_of_tour'];
		$basic_fair = $_POST['basic_fair'];
		$service_charge = $_POST['service_charge'];
		$delivery_charges = $_POST['delivery_charges'];
		$gst_on = isset($_POST['gst_on']) ? $_POST['gst_on'] : '';
		$service_tax_subtotal = $_POST['service_tax_subtotal'];
		$net_total = $_POST['net_total'];

		$payment_due_date = $_POST['payment_due_date'];
		$booking_date = $_POST['booking_date'];

		$payment_date = $_POST['payment_date'];
		$payment_amount = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$bank_name = $_POST['bank_name'];
		$transaction_id = $_POST['transaction_id'];

		$bank_id = $_POST['bank_id'];
		$branch_admin_id = $_POST['branch_admin_id'];
		$financial_year_id = $_POST['financial_year_id'];
		$credit_charges = $_POST['credit_charges'];
		$credit_card_details = $_POST['credit_card_details'];


		$honorific_arr = isset($_POST['honorific_arr']) ? $_POST['honorific_arr'] : [];

		$first_name_arr = isset($_POST['first_name_arr']) ? $_POST['first_name_arr'] : [];

		$middle_name_arr = isset($_POST['middle_name_arr']) ? $_POST['middle_name_arr'] : [];

		$last_name_arr = isset($_POST['last_name_arr']) ? $_POST['last_name_arr'] : [];

		$birth_date_arr = isset($_POST['birth_date_arr']) ? $_POST['birth_date_arr'] : [];

		$adolescence_arr = isset($_POST['adolescence_arr']) ? $_POST['adolescence_arr'] : [];

		$coach_number_arr = isset($_POST['coach_number_arr']) ? $_POST['coach_number_arr'] : [];

		$seat_number_arr = isset($_POST['seat_number_arr']) ? $_POST['seat_number_arr'] : [];

		$ticket_number_arr = isset($_POST['ticket_number_arr']) ? $_POST['ticket_number_arr'] : [];



		$travel_datetime_arr = isset($_POST['travel_datetime_arr']) ? $_POST['travel_datetime_arr'] : [];
		$travel_from_arr = isset($_POST['travel_from_arr']) ? $_POST['travel_from_arr'] : [];
		$travel_to_arr = isset($_POST['travel_to_arr']) ? $_POST['travel_to_arr'] : [];
		$train_name_arr = isset($_POST['train_name_arr']) ? $_POST['train_name_arr'] : [];
		$train_no_arr = isset($_POST['train_no_arr']) ? $_POST['train_no_arr'] : [];
		$ticket_status_arr = isset($_POST['ticket_status_arr']) ? $_POST['ticket_status_arr'] : [];
		$class_arr = isset($_POST['class_arr']) ? $_POST['class_arr'] : [];
		$booking_from_arr = isset($_POST['booking_from_arr']) ? $_POST['booking_from_arr'] : [];
		$boarding_at_arr = isset($_POST['boarding_at_arr']) ? $_POST['boarding_at_arr'] : [];
		$arriving_datetime_arr = isset($_POST['arriving_datetime_arr']) ? $_POST['arriving_datetime_arr'] : [];

		$roundoff = $_POST['roundoff'];
		$reflections = json_decode(json_encode($_POST['reflections']));
		$bsmValues = json_decode(json_encode($_POST['bsmValues']));
		foreach ($bsmValues[0] as $key => $value) {
			switch ($key) {
				case 'basic':
					$basic_fair = ($value != "") ? $value : $basic_fair;
					break;
				case 'service':
					$service_charge = ($value != "") ? $value : $service_charge;
					break;
			}
		}
		$payment_due_date = get_date_db($payment_due_date);
		$booking_date = date('Y-m-d', strtotime($booking_date));
		$payment_date = date('Y-m-d', strtotime($payment_date));

		if ($payment_mode == "Cheque" || $payment_mode == "Credit Card") {
			$clearance_status = "Pending";
		} else {
			$clearance_status = "";
		}

		$financial_year_id = $_SESSION['financial_year_id'];

		begin_t();

		//Get Customer id
		if ($customer_id == '0') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
			$customer_id = $sq_max['max'];
		}

		//Invoice number reset to one in new financial year
		$sq_count = mysqli_num_rows(mysqlQuery("select entry_id from invoice_no_reset_master where service_name='train' and financial_year_id='$financial_year_id'"));
		if ($sq_count > 0) { // Already having bookings for this financial year

			$sq_invoice = mysqli_fetch_assoc(mysqlQuery("select max_booking_id from invoice_no_reset_master where service_name='train' and financial_year_id='$financial_year_id'"));
			$invoice_pr_id = $sq_invoice['max_booking_id'] + 1;
			$sq_invoice = mysqlQuery("update invoice_no_reset_master set max_booking_id = '$invoice_pr_id' where service_name='train' and financial_year_id='$financial_year_id'");
		} else { // This financial year's first booking
			// Get max entry_id of invoice_no_reset_master here
			$sq_entry_id = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as entry_id from invoice_no_reset_master"));
			$max_entry_id = $sq_entry_id['entry_id'] + 1;

			// Insert booking-id(1) for new financial_year only for first the time
			$sq_invoice = mysqlQuery("insert into invoice_no_reset_master(entry_id ,service_name, financial_year_id ,max_booking_id) values ('$max_entry_id','train','$financial_year_id','1')");
			$invoice_pr_id = 1;
		}
		//**Ticket save
		$sq_max = mysqli_fetch_assoc(mysqlQuery("SELECT max(train_ticket_id) as max from train_ticket_master"));
		$train_ticket_id = $sq_max['max'] + 1;
		$reflections = json_encode($reflections);
		$bsmValues = json_encode($bsmValues);
		$sq_ticket = mysqlQuery("INSERT INTO train_ticket_master (train_ticket_id, customer_id, branch_admin_id,financial_year_id, type_of_tour, basic_fair, service_charge, delivery_charges, gst_on, service_tax_subtotal, net_total, payment_due_date, created_at,emp_id,reflections,bsm_values,roundoff,invoice_pr_id) VALUES ('$train_ticket_id','$customer_id', '$branch_admin_id','$financial_year_id', '$type_of_tour', '$basic_fair', '$service_charge', '$delivery_charges', '$gst_on', '$service_tax_subtotal', '$net_total', '$payment_due_date', '$booking_date','$emp_id','$reflections','$bsmValues','$roundoff','$invoice_pr_id')");

		if (!$sq_ticket) {
			$GLOBALS['flag'] = false;
			echo "error--Sorry, Ticket not saved!";
		}

		$pax = 0;
		//**Ticket entries save
		for ($i = 0; $i < sizeof($first_name_arr); $i++) {

			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from train_ticket_master_entries"));
			$entry_id = $sq_max['max'] + 1;
			$birth_date_arr[$i] = get_date_db($birth_date_arr[$i]);

			$sq_entry = mysqlQuery("INSERT INTO train_ticket_master_entries (entry_id, train_ticket_id, honorific, first_name, middle_name, last_name, birth_date, adolescence, coach_number, seat_number, ticket_number) VALUES ('$entry_id', '$train_ticket_id', '$honorific_arr[$i]', '$first_name_arr[$i]', '$middle_name_arr[$i]', '$last_name_arr[$i]', '$birth_date_arr[$i]', '$adolescence_arr[$i]', '$coach_number_arr[$i]', '$seat_number_arr[$i]', '$ticket_number_arr[$i]')");
			if ($adolescence_arr[$i] != "Infant") {
				$pax++;
			}
			if (!$sq_entry) {
				$GLOBALS['flag'] = false;
				echo "error--Some entries not saved!";
			}
		}

		//**Trip Information
		for ($i = 0; $i < sizeof($travel_datetime_arr); $i++) {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from train_ticket_master_trip_entries"));
			$entry_id = $sq_max['max'] + 1;

			$travel_datetime_arr[$i] = isset($travel_datetime_arr[$i]) ? $travel_datetime_arr[$i] : '';
			$travel_from_arr[$i] = isset($travel_from_arr[$i]) ? $travel_from_arr[$i] : '';
			$travel_to_arr[$i] = isset($travel_to_arr[$i]) ? $travel_to_arr[$i] : '';
			$train_name_arr[$i] = isset($train_name_arr[$i]) ? $train_name_arr[$i] : '';
			$train_no_arr[$i] = isset($train_no_arr[$i]) ? $train_no_arr[$i] : '';
			$ticket_status_arr[$i] = isset($ticket_status_arr[$i]) ? $ticket_status_arr[$i] : '';
			$class_arr[$i] = isset($class_arr[$i]) ? $class_arr[$i] : '';
			$booking_from_arr[$i] = isset($booking_from_arr[$i]) ? $booking_from_arr[$i] : '';
			$boarding_at_arr[$i] = isset($boarding_at_arr[$i]) ? $boarding_at_arr[$i] : '';
			$arriving_datetime_arr[$i] = isset($arriving_datetime_arr[$i]) ? $arriving_datetime_arr[$i] : '';
			$travel_datetime_arr[$i] = get_datetime_db($travel_datetime_arr[$i]);
			$arriving_datetime_arr[$i] = get_datetime_db($arriving_datetime_arr[$i]);

			$sq_entry = mysqlQuery("INSERT INTO train_ticket_master_trip_entries (entry_id, train_ticket_id, travel_datetime, travel_from, travel_to, train_name, train_no, ticket_status, class, booking_from, boarding_at, arriving_datetime) VALUES ('$entry_id', '$train_ticket_id', '$travel_datetime_arr[$i]', '$travel_from_arr[$i]', '$travel_to_arr[$i]', '$train_name_arr[$i]', '$train_no_arr[$i]', '$ticket_status_arr[$i]', '$class_arr[$i]', '$booking_from_arr[$i]', '$boarding_at_arr[$i]', '$arriving_datetime_arr[$i]')");
			if ($i == 0)
				$sector = $travel_from_arr[$i] . '-' . $travel_to_arr[$i];
			if ($i > 0)
				$sector = $sector . ',' . $travel_from_arr[$i] . '-' . $travel_to_arr[$i];

			if (!$sq_entry) {
				$GLOBALS['flag'] = false;
				echo "error--Some entries not saved!";
			}
		}

		//**Payment section
		$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from train_ticket_payment_master"));
		$payment_id = $sq_max['max'] + 1;

		$sq_payment = mysqlQuery("insert into train_ticket_payment_master (payment_id, train_ticket_id, financial_year_id, branch_admin_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$train_ticket_id', '$financial_year_id', '$branch_admin_id', '$payment_date', '$payment_amount', '$payment_mode', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_charges','$credit_card_details') ");

		if (!$sq_payment) {
			$GLOBALS['flag'] = false;
			echo "error--Sorry, Payment not saved!";
		}

		//Update customer credit note balance
		$payment_amount1 = $payment_amount;
		if ($payment_mode == 'Credit Note') {

			$sq_credit_note = mysqlQuery("select * from credit_note_master where customer_id='$customer_id'");
			$i = 0;
			while ($row_credit = mysqli_fetch_assoc($sq_credit_note)) {
				if ($row_credit['payment_amount'] <= $payment_amount1 && $payment_amount1 != '0') {
					$payment_amount1 = $payment_amount1 - $row_credit['payment_amount'];
					$temp_amount = 0;
				} else {
					$temp_amount = $row_credit['payment_amount'] - $payment_amount1;
					$payment_amount1 = 0;
				}
				$sq_credit = mysqlQuery("update credit_note_master set payment_amount ='$temp_amount' where id='$row_credit[id]'");
			}
		}
		//Get Particular
		$particular = $this->get_particular($customer_id, $pax, $sector, $train_no_arr[0], $ticket_number_arr[0], $class_arr[0], $train_ticket_id);

		//Finance save                
		$this->finance_save($train_ticket_id, $payment_id, $row_spec, $branch_admin_id, $particular);

		if ($payment_mode != 'Credit Note') {
			//Bank and Cash Book Save
			$this->bank_cash_book_save($train_ticket_id, $payment_id, $branch_admin_id);
		}

		if ($GLOBALS['flag']) {
			commit_t();
			//Ticket Booking email send
			$this->ticket_booking_email_send($train_ticket_id, $payment_amount, $credit_charges);
			$this->booking_sms($train_ticket_id, $customer_id, $booking_date);

			//Ticket payment email send
			$train_ticket_payment_master  = new ticket_payment_master;
			$train_ticket_payment_master->payment_email_notification_send($train_ticket_id, $payment_amount, $payment_mode, $payment_date);

			//Ticket payment sms send
			if ($payment_amount != 0) {
				$train_ticket_payment_master->payment_sms_notification_send($train_ticket_id, $payment_amount, $payment_mode);
			}


			echo "Train Ticket Booking has been successfully saved-" . $train_ticket_id;
			exit;
		} else {
			rollback_t();
			exit;
		}
	}

	public function ticket_master_delete()
	{

		global $delete_master, $transaction_master;
		$train_ticket_id = $_POST['booking_id'];

		$deleted_date = date('Y-m-d');
		$row_spec = "sales";

		$row_ticket = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master where train_ticket_id='$train_ticket_id'"));

		$reflections = json_decode($row_ticket['reflections']);
		$service_tax_subtotal = $row_ticket['service_tax_subtotal'];
		$customer_id = $row_ticket['customer_id'];
		$booking_date = $row_ticket['created_at'];
		$net_total = $row_ticket['net_total'];
		$yr = explode("-", $booking_date);
		$year = $yr[0];

		$sq_ct = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		if ($sq_ct['type'] == 'Corporate' || $sq_ct['type'] == 'B2B') {
			$cust_name = $sq_ct['company_name'];
		} else {
			$cust_name = $sq_ct['first_name'] . ' ' . $sq_ct['last_name'];
		}
		global $transaction_master;

		$trans_id = get_train_ticket_booking_id($train_ticket_id, $year) . ' : ' . $cust_name;
		$transaction_master->updated_entries('Train Sale', $train_ticket_id, $trans_id, $net_total, 0);

		$sq_pax = mysqli_num_rows(mysqlQuery("select * from train_ticket_master_entries where train_ticket_id='$train_ticket_id' and adolescence!='Infant'"));
		$sq_ticket = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master_entries where train_ticket_id='$train_ticket_id'"));
		$sq_trip_ticket = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master_trip_entries where train_ticket_id='$train_ticket_id'"));

		$i = 0;
		$sq_ticket1 = mysqlQuery("select * from train_ticket_master_trip_entries where train_ticket_id='$train_ticket_id'");
		while ($row_ticket1 = mysqli_fetch_assoc($sq_ticket1)) {
			if ($i == 0)
				$sector = $row_ticket1['travel_from'] . '-' . $row_ticket1['travel_to'];
			if ($i > 0)
				$sector = $sector . ',' . $row_ticket1['travel_from'] . '-' . $row_ticket1['travel_to'];
			$i++;
		}
		$ticket_no = $sq_trip_ticket['train_no'];
		$ticket_number = $sq_ticket['ticket_number'];
		$class = $sq_trip_ticket['class'];
		$particular = $this->get_particular($customer_id, $sq_pax, $sector, $ticket_no, $ticket_number, $class, $train_ticket_id);

		$particular = $particular;
		$delete_master->delete_master_entries('Invoice', 'Train Ticket', $train_ticket_id, get_train_ticket_booking_id($train_ticket_id, $year), $cust_name, $row_ticket['net_total']);

		//Getting customer Ledger
		$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
		$cust_gl = $sq_cust['ledger_id'];

		////////////Sales/////////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$old_gl_id = $gl_id = 133;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		////////////service charge/////////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$old_gl_id = $gl_id = isset($reflections[0]->train_sc) ? $reflections[0]->train_sc : 189;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		/////////Service Charge Tax Amount////////
		// Eg. CGST:(9%):24.77, SGST:(9%):24.77
		$service_tax_subtotal = explode(',', $service_tax_subtotal);
		$tax_ledgers = isset($reflections[0]->train_taxes) ? explode(',', $reflections[0]->train_taxes) : [];
		for ($i = 0; $i < sizeof($service_tax_subtotal); $i++) {

			$ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

			$module_name = "Train Ticket Booking";
			$module_entry_id = $train_ticket_id;
			$transaction_id = "";
			$payment_amount = 0;
			$payment_date = $deleted_date;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
			$old_gl_id = $gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');
		}
		/////////Roundoff/////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$old_gl_id = $gl_id = 230;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		///////// Delivery charges //////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$old_gl_id = $gl_id = 33;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		////////Customer Amount//////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = 0;
		$payment_date = $deleted_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$old_gl_id = $gl_id = $cust_gl;
		$payment_side = "Debit";
		$clearance_status = "";
		$transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $old_gl_id, $gl_id, '', $payment_side, $clearance_status, $row_spec, $ledger_particular, 'INVOICE');

		$sq_delete = mysqlQuery("update train_ticket_master set basic_fair='0',service_charge='0',delivery_charges='0',service_tax_subtotal='',net_total='0',delete_status='1', roundoff='0' where train_ticket_id='$train_ticket_id' ");
		if ($sq_delete) {
			echo 'Entry deleted successfully!';
			exit;
		}
	}

	function get_particular($customer_id, $pax, $sector, $train_no, $ticket_number, $class, $train_ticket_id)
	{

		$sq_train = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master where train_ticket_id='$train_ticket_id'"));
		$booking_date = $sq_train['created_at'];
		$yr = explode("-", $booking_date);
		$year = $yr[0];
		$sq_pass = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master_entries where train_ticket_id='$sq_train[train_ticket_id]' and status!='Cancel'"));

		$sq_ct = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		$cust_name = $sq_ct['first_name'] . ' ' . $sq_ct['last_name'];
		return get_train_ticket_booking_id($train_ticket_id, $year) . ' and towards the train tkt of ' . $cust_name . '(' . $sq_pass['first_name'] . ' ' . $sq_pass['last_name'] . ') * ' . $pax . ' traveling for ' . $sector . ' against ticket no ' . $ticket_number . ' by ' . $train_no . '/' . $class;
	}

	public function finance_save($train_ticket_id, $payment_id, $row_spec, $branch_admin_id, $particular)
	{
		$customer_id = $_POST['customer_id'];
		$basic_fair = $_POST['basic_fair'];
		$service_charge = $_POST['service_charge'];
		$delivery_charges = $_POST['delivery_charges'];
		$service_tax_subtotal = $_POST['service_tax_subtotal'];
		$net_total = $_POST['net_total'];
		$payment_date = $_POST['payment_date'];
		$payment_amount1 = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$transaction_id1 = $_POST['transaction_id'];
		$booking_date = $_POST['booking_date'];
		$bank_id1 = $_POST['bank_id'];
		$roundoff = $_POST['roundoff'];
		$credit_charges = isset($_POST['credit_charges']) ? $_POST['credit_charges'] : 0;
		$credit_card_details = isset($_POST['credit_card_details']) ? $_POST['credit_card_details'] : '';

		$reflections = json_decode(json_encode($_POST['reflections']));
		$bsmValues = json_decode(json_encode($_POST['bsmValues']));
		$booking_date = date('Y-m-d', strtotime($booking_date));
		$payment_date1 = date('Y-m-d', strtotime($payment_date));
		$year1 = explode("-", $booking_date);
		$yr1 = $year1[0];
		foreach ($bsmValues[0] as $key => $value) {
			switch ($key) {
				case 'basic':
					$basic_fair = ($value != "") ? $value : $basic_fair;
					break;
				case 'service':
					$service_charge = ($value != "") ? $value : $service_charge;
					break;
			}
		}
		$train_sale_amount = $basic_fair;
		$payment_amount1 = (float)($payment_amount1) + (float)($credit_charges);

		//Get Customer id
		if ($customer_id == '0') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
			$customer_id = $sq_max['max'];
		}
		//Getting customer Ledger
		$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
		$cust_gl = $sq_cust['ledger_id'];

		//Getting cash/Bank Ledger
		if ($payment_mode == 'Cash') {
			$pay_gl = 20;
			$type = 'CASH RECEIPT';
		} else {
			$sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id1' and user_type='bank'"));
			$pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';
			$type = 'BANK RECEIPT';
		}

		global $transaction_master;
		////////////Sales/////////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = $train_sale_amount;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$gl_id = 133;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		/////////Service Charge////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = $service_charge;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$gl_id = isset($reflections[0]->train_sc) ? $reflections[0]->train_sc : 189;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		///////// Delivery charges //////////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = $delivery_charges;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$gl_id = 33;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		/////////Service Charge Tax Amount////////
		// Eg. CGST:(9%):24.77, SGST:(9%):24.77

		$service_tax_subtotal = explode(',', $service_tax_subtotal);
		$tax_ledgers = isset($reflections[0]->train_taxes) ? explode(',', $reflections[0]->train_taxes) : [];
		for ($i = 0; $i < sizeof($service_tax_subtotal); $i++) {

			$service_tax = explode(':', $service_tax_subtotal[$i]);
			$tax_amount = $service_tax[2];
			$ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

			$module_name = "Train Ticket Booking";
			$module_entry_id = $train_ticket_id;
			$transaction_id = "";
			$payment_amount = $tax_amount;
			$payment_date = $booking_date;
			$payment_particular = $particular;
			$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
			$gl_id = $ledger;
			$payment_side = "Credit";
			$clearance_status = "";
			$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '1', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');
		}

		////Roundoff Value
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = $roundoff;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$gl_id = 230;
		$payment_side = "Credit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');

		////////Customer Amount//////
		$module_name = "Train Ticket Booking";
		$module_entry_id = $train_ticket_id;
		$transaction_id = "";
		$payment_amount = $net_total;
		$payment_date = $booking_date;
		$payment_particular = $particular;
		$ledger_particular = get_ledger_particular('To', 'Train Ticket Sales');
		$gl_id = $cust_gl;
		$payment_side = "Debit";
		$clearance_status = "";
		$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, 'INVOICE');


		//////////Payment Amount///////////
		if ($payment_mode != 'Credit Note') {

			if ($payment_mode == 'Credit Card') {

				//////Customer Credit charges///////
				$module_name = "Train Ticket Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $credit_charges;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_train_ticket_booking_id($train_ticket_id, $yr1), $payment_date1, $credit_charges, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $cust_gl;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

				//////Credit charges ledger///////
				$module_name = "Train Ticket Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $credit_charges;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_train_ticket_booking_id($train_ticket_id, $yr1), $payment_date1, $credit_charges, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = 224;
				$payment_side = "Credit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

				//////Get Credit card company Ledger///////
				$credit_card_details = explode('-', $credit_card_details);
				$entry_id = $credit_card_details[0];
				$sq_cust1 = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$entry_id' and user_type='credit company'"));
				$company_gl = $sq_cust1['ledger_id'];
				//////Get Credit card company Charges///////
				$sq_credit_charges = mysqli_fetch_assoc(mysqlQuery("select * from credit_card_company where entry_id='$entry_id'"));
				//////company's credit card charges
				$company_card_charges = ($sq_credit_charges['charges_in'] == 'Flat') ? $sq_credit_charges['credit_card_charges'] : ($payment_amount1 * ($sq_credit_charges['credit_card_charges'] / 100));
				//////company's tax on credit card charges
				$tax_charges = ($sq_credit_charges['tax_charges_in'] == 'Flat') ? $sq_credit_charges['tax_on_credit_card_charges'] : ($company_card_charges * ($sq_credit_charges['tax_on_credit_card_charges'] / 100));
				$finance_charges = $company_card_charges + $tax_charges;
				$finance_charges = number_format($finance_charges, 2);
				$credit_company_amount = $payment_amount1 - $finance_charges;

				//////Finance charges ledger///////
				$module_name = "Train Ticket Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $finance_charges;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_train_ticket_booking_id($train_ticket_id, $yr1), $payment_date1, $finance_charges, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = 231;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				//////Credit company amount///////
				$module_name = "Train Ticket Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $credit_company_amount;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_train_ticket_booking_id($train_ticket_id, $yr1), $payment_date1, $credit_company_amount, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $company_gl;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
			} else {

				$module_name = "Train Ticket Booking Payment";
				$module_entry_id = $payment_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $payment_amount1;
				$payment_date = $payment_date1;
				$payment_particular = get_sales_paid_particular(get_train_ticket_booking_id($train_ticket_id, $yr1), $payment_date1, $payment_amount1, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id1, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $pay_gl;
				$payment_side = "Debit";
				$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
			}

			//////Customer Payment Amount///////
			$module_name = "Train Ticket Booking Payment";
			$module_entry_id = $payment_id;
			$transaction_id = $transaction_id1;
			$payment_amount = $payment_amount1;
			$payment_date = $payment_date1;
			$payment_particular = get_sales_paid_particular(get_train_ticket_booking_id($train_ticket_id, $yr1), $payment_date1, $payment_amount1, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id1, $transaction_id1);
			$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
			$gl_id = $cust_gl;
			$payment_side = "Credit";
			$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
			$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
		}
	}


	public function bank_cash_book_save($train_ticket_id, $payment_id, $branch_admin_id)
	{

		global $bank_cash_book_master;

		$customer_id = $_POST['customer_id'];
		$payment_date = $_POST['payment_date'];
		$payment_amount = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$bank_name = $_POST['bank_name'];
		$bank_id = $_POST['bank_id'];
		$transaction_id = $_POST['transaction_id'];
		$booking_date = $_POST['booking_date'];
		$credit_charges = isset($_POST['credit_charges']) ? $_POST['credit_charges'] : 0;
		$credit_card_details = isset($_POST['credit_card_details']) ? $_POST['credit_card_details'] : '';

		if ($payment_mode == 'Credit Card') {

			$payment_amount = $payment_amount + $credit_charges;
			$credit_card_details = explode('-', $credit_card_details);
			$entry_id = $credit_card_details[0];
			$sq_credit_charges = mysqli_fetch_assoc(mysqlQuery("select bank_id from credit_card_company where entry_id ='$entry_id'"));
			$bank_id = isset($sq_credit_charges['bank_id']) ? $sq_credit_charges['bank_id'] : '';
		}

		$payment_date = date('Y-m-d', strtotime($payment_date));
		$year2 = explode("-", $payment_date);
		$yr2 = $year2[0];

		$booking_date = date('Y-m-d', strtotime($booking_date));
		$year1 = explode("-", $booking_date);
		$yr1 = $year1[0];

		//Get Customer id
		if ($customer_id == '0') {
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
			$customer_id = $sq_max['max'];
		}

		$module_name = "Train Ticket Booking Payment";

		$module_entry_id = $payment_id;

		$payment_date = $payment_date;

		$payment_amount = $payment_amount;

		$payment_mode = $payment_mode;

		$bank_name = $bank_name;

		$transaction_id = $transaction_id;

		$bank_id = $bank_id;

		$particular = get_sales_paid_particular(get_train_ticket_booking_payment_id($payment_id, $yr2), $payment_date, $payment_amount, $customer_id, $payment_mode, get_train_ticket_booking_id($train_ticket_id, $yr1), $bank_id, $transaction_id);

		$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";

		$payment_side = "Debit";

		$payment_type = ($payment_mode == "Cash") ? "Cash" : "Bank";



		$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type, $branch_admin_id);
	}





	public function ticket_booking_email_send($train_ticket_id, $payment_amount, $credit_charges)

	{

		global $currency_logo, $encrypt_decrypt, $secret_key;

		$link = BASE_URL . 'view/customer';

		$sq_ticket = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master where train_ticket_id='$train_ticket_id'"));
		$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$sq_ticket[customer_id]'"));
		$customer_name = ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') ? $sq_customer['company_name'] : $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];

		$date = $sq_ticket['created_at'];
		$yr = explode("-", $date);
		$year = $yr[0];

		$email_id = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key);
		$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);

		$subject = 'Booking confirmation acknowledgement! (' . get_train_ticket_booking_id($train_ticket_id, $year) . ' )';

		$password = $email_id;
		$username = $contact_no;
		$balance_amount = $sq_ticket['net_total'] - $payment_amount;
		$content = '<tr>
		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
			<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Customer Name</td>   <td style="text-align:left;border: 1px solid #888888;">' . $customer_name . '</td></tr>
			<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Total Amount</td>   <td style="text-align:left;border: 1px solid #888888;">' . $currency_logo . ' ' . number_format(($sq_ticket['net_total'] + (float)($credit_charges)), 2) . '</td></tr>
		  	<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Paid Amount</td>   <td style="text-align:left;border: 1px solid #888888;">' . $currency_logo . ' ' . number_format(($payment_amount + (float)($credit_charges)), 2) . '</td></tr>
		  	<tr><td style="text-align:left;border: 1px solid #888888;width:50%">Balance Amount</td>   <td style="text-align:left;border: 1px solid #888888;">' . $currency_logo . ' ' . number_format($balance_amount, 2) . '</td></tr> 
		</table>
	</tr>';
		$TrDetails = mysqlQuery('SELECT * FROM `train_ticket_master_trip_entries` WHERE train_ticket_id = ' . $train_ticket_id);

		while ($rows = mysqli_fetch_assoc($TrDetails)) {
			$content .= '<tr>
		<table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
		  <tr><th colspan=2>Train Details</th></tr>
		  <tr><td style="text-align:left;border: 1px solid #888888;width:50%">From Location</td>   <td style="text-align:left;border: 1px solid #888888;" >' . $rows['travel_from'] . '</td></tr>
		  <tr><td style="text-align:left;border: 1px solid #888888;width:50%">To Location</td>   <td style="text-align:left;border: 1px solid #888888;">' . $rows['travel_to'] . '</td></tr> 
		  <tr><td style="text-align:left;border: 1px solid #888888;width:50%">Travel Date</td>   <td style="text-align:left;border: 1px solid #888888;">' . get_datetime_user($rows['travel_datetime']) . '</td></tr>
		</table>
	  </tr>';
		}
		$content .= mail_login_box($username, $password, $link);

		global $model, $backoffice_mail_id;
		$model->app_email_send('17', $customer_name, $email_id, $content, $subject);
		if ($backoffice_mail_id != "")
			$model->app_email_send('17', "Team", $backoffice_mail_id, $content, $subject);
	}
	public function employee_sign_up_mail($first_name, $last_name, $username, $password, $email_id)
	{
		$link = BASE_URL . 'view/customer';
		$content = mail_login_box($username, $password, $link);

		$subject = 'Welcome aboard!';
		global $model;
		$model->app_email_send('2', $first_name, $email_id, $content, $subject, '1');
	}
	public function booking_sms($booking_id, $customer_id, $created_at)
	{

		global $model, $encrypt_decrypt, $secret_key, $app_contact_no;
		$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		$mobile_no = $encrypt_decrypt->fnDecrypt($sq_customer_info['contact_no'], $secret_key);

		$message = "Dear " . $sq_customer_info['first_name'] . " " . $sq_customer_info['last_name'] . ", your Train ticket booking is confirmed. Ticket details will send you shortly. Please contact for more details " . $app_contact_no . " .";

		$model->send_message($mobile_no, $message);
	}
	public function whatsapp_send()
	{
		global $app_contact_no, $encrypt_decrypt, $secret_key, $app_name, $session_emp_id;

		$booking_date = $_POST['booking_date'];
		$customer_id = $_POST['customer_id'];

		if ($customer_id == '0') {
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM customer_master ORDER BY customer_id DESC LIMIT 1"));
		} else {
			$sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
		}
		if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') {
			$cust_name = $sq_customer['company_name'];
		} else {
			$cust_name = $sq_customer['first_name'] . ' ' . $sq_customer['last_name'];
		}
		$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);

		$sq_emp_info = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id= '$session_emp_id'"));
		if ($session_emp_id == 0) {
			$contact = $app_contact_no;
		} else {
			$contact = $sq_emp_info['mobile_no'];
		}

		$whatsapp_msg = rawurlencode('Dear ' . $cust_name . ',
Hope you are doing great. This is to inform you that your booking is confirmed with us. We look forward to provide you a great experience.
*Booking Date* : ' . get_date_user($booking_date) . '

Please contact for more details : ' . $app_name . ' ' . $contact);
		if ($customer_id == '0') {
			$username = $_POST['contact_no'];
			$password = $_POST['email_id'];
			$whatsapp_msg .= whatsapp_login_box($username, $password);
		}
		$whatsapp_msg .= '%0aThank%20you.%0a';
		$link = 'https://web.whatsapp.com/send?phone=' . $contact_no . '&text=' . $whatsapp_msg;
		echo $link;
	}
}
