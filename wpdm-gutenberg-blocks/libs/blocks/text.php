<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class Text{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){
        $deps = !defined('ATTIRE_BLOCKS_VERSION') ? array( 'wp-blocks', 'wp-element', 'wpdm-admin-bootstrap', 'wpdm-icons', 'wpdm-gb-util', 'wpdm-link-templates', 'wpdm-category-selector', 'wpdm-package-selector' ) : array( 'wp-blocks', 'wp-element', 'wpdm-icons', 'wpdm-gb-util', 'wpdm-link-templates', 'wpdm-category-selector', 'wpdm-package-selector' );
        wp_register_script(
            'wpdm-text-block',
            plugins_url('js/text.js', __WPDM_GB__),
            $deps
        );

        register_block_type( 'download-manager/text', array(
            'attributes'      => array(
                'label' => array(
                    'type' => 'array',
                    'source' => 'children',
                    'selector' =>  '.main-label'
                ),
                'subLabel' => array(
                    'type' => 'array',
                    'source' => 'children',
                    'selector' =>  '.sub-label'
                ),
                'id' => array(
                    'type' => 'string'
                ),
                'name' => array(
                    'type' => 'string'
                ),
                'placeholder' => array(
                    'type' => 'string',
                    'default' =>  ''
                ),
                'labelColor' => array(
                    'type' => 'string',
                    'default' =>  '#333333'
                ),
                'background' => array(
                    'type' => 'string',
                    'default' =>  '#ffffff'
                ),
                'borderColor' => array(
                    'type' => 'string',
                    'default' =>  '#dddddd'
                ),
                'textColor' => array(
                    'type' => 'string',
                    'default' =>  '#333333'
                ),
                'elClass'      => array(
                    'type'      => 'string',
                    'default'   => 'form-control'
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'editor_script' => 'wpdm-text-block',
            'editor_style' => 'wpdm-block-style',
            //'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content){
        return null;
    }

}

new Text();

