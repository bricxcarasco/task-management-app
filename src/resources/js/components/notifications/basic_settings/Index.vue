<template>
  <div>
    <div class="container position-relative pt-6 py-md-6 mb-md-3 home--height">
      <!-- Page loader -->
      <page-loader :show="pageLoading" />

      <!-- Alert message -->
      <base-alert
        :success="alert.success"
        :danger="alert.failed"
        :message="alert.message"
        @closeAlert="resetAlert"
      />

      <div class="row">
        <div class="col-12 offset-md-3 col-md-9">
          <div class="d-flex flex-column h-100 bg-light rounded-3 shadow-lg">
            <div class="border-bottom">
              <h3 class="p-3 mb-0 text-center">
                {{ $t('links.basic_settings') }}
              </h3>
            </div>
            <div class="pb-2 p-md-3">
              <div
                id="basic-settings-accordion"
                class="accordion accordion-no-radius"
              >
                <!-- Feature Description Setting -->
                <div class="accordion-item mb-0">
                  <h2 class="accordion-header" id="headingOne">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapse-generic-settings"
                    >
                      {{ $t('accordions.feature_description') }}
                    </button>
                  </h2>
                  <div
                    class="accordion-collapse collapse"
                    id="collapse-generic-settings"
                    aria-labelledby="headingOne"
                    data-bs-parent="#basic-settings-accordion"
                  >
                    <div class="accordion-body">
                      <form
                        action="POST"
                        @submit.prevent="handleGeneralSettingSubmit"
                      >
                        <div class="col-12 mb-4">
                          <ul class="list-group list-group-flush my-3">
                            <!-- Toggle Display Feature Description -->
                            <li
                              class="d-flex list-group-item py-2 px-2 no-border"
                            >
                              <div class="form-check form-switch">
                                <input
                                  v-model="generalFormData.feature_description"
                                  type="checkbox"
                                  class="form-check-input"
                                  id="general-feature-description"
                                />
                                <label
                                  class="form-check-label"
                                  for="general-feature-description"
                                >
                                  {{ $t('labels.hide_show') }}
                                </label>
                              </div>
                            </li>
                          </ul>
                        </div>
                        <div class="col-12 mb-3 text-center">
                          <base-button
                            type="submit"
                            :title="this.$i18n.t('buttons.register')"
                            :buttonType="'success'"
                          />
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Email Notification Settings -->
                <div class="accordion-item mb-0">
                  <h2 class="accordion-header" id="headingOne">
                    <button
                      class="accordion-button collapsed"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#collapse-email-notification-settings"
                    >
                      {{ $t('accordions.mail_notification_settings') }}
                    </button>
                  </h2>
                  <div
                    class="accordion-collapse collapse"
                    id="collapse-email-notification-settings"
                    aria-labelledby="headingOne"
                    data-bs-parent="#basic-settings-accordion"
                  >
                    <div class="accordion-body">
                      <div class="col-sm-12 mb-3">
                        <p>
                          {{ $t('messages.please_select_to_receive_notif') }}
                        </p>
                        <ul class="list-group list-group-flush my-3">
                          <li
                            v-for="(template, value) in mailTemplates"
                            :key="value"
                            class="d-flex list-group-item py-2 px-2 no-border"
                          >
                            <input
                              class="mail-template-checkbox form-check-input"
                              :value="value"
                              type="checkbox"
                            />
                            <div>
                              <span class="fs-xs c-primary ms-2">
                                {{ template }}
                              </span>
                            </div>
                          </li>
                        </ul>
                        <div class="text-center">
                          <base-button
                            :title="this.$i18n.t('buttons.register')"
                            :buttonType="'success'"
                            @handleClick="handleSaveMailTemplates"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue';
import BaseAlert from '../../base/BaseAlert.vue';
import BaseButton from '../../base/BaseButton.vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BasicSettingsApiService from '../../../api/notifications/settings';
import BpheroConfig from '../../../config/bphero';
import i18n from '../../../i18n';

export default defineComponent({
  name: 'BasicSettingsIndex',
  props: {
    user: {
      type: [Array, Object],
      required: true,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    mail_templates: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAlert,
    BaseButton,
    PageLoader,
  },
  setup(props) {
    /**
     * Data properties
     */
    const basicSettingsApiService = new BasicSettingsApiService();
    const rio = ref(props.rio);
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const mailTemplates = ref(props.mail_templates);
    const generalFormData = ref({
      feature_description: props.user.feature_description,
    });

    /**
     * Computed properties
     */
    const profileImage = computed(() => BpheroConfig.DEFAULT_IMG);
    const imageAltName = computed(
      () => `${rio.value.first_name} ${rio.value.family_name}`
    );

    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Set alert message
     *
     * @param {string} status
     * @returns {void}
     */
    const setAlert = (status = 'success', message = null) => {
      const statusValue = status === 'success' ? 'success' : 'failed';
      alert.value[statusValue] = true;
      alert.value.message = message;
    };

    /**
     * Set page loading
     *
     * @param {bool} state
     * @returns {void}
     */
    const setPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Set selected templates on page load
     */
    const handleSetSelectedTemplates = async () => {
      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);

      await basicSettingsApiService
        .getSelectedTemplates()
        .then((response) => {
          if (response.data.status_code === 200) {
            const selectedSettings = Object.values(response.data.data);
            const checkboxSelector = document.querySelectorAll(
              '.mail-template-checkbox'
            );

            checkboxSelector.forEach((checkbox) => {
              if (selectedSettings.includes(parseInt(checkbox.value, 10))) {
                /* eslint no-param-reassign: "error" */
                checkbox.checked = true;
              }
            });
          }
        })
        .catch(() => {
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Set selected templates on page load
     */
    const handleDisablingAlwaysCheckedTemplate = () => {
      const changeEmailSelector = document.querySelector(
        `.mail-template-checkbox[value="${3}"]`
      );
      const changePassSelector = document.querySelector(
        `.mail-template-checkbox[value="${10}"]`
      );

      // Disable labels
      changeEmailSelector.nextElementSibling.firstChild.classList.add(
        'label-disabled'
      );
      changePassSelector.nextElementSibling.firstChild.classList.add(
        'label-disabled'
      );

      // Check checkboxes
      changeEmailSelector.checked = true;
      changePassSelector.checked = true;

      // Disable checkboxes
      changeEmailSelector.disabled = true;
      changePassSelector.disabled = true;

      // Move disabled email element
      changePassSelector.parentElement.before(
        changeEmailSelector.parentElement
      );
    };

    /**
     * Update mail template settings
     */
    const handleSaveMailTemplates = async () => {
      const data = { rio_id: rio.value.id };
      const unselectedTemplatesArr = [];
      const uncheckedSelector = document.querySelectorAll(
        '.mail-template-checkbox:not(:checked)'
      );

      // Get unselected template values
      uncheckedSelector.forEach((element) => {
        unselectedTemplatesArr.push(element.value);
      });

      // Set unselected templates to data
      data.mail_template_id = unselectedTemplatesArr;

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);

      await basicSettingsApiService
        .updateMailTemplates(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            setAlert(
              'success',
              i18n.global.t('alerts.updated_email_notif_settings')
            );
          }
        })
        .catch(() => {
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Update general settings
     */
    const handleGeneralSettingSubmit = async () => {
      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);

      await basicSettingsApiService
        .updateGeneralSettings(generalFormData.value)
        .then(() => {
          setAlert('success', i18n.global.t('alerts.successfully_updated'));
        })
        .catch(() => {
          setAlert('failed');
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      handleSetSelectedTemplates();
      handleDisablingAlwaysCheckedTemplate();
    });

    return {
      alert,
      pageLoading,
      profileImage,
      imageAltName,
      mailTemplates,
      resetAlert,
      handleSaveMailTemplates,
      handleGeneralSettingSubmit,
      generalFormData,
    };
  },
});
</script>
