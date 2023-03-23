import axios from 'axios';
import ApiService from '../api';

export default class WorkflowApiService extends ApiService {
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
    this.baseUrl = '/api/workflows';
  }

  /**
   * Retrieve connected RIO in NEO
   *
   * Method: GET
   * Endpoint: /api/workflows/approver/list
   *
   * @param int neoId
   * @returns {Promise}
   */
  getApproverList(neoId) {
    return axios.get(`${this.baseUrl}/approver/list/${neoId}`, {
      headers: this.headers,
    });
  }

  /**
   * Validate initial workflow info
   *
   * Method: POST
   * Endpoint: /api/worflows/validate
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateWorkflow(data) {
    return axios.post(`${this.baseUrl}/validate`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validate workflow approvers info
   *
   * Method: POST
   * Endpoint: /api/worflows/validate/approver
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateWorkflowApprover(data) {
    return axios.post(`${this.baseUrl}/validate/approver`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Created Workflow list
   *
   * Method: GET
   * Endpoint: /api/workflows
   *
   * @param {*} params Request data
   * @returns {Promise}
   */
  getLists(params) {
    return axios.get(`${this.baseUrl}/created-workflow-lists`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Save workflow
   *
   * Method: POST
   * Endpoint: /api/worflows/save
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveWorkflow(data) {
    return axios.post(`${this.baseUrl}/save`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Workflow for you list
   *
   * Method: GET
   * Endpoint: /api/workflows
   *
   * @param {*} parms Request data
   * @returns {Promise}
   */
  getWorkflowForYouLists(params) {
    return axios.get(`${this.baseUrl}/workflow-for-you-lists`, {
      headers: this.headers,
      params,
    });
  }

  /**
   * Update workflow action
   *
   * Method: patch
   * Endpoint: /api/workflows/{id}/action
   *
   * @param {*} id Workflow id
   * @param {*} data Request data
   * @returns {Promise}
   */

  updateReaction(id, data) {
    return axios.patch(`${this.baseUrl}/${id}/action`, data, {
      headers: this.headers,
    });
  }

  /**
   * update workflow status to STATUSTYPE::CANCEL
   *
   * Method: patch
   * Endpoint: /api/workflows/{id}/cancel-application
   *
   * @param {*} id Workflow id
   * @returns {Promise}
   */
  cancelApplication(id) {
    return axios.patch(`${this.baseUrl}/${id}/cancel-application`, {
      headers: this.headers,
    });
  }
}
