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
		$_SESSION = array();
        session_destroy();
        header('Location: /eventpicker/');
	}
?>