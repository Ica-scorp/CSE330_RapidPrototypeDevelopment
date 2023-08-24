<?php
    //this file allows users to add other users as coauthors of one event they create
    session_start();
    require 'database.php';
    header("Content-Type: application/json"); 
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    //if not logged in, pass failure value
    if(!isset($_SESSION['user_id'])){
        echo json_encode(array(
            "success" => false,
            "message" => "Not Logged In"
        ));
        exit();
    }

    if (empty($json_str)){
        exit();
    }

    $groupmemberlist=$json_obj['memberlist'];
    $eventTitle = $json_obj['etitle'];
    $eventContent = $json_obj['econtent'];
    $formatted_date=$json_obj['edate'];
    $eventStartTime=$json_obj['etime_start'];
    $eventEndTime=$json_obj['etime_end'];
    $token_r=$json_obj['token'];
    //see if the token transferred through form match that stored in session and prevent CSRF forgery
    if(!hash_equals($_SESSION['token'], $token_r)){
        die("Request forgery detected");
    }
    //loop through every membername that is passed in
    foreach ($groupmemberlist as $membername) {
        //see if the username added exist as a users
        $stmt = $mysqli->prepare("SELECT COUNT(*), id FROM users WHERE username=?");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            echo json_encode(array(
                "success" => false,
                "message" => "php query error"
            ));
            exit;
        }
               
        $stmt->bind_param('s', $membername);
        $stmt->execute();
        $stmt->bind_result($cnt, $memberid);
        $stmt->fetch();
        $stmt->close();
        $msg="all user exist";
        $empty_list=array();
        if($cnt!=0){
            //create that event for every member that is entered and insert that event into database
            $stmt = $mysqli->prepare("INSERT into myEvent (userId, title, content, eventdate, startTime, endTime) values (?, ?, ?, ?, ?, ?) ");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                echo json_encode(array(
                    "success" => false,
                    "message" => "Event Creation Failed"
                ));
                exit;
            }
                            
            $stmt->bind_param('isssss', $memberid, $eventTitle, $eventContent, $formatted_date, $eventStartTime, $eventEndTime);
            $stmt->execute();
            $stmt->close();
        }else{
            $msg="one user does not exist";
            $empty_list[]=$membername;
        }
        
    }
    echo json_encode(array(
        "success" => true,
        "message" => $msg,
        "empty_list"=>$empty_list,
    ));
?>