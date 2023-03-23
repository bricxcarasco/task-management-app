INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES (NULL, '0', '1', 'Dashboard', 'fa-bar-chart', '/', NULL, NULL, NULL), (NULL, '0', '2', 'Admin', 'fa-tasks', '', NULL, NULL, NULL), (NULL, '2', '3', 'Users', 'fa-users', 'auth/users', NULL, NULL, NULL), (NULL, '2', '4', 'Roles', 'fa-user', 'auth/roles', NULL, NULL, NULL), (NULL, '2', '5', 'Permission', 'fa-ban', 'auth/permissions', NULL, NULL, NULL), (NULL, '2', '6', 'Menu', 'fa-bars', 'auth/menu', NULL, NULL, NULL), (NULL, '2', '7', 'Operation log', 'fa-history', 'auth/logs', NULL, NULL, NULL), (NULL, '0', '0', 'Users', 'fa-user', 'users', NULL, '2022-02-23 16:59:56', '2022-02-23 17:04:01');

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES (NULL, 'All permission', '*', '', '*', NULL, NULL), (NULL, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL), (NULL, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL), (NULL, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL), (NULL, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL);

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (NULL, 'Administrator', 'administrator', '2022-02-23 16:34:28', '2022-02-23 16:34:28');

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES (1, 2, NULL, NULL);

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES (1, 1, NULL, NULL);

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`) VALUES (1, 1, NULL, NULL);

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES (NULL, 'admin', '$2y$10$KWiNJmADuFgg4WHCRQgk9.bWtJhy/eiyE3S7l3Af8GeboB.5peJuC', 'Administrator', NULL, 'ikOqFH4Qr1GAOozSEgPmjjQUz7rabezsJvvSvq5zKHlCrn0ZOPCWM8ZD6IU4', '2022-02-23 16:34:28', '2022-02-23 16:34:28')
