const axios 	= 	require('axios');
const express 	= 	require('express');
const app 		= 	express();
const server 	= 	require("http").Server(app);
const path 		= 	require('path');
const cors 		= 	require('cors');
const io 		= 	require('socket.io')(server, { pingInterval: 2000, pingTimeout: 10000, allowEIO3: true });
const mysql 	= 	require('mysql');

// require('dotenv').config();
// const BASE_URL 			= 	process.env.CHAT_URL;
// const port 				= 	process.env.CHAT_PORT;
// const db_host 			= 	process.env.DB_HOST;
// const db_port 			= 	process.env.DB_PORT;
// const db_database 		= 	process.env.DB_DATABASE;
// const db_username 		= 	process.env.DB_USERNAME;
// const db_password 		= 	process.env.DB_PASSWORD;

const tech 		= 	io.of('/');
const port 		= 	8080;

// const BASE_URL 	= 	"http://chat.hihelloapp.com/";
const BASE_URL 	= 	"http://127.0.0.1:8081/";

app.use(express.static(path.join(__dirname, 'public')));
app.use(cors());

/* MySQL Connections */
var connection = mysql.createConnection({
	host     : "127.0.0.1",
	port     : "8889",
	user     : "root",
	password : "root",
	database : "la_hi_hello"

	// host     : "hihellapp.cx3wyfpc93bh.ap-south-1.rds.amazonaws.com",
	// port     : "3306",
	// user     : "admin",
	// password : "JINJN5A0cELWaT6xRJ1S",
	// database : "hihelloapp"
});

/* Listen On Respective Port */
server.listen(port, ()=> { console.log(':: SERVER IS LISTEN ON '+ port+' ::'); });
connection.connect((error) => { if( error ) throw error; });

var i = 0;
let users = [];
let overallUsers = [];

io.on('connection', (socket)=>{
	/* User Joined The Chat */
	socket.on('join',(request)=>{
		socket.join(request.room);
		console.log("********* CHAT JOINED With Room :: "+request.room + " *********");

		if( !users[request.room] ) users[request.room] = [];

		// Ignore user if already added into the array
			if (!users[request.room].includes(socket.id)) users[request.room].push(socket.id);
			if (!overallUsers.includes(socket.id)) overallUsers.push(socket.id);
	});

	/* User Disconnected From Chat */
	socket.on('disconnect', (request)=>{
		socket.leave(request.room);
		console.log('**** DISCONNECT CALLED ****');
		// Remove From Overall List
			let user = overallUsers.indexOf(socket.id);
			if (user > -1) overallUsers.splice(user, 1);
			console.log(overallUsers);

		// Remove From ChatRoom
			for (const [key, value] of Object.entries(users)) {
				if (value.includes(socket.id)) {
					value.splice( value.indexOf(socket.id) ,1)

					// Free Room Key If No Users Are There
						if (value.length == 0) delete users[key]
				}
			}
	});

	/* Send Message */
	socket.on('message', (request) => {
		if(request.id && request.room_id && request.sender_id && request.receiver_id && request.message && request.time){

			let selectSender = "SELECT * FROM users where custom_id = ? and is_active = 'y'";
			let sql1 = connection.query(selectSender, request.sender_id, (error, sender_result) => {
				if( error ) throw error;
				let sender = sender_result[0];

				if( sender === undefined ) {
					io.in(request.sender_id).emit('went-wrong');
					console.log('Sender Not Found'); 
					return false;
				}
				
				let selectReceiver = "SELECT * FROM users where custom_id = ? and is_active = 'y'";
				let sql1 = connection.query(selectReceiver, request.receiver_id, (error, receiver_result) => {
					if( error ) throw error;
					let receiver = receiver_result[0];
					
					if( receiver === undefined ) {
						io.in(request.receiver_id).emit('went-wrong');
						console.log('Receiver Not Found'); 
						return false;
					}

					let selectChatRoom = "SELECT * FROM chat_rooms where custom_id = ? and is_active = 'y'";
					connection.query(selectChatRoom, [request.room_id],(error, _chatRoom) => {
						if( error ) throw error;
						let chatRoom = _chatRoom[0];

						if( chatRoom === undefined ) {
							io.in(request.room_id).emit('went-wrong');
							console.log('Chat Room Not Found'); 
							return false;
						}	

						let addMessageData = {
							custom_id	: 	request.id,
							room_id		: 	chatRoom.id,
							sender_id	: 	sender.id,
							receiver_id	: 	receiver.id,
							message 	: 	request.message,
							created_at 	: 	request.time,
							updated_at 	: 	request.time,
						};

						let addRecord = "INSERT INTO `chat_messages` SET ?";
						let sql = connection.query(addRecord, addMessageData, (error, _addMessage) => {
							if( error ) throw error;	
							
				            // create return object
							let returnObject = {
								room_id   		: 	chatRoom.id,
								sender_id 		:  	sender.id,
								receiver_id 	:  	receiver.id,
								time 			: 	request.time,
								message 		: 	request.message,
								sender 	: {
									id 			: 	sender.custom_id,
									first_name 	: 	sender.first_name,
									last_name 	: 	sender.last_name,
									profile 	: 	BASE_URL+sender.profile_photo,
								},
								receiver 	: {
									id 			: 	receiver.custom_id,
									first_name 	: 	receiver.first_name,
									last_name 	: 	receiver.last_name,
									profile 	: 	BASE_URL+receiver.profile_photo,
								}
							}
							console.log("Return Object ::"+JSON.stringify(returnObject));
							io.in(request.room_id).emit('message', returnObject);		

						});
					});
				});

				// VALIDATE THE USER [ FROM DB ]
					// VALIDATE THE ROOM DETAILS [ FROM DB ]
					// ADD MESSAGE TO DB 
					// EMIT MESSAGE AGAIN
					// SEND PUSH NOTIFICATION [ IF IN THE SCOPE ]
			});
		}else{
			console.log("Precondition Failed !!!");
			return false; 
		}
	});

	/* Mark As Delivered Message */
	socket.on('message-delivered', (request) => {
		updateMessageStatus(request,'delivered');
	});

	/* Read Message */
	socket.on('read-message', (request) => {
		updateMessageStatus(request,'read');
	});

	/* Error Things */
	socket.on('went-wrong', (request)=>{
		console.log("Error Message :: ",request);
		return false; 
	});

	/* Update Message */
	function updateMessageStatus(request, status){
		if(request.id && request.room_id && request.sender_id && request.receiver_id){		
			let selectChatMessage = "SELECT * FROM chat_messages where custom_id = ?";

			connection.query(selectChatMessage, [request.id], (error, _selectMessage) => {
				if( error ) throw error;
				let selectMessage = _selectMessage[0];
				
				if( selectMessage === undefined ) {
					io.in(request.id).emit('went-wrong');
					console.log('Message Not Found'); 
					return false;
				}

				let updateMessage = "UPDATE chat_messages SET status = ? WHERE custom_id = ? ";
				let sql = connection.query(updateMessage, [status, selectMessage.custom_id], (read_error, _message) => {
					if( read_error ) throw read_error;

					// create return object
					let returnObject = {
						id   			: 	selectMessage.custom_id,
						room_id   		: 	request.room_id,
						sender_id 		:  	selectMessage.sender_id,
						receiver_id 	:  	selectMessage.receiver_id,
						message 		: 	selectMessage.message,
						status 			: 	status,
						time 			: 	selectMessage.updated_at,
					}
					console.log("Message Object ::"+JSON.stringify(returnObject));
					io.in(request.room_id).emit('message', returnObject);
				});
			});
		}else{
			console.log("Precondition Failed !!!");
			return false; 
		}

		// CHECK MESSAGE FROM DB
		// MARK AS DELIVERED OR READ
		// EMIT BACK
	}
});
