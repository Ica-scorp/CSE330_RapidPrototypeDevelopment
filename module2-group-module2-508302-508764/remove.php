<!DOCTYPE html>
<html lang="en">
<?php
    session_start();
    //if the user is not logged in, there will be no username stored in the session, so it exits
    if(!isset($_SESSION['user'])){
        header("Location: login.php");
    }
?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Files</title>
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
    <!--allows the user to go back to the user page on this page-->
    
    <div>
        <a href="user.php">
            back to user interface
        </a>
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
            <?php
                
                //gets the username from the session
                $username = $_SESSION['user'];
                $num=0;
                $user_path = sprintf("/srv/upload/%s", $username);
                //open the directory and prints out the files' names as checkbox, if the user select the file and choose "Remove Files", those files will be removed using unlink
                $file_dir = opendir($user_path);
                while($file = readdir($file_dir)){
                    if($file == "." || $file == ".."){
                        continue;
                    }
                
                    echo "<input type='checkbox' name ='filesname[]' value='$file'><label>$file</label><br>";
                }
            ?>
        <p>
        <button type="submit">Remove Files</button>
        </p>
        <?php
        if(isset($_POST['filesname'])){
        //we collect the array of files checkboxes that are selected by the user
        $filenames = $_POST['filesname'];
        foreach($filenames as $name){
            $num=$num+1;
            $full_path = sprintf("/srv/upload/%s/%s", $username, $name);
            $res = unlink($full_path);
            if($res){
            } else {
                //jump to the page that shows the removal of file is not successful if so
                header("Location: remove_failure.html");
            }
        }
        //if we remove at least one file, refresh this page and reset the number of files to be 0 so that there will not be an infinite loop
        if($num>0){
            $num=0;
            header("location: remove.php");
        }
        }
        ?>
        </form>
    </div>
    
</body>
</html>