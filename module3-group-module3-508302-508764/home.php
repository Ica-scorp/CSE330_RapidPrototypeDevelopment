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
        <title>Home</title>
    </head>
    <body>
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
            <input type="text" name="search_content">
            <?php 
                if(isset($_SESSION['user'])){
                    
                    $session_token=$_SESSION['token'];
                    echo "<input type='hidden' name='token' value=$session_token>";
                }
            ?>
            <button type='submit' name='submit_search'> search by story title keywords</button>
        </form>
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
        <?php
        
        if(isset($_SESSION['user'])){
            if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
                if(!hash_equals($_SESSION['token'], $_POST['token'])){
                    die("Request forgery detected");
                }
            }
            $session_token=$_SESSION['token'];
            if(isset($_POST['story_like'])){
                $likes=$_POST['story_like'];
                // if this user already liked the story, delete the like in database, otherwise, insert a like.
                $stmt = $mysqli->prepare("SELECT COUNT(*) from likes where (story_id=? and user_id=?)");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                $stmt->bind_param('ii', $likes, $_SESSION['user_id']);
                $stmt->execute();
                $stmt->bind_result($cct);
                $stmt->fetch();
                $stmt->close();
                if($cct>=1){
                    $stmt = $mysqli->prepare("DELETE from likes where (story_id=? and user_id=?)");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('ii', $likes, $_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->close();
                }else{
                    $stmt = $mysqli->prepare("INSERT into likes (story_id, user_id) values(?, ?)");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    $stmt->bind_param('ii', $likes, $_SESSION['user_id']);
                    $stmt->execute();
                    $stmt->close();

                }
                
                
            }
            
        }
        $user_id = 0;
        if(isset($_SESSION['user'])){
            $user_id = $_SESSION['user_id'];
        }
        //select all stories that post and left join the table likes to count the number of like that belong to the current user for each story.
        $stmt = $mysqli->prepare("SELECT story.user_name, story.title, story.story, story.id, count(likes.id) from story left join likes on (likes.story_id=story.id and likes.user_id= $user_id ) group by story.id");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }
        $stmt->execute();
        //$stmt->bind_param('i', $user_id);
        $stmt->bind_result($poster, $title, $story, $story_id, $ct);
        ?>
        <?php while($stmt->fetch()){
            echo "<form action='story.php' method='POST' style='display: flex; margin-top: 50px; flex-direction: column; align-items: center;'>";
                if(isset($_SESSION['user'])){
                    echo "<input type='hidden' name='token' value=$session_token>";
                }
                $cur_id = $story_id;
                if(isset($_POST['search_content']) && $_POST['search_content']!=null){
                    
                    if((strpos($title, $_POST['search_content']) === false)){
                        echo "</form>";
                        continue;
                    }
                }
                echo"
                <div class='card text-white bg-primary mb-3' style='margin-bottom: 20px;max-width: 1200px;width: 120%;'>
                    <div class='card-header'>"; echo htmlentities($poster); echo "</div>
                    <div class='card-body'>
                        <h4 class='card-title'>"; echo htmlentities($title); echo "</h4>
                        <p class='card-text'>";echo htmlentities($story) ;echo"</p>
                        <button type='submit' class='btn btn-light' value=$cur_id name='story_id'> comment</button>
                    </div>
                </div>
            </form>";//provides the link to each story

            if(isset($_SESSION['user'])){ ?>
                <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method='POST' style='display: flex; margin-top: 50px; flex-direction: column; align-items: center;'>
                <?php
                $session_token=$_SESSION['token'];
                echo "<input type='hidden' name='token' value=$session_token>";
                if($ct==1){//which means the story is likes already
                    echo "<button type='submit' class='btn btn-light' value=$cur_id name='story_like'> &#9829; </button>";
                }
                else{
                    echo "<button type='submit' class='btn btn-light' value=$cur_id name='story_like'> &#9825; </button>";
                }//provides the like function button
                ?>
                </form>
            <?php } ?>

        <?php }?>
    </body>
</html>