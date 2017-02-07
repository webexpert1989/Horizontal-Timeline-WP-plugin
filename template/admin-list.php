<?php
/**************************************************************
 *
 * template for Timeline list
 *
 **************************************************************/

// timeline list
function T_list($data = array(), $total = 0){
/////////////
	global $T_conf;
        
    foreach($data as $d){
        $user = get_user_by('id', $d->post_author);
        $d->author_name = $user->display_name;
    }
    
	$list_table = new T_admin_list($data, $total);
	$list_table->prepare_items();
    
	?>
		<div id="timeline-admin" class = "wrap">
			<h2>
				<?php echo __('Timelines', 'timeline'); ?>
				<a href = "admin.php?page=<?php echo $T_conf['pages']['list'].'&action=new'; ?>" class = "add-new-h2">
					<?php echo __('Add New a Timeline', 'timeline'); ?>
				</a>
			</h2>

			<form id = "timeline-list" method = "get">
				<input type = "hidden" name = "page" value = "<?php echo esc_attr($_REQUEST['page']); ?>" />

				<!-- display table -->
				<?php $list_table->display(); ?>

			</form>
		</div>
	<?php
}
?>