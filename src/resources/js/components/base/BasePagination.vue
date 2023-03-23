<template>
  <nav>
    <ul class="pagination" v-if="shouldDisplay()">
      <!-- Previous Arrow -->
      <li class="page-item" :class="onFirstPage() ? 'disabled' : ''">
        <span class="page-link" v-if="onFirstPage()">‹</span>
        <a
          v-else
          class="page-link"
          @click.stop="changePage(meta.current_page - 1)"
          href="javascript:void(0)"
          >‹</a
        >
      </li>

      <!-- Page Numbers -->
      <li
        class="page-item"
        :class="meta.current_page === page ? 'active' : ''"
        v-for="(page, index) in getNumberPages()"
        :key="index"
      >
        <span class="page-link" v-if="meta.current_page === page">{{
          page
        }}</span>
        <a
          v-else
          class="page-link"
          @click.stop="changePage(page)"
          href="javascript:void(0)"
          >{{ page }}</a
        >
      </li>

      <!-- Next Page -->
      <li class="page-item" :class="onLastPage() ? 'disabled' : ''">
        <span class="page-link" v-if="onLastPage()">›</span>
        <a
          v-else
          class="page-link"
          @click.stop="changePage(meta.current_page + 1)"
          href="javascript:void(0)"
          >›</a
        >
      </li>
    </ul>
  </nav>
</template>

<script>
import BpheroConfig from '../../config/bphero';

export default {
  props: {
    meta: {
      type: [Array, Object],
      required: true,
    },
  },
  setup(props, { emit }) {
    const offset = BpheroConfig.PAGINATION_OFFSET_RANGE;

    /**
     * Get generated page numbers to be displayed
     *
     * @returns {array}
     */
    const getNumberPages = () => {
      let start = props.meta.current_page - offset;
      start = start >= 1 ? start : 1;
      let end = start + offset * 2;
      end = end >= props.meta.last_page ? props.meta.last_page : end;

      const pagesArray = [];
      for (let page = start; page <= end; page += 1) {
        pagesArray.push(page);
      }

      return pagesArray;
    };

    /**
     * Emit change page hook
     *
     * @returns {void}
     */
    const changePage = (page) => {
      emit('change-page', page);
    };

    /**
     * Check if page is current page number
     *
     * @returns {boolean}
     */
    const onCurrentPage = (page) => props.meta.current_page === page;

    /**
     * Check if page is first page
     *
     * @returns {boolean}
     */
    const onFirstPage = () => props.meta.current_page <= 1;

    /**
     * Check if page is last page
     *
     * @returns {boolean}
     */
    const onLastPage = () => props.meta.current_page >= props.meta.last_page;

    /**
     * Check if pagination should be displayed
     *
     * @returns {boolean}
     */
    const shouldDisplay = () => props.meta.total && props.meta.last_page > 1;

    return {
      changePage,
      getNumberPages,
      onCurrentPage,
      onFirstPage,
      onLastPage,
      shouldDisplay,
    };
  },
};
</script>
