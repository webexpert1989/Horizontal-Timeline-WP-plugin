<?php
/**************************************************************
 *
 * menu class for admin page 
 *
 **************************************************************/


// check T_admin_menu is available
if(!class_exists('T_admin_menu')){

	// CREATE A PACKAGE CLASS
	class T_admin_menu{
		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			return;
		}
		
		/**
		 * add top level menus in wordpress admin page
		 *
		 * @return:	void
		 */
		public function add_toplevel_menu(){

			global $menu;

			// Default menu positioning
			$position = '100.1';

			// If enabled, relocate the plugin menus higher
			if(apply_filters('T_relocate_menus', __return_true())){

				for($position = '40.1'; $position <= '100.1'; $position += '0.1'){

					// Ensure there is a space before and after each position we are checking, leaving room for our separators.
					$before = $position - '0.1';
					$after  = $position + '0.1';

					// Do the checks for each position. These need to be strings, hence the quotation marks.
					if(isset($menu[ "$position" ])){
						continue;
					}
					if(isset($menu[ "$before" ])){
						continue;
					}
					if(isset($menu[ "$after" ])){
						continue;
					}

					// If we've successfully gotten this far, break the loop. We've found the position we need.
					break;
				}
			}

			// page class
			$page = new T_page();

			// page ID settings
			global $T_conf;

			// add top level menu
			add_menu_page(
				__('Timeline', 'Timeline'), 
				__('Timeline', 'Timeline'), 
				'administrator', 
				$T_conf['pages']['list'], 
				array($page, 'pages'), 
				T_PLUGIN_URL.'timeline.png', 
				$position
			);

			// Do action allowing extension to add their own toplevel menus
			do_action('T_add_toplevel_menu', $position);

			// Add the menu separators if menus have been relocated (they are by default). Quotations marks ensure these are strings.
			if(apply_filters('T_relocate_menus', __return_true())){
				$this->add_menu_separator("$before");
				$this->add_menu_separator("$after");
			}
			
			return;
		}
		
		/**
		 * Create a separator in the admin menus, above and below our plugin menus
		 *
		 * @param  string $position The menu position to insert the separator
		 * @return void
		 */
		protected function add_menu_separator($position = '40.1'){

			global $menu;

			$index = 0;
			foreach($menu as $offset => $section){

				if('separator' == substr($section[2], 0, 9)){
					$index++;
				}

				if($offset >= $position){

					// Quotation marks ensures the position is a string. Integers won't work if we are using decimal values.
					$menu[ "$position" ] = array('', 'read', "separator{$index}", '', 'wp-menu-separator');
					break;
				}				
			}

			ksort($menu);

			return;
		}
	}
}

?>