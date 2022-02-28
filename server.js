const axios 	= 	require('axios');
const express 	= 	require('express');
const app 		= 	express();
const server 	= 	require("http").Server(app);
const path 		= 	require('path');
const cors 		= 	require('cors');
const io 		= 	require('socket.io')(server, { pingInterval: 2000, pingTimeout: 10000, allowEIO3: true });
const mysql 	= 	require('mysql');

// io.origins('*:*');

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

// const BASE_URL = "https://hi-hello-app.s3.ap-south-1.amazonaws.com/";
// const BASE_URL = "hihellapp.cx3wyfpc93bh.ap-south-1.rds.amazonaws.com";
// const BASE_URL 	= 	"http://chat.hihelloapp.com/";
const BASE_URL 	= 	"http://127.0.0.1:8081/";

app.use(express.static(path.join(__dirname, 'public')));
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

	// host     : "hihellapp.cx3wyfpc93bh.ap-south-1.rds.amazonaws.com",
	// port     : "3306",
	// user     : "admin",
	// password : "JINJN5A0cELWaT6xRJ1S",
	// database : "dev_hi_hello_app"
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
		if(request.id && request.room_id && request.sender_id && request.receiver_id && request.message_type && request.message_value && request.time){

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

						if(request.message_type == 'location' && request.message_lat && request.message_lng ){
							json_message = '{ "type" : "'+request.message_type+'", "value" : "'+request.message_value+'", "other" : { "lat" : "'+request.message_lat+'", "lng" : "'+request.message_lng+'"} }';
						}
						else if(request.message_type == 'file' && request.message_file_path && request.message_file_type ){
							json_message = '{ "type" : "'+request.message_type+'", "value" : "'+request.message_value+'", "other" : { "path" : "'+request.message_file_path+'", "type" : "'+request.message_file_type+'"} }';
						}
						else{
							json_message = '{ "type" : "'+request.message_type+'", "value" : "'+request.message_value+'", "other" : "[]" }';
						}

						let addMessageData = {
							custom_id	: 	request.id,
							room_id		: 	chatRoom.id,
							sender_id	: 	sender.id,
							receiver_id	: 	receiver.id,
							message 	: 	json_message,
							created_at 	: 	request.time,
							updated_at 	: 	request.time,
						};

						let addRecord = "INSERT INTO `chat_messages` SET ?";
						let sql = connection.query(addRecord, addMessageData, (error, _addMessage) => {
							if( error ) throw error;	
							
				            // create return object
							let returnObject = {
								id   		: 	chatRoom.custom_id,
								message: {
					                type 	: request.message_type,
					                value  	: request.message_value,
					                other 	:  {
					                	path 	: 	request.message_file_path,
					                	type 	: 	request.message_file_type,
					                	lat 	: 	request.message_lng,
					                	lng 	: 	request.message_lat,
					                	url     :   'https://maps.googleapis.com/maps/api/staticmap?center='+request.message_lng+','+request.message_lat+'&zoom=14&size=400x400&markers='+request.message_lng+','+request.message_lat+'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ',
  					                },
					            },
								status 		:   'send',
								sender 		: 	{
									id 		: 	sender.custom_id,
								},
								updated_at 	: 	request.time,
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
					message_parse =  JSON.parse(selectMessage.message);
					
					// create return object
					let returnObject = {
						id   		: 	selectMessage.custom_id,
						message: {
							type 		: 	message_parse.type,
		                	value  		: 	message_parse.value,
			            },
						status 		:   status,
						sender 		: 	{
							id 		: 	request.sender_id,
						},
						updated_at 	: 	selectMessage.updated_at,
					};

					if(message_parse.type == 'location'){
						returnObject = {
							... returnObject,
							message: {
								type 		: 	message_parse.type,
			                	value  		: 	message_parse.value,
								other 	:  {
				                	lat 	: 	message_parse.other.lng,
				                	lng 	: 	message_parse.other.lat,
				                	url     :   'https://maps.googleapis.com/maps/api/staticmap?center='+message_parse.other.lng+','+message_parse.other.lat+'&zoom=14&size=400x400&markers='+message_parse.other.lng+','+message_parse.other.lat+'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ',
					            },
					        },
						};	
					}else if(message_parse.type == 'file'){
						returnObject = {
							... returnObject,
							message: {
								type 		: 	message_parse.type,
			                	value  		: 	message_parse.value,
								other 	:  {
				                	path 	: 	message_parse.other.path,
				                	type 	: 	message_parse.other.type,
				            	},
				            },
						};	
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

	/* Get Chat Rooms */
	// socket.on('get-rooms', (request) => {
	// 	if(request.user_id){
	// 		let selectUser = "SELECT * FROM users where custom_id = ? and is_active = 'y'";
	// 		let sql1 = connection.query(selectUser, request.user_id, (error_user, user_result) => {
	// 			if( error_user ) throw error_user;
	// 			let user 	=	user_result[0];
	// 			let limit 	= 	request.limit ?? 10;
	// 			let offset 	= 	request.offset ?? 0;

	// 			if( user === undefined ) {
	// 				io.in(request.user_id).emit('went-wrong');
	// 				console.log('User Not Found'); 
	// 				return false;
	// 			}

	// 			let selectRooms = "select * from `chat_rooms` where (`creator_id` = ? or `participate_id` = ? and `is_active` = 'y') and `chat_rooms`.`deleted_at` is null order by `created_at` desc limit ? offset ?";
					
	// 			let sql2 = connection.query(selectRooms, [user.id, user.id, limit, offset], (error_rooms, _room_result) => {
	// 				if( error_rooms ) throw error_rooms;
	// 				let chat_rooms = _room_result;

	// 				var returnObject = [];

					
	// 				// create return object
	// 				for ( let i = 0; i < chat_rooms.length; i++) {
	// 					var chat_room = chat_rooms[i];
	// 					var participate_id = chat_room.participate_id;

	// 					if(user.id == chat_room.participate_id){
	// 						var participate_id = chat_room.creator_id;
	// 					}

	// 					let selectParticipant = "SELECT * FROM users where id = ? and is_active = 'y'";
	// 					let sql3 = connection.query(selectParticipant, participate_id, (error_participant, _participant_result) => {
	// 						if( error_participant ) throw error_participant;
	// 						let participant = _participant_result[0];

	// 						if( participant != undefined ) {
	// 							chat_room = {
	// 								... chat_room,
	// 								participant: participant,
	// 							};	

	// 							console.log(chat_room );
	// 							return false;
	// 						}
	// 					});
	// 			    }	

	// 				// for(i=0; i<chat_rooms; i++){
	// 					// current_room = chat_rooms[i];

	// 					// let returnObject = {

	// 				// 	id   		: 	chatRoom.custom_id,
	// 				// 	message: {
	// 		  //               type 	: request.message_type,
	// 		  //               value  	: request.message_value,
	// 		  //               other 	:  {
	// 		  //               	path 	: 	request.message_file_path,
	// 		  //               	type 	: 	request.message_file_type,
	// 		  //               	lat 	: 	request.message_lng,
	// 		  //               	lng 	: 	request.message_lat,
	// 			 //            },
	// 		  //           },
	// 				// 	status 		:   'send',
	// 				// 	sender 		: 	{
	// 				// 		id 		: 	sender.custom_id,
	// 				// 	},
	// 				// 	updated_at 	: 	request.time,



	// 					// id            :  current_room.custom_id,
	// 		            // is_active     :  current_room.is_active,
	// 		            // 'creator'  =>  [
	// 		            //     'id'            =>  $this->creator ? $this->creator->custom_id : "",
	// 		            //     'first_name'    =>  $this->creator ? $this->creator->first_name : "",
	// 		            //     'last_name'     =>  $this->creator ? $this->creator->last_name : "",
	// 		            //     'profile'       =>  $this->creator ? generateURL($this->creator->profile_photo) : "",
	// 		            // ],
	// 		            // 'participator'  =>  [
	// 		            //     'id'            =>  $this->participator ? $this->participator->custom_id : "",
	// 		            //     'first_name'    =>  $this->participator ? $this->participator->first_name : "",
	// 		            //     'last_name'     =>  $this->participator ? $this->participator->last_name : "",
	// 		            //     'profile'       =>  $this->participator ? generateURL($this->participator->profile_photo) : "",
	// 		            // ],
	// 		            // 'message'   =>  [
	// 		            //     'id'            =>  $this->latestMessage ? $this->latestMessage->custom_id : "",
	// 		            //     'value'         =>  $this->latestMessage ? $this->latestMessage->message : "",
	// 		            //     'status'        =>  $this->latestMessage ? $this->latestMessage->status : "",
	// 		            //     'updated_at'    =>  $this->latestMessage ? $this->latestMessage->updated_at : "",
	// 		            // ]
	// 					// }
	// 				// }

	// 				console.log("Final :: ",returnObject);
	// 				return false;

	// 				console.log("Message Object ::"+JSON.stringify(returnObject));
	// 				io.in(request.room_id).emit('message', returnObject);
	// 			});

	// 		});

	// 	}
	// 	else{
	// 		console.log("Precondition Failed !!!");
	// 		return false; 
	// 	}
	// });
});