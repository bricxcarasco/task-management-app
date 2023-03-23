/**
 * Attaches event listener for logging out
 *
 * @return {void}
 */
/* eslint no-undef: 0 */
/* eslint-disable import/prefer-default-export */
export const attachLogoutEventListener = () => {
  $(document).on('click', '.logout-js', () => {
    const pageLoader = document.querySelector('.page-loading');
    pageLoader.classList.add('active');
    pageLoader.classList.remove('d-none');
  });
};
