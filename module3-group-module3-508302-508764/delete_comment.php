<?php
    session_start();
    require "database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Deleting</title>
</head>
<body>
    <?php
        if(!isset($_SESSION['user'])){//if not logged in, redirect to home page
            echo "<script>alert('Please Signin first！');
            window.location.href = 'home.php';</script>";
        }
        if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
        }
        if(isset($_POST['comment_id'])){
             //delete the specific comment according to comment id
            $stmt = $mysqli->prepare("DELETE from comment WHERE id = ?");;
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
            }
            $stmt->bind_param('i', $_POST['comment_id']);
            $stmt->execute();
            $stmt->close();
        }
        header("location: edit.php");//redirects to My Post page
    ?>
</body>
</html>