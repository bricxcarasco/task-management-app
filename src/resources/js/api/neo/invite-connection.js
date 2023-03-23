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
     * Base Url for invite/manage connection
     * @var {string}
     */
    this.baseUrl = 'api/neo/profile/invitation-management';
  }

  /**
   * Search connection by Keyword
   *
   * Method: PATCH
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  searchKeyword(data, id) {
    return axios.patch(`${this.baseUrl}/search/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Invite connection user
   *
   * Method: PATCH
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  inviteConnection(data, id) {
    return axios.patch(`${this.baseUrl}/invite-connection/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Connection lists
   *
   * Method: GET
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  getConnectionList(id) {
    return axios.get(`${this.baseUrl}/connection-lists/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Get Selected page
   *
   * Method: PATCH
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  getSelectedPage(data, id) {
    return axios.patch(
      `${this.baseUrl}/participation-invitation-lists/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
  }
}
