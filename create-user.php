<?php 
	
	include('database.php');

	$db_connection = dataBase($db_host,$db_name,$db_user,$db_pass);
	$user_errors = array();

	class User {
		
		public $first_name;
		public $last_name;
		public $email;
		public $username;
		public $password;
		public $authority;
		public $created_date;

		public function User($first_name, $last_name, $email, $username, $password, $authority){
			date_default_timezone_set('UTC');
			$time = new DateTime();
			
			$this->first_name = $first_name;
			$this->last_name = $last_name;
			$this->email = $email;
			$this->username = $username;
			$this->password = $password;
			$this->authority = $authority;
			$this->created_date = $time->format('Y-m-d H:i:s');
			
		}

	}


	if(isset($_POST['firstname']) && ($_POST['firstname'] !== '')) {
		$input_firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
		// echo $input_firstname;
	} else {
		array_push($user_errors, "First name is not valid.");
	}
	
	if(isset($_POST['lastname']) && ($_POST['lastname'] !== '')) {
		$input_lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_STRING); 
	} else {
		array_push($user_errors, "Last name is not valid.");
	}

	if(isset($_POST['email']) && ($_POST['email'] !== '')) {
		$input_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL); 
	} else {
		array_push($user_errors, "E-mail is not valid.");
	}

	if(isset($_POST['username']) && ($_POST['username'] !== '')) {
		$input_username = filter_var($_POST['username'], FILTER_SANITIZE_STRING); 
	} else {
		array_push($user_errors, "Username is not valid.");
	}

	if(isset($_POST['password']) && ($_POST['password'] !== '')) {
		$input_password = ($_POST['password']);
		$hash_password = password_hash($input_password, PASSWORD_BCRYPT); 
	} else {
		array_push($user_errors, "Password is not valid.");
	}

	if(isset($_POST['authority']) && ($_POST['authority'] !== '')) {
		$input_authority = filter_var($_POST['authority'], FILTER_SANITIZE_STRING); 
	} else {
		array_push($user_errors, "Authority is not valid.");
	}


	//if no errors create a new User
	if($user_errors === ''){
	
		$user = new User($input_firstname, $input_lastname, $input_email, $input_username, $hash_password, $input_authority);
		var_dump($user);

		insertUser($db_connection,$user);

	} else {
		array_push($user_errors, "ERROR-Failed to create user.");
	}

	// if (password_verify("weird", $hash_password)) {
	//     echo "correct";
	// } else {
	//     echo "incorrect";
	// }

	displayUsers($db_connection);
	print_r(array_values($user_errors));

	function insertUser($dbConnection, $user){
		
		try {
			$stmt = $dbConnection->prepare('INSERT INTO users (first_name,last_name,username,password,email,authority,created_date) 
			  															VALUES (:first_name,:last_name,:username,:password,:email,:authority, :created_date)');
	  	$stmt->execute((array)$user);

		} catch (PDOException $e) {

			error_log($e);
			echo "ERROR - failed to create record.";
		// echo 'ERROR: '.$e->getMessage(); THIS IS BAD PER JESSE. Do not display database error messages to the browser!!!!!
		}
	}


	function displayUsers($dbConnection){
		try {
			$stmt = $dbConnection->query('SELECT first_name,last_name,username,password,email,authority,created_date FROM users');

			$stmt->setFetchMode(PDO::FETCH_ASSOC);

			while($row = $stmt->fetch()){
				echo "FIRST NAME: ". $row['first_name']. "<br />";
				echo "LAST NAME: ". $row['last_name']. "<br />";
				echo "USERNAME: ". $row['username']. "<br />";
				echo "PASSWORD: ". $row['password']. "<br />";
				echo "EMAIL: ". $row['email']. "<br />";
				echo "AUTHORITY: ". $row['authority']. "<br />";
				echo "CREATED DATE: ". $row['created_date']. "<br />";
				echo "<br /> <br />";
			}
		} catch (PDOException $e){
			error_log($e);
			echo "ERROR - failed to retrieve users.";
		}
	}

?>



