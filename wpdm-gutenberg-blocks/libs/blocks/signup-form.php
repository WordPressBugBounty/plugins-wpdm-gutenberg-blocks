<?php
/**
 * User: shahnuralam
 * Date: 9/17/19
 * Time: 12:38 PM
 */

namespace WPDM\Block;

if (!defined('ABSPATH')) die();

class SigupForm{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
        add_action( 'admin_head', array($this, 'customJS'), 9 );

    }

    function customJS(){
        global $wp_roles;
        $roles = array_reverse($wp_roles->role_names);
        $options = [[ "label" =>  "Default Role", "value"  => ""]];
        $role_ids = get_option("__wpdm_signup_roles", array());
        foreach ($role_ids as $role){
            $options[] = array("label" => $roles[$role], "value" => $role);
        }
        ?>
        <script>
            var __wpdm_roles = <?php echo json_encode($options); ?>;
        </script>
        <?php
    }

    function block(){

        register_block_type( 'download-manager/signup-form', array(
            'attributes'      => array(
                'role' => array(
                    'type' => 'string',
                    'default' => 'subscriber',
                ),
                'logo' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'verifyemail' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'autologin' => array(
                    'type' => 'boolean',
                    'default' => false
                ),
                'note_before' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'note_after' => array(
                    'type' => 'string',
                    'default' => ''
                ),
                'social_only' => array(
                    'type' => 'boolean',
                    'default' => false
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
        return "<section class='__wpdm_gb_section __wpdm_gb_signup_form'>".do_shortcode("[wpdm_reg_form {$xparams} ]")."</section>";
    }

}

new SigupForm();

