import axios from 'axios';

export default class RioApiService {
  constructor() {
    this.promise = null;
    this.headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };
  }

  /**
   * Update RIO profile API endpoint
   *
   * Method: POST
   * Endpoint: /api/rio/profile/update
   *
   * @param {*} data Request data
   * @returns promise
   */
  updateProfile(data) {
    this.promise = axios.post('api/rio/profile/update', data, {
      headers: this.headers,
    });
    return this.promise;
  }
}
