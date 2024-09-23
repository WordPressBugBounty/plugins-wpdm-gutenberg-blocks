<?php
/**
 * User: shahnuralam
 * Date: 8/4/18
 * Time: 4:05 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class Package{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );

    }



    function block(){

        register_block_type( 'download-manager/package', array(
            'attributes'      => array(
                'packageID' => array(
                    'type' => 'integer',
                ),
                'packageTitle' => array(
                    'type' => 'string',
                ),
                'template' => array(
                    'type' => 'string',
                    'default' => 'link-template-panel'
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
        if(!isset($attributes['packageID']) || get_post_type($attributes['packageID']) != 'wpdmpro') return "<div class='w3eden'><div class='well text-center'>".__('Select a package!', 'wpdmpro')."</div></div>";
        return "<section class='__wpdm_gb_section __wpdm_gb_package'>".do_shortcode("[wpdm_package id='{$attributes['packageID']}' template='{$attributes['template']}' ]")."</section>";
    }

}

new Package();

