import axios from 'axios';
import ApiService from '../api';

export default class DocumentOptionApiService extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url for connection application request
     * @var {string}
     */
    this.baseUrl = 'api/document';
  }

  /**
   * Downloads a file or a folder
   *
   * Method: GET
   * Endpoint: /api/document/file-preview/{id}
   *
   * @returns {Promise}
   */
  getFileDocument(id) {
    return axios.get(`${this.baseUrl}/file-preview/${id}`, {
      headers: this.headers,
      responseType: 'arraybuffer',
    });
  }

  /**
   * Updates file/folder name
   *
   * Method: PUT
   * Endpoint: api/document/rename/{id}
   *
   * @returns {Promise}
   */
  updateDocumentName(id, data) {
    return axios.put(`${this.baseUrl}/rename/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes file/folder
   *
   * Method: DELETE
   * Endpoint: api/document/delete/{id}
   *
   * @returns {Promise}
   */
  deleteDocument(id) {
    return axios.delete(`${this.baseUrl}/delete/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Downloads a file or a folder
   *
   * Method: GET
   * Endpoint: /api/document/download/{id}
   *
   * @returns {Promise}
   */
  checkFolderContent(id) {
    return axios.get(`${this.baseUrl}/check-content/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Downloads a file or a folder
   *
   * Method: GET
   * Endpoint: /api/document/download/{id}
   *
   * @returns {Promise}
   */
  downloadDocument(id) {
    return axios.get(`${this.baseUrl}/download/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Get the shared link
   *
   * Method: GET
   * Endpoint: /api/document/shared-link/{document}
   *
   * @returns {Promise}
   */
  sharedLink(document) {
    return axios.get(`${this.baseUrl}/shared-link/${document}`, {
      headers: this.headers,
    });
  }
}
