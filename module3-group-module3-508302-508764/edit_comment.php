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
    <link rel="stylesheet" href="bootstrap.css">
    <title>Edit Story</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                    <a href='post_story.php'><div class='btn btn-secondary my-2 my-sm-0'>Post Story</div></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link active" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" href="edit.php">My post</a>
                    </li>
                </ul>
                <?php
                if(!isset($_SESSION['user'])){
                    echo "
                    <div> 
                        <a href='login.php'><div class='btn btn-secondary my-2 my-sm-0'>Sign in</div></a>
                        <a href='register.php'><div class='btn btn-secondary my-2 my-sm-0'>Sign up</div></a>
                    </div>";
                } else {
                    echo "<a style='margin-right: 20px;'>";
                    printf("Welcome! %s",$_SESSION['user']);
                    echo "</a>";
                    echo "
                    <div>
                        <a href='logout.php'><div class='btn btn-secondary my-2 my-sm-0'>Log out</div></a>
                    </div>
                    ";
                }
                ?>
                
            </div>
        </div>
    </nav>
    <form action="edit_success.php" method="POST" id="portal" style="display: flex; margin-top: 50px; flex-direction: column; align-items: center;">
        <div class="card text-white bg-primary mb-3" style="margin-bottom: 20px; max-width: 1200px; width: 120%;">
            <div class="card-body">
                <?php
                if(!isset($_SESSION['user'])){
                        echo "<script>alert('Please Signin first！');
                        window.location.href = 'home.php';</script>";
                }
                $session_token=$_SESSION['token'];
                if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
                    if(!hash_equals($_SESSION['token'], $_POST['token'])){
                        die("Request forgery detected");
                    }
                }
                if(isset($_POST['comment_id'])){//if there is a comment id transferred, we update the comment
                    $user_id=$_SESSION['user_id'];
                    $comment_id=$_POST['comment_id'];
                    $stmt = $mysqli->prepare("select user_id, comment from comment where id=?");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('i', $comment_id);
                    $stmt->execute();
                    $stmt->bind_result($userid, $comment_body);
                    $stmt->fetch();
                    $stmt->close();

                    if($userid!=$user_id){    
                        //if the user id does not match, the user cannot edit the comment not posted by himself
                        echo "<script>alert('Please Signin first！');
                        window.location.href = 'home.php';</script>";
                    }
                    echo "
                            <input type='hidden' name='token' value=$session_token>
                            <label for='exampleTextarea' class='form-label mt-4'>Main text</label>
                            <textarea class='form-control' id='exampleTextarea' rows='10' name='commentbody' >";echo htmlentities($comment_body); echo"</textarea>
                            <input type='hidden' value=$comment_id name='commentid'>
                        ";
                    echo "<button type='submit' class='btn btn-light btn-lg' style='margin-top: 20px;'>Submit Changes</button>";
                    echo "<button type='reset' class='btn btn-light btn-lg' style='margin-top: 20px; margin-left:10px;'>Reset Changes</button>";
                }
                else{
                    header("Location: home.php");//if not comment id is successfully transferred, redirect to home page
                }
            ?>
            
    </form>
    
</body>
</html>