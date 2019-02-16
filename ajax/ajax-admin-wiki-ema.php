<?php
add_action('admin_footer', 'my_action_javascript'); // Write our JS below here

function my_action_javascript()
{?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var data = {
			'action': 'my_action',
			'whatever': 1234
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(resposta) {
			//alert('Got this from the server: ' + resposta);
		});
	});
	</script>
    <?php
}

add_action( 'wp_ajax_my_action', 'my_action' );

function my_action() {
	global $wpdb; // this is how you get access to the database


        echo 'tamo aqui';

	wp_die(); // this is required to terminate immediately and return a proper response
}