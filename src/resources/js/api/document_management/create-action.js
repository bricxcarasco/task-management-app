import axios from 'axios';
import ApiService from '../api';

export default class CreateActionApiService extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url for connection application request
     * @var {string}
     */
    this.baseUrl = 'api/document';
  }

  /**
   * Create Folder API
   *
   * Method: POST
   * Endpoint: /api/document/folder
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  createFolder(data) {
    return axios.post(`${this.baseUrl}/folder`, data, {
      headers: this.headers,
    });
  }

  /**
   * Save Files API
   *
   * Method: POST
   * Endpoint: /api/document/file
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveFiles(data) {
    return axios.post(`${this.baseUrl}/file`, data, {
      headers: this.headers,
    });
  }
}
