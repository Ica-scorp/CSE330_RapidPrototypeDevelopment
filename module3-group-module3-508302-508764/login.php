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
    <title>Login Service</title>
    <link rel="stylesheet" href="bootstrap.css">
    <style>
        .login-container {
            width: 400px;
            margin: auto;
            margin-top: 100px;
            text-align: center;
            padding: 50px;
            border: solid 1px #fff;
            background-color: #222;
        }

        form {
            margin-top: 20px;
        }

        h1 {
            margin-bottom: 40px;
            font-size: 24px;
            color: #fff;
        }

        .input-container{
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }

        button[type="submit"] {
            width: 90%;
            padding: 10px;
            background-color: #325172;
            color: #fff;
            border: 0;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #444;
        }
        input{
            margin: 5px auto;
            width: 90%;
            height: 90%;
            padding: 10px;
            font-size: 16px;
            border: 0px solid #ddd;
            border-radius: 3px;
        }
        input[type="password"]:focus {
            border-color: #fff;
        }
    </style>
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
                    //if the user is not logged in, we provide the option to either login or register here
                    echo "
                    <div> 
                        <a href='login.php'><div class='btn btn-secondary my-2 my-sm-0'>Sign in</div></a>
                        <a href='register.php'><div class='btn btn-secondary my-2 my-sm-0'>Sign up</div></a>
                    </div>";
                } else {
                    //if the user is logged in, print a welcome message and provides the logout button
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
    <div class="login-container">
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" id="portal">
                <H1>Welcome to Online Forum!</H1>
                <div class="input-container">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
        </form>
    </div>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST"){//if not all the input boxes are empty
            //select the number of times that username appears in the table users and user's id, his hashed password
            $stmt = $mysqli->prepare("SELECT COUNT(*), id, hash_password FROM users WHERE username=?");//prepared query to select hashed password for this username
            if(!$stmt){
                echo "failed";
            }
            $username = $_POST['username'];
            if( !preg_match('/^[\w_\-]+$/', $username) ){ 
                echo "Invalid username"; 
                exit(); 
            } 
            $stmt->bind_param('s', $username);
            $stmt->execute();

            $stmt->bind_result($cnt, $user_id, $pwd_hash);
            $stmt->fetch();
            $pwd_guess = $_POST['password'];
            
            if( !preg_match('/^[\w_\-]+$/', $pwd_guess) ){ 
                echo "Invalid password"; 
                exit(); 
            } 
            //see if there is a matching password stored and the password is checked securely
            if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
                //the user identity is validated and created session valuables to store his name, id, and random CSRF token generated
                $_SESSION['user'] = $username;
                $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
                $_SESSION['user_id'] = $user_id;
                header("location: home.php");//jump to the home page where all the stories post are shown
            
            } else{
                echo "<script>alert('Username or password is incorrect!')</script>";
                //Login failed; redirect back to the login screen and post an alert
            }
        }
    ?>
</body>
</html>
