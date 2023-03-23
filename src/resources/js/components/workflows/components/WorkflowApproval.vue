<template>
  <div>
    <form action="" @submit="handleApproverValidate" ref="formRef" novalidate>
      <p class="text-center mb-0">
        {{ $t('paragraphs.specify_the_approver_for_approval') }}
        <span class="text-danger">*</span>
      </p>
      <ol class="mb-3 mt-5" id="list-approver">
        <li v-for="(input, index) in approversInput" :key="input.sequence">
          <div class="input-group">
            <select
              :id="`approver${input.sequence}`"
              :data-approver-sequence="`approver_sequence${input.sequence}`"
              :data-approver-name="`approver_name${input.sequence}`"
              v-model="input.id"
              class="form-select text-center approver-select-ui"
              :name="`approver${input.sequence}`"
            ></select>
            <input
              type="hidden"
              :class="errors ? validationError(index) : ''"
              :id="`approver_name${input.sequence}`"
              :name="`approver_name${input.sequence}`"
              :value="input.name"
            />
            <input
              type="hidden"
              :class="errors ? validationError(index) : ''"
              :id="`approver_sequence${input.sequence}`"
              :name="`approver_sequence${input.sequence}`"
              :value="input.sequence"
            />
            <button
              v-if="approversInput.length > 1"
              class="btn btn-link border btn-icon"
              type="button"
              @click="removeApprover(input)"
            >
              <i class="ai-x"></i>
            </button>
            <base-validation-error
              v-if="errors"
              :errors="errors[`approvers.${index}.id`]"
            />
          </div>
        </li>
      </ol>
      <div class="text-center">
        <button
          type="button"
          class="btn btn-link"
          id="add-approver"
          @click="addApprover"
        >
          <i class="ai-plus me-2"></i>
          {{ $t('buttons.add_approver') }}
        </button>
      </div>

      <div class="text-center mt-4">
        <button
          type="button"
          class="btn btn-primary me-2"
          @click="$emit('previous-step', CreateWorkflowSteps.FORM)"
        >
          {{ $t('labels.return') }}
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          @click="submitForm($event)"
        >
          {{ $t('buttons.next') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { computed, defineComponent, onMounted, ref, watchEffect } from 'vue';
import i18n from '../../../i18n';
import { objectifyForm } from '../../../utils/objectifyForm';
import CreateWorkflowSteps from '../../../enums/CreateWorkflowSteps';
import WorkflowApiService from '../../../api/workflows/workflow';
import BaseValidationError from '../../base/BaseValidationError.vue';

export default defineComponent({
  name: 'WorkflowApproval',
  components: {
    BaseValidationError,
  },
  props: {
    form_data: {
      type: [Array, Object],
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    is_previous: {
      type: Boolean,
    },
  },
  setup(props, { emit }) {
    const workflowApiService = new WorkflowApiService();
    const formData = ref(props.form_data);
    const formRef = ref({});
    const errors = ref(null);
    const approversInput = ref(props.form_data.approvers);
    const approverSelected = ref({});
    const approverList = ref([]);

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const clearFormData = () => {
      errors.value = null;
      approversInput.value = [
        {
          sequence: Math.floor(Math.random() * 10000),
          name: '',
          id: '',
        },
      ];
      approverSelected.value = {};
      approverList.value = [];
    };

    /**
     * Update vue model with existing form data
     *
     * @returns {void}
     */
    const updateModel = async () => {
      const object = objectifyForm(formRef.value);
      approverSelected.value = object;

      await approversInput.value.forEach((approver) => {
        if (object[`approver${approver.sequence}`]) {
          // eslint-disable-next-line
          approver.id = object[`approver${approver.sequence}`];
          // eslint-disable-next-line
          approver.name = object[`approver_name${approver.sequence}`];
        }
      });
    };

    /**
     * Handle filtered approver list
     *
     * @returns {object}
     */
    const filterApprover = () => {
      const data = approverList.value;
      return data.filter(
        (approver) =>
          !approversInput.value.find(
            // eslint-disable-next-line
            (approverInList) => approverInList.id == approver.id
          )
      );
    };

    /**
     * Computed property for filtered approver list
     *
     * @returns {object}
     */
    const approverFiltered = computed(() => {
      updateModel();
      return filterApprover();
    });

    /**
     * Check validation error in approval text input
     *
     * @returns string
     */
    const validationError = (index) => {
      if (errors) {
        if (errors[`approvers.${index}.id`] !== null) {
          return 'is-invalid';
        }
      }
      return '';
    };

    /**
     * Create select2 approver instance
     *
     * @returns {void}
     */
    const newSelect2Approver = (sequence) => {
      // eslint-disable-next-line
      $(`#approver${sequence}`).select2({
        placeholder: i18n.global.t(
          'paragraphs.specify_the_approver_for_approval'
        ),
        data: props.is_previous ? approverList.value : approverFiltered.value,
        language: {
          noResults: () => i18n.global.t('labels.no_result_found'),
        },
      });
    };

    /**
     * select2 approver event
     *
     * @returns {void}
     */
    const selectApproverEvent = (sequence) => {
      // eslint-disable-next-line
      $(`#approver${sequence}`).on('select2:select', (event) => {
        const {
          params: { data },
        } = event;
        const nameAttr = event.target.getAttribute('data-approver-name');

        const index = approversInput.value.findIndex(
          (object) => object.sequence === sequence
        );

        if (index !== -1) {
          approversInput.value[index].id = data.id;
          approversInput.value[index].name = data.text;
        }

        const indexApproverList = approverList.value.findIndex(
          // eslint-disable-next-line
          (object) => object.id == data.id
        );

        if (indexApproverList !== -1) {
          approverList.value[indexApproverList].selected = true;
        }

        document.getElementById(nameAttr).value = data.text;

        approversInput.value.forEach((approver) => {
          if (
            // eslint-disable-next-line
            approver.sequence !== sequence
          ) {
            let approverListData = [];
            if (
              // eslint-disable-next-line
              approverList.value.length !== approversInput.value.length
            ) {
              approverListData = approverList.value.filter((object) =>
                approversInput.value.find(
                  // eslint-disable-next-line
                  ({ id }) =>
                    // eslint-disable-next-line
                    object.id !== id &&
                    // eslint-disable-next-line
                    object.id !== approversInput.value[index].id
                )
              );
            }

            if (
              // eslint-disable-next-line
              approverList.value.length == approversInput.value.length
            ) {
              approverListData = approverList.value.filter(
                // eslint-disable-next-line
                (object) => object.id == approver.id
              );
            }

            // eslint-disable-next-line
            $(`#approver${approver.sequence}`)
              .empty()
              .select2({
                placeholder: i18n.global.t(
                  'paragraphs.specify_the_approver_for_approval'
                ),
                data: approverListData,
                language: {
                  noResults: () => i18n.global.t('labels.no_result_found'),
                },
              });

            // eslint-disable-next-line
            $(`#approver${approver.sequence}`)
              .val(approver.id)
              .trigger('change');
          }
        });
      });
    };

    /**
     * Add additional approver input
     *
     * @returns {void}
     */
    const addApprover = async () => {
      errors.value = null;
      if (
        approversInput.value.at(-1).id &&
        approverFiltered.value.length !== 0
      ) {
        const newApprover = {
          name: '',
          id: '',
          sequence: Math.floor(Math.random() * 10000),
        };

        await approversInput.value.push(newApprover);

        newSelect2Approver(newApprover.sequence);

        // eslint-disable-next-line
        $(`#approver${newApprover.sequence}`).val('').trigger('change');

        selectApproverEvent(newApprover.sequence);
      }
    };

    /**
     * Remove approver in the list
     *
     * @returns {void}
     */
    const removeApprover = (input) => {
      errors.value = null;

      const { id, name, sequence } = input;

      if (approversInput.value.length > 1) {
        const indexOfApprovers = approversInput.value.findIndex(
          (approver) => approver.sequence === sequence
        );
        approversInput.value.splice(indexOfApprovers, 1);

        // eslint-disable-next-line
        $(`#approver${sequence}`).select2('destroy');

        const approverExists = approverFiltered.value.some(
          // eslint-disable-next-line
          (approver) => approver.id == id
        );

        if (!approverExists) {
          approverFiltered.value.push({
            id,
            text: name,
          });
        }

        const approverListData = approverList.value.filter(
          (object) =>
            !approversInput.value.find(
              // eslint-disable-next-line
              ({ id }) => object.id == id
            )
        );

        approverListData.push({
          id,
          text: name,
        });

        approversInput.value.forEach((approver) => {
          const data = approverListData;

          const object = {
            id: approver.id,
            text: approver.name,
          };

          data.push(object);

          const uniqueIds = [];
          const approversData = data.filter((element) => {
            const isDuplicate = uniqueIds.includes(Number(element.id));
            if (!isDuplicate) {
              uniqueIds.push(Number(element.id));
              return true;
            }
            return false;
          });

          // eslint-disable-next-line
          $(`#approver${approver.sequence}`)
            .empty()
            .select2({
              placeholder: i18n.global.t(
                'paragraphs.specify_the_approver_for_approval'
              ),
              data: approversData,
              language: {
                noResults: () => i18n.global.t('labels.no_result_found'),
              },
            });

          // eslint-disable-next-line
          $(`#approver${approver.sequence}`).val(approver.id).trigger('change');

          data.pop();
        });
      }
    };

    /**
     * Get approver list
     *
     * @returns {void}
     */
    const getApproverList = async () => {
      // Handle responses
      await workflowApiService
        .getApproverList(props.service.data.id)
        .then((response) => {
          approverList.value = response.data.data;
          approverFiltered.value = response.data.data;
        });
    };

    /**
     * Go to confirmation component
     *
     * @returns {void}
     */
    const submitForm = async (event) => {
      event.preventDefault();

      updateModel();

      // Reinitialize request values
      const data = {
        approvers: [],
      };

      // Reinitialize request values
      approversInput.value.forEach((approver) => {
        const newApproverObj = {
          id: approver.id,
          name: approver.name,
        };
        data.approvers.push(newApproverObj);
      });

      // Reinitialize state
      emit('set-loading', true);
      errors.value = null;

      formData.value.approvers = approversInput.value;

      // Handle responses
      workflowApiService
        .validateWorkflowApprover(data)
        .then(() => {
          formData.value.approvers = approversInput.value;
          formData.value.upload_file = props.form_data.upload_file;
          emit('next-step', formData.value);
        })
        .catch((error) => {
          const responseData = error.response?.data;

          // Inject validation errors
          if (responseData?.status_code === 422) {
            errors.value = responseData.data;
          }
        })
        .finally(() => {
          emit('set-loading', false);
        });
    };

    /**
     * Load select2
     *
     * @returns {void}
     */
    const loadSelect2 = () => {
      approversInput.value.forEach((approver) => {
        newSelect2Approver(approver.sequence);
        selectApproverEvent(approver.sequence);

        if (props.is_previous) {
          // eslint-disable-next-line
          $(`#approver${approver.sequence}`).val(approver.id);
        } else {
          // eslint-disable-next-line
          $(`#approver${approver.sequence}`).val('').trigger('change');
        }
      });
    };

    /**
     * Handle on mounted component
     *
     * @returns {void}
     */
    onMounted(async () => {
      await getApproverList();
      loadSelect2();
    });

    /**
     * Apply tracking changes
     */
    watchEffect(() => {
      formData.value = props.form_data;
    });

    return {
      CreateWorkflowSteps,
      clearFormData,
      getApproverList,
      loadSelect2,
      formRef,
      formData,
      errors,
      validationError,
      approversInput,
      addApprover,
      removeApprover,
      approverList,
      submitForm,
    };
  },
});
</script>

<style scoped>
@media only screen and (max-width: 576px) {
  .select2-container {
    width: 50% !important;
  }
}
</style>
