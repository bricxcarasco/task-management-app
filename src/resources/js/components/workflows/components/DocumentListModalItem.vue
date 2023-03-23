<template>
  <div>
    <div v-if="category == 'folder'">
      <li
        class="list-group-item px-0 py-2 position-relative list--white px-2"
        @click.prevent="handleNextFolderContent(document)"
      >
        <i class="h2 m-0 ai-folder"></i>
        <span class="fs-xs c-primary ms-2" for="file">
          {{ document.name }}
        </span>
      </li>
    </div>
    <div v-if="category == 'file'">
      <li
        class="list-group-item px-0 py-2 position-relative list--white px-2"
        @click.prevent="handleSelectedFile(document)"
      >
        <i
          class="h2 m-0 ai-file-text"
          v-if="document.file_type == 'application/pdf'"
        ></i>
        <i class="h2 m-0 ai-image" v-else></i>

        <span class="fs-xs c-primary ms-2">
          {{ document.name }}
        </span>
      </li>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DocumentListModalItem',
  props: {
    document: {
      type: [Object, null],
      required: true,
    },
    category: {
      type: [String, null],
      required: true,
    },
  },
  emits: ['open-folder', 'select-file'],
  setup(props, { emit }) {
    /**
     * Select a file
     *
     * @param {object} document
     * @returns {void}
     */
    const handleSelectedFile = (document) => {
      emit('select-file', document);
    };

    /**
     * Access a folder
     *
     * @param {object} document
     * @returns {void}
     */
    const handleNextFolderContent = (document) => {
      emit('open-folder', document);
    };

    return {
      handleSelectedFile,
      handleNextFolderContent,
    };
  },
};
</script>
