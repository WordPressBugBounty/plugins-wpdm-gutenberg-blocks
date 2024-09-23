<?php
/**
 * User: shahnuralam
 * Date: 10/01/19
 * Time: 07:46 PM
 */

namespace WPDM\Block;

use WPDM\__\Messages;

if (!defined('ABSPATH')) die();

class Search{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/search', array(
            'attributes'      => array(
                'init' => array(
                    'type'    => 'boolean',
                    'default' => false
                ),
                'items_per_page' => array(
                    'type'    => 'integer',
                    'default' => 10
                ),
                'cols' => array(
                    'type'    => 'integer',
                    'default' => 1
                ),
                'colspad' => array(
                    'type'    => 'integer',
                    'default' => 1
                ),
                'colsphone' => array(
                    'type'    => 'integer',
                    'default' => 1
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

        if(method_exists(WPDM()->package->shortCodes, 'searchResult')) {
            $attributes['css_class'] = isset($attributes['className'])?$attributes['className']:'wpdm_search_results';
            $attributes['async'] = 1;
            return "<section class='__wpdm_gb_section __wpdm_gb_packages'>".WPDM()->package->shortCodes->searchResult($attributes)."</section>";
        }
        else
            return Messages::info("Block is available with the pro version only!",  -1);
    }

}

new Search();

