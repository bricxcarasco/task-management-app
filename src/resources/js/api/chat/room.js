import axios from 'axios';
import ApiService from '../api';

export default class ChatRoomApiService extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url
     * @var {string}
     */
    this.baseUrl = 'api/chat/room';
  }

  /**
   * Fetch chat rooms
   *
   * Method: GET
   * Endpoint: /api/chat/room/search
   *
   * @param {*} params Request data
   * @returns promise
   */
  searchChat(params) {
    return axios.get(`${this.baseUrl}/search`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Update Talk Subject
   *
   * Method: POST
   * Endpoint: /api/chat/room/talk-subject
   *
   * @param {*} data Request data
   * @returns promise
   */
  updateTalkSubject(data) {
    this.promise = axios.patch('/api/chat/room/talk-subject', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Archive talk room
   *
   * Method: PATCH
   * Endpoint: /api/chat/room/archive-room/{id}
   *
   * @returns promise
   */
  archiveTalkRoom(id) {
    this.promise = axios.patch(`/api/chat/room/archive-room/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Restore talk room
   *
   * Method: PATCH
   * Endpoint: /api/chat/room/restore-room
   *
   * @returns promise
   */
  restoreTalkRoom() {
    this.promise = axios.patch(`/api/chat/room/restore-room`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * creates neo group as neo
   *
   * Method: POST
   * Endpoint: /api/chat/room/group/neo/create-group/{neo}
   *
   * @returns promise
   */
  createNeoGroup(id, data) {
    this.promise = axios.post(
      `/${this.baseUrl}/group/neo/create-group/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }
}
