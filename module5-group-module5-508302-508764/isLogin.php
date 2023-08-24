<?php
    //returns json data that shows if the user logs in or not and pass useful values
    session_start();
    if(isset($_SESSION['username'])){
        echo json_encode(array(
            'login' => true,
            'userName' => htmlentities($_SESSION['username']),
            'userId' => htmlentities($_SESSION['user_id']),
            'token' => htmlentities($_SESSION['token'])
        ));
    }else{
        echo json_encode(array(
            "login" => false
        ));
    }
?>