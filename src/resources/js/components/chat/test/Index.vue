<template>
  <div>
    <div class="container position-relative zindex-5 pt-6 py-md-6 mb-md-3">
      <p>Test Room</p>

      <ul>
        <li v-for="(item, index) in messages" :key="index">
          [{{ item.datetime }}] : {{ item.message }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'TestRoomIndex',
  setup() {
    const messages = ref([]);

    window.Echo.private('test.chat.room.1').listen('.message.sent', (data) => {
      /* eslint-disable no-console */
      console.log(data);
      messages.value.push(data);
    });

    return {
      messages,
    };
  },
};
</script>
