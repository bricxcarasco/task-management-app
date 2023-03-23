import axios from 'axios';
import ApiService from '../api';

export default class TaskApiService extends ApiService {
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
    this.baseUrl = '/api/tasks';
  }

  /**
   * Add new task
   *
   * Method: POST
   * Endpoint: /api/tasks
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  addTask(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update existing task
   *
   * Method: PUT
   * Endpoint: /api/tasks
   *
   * @param {*} id Task ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  updateTask(id, data) {
    return axios.put(`${this.baseUrl}/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Complete tasks
   *
   * Method: PATCH
   * Endpoint: /api/tasks/complete
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  completeTasks(data) {
    return axios.patch(`${this.baseUrl}/complete`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete tasks
   *
   * Method: PATCH
   * Endpoint: /api/tasks/delete
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  deleteTasks(data) {
    return axios.patch(`${this.baseUrl}/delete`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Task list
   *
   * Method: Patch
   * Endpoint: /api/tasks
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/task-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get tasks based on year and month.
   *
   * Method: GET
   * Endpoint: /api/tasks/month/{date}
   *
   * @param {String} date
   * @returns {Promise}
   */
  getTasksPerMonth(date) {
    return axios.get(`${this.baseUrl}/month/${date}`, {
      headers: this.headers,
    });
  }

  /**
   * Get tasks by day.
   *
   * Method: GET
   * Endpoint: /api/tasks/day/{date}
   *
   * @param {String} date
   * @returns {Promise}
   */
  getTasksPerDay(date) {
    return axios.get(`${this.baseUrl}/day/${date}`, {
      headers: this.headers,
    });
  }

  /**
   * return task
   *
   * Method: Patch
   * Endpoint: /api/tasks
   *
   * @param {*} id $id
   * @returns {Promise}
   */
  returnTask(id) {
    return axios.patch(`${this.baseUrl}/return-task/${id}`, {
      headers: this.headers,
    });
  }
}
