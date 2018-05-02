<?php
    $user_username = empty($_POST['username']) ? false : $_POST['username'];
    $user_pass = empty($_POST['password']) ? false : $_POST['password'];
    $user_confirm_pass = empty($_POST['confirm_password']) ? false : $_POST['confirm_password'];

    if(!$user_username or !user_pass or !$user_confirm_pass) {
        $json = array('status' => 'error',
                      'error' => 'Please fill out all of the fields in the form!');
        $json = json_encode($json);
        echo $json;
        die();
    }

    if(strcmp($user_pass, $user_confirm_pass) != 0) {
        $json = array('status' => 'error',
                      'error' => 'Passwords did not match!');
        $json = json_encode($json);
        echo $json;
        die();
    }

    $user_pass_hashed = password_hash($user_pass, PASSWORD_DEFAULT);

    require("db_credentials.php");

    $mysqli = new mysqli($servername, $username, $password, $dbname);

    $mysqli->query("INSERT INTO users (username, password) VALUES ('$user_username', '$user_pass_hashed')");
    
    if($mysqli->error) {
        $json = array('status' => 'error',
                      'error' => "Error adding the user $username to the database: $mysqli->error");
        $json = json_encode($json);
        echo $json;
    }else {
        $json = array('status' => 'OK',
                      'user' => $user_username);
        $json = json_encode($json);
        echo $json;
    }

    $mysqli->close();
?>