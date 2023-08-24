//send user login message to server
function user_login(){
    var username = document.getElementById("login_username").value;
    if(username == ""){
        return;
    }
    socketio.emit("login_message_to_server", {username:username});
    socketio.on("login_success_message_to_client",function(data) {
        console.log("username: " + data["username"]);
     });
    global_username = username;
    display();
}

//user logout function
function user_logout(){
    socketio.emit("logout_message_to_server", {logout:1});
    if(global_current_room!="no_room"){
        leave_room();
    }
    global_username = "no_user";
    display();
}

//display the userpanel base on the current user state
function get_user_panel(){
    if(global_username == "no_user"){
        document.getElementById("not_login_div").setAttribute("style","");
        document.getElementById("is_login_div").setAttribute("style","display: none");
    }  
    else{
        document.getElementById("is_login_div").setAttribute("style","");
        document.getElementById("not_login_div").setAttribute("style","display: none");
        document.getElementById("user_title_p").innerHTML="Welcome!"+global_username;
    }

}