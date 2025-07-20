<template>
    <div class="flex h-screen bg-gray-100">
        <div class="w-1/4 bg-white overflow-auto flex flex-col justify-between">
            <div>
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">狀態列表</h2>
                </div>
                <div class="p-4">
                    <h3 class="text-md font-semibold flex-grow pb-2">Mode</h3>
                    <button disabled :class="{'w-full p-4': true, 'active': mode === 'personal'}">個人聊天</button>
                    <button :class="{'w-full p-4 hover:bg-purple-300 hover:text-black relative': true, 'active': mode === 'group'}" @click="changeMode">
                        團體聊天
                        <span v-if="group_has_new_message" class="absolute top-0 right-0 mt-1 mr-2 h-2 w-2 bg-red-600 rounded-full"></span>
                    </button>   
                </div>
                <div class = "p-4">
                    <p class="text-md font-semibold cursor-pointer pb-2" id = "userCount"> 線上人數: {{ userCount }}</p>
                    <ul class="overflow-y-auto" id = "userlist">
                        <li v-for="user in userList" :key = "user.uid" class="relative p-4 cursor-pointer" :class="{'hover:bg-gray-50': user.username != username, 'hidden': user.username === username }" @click="chat_to(user.uid, userList.indexOf(user))">
                            {{ user.username }}
                            <span v-if="user_has_new_message[userList.indexOf(user)]" class="absolute top-0 right-0 mt-1 mr-2 h-2 w-2 bg-red-600 rounded-full"></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class = "p-4 text-center">
                <button class="w-[10rem] p-4 bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-red-700" @click="leaveChatroom">離開聊天室</button>

            </div>
        </div>
        <group v-if="mode === 'group'" :username="username"/>
        <one_to_one v-if="mode !== 'group'" :key="key" :username="username" :receiverid="receiver" :messages="one_to_one_messages" @append_message="append_message" @add_notify="add_notify"/>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { socket, state } from '@/socket';
import group from './group.vue';
import one_to_one from './one_to_one.vue';
import 'vue3-emoji-picker/css'

const mode = ref('group');

const userCount = computed(() => state.users.length);
const userList = computed(() => state.users);
const userid = socket.id;
const username = ref('');
const key = ref(0);
const group_has_new_message = ref(false);
const user_has_new_message = ref([]);
const receiver = ref(null);
const one_to_one_messages = ref([]);

onMounted(() => {
    setTimeout(() => {
        username.value = userList.value.find(user => user.uid === userid).username;
        for (let i = 0; i < userList.value.length; i++) {
            user_has_new_message.value.push(false);
        }
    }, 10);
    
});

socket.on('group message', () => {
    if (mode.value != 'group') {
        group_has_new_message.value = true;
    }
});

socket.on('leave', (data) => {
    if (data.uid === receiver.value) {
        receiver.value = null;
        mode.value = 'group';
    }
});

const append_message = (data) => {
    one_to_one_messages.value.push(data);
};

const add_notify = (data) => {
    for (let i = 0; i < userList.value.length; i++) {
        if (userList.value[i].uid === data.senderID && receiver.value !== data.senderID) {
            console.log('add notify');
            user_has_new_message.value[i] = true;
            break;
        }
    }
};

socket.on('private message', (data) => {
    add_notify(data);
    append_message(data);
});

const chat_to = (uid, idx) => {
    if (uid === socket.id) {
        return;
    }
    mode.value = 'personal';
    receiver.value = uid;
    user_has_new_message.value[idx] = false;
    key.value += 1;
};

const changeMode = () => {
    mode.value = 'group';
    group_has_new_message.value = false;
    receiver.value = null;
};

const leaveChatroom = () => {
    window.location.href = '/';
};

</script>