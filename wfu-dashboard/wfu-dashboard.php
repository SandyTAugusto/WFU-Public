<?php
/**
 * @package WFU Dashboard
 * @version 1.0.0
 */
/*
Plugin Name: WFU Dashboard Plugin
Description: Adds the ability to create dashboard messages and a metaox containing links to resources for a multinetwork site setup. Also removes certain meta boxes from the WordPress dashboard (Quick Post, News and Events, Yoast SEO, At A Glance). The main or super admin can create, edit, and delete messages and resource links. Messages can be set to be global or site specific as well as starting and stopping when needed.
Author: Sandy Teed
Version: 1.0.0
*/

/*
	GLOBALS START
*/
global $aDashboardMessageToEdit; //used for editing exisiting messages
global $aWFUDashboardMessages; //messages
global $aWFUDashboardMessagesIDs; //ids for the messages, so we can keep track of what a user sees
global $sResourceLinksMetaBoxContent; //used for rendering content in metabox
global $aResourceLinkToEdit; //used for editing exisiting links

/*
	LOAD CLASSES
*/
require_once plugin_dir_path( __FILE__ ) . 'class-dashboard-plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'class-resource-link.php';
require_once plugin_dir_path( __FILE__ ) . 'class-dashboard-message.php';

/*
	HOOKS START
*/
//CER-01
//adds menu items if we are the superadmin
add_action('admin_menu', array('WFUResourceLink', 'addResourceLinksMenuItem'));

//loads metabox if we have links
add_action('wp_dashboard_setup', array('WFUResourceLink', 'loadResourceLinksMetabox'));

//hooks for saving new links
add_action('admin_post_nopriv_resourceLinksAddForm', array('WFUResourceLink', 'resourceSavePost'));
add_action('admin_post_resourceLinksAddForm', array('WFUResourceLink', 'resourceSavePost'));

//hooks for updating an existing link
add_action('admin_post_nopriv_resourceLinksUpdateForm', array('WFUResourceLink', 'resourceUpdatePost'));
add_action('admin_post_resourceLinksUpdateForm', array('WFUResourceLink', 'resourceUpdatePost'));


//CER-02
//adds menu items if we are the superadmin
add_action('admin_menu', array('WFUDashboardMessage', 'addDashboardMessagesMenuItem'));

//hooks for saving new messages
add_action('admin_post_nopriv_dashboardMessageAddForm', array('WFUDashboardMessage', 'dashboardMessageSavePost'));
add_action('admin_post_dashboardMessageAddForm', array('WFUDashboardMessage', 'dashboardMessageSavePost'));

//hooks for editing messages
add_action('admin_post_nopriv_dashboardMessageUpdateForm', array('WFUDashboardMessage', 'dashboardMessageUpdatePost'));
add_action('admin_post_dashboardMessageUpdateForm', array('WFUDashboardMessage', 'dashboardMessageUpdatePost'));

//hook to generate the message if there are any
add_action('all_admin_notices', array('WFUDashboardMessage', 'WFUDashboardMessageInit'));

//hooks for the ajax call to register that a user dismissed the message
add_action('wp_ajax_nopriv_dismissWFUDashboardMessage', array('WFUDashboardMessage', 'dismissWFUDashboardMessage'));
add_action('wp_ajax_dismissWFUDashboardMessage', array('WFUDashboardMessage', 'dismissWFUDashboardMessage'));


//CER-03
//hook to clean up the dashboard by removing specific metaboxes
add_action('wp_dashboard_setup', array('WFUDashboard', 'wfuDashboardCleanup'));

//loads custom JS and datepicker JS+CSS to be sure we have it
add_action('admin_enqueue_scripts', array('WFUDashboard', 'WFUdashboardScriptAutoloader'));

//sets up the database for just this links
register_activation_hook( __file__, array('WFUDashboard', 'WFUDashboardAutoloader'));
?>