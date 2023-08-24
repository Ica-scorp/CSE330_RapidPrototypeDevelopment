<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Out Service</title>
</head>
<body>
    <p>You are successfully logout</p>
    <br>
    <?php
    //destroy all session variables that are related to this user to log out and provides a link to log in again
        session_start(); 
        session_destroy();
    ?>
    <!--allows the user to login again from this logout page by linking to another php file-->
    <a href="login.php">
        login again
    </a>
</body>
</html>