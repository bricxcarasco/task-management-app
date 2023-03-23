<template>
  <div>
    <!-- Alert message -->
    <base-alert
      :success="alert.success"
      :danger="alert.failed"
      :message="alert.message"
      @closeAlert="resetAlert"
    />

    <div
      class="
        container
        position-relative
        zindex-5
        pt-6
        py-md-6
        mb-md-3
        home--height
      "
    >
      <div class="row">
        <!-- Page loader -->
        <page-loader :show="pageLoading" />

        <div class="col-12 col-md-9 offset-md-3">
          <div class="d-flex flex-column pb-4 pb-md-0 rounded-3">
            <!-- Header and tabs section -->
            <header-tab-section :service="service" :activeTab="'message'" />

            <ul
              class="
                nav nav-tabs
                d-flex
                align-items-center
                justify-content-between
              "
              role="tablist"
            >
              <!-- Received Inquiries -->
              <li
                class="nav-item m-0 flex-1"
                @click.prevent="
                  selectTransmitType(ClassifiedMessageSenderEnum.BUYER)
                "
              >
                <a
                  href="#"
                  class="nav-link px-md-2 px-0 text-center"
                  :class="
                    formData.transmit_type === ClassifiedMessageSenderEnum.BUYER
                      ? 'active'
                      : ''
                  "
                >
                  {{ $t('labels.received_inquiries') }}
                </a>
              </li>
              <!-- Sent Inquiries -->
              <li
                class="nav-item m-0 flex-1"
                @click.prevent="
                  selectTransmitType(ClassifiedMessageSenderEnum.SELLER)
                "
              >
                <a
                  href="#"
                  class="nav-link px-md-2 px-0 text-center"
                  :class="
                    formData.transmit_type ===
                    ClassifiedMessageSenderEnum.SELLER
                      ? 'active'
                      : ''
                  "
                >
                  {{ $t('labels.sent_inquiries') }}
                </a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active show">
                <div class="card p-0 shadow mt-2">
                  <div class="product__wrapper">
                    <!-- Inquiries Datatable -->
                    <ul
                      v-if="inquiries.length > 0"
                      class="
                        product__lists product__inquiry
                        list-group list-group-flush
                      "
                    >
                      <li
                        v-for="(inquiry, index) in inquiries"
                        :key="index"
                        class="
                          list-group-item
                          position-relative
                          list--white
                          p-0
                        "
                        @click="redirectToViewPage($event, inquiry.id)"
                      >
                        <a href="#" class="d-block p-2 c-primary">
                          <div
                            class="
                              d-flex
                              align-items-center
                              justify-content-between
                            "
                          >
                            <span>{{ inquiry.product_title }}</span>
                            <div>
                              <span class="me-2">{{
                                inquiry.last_message_date
                              }}</span>
                            </div>
                          </div>
                          <div
                            class="
                              d-flex
                              align-items-center
                              justify-content-center
                              mt-2
                            "
                          >
                            <div
                              style="
                                height: 50px;
                                width: 50px;
                                background-size: cover;
                                background-position: center;
                              "
                              class="bg-secondary me-2 rounded-circle"
                            >
                              <img
                                class="cover rounded-circle"
                                :src="inquiry.display_photo"
                                @error="
                                  Common.handleNotFoundImageException(
                                    $event,
                                    DefaultImageCategory.RIO_NEO
                                  )
                                "
                              />
                            </div>
                            <div class="flex-1">
                              <div
                                class="
                                  d-flex
                                  align-items-center
                                  justify-content-between
                                "
                              >
                                <div class="d-flex align-items-center">
                                  <span
                                    class="
                                      d-block
                                      ellipsis--contacts
                                      text-start
                                    "
                                  >
                                    {{ inquiry.display_name }}
                                  </span>
                                  <i
                                    v-if="inquiry.is_connected"
                                    class="ai-handshake-o ms-2"
                                  ></i>
                                </div>
                                <button type="button" class="btn fs-xs p-1">
                                  <i class="ai-arrow-right me-1"></i>
                                </button>
                              </div>
                              <span class="c-dark-gray fs-xs">{{
                                inquiry.last_message
                              }}</span>
                            </div>
                          </div>
                        </a>
                      </li>
                    </ul>
                    <div v-else class="text-center">
                      <!-- No messages -->
                      <p class="my-4">
                        {{ $t('paragraphs.no_messages') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pagination -->
              <div class="d-flex justify-content-center mt-2 mb-3">
                <pagination :meta="paginationData" @changePage="changePage" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import BaseAlert from '../../base/BaseAlert.vue';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import Pagination from '../../base/BasePagination.vue';
import HeaderTabSection from '../components/HeaderTabsSection.vue';
import ClassifiedMessageSenderEnum from '../../../enums/ClassifiedMessageSender';
import ClassifiedContactMessagesApiService from '../../../api/classifieds/contact-messages';

export default defineComponent({
  name: 'ContactMessagesListIndex',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    PageLoader,
    BaseAlert,
    Pagination,
    HeaderTabSection,
  },
  setup() {
    /**
     * Data properties
     */
    const apiService = new ClassifiedContactMessagesApiService();
    const inquiries = ref([]);
    const paginationData = ref([]);
    const pageLoading = ref(false);
    const alert = ref({
      success: false,
      failed: false,
    });
    const formData = ref({
      page: 1,
      transmit_type: ClassifiedMessageSenderEnum.BUYER,
    });

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
     * Set alert messages
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
     * Reset alert messages
     *
     * @returns {void}
     */
    const resetAlert = () => {
      alert.value.success = false;
      alert.value.failed = false;
    };

    /**
     * Get list of products
     *
     * @returns {void}
     */
    const getInquiries = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        // Fetch product list
        const getListApi = await apiService.getLists(formData.value);
        const getListResponseData = getListApi.data;

        inquiries.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
      } catch (error) {
        setAlert('failed');
        inquiries.value = [];

        throw error;
      } finally {
        // End page loading
        setPageLoading(false);
      }
    };

    /**
     * Change page
     *
     * @param {Integer} page
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getInquiries();
    };

    /**
     * Change page
     *
     * @param {Integer} page
     * @returns {void}
     */
    const selectTransmitType = (type) => {
      formData.value.transmit_type = type;
    };

    /**
     * Redirect to view page
     *
     * @param {integer} id
     * @returns {void}
     */
    const redirectToViewPage = (event, id) => {
      event.preventDefault();
      event.stopPropagation();

      window.location.href = `/classifieds/messages/${id}`;
    };

    /**
     * Watch for filtered accessibility
     */
    watch(
      () => [formData.value.transmit_type],
      () => {
        formData.value.page = 1;
        getInquiries();
      }
    );

    /**
     * On mounted properties
     */
    onMounted(() => {
      getInquiries();
    });

    return {
      formData,
      inquiries,
      pageLoading,
      alert,
      setAlert,
      resetAlert,
      paginationData,
      changePage,
      redirectToViewPage,
      selectTransmitType,
      ClassifiedMessageSenderEnum,
      Common,
      DefaultImageCategory,
    };
  },
});
</script>
