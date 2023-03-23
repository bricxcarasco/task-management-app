import axios from 'axios';
import ApiService from '../api';

export default class CommentsApiService extends ApiService {
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
    this.baseUrl = '/api/knowledges/articles/comments';
  }

  /**
   * Create comment
   *
   * Method: Post
   * Endpoint: /api/knowledges/articles/comments
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveComment(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete comment
   *
   * Method: Delete
   * Endpoint: /api/knowledges/articles/comments/{id}
   *
   * @param {*} id KnowledgeComment ID
   * @returns {Promise}
   */
  deleteComment(id) {
    return axios.delete(`${this.baseUrl}/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Edit comment
   *
   * Method: Put
   * Endpoint: /api/knowledges/articles/comments/{id}
   *
   * @param {*} id KnowledgeComment ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  editComment(id, data) {
    return axios.put(`${this.baseUrl}/${id}`, data, {
      headers: this.headers,
    });
  }
}
