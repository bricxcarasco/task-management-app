import axios from 'axios';
import ApiService from '../api';

export default class ClassifiedContactMessageApiService extends ApiService {
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
    this.baseUrl = '/api/classifieds/messages';
  }

  /**
   * Get inquiries list based on conditions
   *
   * Method: GET
   * Endpoint: /api/classifieds/messages
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(params) {
    return axios.get(`${this.baseUrl}`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Get message inquiries
   *
   * Method: GET
   * Endpoint: /api/classifieds/messages/{id}
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
   * Send message inquiry
   *
   * Method: POST
   * Endpoint: /api/classifieds/messages/send
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  sendMessage(data) {
    return axios.post(`${this.baseUrl}/send`, data, {
      headers: this.headers,
    });
  }
}
