<?php 
/**************************************************************
 *
 * Configuration Plugin
 *
 **************************************************************/


/***
 * plugin directories
 */

// plugin root dir
define('T_URL', dirname(__FILE__).'/');

// include dir
define('T_MODULE', T_URL.'inc/');

// include pages
define('T_TEMPLATE', T_URL.'template/');

// load assets dir
define('T_PLUGIN_URL', plugin_dir_url(__FILE__));
define('T_ASSETS', T_PLUGIN_URL.'assets/');

//
define('T_LANGUAGE', 'en');

/***
 * plugin settings
 */

global $T_conf;
$T_conf = array(

	// system conf
	'remove_data'   => true,		// if this is true, remove all datas of database table until uninstalling plugin	

	// default value for custom settings
	'rows_per_page'	=> 20,	// max rows number of list table page
    
    // admin pages
    'pages' => array(
        'list'      => 'T-admin-list',
		'edit'		=> 'T-admin-edit',
    )
);

/***
 * shortcode prefix
 */
define('T_SHORTCODE', 'TIMELINE');

?>