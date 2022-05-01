<?php

/**
 * The core theme class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-torit.php';



function init_torit() {

	$torit = new Torit();
	$torit->run();
	return $torit;

}
//$torit = init_torit();


