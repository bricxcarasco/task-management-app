<template>
  <div>
    <!-- Chat Room Actions -->
    <div class="message__tools">
      <div class="d-flex align-items-center justify-content-between">
        <p class="mb-0">
          {{ $t('paragraphs.talk_room_list') }}

          <!-- Chat Directions -->
          <function-info-button class="ms-2">
            <h5 class="border-bottom">つながりチャット</h5>
            <p class="fs-sm mb-5">
              つながりのあるNEOとRIOにメッセージすることができます。
            </p>
            <h5 class="border-bottom">RIOつながりグループチャット</h5>
            <p class="fs-sm mb-5">
              オーナーRIOが作成したグループのメンバーにメッセージすることができます。
            </p>
            <h5 class="border-bottom">NEOメッセージチャット</h5>
            <p class="fs-sm mb-5">
              配信範囲を指定して、NEO内のみのつながりのあるNEOとRIOにメッセージすることができます。
              <br />
              ※受信側のNEOとRIOは返信することができません。
            </p>
            <h5 class="border-bottom">NEOチームチャット</h5>
            <p class="fs-sm">
              NEOに参加中、さらにNEOが作成したグループに参加中のみのRIOの間でチャットすることができます。
              <br />
              ※NEOに参加してもグループに参加しないメンバーはメッセージを送るまたは見ることができません。
            </p>
          </function-info-button>
        </p>
        <div class="btn-group dropdown dropstart">
          <button
            type="button"
            class="btn btn-link"
            data-bs-toggle="dropdown"
            id="dropdownMenuButton1"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="ai-more-vertical"></i>
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <div v-if="session.type === 'NEO' && !session.data.is_member">
              <a class="dropdown-item" @click.prevent="createNeoGroup()">
                {{ $t('links.create_neo_team_chat') }}
              </a>
            </div>
            <a href="#" @click="handleClickRestore" class="dropdown-item">
              {{ $t('links.restore_archive') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { computed } from 'vue';
import FunctionInfoButton from '../../../base/BaseFunctionInfoButton.vue';

export default {
  name: 'ChatRoomAction',
  props: {
    session: {
      type: [Array, Object],
      required: true,
    },
  },
  components: {
    FunctionInfoButton,
  },
  emits: ['handleClickRestore'],
  setup(props, { emit }) {
    const handleClickRestore = async () => {
      emit('handleClickRestore');
    };

    /**
     * Inject record and open modal
     *
     * @returns {void}
     */
    const createNeoGroup = () => {
      /* eslint no-undef: 0 */
      const createNeoGroupModalNode = document.querySelector(
        '#create-neo-group-modal'
      );
      const createNeoGroupModal = computed(
        () => new bootstrap.Modal(createNeoGroupModalNode)
      );
      const field = createNeoGroupModalNode.querySelector('input[name=id]');
      field.value = props.session.data.id;
      createNeoGroupModal.value.show();
    };

    return {
      handleClickRestore,
      createNeoGroup,
    };
  },
};
</script>
