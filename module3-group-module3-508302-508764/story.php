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
    <title>Document</title>
    
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
                    <a class="nav-link active" href="home.php">Home
                    </a>
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
    <?php
    if(isset($_POST['story_id'])){
        //show the content of the story whoes id is passed through form
        if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
            $session_token=$_SESSION['token'];
            $post_token=$_POST['token'];
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
        }
        $stmt = $mysqli->prepare("select user_name, title, story FROM story WHERE id=?");//prepared query to select username, story title, story content according to story id
        if(!$stmt){
            echo "failed";
        }
        $story_id = $_POST["story_id"];
        $stmt->bind_param('i', $story_id);
        $stmt->execute();
        $stmt->bind_result($user_name, $title, $story);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("select link FROM link WHERE story_id=$story_id");//prepared query to select link associated with this story according to story id
        if(!$stmt){
            echo "failed";
        }
        $stmt->execute();
        $stmt->bind_result($link);
        $stmt->fetch();
        $stmt->close();

        echo "
            <div style='display: flex; margin-top: 50px; flex-direction: column; align-items: center;'>
                <div class='card text-white bg-dark mb-3' style='
                    margin-bottom: 20px;
                    max-width: 1200px;
                    width: 120%;'>
                    
                    <div class='card-header' style='font-size: 20px;'>By $user_name</div>
                        <div class='card-body'>
                            <h1 class='card-title' style=' display: flex; align-items: center;justify-content: center; '>$title</h1>
                            
                            <p class='card-text' style='font-size: 20px; margin-top:10px;line-height:40px;'>$story</p>
                            <a href='$link' class='card-text' style='font-size: 20px; color: #183EFA; margin-top:10px;line-height:40px;'>$link</a>
                        </div>
                </div>
            </div>
        ";
    }
    
    ?>
    <div>
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" id="portal" style="display: flex; margin-top: 50px; flex-direction: column; align-items: center;">
            <div class='card text-white bg-primary mb-3' style='margin-bottom: 20px; max-width: 1200px; width: 120%;'>
            <div class='card-body'>
            <?php
                if(isset($_SESSION['user'])){
                    $story_id = $_POST['story_id'];
                    //allow the users to comment after logging in
                    echo "
                    

                            <label for='exampleTextarea' class='form-label mt-4'>Leave your comment</label>
                            <textarea class='form-control' id='exampleTextarea' rows='5' name='comment'></textarea>
                            <input type='hidden' value=$story_id name='story_id'>
                            <input type='hidden' name='token' value=$session_token>
                            <button type='submit' class='btn btn-light btn' style='margin-top: 20px;'> Comment</button>
                    ";
                }
                else{
                    echo "Please login to leave comments.";
                }
            ?>
            </div>
            </div>
        </form>
    </div>
    <?php
        if(isset($_POST['comment'])){//detect if the page is refreshed by commenting and do CSRF check
            $session_token=$_SESSION['token'];
            $post_token=$_POST['token'];
            if(!hash_equals($session_token, $post_token)){
                die("Request forgery detected");
            }
            $story_id = $_POST["story_id"];
            $commentUsername = $_SESSION['user'];
            $commentUserid = $_SESSION['user_id'];
            $comment = strval($_POST['comment']);
            $stmt = $mysqli->prepare("INSERT INTO comment (user_id, user_name, story_id, comment) values(?, ?, ?, ?) ");// insert comment contents in table comment
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param("isis", $commentUserid,  $commentUsername, $story_id, $comment);
            $stmt->execute();
            $stmt->close();
        }

        //select username and comment contents of comments related to this story
        $stmt = $mysqli->prepare("SELECT user_name, comment from comment WHERE story_id = $story_id");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        $stmt->bind_result($commenter, $comment);
        while($stmt->fetch()){
            echo "  
            <div style='display: flex; margin-top: 10px; flex-direction: column; align-items: center;'>
                <div class='card text-white bg-dark mb-3' style='
                    margin-bottom: 20px;
                    max-width: 1200px;
                    width: 120%;'>
                        <div class='card-header'>$commenter</div>
                        <div class='card-body'>
                            <p class='card-text'>$comment</p>
                        </div>
                </div>
            </div>
            ";
        }
        $stmt->close();
        
    ?>
</body>
</html>