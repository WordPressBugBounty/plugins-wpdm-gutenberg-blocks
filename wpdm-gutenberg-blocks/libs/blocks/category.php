<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();


class Category{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/category', array(
            'attributes'      => array(
                'cats' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'title' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'desc' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'toolbar' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'items_per_page' => array(
                    'type'    => 'number',
                    'default' => 10
                ),
                'paging' => array(
                    'type'    => 'number',
                    'default' => 1
                ),
                'cols' => array(
                    'type'    => 'number',
                    'default' => 1
                ),
                'colspad' => array(
                    'type'    => 'number',
                    'default' => 1
                ),
                'colsphone' => array(
                    'type'    => 'number',
                    'default' => 1
                ),
                'orderby' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'order' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'async' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'template'      => array(
                    'type'      => 'string',
                    'default'   => 'link-template-panel'
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content){
        if($attributes['cats'] == "") return "<div class='w3eden'><div class='well text-center'>".__('Select categories!', 'wpdmpro')."</div></div>";
        $attributes['id'] = trim($attributes['cats'], ",");
        unset($attributes['cats']);
        if(isset($attributes['id']) && substr_count($attributes['id'], ",") > 0) {
            $ids = [];
            foreach (explode(",", $attributes['id']) as $slug) {
                $term = get_term_by('slug', $slug, 'id');
                if($term)
                    $ids[] = $term->term_id;
            }
            if(count($ids) > 0) {
                $attributes['id'] = implode(",", $ids);
                $attributes['cat_field'] = 'id';
            }
        }
        $attributes['css_class'] = isset($attributes['className'])?$attributes['className']:'';
        return "<section class='__wpdm_gb_section __wpdm_gb_category'>".WPDM()->categories->shortcode->listPackages($attributes)."</section>";
        //return do_shortcode("[wpdm_category id='{$attributes['cats']}' template='{$attributes['template']}' ]");
    }

}

new Category();

