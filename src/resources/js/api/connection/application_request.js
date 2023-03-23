import axios from 'axios';
import ApiService from '../api';

export default class ApplicationRequest extends ApiService {
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
    this.baseUrl = 'api/connection/request';
  }

  /**
   * Accept connection application requeest of RIO or NEO
   *
   * Method: POST
   * Endpoint: /api/connection/request/accept
   *
   * @returns {Promise}
   */
  acceptConnection(data) {
    return axios.post(`${this.baseUrl}/accept`, data, {
      headers: this.headers,
    });
  }

  /**
   * Accept connection application requeest of RIO or NEO
   *
   * Method: POST
   * Endpoint: /api/connection/request/accept
   *
   * @returns {Promise}
   */
  declineConnection(data) {
    return axios.post(`${this.baseUrl}/decline`, data, {
      headers: this.headers,
    });
  }

  /**
   * Fetches application requests
   *
   * Method: GET
   * Endpoint: api/connection/applications
   *
   * @param {Integer} id Group ID\
   * @returns {Promise}
   */
  getApplicationRequests(params) {
    return axios.get('api/connection/applications', {
      headers: this.headers,
      params,
    });
  }
}
