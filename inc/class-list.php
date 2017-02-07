<?php
/**************************************************************
 *
 * table class for timeline list admin page 
 *
 **************************************************************/


// check WP_List_Table is available
if(!class_exists('WP_List_Table')){
	require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

if(!class_exists('T_admin_list')){
	// CREATE A PACKAGE CLASS
	class T_admin_list extends WP_List_Table{
		
		// table list data
		var $list_data = array();

		// table rows per a page
		var $rows_per_page = 20;

		// total rows number
		var $total_items = 0;

		/**
		 * constuct
		 *
		 * @param:	object array  $table_rows - rows info
		 * @param:	int           $total -  total rows number
		 * @return:	void
		 */
		function __construct($table_rows = array(), $total = 0){
			global $status, $page;
            
			//Set parent defaults
			parent::__construct(array(
				'singular'  => 'timeline',		//singular name of the listed records
				'plural'	=> 'timelines',		//plural name of the listed records
				'ajax'	  => false			//does this table support ajax?
			));
			
			// set list data to display table 
			$this->list_data = $table_rows;

			// set total rows number to display pagination
			$this->total_items = $total? $total: count($table_rows);

			// set table rows per a page
            global $T_conf;
			$this->rows_per_page = $T_conf['rows_per_page'];
		}
		

		/*
		 * For more detailed insight into how columns are handled, take a look at 
		 * WP_List_Table::single_row_columns()
		 * 
		 * @param array $item A singular item(one full row's worth of data)
		 * @param array $column_name The name/slug of the column to be processed
		 * @return string Text or HTML to be placed inside the column <td>
		 */
		function column_default($item, $column_name){
			switch($column_name){
				case 'post_title':
				case 'post_date':
				case 'author_name':
					return $item->$column_name;
				default:
					return print_r($item, true); //Show the whole array for troubleshooting purposes
			}
		}


		/*
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item(one full row's worth of data)
		 * @return string Text to be placed inside the column <td>(doc title only)
		 */
		function column_post_title($item){
			
			//Build row actions
			$actions = array(
				'edit'		=> sprintf('<a href = "?page=%s&action=%s&id=%s">'.__('Edit', 'timeline').'</a>', $_REQUEST['page'], 'edit', $item->ID),
				'delete'	=> sprintf('<a href = "#" onclick = "javascript: removeTimeline(\''.__('Do you remove this documentation really?', 'timeline').'\', \'%s\');">'.__('Remove', 'timeline').'</a>', $item->ID),
			);
			
			//Return the title contents
			return sprintf('%1$s %2$s',
				/*$1%s*/ $item->post_title,
				/*$2%s*/ $this->row_actions($actions)
			);
		}


		/*
		 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
		 * is given special treatment when columns are processed. It ALWAYS needs to
		 * have it's own method.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @param array $item A singular item(one full row's worth of data)
		 * @return string Text to be placed inside the column <td>(doc title only)
		 */
		function column_cb($item){
			return "";
		}


		/*
		 * REQUIRED! This method dictates the table's columns and titles. This should
		 * return an array where the key is the column slug(and class) and the value 
		 * is the column's title text. If you need a checkbox for bulk actions, refer
		 * to the $columns array below.
		 * 
		 * The 'cb' column is treated differently than the rest. If including a checkbox
		 * column in your table you must create a column_cb() method. If you don't need
		 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
		 * 
		 * @see WP_List_Table::::single_row_columns()
		 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
		 */
		function get_columns(){
			$columns = array(
				'post_title'  => __('Title', 'timeline'),
				'post_date'	 => __('Date', 'timeline'),
				'author_name' => __('Author', 'timeline')
			);
			return $columns;
		}


		/*
		 * Optional. If you want one or more columns to be sortable(ASC/DESC toggle), 
		 * you will need to register it here. This should return an array where the 
		 * key is the column that needs to be sortable, and the value is db column to 
		 * sort by. Often, the key and value will be the same, but this is not always
		 * the case(as the value is a column name from the database, not the list table).
		 * 
		 * This method merely defines which columns should be sortable and makes them
		 * clickable - it does not handle the actual sorting. You still need to detect
		 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
		 * your data accordingly(usually by modifying your query).
		 * 
		 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
		 */
		function get_sortable_columns(){
			$sortable_columns = array(
				'post_title'  => array('post_title', false), //true means it's already sorted
				'post_date'   => array('post_date', false),	 
				'author_name' => array('author_name', false)
			);
			return $sortable_columns;
		}


		/*
		 * Optional. If you need to include bulk actions in your list table, this is
		 * the place to define them. Bulk actions are an associative array in the format
		 * 'slug'=>'Visible Title'
		 * 
		 * If this method returns anempty value, no bulk action will be rendered. If
		 * you specify any bulk actions, the bulk actions box will be rendered with
		 * the table automatically on display().
		 * 
		 * Also note that list tables are not automatically wrapped in <form> elements,
		 * so you will need to create those documentationly in order for bulk actions to function.
		 * 
		 * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
		 */
		function get_bulk_actions(){
			$actions = array();
			return $actions;
		}


		/*
		 * REQUIRED! This is where you prepare your data for display. This method will
		 * usually be used to query the database, sort and filter the data, and generally
		 * get it ready to be displayed. At a minimum, we should set $this->items and
		 * $this->set_pagination_args(), although the following properties and methods
		 * are frequently interacted with here...
		 * 
		 * @global WPH $wpdb
		 * @uses $this->_column_headers
		 * @uses $this->items
		 * @uses $this->get_columns()
		 * @uses $this->get_sortable_columns()
		 * @uses $this->get_pagenum()
		 * @uses $this->set_pagination_args()
		 */
		function prepare_items(){
			global $wpdb; //This is used only if making any database queries
			
			
			/**
			 * REQUIRED. Now we need to define our column headers. This includes a complete
			 * array of columns to be displayed(slugs & titles), a list of columns
			 * to keep hidden, and a list of columns that are sortable. Each of these
			 * can be defined in another method(as we've done here) before being
			 * used to build the value for our _column_headers property.
			 */
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			
			
			/**
			 * REQUIRED. Finally, we build an array to be used by the class for column 
			 * headers. The $this->_column_headers property takes an array which contains
			 * 3 other arrays. One for all columns, one for hidden columns, and one
			 * for sortable columns.
			 */
			$this->_column_headers = array($columns, $hidden, $sortable);
							
			
			/**
			 * This checks for sorting input and sorts the data in our array accordingly.
			 * 
			 * In a real-world situation involving a database, you would probably want 
			 * to handle sorting by passing the 'orderby' and 'order' values directly 
			 * to a custom query. The returned data will be pre-sorted, and this array
			 * sorting technique would be unnecessary.
			 */
			function usort_reorder($a, $b){
				$a = (array)$a;
				$b = (array)$b;

				$orderby =(!empty($_REQUEST['orderby']))? $_REQUEST['orderby']: 'post_title'; //If no sort, default to title
				$order =(!empty($_REQUEST['order']))? $_REQUEST['order']: 'asc'; //If no order, default to asc
				$result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
				return($order === 'asc')? $result: -$result; //Send final sort direction to usort
			}
			usort($this->list_data, 'usort_reorder');
			
			
			/***********************************************************************
			 * In a real-world situation, this is where you would place your query.
			 *
			 * For information on making queries in WordPress, see this Codex entry:
			 * http://codex.wordpress.org/Class_Reference/wpdb
			 **********************************************************************/
			
					
			/**
			 * REQUIRED for pagination. Let's figure out what page the user is currently 
			 * looking at. We'll need this later, so you should always include it in 
			 * your own package classes.
			 */
			$current_page = $this->get_pagenum();
			
			/**
			 * The WP_List_Table class does not handle pagination for us, so we need
			 * to ensure that the data is trimmed to only the current page. We can use
			 * array_slice() to 
			 */
			$this->list_data = array_slice($this->list_data,(($current_page-1) * $this->rows_per_page), $this->rows_per_page);
			
			
			
			/**
			 * REQUIRED. Now we can add our *sorted* data to the items property, where 
			 * it can be used by the rest of the class.
			 */
			$this->items = $this->list_data;
			
			/**
			 * REQUIRED. We also have to register our pagination options & calculations.
			 */
			$this->set_pagination_args(array(
				'total_items' => $this->total_items,															//WE have to calculate the total number of items
				'per_page'	  => $this->rows_per_page,															//WE have to determine how many items to show on a page
				'total_pages' => $this->total_items == 0? 0: ceil($this->total_items / $this->rows_per_page)	//WE have to calculate the total number of pages
			));
		}

	}
}
?>