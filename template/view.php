<?php
/**************************************************************
 *
 * template to generate view by shortcode
 *
 **************************************************************/

function T_view($timelines = array(), $opt = 'default'){

    if(empty($timelines)){
        echo 'No Data';
        return false;
    }
    
	///////////

	?>
        <section class="cd-horizontal-timeline <?php echo $opt != 'default'? '': 'theme-light'; ?>">
            <div class="horizontal-timeline">
                <div class="events-wrapper">
                    <div class="events">
                        <ol>
                            <?php foreach($timelines as $i => $t): ?>
                                <?php $date = new DateTime($t->post_date); ?>
                                <li>
                                    <a href="#0" data-date="<?php echo $date->format('d/m/Y'); ?>" class="<?php echo $i == 0? 'selected': ''; ?>">
                                        <span class="dotname"><?php echo $t->post_dotname; ?></span>
                                        <span class="dotdate"><?php echo $date->format('Y'); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ol>

                        <span class="filling-line" aria-hidden="true"></span>
                    </div> <!-- .events -->
                </div> <!-- .events-wrapper -->

                <ul class="cd-timeline-navigation">
                    <li><a href="#0" class="prev inactive">Prev</a></li>
                    <li><a href="#0" class="next">Next</a></li>
                </ul> <!-- .cd-timeline-navigation -->
            </div> <!-- .timeline -->

            <div class="events-content">
                <ol>
                    <?php foreach($timelines as $i => $t): ?>
                        <?php $date = new DateTime($t->post_date); ?>
                        <li data-date="<?php echo $date->format('d/m/Y'); ?>" class="<?php echo $i == 0? 'selected': ''; ?>">
                            <div class="timeline-content-wrap">
                                <?php if($t->post_thumb): ?>
                                    <div class="thumbnail-wrap">
                                        <img src="<?php echo $t->post_thumb; ?>" class="timeline-thumbnail"/>
                                    </div>
                                <?php endif; ?>
                                <div class="context-wrap">
                                    <h2><?php echo $t->post_title; ?></h2>
                                    <div class="context"><?php echo $t->post_content; ?></div>
                                </div>  
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div> <!-- .events-content -->
        </section>

	<?php
}
?>