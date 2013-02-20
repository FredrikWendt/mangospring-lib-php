<?php

function mangospring_admin_menu() {
	add_options_page(
		"MangoApps Settings Page", // title
		"MangoApps", // submenu title
		"manage_options", // access/capability
		__FILE__, // file
		'mangospring_manage_options' // function
	);
}

function mangospring_manage_options() {
?>
	<div class="wrap">
		<h2><?php _e("MangoSpring Settings", "mangospring") ?></h2>
		<form action="options.php" method="post">
		<?php
		settings_fields('mangospring');
		do_settings_sections('mangospring');
		?>
		<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'mangospring'); ?>" />
		</form>
	</div>

<?php
	if (!isset($_POST["mangospring_test"])) {
?>
	<div class="wrap">
		<h2><?php _e("Test Integration", "mangospring") ?></h2>
		<p>N.B.: Please save settings above using the Save Settings button above prior to testing the settings.</p>
		<h3>Read Only Test</h3>
		<p>This test will try to login using the credentials supplied above, and then get a list of the groups the user account is a member of.</p>
		<form method="POST">
			<input type='hidden' name='mangospring_test' value='read' />
			<input type="submit" class="button-secondary" value="<?php esc_attr_e('Perform Read Only Test', 'mangospring'); ?>" />
		</form>
		<h3>Read And Write Test</h3>
		<p>This test will login and post a status update according to settings (private, group and project walls), with the content supplied in the input field below.</p>
		<form method="POST">
			<input type='hidden' name='mangospring_test' value='write' />
<input type='text' id='mangospring_content' name='mangospring_content' value="This text here will be posted to the wall(s) on MangoApps." size="50" /><br />
			<input type="submit" class="button-secondary" value="<?php esc_attr_e('Perform Read/Write Test', 'mangospring'); ?>" />
		</form>
	</div>
<?php
	} else {
		$mango = mangospring_client();

		if ("read" === $_POST["mangospring_test"]) {
			$mango->login();
			$groups = $mango->get_my_groups();
			$mango->logout();
			echo "<pre>". print_r($groups, TRUE) ."</pre>";
		} else if ("write" === $_POST["mangospring_test"]) {
			$content = $_POST["mangospring_content"];
			mangospring_publish_status_update($content);
			echo "<p>Posted status update with text '$content' - now go to mango apps and see the magic!</p>";
		} else {
			echo "<p>Unknown test</p>";
		}
	}
}

function mangospring_sanitize_password($password) {
	$original = get_option("mangospring_password");
	// don't encode unless the password has changed/was changed by the user
	if ($original == $password) {
		return $original;
	}

	$encoded = base64_encode($password);
	return $encoded;
}

function mangospring_field_username() {
	mangospring_field("mangospring_username");
}
function mangospring_field_password() {
	mangospring_field("mangospring_password", array("type" => "password", "extra" => "Will be base64 encoded in database"));
}
function mangospring_field_domain() {
	mangospring_field("mangospring_domain");
}
function mangospring_field_api_key() {
	mangospring_field("mangospring_api_key");
}
function mangospring_field_group_id() {
	mangospring_field("mangospring_group_id", array("extra" => "Optional. If you want to post status updates to the wall of a group, enter that group's ID here."));
}
function mangospring_field_project_id() {
	mangospring_field("mangospring_project_id", array("extra" => "Optional. If you want to post status updates to the wall of a project, enter that project's ID here."));
}
function mangospring_field_private_wall() {
	$name = "mangospring_private_wall";
	$value = get_option($name);
	if ($value === "TRUE") {
		$checked = " checked";
	}
	echo "<input id='$name' type='checkbox' name='$name' value='TRUE' $checked /> \n";
}
function mangospring_field($name, $options="") {
	$type = "text";
	$extra = "";
	if (is_array($options)) {
		if (isset($options["extra"])) {
			$extra = $options["extra"];
		}
		if (isset($options["type"])) {
			$type = $options["type"];
		}
	}
	$value = get_option($name);
	echo "<input size='35' id='$name' type='$type' name='$name' value='$value' /> $extra\n";
}

function mangospring_empty() {
	return "<p>Empty</p>";
}

function mangospring_admin_init() {
	register_setting("mangospring", "mangospring_username");
	register_setting("mangospring", "mangospring_password", 'mangospring_sanitize_password');
	register_setting("mangospring", "mangospring_domain");
	register_setting("mangospring", "mangospring_api_key");
	register_setting("mangospring", "mangospring_group_id");
	register_setting("mangospring", "mangospring_project_id");
	register_setting("mangospring", "mangospring_private_wall");

	add_settings_section('mangospring', 'MangoSpring MangoApps Account Settings', 'mangospring_empty', "mangospring");
	add_settings_field("mangospring_username", "Username", "mangospring_field_username", "mangospring", "mangospring");
	add_settings_field("mangospring_password", "Password", "mangospring_field_password", "mangospring", "mangospring");
	add_settings_field("mangospring_domain", "Domain", "mangospring_field_domain", "mangospring", "mangospring");
	add_settings_field("mangospring_api_key", "API key", "mangospring_field_api_key", "mangospring", "mangospring");
	add_settings_section('mangospring', 'MangoSpring MangoApps Account Settings', 'mangospring_empty', "mangospring");
	add_settings_field("mangospring_private_wall", "Private Wall", "mangospring_field_private_wall", "mangospring", "mangospring");
	add_settings_field("mangospring_group_id", "Group Wall", "mangospring_field_group_id", "mangospring", "mangospring");
	add_settings_field("mangospring_project_id", "Project Wall", "mangospring_field_project_id", "mangospring", "mangospring");
}

add_action('admin_menu', 'mangospring_admin_menu');
add_action('admin_init', 'mangospring_admin_init');
?>
