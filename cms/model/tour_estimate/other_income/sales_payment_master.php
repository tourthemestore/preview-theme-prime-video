<?php
$flag = true;
class sales_payment_master
{

	public function sales_payment_save()
	{
		$row_spec = 'sales';
		$customer_id = $_POST['customer_id'];
		$payment_date = $_POST['payment_date'];
		$payment_amount = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$bank_name = $_POST['bank_name'];
		$transaction_id = $_POST['transaction_id'];
		$branch_admin_id = $_POST['branch_admin_id'];
		$emp_id = $_POST['emp_id'];
		$bank_id = $_POST['bank_id'];

		$payment_amount_arr = $_POST['payment_amount_arr'];
		$purchase_type_arr = $_POST['purchase_type_arr'];
		$purchase_id_arr = $_POST['purchase_id_arr'];
		$credit_card_details = isset($_POST['credit_card_details']) ? $_POST['credit_card_details'] : '';

		$payment_date = date('Y-m-d', strtotime($payment_date));
		$year1 = explode("-", $payment_date);
		$yr1 = $year1[0];

		$created_at = date('Y-m-d H:i');

		if ($payment_mode == "Cheque" || $payment_mode == "Credit Card") {
			$clearance_status = "Pending";
		} else {
			$clearance_status = "";
		}

		$financial_year_id = $_SESSION['financial_year_id'];

		$invoice_no = '';
		$invoice_name = '';
		$module_entry_id = '';
		$credit_c = 0;
		begin_t();

		$payment_mode_new = ($payment_mode == '') ? 'Advance' : $payment_mode;
		for ($i = 0; $i < sizeof($purchase_type_arr); $i++) {

			if ($purchase_type_arr[$i] == 'Group Booking') {
				//Group Outstanding	
				$query = "select * from tourwise_traveler_details where 1 and id='$purchase_id_arr[$i]' and delete_status='0'";
				$sq_booking = mysqlQuery($query);
				while ($row_booking = mysqli_fetch_assoc($sq_booking)) {

					if ($payment_mode == 'Credit Card') {
						//calculate credit card charges
						$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
					}
					$sq = mysqlQuery("select max(payment_id) as max from payment_master");
					$value = mysqli_fetch_assoc($sq);
					$payment_id = $value['max'] + 1;

					$sq_payment = mysqlQuery(" insert into payment_master (payment_id, financial_year_id, branch_admin_id, emp_id, tourwise_traveler_id, date, payment_mode, amount, bank_name, transaction_id, payment_for, travel_type, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$financial_year_id', '$branch_admin_id', '$emp_id', '$purchase_id_arr[$i]', '$payment_date', '$payment_mode_new', '$payment_amount_arr[$i]', '$bank_name', '$transaction_id', 'Tour', '', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
					$invoice_no .= get_group_booking_id($purchase_id_arr[$i], $yr1) . ',';
					$invoice_name .= ' Group Booking Payment,';
					$module_entry_id .= $payment_id . ',';
					$invoice_no1 = get_group_booking_id($purchase_id_arr[$i], $yr1);
					//Bank and Cash Book Save
					if ($payment_mode != 'Advance') {
						$this->bank_cash_book_save($payment_id, 'Group Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
					}
				}
			}
			if ($purchase_type_arr[$i] == 'Package Booking') {
				//Package Outstanding	
				$query = "select * from package_tour_booking_master where 1 and booking_id='$purchase_id_arr[$i]' and delete_status='0'";
				$sq_booking = mysqlQuery($query);
				while ($row_booking = mysqli_fetch_assoc($sq_booking)) {

					if ($payment_mode == 'Credit Card') {
						//calculate credit card charges
						$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
					}

					$sq = mysqlQuery("SELECT max(payment_id) as max FROM package_payment_master");
					$value = mysqli_fetch_assoc($sq);
					$payment_id = $value['max'] + 1;
					$sq_payment = mysqlQuery(" insert into package_payment_master (payment_id, booking_id, financial_year_id, branch_admin_id, emp_id, date, payment_mode, amount, bank_name, transaction_id, payment_for, travel_type, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$financial_year_id', '$branch_admin_id', '$emp_id', '$payment_date', '$payment_mode_new', '$payment_amount_arr[$i]', '$bank_name', '$transaction_id', 'Tour', 'All', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
					$invoice_no .= get_package_booking_id($purchase_id_arr[$i], $yr1) . ',';
					$invoice_name .= ' Package Booking Payment,';
					$module_entry_id .= $payment_id . ',';
					$invoice_no1 = get_package_booking_id($purchase_id_arr[$i], $yr1);
					//Bank and Cash Book Save
					if ($payment_mode != 'Advance') {
						$this->bank_cash_book_save($payment_id, 'Package Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
					}
				}
			}
			if ($purchase_type_arr[$i] == 'B2B Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2b_payment_master"));
				$entry_id = $sq_max['max'] + 1;
				$payment_id = uniqid();

				$sq_payment = mysqlQuery("insert into b2b_payment_master (`entry_id`, `payment_id`, `booking_id`, `branch_admin_id`, `financial_year_id`, `payment_date`, `payment_amount`, `payment_mode`, `bank_name`, `transaction_id`, `bank_id`, `clearance_status`,credit_charges,credit_card_details) values ('$entry_id','$payment_id' ,'$purchase_id_arr[$i]', '1', '$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");

				$invoice_no .= get_b2b_booking_id($purchase_id_arr[$i], $yr1);
				$invoice_name .= ' B2B Booking,';
				$module_entry_id .= $purchase_id_arr[$i] . ',';
				$invoice_no1 = get_b2b_booking_id($purchase_id_arr[$i], $yr1);
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($entry_id, 'B2B Booking', $purchase_id_arr[$i], $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'B2C Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2c_payment_master"));
				$entry_id = $sq_max['max'] + 1;
				$payment_id = uniqid();

				$sq_payment = mysqlQuery("insert into b2c_payment_master (`entry_id`, `payment_id`, `booking_id`, `branch_admin_id`, `financial_year_id`, `payment_date`, `payment_amount`, `payment_mode`, `bank_name`, `transaction_id`, `bank_id`, `clearance_status`,credit_charges,credit_card_details) values ('$entry_id','$payment_id' ,'$purchase_id_arr[$i]', '1', '$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");

				$invoice_no .= get_b2c_booking_id($purchase_id_arr[$i], $yr1);
				$invoice_name .= ' B2C Booking,';
				$module_entry_id .= $purchase_id_arr[$i] . ',';
				$invoice_no1 = get_b2c_booking_id($purchase_id_arr[$i], $yr1);
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($entry_id, 'B2C Booking', $purchase_id_arr[$i], $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Visa Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from visa_payment_master"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into visa_payment_master (payment_id, visa_id, branch_admin_id, financial_year_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$branch_admin_id', '$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
				$invoice_no .= get_visa_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_name .= ' Visa Booking Payment,';
				$module_entry_id .= $payment_id . ',';
				$invoice_no1 = get_visa_booking_id($purchase_id_arr[$i], $yr1);
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Visa Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}

			if ($purchase_type_arr[$i] == 'Miscellaneous Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from miscellaneous_payment_master"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into miscellaneous_payment_master (payment_id, misc_id, branch_admin_id, financial_year_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$branch_admin_id', '$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
				$invoice_no .= get_misc_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_name .= ' Miscellaneous Booking Payment,';
				$module_entry_id .= $payment_id . ',';
				$invoice_no1 = get_misc_booking_id($purchase_id_arr[$i], $yr1);
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Miscellaneous Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Air Ticket Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from ticket_payment_master"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into ticket_payment_master (payment_id, branch_admin_id, ticket_id, financial_year_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$branch_admin_id', '$purchase_id_arr[$i]', '$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
				$invoice_no .= get_ticket_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_name .= ' Air Ticket Booking Payment,';
				$module_entry_id .= $payment_id . ',';
				$invoice_no1 = get_ticket_booking_id($purchase_id_arr[$i], $yr1);
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Air Ticket Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Train Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from train_ticket_payment_master"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into train_ticket_payment_master (payment_id, train_ticket_id, branch_admin_id, financial_year_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$branch_admin_id', '$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
				$invoice_no .= get_train_ticket_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_name .= ' Train Ticket Booking Payment,';
				$module_entry_id .= $payment_id . ',';
				$invoice_no1 = get_train_ticket_booking_id($purchase_id_arr[$i], $yr1);
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Train Ticket Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Hotel Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from hotel_booking_payment"));
				$payment_id = $sq_max['max'] + 1;
				$sq_payment = mysqlQuery("insert into hotel_booking_payment(payment_id, booking_id, branch_admin_id, financial_year_id, payment_date, payment_mode, payment_amount, bank_name, transaction_id, bank_id, clearance_status, created_at,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$branch_admin_id', '$financial_year_id', '$payment_date', '$payment_mode_new', '$payment_amount_arr[$i]', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status', '$created_at','$credit_c','$credit_card_details')");
				$invoice_no .= get_hotel_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_no1 = get_hotel_booking_id($purchase_id_arr[$i], $yr1);
				$module_entry_id .= $payment_id . ',';
				$invoice_name .= ' Hotel Booking Payment,';
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Hotel Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Bus Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from bus_booking_payment_master"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into bus_booking_payment_master (payment_id, booking_id, branch_admin_id, financial_year_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$branch_admin_id','$financial_year_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
				$invoice_no .= get_bus_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_no1 = get_bus_booking_id($purchase_id_arr[$i], $yr1);
				$invoice_name .= ' Bus Booking Payment,';
				$module_entry_id .= $payment_id . ',';
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Bus Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Car Rental Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from car_rental_payment"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into car_rental_payment(payment_id, booking_id, financial_year_id, emp_id, branch_admin_id,  payment_date, payment_mode, payment_amount, bank_name, transaction_id, bank_id, clearance_status, created_at,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$financial_year_id', '$emp_id', '$branch_admin_id', '$payment_date', '$payment_mode_new', '$payment_amount_arr[$i]', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status', '$created_at','$credit_c','$credit_card_details')");
				$invoice_no .= get_car_rental_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_name .= 'Car Rental Booking Payment,';
				$invoice_no1 = get_car_rental_booking_id($purchase_id_arr[$i], $yr1);
				$module_entry_id .= $payment_id . ',';
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Car Rental Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
			if ($purchase_type_arr[$i] == 'Activity Booking') {

				if ($payment_mode == 'Credit Card') {
					//calculate credit card charges
					$credit_c = $this->calculate_credit_charges($payment_amount_arr[$i]);
				}
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(payment_id) as max from exc_payment_master"));
				$payment_id = $sq_max['max'] + 1;

				$sq_payment = mysqlQuery("insert into exc_payment_master (payment_id, exc_id, financial_year_id, branch_admin_id, payment_date, payment_amount, payment_mode, bank_name, transaction_id, bank_id, clearance_status,credit_charges,credit_card_details) values ('$payment_id', '$purchase_id_arr[$i]', '$financial_year_id', '$branch_admin_id', '$payment_date', '$payment_amount_arr[$i]', '$payment_mode_new', '$bank_name', '$transaction_id', '$bank_id', '$clearance_status','$credit_c','$credit_card_details') ");
				$invoice_no .= get_exc_booking_id($purchase_id_arr[$i], $yr1) . ',';
				$invoice_name .= ' Excursion Booking Payment,';
				$invoice_no1 = get_exc_booking_id($purchase_id_arr[$i], $yr1);
				$module_entry_id .= $payment_id . ',';
				//Bank and Cash Book Save
				if ($payment_mode != 'Advance') {
					$this->bank_cash_book_save($payment_id, 'Excursion Booking Payment', $payment_id, $payment_amount_arr[$i], $invoice_no1, $credit_c);
				}
			}
		}
		if ($payment_mode == "Credit Note") {
			//Update customer credit note balance
			$payment_amount1 = $payment_amount;
			$sq_credit_note = mysqlQuery("select * from credit_note_master where customer_id='$customer_id'");
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

		if (!$sq_payment) {
			rollback_t();
			echo "error--Sorry, Receipt not saved!";
			exit;
		} else {
			//Finance Save
			if ($payment_mode != 'Advance') {
				$this->finance_save($payment_id, $invoice_no, $invoice_name, $module_entry_id, $row_spec);
			}
			if ($GLOBALS['flag']) {
				commit_t();
				echo "Receipt has been successfully saved.";
				exit;
			}
		}
	}

	public function finance_save($payment_id, $invoice_no, $invoice_name, $module_entry_id, $row_spec)
	{
		global $transaction_master;
		$branch_admin_id = $_SESSION['branch_admin_id'];
		$customer_id = $_POST['customer_id'];
		$advance_nullify = $_POST['advance_nullify'];
		$total_payment_amount = $_POST['total_payment_amount'];
		$total_purchase = $_POST['total_purchase'];
		$payment_date = $_POST['payment_date'];
		$payment_amount1 = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$transaction_id1 = $_POST['transaction_id'];
		$bank_id = $_POST['bank_id'];

		$credit_charges = isset($_POST['credit_charges']) ? $_POST['credit_charges'] : 0;
		$credit_card_details = isset($_POST['credit_card_details']) ? $_POST['credit_card_details'] : '';

		$payment_date = date('Y-m-d', strtotime($payment_date));

		$total_payment_amount = $total_payment_amount;
		$payment_amount1 = (float)($payment_amount1) + (float)($credit_charges);
		//Getting cash/Bank Ledger
		if ($payment_mode == 'Cash') {
			$pay_gl = 20;
			$type = 'CASH RECEIPT';
		} else {
			$sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
			$pay_gl = isset($sq_bank['ledger_id']) ? $sq_bank['ledger_id'] : '';
			$type = 'BANK RECEIPT';
		}

		//Getting customer Ledger
		$sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer' and group_sub_id='20'"));
		$cust_gl = isset($sq_cust['ledger_id']) ? $sq_cust['ledger_id'] : '';

		//customer Advance Ledger
		$sq_supp = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer' and group_sub_id='22'"));
		$cust_adv_gl = isset($sq_supp['ledger_id']) ? $sq_supp['ledger_id'] : '';

		//Refl
		if ($payment_mode != 'Credit Note') {

			if ($payment_mode == 'Credit Card')
				$cust_amount = $total_payment_amount;
			else
				$cust_amount = $total_purchase;

			if ($total_payment_amount > $total_purchase) {

				$balance_amount = $total_payment_amount - $total_purchase;
				////////Customer Amount//////   
				$module_name = $invoice_name;
				$module_entry_id = $module_entry_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $cust_amount;
				$payment_date = $payment_date;
				$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $cust_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $cust_gl;
				$payment_side = "Credit";
				$clearance_status = "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);


				if ($payment_mode == 'Credit Card') {

					//////Customer Credit charges///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $cust_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

					//////Customer Credit charges Credit///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $cust_gl;
					$payment_side = "Credit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

					//////Credit charges ledger///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
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
					$finance_charges = (float)($company_card_charges) + (float)($tax_charges);
					$finance_charges = number_format($finance_charges, 2);
					$credit_company_amount = $payment_amount1 - $finance_charges;

					//////Finance charges ledger///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $finance_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $finance_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = 231;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					//////Credit company amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_company_amount;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_company_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $company_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				} else {
					//////Payment Amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $payment_amount1;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $payment_amount1, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $pay_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode != "Cash") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					//////Advance Nullify Amount///////
					if ($advance_nullify != '0') {
						$module_name = $invoice_name;
						$module_entry_id = $module_entry_id;
						$transaction_id = $transaction_id1;
						$payment_amount = $advance_nullify;
						$payment_date = $payment_date;
						$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $advance_nullify, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
						$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
						$gl_id = $cust_adv_gl;
						$payment_side = "Debit";
						$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
						$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					}
					if ($cust_adv_gl != '') {
						$module_name = $invoice_name;
						$module_entry_id = $module_entry_id;
						$transaction_id = $transaction_id1;
						$payment_amount = $balance_amount;
						$payment_date = $payment_date;
						$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $balance_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
						$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
						$gl_id = $cust_adv_gl;
						$payment_side = "Credit";
						$clearance_status = ($payment_mode != "Cash") ? "Pending" : "";
						$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					} else {
						$module_name = $invoice_name;
						$module_entry_id = $module_entry_id;
						$transaction_id = $transaction_id1;
						$payment_amount = $balance_amount;
						$payment_date = $payment_date;
						$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $balance_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
						$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
						$gl_id = $cust_gl;
						$payment_side = "Credit";
						$clearance_status = ($payment_mode != "Cash") ? "Pending" : "";
						$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					}
				}
			} else if ($total_payment_amount < $total_purchase) {

				////////Customer Amount//////   
				$module_name = $invoice_name;
				$module_entry_id = $module_entry_id;
				$transaction_id = $transaction_id1;
				$payment_amount = (float)($total_payment_amount) + (float)($credit_charges);
				$payment_date = $payment_date;
				$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $total_payment_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $cust_gl;
				$payment_side = "Credit";
				$clearance_status = "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

				//////Advance Nullify Amount///////
				if ($advance_nullify != '0') {
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $advance_nullify;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $advance_nullify, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $cust_adv_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				}

				if ($payment_mode == 'Credit Card') {

					//////Customer Credit charges///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $cust_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

					//////Credit charges ledger///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
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
					$finance_charges = (float)($company_card_charges) + (float)($tax_charges);
					$finance_charges = number_format($finance_charges, 2);
					$credit_company_amount = $payment_amount1 - $finance_charges;

					//////Finance charges ledger///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $finance_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $finance_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = 231;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					//////Credit company amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_company_amount;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_company_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $company_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				} else {
					//////Payment Amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $payment_amount1;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $payment_amount1, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $pay_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				}
			} else {

				////////Customer Amount//////   
				$module_name = $invoice_name;
				$module_entry_id = $module_entry_id;
				$transaction_id = $transaction_id1;
				$payment_amount = $payment_amount1;
				$payment_date = $payment_date;
				$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $payment_amount1, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
				$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
				$gl_id = $cust_gl;
				$payment_side = "Credit";
				$clearance_status = "";
				$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

				if ($advance_nullify != '0') {
					//////Advance Nullify Amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $advance_nullify;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $advance_nullify, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $cust_adv_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				}
				if ($payment_mode == 'Credit Card') {

					//////Customer Credit charges///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $cust_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);

					//////Credit charges ledger///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
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
					$finance_charges = (float)($company_card_charges) + (float)($tax_charges);
					$credit_company_amount = (float)($payment_amount1) - (float)($finance_charges);
					$finance_charges = number_format($finance_charges, 2);

					//////Finance charges ledger///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $finance_charges;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $finance_charges, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = 231;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
					//////Credit company amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $credit_company_amount;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $credit_company_amount, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $company_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				} else {
					//////Payment Amount///////
					$module_name = $invoice_name;
					$module_entry_id = $module_entry_id;
					$transaction_id = $transaction_id1;
					$payment_amount = $payment_amount1;
					$payment_date = $payment_date;
					$payment_particular = get_sales_paid_particular($invoice_name, $payment_date, $payment_amount1, $customer_id, $payment_mode, $invoice_no, $bank_id, $transaction_id1);
					$ledger_particular = get_ledger_particular('By', 'Cash/Bank');
					$gl_id = $pay_gl;
					$payment_side = "Debit";
					$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
					$transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec, $branch_admin_id, $ledger_particular, $type);
				}
			}
		}
	}

	public function bank_cash_book_save($payment_id, $invoice_name, $module_entry_id1, $pay_amount, $invoice_no1, $credit_c)
	{
		$customer_id = $_POST['customer_id'];
		$payment_date = $_POST['payment_date'];
		$payment_amount = $_POST['payment_amount'];
		$payment_mode = $_POST['payment_mode'];
		$bank_name = $_POST['bank_name'];
		$transaction_id = $_POST['transaction_id'];
		$bank_id = $_POST['bank_id'];
		$credit_card_details = $_POST['credit_card_details'];

		$payment_date = date('Y-m-d', strtotime($payment_date));

		global $bank_cash_book_master;
		if ($payment_mode != 'Credit Note') {

			if ($payment_mode == 'Credit Card') {

				$pay_amount = (float)($pay_amount) + (float)($credit_c);
				$credit_card_details = explode('-', $credit_card_details);
				$entry_id = $credit_card_details[0];
				$sq_credit_charges = mysqli_fetch_assoc(mysqlQuery("select bank_id from credit_card_company where entry_id ='$entry_id'"));
				$bank_id = $sq_credit_charges['bank_id'];
			}
			$module_name = $invoice_name;
			$module_entry_id = $payment_id;
			$payment_date = $payment_date;
			$payment_amount = $pay_amount;
			$payment_mode = $payment_mode;
			$bank_name = $bank_name;
			$transaction_id = $transaction_id;
			$bank_id = $bank_id;
			$particular = get_sales_paid_particular($invoice_name, $payment_date, $pay_amount, $customer_id, $payment_mode, $invoice_no1, $bank_id, $transaction_id);
			$clearance_status = ($payment_mode == "Cheque" || $payment_mode == "Credit Card") ? "Pending" : "";
			$payment_side = "Debit";
			$payment_type = ($payment_mode == "Cash") ? "Cash" : "Bank";

			$bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type);
		}
	}
	public function calculate_credit_charges($amount)
	{

		global $app_credit_charge;

		$result = $amount * ($app_credit_charge / 100);
		return $result;
	}
}
