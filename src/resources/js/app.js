/* eslint no-undef: 0 */
import './bootstrap';
import { attachLogoutEventListener } from './utils/logout';

$(document).ready(() => {
  attachLogoutEventListener();
});
