<?php
    require('globalFunctions.php');

    $id = empty($_POST['id']) ? -1 : $_POST['id'];

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
        $error = 'Empty user ID!';
        errorAndDie();
    }

    $id = $mysqli->real_escape_string($id);

    $sql = "DELETE FROM members WHERE id=$id";
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