<?php

class CRWD_CLIENT {

    function __construct() {
        add_action('init', array($this, 'github_updater'));
        add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets'));
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

}

if(!defined('IWP_MMB_PLUGIN_DIR'))
    define('IWP_MMB_PLUGIN_DIR', $iwp_mmb_plugin_dir);

new CRWD_CLIENT();