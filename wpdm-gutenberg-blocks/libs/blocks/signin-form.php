<?php
/**
 * User: shahnuralam
 * Date: 9/17/19
 * Time: 12:38 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class SigninForm{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/signin-form', array(
            'attributes'      => array(
                'role' => array(
                    'type' => 'string',
                    'default' => 'subscriber',
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
        $xparams = "";
        foreach ($attributes as $key => $value){
            if($value !== '')
                $xparams .= "{$key} = '{$value}' ";
        }
        return "<section class='__wpdm_gb_section __wpdm_gb_signup_form'>".do_shortcode("[wpdm_login_form {$xparams} ]")."</section>";
    }

}

new SigninForm();

