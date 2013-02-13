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

	$domain = get_option("mangospring_domain");
	$username = get_option("mangospring_username");
	$password = get_option("mangospring_password");
	$api_key = get_option("mangospring_api_key");

	$mango = new Mango($domain, $username, $password, $api_key);
	$mango->login();
	$mango->post_status_update("New <b>blog</b> <h2>post</h2>: $post->post_title\n\nRead it all at $url");
}

function mangospring_activation_hook()  {
	add_option("mangospring_username", "Username", "The username of the user to login as, such as first.last@example.com");
	add_option("mangospring_password", "Password", "The password of the user to login as");
	add_option("mangospring_domain", "Domain", "The domain of the user to login as, such as example.mangospring.com");
	add_option("mangospring_api_key", "API Key", "The secret part of an API key, 16 digits");
	add_option("mangospring_group_id", "Group ID", "Optional. If you want to post status updates to the wall of a group, enter that group's ID here");
	add_option("mangospring_project_id", "Project ID", "Optional. If you want to post status updates to the wall of a project, enter that project's ID here");
}

include("mangolib.php");
include("mangospring-admin-page.php");
add_action('publish_post', 'mangospring_publish_post');
add_action('admin_menu', 'mangospring_admin_menu');
register_activation_hook(__FILE__, 'mangospring_activation_hook');
?>
