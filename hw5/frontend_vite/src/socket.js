import { reactive } from "vue";
import { io } from "socket.io-client";

export const state = reactive({
    users: [],
    messages: [],
    bingo_status: []  // { roomID, whos_turn, selected: [] }
});

const URL = "https://backend-hidden-frog-2919.fly.dev/";

export const socket = io(URL);

socket.on("user_list", (data) => {
    state.users = data.users;
});

socket.on("start_bingo", (data) => {
    let tmp = [];
    for(let i = 0; i < 25; i++) {
        tmp.push(false);
    }
    // if the room is already in the list, remove it
    state.bingo_status = state.bingo_status.filter(room => room.roomID !== data.roomID);
    state.bingo_status.push({ roomID: data.roomID, whos_turn: data.whos_turn, selected: tmp });
});

socket.on("bingo_over", (data) => {
    state.bingo_status = state.bingo_status.filter(room => room.roomID !== data.roomID);
});

socket.on("bingo_turn", (data) => {
    let next_turn = data.whos_turn === data.roomID ? data.receiverID : data.roomID;
    let roomIndex = state.bingo_status.findIndex(room => room.roomID === data.roomID);
    state.bingo_status[roomIndex].whos_turn = next_turn;
    let receiverIndex = state.bingo_status.findIndex(room => room.roomID === data.receiverID);
    state.bingo_status[receiverIndex].whos_turn = next_turn;
})

socket.on("bingo_select", (data) => {
    let roomIndex = state.bingo_status.findIndex(room => room.roomID === data.roomID);
    state.bingo_status[roomIndex].selected[data.index] = true;
    let receiverIndex = state.bingo_status.findIndex(room => room.roomID === data.receiverID);
    state.bingo_status[receiverIndex].selected[data.index] = true;
})

socket.on("user_join", (data) => {
    if(data.username === null)
        return;
    socket.emit("group_message", { message: `${data.username} joined`, senderID: "", sender: "sys", type: "system", hasImage: false, img_url: "" });
})

socket.on("user_leave", (data) => {
    if(data.username === null)
        return;
    socket.emit("group_message", { message: `${data.username} left`, senderID: "", sender: "sys", type: "system", hasImage: false, img_url: "" });
})  

socket.on("group message", (data) => {
    state.messages.push(data);
})