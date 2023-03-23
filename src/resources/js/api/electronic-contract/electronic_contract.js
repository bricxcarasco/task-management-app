import axios from 'axios';
import ApiService from '../api';

export default class ElectronicContractService extends ApiService {
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
    this.baseUrl = '/api/electronic-contracts';
  }

  /**
   * Electronic contract - manual recipient registration
   *
   * Method: POST
   * Endpoint: /api/electronic-contracts/manual-register
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  manualRecipientRegister(data) {
    return axios.post(`${this.baseUrl}/manual-recipient-register`, data, {
      headers: this.headers,
    });
  }

  /**
   * Electronic contract - form submission
   *
   * Method: POST
   * Endpoint: /api/electronic-contracts/
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  register(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }
}
