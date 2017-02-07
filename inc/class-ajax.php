<?php
/**************************************************************
 *
 * ajax response class for timeline plugin
 *
 * @info: error code - 100x: GET & POST errors, no fields
 *                     200x: UPDATE faild errors
 *                     300x: INSERT faild errors
 *                     400x: DELETE faild errors
 *                     900x: SELECT faild errors
 *
 **************************************************************/


// check T_ajax is available
if(!class_exists('T_ajax')){

	// CREATE A PACKAGE CLASS
	class T_ajax{

		// Timeline database object
		var $db_obj = '';
		
		/**
		 * constuct
		 *
		 * @return:	void
		 */
		function __construct(){
			return;
		}
					
		/**
		 * update data 
		 *
		 * @return:	void
		 */
		public function timeline_edit(){ 

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1002,
					'error_txt' => __('no POST fields!', 'timeline'),
				));
				exit;
			}
			$post = $_POST;

			/////////////
			$the_issue_key = $post['the_issue_key'];

			// update data to DB
            
            global $current_user;
			get_currentuserinfo();
             
            // insert new timeline
			$post = array(
                'ID'             => $_POST['id'],
                'post_title'     => $_POST['title'],
                'post_content'   => $_POST['content'],
                'post_date'      => $_POST['date'],
                'post_status'    => 'publish',
                'post_type'      => 'timeline', // custom slug
                'post_author'    => $current_user->ID,
            ); 
            
            $update = wp_update_post($post, true);                        
            if(!is_wp_error($update)){
                if(get_post_meta($post['ID'], 'thumb')[0] != $_POST['thumbnail'] && !update_post_meta($post['ID'], 'thumb', $_POST['thumbnail'])){
                    // not udpated
                    echo json_encode(array(
                        'error' => 2001,
                        'error_txt' => __('Sorry, couldn`t update Timeline correctly, Try again later!', 'timeline')	
                    ));
                    exit;
                }
                
                if(get_post_meta($post['ID'], 'dotname')[0] != $_POST['dotname'] && !update_post_meta($post['ID'], 'dotname', $_POST['dotname'])){
                    // not udpated
                    echo json_encode(array(
                        'error' => 2001,
                        'error_txt' => __('Sorry, couldn`t update Timeline correctly, Try again later!', 'timeline')	
                    ));
                    exit;
                }
                
                // succss
                echo json_encode(array(
                    'success' => true,
                    'success_txt' => __('Timeline is updated successfully!', 'timeline')
                ));
                exit;         
            }
            
            // not udpated
            echo json_encode(array(
                'error' => 2001,
                'error_txt' => __('Sorry, couldn`t update Timeline correctly, Try again later!', 'timeline')	
            ));	
			
            /////
			exit;
		}
				
		/**
		 * add new data 
		 *
		 * @return:	void
		 */
		public function timeline_new(){

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1003,
					'error_txt' => __('no POST fields!', 'timeline'),
				));
				exit;
			}
			
			/////////////
			$the_issue_key = $post['the_issue_key'];
            
            global $current_user;
			get_currentuserinfo();
            
            // insert new timeline
			$post = array(
                'post_title'     => $_POST['title'],
                'post_content'   => $_POST['content'],
                'post_date'      => $_POST['date'],
                'post_status'    => 'publish',
                'post_type'      => 'timeline', // custom slug
                'post_author'    => $current_user->ID,
            ); 

            $new = wp_insert_post($post, true); // insert the post and allow WP_Error object

            if(!is_wp_error($new)){
                if(add_post_meta($new, 'dotname', $_POST['dotname'])){
                    if(add_post_meta($new, 'thumb', $_POST['thumbnail'])){
                         // succss
                        echo json_encode(array(
                            'success'     => true,
                            'post_id'     => $new,
                            'success_txt' => __('New Timeline is saved successfully!', 'timeline')
                        ));
                        exit;
                    }
                }                
                
            }
            
            // Error handling
            echo json_encode(array(
                'error'     => 3001,
                'error_info'=> $new,
                'error_txt' => __('Sorry, couldn`t save new Timeline correctly, Try again later!', 'timeline')	
            ));
            
			//////////
			exit;
		}
		
		/**
		 * delete timeline 
		 *
		 * @return:	void
		 */
		public function timeline_del(){ 

			// check POST fields
			if(empty($_POST)){
				// succss
				echo json_encode(array(
					'error' => 1004,
					'error_txt' => __('no POST fields!', 'timeline'),
				));
				exit;
			}
			$post = $_POST;
			
			/////////////
			$the_issue_key = $post['the_issue_key'];

			// delete timeline
            $post = array(
                'ID'             => $_POST['id']
            ); 
            
            $del = wp_delete_post($post['ID'], true);                        
            if(!is_wp_error($del) && delete_post_meta($post['ID'], 'thumb') && delete_post_meta($post['ID'], 'dotname')){
                // succss
				echo json_encode(array(
					'success' => true,
					'success_txt' => __('Timeline is deleted successfully!', 'timeline'),
				));
            } else {
                // not udpated
				echo json_encode(array(
					'error' => 4002,	
					'error_txt' => __('Sorry, couldn`t delete Timeline correctly, Try again later!', 'timeline')	
				));
            }
            
            ////
			exit;
		}
	}
}

?>