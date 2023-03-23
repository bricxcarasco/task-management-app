import axios from 'axios';
import ApiService from '../api';

export default class connectionList extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url for electronic contract
     * @var {string}
     */
    this.baseUrl = 'api/electronic-contracts/list';
  }

  /**
   * retrieve connected services to end-user
   *
   * Method: GET
   * Endpoint: /api/electronic-contracts/list
   *
   * @returns {Promise}
   */
  getConnections(params) {
    return axios.get(`${this.baseUrl}`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * retrieve email/s from selected service/recipient
   *
   * Method: GET
   * Endpoint: /api/electronic-contracts/list/recipient-email
   *
   * @returns {Promise}
   */
  getEmails(params) {
    return axios.get(`${this.baseUrl}/recipient-email`, {
      headers: this.headers,
      params,
    });
  }
}
