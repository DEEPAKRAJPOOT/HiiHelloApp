const axios 	= 	require('axios');
const express 	= 	require('express');
const app 		= 	express();
const server 	= 	require("http").Server(app);
const path 		= 	require('path');
const io 		= 	require('socket.io')(server, { pingInterval: 2000, pingTimeout: 10000 });
const cors 		= 	require('cors');
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
const port 		= 	8081;

// const BASE_URL = "https://hi-hello-app.s3.ap-south-1.amazonaws.com/";
const BASE_URL 	= 	"http://127.0.0.1:8081/";

// app.use(express.static(path.join(__dirname, 'public')));
app.use(cors());

/* MySQL Connections */
var connection = mysql.createConnection({
	// host     : db_host,
	// port     : db_port,
	// user     : db_username,
	// password : db_password,
	// database : db_database

	host     : "127.0.0.1",
	port     : "8889",
	user     : "root",
	password : "root",
	database : "la_hi_hello"
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
			// console.log("Data :: "+ JSON.stringify(request));
			if(request.message && request.message.id && request.message.room && request.message.creator_id && request.message.participate_id && request.message.value && request.message.time){
				
				let selectChatRoom = "SELECT * FROM chat_rooms where custom_id = ? and creator_id = ? and participate_id = ?";
				connection.query(selectChatRoom, [request.message.room, request.message.creator_id,request.message.participate_id],(error, _chatRoom) => {
					if( error ) throw error;
					let chatRoom = _chatRoom[0];

					if( chatRoom === undefined ) {
						io.in(request.message.room).emit('went-wrong');
						console.log('Chat Room Not Found'); 
						return false;
					}

					// Add Details To Chat
					let msg_send = msg_delivered = msg_read = 'n';

					let addMessage = {
						custom_id	: 	request.message.id,
						room_id		: 	chatRoom.id,
						sender_id	: 	chatRoom.creator_id,
						receiver_id	: 	chatRoom.participate_id,
						message 	: 	request.message.value,
						created_at 	: 	request.message.time,
						updated_at 	: 	request.message.time,
					};

					// console.log("Message **** "+JSON.stringify(addMessage));
					let addRecord = "INSERT INTO `chat_messages` SET ? ";

					let sql = connection.query(addRecord, addMessage, (error, result) => {
						if( error ) throw error;	

						let lastRecord = "SELECT * FROM chat_messages WHERE ID = ?";
						let sql = connection.query(lastRecord, result.insertId, (error, _lastMessage) => {
							lastMsg = _lastMessage[0];

							if( lastMsg === undefined ) { 
								io.in(request.message.room).emit('went-wrong'); 
								console.log('Last Message Not Found'); 
								return false; 
							}

							// retrive user data
							let user_query = "SELECT * FROM users where id = ? ";

							let sql1 = connection.query(user_query, chatRoom.creator_id, (error, user_result) => {
								if( error ) throw error;
								let creator = user_result[0];

					            // create return object
								let returnObject = {
									id   			: 	lastMsg.custom_id,
									room   			: 	request.message.room,
									creator_id 		:  	chatRoom.creator_id,
									participate_id 	:  	chatRoom.participate_id,
									type  			: 	lastMsg.message.type,
									value 			: 	lastMsg.message.value,
									time 			: 	request.message.time,
									creator 	: {
										id 			: creator.custom_id,
										first_name 	: creator.first_name,
										last_name 	: creator.last_name,
										profile 	: BASE_URL+creator.profile_photo,
									}
								}
								console.log("Return Object ::"+JSON.stringify(returnObject));
								io.in(request.message.room).emit('message', returnObject);		
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
			if(request.message && request.message.id && request.message.room && request.message.sender_id ){				
				let selectChatMessage = "SELECT * FROM chat_messages where custom_id = "+"'"+request.message.id+"'"+" and sender_id = "+request.message.sender_id+"";

				connection.query(selectChatMessage, [], (error, _selectMessage) => {
					if( error ) throw error;
					let selectMessage = _selectMessage[0];

					if( selectMessage === undefined ) {
						io.in(request.message.id).emit('went-wrong');
						console.log('Message Not Found'); 
						return false;
					}
					// let msgDelivered = "UPDATE chat_messages SET status = 'delivered', deleted_at = NULL WHERE chat_messages.custom_id = "+"'"+selectMessage.custom_id+"'"+"";
					let msgDelivered = "UPDATE chat_messages SET status = 'delivered' WHERE custom_id = ? ";

					let sql = connection.query(msgDelivered, [selectMessage.custom_id], (delivered_error, _deliveredMessage) => {
						if( delivered_error ) throw delivered_error;

						// return object
						let returnObject = {
							id   			: 	selectMessage.custom_id,
							room   			: 	request.message.room,
							creator_id 		:  	selectMessage.sender_id,
							participate_id 	:  	selectMessage.receiver_id,
							value 			: 	selectMessage.value,
							time 			: 	selectMessage.updated_at,
						}

						console.log("Delivered Message Object ::"+JSON.stringify(returnObject));
						io.in(request.message.room).emit('message', returnObject);	
					});
				});
			}else{
				console.log("Precondition Failed !!!");
				return false; 
			}
			// CHECK MESSAGE FROM DB
			// MARK AS DELIVERED
			// EMIT BACK
		});

	/* Read Message */
		socket.on('read-message', (request) => {
			if(request.message && request.message.id && request.message.room && request.message.sender_id ){
				let selectChatMessage = "SELECT * FROM chat_messages where custom_id = "+"'"+request.message.id+"'"+" and sender_id = "+request.message.sender_id+"";

				connection.query(selectChatMessage, [], (error, _selectMessage) => {
					if( error ) throw error;
					let selectMessage = _selectMessage[0];

					if( selectMessage === undefined ) {
						io.in(request.message.id).emit('went-wrong');
						console.log('Message Not Found'); 
						return false;
					}
					let msgRead = "UPDATE chat_messages SET status = 'read' WHERE custom_id = ? ";

					let sql = connection.query(msgRead, [selectMessage.custom_id], (read_error, _readMessage) => {
						if( read_error ) throw read_error;

						// return object
						let returnObject = {
							id   			: 	selectMessage.custom_id,
							room   			: 	request.message.room,
							creator_id 		:  	selectMessage.sender_id,
							participate_id 	:  	selectMessage.receiver_id,
							value 			: 	selectMessage.value,
							time 			: 	selectMessage.updated_at,
						}
						console.log("Read Message Object ::"+JSON.stringify(returnObject));
						io.in(request.message.room).emit('message', returnObject);	
					});
				});
			}else{
				console.log("Precondition Failed !!!");
				return false; 
			}

			// CHECK MESSAGE FROM DB
			// MARK AS READ
			// EMIT BACK
		});

	/* Error Things */
		socket.on('went-wrong', (request)=>{
			console.log("Error Message :: ",request);
			return false; 
		});
});