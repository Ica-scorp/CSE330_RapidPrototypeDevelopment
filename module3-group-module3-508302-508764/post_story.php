<?php
    session_start();
    require 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="bootstrap.css">
        <title>My_Post</title>
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
    if(!isset($_SESSION['user'])){
        echo "<script>alert('Please Signin first！');
        window.location.href = 'home.php';</script>";
        //unlogged in users cannot post story and will be transferred to home page
    }
    ?>
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" id="portal" style="display: flex; margin-top: 50px; flex-direction: column; align-items: center;">
        <div class="card text-white bg-primary mb-3" style="margin-bottom: 20px; max-width: 1200px; width: 120%;">
            <div class="card-body">
                <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" /><!--CSRF token transferred-->
                <label for="title" class="form-label mt-4">Title</label>
                <input type="text" class="form-control" name="stitle" id="title" style="width: 30%;" placeholder="Enter title">

                <label for="exampleTextarea" class="form-label mt-4">Main text</label>
                <textarea class="form-control" id="exampleTextarea" rows="10" data-dl-input-translation="true" name="sbody"></textarea>

                <label for="link" class="form-label mt-4">Link</label>
                <input type="url" class="form-control" name="slink" id="link" style="width: 30%;" placeholder="(optional)">

                <button type='submit' class='btn btn-light btn-lg' style="margin-top: 20px;"> Post</button>
            </div>
        </div>
    </form>
    

    <?php
        if(!isset($_SESSION['user'])){
            echo "Please Login or Signup First!";
        }
        else{
            if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
                if(!hash_equals($_SESSION['token'], $_POST['token'])){
                    die("Request forgery detected");
                }
            }
            $usern=$_SESSION['user'];
            $userid=$_SESSION['user_id'];
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $stitle=strval($_POST['stitle']);
                $sbody=strval($_POST['sbody']);
                //get the contents and title for the story and insert into story table
                $stmt = $mysqli->prepare("INSERT into story (user_id, user_name, title, story) values (?, ?, ?, ?) ");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $stmt->bind_param('isss', $userid, $usern, $stitle, $sbody);
                $stmt->execute();
                $stmt->close();
                if($_POST['slink']){  
                    //if a link is associated with the story, insert it into the link table
                    $slink=strval($_POST['slink']);
                    $story_id = mysqli_insert_id($mysqli);

                    $stmt = $mysqli->prepare("insert into link (story_id, user_id, user_name, link) values (?, ?, ?, ?)");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    
                    $stmt->bind_param('iiss', $story_id, $userid, $usern, $slink);
                    
                    $stmt->execute();
                    
                    $stmt->close();
                }
                unset($_POST);//release the variables stored in POST
            }
        }
    ?>
</body>
</html>
