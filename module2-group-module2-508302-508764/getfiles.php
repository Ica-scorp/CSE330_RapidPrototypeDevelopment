<?php
//this php file sends the file to the browser to view
    session_start();
    // gets the filename and username from session
        $file = $_GET['filename'];
        $username = $_SESSION['user'];
        $full_path =  sprintf("/srv/upload/%s/%s", $username, $file);
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($full_path);
        //this identifies the files type and uploads that successfully to the browser to view
        header('Content-Type: '.$mime);
        header('content-disposition: inline; filename="'.$file.'"');
        readfile($full_path);
?>