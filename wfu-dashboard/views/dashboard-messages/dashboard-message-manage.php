<div class="wrap">
<h1 class="wp-heading-inline">Dashboard Messages</h1>
<?php 
//If ACF isn't installed and or deactivated, tell them to add or activate it first.
if (!class_exists('ACF')) {
	echo '<p>This plugin requires the <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Advanced Custom Fields (ACF)</a> plugin. Please install and activate this plugin before using Resource Links.</p>';
	exit;
} else { 
	global $wpdb;
  $sTablename = $wpdb->prefix . 'wfu_dashboard_messages';

  $oResults = $wpdb->get_results("SELECT * FROM $sTablename");
  ?>
	<br/><br/>
	<a class="button button-primary button-large" href="/wp-admin/admin.php?page=dashboard-message-add-new">Add New</a>
	<?php 
	$oAllSites = get_sites();
	if(!empty($oResults) && count($oResults) > 0) { ?>
	<br/><br/>
		<table class="wp-list-table widefat fixed striped users">
			<thead>
				<tr>
					<td>Message</td>
					<td>Starts</td>
					<td>Ends</td>
					<td>Site</td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($oResults as $oRow) { ?>
					<tr>
						<td>
							<?php echo mb_strimwidth($oRow->message, 0, 50, "..."); ?>
							<div class="row-actions">
								<span class="edit"><a href="/wp-admin/admin.php?page=dashboard-message-manage&action=edit&id=<?php echo $oRow->ID; ?>">Edit</a> |</span>
								<span class="trash"><a href="/wp-admin/admin.php?page=dashboard-message-manage&action=delete&id=<?php echo $oRow->ID; ?>">Delete</a></span>
							</div>
						</td>
						<td><?php echo date("F j, Y", $oRow->start); ?></td>
						<td><?php echo date("F j, Y", $oRow->end); ?></td>
						<td>
							<?php 
							if ($oRow->sites == 0) {
								echo "Global";
							} else {
								foreach ($oAllSites as $key => $oSite) {
									if ($oSite->blog_id == $oRow->sites) {
										echo $oSite->domain . $oSite->path;
										unset($oAllSites['key']);
										break;
									}
								}
							}
							?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } else {
		echo "<br/><br/>No dashboard messages created yet.";
	} 
}?>
</div>