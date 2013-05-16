<?php

class CRWD_CLIENT {

    function __construct() {
        add_action('init', array($this, 'github_updater'));
        add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets'));
        add_filter('map_meta_cap', array($this, 'map_meta_cap'), 10, 4 );
        add_action('wp_loaded', array($this, 'cleanup_plugins'), 20);

        $this->fix_autologin();
    }

    function github_updater() {
        require_once 'updater.class.php';

        define( 'WP_GITHUB_FORCE_UPDATE', true );

        if ( is_admin() ) {

            new CRWP_GitHub_Updater( array(
                'slug' => plugin_basename(IWP_MMB_PLUGIN_DIR).'/'.'init.php',
                'proper_folder_name' => plugin_basename(IWP_MMB_PLUGIN_DIR),
                'api_url' => 'https://api.github.com/repos/croemmich/crwd-client',
                'raw_url' => 'https://raw.github.com/croemmich/crwd-client/master/init.php',
                'github_url' => 'https://github.com/croemmich/crwd-client',
                'zip_url' => 'https://api.github.com/repos/croemmich/crwd-client/zipball/master',
                'sslverify' => true,
                'requires' => '3.0',
                'tested' => '3.5',
                'readme' => 'README.md',
                'access_token' => '',
            ));
        }
    }

    function remove_dashboard_widgets() {
        global $wp_meta_boxes;

        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    }

    function map_meta_cap($caps, $cap, $user_id, $args) {
        // disallow plugin editor
        if ($cap == 'edit_plugins') {
            $caps[] = 'do_not_allow';
        }
        return $caps;
    }

    function fix_autologin() {
        if (!empty($_GET['redirect_to'])) {
            $query = parse_url($_GET['redirect_to'], PHP_URL_QUERY);
            parse_str($query, $params);

            if (!empty($params['auto_login'])) {
                foreach($params as $key=>$value) {
                    $_GET[$key] = $value;
                    $_REQUEST[$key] = $value;
                }
                $_GET['iwpredirect'] = admin_url();
                $_REQUEST['iwpredirect'] = admin_url();
                $unset = array('redirect_to', 'reauth');
                foreach($unset as $u) {
                    unset($_GET[$u]);
                    unset($_REQUEST[$u]);
                }
            }
        }
    }

    function cleanup_plugins() {
        // gravityforms
        remove_action('after_plugin_row_gravityforms/gravityforms.php', array('RGForms', 'plugin_row'));

        // ultimate-tinymce
        remove_filter('plugin_row_meta', 'jwl_execphp_donate_link');
        remove_action('admin_print_styles', 'jwl_admin_style');
    }

}

if(!defined('IWP_MMB_PLUGIN_DIR'))
    define('IWP_MMB_PLUGIN_DIR', $iwp_mmb_plugin_dir);

new CRWD_CLIENT();