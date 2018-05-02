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

    $memberCount = 0;

    $sql = "SELECT COUNT(*) FROM members";
    if($result = $mysqli->query($sql)) {
        $row = $result->fetch_row();
        $result->close();
        $memberCount = $row[0];
    }

    $members = array();

    $sql = "SELECT id, firstName, lastName FROM members ORDER BY firstName";
    if($result = $mysqli->query($sql)) {
        while($person = $result->fetch_assoc()) {
            array_push($members, array('id' => $person['id'],
                                      'firstName' => $person['firstName'],
                                      'lastName' => $person['lastName']));
        }
        $result->close();
    }

    $mysqli->close();

    $json = array('status' => $status,
                 'memberCount' => $memberCount,
                 'members' => $members);

    $json = json_encode($json);
    echo $json;
    die();
?>