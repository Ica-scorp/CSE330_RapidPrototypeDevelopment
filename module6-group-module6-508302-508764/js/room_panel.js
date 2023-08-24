
//place the user in the room
function join_room(room_name, room_type){
    
    if(room_type == "public"){
        console.log("piblic");
        socketio.emit("user_join_room_to_server_public", {username: global_username, room_name:room_name});
        socketio.on("user_join_room_success_public", function(data){
            console.log(data.message);
            global_current_room = room_name;
            display();
        }); 
        socketio.on("user_join_room_fail_public", function(data){
            console.log(data.message);
            display();
        }); 
    }
    else {   
        if(global_is_creater != "1"){
            console.log(document.getElementById("room_password").value);
            socketio.emit("user_join_room_to_server_private", {username: global_username, room_name:room_name, room_password_guess: document.getElementById("room_panel_div").querySelector("#room_password").value});
            socketio.on("user_join_room_to_client_private_success", function(){    
                global_current_room = room_name;     
                display();
            });
            socketio.on("user_join_room_fail_private", function(data){
                console.log(data.message);
                display();
            });     
        }
        else{   
            console.log(document.getElementById("room_password").value);
            socketio.emit("user_join_room_to_server_private", {username: global_username, room_name:room_name, room_password_guess: document.getElementById("room_password").value});
            socketio.on("user_join_room_to_client_private_success", function(){    
                global_current_room = room_name;     
                display();
            });
            socketio.on("user_join_room_fail_private", function(data){
                console.log(data.message);
                display();
            }); 
        }
    }

}

//get all the message and users of the current room
function get_current_room(){
    if(global_current_room == "no_room"){
        document.getElementById("current_room_div").setAttribute("style","display:none");
    }
    else{
        document.getElementById("current_room_div").setAttribute("style","");
        socketio.emit("get_current_room_message_to_server", {room_name:global_current_room});
        socketio.on("get_current_room_message_to_client",function(data) {
            document.getElementById("current_room_name").innerHTML = "You are in the room:" + data.room.room_name;
            document.getElementById("current_room_creater").innerHTML ="created by:" + data.room.room_creater;
            current_room_user_list_div = document.getElementById("current_room_user_list_div");
            current_room_user_list_div.innerHTML = "";

            user_list_div_title = document.createElement("h3");
            user_list_div_title.innerHTML = "users in this room";
            current_room_user_list_div.appendChild(user_list_div_title);

            document.getElementById("mySelect").innerHTML="";
            all_user_opt=document.createElement("option");
            all_user_opt.text="all users";
            all_user_opt.value="all users";
            document.getElementById("mySelect").add(all_user_opt);
            //generating a list of all users.
            data.room.room_user.forEach(user => {
                user_div = document.createElement("div");
                user_div.setAttribute("id","room_user_" + user);

                user_name_p = document.createElement("a");
                user_name_p.innerHTML = user;
                user_div.appendChild(user_name_p);
                
                if(user!=global_username){
                    user_opt=document.createElement("option");
                    user_opt.text=user;
                    user_opt.value=user;
                    document.getElementById("mySelect").add(user_opt);
                }

                //if current user is the creater of the room, the user can kick, mute and ban other users
                if(global_is_creater=="1"){
                    kick_user_button = document.createElement("button");
                    kick_user_button.setAttribute("id","kick_user_button_" + user);
                    kick_user_button.innerHTML = "kickout";
                    kick_user_button.onclick = function(){kickuser(user)};
                    user_div.appendChild(kick_user_button);

                    ban_user_button = document.createElement("button");
                    ban_user_button.setAttribute("id","ban_user_button_" + user);
                    ban_user_button.innerHTML = "ban";
                    ban_user_button.onclick = function(){banuser(user)};
                    user_div.appendChild(ban_user_button);

                    mute_user_button = document.createElement("button");
                    mute_user_button.setAttribute("id","mute_user_button_" + user);
                    mute_user_button.innerHTML = "mute";
                    mute_user_button.onclick = function(){muteuser(user)};
                    user_div.appendChild(mute_user_button);
                }
                current_room_user_list_div.appendChild(user_div);
            });
        });
    }
}

//let a user lieav the room
function leave_room(){
    global_is_creater = "0";
    socketio.emit("user_leave_room_to_server", {username: global_username, room_name:global_current_room});
    socketio.on("user_leave_room_to_client", function(data){
    })
    global_current_room = "no_room";
    ban_to_talk = "0";
    display();
}

// kick a specific user out of the room
function kickuser(kick_user){
    console.log("kick" + kick_user);
    socketio.emit("kick_user_to_server", {kick_user: kick_user, room_name:global_current_room});
}

// ban a specific user out of the room
function banuser(ban_user){
    console.log("ban" + ban_user);
    socketio.emit("ban_user_to_server", {ban_user: ban_user, room_name: global_current_room});
    kickuser(ban_user);
}

//mute a user for 1min
function muteuser(mute_user){
    console.log("mute" + mute_user);
    socketio.emit("mute_user_to_server", {mute_user: mute_user, room_name:global_current_room});
}