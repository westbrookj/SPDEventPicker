<?php
    require('globalFunctions.php');

    $id = empty($_GET['id']) ? -1 : $_GET['id'];

    $status = 'OK';
    $error = '';

    require('db_credentials.php');
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        $status = 'error';
        $error = ' Failed to connect to database: ' . $mysqli->connect_error;
        errorAndDie();
    }

    if($id == -1) {
        $status = 'error';
        $error = 'No ID supplied!';
        errorAndDie();
    }

    $memberDetails = array();

    $sql = "SELECT * FROM members WHERE id=$id";
    if($result = $mysqli->query($sql)) {
        $member = $result->fetch_assoc();
        $result->close();
        
        $memberDetails['firstName'] = $member['firstName'];
        $memberDetails['lastName'] = $member['lastName'];
        $memberDetails['setup'] = $member['setup'];
        $memberDetails['cleanup'] = $member['cleanup'];
        $memberDetails['monitor'] = $member['monitor'];
        $memberDetails['drive'] = $member['drive'];
    }else {
        $status = 'error';
        $error = 'Query Failed: ' . $mysqli->error;
        errorAndDie();
    }

    $json = array('status' => $status,
                 'memberDetails' => $memberDetails);

    $json = json_encode($json);

    echo $json;
?>