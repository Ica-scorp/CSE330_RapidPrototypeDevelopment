// Require the packages we will use:
var http = require('http'),
	url = require('url'),
	path = require('path'),
	path = require('path'),
	fs = require('fs');
const { deflateRawSync } = require('zlib');

const port = 3456;
const file = "index.html";
// Listen for HTTP connections.  This is essentially a miniature static file server that only serves our one file, client.html, on port 3456:
const server = http.createServer(function (req, resp) {
    // This callback runs when a new connection is made to our HTTP server.

    const filename = path.join(__dirname, url.parse(req.url).pathname);
	(fs.exists || path.exists)(filename, function(exists){
		if (exists) {
			fs.readFile(filename, function(err, data){
				if (err) {
					// File exists but is not readable (permissions issue?)
					resp.writeHead(500, {
						"Content-Type": "text/plain"
					});
					resp.write("Internal server error: could not read file" + filename + req.url);
					resp.end();
					return;
				}
				
				// File exists and is readable
				resp.write(data);
				resp.end();
				return;
			});
		}else{
			// File does not exist
			resp.writeHead(404, {
				"Content-Type": "text/plain"
			});
			resp.write("Requested file not found: "+filename);
			resp.end();
			return;
		}
	});
});
server.listen(port);
// Import Socket.IO and pass our HTTP server object to it.
const socketio = require("socket.io")(http, {
    wsEngine: 'ws'
});

// Attach our Socket.IO server to our HTTP server to listen
const io = socketio.listen(server);

room_list = {};

io.sockets.on("connection", function (socket) {
    // This callback runs when a new Socket.IO connection is established.

    socket.on('login_message_to_server', function (data) {
        // This callback runs when the server receives a new message from the client.
        socket.data.username=data["username"];
		socket.data.owned_room="none";
		socket.data.room="none";
        io.sockets.emit('login_success_braodcast_to_client', { login_success_user: data["username"] }) ;// broadcast the message to other users
    });

	socket.on('logout_message_to_server', function (data) {  
		logout = data["logout"];
		io.sockets.emit('logout_success_braodcast_to_client',{logout_success_user:socket.data.username});
		if(logout == 1){
			socket.data.username = "no_user";
			socket.data.owned_room="none";
			socket.data.room="none";
		}
        console.log("username: " + socket.data.username);
    });

	//reveive new room and create the room
	socket.on('create_room_message_to_server', function (data) {  
		room_name = data["room_name"];
		if(room_name.length === 0){
			socket.emit('create_room_fail_message_to_client', {message: "plase input the room name!"});
		}
		else if(room_list.hasOwnProperty(room_name)){
			socket.emit('create_room_fail_message_to_client', {message: "room name already exist!"});
		}
		else{
			new_room = {
				room_name: data["room_name"],
				room_type: data["room_type"],
				room_password: data["room_password"],
				room_creater: data["room_creater"],
				room_user: [],
				room_ban_user: [],
				room_mute_user_time:[],
				message: []
			}
			socket.data.owned_room=data["room_name"];
			socket.data.room=data["room_name"];
			room_list[data["room_name"]] = new_room;
			socket.emit('create_room_success_message_to_client', {room_name: data["room_name"], room_type: data["room_type"]});
		}
		
    });

	//send all the room to client to display
	socket.on('get_room_list_message_to_server', function () {
		socket.emit('get_room_list_message_to_client', {room_list: room_list});
	});

	//send all the messages from the room to client
	socket.on("get_current_room_message_to_server", function(data){
		socket.emit('get_current_room_message_to_client', {room: room_list[data.room_name]});
	});

	//send the room to the client
	socket.on("get_room_state_to_server", function(data){
		socket.emit('get_room_state_to_client', {room: room_list[data.room_name]});
	});


	socket.on("user_join_room_to_server_public", function(data){

		for(var i=0;i<room_list[data.room_name].room_ban_user.length; i++){
			if(room_list[data.room_name].room_ban_user[i] == data["username"]){
				socket.emit("user_join_room_fail_public", {message: "user_banned"});
				return ;
			}
		}
		
		room_list[data.room_name].room_user.push(data["username"]);
		socket.data.room=data["room_name"];
		socket.join(data.room_name);
		socket.emit("user_join_room_success_public",{message: "success"});
		io.sockets.emit('update_room_info');
	});

	socket.on("user_join_room_to_server_private", function(data){
		for(var i=0;i<room_list[data.room_name].room_ban_user.length; i++){
			if(room_list[data.room_name].ban_user[i] == data["username"]){
				socket.emit("user_join_room_fail_private", {message: "user_banned"});
				return ;
			}
		}
		if(room_list[data.room_name].room_password == data.room_password_guess){
			room_list[data.room_name].room_user.push(data["username"]);
			socket.join(data.room_name);
			socket.data.room=data["room_name"];
			socket.emit("user_join_room_to_client_private_success");
			io.sockets.emit('update_room_info');
		};
	});

	//delete a user from a room when the user leave
	socket.on("user_leave_room_to_server", function(data){
		leaving_username = data.username;
		leaving_room_name = data.room_name;
		socket.leave(data.room_name);
		socket.data.room="none";
		if(leaving_username == room_list[leaving_room_name].room_creater){
			socket.data.owned_room="none";
			delete room_list[data.room_name];
			io.to(data.room_name).emit("close_room_to_client");
			return;
		}
		//check all the users
		for(i = 0; i < room_list[leaving_room_name].room_user.length; i++){
			if(room_list[leaving_room_name].room_user[i] == leaving_username){
				room_list[leaving_room_name].room_user.splice(i,1);
				i--;
			}
		}
		socket.emit("user_leave_room_to_client", {room:room_list[leaving_room_name]});
		io.sockets.emit('update_room_info');
	});

	//kick a user out of the room
	socket.on("kick_user_to_server", function(data){;
		socket.data.room="none";
		io.to(data.room_name).emit("kick_user_to_client",{kick_user: data.kick_user});
	});

	//add a user to the ban list and then kick it out
	socket.on("ban_user_to_server", function(data){
		room_list[data.room_name].room_ban_user.push(data.ban_user);
		socket.data.room="none";
		io.to(data.room_name).emit("kick_user_to_client",{kick_user: data.kick_user});
	});

	//sned a private message toa specific user
	socket.on("send_private_message_to_server", function(data){
        io.to(data.room_name).emit('send_private_message_to_client', {room_name:data.room_name, username: data.username, send_user:socket.data.username,message:data.message});
    });

	//send a public messgae to the all user in the room
    socket.on("send_public_message_to_server", function(data){

		if(room_list[data.room_name].room_mute_user_time.hasOwnProperty(socket.data.username)){
			const now = new Date();
			let difference = now - room_list[data.room_name].room_mute_user_time[socket.data.username];
			if(difference/60000 <=5){
				return;
			}
		}

        io.to(data.room_name).emit('send_public_message_to_client', {room_name:data.room_name, send_user:socket.data.username, message:data.message});
    });

	//set the mute time of the user.
	socket.on("mute_user_to_server", function(data){
		const now = new Date();
		room_list[data.room_name].room_mute_user_time[data.mute_user] = now;
		//io.to(data.room_name).emit("mute_user_to_client", {mute_user: data.mute_user});
	});
	//image upload to server

	    socket.on('upload_image_to_server', (imageData,filename,username, room_name) => {
	    // Decode the base64 image data and save it to a file
	    const imageBuffer = Buffer.from(imageData, 'base64');
	    imagePath = 'upload/'+filename;
		
	    fs.writeFile(imagePath, imageBuffer, (err) => {
	        if (err) {
	        	console.error(err);
	        return;
	        }
	
	        // Send the image URL back to the client
	        imageUrl = "http://ec2-18-212-188-163.compute-1.amazonaws.com:3456/" + imagePath;
	        io.to(room_name).emit('image_upload_to_client', imageUrl,username);
	    });
	 });
	// socket.on('disconnect', function () { 
	// 	leaving_username = socket.data.username;
	// 	leaving_room_name = socket.data.owned_room; 
	// 	if(leaving_username == room_list[leaving_room_name].room_creater){
	// 		delete room_list[leaving_room_name];
	// 		io.to(data.room_name).emit("close_room_to_client");
	// 	}else{
	// 		for(i = 0; i < room_list[leaving_room_name].room_user.length; i++){
	// 			if(room_list[leaving_room_name].room_user[i] == leaving_username){
	// 				room_list[leaving_room_name].room_user.splice(i,1);
	// 				i--;
	// 			}
	// 		}
	// 	}
	// 	socket.emit("user_leave_room_to_client", {room:room_list[leaving_room_name]});
	// 	io.sockets.emit('update_room_info');
	// 	io.sockets.emit('logout_success_braodcast_to_client',{logout_success_user:socket.data.username});
    // });
});
