<?php 
//If ACF isn't installed and or deactivated, tell them to add or activate it first.
if (!class_exists('ACF')) {
	echo '<p>This plugin requires the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields (ACF)</a> plugin. Please install and activate this plugin before using Resource Links.</p>';
	exit;
} else {
	//render the add form
	echo '<h1 class="wp-heading-inline">Update Message</h1>';
	$aSites[0] = 'Global';

	foreach (get_sites() as $oSite) {
		if ($oSite->blog_id != 1) {
			$aSites[$oSite->blog_id] = $oSite->domain . $oSite->path;
		}
	}

	acf_add_local_field_group(array(
		'key' => 'dashboardMessagesFieldGroups',
		'title' => 'Dashboard Message',
		'fields' => array(
			array(
				'key' => 'dashboardMessageScope',
				'label' => 'Scope',
				'name' => 'scope',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => $aSites,
				'default_value' => array(
					0 => 'Global',
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'dashboardMessageStartDate',
				'label' => 'Start Date',
				'name' => 'start_date',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'F j, Y',
				'return_format' => 'd/m/Y',
				'first_day' => 1,
			),
			array(
				'key' => 'dashboardMessageEndDate',
				'label' => 'End Date',
				'name' => 'end_date',
				'type' => 'date_picker',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'display_format' => 'F j, Y',
				'return_format' => 'd/m/Y',
				'first_day' => 1,
			),
			array(
				'key' => 'dashboardMessageMessage',
				'label' => 'Message',
				'name' => 'message',
				'type' => 'textarea',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'new_lines' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => array(
			0 => 'permalink',
			1 => 'the_content',
			2 => 'excerpt',
			3 => 'discussion',
			4 => 'comments',
			5 => 'revisions',
			6 => 'slug',
			7 => 'author',
			8 => 'format',
			9 => 'page_attributes',
			10 => 'featured_image',
			11 => 'categories',
			12 => 'tags',
			13 => 'send-trackbacks',
		),
	)
);

$aFields = acf_get_fields("dashboardMessagesFieldGroups");

echo '<form id="dashboardMessageUpdateForm" name="dashboardMessageUpdateForm" action="' . esc_url( admin_url('admin-post.php') ) . '" method="POST">';
 	echo '<input type="hidden" name="action" value="dashboardMessageUpdateForm">';
 	echo '<input type="hidden" name="dashboardMessageId" value="' . $aDashboardMessageToEdit['ID'] . '">';
  foreach ($aFields as $aFields => $aData){
    echo '<label class="field_label">' . $aData['label'] . '</label><br/>';
    if ($aData['name'] == 'scope') {
    	echo '<select name="' . $aData['name'] . '">';
    		foreach ($aData['choices'] as $iKey => $sValue) {
    			if ($iKey == $aDashboardMessageToEdit['sites']) {
    				echo '<option value="' . $iKey . '" selected>' . $sValue . '</option>';
    			} else {
    				echo '<option value="' . $iKey . '">' . $sValue . '</option>';
    			}
    		}
    	echo '</select><br/>';
    } else if ($aData['name'] == 'message') {
    	echo '<textarea name="' . $aData['name'] . '" required="required">' . $aDashboardMessageToEdit['message'] . '</textarea>';
    } else if ($aData['name'] == 'start_date') {
    	echo '<input id="' . $aData['name'] . '" name="' . $aData['name'] . '" type="text" required="required" value="' . date("F j, Y", $aDashboardMessageToEdit['start']) . '"><br/>';
    } else {
    	echo '<input id="' . $aData['name'] . '" name="' . $aData['name'] . '" type="text" required="required" value="' . date("F j, Y", $aDashboardMessageToEdit['end']) . '"><br/>';
    }
  }
  echo "<br/><br/><button class='button button-primary button-large' type='submit'>Update Message</button>";
echo '</form>';

}?>
<script type="text/javascript">
	jQuery(document).ready(function($){
	  jQuery('#start_date').datepicker({dateFormat : "MM d, yy"});
	  jQuery('#end_date').datepicker({dateFormat : "MM d, yy"});
	});
</script>