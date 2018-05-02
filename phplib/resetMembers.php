<?php
    require('globalFunctions.php');

    $status = 'OK';
    $error = '';

    require('db_credentials.php');
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        $status = 'error';
        $error = ' Failed to connect to database: ' . $mysqli->connect_error;
        errorAndDie();
    }

    $sql = "UPDATE members SET setup='No', cleanup='No', monitor='No', drive='No'";
    if($result = $mysqli->query($sql)) {
        $json = array('status' => $status);

        $json = json_encode($json);

        echo $json;
    }else {
        $status = 'error';
        $error = 'Query Failed: ' . $mysqli->error;
        errorAndDie();
    }
?>