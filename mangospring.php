<?php

/*
Plugin Name: WordPress to Mango Spring/Mango Apps
Plugin URI: http://squeed.com/
Description: Posts updates to mangospring
Version: 0.1
Author: Fredrik Wendt, Squeed
License: Apache Public License 2.0
*/

function mangospring_publish_post($post_ID)  {
	$url = get_permalink($post_ID);
	$post = get_post($post_ID);

	$content = "New blog post: $post->post_title\n\nRead it at $url";
	mangospring_publish_status_update($content);
}

function mangospring_client() {
	$domain = get_option("mangospring_domain");
	$username = get_option("mangospring_username");
	$password = get_option("mangospring_password");
	$api_key = get_option("mangospring_api_key");

	$mango = new Mango($domain, $username, $password, $api_key);
	
	return $mango;
}

function mangospring_publish_status_update($content) {

	$mango = mangospring_client();

	$mango->login();
	$private_wall = get_option("mangospring_private_wall");
	$group_id = get_option("mangospring_group_id");
	$project_id = get_option("mangospring_project_id");

	if (isset($private_wall) && $private_wall === 'TRUE') {
		$mango->post_status_update($content);
	}
	if (isset($group_id)) {
		$mango->post_status_update_on_group_wall($group_id, $content);
	}
	if (isset($project_id)) {
		$mango->post_status_update_on_project_wall($project_id, $content);
	}
	$mango->logout();
}

function mangospring_activation_hook()  {
	add_option("mangospring_username", "Username", "The username of the user to login as, such as first.last@example.com");
	add_option("mangospring_password", "Password", "The password of the user to login as");
	add_option("mangospring_domain", "Domain", "The domain of the user to login as, such as example.mangospring.com");
	add_option("mangospring_api_key", "API Key", "The secret part of an API key, 16 digits");
	add_option("mangospring_group_id", "", "Optional. If you want to post status updates to the wall of a group, enter that group's ID here");
	add_option("mangospring_project_id", "", "Optional. If you want to post status updates to the wall of a project, enter that project's ID here");
	add_option("mangospring_private_wall", "TRUE", "Enable this to post an entry on the mangospring user account's private wall");
}

include("mangolib.php");
include("mangospring-admin-page.php");
add_action('publish_post', 'mangospring_publish_post');
add_action('admin_menu', 'mangospring_admin_menu');
register_activation_hook(__FILE__, 'mangospring_activation_hook');
?>
