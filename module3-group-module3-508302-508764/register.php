<!DOCTYPE html>
<html>
<head>
    <title>Register Service</title>
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
        input[type="text", type="password"]:focus {
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
    
    <div class="login-container">
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" id="portal">
                <H1>Sign up for Online Forum</H1>
                <div class="input-container">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="cpassword" placeholder="Confirm Password" required>
                </div>
                <button type="submit">register</button>
        </form>
    </div>
    <!--provides a form to submit registration information-->

    <?php
        require 'database.php';
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            //if the user filled something
            $username= strval($_POST['username']);
            if( !preg_match('/^[\w_\-]+$/', $username) ){ 
                echo "Invalid username"; 
                exit(); 
            } 
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
            $password_original = strval($_POST['password']);
            $cpassword_original = strval($_POST['cpassword']);
            
            if( !preg_match('/^[\w_\-]+$/', $password_original) ){ 
                echo "Invalid password"; 
                exit(); 
            } 
            
            if(strcmp($password_original, $cpassword_original)!=0){//see if the reconfirmed password match before hashing and storing it securely
                echo "<script>alert('Password does not match!')</script>";
            }
            else
            {   //stores the user information in table users
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->bind_result($cnt);
                $stmt->fetch();
                $stmt->close();
                if($cnt!=0){//make sure there is no the same username for different users
                    echo "<script>alert('Username already exists!')</script>";//
                }
                else{
                    $password_hashed=password_hash($password_original, PASSWORD_BCRYPT);
                    //stores the hashed password securely
                    $stmt = $mysqli->prepare("Insert into users (username, hash_password) values (? , ?)");
                    if(!$stmt){
                        printf("Query Prep Failed: %s\n", $mysqli->error);
                        exit;
                    }
                    
                    $stmt->bind_param('ss', $username, $password_hashed);
                    $stmt->execute();
                    $stmt->close();
                    echo "<script>alert('Registration successful')</script>";
                    header("position: home.php");//redirects to home page after registration
                }
            }
        }
        
    ?>
</body>
</html>
