<?php
    require('globalFunctions.php');

    $id = empty($_POST['id']) ? -1 : $_POST['id'];
    $firstName = empty($_POST['firstName']) ? -1 : $_POST['firstName'];
    $lastName = empty($_POST['lastName']) ? -1 : $_POST['lastName'];
    $setup = empty($_POST['setup']) ? -1 : $_POST['setup'];
    $cleanup = empty($_POST['cleanup']) ? -1 : $_POST['cleanup'];
    $monitor = empty($_POST['monitor']) ? -1 : $_POST['monitor'];
    $drive = empty($_POST['drive']) ? -1 : $_POST['drive'];

    $status = 'OK';
    $error = '';

    require('db_credentials.php');
    $mysqli = new mysqli($servername, $username, $password, $dbname);
    if ($mysqli->connect_error) {
        $status = 'error';
        $error = ' Failed to connect to database: ' . $mysqli->connect_error;
        errorAndDie();
    }

    $firstName = $mysqli->real_escape_string($firstName);
    $lastName = $mysqli->real_escape_string($lastName);
    $setup = $mysqli->real_escape_string($setup);
    $cleanup = $mysqli->real_escape_string($cleanup);
    $monitor = $mysqli->real_escape_string($monitor);
    $drive = $mysqli->real_escape_string($drive);

    if($id == -1 or $firstName == -1 or $lastName == -1 or $setup == -1 or $cleanup == -1 or $monitor == -1 or $drive == -1) {
        $status = 'error';
        $error = 'Empty form elements!';
        errorAndDie();
    }

    $sql = "UPDATE members SET firstName='$firstName', lastName='$lastName', setup='$setup', cleanup='$cleanup', monitor='$monitor', drive='$drive' WHERE id=$id";
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