import axios from 'axios';
import RioApiService from '../rio';

export default class RioInformationApiService extends RioApiService {
  /**
   * Update email address of authenticated user
   *
   * @param {Object} data
   * @returns {Promise}
   */
  updateEmailAddress(data) {
    return axios.post('api/rio/information/update/email', data, {
      headers: this.headers,
    });
  }

  /**
   * Updates the password of the authenticated user
   *
   * Method: POST
   * Endpoint: /api/rio/information/update/password
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  updatePassword(data) {
    return axios.post('api/rio/information/update/password', data, {
      headers: this.headers,
    });
  }
}
