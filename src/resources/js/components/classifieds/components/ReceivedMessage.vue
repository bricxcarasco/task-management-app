<template>
  <li class="chat__wrapper sender d-flex align-items-start">
    <!-- Profile Image -->
    <img
      class="rounded-circle height-50 me-3"
      :src="message.image"
      alt="User"
      width="50"
      height="50"
      @error="
        Common.handleNotFoundImageException(
          $event,
          DefaultImageCategory.RIO_NEO
        )
      "
    />

    <div class="d-flex flex-column align-items-start">
      <span class="chat__name mb-2" v-if="!isEmptyMessage">{{ name }}</span>
      <p class="chat__name mb-0" v-if="isEmptyMessage && message.attaches">
        {{ name }}
      </p>

      <!-- Message -->
      <div class="chat__box p-3" v-if="!isEmptyMessage">
        <p
          v-if="isPaymentUrl(message.message)"
          class="chat__content fs-md mt-0 m-0"
        >
          <a :href="message.message">{{ message.message }}</a>
        </p>
        <p v-else class="chat__content fs-md mt-0 m-0">
          {{ message.message }}
        </p>
      </div>

      <!-- Images -->
      <ul v-if="message.attaches !== null" class="chat__item--inquiry">
        <li
          class="chat__box chat__box--inquiry p-3 mt-2 hoverable text-center"
          v-for="(image, index) in message.image_urls"
          :key="index"
          @click="viewImage(image)"
        >
          <img
            class="chat__image--inquiry"
            :src="image"
            alt="Image"
            @error="
              Common.handleNotFoundImageException(
                $event,
                DefaultImageCategory.DOCUMENT_MANAGEMENT
              )
            "
          />
        </li>
      </ul>

      <!-- Date -->
      <p class="chat__time mt-2 mb-0 fs-xs">{{ message.date }}</p>
    </div>
  </li>
</template>

<script>
import { ref, computed } from 'vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';

export default {
  name: 'ChatMessageReceived',
  props: {
    message: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const message = ref(props.message);
    const name = computed(() => `${message.value.name}`);
    const isEmptyMessage = message.value.message === null;

    /**
     * Image preview
     */
    const viewImage = (image) => {
      emit('preview-image', image);
    };

    /**
     * Check if message is a payment URL
     *
     * @param {string} msg
     * @return {bool}
     */
    const isPaymentUrl = (msg) => {
      if (msg.startsWith('https://') || msg.startsWith('http://')) {
        if (msg.includes('/classifieds/payments/')) {
          return true;
        }
      }

      return false;
    };

    return {
      name,
      isEmptyMessage,
      viewImage,
      isPaymentUrl,
      Common,
      DefaultImageCategory,
    };
  },
};
</script>
