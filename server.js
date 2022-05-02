const axios 	= 	require('axios');
const express 	= 	require('express');
const app 		= 	express();
const server 	= 	require("http").Server(app);
const path 		= 	require('path');
const cors 		= 	require('cors');
const io 		= 	require('socket.io')(server, { pingInterval: 2000, pingTimeout: 10000, allowEIO3: true });
const mysql 	= 	require('mysql');
const tech 		= 	io.of('/');
const port 		= 	8080;

// const APP_URL 	= 	"https://hihelloapp.com/";
const APP_URL 	= 	"http://localhost:8000/";

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
	// database : "dev_hi_hello_app"
});

/* Listen On Respective Port */
server.listen(port, ()=> { console.log(':: SERVER IS LISTEN ON '+ port+' ::'); });
connection.connect((error) => { if( error ) throw error; });

var i = 0;
let users = [];
let overallUsers = [];

io.on('connection', (socket)=>{
	/* User Joined The Global Chat When It's Online */
	socket.on('join-global',(request)=>{		
		socket.join(request.user_id);
		console.log("********* OVERALL JOINED With ID :: "+request.user_id + " *********");

		if (!overallUsers.includes(request.user_id)) overallUsers.push(request.user_id);
		
		// Send Online Method
		io.sockets.emit("online", overallUsers);
	});

	/* User Joined The Room Chat When Enter In Any Room */
	socket.on('join-room',(request)=>{		
		socket.join(request.room_id);
		console.log("********* CHAT JOINED With Room ID :: "+request.room_id + " *********");

		if( !users[request.room_id] ) users[request.room_id] = [];

		// Ignore user if already added into the array
		if (!users[request.room_id].includes(request.user_id)) users[request.room_id].push(request.user_id);

		// Send Online Method
		io.sockets.emit("online", overallUsers);
	});

	/* User Disconnected From Chat Room */
	socket.on('disconnect-room', (request)=>{
		console.log('**** DISCONNECT ROOM ****');

		// Remove From ChatRoom
		for (const [key, value] of Object.entries(users)) {
			if (value.includes(request.user_id)) {
				value.splice( value.indexOf(request.user_id) ,1)

				// Free Room Key If No Users Are There
					if (value.length == 0) delete users[key]
			}
		}
	});

	/* User Disconnected From Global Chat (Offline) */
	socket.on('disconnect-global', (request)=>{
		console.log('**** DISCONNECT GLOBAL ****');

		// Remove From ChatRoom
		for (const [key, value] of Object.entries(users)) {
			if (value.includes(request.user_id)) {
				value.splice( value.indexOf(request.user_id) ,1)

				// Free Room Key If No Users Are There
				if (value.length == 0) delete users[key]
			}
		}

		// Remove From Overall List
		let user = overallUsers.indexOf(request.user_id);
		if (user > -1) overallUsers.splice(user, 1);

		// Send Offline Method
		io.sockets.emit("offline", overallUsers);
	});


	/* Offline (Auto Disconnect By Socket) */
  	socket.on("disconnecting", (reason) => {
  		let user_id = [...socket.rooms][1];  // request.user_id (custom_id) which we have pass at join time
      	console.log("Disconnect User ID ::", user_id);

      	// Remove From ChatRoom
		for (const [key, value] of Object.entries(users)) {
			if (value.includes(user_id)) {
				value.splice( value.indexOf(user_id) ,1)

				// Free Room Key If No Users Are There
				if (value.length == 0) delete users[key]
			}
		}
		
      	// Remove From Overall List
		let user = overallUsers.indexOf(user_id);
		if (user > -1) overallUsers.splice(user, 1);

		// Send Offline Method
		io.sockets.emit("offline", overallUsers);
  	});


	/* Send New Message */
	socket.on('send-message', (request) => {
		if(request.id && request.room_id && request.sender_id && request.receiver_id && request.message_type && request.message_value && request.time){

			let selectSender = "SELECT * FROM users where custom_id = ? and deleted_at is NULL and is_active = 'y'";
			let sql1 = connection.query(selectSender, request.sender_id, (error, sender_result) => {
				if( error ) throw error;
				let sender = sender_result[0];

				if( sender === undefined ) {
					io.in(request.sender_id).emit('went-wrong','Sender Not Found');
					console.log('Sender Not Found'); 
					return false;
				}

				let selectReceiver = "SELECT * FROM users where custom_id = ? and deleted_at is NULL and is_active = 'y'";
				let sql1 = connection.query(selectReceiver, request.receiver_id, (error, receiver_result) => {
					if( error ) throw error;
					let receiver = receiver_result[0];
					
					if( receiver === undefined ) {
						io.in(request.receiver_id).emit('went-wrong','Receiver Not Found');
						console.log('Receiver Not Found'); 
						return false;
					}

					let selectChatRoom = "SELECT * FROM chat_rooms where custom_id = ? and deleted_at is NULL and is_active = 'y'";
					connection.query(selectChatRoom, [request.room_id],(error, _chatRoom) => {
						if( error ) throw error;
						let chatRoom = _chatRoom[0];

						if( chatRoom === undefined ) {
							io.in(request.room_id).emit('went-wrong','Chat Room Not Found');
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
							json_message = '{ "type" : "'+request.message_type+'", "value" : "'+request.message_value+'", "other" : {} }';
						}	

						console.log("users :: ",users);
						console.log("overallUsers :: ",overallUsers);

						// If Both User In Same Room (Both Online)
						let msg_status = 'send';
						if ( overallUsers.includes(request.receiver_id) 
							&& users[request.room_id] 
							&& users[request.room_id].includes(request.sender_id) 
							&& users[request.room_id].includes(request.receiver_id)){
								msg_status = 'read';
						}

						let addMessageData = {
							custom_id	: 	request.id,
							room_id		: 	chatRoom.id,
							sender_id	: 	sender.id,
							receiver_id	: 	receiver.id,
							message 	: 	json_message,
							status 		: 	msg_status,
							created_at 	: 	request.time,
							updated_at 	: 	request.time,
						};

						let addRecord = "INSERT INTO `chat_messages` SET ?";
						let sql = connection.query(addRecord, addMessageData, (error, _addMessage) => {
							if( error ) throw error;	
								
				            // create return object
				            let returnSendMsg = {
								id   		: 	request.id,
								message: {
									type 		: 	request.message_type,
				                	value  		: 	request.message_value,
				                	other 		:  	{},
					            },
								status 		:   msg_status,
								sender 		: 	{
									id 		: 	sender.custom_id,
								},
								created_at 	: 	request.time,
								updated_at 	: 	request.time,
							};

							if(request.message_type == 'location'){
								returnSendMsg = {
									... returnSendMsg,
									message: {
										type 		: 	request.message_type,
				                		value  		: 	request.message_value,
										other 	:  {
						                	lng 	: 	request.message_lng,
						                	lat 	: 	request.message_lat,
						                	url     :   'https://maps.googleapis.com/maps/api/staticmap?center='+request.message_lat+','+request.message_lng+'&zoom=14&size=400x400&markers='+request.message_lat+','+request.message_lng+'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ',
							            },
							        },
								};	
							}else if(request.message_type == 'file'){
								returnSendMsg = {
									... returnSendMsg,
									message: {
										type 		: 	request.message_type,
				                		value  		: 	request.message_value,
										other 	:  {
						                	path 	: 	request.message_file_path,
						                	type 	: 	request.message_file_type,
						            	},
						            },
								};	
							}

							io.in(request.room_id).emit('receive-message', returnSendMsg);	
							console.log("Send Message Object ::"+JSON.stringify(returnSendMsg));

							if(overallUsers.includes(request.receiver_id)){

								// When New User(Not From The Room) Send Message
								if (users[request.room_id] && !users[request.room_id].includes(request.receiver_id)) {
									let returnNewMsg = {
										id   				: 	chatRoom.custom_id,
										is_active   		:   chatRoom.is_active,
										creator: {
											id 				: 	sender.custom_id,
											full_name 		: 	sender.full_name,
											profile 		: 	sender.profile_photo,
										},
										participator: {
											id 				: 	receiver.custom_id,
											full_name 		: 	receiver.full_name,
											profile 		: 	receiver.profile_photo,
										},
										latest_message: {
											id 				: 	request.id,
											message : {
												type 			: 	request.message_type,
						                		value  			: 	request.message_value,
						                		other  			: 	{},
											},
						                	status  		: 	'send',
						                	created_at 		: 	request.time,
											updated_at 		: 	request.time,
							            }
									};

									io.in(request.receiver_id).emit('new-message', returnNewMsg);	
									console.log("New Message Object ::"+JSON.stringify(returnNewMsg));
								}
							}

							// Send Push Notification
							if(msg_status != 'read'){
								push_message = request.message_value; 
								sendNotification(request.room_id, request.id, push_message);
								console.log("Log: Push Notification");
							}
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

	/* Read Message */
	socket.on('message-read', (request) => {
		if(request.id && request.room_id && request.sender_id && request.receiver_id && request.time){		
			let status = 'read';

			var selectChatMessage = "SELECT * FROM chat_messages where custom_id = ? and deleted_at is NULL";
			connection.query(selectChatMessage, [request.id], (error, _selectMessage) => {
				if( error ) throw error;
				let selectMessage = _selectMessage[0];
				
				if( selectMessage === undefined ) {
					io.in(request.id).emit('went-wrong','Message Not Found');
					console.log('Message Not Found Of Id :: ',request.id); 
					return false;
				}

				let updateMessage =  "UPDATE chat_messages SET status = ?, updated_at = ?, deleted_at = NULL WHERE room_id = ? AND created_at <= ? ";
				let sql = connection.query(updateMessage, [status, request.time, selectMessage.room_id, selectMessage.created_at], (read_error, _message) => {
					if( read_error ) throw read_error;
					message_parse =  JSON.parse(selectMessage.message);
					
					// create return object
					let returnUpdatedMsg = {
						id   		: 	selectMessage.custom_id,
						message: {
							type 		: 	message_parse.type,
		                	value  		: 	message_parse.value,
			            },
						status 		:   status,
						sender 		: 	{
							id 		: 	request.sender_id,
						},
						created_at 	: 	request.time,
						updated_at 	: 	request.time,
					};

					if(message_parse.type == 'location'){
						returnUpdatedMsg = {
							... returnUpdatedMsg,
							message: {
								type 		: 	message_parse.type,
			                	value  		: 	message_parse.value,
								other 	:  {
				                	lat 	: 	message_parse.other.lng,
				                	lng 	: 	message_parse.other.lat,
				                	url     :   'https://maps.googleapis.com/maps/api/staticmap?center='+message_parse.other.lat+','+message_parse.other.lng+'&zoom=14&size=400x400&markers='+message_parse.other.lat+','+message_parse.other.lng+'&markers=color:red&key=AIzaSyA2GIt7Ld9duVo85H4Mr15Y_v7Sc6pfzlQ',
					            },
					        },
						};	
					}else if(message_parse.type == 'file'){
						returnUpdatedMsg = {
							... returnUpdatedMsg,
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

					console.log("Updated Message Object ::"+JSON.stringify(returnUpdatedMsg));
					io.in(request.room_id).emit('updated-message', returnUpdatedMsg);
				});
			});
		}else{
			console.log("Precondition Failed !!!");
			return false; 
		}

		// CHECK MESSAGE FROM DB
		// MARK AS DELIVERED OR READ
		// EMIT BACK
	});

	/*
	* room_id => For which room you want to send notification
	* chat_message => For which message you want to send notification
	* message => send message on notification
	*/
	function sendNotification(room_id, chat_message, message) {
		// if( users[room_id] !== undefined && users[room_id].length < 2 )	{
			message = message.replace(/(\r\n|\n|\r)/gm, "");

			axios.post(APP_URL + 'api/v1/chat/send-push/'+chat_message+'/'+( encodeURIComponent(message) ) )
			  	.then(response => {
					console.log('NOTIFICATION SENT'); 
				})
			  	.catch(error => {
			   		console.error(error); 
				});
		// }else{
		// 	io.in(room_id).emit('went-wrong','Notification Details Not Found');
		// 	console.log('Notification Details Not Found'); 
		// 	return false;
		// }
	}
});

/* ************************************************** Testing (Extra Method) ********************************************** */

/* Error Things */
// socket.on('went-wrong', (request)=>{
// 	console.log("Error Message :: ",request);
// 	return false; 
// });

/* Mark As Delivered Message */
/* socket.on('message-delivered', (request) => {
	if(request.user_id && request.time){	
		let status = 'delivered';

		let selectUser = "SELECT * FROM users where custom_id = ? and deleted_at is NULL and is_active = 'y'";
		let sql1 = connection.query(selectUser, request.user_id, (error, _user_result) => {
			if( error ) throw error;
			let user = _user_result[0];

			if( user === undefined ) {
				io.in(request.user_id).emit('went-wrong','User Not Found');
				console.log('User Not Found'); 
				return false;
			}	

			let deliveredMessage = "UPDATE chat_messages SET status = ?, updated_at = ? WHERE receiver_id = ?";
			let sql2 = connection.query(deliveredMessage, [status, request.time, user.id], (delivered_error, _message) => {
				if( delivered_error ) throw delivered_error;
			
				// create return object
				let returnUpdatedMsg = {
					status : status,
				};

				io.in("CMH5cW78WAvPbECUMF4o").emit('updated-message', returnUpdatedMsg);
				console.log("Delivered All Messages Of User Id :: ",request.user_id);
			});

		});
	}else{
		console.log("Precondition Failed !!!");
		return false; 
	}
*/

// 	// CHECK MESSAGE FROM DB
// 	// MARK AS DELIVERED OR READ
// 	// EMIT BACK
// });

/* Get Message Status */
/* socket.on('message-status', (request) => {
	if(request.id){	
		let selectChatMessage = "SELECT * FROM chat_messages where custom_id = ?";
		let sql1 = connection.query(selectChatMessage, request.id, (error, _selectMessage) => {
			if( error ) throw error;
			let selectMessage = _selectMessage[0];
			message_parse =  JSON.parse(selectMessage.message);
			
			if( selectMessage === undefined ) {
				io.in(request.id).emit('went-wrong','Message Not Found');
				console.log('Message Not Found Of Id :: ',request.id); 
				return false;
			}

			// create return object
			let returnUpdatedMsg = {
				id   		: 	selectMessage.custom_id,
				status 		:   selectMessage.status,
			};

			console.log("Status Message Object ::"+JSON.stringify(returnUpdatedMsg));
			io.in(request.room_id).emit('updated-message', returnUpdatedMsg);

		});
	}else{
		console.log("Precondition Failed !!!");
		return false; 
	}
});
*/

/* Get Chat Rooms */
/* socket.on('get-rooms', (request) => {
	if(request.user_id){

		let selectUser = "SELECT `id`,`custom_id`,`first_name`,`last_name`,`profile_photo` FROM users where custom_id = ? and is_active = 'y'";
		let sql1 = connection.query(selectUser, request.user_id, (error_user, user_result) => {

			if( error_user ) throw error_user;
			let user 	=	user_result[0];
			let limit 	= 	request.limit ?? 10;
			let offset 	= 	request.offset ?? 0;

			if( user === undefined ) {
				io.in(request.user_id).emit('went-wrong');
				console.log('User Not Found'); 
				return false;
			}

			let selectRooms = "select `id`,`custom_id`,`creator_id`,`participate_id`,`is_active` from `chat_rooms` where (`creator_id` = ? or `participate_id` = ? and `is_active` = 'y') and `chat_rooms`.`deleted_at` is null order by `created_at` desc limit ? offset ?";
				
			let sql2 = connection.query(selectRooms, [user.id, user.id, limit, offset], (error_rooms, _room_result) => {
				if( error_rooms ) throw error_rooms;
				let chat_rooms = _room_result;

				flag = false;
				let returnObject = [];

				// create return object
				for ( let i = 0; i < chat_rooms.length; i++) {

					var chat_room = chat_rooms[i];
					var arr_participate = [];
					var arr_message = [];
					var is_creator = is_particapate = is_message = false;

					if( !returnObject[i] ) returnObject[i] = [];
					if( !returnObject[i]['creator'] ) returnObject[i]['creator'] = [];
					if( !returnObject[i]['participant'] ) returnObject[i]['participant'] = [];
					if( !returnObject[i]['message'] ) returnObject[i]['message'] = [];

					if (!returnObject[i].includes(chat_room)) returnObject[i].push(chat_room);

					var participate_id = chat_room.participate_id;

					if(user.id == chat_room.participate_id){
						var participate_id = chat_room.creator_id;
					}

					// chat_room = {
					// 	... chat_room,
					// 	creator: user,
					// };
					// is_creator = true;

					let selectParticipant = "SELECT `id`,`custom_id`,`first_name`,`last_name`,`profile_photo` FROM users where id = ? and is_active = 'y'";
					let sql3 = connection.query(selectParticipant, participate_id, (error_participant, _participant_result) => {
						if( error_participant ) throw error_participant;
						let participant = _participant_result[0];

						if( participant != undefined ) {	
							// is_particapate = true;
							// console.log("is_particapate" , is_particapate);

							// chat_room = {
							// 	... chat_room,
							// 	participant: participant,
							// };
						}
					});

					let latestMsg =  "select * from `chat_messages` where `room_id` = ? and `chat_messages`.`deleted_at` is null order by `created_at` desc limit ?";
					let sql4 = connection.query(latestMsg, [chat_room.id, 1], (error_latestMsg, _latest_msg_result) => {
			    		if( error_latestMsg ) throw error_participant;
						let latest_message = _latest_msg_result[0];

						if( latest_message != undefined ) {
							is_message = true;
							// console.log("is_message" , is_message);

							// returnObject[i]['message'].push(latest_message);
							returnObject.push(latest_message);

							// console.log("Message :: ",returnObject);
							// return false;

							// if( (i+1) == chat_rooms.length){
							// 	flag = true;
							// 	// console.log("ff",i, chat_rooms.length, flag,returnObject);
							// }
							
							// return false;
						}
					});

					// if(is_creator == true){ 
					// 	returnObject[i]['creator'].push(user); 
					// 	console.log("user ::", user);
					// }
					// if(is_particapate == true){
					//  	returnObject[i]['participant'].push(participant); 
					// 	console.log("participant ::", participant);
					// }
					// if(is_message == true){ 
					//  	returnObject[i]['message'].push(latest_message); 
					// 	console.log("latest_message ::", latest_message);
					// }

			    }	

				console.log("Final :: ",returnObject);
				return false;

				console.log("Message Object ::"+JSON.stringify(returnObject));
				io.in(request.room_id).emit('message', returnObject);
			});

		});
	}
	else{
		console.log("Precondition Failed !!!");
		return false; 
	}
});
*/