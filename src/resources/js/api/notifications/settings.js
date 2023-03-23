import axios from 'axios';
import ApiService from '../api';

export default class SettingsApiService extends ApiService {
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
    this.baseUrl = '/api/notifications/settings';
  }

  /**
   * Get selected templates of authenticated user
   *
   * @returns {Promise}
   */
  getSelectedTemplates() {
    return axios.get(`${this.baseUrl}/mail-templates`, {
      headers: this.headers,
    });
  }

  /**
   * Update the mail template settings of authenticated user
   *
   * @param {Object} data
   * @returns {Promise}
   */
  updateMailTemplates(data) {
    return axios.post(`${this.baseUrl}/mail-templates`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update the general settings of the user
   *
   * @param {Object} data
   * @returns {Promise}
   */
  updateGeneralSettings(data) {
    return axios.post(`${this.baseUrl}/general`, data, {
      headers: this.headers,
    });
  }
}
