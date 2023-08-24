<?php
    require "database.php";
?>
<?php
header("Content-Type: application/json"); 
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$username = strval($json_obj['username']);
$password = strval($json_obj['password']);
//select the number of times that username appears in the table users and user's id, his hashed password
$stmt = $mysqli->prepare("SELECT COUNT(*), id, hashPwd FROM users WHERE username=?");//prepared query to select hashed password for this username
if(!$stmt){
    echo htmlentities(json_encode(array(
		"success" => false,
		"message" => "Query Failed"
	)));
	exit;
}
//check if username is valid
if( !preg_match('/^[\w_\-]+$/', $username) ){ 
    echo htmlentities(json_encode(array(
		"success" => false,
		"message" => "Invalid username"
	)));
	exit;
} 
$stmt->bind_param('s', $username);
$stmt->execute();

$stmt->bind_result($cnt, $user_id, $pwd_hash);
$stmt->fetch();
$pwd_guess = $password;
if( !preg_match('/^[\w_\-]+$/', $pwd_guess) ){ 
    echo json_encode(array(
		"success" => false,
		"message" => "Invalid password"
	));
	exit;
}
//see if there is a matching password stored and the password is checked securely
if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
    session_start();
    //the user identity is validated and created session valuables to store his name, id, and random CSRF token generated
    $_SESSION['username'] = $username;
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['user_id'] = $user_id;
    echo json_encode(array(
		"success" => true
	));
	exit;
} else{
    echo json_encode(array(
		"success" => false,
		"message" => "Incorrect Username or Password"
	));
	exit;
}
?>