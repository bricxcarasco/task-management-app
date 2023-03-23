<template>
  <div
    class="modal fade"
    id="schedule-invitation-modal"
    tabindex="-1"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    ref="modalRef"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ $t('buttons.invite') }}</h4>
          <button
            class="btn-close"
            type="button"
            data-bs-dismiss="modal"
            aria-label="Close"
            @click="handleRevertModal"
          ></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="normal-input" class="form-label">{{
              $t('labels.add_connection_to_invite')
            }}</label>
            <input
              class="form-control"
              type="search"
              id="normal-input"
              placeholder=""
              v-model="search"
            />
          </div>
          <div v-if="service.type === 'NEO'" class="row">
            <div class="col-12">
              <label for="normal-input" class="form-label">
                <div class="mt-3">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    id="members"
                    @change="selectBulk($event)"
                  />
                  <label class="ms-1">
                    {{ $t('labels.invite_all_neo_members') }}
                  </label>
                </div>
              </label>
            </div>
            <div class="col-12">
              <label for="normal-input" class="form-label">
                <div class="mt-3">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    id="connections"
                    @change="selectBulk($event)"
                  />
                  <label class="ms-1">
                    {{ $t('labels.invite_all_neo_connections') }}
                  </label>
                </div>
              </label>
            </div>
          </div>
          <div class="connection__invite">
            <ul
              v-if="searchGuestList.length > 0"
              class="
                connection__lists
                list-group list-group-flush
                mt-2
                border
                p-2
              "
            >
              <li
                v-for="(list, index) in searchGuestList"
                :key="index"
                :value="index"
                class="
                  d-flex
                  justify-content-between
                  align-items-center
                  list-group-item
                  px-0
                  py-2
                  position-relative
                  list--white
                  px-2
                "
              >
                <label :for="index">
                  <img
                    class="
                      rounded-circle
                      me-2
                      d-inline-block
                      img--profile_image_sm
                    "
                    :src="list.profile_picture ?? defaultImage"
                    @error="
                      Common.handleNotFoundImageException(
                        $event,
                        DefaultImageCategory.RIO_NEO
                      )
                    "
                    alt="Product"
                    width="40"
                  />
                  <span class="fs-xs c-primary ms-2">{{ list.name }}</span>
                </label>
                <div
                  class="
                    vertical-right
                    d-flex
                    align-items-center
                    justify-content-center
                  "
                >
                  <input
                    v-bind:value="{
                      id: list.id,
                      type: list.service,
                      name: list.name.trim(),
                      profile_picture: list.profile_picture
                        ? list.profile_picture
                        : defaultImage,
                    }"
                    v-model="guests"
                    type="checkbox"
                    class="form-check-input"
                    :id="list.name.trim() + '' + list.id"
                  />
                </div>
              </li>
            </ul>
            <div
              v-else
              class="mt-2 border p-2 d-flex justify-content-center mt-3"
            >
              {{ $t('labels.no_search_result') }}
            </div>
          </div>
          <div class="text-center mt-4">
            <button
              @click="handleGuestsSelected"
              type="button"
              class="btn btn-primary"
              data-bs-dismiss="modal"
              aria-label="Close"
            >
              {{ $t('buttons.register') }}
            </button>
          </div>
          <p class="mt-4 fs-xs">
            {{ $t('labels.notification_calendar_description') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, computed, ref, watch } from 'vue';
import BpheroConfig from '../../../config/bphero';
import Common from '../../../common';
import DefaultImageCategory from '../../../enums/DefaultImageCategory';
import ServiceSelectionTypes from '../../../enums/ServiceSelectionTypes';

export default defineComponent({
  name: 'ScheduleInvitationModal',
  props: {
    connection_list: {
      type: [Array, Object],
      required: true,
    },
    member_list: {
      type: [Array, Object],
      required: true,
    },
    merged_list: {
      type: [Array, Object],
      required: true,
    },
    guest_list: {
      type: String,
      required: true,
    },
    current_guests: {
      type: [Array, Object],
      required: true,
    },
    selected: {
      type: [Array, Object],
      required: true,
    },
    is_current_owner: {
      type: Boolean,
      required: true,
    },
    update_selected: {
      type: [Array, Object],
      required: false,
    },
    service: {
      type: [Array, Object],
      required: true,
    },
  },
  emits: ['handleGuestsSelected'],
  setup(props, { emit }) {
    const lists = ref(
      props.service.type === ServiceSelectionTypes.NEO
        ? props.merged_list
        : props.connection_list
    );
    const guests = ref(props.selected);
    const search = ref('');
    const currentGuests = ref(props.current_guests);
    const updateSelected = ref(props.update_selected);
    const savedGuests = ref(props.selected);

    watch(
      () =>
        props.service.type === ServiceSelectionTypes.NEO
          ? props.merged_list
          : props.connection_list,
      (newValue) => {
        lists.value = newValue;
      }
    );

    watch(
      () => [props.guest_list, props.is_current_owner, props.update_selected],
      ([, isCurrentOwner, updateSelect]) => {
        if (!isCurrentOwner) {
          guests.value = [];
          savedGuests.value = [];
        } else {
          guests.value = updateSelect;
          savedGuests.value = updateSelect;
        }
      }
    );

    /**
     * Computed properties
     */
    const defaultImage = computed(() => BpheroConfig.DEFAULT_IMG);

    /**
     * Check if name exists on other list
     *
     * @parmeter integer eventId, string name
     * @return {Boolean}
     */
    const includeCheckbox = (eventId, name) => {
      const list =
        eventId === 'members' ? props.connection_list : props.member_list;
      const bulkElement = document.getElementById(
        eventId === 'members' ? 'connections' : 'members'
      );
      const nameExists = list.some((el) => `${el.name}${el.id}` === name);

      if (bulkElement.checked && nameExists) {
        return false;
      }

      return true;
    };

    /**
     * Uncheck/check event handler for bulk options
     *
     * @parmeter event event
     * @return {void}
     */
    const selectBulk = (event) => {
      const eventId = event.target.id;
      const list =
        eventId === 'members' ? props.member_list : props.connection_list;

      // used for instead of foreach because asynchronous operation is happening on every iteration
      (async function triggerClick() {
        for (let i = 0; i <= list.length - 1; i += 1) {
          const name = `${list[i].name.trim()}${list[i].id}`;
          const element = document.getElementById(name);

          // check if one of the bulk selection is unchecked and name is on other list
          if (!event.target.checked) {
            if (includeCheckbox(eventId, name) && element.checked) {
              element.click();
            }
          }

          // if check box and element is unchecked trigger click
          if (event.target.checked && !element.checked) {
            element.click();
          }

          /* eslint-disable no-await-in-loop */
          await new Promise(setTimeout);
        }
      })();
    };

    const handleGuestsSelected = () => {
      savedGuests.value = JSON.parse(JSON.stringify(guests.value));
      emit('handleGuestsSelected', JSON.parse(JSON.stringify(guests.value)));
    };

    /**
     * Computed properties for searching guest list
     */
    const searchGuestList = computed(() =>
      lists.value.filter((list) =>
        list.name.toLowerCase().includes(search.value.toLowerCase())
      )
    );

    const handleRevertModal = () => {
      search.value = '';

      if (savedGuests.value) {
        guests.value = savedGuests.value;
      } else {
        guests.value = [];
      }
    };

    return {
      Common,
      DefaultImageCategory,
      defaultImage,
      lists,
      handleGuestsSelected,
      ServiceSelectionTypes,
      guests,
      search,
      searchGuestList,
      currentGuests,
      updateSelected,
      handleRevertModal,
      savedGuests,
      selectBulk,
    };
  },
});
</script>
