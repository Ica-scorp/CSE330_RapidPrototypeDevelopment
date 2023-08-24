<!DOCTYPE html>
<html>
<head>
    <title>Edit_success page</title>
</head>
<body>
    <?php
        session_start();
        require 'database.php';
        $usern=$_SESSION['user'];
        $userid=$_SESSION['user_id'];
        if(isset($_POST['storytitle']) and isset($_POST['storybody']) ){
            //if both varibales are transferred via post, it means a story is edited
            if(isset($_POST['token'])){//see if the token transferred through form match that stored in session and prevent CSRF forgery
                if(!hash_equals($_SESSION['token'], $_POST['token'])){
                    die("Request forgery detected");
                }
            }
            $stitle=strval($_POST['storytitle']);
            $sbody=strval($_POST['storybody']);
            $sid=$_POST['storyid'];
            //update the contents and title of the story
            $stmt = $mysqli->prepare("update story set title=?, story=? where id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->bind_param('ssi', $stitle, $sbody, $sid);
            $stmt->execute();
            $stmt->close();
            if(isset($_POST['storylink'])){
                $slink=$_POST['storylink'];
                //update the link of the story if there is one
                $stmt = $mysqli->prepare("update link set link=? where story_id=?");
                if(!$stmt){
                    printf("Query Prep Failed: %s\n", $mysqli->error);
                    exit;
                }
                
                $stmt->bind_param('si', $slink, $sid);
                $stmt->execute();
                $stmt->close();
            }
        }
        if(isset($_POST['commentbody'])){
            //if a comment is edited, we update the comment

            $cbody=strval($_POST['commentbody']);
            $cid=$_POST['commentid'];
            $stmt = $mysqli->prepare("update comment set comment=? where id=?");
            if(!$stmt){
                printf("Query Prep Failed: %s\n", $mysqli->error);
                exit;
            }
            
            $stmt->bind_param('si', $cbody, $cid);
            $stmt->execute();
            $stmt->close();
        }
        
        echo "<script>alert('Edit successfull!！');
        window.location.href = 'edit.php';</script>";
    ?>
</body>
</html>
