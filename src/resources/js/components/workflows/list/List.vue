<template>
  <div class="tab-content">
    <!-- Page loader -->
    <page-loader :show="pageLoading" />
    <!-- WorkFlow Created Tab -->
    <div class="tab-pane fade show active" id="workFlowCreated" role="tabpanel">
      <!-- Status Types -->
      <select
        class="form-select form-select-sm d-inline-block"
        style="width: auto; margin-right: 5px"
        v-model="formData.status_type"
      >
        <option :value="WorkflowStatustTypes.ALL">
          {{ $t('labels.all') }}
        </option>
        <option :value="WorkflowStatustTypes.APPLYING">
          {{ $t('labels.applying') }}
        </option>
        <option :value="WorkflowStatustTypes.REMANDED">
          {{ $t('labels.remanded') }}
        </option>
        <option :value="WorkflowStatustTypes.APPROVED">
          {{ $t('labels.approved') }}
        </option>
        <option :value="WorkflowStatustTypes.REJECTED">
          {{ $t('labels.rejected') }}
        </option>
        <option :value="WorkflowStatustTypes.CANCELLED">
          {{ $t('labels.cancelled') }}
        </option>
      </select>
      <!-- Sort List -->
      <select
        class="form-select form-select-sm d-inline-block w-15"
        style="width: auto"
        v-model="formData.sort_by"
      >
        <option :value="WorkflowSortTypes.newest_application_date">
          {{ $t('labels.newest_application_date') }}
        </option>
        <option :value="WorkflowSortTypes.oldest_application_date">
          {{ $t('labels.oldest_application_date') }}
        </option>
      </select>
      <!-- WorkFlow Created List -->
      <div class="card p-2 shadow mt-2 tab-content">
        <ul class="list-group list-group-flush mt-2">
          <!-- No result found -->
          <h6 v-if="createdWorkflowList == ''" style="text-align: center">
            {{ $t('labels.no_result_found') }}
          </h6>
          <registered-item
            v-for="(list, index) in createdWorkflowList"
            :list="list"
            :key="index"
          />
        </ul>
      </div>
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-2 mb-3">
        <pagination :meta="paginationData" @changePage="changePage" />
      </div>
    </div>

    <!-- WorkFlow For You Tab -->
    <div
      class="connection__wrapper tab-pane fade"
      id="workFlowforYou"
      role="tabpanel"
    >
      <!-- Approver Status Types -->
      <select
        class="form-select form-select-sm d-inline-block"
        style="width: auto; margin-right: 5px"
        v-model="formDataForYou.approver_status_type"
      >
        <option :value="WorkflowApproverStatusTypes.ALL">
          {{ $t('labels.all') }}
        </option>
        <option :value="WorkflowApproverStatusTypes.PENDING">
          {{ $t('labels.pending') }}
        </option>
        <option :value="WorkflowApproverStatusTypes.DONE">
          {{ $t('labels.done') }}
        </option>
        <option :value="WorkflowApproverStatusTypes.COMPLETED">
          {{ $t('labels.completed') }}
        </option>
        <option :value="WorkflowApproverStatusTypes.REJECTED">
          {{ $t('labels.rejected') }}
        </option>
        <option :value="WorkflowApproverStatusTypes.CANCELLED">
          {{ $t('labels.cancelled') }}
        </option>
      </select>
      <!-- Sort List -->
      <select
        class="form-select form-select-sm d-inline-block w-15"
        style="width: auto"
        v-model="formDataForYou.sort_by"
      >
        <option :value="WorkflowSortTypes.newest_application_date">
          {{ $t('labels.newest_application_date') }}
        </option>
        <option :value="WorkflowSortTypes.oldest_application_date">
          {{ $t('labels.oldest_application_date') }}
        </option>
      </select>
      <!-- WorkFlow For You List -->
      <div class="card p-2 shadow mt-2 tab-content">
        <ul class="list-group list-group-flush mt-2">
          <!-- No result found -->
          <h6 v-if="workflowForYouList == ''" style="text-align: center">
            {{ $t('labels.no_result_found') }}
          </h6>
          <action-item
            v-for="(list, index) in workflowForYouList"
            :list="list"
            :key="index"
          />
        </ul>
      </div>
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-2 mb-3">
        <pagination
          :meta="forYouPaginationData"
          @changePage="forYouChangePage"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, ref, watch, onMounted } from 'vue';
import PageLoader from '../../base/BaseSectionLoader.vue';
import RegisteredItem from './RegisteredItem.vue';
import ActionItem from './ActionItem.vue';
import WorkflowApiService from '../../../api/workflows/workflow';
import WorkflowSortTypes from '../../../enums/WorkflowSortTypes';
import WorkflowStatustTypes from '../../../enums/WorkflowStatusTypes';
import Pagination from '../../base/BasePagination.vue';
import WorkflowApproverStatusTypes from '../../../enums/WorkflowApproverStatusTypes';

export default defineComponent({
  name: 'WorkflowList',
  components: {
    RegisteredItem,
    ActionItem,
    PageLoader,
    Pagination,
  },
  // Emit badge count
  emits: ['badgeCount', 'pendingCount'],
  setup(props, { emit }) {
    const workflowApiService = new WorkflowApiService();
    const formData = ref({
      // Created worflow tab
      status_type: 0,
      sort_by: 1,
    });
    const formDataForYou = ref({
      // Workflow for you tab
      approver_status_type: 0,
      sort_by: 1,
    });
    const createdWorkflowList = ref([]);
    const workflowForYouList = ref([]);
    const paginationData = ref([]);
    const forYouPaginationData = ref([]);
    const pageLoading = ref(false);
    const countReturned = ref();

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
     * Set page loading for workflow for you
     *
     * @param {bool} state
     * @returns {void}
     */
    const setForYouPageLoading = (state) => {
      pageLoading.value = state;
    };

    /**
     * Get list of Created workflows
     *
     * @returns {void}
     */
    const getCreatedWorkflows = async () => {
      // Start page loading
      setPageLoading(true);

      try {
        const getCreatedWorflowListApi = await workflowApiService.getLists(
          formData.value
        );
        const getListResponseData = getCreatedWorflowListApi.data;
        createdWorkflowList.value = getListResponseData?.data || [];
        paginationData.value = getListResponseData?.meta || [];
        countReturned.value = getListResponseData?.count;
        emit('badgeCount', countReturned.value);
      } catch (error) {
        createdWorkflowList.value = [];
        throw error;
      } finally {
        setPageLoading(false);
      }
    };

    /**
     * Get list of workflow for you
     *
     * @returns {void}
     */
    const getWorkflowForYou = async () => {
      // Start page loading
      setForYouPageLoading(true);

      try {
        const getWorkflowForYouListApi =
          await workflowApiService.getWorkflowForYouLists(formDataForYou.value);
        const getWorkflowForYouListResponseData = getWorkflowForYouListApi.data;
        workflowForYouList.value =
          getWorkflowForYouListResponseData?.data || [];
        forYouPaginationData.value =
          getWorkflowForYouListResponseData?.meta || [];
        emit('pendingCount', getWorkflowForYouListResponseData?.data);
      } catch (error) {
        workflowForYouList.value = [];
        throw error;
      } finally {
        setForYouPageLoading(false);
      }
    };

    /**
     * Change page for created workflow
     *
     * @returns {void}
     */
    const changePage = (page) => {
      formData.value.page = page;
      getCreatedWorkflows();
    };

    /**
     * Change page for workflow for you
     *
     * @returns {void}
     */
    const forYouChangePage = (page) => {
      formDataForYou.value.page = page;
      getWorkflowForYou();
    };

    /**
     * Watch for created worfklow tab changes
     */
    watch(
      () => [formData.value.sort_by, formData.value.status_type],
      () => {
        getCreatedWorkflows();
      }
    );

    /**
     * Watch for workflow for you tab changes
     */
    watch(
      () => [
        formDataForYou.value.sort_by,
        formDataForYou.value.approver_status_type,
      ],
      () => {
        getWorkflowForYou();
      }
    );

    /**
     * On mounted properties
     */
    onMounted(() => {
      getCreatedWorkflows();
      getWorkflowForYou();
    });

    return {
      formData,
      getCreatedWorkflows,
      createdWorkflowList,
      getWorkflowForYou,
      WorkflowSortTypes,
      WorkflowStatustTypes,
      pageLoading,
      setPageLoading,
      paginationData,
      changePage,
      WorkflowApproverStatusTypes,
      workflowForYouList,
      formDataForYou,
      setForYouPageLoading,
      forYouChangePage,
      forYouPaginationData,
    };
  },
});
</script>
