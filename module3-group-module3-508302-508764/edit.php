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
    <title>Edit</title>
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
                    //always provides the option to login and register if not logged in
                    echo "
                    <div> 
                        <a href='login.php'><div class='btn btn-secondary my-2 my-sm-0'>Sign in</div></a>
                        <a href='register.php'><div class='btn btn-secondary my-2 my-sm-0'>Sign up</div></a>
                    </div>";
                } else {
                    if(isset($_SESSION['user'])){
                        $session_token=$_SESSION['token'];//get the CSRF tokem stored in session
                    }
                    //if the user is logged in
                    echo "<a style='margin-right: 20px;'>";
                    printf("Welcome! %s",$_SESSION['user']);
                    echo "</a>";
                    echo "
                    <div>
                        <a href='logout.php'><div class='btn btn-secondary my-2 my-sm-0'>Log out</div></a>
                    </div>
                    ";
                    //provides the option to logout at the top for logged in users
                }
                ?>
                
            </div>
        </div>
    </nav>
    <?php
    if(!isset($_SESSION['user'])){
        echo "<script>alert('Please Signin first！');
        window.location.href = 'home.php';</script>";
        //if the user is not logged in and accidently get to this page, redirect him to home page and post an alert
    }
    $session_token=$_SESSION['token'];
    //story edit
    echo "
    <div style='align-items: center; margin :10%; left: 50%'>
        <h3>Story</h3>
        <table class='table table-hover'>
            <thead>
                <tr>
                    <th scope='col'>Title</th>
                    <th scope='col'>Abstract</th>
                    <th scope='col'></th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <tbody>";
            //select stories' titles, ids, and contents from story table in mySQL according to this user id
            $stmt = $mysqli->prepare("SELECT title, id, story from story WHERE user_id = ?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($title, $story_id, $text);
            
            while($stmt->fetch()){
                $sub = substr($text, 0, 200);
                //provides a beginning of each story's contents and provides two links
                //one link for deleting a story, the other for editing a story
                //the story_id is transferred via POST in the form submission
                echo "
                <tr class='table-active'>
                    <th scope='row'>$title</th>
                    <td>$sub</td>
                    <td>
                        <form action='delete_story.php' method='POST'>
                            <input type='hidden' name='token' value=$session_token>
                            <button type='submit' value=$story_id name='story_id'>delete</button>
                        </form>
                    </td>
                    <td>
                        <form action='edit_story.php' method='POST'>
                            <input type='hidden' name='token' value=$session_token>
                            <button type='submit' value=$story_id name='story_id'>edit</button>
                        </form>
                    </td>
                </tr>";
                //provides delte and edite option for each story as well as transferring the CSRF token stored in session
            }
            $stmt->close();
            echo"    
            </tbody>
        </table>
    </div>";
    //comment
    echo "
    <div style='align-items: center; margin :10%; left: 50%'>
        <h3>Comment</h3>
        <table class='table table-hover'>
            <thead>
                <tr>
                    <th scope='col'>Comment</th>
                    <th scope='col'>In story</th>
                    <th scope='col'>Poster</th>
                    <th scope='col'></th>
                    <th scope='col'></th>
                </tr>
            </thead>
            <tbody>";
            //select the relevant information for each comment:
            //comment contents, comment id and story_id, title, contents, story composer of the story the comment is attached to
            //join the contents of the two table story and comments by matching story_id and searching by the logged in user's id
            $stmt = $mysqli->prepare("SELECT comment.comment, comment.id, comment.story_id, story.title, story.user_name from comment JOIN story ON (story.id=comment.story_id) where comment.user_id = ?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $stmt->bind_result($comment, $comment_id, $comment_story_id, $comment_story_title, $comment_story_poster);
            while($stmt->fetch()){
                echo "
                <tr class='table-active'>
                    <th scope='row'>$comment</th>
                    <td>$comment_story_title</td>
                    <td>$comment_story_poster</td>
                    <td>
                        <form action='delete_comment.php' method='POST'>
                            <button type='submit' value=$comment_id name='comment_id'> delete</button>
                            <input type='hidden' name='token' value=$session_token>
                        </form>
                    </td>
                    <td>
                        <form action='edit_comment.php' method='POST'>
                            <button type='submit' value=$comment_id name='comment_id'> edit </button>
                            <input type='hidden' name='token' value=$session_token>
                        </form>
                    </td>
                </tr>";
                //provides delte and edite option for each comment as well as transferring the CSRF token stored in session
            }
            $stmt->close();
            echo"    
            </tbody>
        </table>
    </div>";
    ?>
</body>
</html>