<?php
    session_start();
    //if the user is not logged in, there will be no username stored in the session, so it exits
    if(!isset($_SESSION['user'])){
        header("Location: login.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <style>
    body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        div {
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

        button {
            width: 50%;
            padding: 10px;
            background-color: #3f51b5;
            color: #fff;
            border: 0;
            border-radius: 5px;
            font-size: 18px;
            margin : 10px;
            cursor: pointer;
        }

        button:hover {
            background-color: #444;
        }

        input[type="text"]:focus {
            border-color: #888;
        }
    </style>
</head>
<body>
    <!--allows the user to jump to jump to different webpages to do their ideal manipulations-->
    <div>
        <a href="upload.php">
            upload files
        </a>
        
        <br>
        <a href="view.php">
            view file
        </a>
        <br>
        <a href="remove.php">
            remove files
        </a>
        <br>
        <a href="logout.php">
            logout
        </a>
    </div>
</body>
</html>