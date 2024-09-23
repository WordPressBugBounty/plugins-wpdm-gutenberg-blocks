<?php
/**
 * User: shahnuralam
 * Date: 10/01/19
 * Time: 07:46 PM
 */

namespace WPDM\Block;

use WPDM\__\Messages;

if (!defined('ABSPATH')) die();

class Packages{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/packages', array(
            'attributes'      => array(
                'search' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'categories' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'include_children' => array(
                    'type'    => 'boolean',
                    'default' => false
                ),
                'operator' => array(
                    'type'    => 'string',
                    'default' => "IN"
                ),
                'xcats' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'tag' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'tag__not_in' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'author' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'author__not_in' => array(
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
                'tbgrid' => array(
                    'type'    => 'string',
                    'default' => "5,2,2,3"
                ),
                'items_per_page' => array(
                    'type'    => 'string',
                    'default' => "10"
                ),
                'paging' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'cols' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'colspad' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'colsphone' => array(
                    'type'    => 'string',
                    'default' => "1"
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
        if(method_exists(WPDM()->package->shortCodes, 'packages')) {
            $attributes['css_class'] = isset($attributes['className'])?$attributes['className']:'wpdm_packages';
            if(isset($attributes['categories']) && substr_count($attributes['categories'], ",") > 0) {
                $ids = [];
                foreach (explode(",", $attributes['categories']) as $slug) {
                    $term = get_term_by('slug', $slug, 'wpdmcategory');
                    if($term)
                        $ids[] = $term->term_id;
                }
                if(count($ids) > 0) {
                    $attributes['categories'] = implode(",", $ids);
                    $attributes['cat_field'] = 'id';
                }
            }
            return "<section class='__wpdm_gb_section __wpdm_gb_packages'>".WPDM()->package->shortCodes->packages($attributes)."</section>";
        }
        else
            return Messages::info("Block is available with the pro version only!");
    }

}

new Packages();

