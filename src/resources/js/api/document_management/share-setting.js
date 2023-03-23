import axios from 'axios';
import ApiService from '../api';

export default class ShareSettingApiService extends ApiService {
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
    this.baseUrl = 'api/document/share-setting';
  }

  /**
   * Create records for share access
   *
   * Method: GET
   * Endpoint: api/document/share-setting/connected-list/{id}
   *
   * @returns {Promise}
   */
  saveShareSetting(params) {
    return axios.get(`${this.baseUrl}`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Returns list of connected services without share access
   *
   * Method: GET
   * Endpoint: api/document/share-setting/connected-list/{id}
   *
   * @returns {Promise}
   */
  getConnectedList(id, params) {
    return axios.get(`${this.baseUrl}/connected-list/${id}`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Returns list of connected services with share access
   *
   * Method: GET
   * Endpoint: api/document/share-setting/connected-list/{id}
   *
   * @returns {Promise}
   */
  getPermittedList(id, params) {
    return axios.get(`${this.baseUrl}/permitted-list/${id}`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Soft delete selected item
   *
   * Method: GET
   * Endpoint: api/document/share-setting/connected-list/{id}
   *
   * @returns {Promise}
   */
  unshare(id) {
    return axios.delete(`${this.baseUrl}/unshare/${id}`, {
      headers: this.headers,
    });
  }
}
