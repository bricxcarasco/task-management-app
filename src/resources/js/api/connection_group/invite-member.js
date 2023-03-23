import axios from 'axios';
import ApiService from '../api';

export default class InviteMemberApiService extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url for Invite Member API
     * @var {string}
     */
    this.baseUrl = 'api/connection/groups';
  }

  /**
   * Fetches connected rio users
   *
   * Method: GET
   * Endpoint: /api/connection/groups/invite-members/{id}/search
   *
   * @param {Integer} id Group ID\
   * @returns {Promise}
   */
  getRioConnectedUsers(id, params) {
    return axios.get(`${this.baseUrl}/invite-members/${id}/search`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Sends invite to user
   *
   * Method: POST
   * Endpoint: /api/connection/groups/invite-members/{id}/search
   *
   * @param {Integer} id Group ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  sendInvite(id, data) {
    return axios.post(`${this.baseUrl}/invite-members/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes connection invitation
   *
   * Method: POST
   * Endpoint: /api/connection/groups/invite-members/{id}/search
   *
   * @param {Integer} id Invitation ID
   * @returns {Promise}
   */
  deleteInvite(id) {
    return axios.delete(`${this.baseUrl}/invite-members/${id}`, {
      headers: this.headers,
    });
  }
}
