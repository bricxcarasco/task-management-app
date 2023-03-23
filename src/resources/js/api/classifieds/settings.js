import axios from 'axios';
import ApiService from '../api';

export default class ClassifiedSettingsApiService extends ApiService {
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
    this.baseUrl = '/api/classifieds/settings';
  }

  /**
   * Get bank accounts
   *
   * Method: Patch
   * Endpoint: /api/classifieds/settings
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getSettings() {
    return axios.get(`${this.baseUrl}`, {
      headers: this.headers,
    });
  }

  /**
   * Register bank account details
   *
   * Method: Patch
   * Endpoint: /api/classifieds/settings
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  registerSetting(data) {
    return axios.post(`${this.baseUrl}/register-setting`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edit bank account details
   *
   * Method: Patch
   * Endpoint: /api/classifieds/settings
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  editSetting(data) {
    return axios.patch(`${this.baseUrl}/edit-setting`, data, {
      headers: this.headers,
    });
  }

  /**
   * Save bank account details
   *
   * Method: Patch
   * Endpoint: /api/classifieds/settings
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveAccountDetails(data) {
    return axios.patch(`${this.baseUrl}/save-accounts`, data, {
      headers: this.headers,
    });
  }
}
