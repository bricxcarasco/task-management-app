import axios from 'axios';
import ApiService from '../api';

export default class ArticlesApiService extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url
     * @var {string}
     */
    this.baseUrl = '/api/knowledges/articles';
  }

  /**
   * Load comments
   *
   * Method: Patch
   * Endpoint: /api/articles/comments/{id}
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  loadComments(id, data) {
    return axios.patch(`${this.baseUrl}/comments/${id}`, data, {
      headers: this.headers,
    });
  }
}
