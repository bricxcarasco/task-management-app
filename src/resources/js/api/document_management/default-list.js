import axios from 'axios';
import ApiService from '../api';

export default class DefaultListApiService extends ApiService {
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
   * Returns list of personal files
   *
   * Method: GET
   * Endpoint: api/document/list
   *
   * @returns {Promise}
   */
  getPersonalDocuments(params) {
    return axios.get(`${this.baseUrl}/list`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Returns list of personal files in a folder
   *
   * Method: GET
   * Endpoint: api/document/list/{id}
   *
   * @returns {Promise}
   */
  getPersonalDocumentsFolder(id, params) {
    return axios.get(`${this.baseUrl}/list/${id}`, {
      headers: this.headers,
      params,
    });
  }
}
