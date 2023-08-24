<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .login-container {
            width: 400px;
            margin: auto;
            text-align: center;
            padding: 50px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px #bbb;
        }

        form {
            margin-top: 20px;
        }

        h1 {
            margin-bottom: 40px;
            font-size: 24px;
            color: #444;
        }

        .input-container{
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #3f51b5;
            color: #fff;
            border: 0;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #444;
        }
        input[type="text"]{
            margin: 0 auto;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="text"]:focus {
            border-color: #888;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" id="portal">
                
                <H1>Welcome to Online File Operating System!</H1>
                <div class="input-container">
                    <input type="text" name="usern" placeholder="Username" required>
                </div>
                <button type="submit">Login</button>
        </form>
    </div>
    <?php
        session_start();
        //if no username is entered, nothing happens
        if(!isset($_POST['usern'])){
            exit();
        }
        //store the username in the session to keep logging in
        $username=strval($_POST["usern"]);
        $_SESSION['user'] = $username;
        $usernames=fopen("/srv/users.txt", "r");
        //read user names from the file in the directory "upload" and see if the input mathches
        while(!feof($usernames)){
            $tmp=fgets($usernames);
            $tmp=trim($tmp, " \n\r\t\v\x00");
            if($username==$tmp){
                header("Location: user.php");
                exit; 
            }
        }
        //as a creative portion: we pops up an alert if the user name entered does not match any stored user names
        if(isset($_POST['usern'])){
            echo "<script>alert('User does not exist!')</script>";
        }
    ?>
</body>
</html>
