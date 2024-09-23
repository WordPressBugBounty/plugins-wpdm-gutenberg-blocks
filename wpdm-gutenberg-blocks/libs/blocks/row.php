<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();


class Row{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){
        wp_register_script(
            'wpdm-column-block',
            plugins_url('js/column.js', __WPDM_GB__),
            array( 'wp-blocks', 'wp-element','wpdm-admin-bootstrap', 'wpdm-icons', 'wpdm-gb-util' )
        );

        wp_register_script(
            'wpdm-row-block',
            plugins_url('js/row.js', __WPDM_GB__),
            array( 'wp-blocks', 'wp-element','wpdm-admin-bootstrap', 'wpdm-icons', 'wpdm-gb-util', 'wpdm-column-block' )
        );

        register_block_type( 'download-manager/row', array(
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
            'editor_script' => 'wpdm-row-block',
            'editor_style' => 'wpdm-block-style',
            //'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content ){

    }

}

new Row();

