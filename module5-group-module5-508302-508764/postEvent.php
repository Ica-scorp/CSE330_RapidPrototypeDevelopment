<?php
    session_start();
    require "database.php";
?>

<?php 
    header("Content-Type: application/json"); 
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str, true);
    //if not logged in, pass failure value
    if(!isset($_SESSION['user_id'])){
        echo json_encode(array(
            "success" => false,
            "message" => "Not Logged In"
        ));
        exit();
    }
    if (empty($json_str)){
        exit();
    }
    //Variables can be accessed as such:
    $eventdate_r = new DateTime($json_obj['date']);
    $formatted_date = $eventdate_r->format('Y-m-d');
    $userid_r = $_SESSION['user_id'];
    $token_r=$json_obj['token'];
    //see if the token transferred through form match that stored in session and prevent CSRF forgery
    if(!hash_equals($_SESSION['token'], $token_r)){
        die("Request forgery detected");
    }
    //select the events that belongs to the user and on that date
    $stmt = $mysqli->prepare("SELECT id, userId, title, content, eventdate, startTime, endTime, important from myEvent where eventdate=? AND userId=?");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Event Search Failed"
        ));
        exit;
    }
    $stmt->bind_param('si', $formatted_date, $userid_r);
    $stmt->execute();
    $stmt->bind_result($eventid, $event_userid, $eventtitle, $eventcontent, $eventdate, $eventstart, $eventend, $important);
    $eventlist=array();//create an array for holding details for each event
    while($stmt->fetch()){
        $eventlist[] = array(//adding details of events belongs to the user
            'eventid' => htmlentities($eventid),
            'event_userid' => htmlentities($event_userid),
            'eventtitle' => htmlentities($eventtitle),
            'eventcontent'=> htmlentities($eventcontent),
            'eventdate'=>htmlentities($eventdate),
            'eventstart'=> htmlentities($eventstart),
            'eventend'=>htmlentities($eventend),
            'eventimportant'=>htmlentities($important),
        );

    }
    $stmt->close();
    //select the events that belongs to the user's linked users and on that date by using join
    $stmt = $mysqli->prepare("SELECT myEvent.id, myEvent.userId, myEvent.title, myEvent.content, myEvent.eventdate, myEvent.startTime, myEvent.endTime,userRelations.usernameLinked from myEvent join userRelations on (userRelations.userIdLinked = myEvent.userId) where userRelations.userId = ? and myEvent.eventdate = ?");
    if(!$stmt){
        echo json_encode(array(
            "success" => false,
            "message" => "Event Linked Search Failed"
        ));
        exit;
    }
    $stmt->bind_param('is', $userid_r, $formatted_date);
    $stmt->execute();
    $stmt->bind_result($eventid, $event_userid, $eventtitle, $eventcontent, $eventdate, $eventstart, $eventend, $friendusername);
    while($stmt->fetch()){
        $eventlist[] = array(//adding details of events belongs to the user's linked users, including usernames
            'eventid' => htmlentities($eventid),
            'event_userid' => htmlentities($event_userid),
            'eventtitle' => htmlentities($eventtitle),
            'eventcontent'=> htmlentities($eventcontent),
            'eventdate'=>htmlentities($eventdate),
            'eventstart'=> htmlentities($eventstart),
            'eventend'=>htmlentities($eventend),
            'event_username'=>htmlentities($friendusername)
        );
    }
    $stmt->close();

    $data = array("success" => true, "events"=>$eventlist);//return the list under key "events"
    $json = json_encode($data);
    echo $json;
    exit;
?>