<?php
    function errorAndDie() {
        global $status;
        global $error;
        global $mysqli;
        global $availSetup;
        global $availCleanup;
        global $availMonitor;
        global $availDrive;
        
        $mysqli->close();
        
        $json = array('status' => $status,
                     'error' => $error);
        $json = json_encode($json);

        echo $json;
        die();
    }
?>