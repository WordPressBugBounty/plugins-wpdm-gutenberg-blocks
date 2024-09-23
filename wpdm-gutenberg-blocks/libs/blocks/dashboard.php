<?php
/**
 * User: shahnuralam
 * Date: 9/17/19
 * Time: 12:38 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class Dashboard{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/dashboard', array(
            'attributes'      => array(
                'recommended' => array(
                    'type' => 'string',
                    'default' => 'recent',
                ),
                'fav' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'logo' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'redirect' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'regurl' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'note_before' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'note_after' => array(
                    'type' => 'string',
                    'default' => ''
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
        return WPDM()->user->dashboard->dashboard($attributes);
    }

}

new Dashboard();

