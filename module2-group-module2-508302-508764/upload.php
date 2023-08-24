<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Upload Service</title>
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
            width: 100%;
            padding: 10px;
            background-color: #3f51b5;
            color: #fff;
            border: 0;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #444;
        }

    </style>
</head>
<body>
	<?php
	//if the user is not logged in, there will be no username stored in the session, so it exits
	if(!isset($_SESSION['user'])){
		header("Location: login.php");
	}

	?>
	<div>
		
		<h1>Choose a file to upload:</h1> 
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
			<p>
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
				<input name="uploadedfile" type="file" id="uploadfile_input" />
				
			</p>
			<p>
				<button type="submit">Upload File</button>
			</p>
		</form>
		<!--allows the user to go back to the user page on this page-->
		<a href="user.php">
        	back to user interface
    	</a>
		
	</div>
	
    
	<?php
	// if no file is uploaded, do nothing
	if (!isset($_FILES['uploadedfile']['name']))
		exit();
	
	// Get the filename and make sure it is valid
	$filename = basename($_FILES['uploadedfile']['name']);
	if(!preg_match('/^[\w_\.\-]+$/', $filename) ){
		echo htmlentities($filename);
		echo "Invalid filename";
		exit();
	}
	//Get the username from session and make sure it is valid
	$username = $_SESSION['user'];
	if( !preg_match('/^[\w_\-]+$/', $username) ){
		echo "Invalid username";
		exit();
	}
	$tmpfullpath = $_FILES['uploadedfile']['tmp_name'];
	$full_path = sprintf("/srv/upload/%s/%s", $username, $filename);//try if we can move the file successfully to that specific directory of that user
	//if yes, we go to upload_success page; if not, we go to upload_failure page with the prompt that the file size cannot exceed 2MB
	if( move_uploaded_file($tmpfullpath, $full_path) ){
		header("Location: upload_success.html");
	}else{
		header("Location: upload_failure.html");
	}

	?>

</body>
</html>
