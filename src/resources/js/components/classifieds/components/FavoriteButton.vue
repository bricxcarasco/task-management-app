<template>
  <div>
    <button
      v-if="
        service.type === ServiceSelectionTypes.RIO ||
        (service.type === ServiceSelectionTypes.NEO && service.data.is_owner)
      "
      type="button"
      class="btn btn-outline-secondary fs-xs p-1 button__favorite"
      :class="product.is_favorite ? 'favorite' : ''"
      @click="toggleFavorite($event, product)"
    >
      <i
        class="me-1 avoid-events"
        :class="product.is_favorite ? 'ai-star-filled' : 'ai-star'"
      ></i>
      {{ $t('buttons.favorite') }}
    </button>
    <div class="view-product-btn-favorite-hidden" v-else></div>
  </div>
</template>
<style>
.avoid-events {
  pointer-events: none;
}
</style>
<script>
import { defineComponent, ref } from 'vue';
import ClassifiedFavoritesApiService from '../../../api/classifieds/favorites';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'FavoriteButtonComponent',
  props: {
    service: {
      type: [Array, Object],
      required: true,
    },
    product: {
      type: [Array, Object],
      required: true,
    },
    isFavorite: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['set-alert', 'reset-alert', 'page-loading', 'get-products'],
  setup(props, { emit }) {
    /**
     * Data properties
     */
    const classifiedFavoritesApiService = new ClassifiedFavoritesApiService();
    const errors = ref(null);
    const disableBtn = ref(false);
    const isFavorite = ref(props.isFavorite);

    /**
     * Add new favorite
     *
     * @returns {void}
     */
    const addFavorite = async (product) => {
      // Reinitialize state
      errors.value = null;
      emit('reset-alert');
      emit('page-loading', true);

      await classifiedFavoritesApiService
        .favoriteProduct(product.id, product)
        .then(() => {
          emit('page-loading', false);
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Render validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          emit('set-alert', 'failed');
          emit('page-loading', false);
        });
    };

    /**
     * Remove existing favorite
     *
     * @returns {void}
     */
    const removeFavorite = async (product) => {
      // Reinitialize state
      errors.value = null;
      emit('reset-alert');
      emit('page-loading', true);

      await classifiedFavoritesApiService
        .unfavoriteProduct(product.id, product)
        .then(() => {
          if (isFavorite.value) {
            emit('get-products');
          } else {
            emit('page-loading', false);
          }
        })
        .catch((error) => {
          const responseData = error.response.data;

          // Render validation errors
          if (responseData.status_code === 422) {
            errors.value = responseData.data;
            return;
          }

          emit('set-alert', 'failed');
          emit('page-loading', false);
        });
    };

    /**
     * Handle toggling favorite button
     *
     * @param {object} event
     * @returns {void}
     */
    const toggleFavorite = (event, product) => {
      event.preventDefault();
      event.stopPropagation();

      const favoriteBtn = event.target;
      const favoriteIcon = favoriteBtn.children[0];

      if (favoriteBtn !== undefined) {
        if (favoriteBtn.classList.contains('favorite')) {
          // Set button UI as removed
          if (!isFavorite.value) {
            favoriteBtn.classList.remove('favorite');

            if (favoriteIcon !== undefined) {
              favoriteIcon.classList.add('ai-star');
              favoriteIcon.classList.remove('ai-star-filled');
            }
          }

          removeFavorite(product);
        } else {
          // Set button UI as added
          favoriteBtn.classList.add('favorite');

          if (favoriteIcon !== undefined) {
            favoriteIcon.classList.remove('ai-star');
            favoriteIcon.classList.add('ai-star-filled');
          }

          addFavorite(product);
        }
      }
    };

    return {
      toggleFavorite,
      disableBtn,
      ServiceSelectionTypes,
    };
  },
});
</script>
