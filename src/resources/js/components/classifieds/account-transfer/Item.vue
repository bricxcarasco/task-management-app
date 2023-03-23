<template>
  <div class="add-account">
    <div class="mb-3 position-relative" id="div-1">
      <label for="text-input" class="form-label">{{
        `${$t('labels.account')}${item_count + 1}`
      }}</label>
      <div class="input-group">
        <input
          class="form-control"
          type="text"
          id="text-input"
          :value="`${settings.bank} ${settings.branch} (${
            parseInt(settings.account_type) === AccountTypesConstant.SAVINGS
              ? $t('labels.savings')
              : $t('labels.current')
          }) ${settings.account_number} ${settings.account_name}`"
          readonly
        />
        <button
          class="btn btn-link border btn-icon"
          type="button"
          @click="handleOnEdit(settings, account_index)"
        >
          <i class="ai-edit"></i>
        </button>
        <button
          class="btn btn-link border btn-icon"
          type="button"
          @click="handleOnDelete(settings, account_index)"
        >
          <i class="ai-x"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import Common from '../../../common';
import AccountTypesConstant from '../../../enums/AccountTypesConstant';
import AccountTransferOperation from '../../../enums/AccountTransferOperation';

export default {
  name: 'BankItem',
  props: {
    settings: {
      type: Object,
      required: true,
    },
    item_count: {
      type: Number,
      required: true,
    },
    account_index: {
      type: Number,
      required: true,
    },
  },
  setup() {
    let formModalNode = ref(null);
    let formModal = ref(null);
    let deleteModalNode = ref(null);
    let deleteModal = ref(null);
    let operationNode = ref(null);

    /**
     * On mounted properties
     */
    onMounted(() => {
      /* eslint no-undef: 0 */
      operationNode = document.querySelector(
        "form input#operation[type='hidden']"
      );
      formModalNode = document.getElementById('account-form-modal');
      deleteModalNode = document.getElementById('delete-setting-modal');
      deleteModal = computed(() => new bootstrap.Modal(deleteModalNode));
      formModal = computed(() => new bootstrap.Modal(formModalNode));
    });

    /**
     * Construct form data
     *
     * @param {Object} data
     * @returns {Object}
     */
    const constructFormData = (data) => ({
      bank: data.bank,
      branch: data.branch,
      account_type: parseInt(data.account_type, 10),
      account_number: data.account_number,
      account_name: data.account_name,
    });

    /**
     * Delete modal
     *
     * @param {int} id
     * @returns {void}
     */
    const handleOnDelete = (setting, index) => {
      const form = deleteModalNode.querySelector('form');
      const data = constructFormData(setting);
      data.account_index = parseInt(index, 10);
      Common.fillForm(form, data);
      deleteModal.value.show();
    };

    /**
     * Edit modal
     *
     * @param {int} id
     * @returns {void}
     */
    const handleOnEdit = (setting, index) => {
      const form = formModalNode.querySelector('form');
      const data = constructFormData(setting);
      operationNode.value = AccountTransferOperation.EDIT;
      data.account_index = parseInt(index, 10);
      Common.fillForm(form, data);
      formModal.value.show();
    };

    return {
      handleOnEdit,
      handleOnDelete,
      operationNode,
      AccountTypesConstant,
    };
  },
};
</script>
