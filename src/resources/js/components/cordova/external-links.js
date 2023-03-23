/**
 * Attaches event listener for handling external links
 *
 * @return {void}
 */
/* eslint no-undef: 0 */
export default () => {
  const windowOpen = window.open;

  /**
   * Overwrite window open method with cordova in-app browser method
   */
  /* eslint-disable func-names */
  window.open = function (url, target = '_blank', windowFeatures = '') {
    const { hostname } = window.location;

    try {
      const urlInfo = new URL(url);
      const targetHost = urlInfo.hostname || '';

      if (targetHost !== hostname) {
        return cordova.InAppBrowser.open(url, target, windowFeatures);
      }
      /* eslint-disable no-empty */
    } catch (error) {}

    return windowOpen(url, target, windowFeatures);
  };

  /**
   * Attach event listener for links to be opened externally
   */
  $(document).on('click', 'a.js-external-link', (event) => {
    event.preventDefault();
    cordova.InAppBrowser.open(event.target.href, '_blank', 'location=yes');
  });
};
