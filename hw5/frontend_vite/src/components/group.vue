<template>
    <div class="flex-grow bg-white shadow-lg rounded-lg m-4">
            <div class="flex flex-col h-full">
                <div class="p-4 border-b">
                    {{ username }} | Kira 平台聊天室
                </div>
                <div class="overflow-y-auto p-6 h-[80vh]">
                    <div class="text-sm text-gray-700 flex flex-col" id = "chatroom">
                        <div v-for="message in messages" class="message flex flex-col" :class="{'own-message': message.sender === username, 'system-message': message.type === 'system', 'normal-message': message.type === 'normal'} ">
                            <span v-if="message.type === 'normal'" class="sender-name" :class="{'own': message.sender === username, 'other': message.sender !== username}">
                                {{ message.sender }}
                            </span>
                            <div class="message-content p-2 rounded-lg" :class="{'bg-green-300': message.sender === username, 'bg-blue-300': message.sender !== username && message.type != 'system'}">
                                {{ message.message }}
                                <img v-if="message.has_image" :src="message.img_url" class="rounded-lg" style="max-width: 100%; max-height: 200px;">
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
                        <div v-if="isEmojiPickerVisible" class="absolute z-10" style="bottom: 100%; left: 0;">
                            <EmojiPicker :native="false" @select="insertEmoji"/>
                        </div>
                    </div>
                    <div class="flex">
                        <input type="text" v-model="message_input" class="border flex-grow p-2 mr-4 rounded focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="輸入訊息...">
                        <button class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 focus:outline-none" @click="sendMes">發送</button>
                    </div>
                    
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { socket, state } from '@/socket';
import EmojiPicker from 'vue3-emoji-picker';
import 'vue3-emoji-picker/css'

const userList = computed(() => state.users);
const messages = computed(() => state.messages);
const userid = socket.id;

const message_input = ref('');

const props = defineProps({
    username: {
        type: String,
        required: true
    },
});

const sendMes = () => {
    if (message_input.value.trim() === '') {
        return;
    }
    socket.emit('group_message', {
        message: message_input.value.trim(),
        sender: props.username,
        senderID: userid,
        type: 'normal',
        has_image: false,
        img_url: ""
    });
    message_input.value = '';
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
            socket.emit('group_message', {
                message: '',
                sender: props.username,
                senderID: userid,
                type: 'normal',
                has_image: true,
                img_url: e.target.result
            });
        };
        reader.readAsDataURL(file);
    }
}
</script>