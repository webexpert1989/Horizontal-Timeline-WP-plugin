<?php
/**************************************************************
 *
 * template to edit timeline
 *
 **************************************************************/


// edit timeline
function T_edit($data = null){
    
	/////////////
    if(empty($data)){
        $data = (object)[
            ID => 0,
            post_title => '',
            post_content => '',
            post_date => '',
        ];
    } else {
       $d = new DateTime($data->post_date);
    }
    
	///////////
	global $T_conf;
    
	// enable connection to wordpress media lib    
    wp_enqueue_media();
    
	?>
		<div id="timeline-admin" class="wrap">
			<h2>
				<?php empty($data->ID)? printf(__('New Timeline', 'timeline')): printf(__('Edit Timeline `%s`', 'timeline'), $data->post_title); ?>
				<a id="timeline-list-page" href="admin.php?page=<?php echo $T_conf['pages']['list']; ?>" class="add-new-h2">
					<?php echo __('List', 'timeline'); ?>
				</a>
			</h2>

			<div class="message-wrapper">
				<?php do_action('T-admin-message'); ?>
			</div>
			
			<div class="content-wrapper">
                <hr />
                
                <section>
                    <label for="timeline-date"><?php echo __('Date', 'timeline'); ?>:</label> 
                    <input type="text" id="timeline-date" name="timeline-date" value="<?php echo $data->post_date? $d->format('m/d/Y'): ''; ?>" placeholder="mm/dd/yyyy"/>
                </section>
                <section>
                    <label for="timeline-dotname"><?php echo __('Name', 'timeline'); ?>:</label> 
                    <input type="text" id="timeline-dotname" name="timeline-dotname" value="<?php echo $data->post_dotname; ?>"/>
                </section>
                <section>
                    <label for="timeline-title"><?php echo __('Timeline Subject', 'timeline'); ?>:</label> 
                    <input type="text" id="timeline-title" name="timeline-title" value="<?php echo $data->post_title; ?>"/>
                </section>
                
                <section>
                    <label for="timeline-thumbnail"><?php echo __('Timeline Thumbnail', 'timeline'); ?>:</label> 
                    <div class="upload">
                        <input type="text" id="timeline-thumbnail" name="timeline-thumbnail" value="<?php echo $data->post_thumb; ?>"/>
                        <input type="button" class="button-secondary" data-upload data-preview="#thumbnail-preview" value="<?php echo __('Select', 'timeline'); ?>"/>
                    </div>
                    <div id="thumbnail-preview" class="thumbnail-preview">
                        <?php if($data->post_thumb): ?>
                            <img src="<?php echo $data->post_thumb; ?>"/>
                        <?php endif; ?>
                    </div>
                </section>
                
                <section>
                    <label><?php echo __('Timeline Description', 'timeline'); ?>: </label>
                    <div class = "desc-editor">
                        <?php wp_editor($data->post_content, 'timeline-content', array('editor_height' => 300 , 'media_buttons' => true, 'teeny' => false)); ?>
                    </div>
                </section>
                
                <div class="action-wrap">
                    <hr />
                    <input type="button" id="timeline-save" class="button-primary" data-id="<?php echo $data->ID; ?>" value="<?php echo __('Save Timeline', 'timeline'); ?>"/>
                    <?php if(!empty($data->ID)): ?>
                        <input type="button" id="timeline-del" class="button-secondary" onclick="javascript: removeTimeline('<?php echo __('Do you remove this Timeline really?', 'timeline'); ?>', '<?php echo $data->ID; ?>');" value="<?php echo __('Delete Timeline', 'timeline'); ?>"/>
                    <?php endif; ?>
                </div>
			</div>

			<input type="hidden" id="timeline-id" value="<?php echo $data->ID; ?>"/>
		</div>
	<?php
}
?>