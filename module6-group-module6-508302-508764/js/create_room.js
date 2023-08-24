//only user that login can create room
function get_create_room_panel(){
    if(global_username != "no_user"){
        document.getElementById("create_room_panel_div").setAttribute("style","");
    }
    else{
        document.getElementById("create_room_panel_div").setAttribute("style","display:none");
    }
}

//make the create room form visable
function get_create_room_form(){
    document.getElementById("create_room_form_div").setAttribute("style","");
    document.getElementById("create_room_button").setAttribute("style","display:none");
}

//make it disappear
function cancel_create_room_form(){
    document.getElementById("create_room_form_div").setAttribute("style","display:none");
    document.getElementById("create_room_button").setAttribute("style","");
}

//send room_message to the server
function create_room(){
    var room_name = document.getElementById("room_name").value;
    var room_type = document.getElementById("room_type").value;
    var flag = 0;
    var room_password = "";
    if(room_type == "private"){
        room_password = document.getElementById("room_password").value;
    }
    socketio.emit("create_room_message_to_server", {room_name:room_name, room_type:room_type, room_creater:global_username, room_password:room_password});
}

socketio.on("create_room_success_message_to_client",function(data) {
    cancel_create_room_form();
    global_is_creater = "1";
    join_room(data.room_name, data.room_type);
    display();
});
socketio.on("create_room_fail_message_to_client",function(data) {
    console.log(data.message);
});