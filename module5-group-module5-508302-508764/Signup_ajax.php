<?php
    require "database.php";
    session_start();
?>
<?php
    header("Content-Type: application/json"); 
    $json_str = file_get_contents('php://input');
    if (empty($json_str)){
        exit();
    }
    //This will store the data into an associative array
    $json_obj = json_decode($json_str, true);

    //Variables can be accessed as such:
    $username = strval($json_obj['username']);
    $password_original = strval($json_obj['password']);
    // if either is empty, login fails
    if(empty($username)||empty($password_original)){
        echo json_encode(array(
            "success" => false,
            "message" => "Empty username or password"
        ));
        exit;
    }
    //check if username and password are both valid
    if( !preg_match('/^[\w_\-]+$/', $username) ){ 
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid Username"
        ));
        exit;
    } 
    if( !preg_match('/^[\w_\-]+$/', $password_original) ){ 
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid Password"
        ));
        exit;
    } 
    //stores the user information in table users
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->fetch();
    $stmt->close();
    if($cnt!=0){//make sure there is no the same username for different users
        echo json_encode(array(
            "success" => false,
            "message" => "Username already exists!"
        ));
        exit;
    }
    else{
        $password_hashed=password_hash($password_original, PASSWORD_BCRYPT);
        //stores the hashed password securely
        $stmt = $mysqli->prepare("Insert into users (username, hashPwd) values (? , ?)");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            echo json_encode(array(
                "success" => false
            ));
            exit;
        }
        $stmt->bind_param('ss', $username, $password_hashed);
        $stmt->execute();
        $stmt->close();
        echo json_encode(array(
            "success" => true
        ));
        exit;
    }

        
?>