<?php
    if(!session_start()) {
		// If the session couldn't start, present an error
		$json = array('error' => 'Could not start the session. Please contact the network administrator.',
                      'status' => 'error'
                     );
        $json = json_encode($json);
        echo $json;
		die();
	}
	
	// Check to see if the user has already logged in
	$loggedIn = empty($_SESSION['logged_in']) ? false : $_SESSION['logged_in'];
	
	if ($loggedIn) {
		$json = array('status' => 'OK',
                      'message' => 'Already logged in.');
        $json = json_encode($json);
        echo $json;
        die();
	}

    require("db_credentials.php");
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        $status = 'error';
        $error = ' Failed to connect to database: ' . $mysqli->connect_error;
        errorAndDie();
    }

    $username = empty($_POST['username']) ? '' : $_POST['username'];
    $password = empty($_POST['password']) ? '' : $_POST['password'];
    $hashed_pass = '';

    $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
    $result = $mysqli->query($sql);

    if($user = $result->fetch_assoc()) {
        $hashed_pass = $user['password'];
    }else {
        loginError();
    }
    $result->close();
    $mysqli->close();

    if (password_verify($password, $hashed_pass)) {
        $_SESSION['logged_in'] = $username;
		$json = array('status' => 'OK',
                      'message' => 'Successfully logged in.'
                     );
        $json = json_encode($json);
        echo $json;
        die();
    }else {
        loginError();
    }

    function loginError() {
        global $username;
        global $password;
        global $hashed_pass;
        global $session_path;
        
        $json = array('status' => 'error',
                      'error' => 'Incorrect login credentials.',
                     );
        $json = json_encode($json);
        echo $json;
        die();
    }
?>