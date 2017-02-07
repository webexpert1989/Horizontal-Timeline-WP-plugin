<?php
/**************************************************************
 *
 * load assets class for timeline list admin/front page 
 *
 **************************************************************/


// check T_assets is available
if(!class_exists('T_assets')){

	// CREATE A PACKAGE CLASS
	class T_assets{
				
		/**
		 * constuct
		 * @return:	void
		 */
		function __construct(){
			return;
		}
				
		/**
		 * add javascript & css files to wordpress
		 *
		 * @return:	void
		 */
		public function add_assets(){
			
			$this->add_js();
			$this->add_css();

			return;
		}
				
		/**
		 * add javascript & css files to wordpress admin page
		 *
		 * @return:	void
		 */
		public function add_admin_assets(){
			
			$this->add_admin_js();
			$this->add_admin_css();

			return;
		}
				
		/**
		 * register & active javascript files
		 *
		 * @return:	void
		 */
		protected function add_js(){
            
			wp_register_script('T_js_lib_jquery_mobile', T_ASSETS.'lib/jquery.mobile.custom.min.js');
			wp_enqueue_script('T_js_lib_jquery_mobile', false, array(), false, true);
            
			wp_register_script('T_js_modernizr', T_ASSETS.'js/modernizr.js');
			wp_enqueue_script('T_js_modernizr', false, array(), false, true);
            
			wp_register_script('T_js_main', T_ASSETS.'js/main.js');
			wp_enqueue_script('T_js_main', false, array(), false, true);
            
			return;
		}
		
		/**
		 * register & active css files
		 *
		 * @return:	void
		 */
		protected function add_css(){
            
			wp_register_style('T_css', T_ASSETS.'css/style.css'); 
			wp_enqueue_style('T_css');

			return;

		}
        
        /**
		 * register & active javascript files for admin page
		 *
		 * @return:	void
		 */
		protected function add_admin_js(){
            
			wp_register_script('T_admin_jquery_ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js');
			wp_enqueue_script('T_admin_jquery_ui');
            
			wp_register_script('T_admin_js', T_ASSETS.'js/admin.js');
			wp_enqueue_script('T_admin_js');
			
			wp_localize_script('T_admin_js', 'global_var', 
				array(
					//To use this variable in javascript use "youruniquejs_vars.ajaxurl"
					'ajaxurl' => admin_url('admin-ajax.php'),
					//To use this variable in javascript use "youruniquejs_vars.the_issue_key"
					'the_issue_key' => $the_issue_key,
				) 
			); 

			return;
		}
		
		/**
		 * register & active css files for admin page
		 *
		 * @return:	void
		 */
		protected function add_admin_css(){

			wp_register_style('T_admin_jquery_ui_css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'); 
			wp_enqueue_style('T_admin_jquery_ui_css');

			wp_register_style('T_admin_css', T_ASSETS.'css/admin.css'); 
			wp_enqueue_style('T_admin_css');

			return;

		}
	}
}

?>