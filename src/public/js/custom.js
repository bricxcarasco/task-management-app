/* eslint func-names: ["error", "never"] */
(function () {
  if (document.getElementById('register-present_address_nationality')) {
    const presentAddressPrefecture = document.querySelector(
      '.select-present-address-prefecture'
    );
    document.getElementById(
      'register-present_address_nationality'
    ).style.display = presentAddressPrefecture.value === '0' ? 'block' : 'none';
    presentAddressPrefecture.addEventListener('change', (event) => {
      if (event.target.value === '0') {
        document.getElementById(
          'register-present_address_nationality'
        ).style.display = 'block';
      } else {
        document.getElementById('present_address_nationality').value = '';
        document.getElementById(
          'register-present_address_nationality'
        ).style.display = 'none';
      }
    });
  }

  if (document.querySelector('.password-toggle-indicator')) {
    const passwordToggleCheck = document.querySelector(
      '.password-toggle-indicator'
    );
    passwordToggleCheck.addEventListener('click', () => {
      const passwordToggle = document.getElementById('password_toggle');
      if (passwordToggle) {
        if (passwordToggle.type === 'password') {
          passwordToggle.type = 'text';
        } else {
          passwordToggle.type = 'password';
        }
      }
    });
  }
})();
