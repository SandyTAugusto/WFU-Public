<?php

/*
	REVIEW: Nearlly 100% of the operations of this file would have been simplier 
	and safer to implement via custom WP API endpoints, unless that was precluded by WFU
*/
//Class for resource links, handles the links object and deals with the hook functions as well.
class WFUResourceLink {

	public function add($sName, $sUrl) 
	{
		global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_resource_links';

	  $wpdb->insert($sTablename, array(
	    'name' => $sName, 
	    'link' => $sUrl)
	  );
  }

  public function update($iId, $sName, $sUrl) 
  {
  	global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_resource_links';

	  $wpdb->update($sTablename, 
	    array('name' => $sName, 
	    'link' => $sUrl), 
	    array('ID' => $iId)
	  );
  }

  public function delete($iLinkId) 
  {
		/*
		REVIEW: I probably would have set the table name as a class variable
		*/
  	global $wpdb;
    $sTablename = $wpdb->prefix . 'wfu_resource_links';

		/*
			REVIEW: Generally Custom DB queries are supposed to be run though $wpdb->prepare
		*/
    $wpdb->query("DELETE FROM $sTablename WHERE ID = " . $iLinkId);
  }

  public function get($iLinkId) 
  {
  	global $wpdb;
    $sTablename = $wpdb->prefix . 'wfu_resource_links';

		/*
		REVIEW no use of $wpdb->prepare
		*/
		$oResults = $wpdb->get_results("SELECT * FROM $sTablename where ID = " . $iLinkId);
		
		/*
			REVIEW (Personal preference?) I generally set a default value rather than leveraging else statements
			Also easy to generate early returns and catch posibilities of invalid variabls.
		*/
    if(!empty($oResults) && count($oResults) > 0) {
    	return (Array) $oResults[0];
    } else {
    	return NULL;
    }
  }

  public function getAll() 
  {
  	global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_resource_links';

	  $oResults = $wpdb->get_results("SELECT * FROM $sTablename");

	  return $oResults;
  }

  public function setMetaboxContent() 
  {
		global $wpdb;
	  $sTablename = $wpdb->prefix . 'wfu_resource_links';

	  $oResults = $wpdb->get_results("SELECT * FROM $sTablename");

	  if(!empty($oResults) && count($oResults) > 0) {
    	global $sResourceLinksMetaBoxContent;

	    $sReturnedResourceLinks = "";
	    foreach ($oResults as $oRow) {
	        $sReturnedResourceLinks .= '<a href="' . $oRow->link . '">' . $oRow->name . '</a><br/>';
	    }

	    $sResourceLinksMetaBoxContent = '<p>' . $sReturnedResourceLinks . '</p>';

	  	return TRUE;
	  } else {
	  	return FALSE;
	  }
  }

  /*
		HOOK FUNCTIONS START
	*/
	function resourceSavePost() 
	{
	  if(!isset($_POST['action']) || $_POST['action'] !== "resourceLinksAddForm") {
	    return;
	  }
	  
		$oResourceLink = new WFUResourceLink;

		$oResourceLink->add($_POST['linkName'], $_POST['linkURL']);

	  wp_redirect('./wp-admin/admin.php?page=resource-links-manage');
	}

	function resourceUpdatePost() 
	{
	  if(!isset($_POST['action']) || $_POST['action'] !== "resourceLinksUpdateForm") {
	    return;
	  }
		$oResourceLink = new WFUResourceLink;

		$oResourceLink->update($_POST['resourceLinkId'], $_POST['linkName'], $_POST['linkURL']);

	  wp_redirect('./wp-admin/admin.php?page=resource-links-manage');
	}


	function addResourceLinksMenuItem() 
	{
		if (is_super_admin(get_current_user_id())) {
	    add_menu_page('Resource Links Manage', 'Resource Links', 'manage_options', 'resource-links-manage', array('WFUResourceLink', 'resourceLinksManageInit'));
	    add_submenu_page( 'resource-links-manage', 'Add New Links', 'Add New Links', 'administrator', 'resource-links-add-new', array('WFUResourceLink', 'resourceLinksAddInit'));
		}
	}

	//when they visit the main link, load this view file
	function resourceLinksManageInit() 
	{
		$oResourceLink = new WFUResourceLink;

		/*
			REVIEW: Un-validated and santized parameters passed directly to DB operations
			Complex and muddy nested if/else statements, with no clear return statement
		*/
	  //if they click the delete link try to delete this resource link
	  if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
	    $oResourceLink->delete($_GET['id']);
	  	$oResults = $oResourceLink->getAll();

	    require_once(plugin_dir_path( __FILE__ ) . '/views/resource-links/resource-links-manage.php');
	  } else if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
	    $aLink = $oResourceLink->get($_GET['id']);
	    
	    if (!is_null($aLink)) {
	      $aResourceLinkToEdit = $aLink;
	      require_once(plugin_dir_path( __FILE__ ) . '/views/resource-links/resource-links-edit.php');
	    } else {
	    	//if invalid id, open the manage page
	  		$oResults = $oResourceLink->getAll();

	      require_once(plugin_dir_path( __FILE__ ) . '/views/resource-links/resource-links-manage.php');
	    }
	  } else {
	  	$oResults = $oResourceLink->getAll();

	    require_once(plugin_dir_path( __FILE__ ) . '/views/resource-links/resource-links-manage.php');
	  }
	}

	//when they visit the add link, load this view file
	function resourceLinksAddInit() 
	{
	  require_once(plugin_dir_path( __FILE__ ) . '/views/resource-links/resource-links-add.php');
	}

	//fetch any resource links if we have them, if none don't render metabox
	function loadResourceLinksMetabox() 
	{
		$oResourceLink = new WFUResourceLink;

	  $iResults = $oResourceLink->setMetaboxContent();
	  if($iResults === TRUE) {
		  global $wp_meta_boxes;

	  	wp_add_dashboard_widget('resourceLinks', 'Resource Links', array('WFUResourceLink', 'addResourceLinksContent'));
	  }
	}

	//WP structing renderings box, content inside box afterwords
	function addResourceLinksContent() 
	{
	    global $sResourceLinksMetaBoxContent;
	    echo $sResourceLinksMetaBoxContent;
	}
}
?>