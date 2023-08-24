
// get the list of all the rooms
function get_room_list(){
    if(global_username == "no_user" || global_current_room != "no_room"){
        document.getElementById("room_panel_div").setAttribute("style","display:none");
    }
    else{
        document.getElementById("room_panel_div").setAttribute("style","");
        socketio.emit("get_room_list_message_to_server");
        socketio.on("get_room_list_message_to_client",function(data) {
            room_display_div = document.getElementById("room_display_div");
            room_display_div.innerHTML = "";
            room_list = data.room_list;
            for(let room in room_list) {
                room_name = room_list[room].room_name;
                room_type = room_list[room].room_type;
                room_creater = room_list[room].room_creater;
                new_room_div = document.createElement("div");
                new_room_div.setAttribute("id", room_name + "_div");
                
                room_name_h3 = document.createElement("h3");
                room_name_h3.setAttribute("id", room_name + "_room_name");
                room_name_h3.innerHTML = room_name;
                new_room_div.appendChild(room_name_h3);

                room_creater_p = document.createElement("p");
                room_creater_p.setAttribute("id", room_name + "_room_creater");
                room_creater_p.innerHTML = "creater: " + room_creater;
                new_room_div.appendChild(room_creater_p);

                room_type_p = document.createElement("p");
                room_type_p.setAttribute("id", room_name + "_room_type");
                room_type_p.innerHTML = "type: " + room_type;
                new_room_div.appendChild(room_type_p);

                if(room_type == "private"){
                    room_password_input = document.createElement("input");
                    room_password_input.setAttribute("type", "password");
                    room_password_input.setAttribute("placeholder", "private room, use password to login");
                    room_password_input.setAttribute("id", "room_password");
                    new_room_div.appendChild(room_password_input);
                }

                join_room_button = document.createElement("button");
                join_room_button.setAttribute("id", room_name + "_join_room_button");
                join_room_button.innerHTML = "join";
                join_room_button.onclick = function(){
                    join_room(room_name, room_type);
                };
                new_room_div.appendChild(join_room_button);

                room_display_div.appendChild(new_room_div);
            }
        });
    }
}