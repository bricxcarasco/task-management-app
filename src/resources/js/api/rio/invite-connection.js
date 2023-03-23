import axios from 'axios';
import ApiService from '../api';

export default class InviteConnection extends ApiService {
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
    this.baseUrl = 'api/rio/profile/invitation-management';
  }

  /**
   * Accept invitation
   *
   * Method: PATCH
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  acceptInvitation(id) {
    return axios.patch(`${this.baseUrl}/accept-invitation/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Decline invitation
   *
   * Method: PATCH
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  declineInvitation(id) {
    return axios.patch(`${this.baseUrl}/decline-invitation/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Get invitation list
   *
   * Method: GET
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  getInvitationList(data) {
    return axios.patch(`${this.baseUrl}/invite-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get and Update invitation pages
   *
   * Method: PATCH
   * Endpoint: api/neo/profile/invitation-management
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  getInvitationListPage(data) {
    return axios.patch(`${this.baseUrl}/pending-invitation`, data, {
      headers: this.headers,
    });
  }
}
