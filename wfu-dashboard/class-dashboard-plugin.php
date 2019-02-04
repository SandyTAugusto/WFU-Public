<?php
//Main class for the plug, loads needed scripts and does the clean up of metaboxes
class WFUDashboard {
	function WFUdashboardScriptAutoloader() 
	{
	  wp_enqueue_style('datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
	  wp_enqueue_script('js-datepicker', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'), null, true); 
	  wp_enqueue_script('ajax-script', plugins_url('/js/dismiss-message.js', __FILE__ ), array( 'jquery' ), '1.0', true);
	}

	function WFUDashboardAutoloader()
	{
	  include('autoloader.php');
	}

	function wfuDashboardCleanup() 
	{
		//Doing the remove on both sides in case the metabox was moved
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'side' );
		remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );

		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' );
	}

}
?>