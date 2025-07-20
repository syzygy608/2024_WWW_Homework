<template>

    <!-- 聊天室 -->
    <div class="flex-grow bg-white shadow-lg rounded-lg m-4">
        <div class="flex flex-col h-full">
            <div class="p-4 border-b">
                {{ username }} and {{ receivername }} 's chatroom
            </div>
            <div class="overflow-y-auto p-6 h-[80vh]">
                <div class="text-sm text-gray-700 flex flex-col" id = "chatroom">
                    <div v-for="message in messages" class="message flex flex-col" :class="{'own-message': message.sender === username, 'normal-message': message.type === 'normal'} ">
                        <span class="sender-name" :class="{'own': message.sender === username, 'other': message.sender !== username}">
                            {{ message.sender }}
                        </span>
                        <div class="message-content p-2 rounded-lg" :class="{'bg-green-300': message.sender === username, 'bg-blue-300': message.sender !== username && message.type != 'system'}">
                            <div v-if="message.has_image">
                                <img :src="message.img_url" class="rounded-lg" style="max-width: 100%; max-height: 200px;">
                            </div>
                            <div v-else v-html="message.message" class="rounded-lg"></div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="p-4 border-t">
                <div class = "flex p-2 relative">
                    <div @click="toggleEmojiPicker">
                        <i class="material-icons">add_reaction</i>
                    </div>
                    <div>
                        <i class="material-icons" @click="triggerFileInput">image</i>
                        <input type="file" ref="fileInput" @change="handleFileChange" class = "hidden" accept="image/*" multiple="false"> 
                    </div>
                    <i class="material-icons" @click="sendGameInviteMes">sports_esports</i>
                    <div v-if="isEmojiPickerVisible" class="absolute z-10" style="bottom: 100%; left: 0;">
                        <EmojiPicker :native="false" @select="insertEmoji"/>
                    </div>
                </div>
                <div class="flex">
                    <input type="text" v-model="message_input" class="border flex-grow p-2 mr-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="輸入訊息...">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none" @click="sendMes">發送</button>
                </div>
                <div v-if="popup" class="w-[55rem] h-[40rem] flex flex-row fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
                    <div class="grid grid-cols-5 grid-rows-5 w-[40rem] h-[40rem] bg-purple-300 rounded-lg">
                        <div v-for="number in bingo_table" @click="selectBingo(number)" :key="number" :id="`number-${number}`" class="w-[7rem] h-[7rem] bg-purple-200 rounded-lg m-2 border-2 text-2xl border-gray-200 flex justify-center items-center">
                            {{ number }}
                        </div>
                    </div>
                    <div class = "w-[15rem] h-[20rem] ml-2 p-7 bg-purple-300 rounded-lg text-center">
                        <p v-if="!bingo_open" class = "text-xl">等待另外一個玩家...</p>
                        <p v-else class = "text-xl">輪到 {{ bigno_turn }} 的回合</p>
                        <button @click="togglePopup">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="popup" class="fixed top-0 left-0 w-full h-full bg-black opacity-50 z-10"></div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { socket, state } from '@/socket';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css'

const props = defineProps({
    username: {
        type: String,
        required: true
    },
    receiverid: {
        type: String,
        required: false
    },
    messages: {
        type: Array,
        required: false
    }
});

const messages = computed({
    get() {
        return props.messages.filter(message => (message.senderID === props.receiverid || 
            message.receiverID === props.receiverid) && 
            (message.senderID === socket.id || message.receiverID === socket.id));
    }
});

const userList = computed(() => state.users);

let receivername = null;

for (let i = 0; i < userList.value.length; i++) {
    if (userList.value[i].uid === props.receiverid) {
        receivername = userList.value[i].username;
        break;
    }
}

const emit = defineEmits(['append_message']);

const message_input = ref('');

const sendMes = () => {
    if (message_input.value.trim() === '') {
        return;
    }
    let data = {
        message: message_input.value.trim(),
        sender: props.username,
        senderID: socket.id,
        receiverID: props.receiverid,
        type: 'normal',
        has_image: false,
        img_url: ""
    };
    socket.emit('private_message', data);
    message_input.value = '';
    emit('append_message', data);
};

const isEmojiPickerVisible = ref(false);

const toggleEmojiPicker = () => {
    isEmojiPickerVisible.value = !isEmojiPickerVisible.value;
};

const insertEmoji = (emoji) => {
    message_input.value += emoji.i;
};

const fileInput = ref(null);

function triggerFileInput() {
    fileInput.value.click();
}

function handleFileChange(event) {
    const files = event.target.files;
    if(files.length > 0) {
        const file = files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            let data = {
                message: '',
                sender: props.username,
                senderID: socket.id,
                receiverID: props.receiverid,
                type: 'normal',
                has_image: true,
                img_url: e.target.result
            };
            socket.emit('private_message', data);
            emit('append_message', data);
        };
        reader.readAsDataURL(file);
    }
}

const sendGameInviteMes = () => {
    let data = {
        message: `我們一起來玩遊戲吧！<br><a id = "open" href = "#" class = "text-purple-500"> >>>>>>>>> Play Game <<<<<<<<< </a>`,
        sender: props.username,
        senderID: socket.id,
        receiverID: props.receiverid,
        type: 'normal',
        has_image: false,
        img_url: ""
    };
    socket.emit('private_message', data);
    emit('append_message', data);
};

const popup = ref(false);
const bingo_status = computed(() => state.bingo_status);
const selected = computed(() => bingo_status.value.find(bingo => bingo.roomID === socket.id) ? bingo_status.value.find(bingo => bingo.roomID === socket.id).selected : new Array(25).fill(false));
const bingo_table = ref([]);
const bingo_open = ref(false);
const bigno_turn = ref('');
const whos_turn = ref('');

const togglePopup = () => {
    popup.value = !popup.value;
    bingo_table.value = [];
    bingo_open.value = false;
    bigno_turn.value = '';
    socket.emit('bingo over', { roomID: socket.id, receiverID: props.receiverid });
};

// if open is clicked, emit a event to open the game
document.addEventListener('click', function(e) {
    if (e.target && e.target.id == 'open') {
        e.preventDefault();
        popup.value = true;
        while (bingo_table.value.length < 25) {
            let num = Math.floor(Math.random() * 25) + 1;
            if (bingo_table.value.indexOf(num) === -1) {
                bingo_table.value.push(num);
            }
        }
        socket.emit('join bingo', { roomID: socket.id, receiverID: props.receiverid });

    }
});

socket.on('join_bingo', (data) => {
    if (data.canStart) {
        for (let i = 0; i < 25; i++) {
            document.getElementById(`number-${i + 1}`).classList.remove('bg-blue-300');
        } 

        bingo_open.value = true;
        for (let i = 0; i < userList.value.length; i++) {
            if (userList.value[i].uid === data.whos_turn) {
                bigno_turn.value = userList.value[i].username;
                break;
            }
        }
        if (socket.id === data.whos_turn) {
            bigno_turn.value = '你';
        }
        whos_turn.value = data.whos_turn;
        alert('遊戲開始，現在是' + bigno_turn.value + '的回合！');
        
    }
});

socket.on('close_bingo', (data) => {
    popup.value = false;
    bingo_table.value = [];
    bingo_open.value = false;
    bigno_turn.value = '';
    alert('bingo 遊戲結束。');
});

socket.on('next_turn' , (data) => {
    whos_turn.value = data.whos_turn;
    for (let i = 0; i < userList.value.length; i++) {
        if (userList.value[i].uid === data.whos_turn) {
            bigno_turn.value = userList.value[i].username;
            break;
        }
    }
    if (socket.id === data.whos_turn) {
        bigno_turn.value = '你';
    }
});

socket.on('select', (data) => {
    document.getElementById(`number-${data.index + 1}`).classList.add('bg-blue-300');
});

function checkBingo() {
    let d_bingo = [];
    for (let i = 0; i < 5; i++) {
        let row = [];
        for (let j = 0; j < 5; j++) {
            let is_selected = selected.value[i * 5 + j];
            row.push({ value: bingo_table.value[i * 5 + j], selected: is_selected });
        }
        d_bingo.push(row);
    }
    let bingo = 0, slash1 = 0, slash2 = 0;
    for (let i = 0; i < 5; i++) {
        let row = 0;
        let col = 0;
        for (let j = 0; j < 5; j++) {
            if (d_bingo[i][j].selected) {
                row++;
            }
            if (d_bingo[j][i].selected) {
                col++;
            }
            if (i === j && d_bingo[i][j].selected) {
                slash1++;
            }
            if (i + j === 4 && d_bingo[i][j].selected) {
                slash2++;
            }
        }
        if (row === 5) {
            bingo++;
        }
        if (col === 5) {
            bingo++;
        }
    }
    if (slash1 === 5) {
        bingo++;
    }
    if (slash2 === 5) {
        bingo++;
    }
    // 特殊分數，如果四個角都被選中，則加 10 分
    if (d_bingo[0][0].selected && d_bingo[0][4].selected && d_bingo[4][0].selected && d_bingo[4][4].selected) {
        bingo += 10;
    }
    return bingo;
}

const selectBingo = (number) => {
    if (whos_turn.value !== socket.id) {
        alert('還沒輪到你！');
        return;
    }
    if (!selected.value[number - 1]) {
        selected.value[number - 1] = true;
        bingo_status.value.find(bingo => bingo.roomID === props.receiverid).selected = selected.value;
    } else {
        alert('這格已經被選過了！');
        return;
    }
    socket.emit('bingo select', { roomID: socket.id, receiverID: props.receiverid, index: number - 1 });
    let if_bingo = checkBingo();
    if (if_bingo >= 5) {
        // 贏了，選擇是否再來一局
        let res = confirm('Bingo! 你贏了! 獲得了 ' + if_bingo + ' 分，是否再來一局？');
        if (res) {
            bingo_open.value = false;
            socket.emit('bingo init', { roomID: socket.id, receiverID: props.receiverid });
        }
        else {
            socket.emit('bingo over', { roomID: socket.id, receiverID: props.receiverid });
        }
        return;
    }
    socket.emit('bingo turn', { roomID: socket.id, receiverID: props.receiverid, whos_turn: whos_turn.value });
};

</script>