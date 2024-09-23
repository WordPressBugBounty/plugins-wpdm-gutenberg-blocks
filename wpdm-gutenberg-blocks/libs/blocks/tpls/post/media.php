<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/10/19 14:33
 */
if(!defined("ABSPATH")) die();
?>
<div class="card card-post-media mb-4">
    <div class="card-body">
        <div class="media">
            <?php wpdm_thumb($post, array(150, 150), true, array('crop' => true, 'class' => 'mr-3 w-64')) ?>
            <div class="media-body">
                <h3><?php echo $post->post_title; ?></h3>
                <div class="text-muted"><?php echo get_the_date('', $post); ?></div>
            </div>
        </div>
    </div>
    <div class="card-footer text-right">
        <a href="<?php echo get_permalink($post); ?>" class="btn btn-primary btn-sm"><?php _e('Read More', 'attire'); ?></a>
    </div>
</div>
