<?php
    session_start();
    //if the user is not logged in, there will be no username stored in the session, so it exits
	if(!isset($_SESSION['user'])){
		header("Location: login.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Files</title>
    <style>
	body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
    }

	div {
		width: 800px;
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

	.file {
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

	input[type="file"]:focus {
		border-color: #888;
	}
    </style>
</head>
<body>
    <!--allows the user to go back to the user page on this page-->
    <p>
        <a href="user.php">
            back to user interface
        </a>
    </p>
    <!--submits the form to getfiles.php to view the files in browser-->
    <div>
    <form action="getfiles.php" method="GET">
        
    <?php
        //if the user is not logged in, there will be no username stored in the session, so it exits
	    if(!isset($_SESSION['user'])){
		    echo "You are not logged in!";
	    	exit();
    	}
        $username = $_SESSION['user'];
        $user_path = sprintf("/srv/upload/%s", $username);
        $file_dir = opendir($user_path);
        //we open the user's directory and prints out the files as buttons that can submit for the form
        while($file = readdir($file_dir)){
            // we skip the first two that are not real files
            if($file == "." || $file == ".."){
                continue;
            }
            $handle = fopen($user_path."/".$file, "r");
            $finfo = fstat($handle);
            echo "<input type='submit' name='filename' value=$file />"."<br>";
            //we also prints out the files' information as a creative portion
            //we make use of the codes in the website: https://www.xin3721.com/PHP/php42094.html
            echo "Size:".htmlentities(round(($finfo["size"]/1024),1))."kb"."<br>";
            echo "Latest Editing Date:".htmlentities(date("Y-m-d h:i:s",$finfo["mtime"]))."<br>" ;
            //the citation for this website ends here, we simply shows the size and latest time the file was edited for each file shown

        }
    ?>
    </form>
    </div>
</body>
</html>