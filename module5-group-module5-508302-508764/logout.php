<body>
    
    <?php
    //destroy all session variables that are related to this user to log out and provides a link to log in again
        session_start(); 
        session_destroy();
        header("location: home.html");
    ?>
</body>
</html>