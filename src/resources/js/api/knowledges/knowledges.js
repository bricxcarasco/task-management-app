import axios from 'axios';
import ApiService from '../api';

export default class KnowledgeApiService extends ApiService {
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
    this.baseUrl = '/api/knowledges';
  }

  /**
   * Get all public folders
   *
   * Method: GET
   * Endpoint: /api/knowledges/get-folders/{id?}
   *
   * @returns {Promise}
   */
  getFolders(id = null) {
    let url = `${this.baseUrl}/get-folders`;

    if (id !== null) {
      url = `${this.baseUrl}/get-folders/${id}`;
    }

    return axios.get(url, {
      headers: this.headers,
    });
  }

  /**
   * Get all public articles
   *
   * Method: GET
   * Endpoint: /api/knowledges/get-articles/{id?}
   *
   * @returns {Promise}
   */
  getArticles(id = null) {
    let url = `${this.baseUrl}/get-articles`;

    if (id !== null) {
      url = `${this.baseUrl}/get-articles/${id}`;
    }

    return axios.get(url, {
      headers: this.headers,
    });
  }

  /**
   * Create Folder API
   *
   * Method: POST
   * Endpoint: /api/knowledges/folder
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  createFolder(data) {
    return axios.post(`${this.baseUrl}/folder`, data, {
      headers: this.headers,
    });
  }

  /**
   * Rename Folder API
   *
   * Method: PUT
   * Endpoint: /api/knowledges/rename/{id}
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  renameFolder(id, data) {
    return axios.put(`${this.baseUrl}/rename/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * move knowledge API
   *
   * Method: PUT
   * Endpoint: /api/knowledges/move/{id}
   */
  moveKnowledge(id, data) {
    return axios.put(`${this.baseUrl}/move/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete folder/article
   *
   * Method: DELETE
   * Endpoint: /api/knowledges/delete/{id}
   *
   * @param {*} id Knowledge ID
   * @returns {Promise}
   */
  delete(id) {
    return axios.delete(`${this.baseUrl}/delete/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Create article
   *
   * Method: POST
   * Endpoint: /api/knowledges/articles/
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveArticle(data) {
    return axios.post(`${this.baseUrl}/articles`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update article
   *
   * Method: PUT
   * Endpoint: /api/knowledge/articles/
   *
   * @param {*} data Request data
   * @param int draft draft_id
   * @returns {Promise}
   */
  updateArticle(data, draft) {
    return axios.put(`${this.baseUrl}/articles/${draft}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Create article as draft
   *
   * Method: POST
   * Endpoint: /api/knowledge/articles/draft
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveArticleDraft(data) {
    return axios.post(`${this.baseUrl}/articles/drafts`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update article draft
   *
   * Method: PUT
   * Endpoint: /api/knowledge/articles/draft
   *
   * @param {*} data Request data
   * @param int draft draft_id
   * @returns {Promise}
   */
  updateArticleDraft(data, draft) {
    return axios.put(`${this.baseUrl}/articles/drafts/${draft}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Save search keyword to session
   *
   * Method: GET
   * Endpoint: /api/knowledges/articles/search/session/{keyword?}
   *
   * @returns {Promise}
   */
  saveSearchToSession(keyword = null) {
    let url = `${this.baseUrl}/articles/search/session`;

    if (keyword !== null) {
      url = `${this.baseUrl}/articles/search/session/${keyword}`;
    }

    return axios.get(url, {
      headers: this.headers,
    });
  }

  /**
   * Get searched articles
   *
   * Method: GET
   * Endpoint: /api/knowledges/articles/search
   *
   * @param {*} params Request data
   *
   * @returns {Promise}
   */
  searchArticles(params) {
    return axios.get(`${this.baseUrl}/articles/search`, {
      headers: this.headers,
      params,
    });
  }
}
