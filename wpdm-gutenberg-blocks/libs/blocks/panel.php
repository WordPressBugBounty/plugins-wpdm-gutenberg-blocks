<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class Panel{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){
        $deps = array( 'wp-blocks', 'wp-element', 'wpdm-icons', 'wpdm-gb-util', 'wpdm-link-templates', 'wpdm-link-templates', 'wpdm-category-selector', 'wpdm-package-selector' , 'wpdm-category-dropdown');
        if(!defined('ATTIRE_BLOCKS_VERSION')) $deps[] = 'wpdm-admin-bootstrap';
        wp_register_script(
            'wpdm-panel-block',
            plugins_url('js/panel.js', __WPDM_GB__),
            $deps
        );

        register_block_type( 'download-manager/panel', array(
            'attributes'      => array(
                'title' => array(
                    'type' => 'array',
                    'source' => 'children',
                    'selector' =>  '.title'
                ),
                'panel_id' => array(
                    'type' => 'string'
                ),
                'content' => array(
                    'type' => 'array',
                    'source' => 'children',
                    'selector' =>  '.content'
                ),
                'titleColor' => array(
                    'type' => 'string',
                    'default' =>  '#333333'
                ),
                'headerBG' => array(
                    'type' => 'string',
                    'default' =>  '#eeeeee'
                ),
                'borderColor' => array(
                    'type' => 'string',
                    'default' =>  '#dddddd'
                ),
                'textColor' => array(
                    'type' => 'string',
                    'default' =>  '#333333'
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'editor_script' => 'wpdm-panel-block',
            'editor_style' => 'wpdm-block-style',
            //'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content){
        return null;
    }

}

new Panel();

