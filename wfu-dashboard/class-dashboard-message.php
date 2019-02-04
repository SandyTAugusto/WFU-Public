<?php
//Class for dashboard messaging, handles the message object and deals with the hook functions as well.
class WFUDashboardMessage {

	public function add($sStartDate, $sEndDate, $sMessage, $iBlogId) 
	{
	  global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';

	  $wpdb->insert($sTablename, array(
	    'start' => strtotime($sStartDate), 
	    'end' => strtotime($sEndDate),
	    'message' => $sMessage,
	    'sites' => $iBlogId)
	  );
  }

  public function update($iId, $sStartDate, $sEndDate, $sMessage, $iBlogId) 
  {
	  global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';

	  $wpdb->update($sTablename, 
	    array('start' => strtotime($sStartDate), 
	    'end' => strtotime($sEndDate),
	    'message' => $sMessage,
	    'sites' => $iBlogId), 
	    array('ID' => $iId)
	  );
  }

  public function delete($iLinkId) 
  {
  	global $wpdb;
    $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';
    
    $wpdb->query("DELETE FROM $sTablename WHERE ID = " . $iLinkId);
  }

  public function get($iLinkId) 
  {
  	global $wpdb;
    $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';

    $oResults = $wpdb->get_results("SELECT * FROM $sTablename where ID = " . $iLinkId);
    if(!empty($oResults) && count($oResults) > 0) {
    	return (Array) $oResults[0];
    } else {
    	return NULL;
    }
  }

  public function getAll() 
  {
  	global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';

	  $oResults = $wpdb->get_results("SELECT * FROM $sTablename");

	  return $oResults;
  }

  public function loadDashboardMessages() 
  {
		global $wpdb;
		global $aWFUDashboardMessages;
		global $aWFUDashboardMessagesIDs;
	  $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';

	  //Current day, earliest time
	  $iTime = strtotime(date('F j, Y'));

	  //Filtering done by db call, time range from above, then if global and or site specific
	  $oResults = $wpdb->get_results("SELECT * FROM $sTablename WHERE start <= " . $iTime . " && end >= " . $iTime . " && (sites = 0 || sites = " . get_current_blog_id() . ")");
	  
		if(!empty($oResults) && count($oResults) > 0) {
		  foreach ($oResults as $oRow) {
		  	if ($oRow->users_dismissed != "") {
					$aUserDissmissed = json_decode($oRow->users_dismissed, true);

					//If a user dismissed this message already, don't bother adding this message, continue
					if (in_array(get_current_user_id(), $aUserDissmissed)) {
						continue;
					}

		  		$aWFUDashboardMessages .= $oRow->message . '<br/>';
		  		$aWFUDashboardMessagesIDs[] = $oRow->ID; 
		  	} else {
		  		$aWFUDashboardMessages .= $oRow->message . '<br/>';
		  		$aWFUDashboardMessagesIDs[] = $oRow->ID; 
		  	}
		  }

		  //In case there was an issue concating messages, don't display a blank message
		  if (!is_null($aWFUDashboardMessages)) {
	  		require_once(plugin_dir_path( __FILE__ ) . '/views/dashboard-messages/dashboard-message-message-container.php');
		  }
	  }
  }

  public function dismissMessages($aMessagesIds) 
  {
  	global $wpdb;
		global $aWFUDashboardMessages;
		global $aWFUDashboardMessagesIDs;

		$sTablename = $wpdb->prefix . 'wfu_dashboard_messages';
		  
		foreach ($aMessagesIds as $iMessageId) {
			$oResults = $wpdb->get_results("SELECT users_dismissed FROM $sTablename WHERE ID = " . $iMessageId);
		
			foreach ($oResults as $oRow) {
				if ($oRow->users_dismissed == "") {
					$aUserDissmissed[] = get_current_user_id();

					$aUserDissmissed = array_unique($aUserDissmissed, SORT_REGULAR);

					$wpdb->update($sTablename, 
				    array('users_dismissed' => json_encode($aUserDissmissed)), 
				    array('ID' => $iMessageId)
				  );
				} else {
					$aUserDissmissed = json_decode($oRow->users_dismissed, true);
					$aUserDissmissed[] = get_current_user_id();

					$aUserDissmissed = array_unique($aUserDissmissed, SORT_REGULAR);

					$wpdb->update($sTablename, 
				    array('users_dismissed' => json_encode($aUserDissmissed)), 
				    array('ID' => $iMessageId)
				  );
				}
		  }
		}
  }

	/*
		HOOK FUNCTIONS START
	*/
  function addDashboardMessagesMenuItem() 
	{
		//Add to the admin menu if we are the main network admin aka super admin
		if (is_super_admin(get_current_user_id())) {
	    add_menu_page('Dashboard Messages Manage', 'Dashboard Messages', 'manage_options', 'dashboard-message-manage', array('WFUDashboardMessage', 'dashboardMessageManageInit'));
	    add_submenu_page('dashboard-message-manage', 'Add New Message', 'Add New Message', 'administrator', 'dashboard-message-add-new', array('WFUDashboardMessage', 'dashboardMessageAddInit'));
		}
	}

	//when they visit the add link, load this view file
	function dashboardMessageManageInit() 
	{
		$oDashboardMessage = new WFUDashboardMessage;

		//If clicked on delete try to delete this message
		if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
			$oDashboardMessage->delete($_GET['id']);

			$oDashboardMessage->getAll();
	    require_once(plugin_dir_path( __FILE__ ) . '/views/dashboard-messages/dashboard-message-manage.php');
	  //If clicked on edit load the message and edit page
	  } else if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
	    $aMessage = $oDashboardMessage->get($_GET['id']);
	    
	    if (!is_null($aMessage)) {
	      $aDashboardMessageToEdit = $aMessage;
	      require_once(plugin_dir_path( __FILE__ ) . '/views/dashboard-messages/dashboard-message-edit.php');
	    } else {
	    	//if invalid id, open the manage page
	  		$oResults = $oDashboardMessage->getAll();

	      require_once(plugin_dir_path( __FILE__ ) . '/views/dashboard-messages/dashboard-message-manage.php');
	    }
	  } else {
	  	$oResults = $oDashboardMessage->getAll();

	    require_once(plugin_dir_path( __FILE__ ) . '/views/dashboard-messages/dashboard-message-manage.php');
	  }
	}

	//when they visit the add message link, load this view file
	function dashboardMessageAddInit() 
	{
	  require_once(plugin_dir_path( __FILE__ ) . '/views/dashboard-messages/dashboard-message-add.php');
	}

	//function to save the new message
	function dashboardMessageSavePost() 
	{
	  if(!isset($_POST['action']) || $_POST['action'] !== "dashboardMessageAddForm") {
	    return;
	  }
	  
	  $oDashboardMessage = new WFUDashboardMessage;

		$oDashboardMessage->add($_POST['start_date'], $_POST['end_date'], $_POST['message'], $_POST['scope']);

	  wp_redirect('./wp-admin/admin.php?page=dashboard-message-manage');
	}

	//function to update a message
	function dashboardMessageUpdatePost() 
	{
	  if(!isset($_POST['action']) || $_POST['action'] !== "dashboardMessageUpdateForm") {
	    return;
	  }
	  
	  $oDashboardMessage = new WFUDashboardMessage;

	 	$oDashboardMessage->update($_POST['dashboardMessageId'], $_POST['start_date'], $_POST['end_date'], $_POST['message'], $_POST['scope']);

	  wp_redirect('./wp-admin/admin.php?page=dashboard-message-manage');
	}

	//Main logic to show messages
	function WFUDashboardMessageInit() 
	{
		//super admins don't see these messages
		if (!is_super_admin(get_current_user_id())) {
	  	$oDashboardMessage = new WFUDashboardMessage;
			$oDashboardMessage->loadDashboardMessages();
		}
	}

	//Called via AJAX to mark when a user dismisses the link
	function dismissWFUDashboardMessage() 
	{
		$aMessagesIds = json_decode(str_replace('\\', "", $_REQUEST['sMessagesIds']), true);
		
		$oDashboardMessage = new WFUDashboardMessage;
		$oDashboardMessage->dismissMessages($aMessagesIds);
	}
}
?>