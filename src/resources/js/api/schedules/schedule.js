import axios from 'axios';
import ApiService from '../api';

export default class ScheduleApiService extends ApiService {
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
    this.baseUrl = '/api/schedules';
  }

  /**
   * Get schedules by year and month.
   *
   * Method: GET
   * Endpoint: /api/schedules/month/{date}
   *
   * @param {String} date
   * @returns {Promise}
   */
  getSchedulesPerMonth(date) {
    return axios.get(`${this.baseUrl}/month/${date}`, {
      headers: this.headers,
    });
  }

  /**
   * Get schedules by day.
   *
   * Method: GET
   * Endpoint: /api/schedules/day/{date}
   *
   * @param {String} date
   * @returns {Promise}
   */
  getSchedulesPerDay(date) {
    return axios.get(`${this.baseUrl}/day/${date}`, {
      headers: this.headers,
    });
  }

  /**
   * Add new schedule
   *
   * Method: POST
   * Endpoint: /api/schedules
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  addSchedule(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update connection list
   *
   * Method: Patch
   * Endpoint: /api/schedules
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  updateConnectionList(data) {
    return axios.patch(`${this.baseUrl}/update-connection-list`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update existing schedule guest list
   *
   * Method: Patch
   * Endpoint: /api/schedules
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  updateGuestList(data) {
    return axios.patch(`${this.baseUrl}/update-guest-list`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete schedule
   *
   * Method: Delete
   * Endpoint: /api/schedules/${schedule}
   *
   * @param {String} schedule
   * @returns {Promise}
   */
  deleteSchedule(id) {
    return axios.delete(`${this.baseUrl}/delete-schedule/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Update schedule
   *
   * Method: PUT
   * Endpoint: /api/schedules/update-schedule/${schedule}
   *
   * @param {String} schedule
   * @returns {Promise}
   */
  editSchedule(id, data) {
    return axios.put(`${this.baseUrl}/update-schedule/${id}`, data, {
      headers: this.headers,
    });
  }
}
