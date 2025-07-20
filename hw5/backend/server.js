var server = require("http").Server(app);
var express = require('express');
var cors = require('cors');
var app = express();

app.use(cors());

console.log(process.env.NODE_ENV);

var server = require("http").Server(app);
const io = require("socket.io")(server, {
    cors: {
        origin: ["http://localhost:5500", "http://wwweb2024.csie.io:50094", "http://wwweb2024.csie.io:50095", "http://localhost:50094", "http://localhost:50095"],
        methods: ["GET", "POST"]
    }
});

let users = [];
let messages = [];
let bingo_rooms = []; // { roomID: ""}

io.on('connection', function(socket) {
    console.log('a user connected');

    let newUser = true;
    let username = null;

    socket.on('group_message', function(data) {
        messages.push(data);
        io.sockets.emit('group message', data);
    });

    socket.on('join bingo', function(data) {
        let roomExists = false;
        for (let room of bingo_rooms) {
            if (room.roomID === data.roomID) {
                roomExists = true;
                break;
            }
        }
        if (!roomExists) {
            bingo_rooms.push({ roomID: data.roomID });
            bingo_rooms.push({ roomID: data.receiverID });
        }
        let whos_turn = Math.floor(Math.random() + 0.5) === 0 ? data.roomID : data.receiverID;
        io.to(data.roomID).emit('join_bingo', { canStart: roomExists, whos_turn: whos_turn });
        io.to(data.receiverID).emit('join_bingo', { canStart: roomExists, whos_turn: whos_turn });
        if (!roomExists) {
            io.sockets.emit('start_bingo', { roomID: data.roomID, whos_turn: whos_turn });
            io.sockets.emit('start_bingo', { roomID: data.receiverID, whos_turn: whos_turn });
        }
    });

    socket.on('bingo init', function(data) {
        let whos_turn = Math.floor(Math.random() + 0.5) === 0 ? data.roomID : data.receiverID;
        io.to(data.roomID).emit('join_bingo', { canStart: true, whos_turn: whos_turn });
        io.to(data.receiverID).emit('join_bingo', { canStart: true, whos_turn: whos_turn });
        io.sockets.emit('start_bingo', { roomID: data.roomID, whos_turn: whos_turn });
        io.sockets.emit('start_bingo', { roomID: data.receiverID, whos_turn: whos_turn });
    });

    socket.on('bingo over' , function(data) {
        let roomID = data.roomID;
        let receiverID = data.receiverID;
        let roomIndex = bingo_rooms.findIndex(room => room.roomID === roomID);
        bingo_rooms.splice(roomIndex, 1);
        let receiverIndex = bingo_rooms.findIndex(room => room.roomID === receiverID);
        bingo_rooms.splice(receiverIndex, 1);
        io.sockets.emit('bingo_over', { roomID: roomID });
        io.sockets.emit('bingo_over', { roomID: receiverID });
        io.to(roomID).emit('close_bingo');
        io.to(receiverID).emit('close_bingo');
    });

    socket.on('bingo turn', function(data) {
        io.sockets.emit('bingo_turn', data);
        let next_turn = data.whos_turn === data.roomID ? data.receiverID : data.roomID;
        io.to(data.roomID).emit('next_turn', { whos_turn: next_turn });
        io.to(data.receiverID).emit('next_turn', { whos_turn: next_turn });
    });

    socket.on('bingo select', function(data) {
        io.sockets.emit('bingo_select', data);
        io.to(data.roomID).emit('select', { index: data.index });
        io.to(data.receiverID).emit('select', { index: data.index });
    });

    socket.on('join_private', function(data) {
        // if socket has already joined a room, leave it
        for (let room of socket.rooms) {
            if (room !== socket.id) {
                socket.leave(room);
            }
        }
        socket.join(data.roomID);
    });

    socket.on('private_message', function(data) {
        io.to(data.receiverID).emit('private message', data);
    });

    socket.on('join', function(data) {
        for (let user of users) {
            if (user.username === data.username) {
                newUser = false;
            }
        }
        if (newUser) {
            username = data.username;
            users.push({ username: username, uid: socket.id });
            data.userCount = users.length;
            io.emit('join_success', data);
            socket.emit('user_join', { username: username });
            io.sockets.emit('user_list', { users: users });
            console.log(users);
        }
        else {
            newUser = true;
            io.to(socket.id).emit('join_fail', { message: 'Username already exists' });
        }
    });
    socket.on('disconnect', function() {
        console.log('user disconnected');
        for (let i = 0; i < users.length; i++) {
            if (users[i].username === username) {
                users.splice(i, 1);
            }
        }
        io.emit('user_leave', { username: username });
        io.sockets.emit('user_list', { users: users });
    });
});

server.listen(process.env.PORT, '127.0.0.1', function() {
    console.log('HTTP Server: http://127.0.0.1:' + process.env.PORT);
});