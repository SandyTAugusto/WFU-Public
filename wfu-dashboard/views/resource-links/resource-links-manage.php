<div class="wrap">
<h1 class="wp-heading-inline">Resource Links</h1>
<?php 
//If ACF isn't installed and or deactivated, tell them to add or activate it first.
if (!class_exists('ACF')) {
	echo '<p>This plugin requires the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields (ACF)</a> plugin. Please install and activate this plugin before using Resource Links.</p>';
	exit;
} else { ?>
	<br/><br/>
	<a class="button button-primary button-large" href="/wp-admin/admin.php?page=resource-links-add-new">Add New</a>
  <?php if(!empty($oResults) && count($oResults) > 0) { ?>
		<br/><br/>
		<table class="wp-list-table widefat fixed striped users">
			<thead>
				<tr>
					<td>Label</td>
					<td>Link</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($oResults as $oRow) { ?>
					<tr>
						<td>
							<?php echo $oRow->name; ?>
							<div class="row-actions">
								<span class="edit"><a href="/wp-admin/admin.php?page=resource-links-manage&action=edit&id=<?php echo $oRow->ID; ?>">Edit</a> |</span>
								<span class="trash"><a href="/wp-admin/admin.php?page=resource-links-manage&action=delete&id=<?php echo $oRow->ID; ?>">Delete</a></span>
							</div>
						</td>
						<td><a href="<?php echo $oRow->link; ?>"><?php echo $oRow->link; ?></a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } else {
		echo "<br/><br/>No resource links created yet.";
	}
} ?>
</div>