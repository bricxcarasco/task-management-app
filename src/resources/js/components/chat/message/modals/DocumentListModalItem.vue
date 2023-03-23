<template>
  <div>
    <div v-if="category == 'folder'">
      <li
        class="list-group-item px-0 py-2 position-relative list--white px-2"
        v-if="
          (talk_subject.type === ServiceSelectionTypesEnum.RIO &&
            talk_subject.id === document.owner_rio_id) ||
          (talk_subject.type === ServiceSelectionTypesEnum.NEO &&
            talk_subject.id === document.owner_neo_id)
        "
        @click.prevent="handleNextFolderContent(document.document_id)"
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
        v-if="
          (talk_subject.type === ServiceSelectionTypesEnum.RIO &&
            talk_subject.id === document.owner_rio_id) ||
          (talk_subject.type === ServiceSelectionTypesEnum.NEO &&
            talk_subject.id === document.owner_neo_id)
        "
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
import ServiceSelectionTypesEnum from '../../../../enums/ServiceSelectionTypes';

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
    talk_subject: {
      type: [Array, Object],
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
      ServiceSelectionTypesEnum,
      handleSelectedFile,
      handleNextFolderContent,
    };
  },
};
</script>
