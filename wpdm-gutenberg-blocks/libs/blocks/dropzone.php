<?php
/**
 * User: shahnuralam
 * Date: 9/17/19
 * Time: 12:38 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class DropZone{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/dropzone', array(
            'attributes'      => array(
                'logo' => array(
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
        $xparams = "";
        foreach ($attributes as $key => $value){
            if($value !== '')
                $xparams .= "{$key} = '{$value}' ";
        }
        return substr_count($_SERVER['REQUEST_URI'], 'wp-json') ? '<img src="'.__WPDM_GBURL__.'assets/images/dropzone-preview.jpg" />' : "<section class='__wpdm_gb_section __wpdm_gb_dropzone'>".do_shortcode("[wpdm_dropzone {$xparams} ]")."</section>";
    }

}

new DropZone();

