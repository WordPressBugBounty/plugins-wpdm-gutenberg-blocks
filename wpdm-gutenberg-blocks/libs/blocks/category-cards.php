<?php
/**
 * User: shahnuralam
 * Date: 09/01/19
 * Time: 4:05 PM
 */

namespace WPDM\Block;

use WPDM\__\Messages;

if (!defined('ABSPATH')) die();

class CategoryBlocks{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/category-blocks', array(
            'attributes'      => array(
                'cats' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'cols' => array(
                    'type'    => 'string',
                    'default' => "3"
                ),
                'button_color' => array(
                    'type'    => 'string',
                    'default' => 'rgb(0, 115, 255)'
                ),
                'border_color' => array(
                    'type'    => 'string',
                    'default' => 'rgb(0, 115, 255)'
                ),
                'hover_color'      => array(
                    'type'      => 'string',
                    'default'   => 'rgb(0, 115, 255)'
                ),
                'template'      => array(
                    'type'      => 'string',
                    'default'   => ''
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'editor_script' => 'wpdm-categoryblocks-block',
            'editor_style' => 'wpdm-block-style',
            'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content){
        if($attributes['cats'] == "") return "<div class='w3eden'><div class='well text-center'>".__('Select categories!', 'wpdmpro')."</div></div>";
        global $wpdm_archive_page;
        if(shortcode_exists('wpdm_category_blocks')){
            $attributes['cats'] = trim($attributes['cats'], ",");
            return "<section class='__wpdm_gb_category_cards'>".do_shortcode("[wpdm_category_blocks categories='{$attributes['cats']}' cols='{$attributes['cols']}' border_color='{$attributes['border_color']}' button_color='{$attributes['button_color']}' hover_color='{$attributes['hover_color']}' container='{$attributes['className']}']")."</section>";
        }
        return Messages::info("<div style='padding: 10px 0'>This block requires <a target='_blank' href='https://www.wpdownloadmanager.com/download/wpdm-directory-add-on/'><strong>WPDM - Directory Add-on</strong></a></div>", -1);
    }

}

new CategoryBlocks();

