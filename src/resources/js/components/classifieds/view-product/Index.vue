<template>
  <div>
    <!-- Send inquiry modal -->
    <send-inquiry-modal
      :product="product"
      @update-button="updateInquiryButton"
    />

    <!-- Inquiry sent modal -->
    <inquiry-sent-modal />

    <!-- Transaction flow modal -->
    <transaction-flow-modal />

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
            <header-tab-section :service="service" :activeTab="'search'" />

            <div>
              <button class="btn btn-link" @click="redirectToListPage">
                <i class="ai-arrow-left me-2"></i>
                {{ $t('buttons.back_to_list') }}
              </button>
            </div>
            <div class="tab-content">
              <div class="tab-pane fade active show">
                <div class="row">
                  <div class="col-md-6 offset-md-3">
                    <div
                      class="
                        tns-carousel-wrapper
                        tns-nav-inside
                        tns-nav-light
                        tns-controls-inside
                        tns-controls-onhover
                      "
                    >
                      <div class="carousel-slider tns-carousel-inner">
                        <div
                          v-for="(url, index) in product.image_urls"
                          :key="index"
                        >
                          <img
                            class="rounded-3 d-block mx-auto"
                            :src="url"
                            alt="Image"
                            @error="
                              Common.handleNotFoundImageException(
                                $event,
                                DefaultImageCategory.CLASSIFIED
                              )
                            "
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card p-0 shadow mt-2">
                  <div class="product__wrapper">
                    <ul class="product__lists list-group list-group-flush">
                      <li
                        class="
                          list-group-item
                          position-relative
                          list--white
                          p-2
                        "
                      >
                        <span
                          class="
                            product__exhibitionStatus
                            c-white
                            fs-xs
                            p-1
                            ms-2
                            mt-2
                          "
                          :class="
                            product.is_accept ? 'bg-blue' : 'bg-dark-gray'
                          "
                        >
                          {{ product.product_accessibility }}
                        </span>
                        <div class="position-relative text-end">
                          <!-- Favorite button -->
                          <favorite-button
                            :product="product"
                            :service="service"
                            @page-loading="setPageLoading"
                          />
                        </div>
                        <div
                          class="
                            d-flex
                            align-items-center
                            justify-content-center
                            mt-2
                          "
                        >
                          <div class="flex-1">
                            <span class="d-block text-start">
                              {{ product.title }}
                            </span>
                            <span
                              v-if="product.price_str !== null"
                              class="text-danger"
                            >
                              {{
                                $t('labels.price', { price: product.price_str })
                              }}
                            </span>
                            <span v-else class="text-danger">
                              {{ $t('labels.individual_quote') }}
                            </span>
                          </div>
                        </div>
                        <p class="mt-3 mb-0">
                          ■ {{ $t('labels.product_service_description') }}
                        </p>
                        <p class="p-2 bg-gray mt-2">
                          {{ product.detail }}
                        </p>
                        <!-- <p class="mt-3 mb-0">■ プロモーション動画</p>
                        <div class="p-2 bg-gray mt-2"></div> -->
                        <p class="mt-3 mb-0">
                          ■ {{ $t('labels.product_information') }}
                        </p>
                        <div class="table-responsive mt-2">
                          <table class="table table-bordered">
                            <tr>
                              <th scope="row" class="bg-gray">
                                {{ $t('labels.category') }}
                              </th>
                              <td>{{ product.sale_category_name }}</td>
                            </tr>
                            <tr>
                              <th scope="row" class="bg-gray">
                                {{ $t('labels.contributor') }}
                              </th>
                              <td v-if="sellerProfileLink !== null">
                                <a class="p-0" :href="sellerProfileLink">
                                  {{ product.selling_name }}
                                </a>
                              </td>
                              <td v-else>
                                {{ product.selling_name }}
                              </td>
                            </tr>
                          </table>
                        </div>
                        <div class="text-end">
                          <button
                            type="button"
                            class="btn btn-link"
                            data-bs-toggle="modal"
                            data-bs-target="#transaction-flow"
                          >
                            {{ $t('buttons.to_receive_product') }}
                          </button>
                        </div>
                        <div v-if="!product.is_owned" class="text-center">
                          <button
                            v-if="hasInquiry"
                            class="btn btn-primary w-md-auto w-xs-100 mt-4"
                            disabled
                          >
                            {{ $t('buttons.inquiry_sent') }}
                          </button>
                          <button
                            v-else
                            class="btn btn-primary w-md-auto w-xs-100 mt-4"
                            data-bs-toggle="modal"
                            data-bs-target="#send-inquiry-modal"
                          >
                            {{ $t('buttons.contact_seller') }}
                          </button>
                        </div>
                      </li>
                    </ul>
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
import { defineComponent, ref, onMounted, computed } from 'vue';
import { tns } from 'tiny-slider';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import PageLoader from '../../base/BaseSectionLoader.vue';
import HeaderTabSection from '../components/HeaderTabsSection.vue';
import FavoriteButton from '../components/FavoriteButton.vue';
import SendInquiryModal from '../components/SendInquiryModal.vue';
import TransactionFlowModal from '../components/TransactionFlowModal.vue';
import InquirySentModal from '../components/InquirySentModal.vue';

export default defineComponent({
  name: 'ViewProductsPage',
  props: {
    rio: {
      type: [Array, Object],
      required: true,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
    product: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    PageLoader,
    HeaderTabSection,
    FavoriteButton,
    SendInquiryModal,
    TransactionFlowModal,
    InquirySentModal,
  },
  setup(props) {
    /**
     * Data properties
     */
    const product = ref(props.product);
    const pageLoading = ref(false);
    const hasInquiry = ref(product.value.has_inquiry);
    const sellerProfileLink = computed(() => {
      switch (product.value.selling_type) {
        case 'RIO':
          return `/rio/profile/introduction/${product.value.selling_id}`;
        case 'NEO':
          return `/neo/profile/introduction/${product.value.selling_id}`;
        default:
          return null;
      }
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
     * Redirect to all products list page
     *
     * @returns {void}
     */
    const redirectToListPage = (event) => {
      event.preventDefault();

      window.location.href = '/classifieds';
    };

    /**
     * Update send inquiry button
     *
     * @returns {void}
     */
    const updateInquiryButton = () => {
      hasInquiry.value = true;
    };

    /**
     * On mounted function
     */
    onMounted(() => {
      // Initialize slider
      tns({
        container: '.carousel-slider',
        gutter: 15,
        swipeAngle: false,
        speed: 400,
        arrowKeys: true,
        controlsText: [
          '<i class="ai-arrow-left"></i>',
          '<i class="ai-arrow-right"></i>',
        ],
      });
    });

    return {
      hasInquiry,
      pageLoading,
      setPageLoading,
      redirectToListPage,
      sellerProfileLink,
      updateInquiryButton,
      Common,
      DefaultImageCategory,
    };
  },
});
</script>
