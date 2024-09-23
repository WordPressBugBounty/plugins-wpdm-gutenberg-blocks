<?php
/**
 * User: shahnuralam
 * Date: 10/01/19
 * Time: 07:46 PM
 */

namespace WPDM\Block;

use WPDM\__\Template;

if (!defined('ABSPATH')) die();

class Posts{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
        add_filter( 'wpdm_template_path', array($this, 'registerTplPath'));
        add_action( 'admin_head', array($this, 'templates'), 9 );
    }


    function registerTplPath($paths){
        array_unshift($paths, get_stylesheet_directory()."/download-manager/gutenberg/");
        array_unshift($paths, get_template_directory()."/download-manager/gutenberg/");
        return $paths;
    }

    function block(){
        $deps = array( 'wp-blocks', 'wp-element', 'wpdm-icons', 'wpdm-gb-util', 'wpdm-link-templates', 'wpdm-link-templates', 'wpdm-category-selector', 'wpdm-package-selector' , 'wpdm-category-dropdown');
        if(!defined('ATTIRE_BLOCKS_VERSION')) $deps[] = 'wpdm-admin-bootstrap';
        wp_register_script(
            'wpdm-posts-block',
            plugins_url('js/posts.js', __WPDM_GB__),
            $deps
        );

        register_block_type( 'download-manager/posts', array(
            'attributes'      => array(
                'search' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'categories' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'include_children' => array(
                    'type'    => 'boolean',
                    'default' => false
                ),
                'operator' => array(
                    'type'    => 'string',
                    'default' => "IN"
                ),
                'xcats' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'tag' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'tag__not_in' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'author' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'author__not_in' => array(
                    'type'    => 'string',
                    'default' => ""
                ),

                'items_per_page' => array(
                    'type'    => 'string',
                    'default' => "10"
                ),
                'paging' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'cols' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'order_by' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'order' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'template'      => array(
                    'type'      => 'string',
                    'default'   => 'post/card.php'
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'editor_script' => 'wpdm-posts-block',
            'editor_style' => 'wpdm-block-style',
            'render_callback' => array($this, 'output'),
        ) );
    }

    function output( $attributes, $content){

        global $WPDM;

        $attributes['css_class'] = isset($attributes['className'])?$attributes['className']:'wpdm_posts';
        return "<section class='__wpdm_gb_section __wpdm_gb_posts'>".$this->_posts($attributes)."</section>";
    }

    function _posts($params = array('items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => '','cols'=>3, 'colspad'=>2, 'colsphone' => 1, 'tags' => '', 'categories' => '', 'year' => '', 'month' => '', 's' => '', 'css_class' => 'wpdm_packages', 'e_id' => '', 'async' => 1))
    {
        global $current_user, $post;

        static $wpdm_posts = 0;

        $wpdm_posts++;

        //$params['order_by']  = isset($params['order_field']) && $params['order_field'] != '' && !isset($params['order_by'])?$params['order_field']:$params['order_by'];
        $scparams = $params;
        $defaults = array('author' => '', 'author_name' => '', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => 'link-template-panel','cols'=>3, 'colspad'=>2, 'colsphone' => 1, 'css_class' => 'wpdm_packages', 'e_id' => 'wpdm_packages_'.$wpdm_posts, 'async' => 1);
        $params = shortcode_atts($defaults, $params );

        if(is_array($params))
            extract($params);

        if(!isset($items_per_page) || $items_per_page < 1) $items_per_page = 10;

        $cwd_class = "col-md-".(int)(12/$cols);
        $cwdsm_class = "col-sm-".(int)(12/$colspad);
        $cwdxs_class = "col-xs-".(int)(12/$colsphone);

        if(isset($order_by) && !isset($order_field)) $order_field = $order_by;
        $order_field = isset($order_field) ? $order_field : 'date';
        $order_field = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : $order_field;
        $order = isset($order) ? $order : 'desc';
        $order = isset($_GET['order']) ? esc_attr($_GET['order']) : $order;
        $cp = wpdm_query_var('cp','num');
        if(!$cp) $cp = 1;

        $params = array(
            'paged' => $cp,
            'posts_per_page' => $items_per_page,
        );

        if(isset($scparams['s']) && $scparams['s'] != '') $params['s'] = $scparams['s'];
        if(isset($scparams['author']) && $scparams['author'] != '') $params['author'] = $scparams['author'];
        if(isset($scparams['author_name']) && $scparams['author_name'] != '') $params['author_name'] = $scparams['author_name'];
        if(isset($scparams['author__not_in']) && $scparams['author__not_in'] != '') $params['author__not_in'] = explode(",",$scparams['author__not_in']);
        if(isset($scparams['search']) && $scparams['search'] != '') $params['s'] = $scparams['search'];
        if(isset($scparams['tag']) && $scparams['tag'] != '') $params['tag'] = $scparams['tag'];
        if(isset($scparams['tag_id']) && $scparams['tag_id'] != '') $params['tag_id'] = $scparams['tag_id'];
        if(isset($scparams['tag__and']) && $scparams['tag__and'] != '') $params['tag__and'] = explode(",",$scparams['tag__and']);
        if(isset($scparams['tag__in']) && $scparams['tag__in'] != '') $params['tag__in'] = explode(",",$scparams['tag__in']);
        if(isset($scparams['tag__not_in']) && $scparams['tag__not_in'] != '') {
            $params['tag__not_in'] = explode(",",$scparams['tag__not_in']);
            foreach ($params['tag__not_in'] as &$tg){
                if(!is_numeric($tg)){
                    $tgg = get_term_by('slug', $tg, 'post_tag');
                    $tg = $tgg->term_id;
                }
            }
        }

        if(isset($scparams['tag_slug__and']) && $scparams['tag_slug__and'] != '') $params['tag_slug__and'] = explode(",",$scparams['tag_slug__and']);
        if(isset($scparams['tag_slug__in']) && $scparams['tag_slug__in'] != '') $params['tag_slug__in'] = explode(",",$scparams['tag_slug__in']);
        if(isset($scparams['categories']) && $scparams['categories'] != '') {
            $operator = isset($scparams['operator'])?$scparams['operator']:'IN';
            $scparams['categories'] = trim($scparams['categories'], ",");
            $params['tax_query'] = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => explode(",",$scparams['categories']),
                'include_children' => ( isset($scparams['include_children']) && $scparams['include_children'] != '' )?$scparams['include_children']: false,
                'operator' => $operator
            ));
        }

        if(isset($scparams['xcats']) && $scparams['xcats'] != '') {
            $xcats = explode(",",$scparams['xcats']);
            foreach ($xcats as &$xcat){
                if(!is_numeric($xcat)){
                    $xct = get_term_by('slug', $xcat, 'wpdmcategory');
                    $xcat = $xct->term_id;
                }
            }
            //todo: add categroy query
        }


        if(isset($scparams['year']) ||isset($scparams['month']) || isset($scparams['day'])){
            $date_query = array();

            if(isset($scparams['day']) && $scparams['day'] == 'today') $scparams['day'] = date('d');
            if(isset($scparams['year']) && $scparams['year'] == 'this') $scparams['year'] = date('Y');
            if(isset($scparams['month']) && $scparams['month'] == 'this') $scparams['month'] = date('m');
            if(isset($scparams['week']) && $scparams['week'] == 'this') $scparams['week'] = date('W');

            if(isset($scparams['year']))  $date_query['year'] = $scparams['year'];
            if(isset($scparams['month']))  $date_query['month'] = $scparams['month'];
            if(isset($scparams['week']))  $date_query['week'] = $scparams['week'];
            if(isset($scparams['day']))  $date_query['day'] = $scparams['day'];
            $params['date_query'][] = $date_query;
        }

        if(isset($order_field) && $order_field !== '') {
            $params['orderby'] = $order_field;
            $params['order'] = $order;
        }

        $params = apply_filters("wpdm_posts_query_params", $params);

        $posts = new \WP_Query($params);

        $total = $posts->found_posts;

        $pages = ceil($total / $items_per_page);
        $page = isset($_GET['cp']) ? (int)$_GET['cp'] : 1;
        $start = ($page - 1) * $items_per_page;


        $html = '';

        $default_template = 'post/media.php';

        $template = isset($scparams['template']) ? $scparams['template'] : $default_template;

        $T = new Template();
        while($posts->have_posts()) { $posts->the_post();
            $T->assign('post', $post);
            $repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>". $T->fetch($template, __WPDM_GBDIR__.'/blocks/tpls/') . "</div>";
            $html .=  $repeater;

        }
        wp_reset_postdata();

        $html = "<div class='row'>{$html}</div>";


        if (!isset($paging) || intval($paging) == 1) {
            //$e_id = isset($e_id) && $e_id !== '' ? $e_id : uniqid();
            $pag_links = wpdm_paginate_links( $total, $items_per_page, $page, 'cp', array('container' => '#'.$e_id, 'async' => (isset($async) && $async == 1 ? 1 : 0), 'next_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-top: 2px solid;transform: rotate(45deg);margin-left: -2px;margin-top: -2px;"></i> ', 'prev_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-bottom: 2px solid;transform: rotate(135deg);margin-left: 2px;margin-top: -2px;"></i> '));
            $pgn = "<div style='clear:both'></div>" . $pag_links . "<div style='clear:both'></div>";
        }
        else
            $pgn = "";
        global $post;

        $burl = get_permalink();
        $sap = get_option('permalink_structure') ? '?' : '&';
        $burl = $burl . $sap;
        if (isset($_GET['p']) && $_GET['p'] != '') $burl .= 'p=' . esc_attr($_GET['p']) . '&';
        if (isset($_GET['src']) && $_GET['src'] != '') $burl .= 'src=' . esc_attr($_GET['src']) . '&';

        $css_class = isset($scparams['css_class'])?sanitize_text_field($scparams['css_class']):'';


        return "<div class='w3eden' id='{$e_id}' style='position: relative'><div class='{$css_class}'>" . $html  . $pgn . "<div style='clear:both'></div></div></div>";
    }

    function templates(){
        $files = scandir(__WPDM_GBDIR__.'/blocks/tpls/post/');
        $templates = array();
        foreach ($files as $file){
            if(strpos($file, '.php')){
                $templates[] = array('value' => "post/$file", 'label' => "Plugin / ".ucwords(str_replace(array(".php", "-"), array("", " "), $file)));
            }
        }

        if(file_exists(get_template_directory()."/download-manager/gutenberg/post/")) {
            $path = get_template_directory() . "/download-manager/gutenberg/post/";
            $files = scandir($path);
            foreach ($files as $file) {
                if(strpos($file, '.php')){
                    $templates[] = array('value' => "post/$file", 'label' => "Theme / ".ucwords(str_replace(array(".php", "-"), array("", " "), $file)));
                }
            }
        }

        if(get_template_directory() !== get_stylesheet_directory()) {
            if (file_exists(get_stylesheet_directory() . "/download-manager/gutenberg/post/")) {
                $path = get_stylesheet_directory() . "/download-manager/gutenberg/post/";
                $files = scandir($path);
                foreach ($files as $file) {
                    if (strpos($file, '.php')) {
                        $templates[] = array('value' => "post/$file", 'label' => "Theme / " . ucwords(str_replace(array(".php", "-"), array("", " "), $file)));
                    }
                }
            }
        }

        ?>
        <script>
            var post_templates_gb = <?php echo json_encode($templates); ?>;
        </script>
        <?php
    }


}

new Posts();

