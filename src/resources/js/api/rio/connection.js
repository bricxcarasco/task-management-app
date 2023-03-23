import axios from 'axios';
import RioApiService from '../rio';

export default class RioConnectionApiService extends RioApiService {
  /**
   * Connect authenticated user to other RIO
   *
   * @param {Object} data
   * @returns {Promise}
   */
  connect(data) {
    this.promise = axios.post('api/rio/connection/connect', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Disconnect or cancel pending connection request authenticated user to other RIO
   *
   * @param {Object} data
   * @returns {Promise}
   */
  cancelDisconnect(data) {
    this.promise = axios.post('api/rio/connection/cancel-disconnect', data, {
      headers: this.headers,
    });
    return this.promise;
  }
}
