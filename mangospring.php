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
	file_put_contents("/tmp/mango.txt", "$post_ID, $url -> " . $post->post_title);
}

add_action('publish_post', 'mangospring_publish_post');
