<?php
/*
Plugin Name: Horizontal Scrolling Timeline
Description: wordpress plugin for Horizontal Scrolling Timeline, You can use the plugin with shortcode "[TIMELINE]"
Version: 1.0.1
*/

if(!defined('ABSPATH')){
	exit;
}

// Let's go!
if(class_exists('T_init')){
	new T_init();
}

// manula builder class
class T_init{
	
	/**
	 * Our plugin version
	 *
	 * @var string
	 */
	public static $version = '1.0.1';

	/**
	 * Our plugin file
	 *
	 * @var string
	 */
	public static $plugin_file = __FILE__;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct(){
		
		// load settings & install function 
		require_once(dirname(__FILE__).'/settings.php');

		// Activation and uninstall hooks
		register_activation_hook(__FILE__, array(__CLASS__, 'do_activation'));
		register_uninstall_hook( __FILE__, array(__CLASS__, 'do_uninstall'));

		// Load dependancies
		$this->load_dependancies();

		// Load templates
		$this->load_templates();

		// Setup localization
		$this->set_locale();

		// Define hooks
		$this->define_hooks();

		// Define Ajax
		$this->define_ajax();

		// Define Shortcode
		$this->define_shortcode();

	}

	/**
	 * Activation
	 *
	 * @return void
	 */
	public static function do_activation(){

		global $wp_version;

		// Deactivate the plugin if the WordPress version is below the minimum required.
		if (version_compare($wp_version, '4.0', '<')){
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(__(sprintf('Sorry, but your version of WordPress, <strong>%s</strong>, is not supported. The plugin has been deactivated. <a href="%s">Return to the Dashboard.</a>', $wp_version, admin_url()), 'timeline'));
			return false;
		}

		// Add options
		add_option('T_version', self::$version);

		// Trigger hooks
		do_action('T_activate');

	}

	/**
	 * Uninstall
	 *
	 * @return void
	 */
	public static function do_uninstall(){

		// Get the settings
		global $T_conf;

		// If enabled, remove the plugin data
		if ($T_conf['remove_data']){
            
            $posts = get_posts(array(
                'post_type'      => 'timeline', 
                'posts_per_page' => -1,
                'post_status'    => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')  
            ));
            
			// Delete all of the datas
			foreach($posts as $t){
				wp_delete_post($t->ID, true); 
			}

			// Delete options
			delete_option('T_version');
		}
        
        return;
    }

	/**
	 * Load dependancies
	 *
	 * @return void
	 */
	protected function load_dependancies(){
        
		require_once(T_MODULE.'class-global.php');
		require_once(T_MODULE.'class-ajax.php');
		require_once(T_MODULE.'class-shortcode.php');
		require_once(T_MODULE.'class-menu.php');
		require_once(T_MODULE.'class-assets.php');
		require_once(T_MODULE.'class-list.php');
		require_once(T_MODULE.'class-template.php');

	}

	/**
	 * Load templates
	 *
	 * @return void
	 */
	protected function load_templates(){
		
		require_once(T_TEMPLATE.'admin-edit.php');
		require_once(T_TEMPLATE.'admin-list.php');
		require_once(T_TEMPLATE.'view.php');

	}

	/**
	 * Set locale
	 *
	 * @return void
	 */
	protected function set_locale(){

		// Load plugin textdomain
		load_plugin_textdomain('timeline', false, T_LANGUAGE);

	}

	/**
	 * Define menu hooks
	 *
	 * @return void
	 */
	protected function define_hooks(){

		// Initiate components
		$menu = new T_admin_menu();
		$assets = new T_assets();

		/**
		 * Hook everything, "connect all the dots"!
		 *
		 * All of these actions connect the various parts of our plugin together.
		 * The idea behind this is to keep each "component" as separate as possible, decoupled from other components.
		 *
		 * These hooks bridge the gaps.
		 */
		add_action('admin_menu', array($menu, 'add_toplevel_menu'));
		add_action('admin_enqueue_scripts', array($assets, 'add_admin_assets'));
        
        ///
		add_action('wp_enqueue_scripts', array($assets, 'add_assets'));

	}

	/**
	 * Define ajax hooks
	 *
	 * @return void
	 */
	protected function define_ajax(){

		// Initiate components
		$ajax = new T_ajax();
		
		add_action('wp_ajax_timeline_edit', array($ajax, 'timeline_edit'));
		add_action('wp_ajax_nopriv_timeline_edit', array($ajax, 'timeline_edit'));

		add_action('wp_ajax_timeline_new', array($ajax, 'timeline_new'));
		add_action('wp_ajax_nopriv_timeline_new', array($ajax, 'timeline_new'));

		add_action('wp_ajax_timeline_del', array($ajax, 'timeline_del'));
		add_action('wp_ajax_nopriv_timeline_del', array($ajax, 'timeline_del'));

	}

	/**
	 * Define shortcode hooks
	 *
	 * @return void
	 */
	protected function define_shortcode(){

		// Initiate components
		$shortcode = new T_shortcode();
		
		add_shortcode(T_SHORTCODE, array($shortcode, 'shortcode'));
	}
}
?>