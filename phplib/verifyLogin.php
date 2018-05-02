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

    $location = empty($_GET['location']) ? '' : $_GET['location'];
    
    if(isset($_SESSION['logged_in']) and $location != 'login') {
        $user = $_SESSION['logged_in'];
        $json = array('status' => 'OK',
                      'user' => $user
                     );
        $json = json_encode($json);
        echo $json;
    }else if(isset($_SESSION['logged_in']) and $location === 'login'){
        $user = $_SESSION['logged_in'];
        $json = array('status' => 'OK',
                      'redirect' => "/eventpicker/",
                      'user' => $user
                     );
        $json = json_encode($json);
        echo $json;
    }else if(!isset($_SESSION['logged_in']) and ($location == 'picker' or $location == 'manage' or $location == 'adduser')){
        $json = array('status' => 'error',
                      'redirect' => "/eventpicker/login/"
                     );
        $json = json_encode($json);
        echo $json;
    }else {
        $json = array('status' => 'OK',
                      'user' => null
                     );
        $json = json_encode($json);
        echo $json;
    }
?>