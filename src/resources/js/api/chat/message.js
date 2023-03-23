import axios from 'axios';
import ApiService from '../api';

export default class ChatMessageApiService extends ApiService {
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
    this.baseUrl = '/api/chat/message';
  }

  /**
   * Get messages of talk room.
   *
   * Method: GET
   * Endpoint: /api/chat/message/{id}
   *
   * @param {Integer} id Chat id
   * @returns {Promise}
   */
  getMessages(id) {
    return axios.get(`${this.baseUrl}/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Send message.
   *
   * Method: POST
   * Endpoint: /api/chat/message
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  sendMessage(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete message.
   *
   * Method: DELETE
   * Endpoint: /api/chat/message
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  deleteMessage(data) {
    return axios.post(`${this.baseUrl}/delete`, data, {
      headers: this.headers,
    });
  }
}
