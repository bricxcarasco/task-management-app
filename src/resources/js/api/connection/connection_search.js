import axios from 'axios';
import ApiService from '../api';

export default class ConnectionSearch extends ApiService {
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
    this.baseUrl = 'api/connection/search';
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
  getSearchResults(params) {
    return axios.get(`${this.baseUrl}/results`, {
      headers: this.headers,
      params,
    });
  }
}
