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
    <title>Story Deleting</title>
</head>
<body>
    <?php
        if(!isset($_SESSION['user'])){//if not logged in, redirect to home page
            echo "<script>alert('Please Signin first！');
            window.location.href = 'home.php';</script>";
        }
        $session_token=$_SESSION['token'];
        if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
        }
        if(isset($_POST['story_id'])){
            $stmt = $mysqli->prepare("DELETE from comment WHERE story_id = ?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
            }
            $stmt->bind_param('i', $_POST['story_id']);
            $stmt->execute();
            $stmt->close();
            //delete comments first because comment has foreign key that reference story
            $stmt = $mysqli->prepare("DELETE from link WHERE story_id = ?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
            }
            $stmt->bind_param('i', $_POST['story_id']);
            $stmt->execute();
            $stmt->close();

            $stmt = $mysqli->prepare("DELETE from story WHERE id = ?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
            }
            $stmt->bind_param('i', $_POST['story_id']);
            $stmt->execute();
            $stmt->close();
            //then delete the story according to the story id
        }
        
        header("location: edit.php");//go back to general my post page
    ?>
</body>
</html>