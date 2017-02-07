<?php
/**************************************************************
 *
 * page driving class for admin page 
 *
 **************************************************************/


// check T_page is available
if(!class_exists('T_page')){

	// CREATE A PACKAGE CLASS
	class T_page{
		
		// global page settings class object
		var $get_template;

		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			
			global $T_conf;
			$this->get_template = $T_conf['pages'];

			return;
		}
		
		/**
		 * list page
		 *
		 * @return:	void
		 */
		public function pages(){
			switch($_REQUEST['action']){
				case 'new':
					$this->page_new();
					break;
                    
				case 'edit':
					$this->page_edit();
					break;

				case 'del':
					$this->page_list_del();
					break;

				default:
					$this->page_list();
					break;
			}
			
			return;
		}
		
		/**
		 * list page - list view items
		 *
		 * @return:	void
		 */
		protected function page_list(){
            global $T_conf;
            
			// print list table
            $posts = get_posts(array(
                'post_type'      => 'timeline', 
                'orderby'        => $_REQUEST['orderby'],
	            'order'          => $_REQUEST['order'],
                'posts_per_page' => -1,
                'post_status'    => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')  
            ));
            
			T_list($posts, wp_count_posts('timeline')->publish);

			return;
		}
				
		/**
		 * list page - edit
		 *
		 * @return:	void
		 */
		protected function page_edit(){
			// doc ID to edit doc
			$timeline_id = isset($_REQUEST['id'])? $_REQUEST['id']: -1;
            
			// get form datas
            $post = get_post($timeline_id);
			
			if(empty($post)){
				T_global::admin_notice(array('error' => __('Sorry, could`t find detail info to edit timeline!', 'timeline')));
				
				// display list table after deleted doc
				$this->page_list();

			} else {
                $post->post_thumb = get_post_meta($post->ID, 'thumb')[0];
                $post->post_dotname = get_post_meta($post->ID, 'dotname')[0];
                
				// print form
				T_edit($post);
			}
			
			return;
		}
				
		/**
		 * list page - new
		 *
		 * @return:	void
		 */
		protected function page_new(){
            // print form
            T_edit();
			
			return;
		}
		
		/**
		 * list page - delete items
		 *
		 * @return:	void
		 */
		protected function page_list_del(){
			// doc ID to delete doc
			$timeline_id = isset($_REQUEST['id'])? $_REQUEST['id']: -1;

			if(is_array($timeline_id)){
				foreach($timeline_id as $id){
					$this->del_list_item($id);
				}
			} else {
				$this->del_list_item($timeline_id);
			}

			// display list table after deleted doc
			$this->page_list();

			return;
		}

		/**
		 * delete one item & row from database
		 *
		 * @param:	int  $timeline_id - row ID
		 * @return:	void
		 */
		protected function del_list_item($timeline_id){
			$post = get_post($timeline_id);
            
            // delete timeline            
            $del = wp_delete_post($timeline_id, true);   
            
            if(is_wp_error($del)){
                // not udpated
				T_global::admin_notice(array('error' => __('Sorry, couldn`t delete selected timeline correctly, Try again later!', 'timeline')));
            } else {
                // succss
				T_global::admin_notice(array('updated' => __('Timeline `'.$post->post_title.'` is deleted successfully!', 'timeline')));
            }

			return;
		}
	}
}

?>