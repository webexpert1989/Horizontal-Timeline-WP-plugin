<?php
/**************************************************************
 *
 * shortcode class for timeline app 
 *
 **************************************************************/


// check T_shortcode is available
if(!class_exists('T_shortcode')){

	// CREATE A PACKAGE CLASS
	class T_shortcode{
		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			return;
		}

		/**
		 * put app form to display in the page from entered shortcode
		 *
		 * @param:	array   $attr - shortcode attributes
		 * @param:	string  $content - shortcode contents
		 * @return:	string  Returns html on success, false on failure 
		 */
		public function shortcode($attr, $content = null){
			
			// get doc info
			$posts = get_posts(array(
                'post_type'      => 'timeline', 
                'orderby'        => 'post_date',
	            'order'          => 'asc',
                'posts_per_page' => -1,
                'post_status'    => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')  
            ));
            
			if(empty($posts)){
				T_global::admin_notice(array('error' => __('Sorry, could`t find detail info to get timeline form!', 'timeline')));
				return false;
			} else {
                foreach($posts as $t){
                    $t->post_thumb = get_post_meta($t->ID, 'thumb')[0];
                    $t->post_dotname = get_post_meta($t->ID, 'dotname')[0];
                }
                
				return T_view($posts);
			}
		}
	}
}

?>