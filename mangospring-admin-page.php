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
	mangospring_field("mangospring_password", "password", "Will be base64 encoded in database");
}
function mangospring_field_domain() {
	mangospring_field("mangospring_domain");
}
function mangospring_field_api_key() {
	mangospring_field("mangospring_api_key");
}
function mangospring_field($name, $type='text', $extra='') {
	$value = get_option($name);
	echo "<input size='35' id='$name' type='$type' name='$name' value='$value' /> $extra";
}

function mangospring_empty() {
	return "<p>Empty</p>";
}

function mangospring_admin_init() {
	register_setting("mangospring", "mangospring_username");
	register_setting("mangospring", "mangospring_password", 'mangospring_sanitize_password');
	register_setting("mangospring", "mangospring_domain");
	register_setting("mangospring", "mangospring_api_key");

	add_settings_section('mangospring', 'MangoSpring MangoApps Settings', 'mangospring_empty', "mangospring");
	add_settings_field("mangospring_username", "Username", "mangospring_field_username", "mangospring", "mangospring");
	add_settings_field("mangospring_password", "Password", "mangospring_field_password", "mangospring", "mangospring");
	add_settings_field("mangospring_domain", "Domain", "mangospring_field_domain", "mangospring", "mangospring");
	add_settings_field("mangospring_api_key", "API key", "mangospring_field_api_key", "mangospring", "mangospring");
}

add_action('admin_menu', 'mangospring_admin_menu');
add_action('admin_init', 'mangospring_admin_init');
?>
