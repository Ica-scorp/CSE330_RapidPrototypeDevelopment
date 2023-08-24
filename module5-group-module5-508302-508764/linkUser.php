<?php
    session_start();
    require "database.php";
?>

<?php 
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
    //Variables can be accessed as such:
    $userid_r = $_SESSION['user_id'];
    $usernamelinked_r = $json_obj['usernamelinked'];
    $token_r=$json_obj['token'];

    //see if the token transferred through form match that stored in session and prevent CSRF forgery
    if(!hash_equals($_SESSION['token'], $token_r)){
        die("Request forgery detected");
    }
    //see if the usernamelinked exist as a user:
    $stmt = $mysqli->prepare("SELECT COUNT(*), id FROM users WHERE username=?");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "User Linked Failed"
        ));
        exit;
    }
    $stmt->bind_param('s', $usernamelinked_r);
    $stmt->execute();
    $stmt->bind_result($cnt, $useridlinked_);
    $stmt->fetch();
    $stmt->close();
    if($cnt==0){
        echo json_encode(array(
            "success" => false,
            "message" => "User Linked Does Not Exist!"
        ));
        exit;
    }
    else{
        if($userid_r!=$useridlinked_){//make sure there is no the same username for different users
            $stmt = $mysqli->prepare("insert into userRelations (userId, usernameLinked, userIdLinked) values(?,?,?)");
            if(!$stmt){
                echo json_encode(array(
                    "success" => false,
                    "message" => "User Linked Failed"
                ));
                exit;
            }
            $stmt->bind_param('isi', $userid_r, $usernamelinked_r, $useridlinked_);
            $stmt->execute();
            $stmt->close();

            echo json_encode(array(
                "success" => true
            ));
            exit;

        }
        echo json_encode(array(
            "success" => false,
            "message" => "Same User Linked!"
        ));
        exit;
        
    }

    
?>