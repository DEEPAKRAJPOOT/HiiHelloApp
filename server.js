const axios 	= 	require('axios');
const express 	= 	require('express');
const app 		= 	express();
const server 	= 	require("http").Server(app);
const path 		= 	require('path');
const io 		= 	require('socket.io')(server, { pingInterval: 2000, pingTimeout: 10000 });
const cors 		= 	require('cors');
const mysql 	= 	require('mysql');

require('dotenv').config();

const BASE_URL 			= 	process.env.CHAT_URL;
const port 				= 	process.env.CHAT_PORT;
const db_host 			= 	process.env.DB_HOST;
const db_port 			= 	process.env.DB_PORT;
const db_database 		= 	process.env.DB_DATABASE;
const db_username 		= 	process.env.DB_USERNAME;
const db_password 		= 	process.env.DB_PASSWORD;

const tech 		= 	io.of('/');

// const port 		= 	8081;
// const BASE_URL = "https://hi-hello-app.s3.ap-south-1.amazonaws.com/";
// const BASE_URL 	= 	"http://127.0.0.1:8081/";

// app.use(express.static(path.join(__dirname, 'public')));
app.use(cors());

/* MySQL Connections */
var connection = mysql.createConnection({
	host     : db_host,
	port     : db_port,
	user     : db_username,
	password : db_password,
	database : db_database
	// host     : "127.0.0.1",
	// port     : "8889",
	// user     : "root",
	// password : "root",
	// database : "la_hi_hello"
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

	/* Manage Typing */
		// socket.on('typing', (request) => {
		// 	console.log((request.user)+' is typing...');
		// 	io.in(request.room).emit('typing', request)
		// });

	/* Send Message */
		socket.on('message', (request) => {
			// console.log("Data :: "+ JSON.stringify(request));
			
			let selectSupportRoom = "SELECT * FROM chat_rooms where custom_id = ?";
		
			connection.query(selectSupportRoom, [request.message.room], (error, _supportRoom) => {
				if( error ) throw error;
				let supportRoom = _supportRoom[0];

				if( supportRoom === undefined ) {
					 io.in(request.message.room).emit('went-wrong');
					// let creareRoom = 'INSERT INTO chat_rooms values(NULL,"aseqwreqwreqwr",'+request.message.creator_id+','+request.message.participate_id+',"y",null,null,null)';
	
					// connection.query(creareRoom, [], (error, _creareRoom) => {
					// 	console.log('Support Room Created',creareRoom ); 
					// });
					console.log('Support Room Not Found' ); 
					return false;
				}

				// Add Details To Chat

				let senderId = supportRoom.creator_id;
				let receiverId = supportRoom.participate_id;
				let msg_send = msg_delivered = msg_read = 'n';

				let supportMessage = {
					custom_id	: 	request.message.id,
					room_id		: 	supportRoom.id,
					sender_id	: 	senderId,
					receiver_id	: 	receiverId,
					created_at 	: 	request.message.time,
					updated_at 	: 	request.message.time,
				};

				supportMessage = {
					... supportMessage,
					message : request.message.value,
				};

				/*if(request.message.type == 'text'){
					supportMessage = {
						... supportMessage,
						message : request.message.value,
					};	
				}
				else if(request.message.type == 'location'){
					supportMessage = {
						... supportMessage,
						message: request.message.value,
						// lat: request.message.other.lat,
						// lng: request.message.other.lng
					};	
				}*/

				// console.log("Message **** "+JSON.stringify(supportMessage));
				let addRecord = "INSERT INTO `chat_messages` SET ? ";

				let sql = connection.query(addRecord, supportMessage, (error, result) => {
					if( error ) throw error;	
					// console.log("Result ::"+JSON.stringify(result));

					let lastRecord = "SELECT * FROM chat_messages WHERE ID = ?";
					
					let sql = connection.query(lastRecord, result.insertId, (error, _lastResult) => {
						lastResult = _lastResult[0];
						if( lastResult === undefined ) { 
							io.in(request.message.room).emit('went-wrong'); 
							console.log('Last Record Not Found'); 
							return false; 
						}
						 
						// retrive user data
						let user_query = "SELECT * FROM users where id = ? ";

						let sql1 = connection.query(user_query, supportRoom.creator_id, (error, user_result) => {
							if( error ) throw error;
							let user = user_result[0];

							// retrive message
							message =  lastResult.message.value;

				            // create return object
							let returnObject = {
								id   		: 	lastResult.custom_id,
								type  		: 	'text',
								// type.  		: 	lastResult.type,
								value 		: 	message,
								sender_id 	: 	lastResult.sender_id,
								date 		: 	lastResult.created_at,
								user 		: {
									id 			: user.custom_id,
									first_name 	: user.first_name,
									last_name 	: user.last_name,
									profile 	: BASE_URL+user.profile_photo,
								}
							}
							console.log("Return Object ::"+JSON.stringify(returnObject));
							console.log("Room Id ::"+request.message.room);

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
		});

	/* Mark As Delivered Message */
		socket.on('message-delivered', (request) => {
			// CHECK MESSAGE FROM DB
			// MARK AS DELIVERED
			// EMIT BACK
		});

	/* Read Message */
		socket.on('read-message', (request) => {
			// CHECK MESSAGE FROM DB
			// MARK AS READ
			// EMIT BACK
		});
});