<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

use WPDM\__\Template;

if (!defined('ABSPATH')) die();


class CardSlider{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){
        wp_register_script(
            'wpdm-card-slider-block',
            plugins_url('js/card-slider.js', __WPDM_GB__),
            array( 'wp-blocks', 'wp-element','wpdm-admin-bootstrap', 'wpdm-icons', 'wpdm-link-templates', 'wpdm-category-dropdown' )
        );

        register_block_type( 'download-manager/card-slider', array(
            'attributes'      => array(
                'cat' => array(
                    'type'    => 'string',
                    'default' => ""
                ),

                'items_per_page' => array(
                    'type'    => 'number',
                    'default' => 4
                ),

                'order_by' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'order' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'color' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'editor_script' => 'wpdm-card-slider-block',
            'editor_style' => 'wpdm-block-style',
            'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content){
        //if($attributes['cat'] == "") return "<div class='w3eden'><div class='well text-center'>".__('Select categories!', 'wpdmpro')."</div></div>";
        $count = isset($attributes['items_per_page']) && (int)$attributes['items_per_page'] > 0 ? (int)$attributes['items_per_page'] : 3;
        $cat = trim($attributes['cat'], ',');
        if(isset($_REQUEST['attributes']))
            $count = 1;
        $params = array(
            'post_type' => 'wpdmpro',
            'posts_per_page' => $count,

        );
        if($cat != '') {
            $cids = explode(",", $cat);
            $params['tax_query'] = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => $cids,
                'include_children' => false,
                'meta_query' => array(
                    array(
                        'key' => '_thumbnail_id',
                        'compare' => 'EXISTS'
                    ),
                )
            ));
        }
        $packages = get_posts($params);
        $template = new Template();
        //if(isset($_REQUEST['attributes']))
        //    return "<img style='box-shadow:none' src='".plugins_url('/wpdm-gutenberg-blocks/images/card-slider.png')."' alt='Card Slider Preview' />";
        return $template->assign("slides", $packages)
            ->assign("color", $attributes['color'])
            ->assign("colorrgb", wpdm_hex2rgb($attributes['color']))
            ->fetch('card-slider.php', __WPDM_GBDIR__.'/tpls');

    }

}

new CardSlider();

