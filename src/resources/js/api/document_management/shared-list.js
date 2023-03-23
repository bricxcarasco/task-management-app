import axios from 'axios';
import ApiService from '../api';

export default class SharedListApiService extends ApiService {
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
   * Returns list of shared files by owner or shared to the owner
   *
   * Method: GET
   * Endpoint: api/document/shared
   *
   * @returns {Promise}
   */
  getSharedDocuments(params) {
    return axios.get(`${this.baseUrl}/shared`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Returns list of shared files by owner or shared to the owner from a shared folder
   *
   * Method: GET
   * Endpoint: api/document/list/{$id}
   *
   * @returns {Promise}
   */
  getSharedDocumentsFolder(id, params) {
    return axios.get(`${this.baseUrl}/list/${id}`, {
      headers: this.headers,
      params,
    });
  }
}
