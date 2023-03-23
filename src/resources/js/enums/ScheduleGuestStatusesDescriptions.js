/**
 * ScheduleGuestStatuses enum descriptions
 */

import i18n from '../i18n';

const ScheduleGuestStatuses = {
  /**
   * WAITING FOR RESPONSE
   */
  0: i18n.global.t('enums.schedule_guest_statuses.waiting_for_response'),

  /**
   * PARTICIPATE
   */
  1: i18n.global.t('enums.schedule_guest_statuses.participate'),

  /**
   * NOT PARTICIPATE
   */
  '-1': i18n.global.t('enums.schedule_guest_statuses.not_participate'),
};

export default ScheduleGuestStatuses;
