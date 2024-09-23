<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();


class Section{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){


        register_block_type( 'download-manager/section', array(
            'attributes'      => array(
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
            'editor_script' => 'wpdm-section-block',
            'editor_style' => 'wpdm-block-style',
            //'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content ){

    }

}

new Section();

