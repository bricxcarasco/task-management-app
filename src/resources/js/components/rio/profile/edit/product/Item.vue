<template>
  <div class="card mt-3">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between">
        <h5 class="card-title text-center card-title--center">
          {{ `${$t('labels.product')}${item_number + 1}` }}
        </h5>
        <div class="button-container">
          <!-- Edit button -->
          <button
            class="btn btn--hover-black p-1 mb-1"
            type="button"
            @click="onEdit"
          >
            <i class="ai-edit"></i>
          </button>
          <!-- Delete button -->
          <button
            class="btn-close"
            type="button"
            aria-label="Close"
            @click="onDelete(product.id)"
          ></button>
        </div>
      </div>
      <div class="mt-2">
        <div class="mt-1">
          <div class="card-text fs-sm my-3">
            <h6 class="m-0">{{ $t('labels.product_name') }}</h6>
            <p class="m-0">{{ product.content }}</p>
          </div>
          <div class="card-text fs-sm my-3">
            <h6 class="m-0">{{ $t('labels.product_description') }}</h6>
            <p class="m-0">{{ product.additional }}</p>
          </div>
          <div
            class="card-text fs-sm my-3"
            v-if="product.reference_url.length != 0"
          >
            <h6 class="m-0">{{ $t('labels.product_reference_url') }}</h6>
            <a
              href=""
              @click.prevent="
                $emit('open-reference-url', product.reference_url)
              "
            >
              {{ product.reference_url }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import Common from '../../../../../common';

export default {
  name: 'ProductItem',
  props: {
    product: {
      type: Object,
      required: true,
    },
    item_number: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    /* eslint no-undef: 0 */
    const deleteModalNode = document.getElementById('delete-product-modal');
    const formModalNode = document.getElementById('product-form-modal');
    const deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
    const formModal = computed(() => new bootstrap.Modal(formModalNode));

    /**
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const onDelete = (id) => {
      // Inject record id to modal
      const field = deleteModalNode.querySelector('input[name=id]');
      field.value = id;
      deleteModal.value.show();
    };

    /**
     * Inject record and open modal
     *
     * @param {int} id
     * @returns {void}
     */
    const onEdit = () => {
      // Inject record id to modal
      const form = formModalNode.querySelector('form');
      Common.fillForm(form, props.product);
      formModal.value.show();
    };

    return {
      onDelete,
      onEdit,
    };
  },
};
</script>
