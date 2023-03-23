import axios from 'axios';
import RioApiService from '../rio';

export default class AccountInformationApiService extends RioApiService {
  /**
   * Update secret question of authenticated user
   *
   * @param {Object} data
   * @returns {Promise}
   */
  updateSecretQuestion(data) {
    this.promise = axios.post(
      'api/rio/information/update/secret-question',
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }
}
