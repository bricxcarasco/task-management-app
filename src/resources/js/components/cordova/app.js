import attachExternalLinkHandler from './external-links';

const app = {
  /**
   * Initialize applicationn
   */
  initialize() {
    this.bindEvents();
  },

  /**
   * Attach device ready event listener
   */
  bindEvents() {
    document.addEventListener('deviceready', this.onDeviceReady, false);
  },

  /**
   * Device ready event listener
   */
  onDeviceReady() {
    attachExternalLinkHandler();
  },
};

app.initialize();
