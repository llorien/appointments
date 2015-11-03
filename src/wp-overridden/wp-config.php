<?php
    /**
     * The base configurations of the WordPress.
     *
     * This file has the following configurations: MySQL settings, Table Prefix,
     * Secret Keys, WordPress Language, and ABSPATH. You can find more information
     * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
     * wp-config.php} Codex page. You can get the MySQL settings from your web host.
     *
     * This file is used by the wp-config.php creation script during the
     * installation. You don't have to use the web site, you can just copy this file
     * to "wp-config.php" and fill in the values.
     *
     * @package WordPress
     */

    // Required for batcache use
    // define('WP_CACHE', true);
    // configures batcache
    // $batcache = [
    //   'seconds'=>0,
    //   'max_age'=>30*60, // 30 minutes
    //   'debug'=>false
    // ];

    $appengine_app_ids = array(
        'prod' => 'brithon-prod',
        'dev' => 'brithon-dev',
        'local' => 'brithon-local'
    );

    use \google\appengine\api\app_identity\AppIdentityService;
    // running on appengine
    if (isset($_SERVER['APPLICATION_ID'])) {
        $application_id = AppIdentityService::getApplicationId();

        // online GAE
        switch ($application_id) {
            case $appengine_app_ids['prod']:
                /** Live environment Cloud SQL login info */
                define('DB_NAME', 'brithon_appointments');
                define('DB_HOST', ':/cloudsql/brithon-prod:brithon-db');
                define('DB_USER', 'root');
                define('DB_PASSWORD', '');

                // 1. uncomment this line after single site installation.
                define('WP_ALLOW_MULTISITE', true);
                // 2. uncomment this line after network is enabled in the browser.
                define('MULTISITE', true);
                define('SUBDOMAIN_INSTALL', false);
                define('DOMAIN_CURRENT_SITE', 'appointments.brithon.com');
                define('PATH_CURRENT_SITE', '/');
                define('SITE_ID_CURRENT_SITE', 1);
                define('BLOG_ID_CURRENT_SITE', 1);
                break;
            case $appengine_app_ids['dev']:
                define('DB_NAME', 'brithon_appointments');
                define('DB_HOST', ':/cloudsql/brithon-dev:brithon-db');
                define('DB_USER', 'root');
                define('DB_PASSWORD', '');

                // 1. uncomment this line after single site installation.
                define('WP_ALLOW_MULTISITE', true);
                // 2. uncomment this line after network is enabled in the browser.
                define('MULTISITE', true);
                define('SUBDOMAIN_INSTALL', false);
                define('DOMAIN_CURRENT_SITE', 'appointments-dev.brithon.com');
                define('PATH_CURRENT_SITE', '/');
                define('SITE_ID_CURRENT_SITE', 1);
                define('BLOG_ID_CURRENT_SITE', 1);
                break;
            case $appengine_app_ids['local']:
                // local GAE
                define('DB_NAME', 'brithon_appointments');
                define('DB_HOST', '127.0.0.1');
                define('DB_USER', 'root');
                define('DB_PASSWORD', '');

                // 1. uncomment this line after single site installation.
                define('WP_ALLOW_MULTISITE', true);
                // 2. uncomment this line after network is enabled in the browser.
                define('MULTISITE', true);
                define('SUBDOMAIN_INSTALL', false);
                define('DOMAIN_CURRENT_SITE', 'appointments-local.brithon.com');
                define('PATH_CURRENT_SITE', '/');
                define('SITE_ID_CURRENT_SITE', 1);
                define('BLOG_ID_CURRENT_SITE', 1);
                break;
            default:
                die('Unrecognized application_id: ' . $application_id);
        }
    } else {
        // running without GAE
        define('DB_NAME', 'brithon_appointments');
        define('DB_HOST', '127.0.0.1');
        define('DB_USER', 'root');
        define('DB_PASSWORD', '');

        // 1. uncomment this line after single site installation.
        define('WP_ALLOW_MULTISITE', true);
        // 2. uncomment this line after network is enabled in the browser.
        define('MULTISITE', true);
        define('SUBDOMAIN_INSTALL', false);
        define('DOMAIN_CURRENT_SITE', 'appointments-local.brithon.com');
        define('PATH_CURRENT_SITE', '/');
        define('SITE_ID_CURRENT_SITE', 1);
        define('BLOG_ID_CURRENT_SITE', 1);
    }

    // Determine HTTP or HTTPS, then set WP_SITEURL and WP_HOME
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
    {
        $protocol_to_use = 'https://';
    } else {
        $protocol_to_use = 'http://';
    }
    define( 'WP_SITEURL', $protocol_to_use . $_SERVER['HTTP_HOST']);
    define( 'WP_HOME', $protocol_to_use . $_SERVER['HTTP_HOST']);

    /** Database Charset to use in creating database tables. */
    define('DB_CHARSET', 'utf8');

    /** The Database Collate type. Don't change this if in doubt. */
    define('DB_COLLATE', '');

    /**#@+
     * Authentication Unique Keys and Salts.
     *
     * Change these to different unique phrases!
     * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
     * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
     *
     * @since 2.6.0
     */
    define('AUTH_KEY',         'ZdJi)zW5zdcgbK?QMO mb${z(q{TY>x~5Wt~+-+<,=q_Y2Zw++pz%1m&{hTf;):H');
    define('SECURE_AUTH_KEY',  'pgIV|CY?yR]^==>fi>p.O]8Rj$6Mw]t=#m9]u(@} X!$=cA[=T|tbdU f]Wc`ZP8');
    define('LOGGED_IN_KEY',    '$d8W-{u;{bikqJ8LyhGFsqRX,!2I}>-~u(o-r#lT_w=}EH_5Z9T/Act#]|a,Afo|');
    define('NONCE_KEY',        ':*DI/-|r22p6##_W`3w0F`UqK.*F/I:t1wv&AKu534D~Z14qOl;,]LmQz=+t|]~:');
    define('AUTH_SALT',        '@i,9i(M uA:(iMg$hW@yIm179|@y=.X8n3wB?`b5zF+o=/(Ox8vGI q+p:sqt+F>');
    define('SECURE_AUTH_SALT', 'QZ~SqyXSYI:K<.-xMk++JKFa-EL8sK1^3/s,a8eq-ioOMSd3Er!_q_Rt+8F5|WW#');
    define('LOGGED_IN_SALT',   'aTss&l94tK1JOHdA1W6j,~iMtTs&Lm,59V!xEk?i%v%k#h8|FM#H[WEP.e+BzsLP');
    define('NONCE_SALT',       'KC]i`.rt>j=Mo|z2dKw}Vszn+<e~M<}Bo,*J|ZfBx%&QjIljd}dX.G1,!|>2SF>I');
    define('WP_CACHE_KEY_SALT','XIiGa`+0C6uRgfAmT<##mwZ?QLeha*6n<r+LSM|dtI=n:bLGy_$O.g9 ul3D{g|h');
    /**#@-*/

    /**
     * WordPress Database Table prefix.
     *
     * You can have multiple installations in one database if you give each a unique
     * prefix. Only numbers, letters, and underscores please!
     */
    $table_prefix  = 'wp_';

    /**
     * WordPress Localized Language, defaults to English.
     *
     * Change this to localize WordPress. A corresponding MO file for the chosen
     * language must be installed to wp-content/languages. For example, install
     * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
     * language support.
     */
    define('WPLANG', '');

    /**
     * For developers: WordPress debugging mode.
     *
     * Change this to true to enable the display of notices during development.
     * It is strongly recommended that plugin and theme developers use WP_DEBUG
     * in their development environments.
     */
    define('WP_DEBUG', true);

    /**
     * Disable default wp-cron in favor of a real cron job
     */
    define('DISABLE_WP_CRON', true);
    
    /* That's all, stop editing! Happy blogging. */

    /** Absolute path to the WordPress directory. */
    if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/wordpress/');

    /** Sets up WordPress vars and included files. */
    require_once(ABSPATH . 'wp-settings.php');


