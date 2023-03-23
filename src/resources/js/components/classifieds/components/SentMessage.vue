<template>
  <li class="chat__wrapper you d-flex flex-column align-items-end">
    <span v-if="!isEmptyMessage" class="chat__name mb-2">
      {{ name }}
    </span>
    <span
      v-if="isEmptyMessage && message.attaches !== null"
      class="chat__name mb-0"
    >
      {{ name }}
    </span>

    <!-- Message -->
    <div v-if="!isEmptyMessage" class="chat__box chat__box--inquiry p-3">
      <p
        v-if="isPaymentUrl(message.message)"
        class="chat__content fs-md mt-0 m-0 c-white"
      >
        <a :href="message.message" class="c-white">{{ message.message }}</a>
      </p>
      <p v-else class="chat__content fs-md mt-0 m-0 c-white">
        {{ message.message }}
      </p>
    </div>

    <!-- Images -->
    <ul v-if="message.image_urls.length > 0" class="chat__item--inquiry">
      <li
        class="chat__box chat__box--inquiry p-3 mt-2 hoverable text-center"
        v-for="(image, index) in message.image_urls"
        :key="index"
        @click="viewImage(image)"
      >
        <div v-if="image === null">
          <span class="spinner-border spinner-border--sm text-primary"></span>
        </div>
        <div v-else>
          <img
            class="chat__image--inquiry"
            :src="image"
            @load="handleImageLoadSuccess"
            @error="handleImageLoadError"
            alt="Image"
            v-if="isImageLoaded"
          />
        </div>
      </li>
    </ul>

    <!-- Date -->
    <p class="chat__time mt-2 mb-0 fs-xs text-end">
      <span
        v-if="message.date == null"
        class="spinner-border spinner-border--sm text-primary"
      >
      </span>
      <span v-else>{{ message.date }}</span>
    </p>
  </li>
</template>

<script>
import { ref, computed } from 'vue';

export default {
  name: 'InquiryMessageSent',
  props: {
    message: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const message = ref(props.message);
    const attachmentLoading = ref(true);
    const isImageLoaded = ref(true);
    const name = computed(() => `${message.value.name}`);
    const isEmptyMessage =
      message.value.message === null || message.value.message === '';

    /**
     * Attach event listener for image load error
     */
    const handleImageLoadSuccess = () => {
      isImageLoaded.value = true;
      attachmentLoading.value = false;
    };

    /**
     * Attach event listener for image load error
     */
    const handleImageLoadError = () => {
      isImageLoaded.value = false;
      attachmentLoading.value = false;
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

    /**
     * Image preview
     */
    const viewImage = (image) => {
      emit('preview-image', image);
    };

    return {
      name,
      isEmptyMessage,
      viewImage,
      handleImageLoadSuccess,
      handleImageLoadError,
      isPaymentUrl,
      isImageLoaded,
      attachmentLoading,
    };
  },
};
</script>
