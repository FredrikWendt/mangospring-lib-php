Goal
----

Post a status update for a published blog post''s title (with the permalink) on a project or group wall in mangosapps.com.


TODO
----

Should:
  * Add configuration of mangoapps settings:
   * project or group to post to (or none, to post to the user''s wall)
  
Nice:
  * Add a "Test Configuration" button/step

Done:
  * Contains wordpress plugin configuration page, with the following settings:
   * domain, username, password, api_key


Installation
------------

Right now, you need to manually clone this into a directory in the plugins directory: ```.../wp-content/plugins/mangospring/``` to get it to work with WordPress.

Other Usage
-----------

This lib also works for posting git commit messages from GitHub to a project wall. Code:

	<?php

	include("/opt/mangospring-lib-php/mangolib.php");

	// GitHub sends input (payload) as POST
	if (isset($_POST["payload"])) {
		$json = json_decode($_POST["payload"]);
	} else {
		// for testing purpose, put the JSON dump from GitHub in a local file
		$json = json_decode(file_get_contents("github-payload-example.json"));
	}
	$message = "Git push notification for ". $json->repository->url .":\n";
	foreach ($json->commits as $i => $commit) {
		$message .= $commit->author->name . ": " . $commit->message ."\n";
	}

	$project_id = 123456;
	$mango = new Mango("DOMAIN.mangospring.com", "USERNAME", base64_encode("PASSWORD"), "API_KEY");
	$mango->login();
	$mango->post_status_update_on_project_wall($project_id, $message);
	$mango->logout();

	?>
