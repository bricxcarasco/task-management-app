<template>
  <div>
    <!-- Form Award Modal -->
    <award-form-modal
      :neo="editNeo"
      @get-awards="getAwards"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Award Modal -->
    <delete-award-modal
      :neo="editNeo"
      @get-awards="getAwards"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form Industry Modal -->
    <industry-form-modal
      :neo="editNeo"
      :years_of_experiences="years_of_experiences"
      :business_categories="business_categories"
      @get-industries="getIndustries"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Industry Modal -->
    <delete-industry-modal
      :neo="editNeo"
      @get-industries="getIndustries"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form URL Modal -->
    <url-form-modal
      :neo="editNeo"
      :years_of_experiences="years_of_experiences"
      :business_categories="business_categories"
      @get-urls="getUrls"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete URL Modal -->
    <delete-url-modal
      :neo="editNeo"
      @get-urls="getUrls"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- History form modal -->
    <history-form-modal
      :neo_id="neo.id"
      @get-histories="getHistories"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete History Modal -->
    <delete-history-modal
      @get-histories="getHistories"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form Qualification Modal -->
    <qualification-form-modal
      :neo="editNeo"
      @get-qualifications="getQualifications"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Qualification Modal -->
    <delete-qualification-modal
      :neo="editNeo"
      @get-qualifications="getQualifications"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form Skill Modal -->
    <skill-form-modal
      :neo="editNeo"
      @get-skills="getSkills"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Skill Modal -->
    <delete-skill-modal
      :neo="editNeo"
      @get-skills="getSkills"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form Product Modal -->
    <product-form-modal
      :neo="editNeo"
      @get-products="getProducts"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Product Reference URL Modal -->
    <reference-url-modal
      @open-reference-url-new-tab="handleOpenReferenceURL"
      ref="referenceUrlModalRef"
    />
    <!-- Delete Product Modal -->
    <delete-product-modal
      :neo="editNeo"
      @get-products="getProducts"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <!-- Form Email Modal -->
    <email-form-modal
      :neo="editNeo"
      @get-emails="getEmails"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
      @set-page-loading="setPageLoading"
    />
    <!-- Delete Email Modal -->
    <delete-email-modal
      @get-emails="getEmails"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />

    <delete-profile-image-modal
      ref="deleteProfileModalRef"
      @removeProfileImage="removeProfileImage"
    />

    <div class="container position-relative pt-6 py-md-6 mb-md-3 home">
      <page-loader :show="pageLoading" />

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
                {{ $t('headers.neo_profile') }}
              </h3>
            </div>
            <div class="py-2 p-md-3">
              <!-- User profile image -->
              <div class="px-2 py-2 mb-2 text-center">
                <div class="position-relative">
                  <div class="position-relative image__profile">
                    <img
                      class="d-block rounded-circle mx-auto img--profile_image"
                      :src="initialNeoProfile.profile_image"
                      :alt="imageAltName"
                      @error="
                        Common.handleNotFoundImageException(
                          $event,
                          DefaultImageCategory.RIO_NEO
                        )
                      "
                      width="110"
                    />
                    <button
                      class="btn__upload"
                      type="button"
                      v-if="initialNeoProfile.profile_photo"
                      @click="openDeleteProfileImageModal"
                    >
                      <label for="image__upload">
                        <i class="ai-delete"></i>
                      </label>
                    </button>
                  </div>
                </div>

                <form ref="formRef">
                  <file-pond
                    ref="filepondRef"
                    id="profile_image_filepond"
                  ></file-pond>
                </form>
                <div v-show="hasProfileImageForEdit">
                  <button
                    class="btn btn-primary d-block w-20 mx-auto"
                    id="btn__imageSave"
                    type="button"
                    @click.prevent="handleSaveProfileImage"
                  >
                    {{ $t('buttons.profile_image_edit') }}
                  </button>
                </div>
              </div>
              <div id="accordionExample" class="accordion accordion-no-radius">
                <base-accordion
                  v-for="info in infoList"
                  :key="info.id"
                  :index="info.id"
                  :title="info.title"
                  @accordionClick="manageAccordions"
                >
                  <!-- Organization Name Section -->
                  <div v-if="info.id == 1">
                    <organization-name-section
                      :neo="neo"
                      :organization_types="organization_types"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-page-loading="setPageLoading"
                    />
                  </div>

                  <!-- Location Section -->
                  <div v-else-if="info.id == 2">
                    <div class="row">
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.prefectures') }}
                          <sup class="text-danger ms-1">*</sup>
                        </label>
                        <select
                          v-model="editNeoProfile.prefecture"
                          class="form-select"
                          id="select-prefecture"
                          :class="
                            errors?.prefecture != null ? 'is-invalid' : ''
                          "
                        >
                          <option
                            v-for="(value, key) in prefectures"
                            :key="key"
                            :value="key"
                          >
                            {{ value }}
                          </option>
                        </select>
                        <div
                          v-show="errors?.prefecture"
                          v-for="(error, index) in errors?.prefecture"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div v-if="showCountry" class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.country') }}
                          <sup class="text-danger ms-1">*</sup>
                        </label>
                        <input
                          v-model="editNeoProfile.nationality"
                          class="form-control"
                          :class="
                            errors?.nationality != null ? 'is-invalid' : ''
                          "
                          type="text"
                        />
                        <div
                          v-show="errors?.nationality"
                          v-for="(error, index) in errors?.nationality"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.municipality') }}</label
                        >
                        <input
                          v-model="editNeoProfile.city"
                          :class="errors?.city != null ? 'is-invalid' : ''"
                          class="form-control"
                          type="email"
                        />
                        <div
                          v-show="errors?.city"
                          v-for="(error, index) in errors?.city"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.address') }}
                        </label>
                        <input
                          v-model="editNeoProfile.address"
                          :class="errors?.address != null ? 'is-invalid' : ''"
                          class="form-control"
                          type="text"
                        />
                        <div
                          v-show="errors?.address"
                          v-for="(error, index) in errors?.address"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.building') }}
                        </label>
                        <input
                          v-model="editNeoProfile.building"
                          :class="errors?.building != null ? 'is-invalid' : ''"
                          class="form-control"
                          type="text"
                        />
                        <div
                          v-show="errors?.building"
                          v-for="(error, index) in errors?.building"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div
                        class="
                          d-flex
                          justify-content-center
                          align-items-center
                          mt-3
                        "
                      >
                        <base-button
                          :title="this.$i18n.t('buttons.register')"
                          :buttonType="'success'"
                          :loading="loading"
                          @handleClick="handleUpdateLocation"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Self-introduction Section -->
                  <div v-else-if="info.id == 3">
                    <div>
                      <label for="textarea-input" class="form-label"
                        >{{ $t('accordions.self_introduction') }}
                      </label>
                      <textarea
                        v-model="editNeoProfile.self_introduce"
                        class="form-control"
                        id="textarea-input"
                        rows="5"
                        :class="
                          errors?.self_introduce != null ? 'is-invalid' : ''
                        "
                      ></textarea>
                      <p class="text-end fs-xs mb-0 mt-2">
                        {{ $t('labels.char_count') }}：<span id="charCounter">{{
                          totalCharacter
                        }}</span
                        >/ {{ maxCharacter }}
                      </p>
                      <div
                        v-show="errors?.self_introduce"
                        v-for="(error, index) in errors?.self_introduce"
                        :key="index"
                        class="invalid-feedback"
                      >
                        {{ error }}
                      </div>
                    </div>
                    <div
                      class="
                        d-flex
                        justify-content-center
                        align-items-center
                        mt-3
                      "
                    >
                      <base-button
                        :title="this.$i18n.t('buttons.register')"
                        :buttonType="'success'"
                        :loading="loading"
                        @handleClick="handleSaveIntroduction"
                      />
                    </div>
                  </div>

                  <!-- History & Establishment Section -->
                  <div v-else-if="info.id == 4">
                    <establishment-history-section
                      ref="establishmentHistorySectionRef"
                      :neo="neo"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-page-loading="setPageLoading"
                    />
                  </div>

                  <!-- Email Address Section -->
                  <div v-else-if="info.id == 5">
                    <email-section
                      ref="emailSectionRef"
                      :loading="loading"
                      :neo="editNeo"
                    />
                  </div>

                  <!-- Telephone Section -->
                  <div v-else-if="info.id == 6">
                    <div>
                      <label class="form-label" for="tel"
                        >{{ $t('labels.telephone') }}
                        <sup class="text-danger ms-1">*</sup></label
                      >
                      <input
                        v-model="editNeo.tel"
                        class="form-control"
                        :class="errors?.tel != null ? 'is-invalid' : ''"
                        type="number"
                      />
                      <div
                        v-show="errors?.tel"
                        v-for="(error, index) in errors?.tel"
                        :key="index"
                        class="invalid-feedback"
                      >
                        {{ error }}
                      </div>
                    </div>
                    <div
                      class="
                        d-flex
                        justify-content-center
                        align-items-center
                        mt-3
                      "
                    >
                      <base-button
                        :title="this.$i18n.t('buttons.register')"
                        :buttonType="'success'"
                        :loading="loading"
                        @handleClick="handleSaveTelephone"
                      />
                    </div>
                  </div>

                  <!-- URL Section -->
                  <div v-else-if="info.id == 7">
                    <url-section
                      ref="urlSectionRef"
                      :neo="editNeo"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Industry Section -->
                  <div v-else-if="info.id == 8">
                    <industry-section
                      ref="industrySectionRef"
                      :neo="editNeo"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Business hours & Holiday info Section -->
                  <div v-else-if="info.id == 9">
                    <div class="row">
                      <div class="col-sm-12 mb-3">
                        <label class="form-label" for="reg-email"
                          >{{ $t('labels.business_hours') }}
                          <sup class="text-danger ms-1">*</sup></label
                        >
                        <input
                          v-model="editNeo.business_hours.content"
                          class="form-control"
                          :class="errors?.content != null ? 'is-invalid' : ''"
                          type="text"
                        />
                        <div
                          v-show="errors?.content"
                          v-for="(error, index) in errors?.content"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label" for="holiday"
                          >{{ $t('labels.holiday') }}
                        </label>
                        <input
                          v-model="editNeo.business_hours.additional"
                          class="form-control"
                          :class="
                            errors?.additional != null ? 'is-invalid' : ''
                          "
                          type="text"
                        />
                        <div
                          v-show="errors?.additional"
                          v-for="(error, index) in errors?.additional"
                          :key="index"
                          class="invalid-feedback"
                        >
                          {{ error }}
                        </div>
                      </div>
                      <div
                        class="
                          d-flex
                          justify-content-center
                          align-items-center
                          mt-3
                        "
                      >
                        <base-button
                          :title="this.$i18n.t('buttons.register')"
                          :buttonType="'success'"
                          :loading="loading"
                          @handleClick="handleSaveBusinessHoliday"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Qualification & Skills Section -->
                  <div v-else-if="info.id == 10">
                    <qualification-section
                      ref="qualificationSectionRef"
                      :neo="editNeo"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                    <skill-section
                      ref="skillSectionRef"
                      :neo="editNeo"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Award History Section -->
                  <div v-else-if="info.id == 11">
                    <award-section
                      ref="awardSectionRef"
                      :neo="editNeo"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Profile Video Section -->
                  <div v-else-if="info.id == 12"></div>

                  <!-- Commodity Section -->
                  <div v-else-if="info.id == 13">
                    <product-section
                      ref="productSectionRef"
                      :neo="editNeo"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                      @open-reference-url="handleOpenReferenceURL"
                    />
                  </div>

                  <!-- Overseas Correspondence Section -->
                  <div v-else-if="info.id == 14">
                    <label class="form-label" for="reg-fn"
                      >{{ $t('accordions.overseas') }}
                      <sup class="text-danger ms-1">*</sup></label
                    >
                    <div class="pt-3">
                      <div
                        class="form-check form-check-inline"
                        :class="
                          errors?.overseas_support != null ? 'is-invalid' : ''
                        "
                      >
                        <input
                          v-model="editNeoProfile.overseas_support"
                          value="0"
                          class="form-check-input"
                          type="radio"
                          id="overseas-no"
                          name="overseas_support"
                        />
                        <label class="form-check-label" for="overseas-no">{{
                          $t('labels.no')
                        }}</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input
                          v-model="editNeoProfile.overseas_support"
                          value="1"
                          class="form-check-input"
                          type="radio"
                          id="overseas-yes"
                          name="overseas_support"
                        />
                        <label class="form-check-label" for="overseas-yes">{{
                          $t('labels.yes')
                        }}</label>
                      </div>
                      <base-validation-error :errors="errors?.gender" />
                      <div
                        class="
                          d-flex
                          justify-content-center
                          align-items-center
                          mt-3
                        "
                      >
                        <base-button
                          :title="this.$i18n.t('buttons.register')"
                          :buttonType="'success'"
                          :loading="loading"
                          @handleClick="handleSaveOverseasSupport"
                        />
                      </div>
                    </div>
                  </div>
                </base-accordion>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, computed, onMounted, watch } from 'vue';
import BaseAccordion from '../../../base/BaseAccordion.vue';
import BaseAlert from '../../../base/BaseAlert.vue';
import BaseButton from '../../../base/BaseButton.vue';
import BaseValidationError from '../../../base/BaseValidationError.vue';
import PageLoader from '../../../base/BaseSectionLoader.vue';
import OrganizationNameSection from './organization_name/Section.vue';
import EstablishmentHistorySection from './establishment_history/Section.vue';
import HistoryFormModal from './establishment_history/FormModal.vue';
import DeleteHistoryModal from './establishment_history/DeleteModal.vue';
import NeoProfileApiService from '../../../../api/neo/profile';
import { NeoProfileEditInfoList } from '../../../../utils/data';
import PrefectureTypes from '../../../../enums/PrefectureTypes';
import Accordions from '../../../../enums/NeoAccordions';
import IndustrySection from './industry/Section.vue';
import IndustryFormModal from './industry/FormModal.vue';
import DeleteIndustryModal from './industry/DeleteModal.vue';
import UrlSection from './url/Section.vue';
import UrlFormModal from './url/FormModal.vue';
import DeleteUrlModal from './url/DeleteModal.vue';
import AwardSection from './award_history/Section.vue';
import AwardFormModal from './award_history/FormModal.vue';
import DeleteAwardModal from './award_history/DeleteModal.vue';
import QualificationSection from './qualification/Section.vue';
import QualificationFormModal from './qualification/FormModal.vue';
import DeleteQualificationModal from './qualification/DeleteModal.vue';
import SkillSection from './skill/Section.vue';
import SkillFormModal from './skill/FormModal.vue';
import DeleteSkillModal from './skill/DeleteModal.vue';
import ProductSection from './product/Section.vue';
import ProductFormModal from './product/FormModal.vue';
import DeleteProductModal from './product/DeleteModal.vue';
import ReferenceUrlModal from './product/ReferenceUrlModal.vue';
import EmailSection from './email_address/Section.vue';
import EmailFormModal from './email_address/FormModal.vue';
import DeleteEmailModal from './email_address/DeleteModal.vue';
import FilePond from '../../../base/BaseFilePond.vue';
import DeleteProfileImageModal from './image/DeleteModal.vue';
import Common from '../../../../common';
import DefaultImageCategory from '../../../../enums/DefaultImageCategory';
import BpheroConfig from '../../../../config/bphero';

export default defineComponent({
  name: 'NeoProfileEdit',
  props: {
    user: {
      type: [Array, Object],
      required: true,
    },
    neo: {
      type: [Array, Object],
      required: true,
    },
    neo_profile: {
      type: [Array, Object],
      required: true,
    },
    prefectures: {
      type: [Array, Object],
      required: true,
    },
    years_of_experiences: {
      type: [Array, Object],
      required: true,
    },
    business_categories: {
      type: [Array, Object],
      required: true,
    },
    organization_types: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    BaseAccordion,
    BaseAlert,
    BaseButton,
    BaseValidationError,
    PageLoader,
    OrganizationNameSection,
    EstablishmentHistorySection,
    HistoryFormModal,
    DeleteHistoryModal,
    IndustrySection,
    IndustryFormModal,
    DeleteIndustryModal,
    UrlSection,
    UrlFormModal,
    DeleteUrlModal,
    AwardSection,
    AwardFormModal,
    DeleteAwardModal,
    QualificationSection,
    QualificationFormModal,
    DeleteQualificationModal,
    SkillSection,
    SkillFormModal,
    DeleteSkillModal,
    ProductSection,
    ProductFormModal,
    DeleteProductModal,
    ReferenceUrlModal,
    EmailFormModal,
    EmailSection,
    DeleteEmailModal,
    FilePond,
    DeleteProfileImageModal,
  },
  setup(props) {
    /**
     * Data ref properties
     */
    const neoProfileApiService = new NeoProfileApiService();
    const infoList = ref(NeoProfileEditInfoList);
    const editUser = ref(props.user);
    const editNeo = ref(props.neo);
    const showEmailModal = ref(false);
    const emailSectionRef = ref(null);
    const editNeoProfile = ref(props.neo_profile);
    const initialNeo = ref(props.neo);
    const initialNeoProfile = ref(props.neo_profile);
    const initialBusinessHours = ref(props.neo.business_hours);
    const loading = ref(false);
    const editId = ref(0);
    const errors = ref(null);
    const pageLoading = ref(false);
    const showCountry = ref(false);
    const alert = ref({
      success: false,
      failed: false,
      message: '',
    });
    const maxCharacter = ref(500);
    const totalCharacter = ref(0);
    const establishmentHistorySectionRef = ref(null);
    const industrySectionRef = ref(null);
    const urlSectionRef = ref(null);
    const awardSectionRef = ref(null);
    const qualificationSectionRef = ref(null);
    const skillSectionRef = ref(null);
    const productSectionRef = ref(null);
    const referenceUrlModalRef = ref(null);

    const filepondRef = ref(null);
    const deleteProfileModalRef = ref(null);
    const formRef = ref({});

    const hasProfileImageForEdit = ref(false);

    /**
     * Computed properties
     */
    const profileImage = computed(() => BpheroConfig.DEFAULT_IMG);
    const imageAltName = computed(() => `${editNeo.value.organization_name}`);

    /**
     * Reset alert message
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
      alert.value.message = '';
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
     * Set loading status
     *
     * @param {string} status
     * @returns {void}
     */
    const setLoading = (status) => {
      loading.value = status;
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
     * Update NEO profile image
     *
     * @returns {void}
     */
    const handleSaveProfileImage = async () => {
      const files = filepondRef.value.pond.getFiles();

      if (files.length > 0) {
        const data = {
          id: editNeo.value.id,
          profile_image: files[0].file,
        };

        // Reset alert & begin loading
        resetAlert();
        setPageLoading(true);
        setLoading(true);

        await neoProfileApiService
          .updateProfileImage(data)
          .then((response) => {
            if (response.data.status_code === 200) {
              // Remove errors & show success alert
              errors.value = null;
              setAlert('success');

              // Remove files in filepond instance
              filepondRef.value.pond.removeFiles();
              hasProfileImageForEdit.value = false;

              // Set new data
              initialNeoProfile.value.profile_photo = `${
                response.data.data.profile_image
              }?${new Date().getTime()}`;
              initialNeoProfile.value.profile_image = `${
                response.data.data.profile_image
              }?${new Date().getTime()}`;
            }
          })
          .catch((error) => {
            const responseData = error.response.data;

            if (responseData.status_code === 422) {
              errors.value = Common.constructValidationErrors(
                responseData.data
              );

              return;
            }

            setAlert('failed');
          })
          .finally(() => {
            // End loading
            setPageLoading(false);
            setLoading(false);
          });
      } else {
        // Remove files in filepond instance
        filepondRef.value.pond.removeFiles();
        hasProfileImageForEdit.value = false;

        setAlert('failed');
      }
    };

    /**
     * Handler for opening delete profile image modal
     *
     * @returns {void}
     */
    const openDeleteProfileImageModal = async () => {
      deleteProfileModalRef.value.show();
    };

    /**
     * Remove NEO profile image
     *
     * @returns {void}
     */
    const removeProfileImage = async () => {
      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      deleteProfileModalRef.value.setLoading(true);

      await neoProfileApiService
        .deleteProfileImage(editNeo.value.id)
        .then((response) => {
          if (response.data.status_code === 200) {
            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');

            // Remove files in filepond instance
            filepondRef.value.pond.removeFiles();
            hasProfileImageForEdit.value = false;

            // Set new data
            initialNeoProfile.value.profile_image = `${
              response.data.data.profile_image
            }?${new Date().getTime()}`;
            initialNeoProfile.value.profile_photo = null;
          }
        })
        .catch((error) => {
          const responseData = error.response.data;

          if (responseData.status_code === 422) {
            errors.value = Common.constructValidationErrors(responseData.data);

            return;
          }

          setAlert('failed');
        })
        .finally(() => {
          // End loading
          setPageLoading(false);
          setLoading(false);
          deleteProfileModalRef.value.hide();
        });
    };

    /**
     * Cancel and revert neo edit changes
     */
    const cancelNeoDataEdit = () => {
      editNeo.value = { ...initialNeo.value };
    };

    /**
     * Cancel and revert neo_profile edit changes
     */
    const cancelNeoProfileDataEdit = () => {
      editNeoProfile.value = { ...initialNeoProfile.value };
    };

    /**
     * Cancel and revert business_hours edit changes
     */
    const cancelBusinessHoursDataEdit = () => {
      editNeo.value.business_hours = { ...initialBusinessHours.value };
    };

    /**
     *
     * Function to revert to original value for self_introduction (if not saved)
     */
    const cancelIntroductionDataEdit = () => {
      editNeoProfile.value = { ...initialNeoProfile.value };
      totalCharacter.value =
        editNeoProfile.value.self_introduce != null
          ? editNeoProfile.value.self_introduce.length
          : 0;
      document.getElementById('charCounter').style.color = '#737491';
    };

    /**
     * Template reference call for get emails
     *
     * @param {object} data
     * @returns {void}
     */
    const getEmails = () => {
      emailSectionRef.value.getEmails();
    };
    /**
     * Accordion management handler
     */
    const manageAccordions = (id) => {
      // Reset errors
      errors.value = null;

      switch (id) {
        case Accordions.LOCATION:
          cancelNeoProfileDataEdit();
          break;
        case Accordions.SELF_INTRODUCTION:
          cancelIntroductionDataEdit();
          break;
        case Accordions.TELEPHONE:
          cancelNeoDataEdit();
          break;
        case Accordions.BUSINESS_HOLIDAY_INFO:
          cancelBusinessHoursDataEdit();
          break;
        case Accordions.OVERSEAS:
          cancelNeoProfileDataEdit();
          break;
        default:
          break;
      }

      // Set edit id value
      editId.value = id;
    };

    /**
     * Template reference call for get histories
     *
     * @returns {void}
     */
    const getHistories = () => {
      establishmentHistorySectionRef.value.getHistories();
    };

    /**
     * Update NEO profile location
     */
    const handleUpdateLocation = async () => {
      const data = {
        prefecture: editNeoProfile.value.prefecture,
        nationality: editNeoProfile.value.nationality,
        city: editNeoProfile.value.city,
        address: editNeoProfile.value.address,
        building: editNeoProfile.value.building,
      };

      // Reset flash message
      resetAlert();

      // Start loading
      loading.value = true;

      await neoProfileApiService
        .updateLocation(data, editNeo.value.id)
        .then((response) => {
          if (response.data.status_code === 200) {
            const responseData = response.data.data;

            // Set new data as initial data
            initialNeoProfile.value.prefecture = responseData.prefecture;
            initialNeoProfile.value.nationality = responseData.nationality;
            initialNeoProfile.value.city = responseData.city;
            initialNeoProfile.value.address = responseData.address;
            initialNeoProfile.value.building = responseData.building;

            // Set nationality to null if prefecture is not 'æ—¥æœ¬ä»¥å¤–'
            if (
              parseInt(responseData.prefecture, 10) !==
              parseInt(PrefectureTypes.OTHER, 10)
            ) {
              editNeoProfile.value.nationality = null;
            }

            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');
          }
        })
        .catch((error) => {
          if (error.response.status === 400) {
            errors.value = error.response.data;
            loading.value = false;

            return;
          }

          alert.value.failed = true;
        })
        .finally(() => {
          // End loading
          loading.value = false;
        });
    };

    /**
     * Toggle location country display handler
     *
     * @param {Number} value
     */
    const handleDisplayCountry = (value) => {
      showCountry.value = value === PrefectureTypes.OTHER;
    };

    /**
     * Template reference call for get industries
     */
    const getIndustries = () => {
      industrySectionRef.value.getIndustries();
    };

    /**
     * Template reference call for get URLs
     */
    const getUrls = () => {
      urlSectionRef.value.getUrls();
    };

    /**
     * Template reference call for get awards
     */
    const getAwards = () => {
      awardSectionRef.value.getAwards();
    };

    /**
     * Template reference call for get qualifications
     */
    const getQualifications = () => {
      qualificationSectionRef.value.getQualifications();
    };

    /**
     * Template reference call for get skills
     */
    const getSkills = () => {
      skillSectionRef.value.getSkills();
    };

    /**
     * Template reference call for get products
     */
    const getProducts = () => {
      productSectionRef.value.getProducts();
    };

    /**
     * Open product reference URL
     *
     * @returns {void}
     */
    const handleOpenReferenceURL = (referenceUrl) => {
      referenceUrlModalRef.value.referenceUrl = referenceUrl;
      referenceUrlModalRef.value.show();
    };

    /**
     * Telephone registration handler
     */
    const handleSaveTelephone = async () => {
      resetAlert();
      loading.value = true;
      await neoProfileApiService
        .updateTelephone(
          {
            id: editId.value,
            tel: editNeo.value.tel,
          },
          editNeo.value.id
        )
        .then((response) => {
          if (response.data.status_code === 200) {
            initialNeo.value.tel = editNeo.value.tel;
            errors.value = null;
            setAlert('success');
          }
          loading.value = false;
        })
        .catch((error) => {
          if (error.response.status === 400) {
            errors.value = error.response.data;
            loading.value = false;
            return;
          }
          alert.value.failed = true;
          loading.value = false;
        })
        .finally(() => {
          // End loading
          loading.value = false;
        });
    };

    /**
     * Self-Introduction handler
     */
    const handleSaveIntroduction = async () => {
      setPageLoading(true);
      resetAlert();
      loading.value = true;
      await neoProfileApiService
        .updateIntroduction(
          {
            id: editId.value,
            self_introduce: editNeoProfile.value.self_introduce,
          },
          editNeo.value.id
        )
        .then((response) => {
          if (response.data.status_code === 200) {
            const responseData = response.data.data;
            errors.value = null;
            setAlert('success');

            // Update initial values
            initialNeoProfile.value.self_introduce =
              responseData.self_introduce;
            totalCharacter.value =
              initialNeoProfile.value.self_introduce != null
                ? initialNeoProfile.value.self_introduce.length
                : 0;
          }
          loading.value = false;
        })
        .catch((error) => {
          if (error.response.status === 400) {
            errors.value = error.response.data;
            loading.value = false;

            return;
          }

          alert.value.failed = true;
        })
        .finally(() => {
          setPageLoading(false);
        });
    };

    /**
     * Business hours and holiday registration handler
     */
    const handleSaveBusinessHoliday = async () => {
      resetAlert();
      loading.value = true;
      neoProfileApiService
        .upsertBusinessHoliday(
          {
            content: editNeo.value.business_hours.content,
            additional: editNeo.value.business_hours.additional,
          },
          editNeo.value.id
        )
        .then((response) => {
          if (response.data.status_code === 200) {
            initialBusinessHours.value.content =
              editNeo.value.business_hours.content;
            initialBusinessHours.value.additional =
              editNeo.value.business_hours.additional;
            errors.value = null;
            setAlert('success');
          }
          loading.value = false;
        })
        .catch((error) => {
          if (error.response.status === 400) {
            errors.value = error.response.data;
            loading.value = false;
            return;
          }
          alert.value.failed = true;
          loading.value = false;
        });
    };

    /**
     * Overseas support handler
     */
    const handleSaveOverseasSupport = async () => {
      resetAlert();
      loading.value = true;
      await neoProfileApiService
        .updateOverseasSupport(
          {
            id: editId.value,
            overseas_support: editNeoProfile.value.overseas_support,
          },
          editNeo.value.id
        )
        .then((response) => {
          if (response.data.status_code === 200) {
            initialNeoProfile.value.overseas_support =
              editNeoProfile.value.overseas_support;
            errors.value = null;
            setAlert('success');
          }
          loading.value = false;
        })
        .catch((error) => {
          if (error.response.status === 400) {
            errors.value = error.response.data;
            loading.value = false;
            return;
          }
          alert.value.failed = true;
          loading.value = false;
        })
        .finally(() => {
          // End loading
          loading.value = false;
        });
    };

    /**
     * initialize event listeners for profile image edit
     */
    const uploadProfileImageListener = () => {
      const pond = document.querySelector('#profile_image_filepond');

      pond.addEventListener('FilePond:processfile', () => {
        hasProfileImageForEdit.value = true;
      });
      pond.addEventListener('FilePond:processfilerevert', () => {
        hasProfileImageForEdit.value = false;
      });
      pond.addEventListener('FilePond:removefile', () => {
        hasProfileImageForEdit.value = false;
      });
    };

    /**
     * Mounted properties
     */
    onMounted(() => {
      handleDisplayCountry(parseInt(editNeoProfile.value.prefecture, 10));
      uploadProfileImageListener();
    });

    /**
     * Watch on prefecture change
     */
    watch(
      () => editNeoProfile.value.prefecture,
      (newValue) => {
        handleDisplayCountry(parseInt(newValue, 10));
      }
    );

    /**
     * Watch on self_introduce change
     */
    watch(
      () => editNeoProfile.value.self_introduce,
      () => {
        totalCharacter.value =
          editNeoProfile.value.self_introduce != null
            ? editNeoProfile.value.self_introduce.length
            : 0;
        document.getElementById('charCounter').style.color = '#737491';

        if (totalCharacter.value > maxCharacter.value) {
          document.getElementById('charCounter').style.color = '#f74f78';
        }
      }
    );

    return {
      industrySectionRef,
      awardSectionRef,
      qualificationSectionRef,
      skillSectionRef,
      urlSectionRef,
      productSectionRef,
      referenceUrlModalRef,
      getIndustries,
      getAwards,
      getQualifications,
      getSkills,
      getUrls,
      getProducts,
      cancelNeoDataEdit,
      editUser,
      editNeo,
      editNeoProfile,
      initialNeoProfile,
      loading,
      profileImage,
      imageAltName,
      manageAccordions,
      handleUpdateLocation,
      alert,
      pageLoading,
      infoList,
      resetAlert,
      setAlert,
      setLoading,
      setPageLoading,
      editId,
      handleSaveTelephone,
      errors,
      showCountry,
      establishmentHistorySectionRef,
      getHistories,
      handleSaveIntroduction,
      totalCharacter,
      cancelIntroductionDataEdit,
      maxCharacter,
      emailSectionRef,
      showEmailModal,
      getEmails,
      handleSaveBusinessHoliday,
      filepondRef,
      formRef,
      deleteProfileModalRef,
      handleSaveProfileImage,
      openDeleteProfileImageModal,
      removeProfileImage,
      handleSaveOverseasSupport,
      hasProfileImageForEdit,
      handleOpenReferenceURL,
      Common,
      DefaultImageCategory,
    };
  },
});
</script>
