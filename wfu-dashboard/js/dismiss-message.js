jQuery(document).on('click', '.wfu-dashboard-message .notice-dismiss', function() {
  jQuery.ajax({
    url: ajaxurl,
    data: {
      'action': 'dismissWFUDashboardMessage',
      sMessagesIds: jQuery('#WFUDashboardMessagesId').text() //Had to use a span instead of a hidden input, WP was misformating the JSON by forcing double quotes
    }
  })
})