<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/10/19 14:33
 */
if(!defined("ABSPATH")) die();
?>
<div class="card">
    <?php wpdm_thumb($post, array(400, 300), true, array('crop' => true, 'class' => 'card-img-top')) ?>
    <div class="card-body">
        <h3 class="card-title font-weight-bold"><?php echo $post->post_title; ?></h3>
        <p class="card-text"><?php echo wpdm_get_excerpt($post); ?></p>
    </div>
    <a href="<?php echo get_permalink($post); ?>" class="btn btn-primary btn-lg card-footer"><?php _e('Read More', 'attire'); ?></a>
</div>
