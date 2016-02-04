<?php
	
	include('database.php');
	
	$ticket_errors = array();
	$db_connection = dataBase($db_host,$db_name,$db_user,$db_pass);
	
	class Ticket {
		
		public $submitted_date;
		public $submitted_by;
		public $assigned_to;
		public $status;
		public $subject;
		public $description;

		public function Ticket($user, $subj, $desc){
			date_default_timezone_set('UTC');
			$time = new DateTime();
			

			$this->submitted_date = $time->format('Y-m-d H:i:s');
			$this->assigned_to = "unassigned";
			$this->status = "new";
			$this->submitted_by = $user;
			$this->subject = $subj;
			$this->description = $desc;
			
		}

	}

	if($_POST['username'] !== '') {
		$input_name = filter_var($_POST['username'], FILTER_SANITIZE_STRING); 
	} else {
		array_push($ticket_errors, "Username is not valid.");
	}
	
	if($_POST['subject'] !== '') {
		$input_subj = filter_var($_POST['subject'], FILTER_SANITIZE_STRING); 
	} else {
		array_push($ticket_errors, "Subject is not valid.");
	}

	if($_POST['description'] != '') {
		$input_desc = filter_var($_POST['description'], FILTER_SANITIZE_STRING); 
	} else {
		array_push($ticket_errors, "Description is not valid.");
	}

	

	if($ticket_errors === ''){
	
		$ticket = new Ticket($input_name, $input_subj, $input_desc);
		var_dump($ticket);

		insertTicket($db_connection,$ticket);

	} else {
		array_push($ticket_errors, "ERROR-Failed to create new ticket.");
	}

	displayTickets($db_connection);

	print_r(array_values($ticket_errors));

	

	//Accepts a database connection  and a ticket and attempts to insert a ticket into the database
	function insertTicket($dbConnection, $ticket){
		
		try {
			$stmt = $dbConnection->prepare('INSERT INTO tickets (submitted_date,submitted_by,assigned_to,subject,description,status) 
			  															VALUES (:submitted_date,:submitted_by,:assigned_to,:subject,:description,:status)');
	  	$stmt->execute((array)$ticket);

		} catch (PDOException $e) {

			error_log($e);
			echo "ERROR - failed to create record.";
		// echo 'ERROR: '.$e->getMessage(); THIS IS BAD PER JESSE. Do not display database error messages to the browser!!!!!
		}
	}


	function displayTickets($dbConnection){
		try {
			$stmt = $dbConnection->query('SELECT submitted_date,submitted_by,assigned_to,subject,description,status FROM tickets');

			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($row = $stmt->fetch()){
				echo "DATE/TIME: ". $row['submitted_date']. "<br />";
				echo "SUBMITTED BY: ". $row['submitted_by']. "<br />";
				echo "ASSIGNED TO: ". $row['assigned_to']. "<br />";
				echo "SUBJECT: ". $row['subject']. "<br />";
				echo "DESCRIPTION: ". $row['description']. "<br />";
				echo "STATUS: ". $row['status']. "<br />";
				echo "<br /> <br />";
			}
		} catch (PDOException $e){
			error_log($e);
			echo "ERROR - failed to retrieve ticket.";
		}
	}

?>