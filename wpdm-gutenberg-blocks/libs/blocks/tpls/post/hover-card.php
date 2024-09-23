<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 16/10/19 21:34
 */
if(!defined("ABSPATH")) die();
$preview = get_the_post_thumbnail_url($post);

?>
<div class="card hover-card">
    <div class="card-body text-left p-3">
        <div class="media">
            <?php echo get_avatar($post->post_author, 96, "", "Author", array('class' => 'mr-3 w-48 rounded-circle')); ?>
            <div class="media-body">
                <div class="author-name"><?php the_author(); ?></div>
                <div class="text-muted"><?php echo get_the_date("", $post); ?></div>
            </div>
        </div>
    </div>
    <div class="chimg">
        <?php wpdm_thumb($post, array(400, 300), true, array('crop' => true)) ?>
    </div>
    <div class="card-body text-left p-3">
        <h3 class="card-title font-weight-bold ellipsis mb-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p class="card-text"><?php echo wpdm_get_excerpt($post); ?></p>
    </div>
    <a href="<?php the_permalink(); ?>" class="card-footer text-right text-uppercase text-small"><?php _e('Read More', 'attire'); ?> <i class="fa fa-angle-double-right"></i></a>
</div>

