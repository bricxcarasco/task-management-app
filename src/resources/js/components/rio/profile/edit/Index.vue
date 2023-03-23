<template>
  <div>
    <!-- Product Form Modal -->
    <product-form-modal
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
      @get-products="getProducts"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Educational background form modal -->
    <educational-background-form-modal
      @get-backgrounds="getEducationalBackgrounds"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Educational background Modal -->
    <delete-educational-background-modal
      @get-backgrounds="getEducationalBackgrounds"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Profession Form Modal -->
    <profession-form-modal
      @get-professions="getProfessions"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Profession Modal -->
    <delete-profession-modal
      @get-professions="getProfessions"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form Industry Modal -->
    <industry-form-modal
      @get-industries="getIndustries"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
      :years_of_experiences="years_of_experiences"
      :business_categories="business_categories"
    />
    <!-- Delete Industry Modal -->
    <delete-industry-modal
      @get-industries="getIndustries"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Form Industry Modal -->
    <award-form-modal
      @get-awards="getAwards"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Award Modal -->
    <delete-award-modal
      @get-awards="getAwards"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Qualification Form Modal -->
    <qualification-form-modal
      @get-qualifications="getQualifications"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Qualification Modal -->
    <delete-qualification-modal
      @get-qualifications="getQualifications"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Add Skill Modal -->
    <skill-form-modal
      @get-skills="getSkills"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Delete Skill Modal -->
    <delete-skill-modal
      @get-skills="getSkills"
      @set-alert="setAlert"
      @reset-alert="resetAlert"
    />
    <!-- Affiliate Form Modal -->
    <affiliate-form-modal
      @get-affiliates="getAffiliates"
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
                {{ $t('headers.profile_edit') }}
              </h3>
            </div>
            <div class="py-2 p-md-3">
              <!-- User profile image -->
              <div class="px-2 py-2 mb-2 text-center">
                <div class="position-relative">
                  <div class="position-relative image__profile">
                    <img
                      class="d-block rounded-circle mx-auto img--profile_image"
                      :src="initialRioProfile.profile_image"
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
                      v-if="initialRioProfile.profile_photo"
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
                  @accordionClick="updateEditId"
                >
                  <!-- Name Section -->
                  <div v-if="info.id == 1">
                    <!-- Family Name -->
                    <div>
                      <label class="form-label" for="reg-fn"
                        >{{ $t('labels.name') }}
                        <sup class="text-danger ms-1">*</sup></label
                      >
                      <input
                        v-model="editRio.family_name"
                        class="form-control"
                        :class="errors?.family_name != null ? 'is-invalid' : ''"
                        type="text"
                        :placeholder="$t('placeholders.family_name')"
                      />
                      <base-validation-error :errors="errors?.family_name" />
                    </div>
                    <!-- First Name -->
                    <div class="mt-2 mb-4">
                      <input
                        v-model="editRio.first_name"
                        class="form-control"
                        :class="errors?.first_name != null ? 'is-invalid' : ''"
                        type="text"
                        :placeholder="$t('placeholders.first_name')"
                      />
                      <base-validation-error :errors="errors?.first_name" />
                    </div>
                    <!-- Family Kana Name -->
                    <div>
                      <label class="form-label" for="reg-fn"
                        >{{ $t('labels.name_furigana') }}
                        <sup class="text-danger ms-1">*</sup></label
                      >
                      <input
                        v-model="editRio.family_kana"
                        class="form-control"
                        :class="errors?.family_kana != null ? 'is-invalid' : ''"
                        type="text"
                        :placeholder="$t('placeholders.family_kana')"
                      />
                      <base-validation-error :errors="errors?.family_kana" />
                    </div>
                    <!-- First Kana Name -->
                    <div class="mt-2">
                      <input
                        v-model="editRio.first_kana"
                        class="form-control"
                        :class="errors?.first_kana != null ? 'is-invalid' : ''"
                        type="text"
                        :placeholder="$t('placeholders.first_kana')"
                      />
                      <base-validation-error :errors="errors?.first_kana" />
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
                          @handleClick="handleSaveName"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Birth Date -->
                  <div v-else-if="info.id == 2">
                    <div>
                      <label class="form-label" for="reg-fn">
                        {{ $t('accordions.birthday') }}
                        <sup class="text-danger ms-1">*</sup>
                      </label>
                      <input
                        v-model="editRio.birth_date"
                        class="form-control date-picker_ymd rounded pe-5"
                        :class="errors?.birth_date != null ? 'is-invalid' : ''"
                        type="text"
                        placeholder="yyyy-mm-dd"
                      />
                      <base-validation-error :errors="errors?.birth_date" />
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
                        @handleClick="handleSaveBirthdate"
                      />
                    </div>
                  </div>

                  <!-- Gender -->
                  <div v-else-if="info.id == 3">
                    <div>
                      <label class="form-label" for="reg-fn"
                        >{{ $t('accordions.gender') }}
                        <sup class="text-danger ms-1">*</sup></label
                      >
                      <div class="pt-3">
                        <div
                          class="form-check form-check-inline"
                          :class="errors?.gender != null ? 'is-invalid' : ''"
                        >
                          <input
                            v-model="editRio.gender"
                            value="1"
                            class="form-check-input"
                            type="radio"
                            id="gender-male"
                            name="gender"
                          />
                          <label class="form-check-label" for="gender-male">{{
                            $t('labels.gender_male')
                          }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input
                            v-model="editRio.gender"
                            value="2"
                            class="form-check-input"
                            type="radio"
                            id="gender-female"
                            name="gender"
                          />
                          <label class="form-check-label" for="gender-female">{{
                            $t('labels.gender_female')
                          }}</label>
                        </div>
                        <base-validation-error :errors="errors?.gender" />
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
                        @handleClick="handleSaveGender"
                      />
                    </div>
                  </div>

                  <!-- Present Address -->
                  <div v-else-if="info.id == 4">
                    <div class="row">
                      <div class="col-sm-12 mb-3">
                        <label class="form-label" for="reg-email">
                          {{ $t('labels.present_address_prefecture') }}
                          <sup class="text-danger ms-1">*</sup>
                        </label>
                        <select
                          v-model="editRioProfile.present_address_prefecture"
                          class="form-select"
                          id="select-present_address_prefecture"
                          :class="
                            errors?.present_address_prefecture != null
                              ? 'is-invalid'
                              : ''
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
                        <base-validation-error
                          :errors="errors?.present_address_prefecture"
                        />
                      </div>
                      <div class="present-country col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.present_address_country') }}
                          <sup class="text-danger ms-1">*</sup>
                        </label>
                        <input
                          v-model="editRioProfile.present_address_nationality"
                          class="form-control"
                          :class="
                            errors?.present_address_nationality != null
                              ? 'is-invalid'
                              : ''
                          "
                          type="text"
                        />
                        <base-validation-error
                          :errors="errors?.present_address_nationality"
                        />
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.present_address_municipality') }}</label
                        >
                        <input
                          v-model="editRioProfile.present_address_city"
                          :class="
                            errors?.present_address_city != null
                              ? 'is-invalid'
                              : ''
                          "
                          class="form-control"
                          type="email"
                        />
                        <base-validation-error
                          :errors="errors?.present_address_city"
                        />
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.present_address_address') }}
                        </label>
                        <input
                          v-model="editRioProfile.present_address"
                          :class="
                            errors?.present_address != null ? 'is-invalid' : ''
                          "
                          class="form-control"
                          type="text"
                        />
                        <base-validation-error
                          :errors="errors?.present_address"
                        />
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label">
                          {{ $t('labels.present_address_building') }}
                        </label>
                        <input
                          v-model="editRioProfile.present_address_building"
                          :class="
                            errors?.present_address_building != null
                              ? 'is-invalid'
                              : ''
                          "
                          class="form-control"
                          type="text"
                        />
                        <base-validation-error
                          :errors="errors?.present_address_building"
                        />
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
                          @handleClick="handleSavePresentAddress"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Nationality -->
                  <div v-else-if="info.id == 5">
                    <div class="row">
                      <div class="col-sm-12 mb-3">
                        <label for="text-input" class="form-label"
                          >{{ $t('labels.home_address_prefecture') }}
                          <sup class="text-danger ms-1">*</sup></label
                        >
                        <select
                          v-model="editRioProfile.home_address_prefecture"
                          class="form-select select-home_address_prefecture"
                          id="home_address_prefecture"
                          :class="
                            errors?.home_address_prefecture != null
                              ? 'is-invalid'
                              : ''
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
                        <base-validation-error
                          :errors="errors?.home_address_prefecture"
                        />
                      </div>
                      <div
                        class="col-sm-12 mb-3 home_address_nationality"
                        id="register-home_address_nationality"
                      >
                        <label class="form-label" for="reg-fn">
                          {{ $t('labels.home_address_country') }}
                          <sup class="text-danger ms-1">*</sup>
                        </label>
                        <input
                          v-model="editRioProfile.home_address_nationality"
                          class="form-control"
                          :class="
                            errors?.home_address_nationality != null
                              ? 'is-invalid'
                              : ''
                          "
                          type="text"
                        />
                        <base-validation-error
                          :errors="errors?.home_address_nationality"
                        />
                      </div>
                      <div class="col-sm-12 mb-3">
                        <label class="form-label" for="reg-fn">{{
                          $t('labels.home_address_municipality')
                        }}</label>
                        <input
                          v-model="editRioProfile.home_address_city"
                          class="form-control"
                          :class="
                            errors?.home_address_city != null
                              ? 'is-invalid'
                              : ''
                          "
                          type="text"
                        />
                        <base-validation-error
                          :errors="errors?.home_address_city"
                        />
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
                        @handleClick="handleSaveHomeAddress"
                      />
                    </div>
                  </div>

                  <!-- Self Introduction -->
                  <div v-else-if="info.id == 6">
                    <div>
                      <label for="textarea-input" class="form-label"
                        >{{ $t('accordions.self_introduction') }}
                      </label>
                      <textarea
                        v-model="editRioProfile.self_introduce"
                        class="form-control"
                        id="textarea-input"
                        rows="5"
                        :class="
                          errors?.self_introduce != null ? 'is-invalid' : ''
                        "
                        @keyup="charCount"
                      ></textarea>
                      <p class="text-end fs-xs mb-0 mt-2">
                        {{ $t('labels.char_count') }}：<span id="charCounter">{{
                          totalCharacter
                        }}</span
                        >/ {{ maxCharacter }}
                      </p>
                      <base-validation-error :errors="errors?.self_introduce" />
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
                        @handleClick="handleSaveSelfIntroduction"
                      />
                    </div>
                  </div>

                  <!-- Industry Section -->
                  <div v-else-if="info.id == 7">
                    <industry-section
                      ref="industrySectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Profession Section -->
                  <div v-else-if="info.id == 8">
                    <profession-section
                      ref="professionSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Profile Video -->
                  <div v-else-if="info.id == 9">
                    <div>
                      <label class="form-label" for="reg-email"
                        >プロフィール動画
                        <sup class="text-danger ms-1">*</sup></label
                      >
                      <input
                        class="form-control"
                        :class="errors?.first_name != null ? 'is-invalid' : ''"
                        type="text"
                      />
                      <div
                        v-show="errors?.first_name"
                        v-for="(error, index) in errors?.first_name"
                        :key="index"
                        class="invalid-feedback"
                      >
                        {{ error }}
                      </div>
                    </div>
                  </div>

                  <!-- Educational Background -->
                  <div v-else-if="info.id == 10">
                    <educational-background-section
                      ref="educationalSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Telephone -->
                  <div v-else-if="info.id == 11">
                    <div>
                      <label class="form-label" for="reg-email"
                        >{{ $t('labels.telephone') }}
                        <sup class="text-danger ms-1">*</sup></label
                      >
                      <div>
                        <input
                          id="edit_tel"
                          v-model="editRio.tel"
                          class="form-control"
                          :class="errors?.tel != null ? 'is-invalid' : ''"
                          type="tel"
                        />
                        <base-validation-error
                          class="d-block"
                          :errors="errors?.tel"
                        />
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

                  <!-- Qualifications and Skills -->
                  <div v-else-if="info.id == 12">
                    <!-- Qualification Section -->
                    <qualification-section
                      ref="qualificationSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />

                    <!-- Skill Section -->
                    <skill-section
                      ref="skillSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Award History -->
                  <div v-else-if="info.id == 13">
                    <award-section
                      ref="awardSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
                  </div>

                  <!-- Products -->
                  <div v-else-if="info.id == 14">
                    <product-section
                      ref="productSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                      @open-reference-url="handleOpenReferenceURL"
                    />
                  </div>

                  <!-- Business Use -->
                  <div v-else-if="info.id == 15">
                    <affiliate-section
                      ref="affiliateSectionRef"
                      :loading="loading"
                      @set-alert="setAlert"
                      @reset-alert="resetAlert"
                      @set-loading="setLoading"
                    />
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
import { RioProfileEditInfoList } from '../../../../utils/data';
import RioProfileApiService from '../../../../api/rio/profile';
import ProfessionSection from './profession/Section.vue';
import IndustrySection from './industry/Section.vue';
import EducationalBackgroundSection from './educational_background/Section.vue';
import ProfessionFormModal from './profession/FormModal.vue';
import DeleteProfessionModal from './profession/DeleteModal.vue';
import IndustryFormModal from './industry/FormModal.vue';
import DeleteIndustryModal from './industry/DeleteModal.vue';
import EducationalBackgroundFormModal from './educational_background/FormModal.vue';
import DeleteEducationalBackgroundModal from './educational_background/DeleteModal.vue';
import AwardSection from './award_history/Section.vue';
import AwardFormModal from './award_history/FormModal.vue';
import DeleteAwardModal from './award_history/DeleteModal.vue';
import QualificationSection from './qualification/Section.vue';
import QualificationFormModal from './qualification/FormModal.vue';
import DeleteQualificationModal from './qualification/DeleteModal.vue';
import SkillSection from './skill/Section.vue';
import SkillFormModal from './skill/FormModal.vue';
import DeleteSkillModal from './skill/DeleteModal.vue';
import ProductFormModal from './product/FormModal.vue';
import DeleteProductModal from './product/DeleteModal.vue';
import ProductSection from './product/Section.vue';
import AffiliateSection from './business_use/Section.vue';
import AffiliateFormModal from './business_use/FormModal.vue';
import ReferenceUrlModal from './product/ReferenceUrlModal.vue';
import FilePond from '../../../base/BaseFilePond.vue';
import DeleteProfileImageModal from './image/DeleteModal.vue';
import PrefectureTypes from '../../../../enums/PrefectureTypes';
import Accordions from '../../../../enums/RioAccordions';
import Common from '../../../../common';
import BpheroConfig from '../../../../config/bphero';
import DefaultImageCategory from '../../../../enums/DefaultImageCategory';

export default defineComponent({
  name: 'RioProfileEdit',
  props: {
    user: {
      type: [Array, Object],
      required: true,
    },
    rio: {
      type: [Array, Object],
      required: true,
    },
    rio_profile: {
      type: [Array, Object],
      required: true,
    },
    rio_expert: {
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
  },
  components: {
    ProductFormModal,
    ReferenceUrlModal,
    DeleteProductModal,
    BaseAccordion,
    BaseAlert,
    BaseButton,
    BaseValidationError,
    PageLoader,
    ProfessionSection,
    IndustrySection,
    EducationalBackgroundSection,
    ProfessionFormModal,
    DeleteProfessionModal,
    IndustryFormModal,
    DeleteIndustryModal,
    EducationalBackgroundFormModal,
    DeleteEducationalBackgroundModal,
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
    AffiliateSection,
    AffiliateFormModal,
    FilePond,
    DeleteProfileImageModal,
  },
  setup(props) {
    /**
     * Data properties
     */
    const rioProfileApiService = new RioProfileApiService();
    const initialRio = ref(props.rio);
    const initialRioProfile = ref(props.rio_profile);
    const loading = ref(false);
    const editUser = ref(props.user);
    const editRio = ref(props.rio);
    const editRioProfile = ref(props.rio_profile);
    const editId = ref(0);
    const infoList = ref(RioProfileEditInfoList);
    const pageLoading = ref(false);
    const errors = ref(null);
    const alert = ref({
      success: false,
      failed: false,
    });
    const referenceUrlModalRef = ref(null);
    const showEducModal = ref(false);
    const showAwardModal = ref(false);
    const isDisabledAddEducButton = ref(false);
    const professionSectionRef = ref(null);
    const industrySectionRef = ref(null);
    const educationalSectionRef = ref(null);
    const awardSectionRef = ref(null);
    const qualificationSectionRef = ref(null);
    const skillSectionRef = ref(null);
    const productSectionRef = ref(null);
    const affiliateSectionRef = ref(null);
    const intlTelInstance = ref(null);

    const filepondRef = ref(null);
    const deleteProfileModalRef = ref(null);
    const formRef = ref({});

    const hasProfileImageForEdit = ref(false);

    /**
     * Computed properties
     */
    const profileImage = computed(() => BpheroConfig.DEFAULT_IMG);
    const imageAltName = computed(
      () => `${editRio.value.first_name} ${editRio.value.family_name}`
    );

    /**
     *
     * Self Introduction variables
     */
    const maxCharacter = ref(500);
    const selfIntroductionOriginal = ref(editRioProfile.value.self_introduce);
    const totalCharacterOriginal = ref(0);
    const totalCharacter = ref(0);

    if (selfIntroductionOriginal.value != null) {
      totalCharacterOriginal.value = editRioProfile.value.self_introduce.length;
      totalCharacter.value = editRioProfile.value.self_introduce.length;
    }

    /**
     * Function to revert to original value (if not saved)
     */
    const revertSelfIntroduction = () => {
      errors.value = null;
      editRioProfile.value.self_introduce = selfIntroductionOriginal.value;
      totalCharacter.value = totalCharacterOriginal;
      document.getElementById('charCounter').style.color = '#737491';
    };

    /**
     * Toggle Home address nationality display handler
     *
     * @param {Number} value
     */
    const handleDisplayingHomeNationality = (value) => {
      const homeNationalitySelector = document.querySelector(
        '.home_address_nationality'
      );
      if (value === PrefectureTypes.OTHER) {
        homeNationalitySelector.classList.remove('d-none');
        editRioProfile.value.home_address_nationality =
          initialRioProfile.value.home_address_nationality;
      } else {
        homeNationalitySelector.classList.add('d-none');
        editRioProfile.value.home_address_nationality = '';
      }
    };

    /**
     * Cancel and revert rio_profile edit changes
     */
    const cancelRioProfileDataEdit = () => {
      editRioProfile.value = { ...initialRioProfile.value };
    };

    /**
     * Cancel and revert rio edit changes
     */
    const cancelRioDataEdit = () => {
      editRio.value = { ...initialRio.value };
    };

    const updateEditId = (id) => {
      // Reset errors
      errors.value = null;

      switch (id) {
        case Accordions.BIRTHDAY:
        case Accordions.GENDER:
        case Accordions.NAME:
        case Accordions.TELEPHONE:
          cancelRioDataEdit();
          break;
        case Accordions.PRESENT_ADDRESS:
        case Accordions.HOME_ADDRESS:
          cancelRioProfileDataEdit();
          break;
        case Accordions.SELF_INTRODUCTION:
          revertSelfIntroduction();
          break;
        default:
          break;
      }

      editId.value = id;
    };

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
     * Open product reference URL
     *
     * @returns {void}
     */
    const handleOpenReferenceURL = (referenceUrl) => {
      referenceUrlModalRef.value.referenceUrl = referenceUrl;
      referenceUrlModalRef.value.show();
    };

    /**
     * Update RIO profile image
     *
     * @returns {void}
     */
    const handleSaveProfileImage = async () => {
      const files = filepondRef.value.pond.getFiles();

      if (files.length > 0) {
        const data = {
          id: editRio.value.id,
          profile_image: files[0].file,
        };

        // Reset alert & begin loading
        resetAlert();
        setPageLoading(true);
        setLoading(true);

        await rioProfileApiService
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
              initialRioProfile.value.profile_photo = `${
                response.data.data.profile_image
              }?${new Date().getTime()}`;
              initialRioProfile.value.profile_image = `${
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
     * Remove RIO profile image
     *
     * @returns {void}
     */
    const removeProfileImage = async () => {
      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      deleteProfileModalRef.value.setLoading(true);

      await rioProfileApiService
        .deleteProfileImage(editRio.value.id)
        .then((response) => {
          if (response.data.status_code === 200) {
            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');

            // Remove files in filepond instance
            filepondRef.value.pond.removeFiles();
            hasProfileImageForEdit.value = false;

            // Set new data
            initialRioProfile.value.profile_image = `${
              response.data.data.profile_image
            }?${new Date().getTime()}`;
            initialRioProfile.value.profile_photo = null;
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
     * Template reference call for get professions
     *
     * @returns {void}
     */
    const getProfessions = () => {
      professionSectionRef.value.getProfessions();
    };

    /**
     * Template reference call for get products
     *
     * @param {object} data
     * @returns {void}
     */
    const getProducts = () => {
      productSectionRef.value.getProducts();
    };

    /**
     * Template reference call for get industries
     */
    const getIndustries = () => {
      industrySectionRef.value.getIndustries();
    };

    /**
     * Template reference call for get educational backgrounds
     *
     * @returns {void}
     */
    const getEducationalBackgrounds = () => {
      educationalSectionRef.value.getEducationalBackgrounds();
    };

    /**
     * Template reference call for get awards
     *
     * @param {object} data
     * @returns {void}
     */
    const getAwards = () => {
      awardSectionRef.value.getAwards();
    };

    /**
     * Template reference call for get qualifications
     *
     * @returns {void}
     */
    const getQualifications = () => {
      qualificationSectionRef.value.getQualifications();
    };

    /**
     * Template reference call for get skills
     *
     * @returns {void}
     */
    const getSkills = () => {
      skillSectionRef.value.getSkills();
    };

    /**
     * Template reference call for get affiliates
     *
     * @param {object} data
     * @returns {void}
     */
    const getAffiliates = () => {
      affiliateSectionRef.value.getAffiliates();
    };

    /**
     * Toggle present address country display handler
     *
     * @param {Number} value
     */
    const handleDisplayingPresentCountry = (value) => {
      const presentCountry = document.querySelector('.present-country');

      if (value === PrefectureTypes.OTHER) {
        // Show country if selected value is "日本以外"
        presentCountry.classList.remove('d-none');
      } else {
        // Hide country if selected value is not "日本以外"
        presentCountry.classList.add('d-none');
      }
    };

    /**
     * Update RIO profile gender
     */
    const handleSaveGender = async () => {
      const data = {
        gender: editRio.value.gender,
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updateGender(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');

            // Set new data
            initialRio.value.gender = editRio.value.gender;
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
        });
    };

    /**
     * Update RIO profile birthdate
     */
    const handleSaveBirthdate = async () => {
      const data = {
        birth_date: editRio.value.birth_date,
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updateBirthdate(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');

            // Set new data
            initialRio.value.birth_date = editRio.value.birth_date;
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
        });
    };

    /**
     * Update RIO profile present address
     */
    const handleSavePresentAddress = async () => {
      const data = {
        present_address_prefecture:
          editRioProfile.value.present_address_prefecture,
        present_address_nationality:
          editRioProfile.value.present_address_nationality,
        present_address_city: editRioProfile.value.present_address_city,
        present_address: editRioProfile.value.present_address,
        present_address_building: editRioProfile.value.present_address_building,
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updatePresentAddress(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            const responseData = response.data.data;

            // Set new data
            initialRioProfile.value = Object.assign(
              initialRioProfile.value,
              responseData
            );
            editRioProfile.value = Object.assign(
              editRioProfile.value,
              responseData
            );

            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');
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
        });
    };

    /**
     * Update RIO profile self-introduction
     */
    const handleSaveSelfIntroduction = async () => {
      const data = {
        self_introduce: editRioProfile.value.self_introduce,
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updateSelfIntroduction(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            errors.value = null;
            setAlert('success');

            // Set new data
            selfIntroductionOriginal.value =
              editRioProfile.value.self_introduce;
            totalCharacterOriginal.value =
              editRioProfile.value.self_introduce != null
                ? editRioProfile.value.self_introduce.length
                : 0;
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
        });
    };

    /**
     *
     * Self-Introduction character counter
     */
    const charCount = () => {
      totalCharacter.value = editRioProfile.value.self_introduce.length;
      document.getElementById('charCounter').style.color = '#737491';

      if (totalCharacter.value > maxCharacter.value) {
        document.getElementById('charCounter').style.color = '#f74f78';
      }
    };

    /**
     * Update RIO profile telephone
     */
    const handleSaveTelephone = async () => {
      const data = {
        tel: intlTelInstance.value.getNumber(),
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updateTelephone(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            const responseData = response.data.data;

            // Set new data
            initialRio.value = Object.assign(initialRio.value, responseData);
            editRio.value = Object.assign(editRio.value, responseData);

            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');
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
        });
    };

    /**
     * Update RIO profile home address
     */
    const handleSaveHomeAddress = async () => {
      const data = {
        home_address_nationality: editRioProfile.value.home_address_nationality,
        home_address_prefecture: editRioProfile.value.home_address_prefecture,
        home_address_city: editRioProfile.value.home_address_city,
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updateHomeAddress(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            const responseData = response.data.data;

            // Set new data
            initialRioProfile.value = Object.assign(
              initialRioProfile.value,
              responseData
            );
            editRioProfile.value = Object.assign(
              editRioProfile.value,
              responseData
            );

            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');
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
        });
    };

    /**
     * Update RIO profile name
     */
    const handleSaveName = async () => {
      const data = {
        first_name: editRio.value.first_name,
        family_name: editRio.value.family_name,
        first_kana: editRio.value.first_kana,
        family_kana: editRio.value.family_kana,
      };

      // Reset alert & begin loading
      resetAlert();
      setPageLoading(true);
      setLoading(true);

      await rioProfileApiService
        .updateName(data)
        .then((response) => {
          if (response.data.status_code === 200) {
            // Set new data
            initialRio.value.first_name = editRio.value.first_name;
            initialRio.value.family_name = editRio.value.family_name;
            initialRio.value.first_kana = editRio.value.first_kana;
            initialRio.value.family_kana = editRio.value.family_kana;

            // Remove errors & show success alert
            errors.value = null;
            setAlert('success');
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
        });
    };

    /**
     * Intialize intl-tel-input to phone field
     */
    const initializeIntlPhoneField = () => {
      const telInput = document.querySelector('#edit_tel');
      intlTelInstance.value = window.intlTelInput(telInput, {
        initialCountry: 'jp',
        utilsScript: '/js/intl-tel-input/utils.min.js',
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
      // Present country display handler
      handleDisplayingPresentCountry(
        parseInt(editRioProfile.value.present_address_prefecture, 10)
      );

      // Home country display handler
      handleDisplayingHomeNationality(
        parseInt(editRioProfile.value.home_address_prefecture, 10)
      );

      initializeIntlPhoneField();
      uploadProfileImageListener();
    });

    /**
     * Watch on Present address prefecture change
     */
    watch(
      () => editRioProfile.value.present_address_prefecture,
      (newValue) => {
        handleDisplayingPresentCountry(parseInt(newValue, 10));
      }
    );

    /**
     * Watch on Home Address prefecture address change
     */
    watch(
      () => editRioProfile.value.home_address_prefecture,
      (newValue) => {
        handleDisplayingHomeNationality(parseInt(newValue, 10));
      }
    );

    return {
      initialRioProfile,
      loading,
      alert,
      editUser,
      editRio,
      editRioProfile,
      editId,
      infoList,
      pageLoading,
      errors,
      referenceUrlModalRef,
      filepondRef,
      formRef,
      profileImage,
      imageAltName,
      updateEditId,
      handleSaveGender,
      showEducModal,
      isDisabledAddEducButton,
      setLoading,
      setPageLoading,
      setAlert,
      resetAlert,
      showAwardModal,
      deleteProfileModalRef,
      handleSaveProfileImage,
      openDeleteProfileImageModal,
      removeProfileImage,
      handleSavePresentAddress,
      handleDisplayingPresentCountry,
      cancelRioProfileDataEdit,
      handleSaveBirthdate,
      handleSaveName,
      handleSaveHomeAddress,
      handleDisplayingHomeNationality,
      professionSectionRef,
      getProfessions,
      industrySectionRef,
      getIndustries,
      educationalSectionRef,
      getEducationalBackgrounds,
      qualificationSectionRef,
      getQualifications,
      skillSectionRef,
      getSkills,
      handleSaveSelfIntroduction,
      totalCharacter,
      charCount,
      revertSelfIntroduction,
      maxCharacter,
      handleSaveTelephone,
      awardSectionRef,
      getAwards,
      productSectionRef,
      getProducts,
      handleOpenReferenceURL,
      affiliateSectionRef,
      getAffiliates,
      hasProfileImageForEdit,
      Common,
      DefaultImageCategory,
    };
  },
});
</script>
