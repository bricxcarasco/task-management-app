import axios from 'axios';
import ApiService from '../api';

export default class NeoMessageApiService extends ApiService {
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
    this.baseUrl = 'api/chat/neo-message';
  }

  /**
   * Get and update filtered list
   *
   * Method: PATCH
   * Endpoint: /api/chat/neo-message
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  getFilteredList(data) {
    this.promise = axios.patch(`${this.baseUrl}/filter-list`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Create neo message
   *
   * Method: POST
   * Endpoint: /api/chat/neo-message
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  createNeoMessage(data) {
    this.promise = axios.post(`${this.baseUrl}/create-neo-message`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Get and update pagination data base on filter
   *
   * Method: Patch
   * Endpoint: /api/chat/neo-message
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  getSelectedPage(data) {
    this.promise = axios.patch(`${this.baseUrl}/select-page`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update search list base on filter selected
   *
   * Method: Patch
   * Endpoint: /api/chat/neo-message
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateList(data) {
    this.promise = axios.patch(`${this.baseUrl}/search`, data, {
      headers: this.headers,
    });
    return this.promise;
  }
}
