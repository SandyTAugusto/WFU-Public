<?php 
//If ACF isn't installed and or deactivated, tell them to add or activate it first.
if (!class_exists('ACF')) {
	echo '<p>This plugin requires the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields (ACF)</a> plugin. Please install and activate this plugin before using Resource Links.</p>';
	exit;
} else {
	//render the add form
	echo '<h1 class="wp-heading-inline">Update Resource Link</h1>';
	/*
		REVIEW: Running this function in a view seems perilous
		I may misudnerstand what is happening here but I wouldn't think
		this would need to run every time the view is loaded. 
	*/
	acf_add_local_field_group(array(
		'key' => 'uniqupeResourceLinksGroupKey',
		'title' => 'Resource Links',
		'fields' => array(
			array(
				'key' => 'uniqueResourceLinkNameKey',
				'label' => 'Link Name:',
				'name' => 'linkName',
				'type' => 'text',
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
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'uniqueResourceLinkURLKey',
				'label' => 'Link URL:',
				'name' => 'linkURL',
				'type' => 'url',
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
			)
		),
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

$aFields = acf_get_fields("uniqupeResourceLinksGroupKey");

echo '<form id="resourceLinksUpdateForm" name="resourceLinksUpdateForm" action="' . esc_url( admin_url('admin-post.php') ) . '" method="POST">';
 	echo '<input type="hidden" name="action" value="resourceLinksUpdateForm">';
 	echo '<input type="hidden" name="resourceLinkId" value="' . $aResourceLinkToEdit['ID'] . '">';
  foreach ($aFields as $aFields => $aData){
    echo '<label class="field_label">' . $aData['label'] . '</label><br/>';
    if ($aData['name'] == 'linkURL') {
    	echo '<input id="' . $aData['name'] . '" name="' . $aData['name'] . '" type="url" required="required" value="' . $aResourceLinkToEdit['link'] . '">';
    } else {
    	echo '<input id="' . $aData['name'] . '" name="' . $aData['name'] . '" type="text" required="required" value="' . $aResourceLinkToEdit['name'] . '"><br/>';
    }
  }
  echo "<br/><br/><button class='button button-primary button-large' type='submit'>Update Link</button>";
echo '</form>';

}