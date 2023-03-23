import axios from 'axios';
import NeoApiService from '../neo';

export default class RioInformationApiService extends NeoApiService {
  /**
   * Set or remove member role to Administrator = 2
   *
   * @param {Object} data
   * @returns {Promise}
   */
  setRemoveAdministrator(data) {
    return axios.post(
      `api/neo/administrator/update/administrator/${data.neo_id}`,
      {
        rio_id: data.rio_id,
        type: data.type,
      },
      {
        headers: this.headers,
      }
    );
  }

  /**
   * Set member role to Owner = 1
   *
   * @param {Object} data
   * @returns {Promise}
   */
  transferOwnership(data) {
    return axios.post(
      `api/neo/administrator/update/owner/${data.neo_id}`,
      {
        rio_id: data.rio_id,
      },
      {
        headers: this.headers,
      }
    );
  }
}
