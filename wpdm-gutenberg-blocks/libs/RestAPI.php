<?php
/**
 * User: shahnuralam
 * Date: 8/21/18
 * Time: 7:15 AM
 */

namespace WPDM\Block\libs;

use WPDM\__\Crypt;


class RestAPI
{

    function __construct()
    {

        add_action( 'rest_api_init', array($this, 'restAPIInit'));
    }

    function restAPIInit(){

        //wpdm/v2/alldownloads
        register_rest_route( 'wpdm', '/alldownloads', array(
            'methods' => 'GET',
            'callback' => array($this, 'dataTable'),
            'permission_callback' => '__return_true'
        ) );

        //wpdm/v2/search-package
        register_rest_route( 'wpdm', '/search-package', array(
            'methods' => 'GET',
            'callback' => array($this, 'searchPackages'),
            'permission_callback' => '__return_true'
        ) );

        //wpdm/v2/link-templates
        register_rest_route( 'wpdm', '/link-templates', array(
            'methods' => 'GET',
            'callback' => array($this, 'linkTemplates'),
            'permission_callback' => '__return_true'
        ) );

        //wpdm/v2/post-templates
        register_rest_route( 'wpdm', '/post-templates', array(
            'methods' => 'GET',
            'callback' => array($this, 'postTemplates'),
            'permission_callback' => '__return_true'
        ) );

        //wpdm/v2/categories
        register_rest_route( 'wpdm', '/categories', array(
            'methods' => 'GET',
            'callback' => array($this, 'categories'),
            'permission_callback' => '__return_true'
        ) );

        //wpdm/v2/layouts
        register_rest_route( 'wpdm', '/layouts', array(
            'methods' => 'GET',
            'callback' => array($this, 'layouts'),
            'permission_callback' => '__return_true'
        ) );

        //wpdm/v2/getlayout
        register_rest_route( 'wpdm', '/getlayout', array(
            'methods' => 'GET',
            'callback' => array($this, 'getlayout'),
            'permission_callback' => '__return_true'
        ) );
    }

    function linkTemplates()
    {

        $ctpls = WPDM()->packageTemplate->getTemplates("link", true);
        foreach ($ctpls as $ctpl) {
            if(!is_array($ctpl)){
                $tmpdata = file_get_contents($ctpl);
                $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
                if (preg_match($regx, $tmpdata, $matches)) {
                    $data[] = array('value' => basename($ctpl), 'label' => $matches[1]);
                }
            } else {
                $data[] = array('value' => $ctpl['ID'], 'label' => $ctpl['name']);
            }
        }

        wp_send_json($data);
        die();

    }

    function postTemplates(){
        $files = scandir(__WPDM_GBDIR__.'/blocks/tpls/post/');
        $templates = array();
        foreach ($files as $file){
            if(strpos($file, '.php')){
                $templates[] = array('value' => $file, 'label' => "Plugin / ".ucfirst(str_replace(".php", "", $file)));
            }
        }

        if(file_exists(get_template_directory()."/download-manager/gutenberg/post/")) {
            $path = get_template_directory() . "/download-manager/gutenberg/post/";
            $files = scandir($path);
            foreach ($files as $file) {
                if(strpos($file, '.php')){
                    $templates[] = array('value' => $file, 'label' => "Plugin / ".ucfirst(str_replace(".php", "", $file)));
                }
            }
        }

        if(file_exists(get_stylesheet_directory()."/gutenberg/layouts/")) {
            $path = get_stylesheet_directory() . "/gutenberg/layouts/";
            $files = scandir($path);
            foreach ($files as $file) {
                if(strpos($file, '.php')){
                    $templates[] = array('value' => $file, 'label' => "Plugin / ".ucfirst(str_replace(".php", "", $file)));
                }
            }
        }

        wp_send_json($templates);
    }

    function searchPackages(){

        $packs = get_posts(array('post_type' => 'wpdmpro','s' => wpdm_query_var('s', 'txt'), 'posts_per_page' => -1));
        $data = array();
        if(wpdm_query_var('selected', 'int') > 0){
            $selected = get_post(wpdm_query_var('selected'));
            $data[] = ['value' => $selected->ID, 'label' => $selected->post_title];
        }
        foreach ($packs as $pack){
            if(wpdm_query_var('selected', 'int') !== $pack->ID)
                $data[] = array('value' => $pack->ID, 'label' => $pack->post_title);
        }
        wp_send_json($data);
        die();

    }

    function categories(){
        $tax = wpdm_query_var('tax');
        $tax = $tax ? $tax : 'wpdmcategory';
        $cats = get_terms(array('taxonomy' => $tax,
            'hide_empty' => false));
        $data = array();
        foreach ($cats as $cat){
            $data[] = array('value' => $cat->slug, 'id' => $cat->term_id, 'label' => $cat->name);
        }
        wp_send_json($data);
        die();

    }

    function layouts(){
        $layouts = array();
        if(file_exists(get_template_directory()."/gutenberg/layouts/")) {
            $path = get_template_directory() . "/gutenberg/layouts/";
            $files = scandir($path);
            foreach ($files as $file) {
                if (strstr($file, '.json')) {
                    $template = file_get_contents($path.$file);
                    $template = json_decode($template);
                    $layouts[md5($path.$file)] = array('id' => md5($path.$file), 'path' => Crypt::encrypt($path.$file), 'title' => $template->title, 'preview' => $template->preview);
                }
            }
        }

        if(file_exists(get_stylesheet_directory()."/gutenberg/layouts/")) {
            $path = get_stylesheet_directory() . "/gutenberg/layouts/";
            $files = scandir($path);
            foreach ($files as $file) {
                if (strstr($file, '.json')) {
                    $template = file_get_contents($path.$file);
                    $template = json_decode($template);
                    $layouts[md5($path.$file)] = array('id' => md5($path.$file), 'path' => Crypt::encrypt($path.$file), 'title' => $template->title, 'preview' => $template->preview);
                }
            }
        }

        wp_send_json(array_values($layouts));

    }

    function getlayout(){
        $layout = Crypt::decrypt(wpdm_query_var('layout'));
        $template = file_get_contents($layout);
        $template = json_decode($template);
        //echo stripslashes_deep(htmlspecialchars_decode(str_replace(array("\n", "\r"), "", $template->content)));
        wp_send_json($template);
        die();
    }

    function dataTable()
    {
        global $current_user, $post;

        $scparams = Crypt::decrypt(wpdm_query_var('_scparams'), true);

        //$defaults = array('author' => '', 'author_name' => '', 'categories' => '', 'xcats' => '', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => 'link-template-panel', 'cols' => 'title,file_count,download_count|categories|update_date|download_link', 'colheads' => 'Title|Categories|Update Date|Download', 'css_class' => 'wpdm_packages',  'async' => 1);
        //$scparams = shortcode_atts($defaults, $scparams, 'wpdm_packages');
	    //wpdmdd($scparams);
        if (is_array($scparams))
            extract($scparams);

        if (!isset($items_per_page) || $items_per_page < 1) $items_per_page = 10;

        if (isset($order_by) && !isset($order_field)) $order_field = $order_by;
        $order_field = isset($order_field) ? $order_field : 'date';
        $order_field = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : $order_field;
        $order = isset($order) ? $order : 'desc';
        $order = isset($_GET['order']) ? esc_attr($_GET['order']) : $order;
        $cp = wpdm_query_var('cp', 'num');
        if (!$cp) $cp = 1;

        $params = array(
            'post_type' => 'wpdmpro',
            'paged' => $cp,
            'posts_per_page' => $items_per_page,
        );

        if (isset($scparams['s']) && $scparams['s'] != '') $params['s'] = $scparams['s'];
        if (isset($_GET['skw']) && $_GET['skw'] != '') $scparams['s'] = $params['s'] = wpdm_query_var('skw', 'txt');
        if (isset($scparams['author']) && $scparams['author'] != '') $params['author'] = $scparams['author'];
        if (isset($scparams['author_name']) && $scparams['author_name'] != '') $params['author_name'] = $scparams['author_name'];
        if (isset($scparams['author__not_in']) && $scparams['author__not_in'] != '') $params['author__not_in'] = explode(",", $scparams['author__not_in']);
        if (isset($scparams['search']) && $scparams['search'] != '') $params['s'] = $scparams['search'];
        if (isset($scparams['tag']) && $scparams['tag'] != '') $params['tag'] = $scparams['tag'];
        if (isset($scparams['tag_id']) && $scparams['tag_id'] != '') $params['tag_id'] = $scparams['tag_id'];
        if (isset($scparams['tag__and']) && $scparams['tag__and'] != '') $params['tag__and'] = explode(",", $scparams['tag__and']);
        if (isset($scparams['tag__in']) && $scparams['tag__in'] != '') $params['tag__in'] = explode(",", $scparams['tag__in']);
        if (isset($scparams['tag__not_in']) && $scparams['tag__not_in'] != '') {
            $params['tag__not_in'] = explode(",", $scparams['tag__not_in']);
            foreach ($params['tag__not_in'] as &$tg) {
                if (!is_numeric($tg)) {
                    $tgg = get_term_by('slug', $tg, 'post_tag');
                    $tg = $tgg->term_id;
                }
            }
        }

        if (isset($scparams['post__in']) && $scparams['post__in'] != '') $params['post__in'] = explode(",", $scparams['post__in']);
        if (isset($scparams['post__not_in']) && $scparams['post__not_in'] != '') $params['post__not_in'] = explode(",", $scparams['post__not_in']);

        if (isset($scparams['tag_slug__and']) && $scparams['tag_slug__and'] != '') $params['tag_slug__and'] = explode(",", $scparams['tag_slug__and']);
        if (isset($scparams['tag_slug__in']) && $scparams['tag_slug__in'] != '') $params['tag_slug__in'] = explode(",", $scparams['tag_slug__in']);
        if(wpdm_query_var('category') !== '') {
            $cat = get_term(wpdm_query_var('category', 'int'), 'wpdmcategory');
            $scparams['categories'] = is_object($cat) ? $cat->name : '';
        }
        if (isset($scparams['categories']) && $scparams['categories'] != '') {
            $operator = isset($scparams['operator']) ? $scparams['operator'] : 'IN';
            $scparams['categories'] = trim($scparams['categories'], ",");
            $__cats = [];
            $scparams['categories'] = explode(",", $scparams['categories']);

            foreach ($scparams['categories'] as $cat){
                $_term = get_term_by("slug", $cat, 'wpdmcategory');
                $__cats[] = $_term->term_id;
            }
            $params['tax_query'] = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'term_id',
                'terms' => $__cats,
                'include_children' => (isset($scparams['include_children']) && $scparams['include_children'] != '') ? $scparams['include_children'] : false,
                'operator' => $operator
            ));

        }

        if (isset($scparams['xcats']) && $scparams['xcats'] != '') {
            $xcats = explode(",", $scparams['xcats']);
            foreach ($xcats as &$xcat) {
                if (!is_numeric($xcat) && $xcat !== '') {
                    $xct = get_term_by('slug', $xcat, 'wpdmcategory');
                    $xcat = $xct->term_id;
                }
            }
            $params['tax_query'][] = array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'term_id',
                'terms' => $xcats,
                'operator' => 'NOT IN',
            );
        }

        if (isset($params['tax_query']) && count($params['tax_query']) > 1)
            $params['tax_query']['relation'] = 'AND';
        else
            $params['tax_query']['relation'] = 'OR';


        if (get_option('_wpdm_hide_all', 0) == 1) {
            $params['meta_query'] = array(
                array(
                    'key' => '__wpdm_access',
                    'value' => '"guest"',
                    'compare' => 'LIKE'
                )
            );
            if (is_user_logged_in()) {
                $params['meta_query'][] = array(
                    'key' => '__wpdm_access',
                    'value' => $current_user->roles[0],
                    'compare' => 'LIKE'
                );
                $params['meta_query']['relation'] = 'OR';
            }
        }

        if (isset($scparams['year']) || isset($scparams['month']) || isset($scparams['day'])) {
            $date_query = array();

            if (isset($scparams['day']) && $scparams['day'] == 'today') $scparams['day'] = date('d');
            if (isset($scparams['year']) && $scparams['year'] == 'this') $scparams['year'] = date('Y');
            if (isset($scparams['month']) && $scparams['month'] == 'this') $scparams['month'] = date('m');
            if (isset($scparams['week']) && $scparams['week'] == 'this') $scparams['week'] = date('W');

            if (isset($scparams['year'])) $date_query['year'] = $scparams['year'];
            if (isset($scparams['month'])) $date_query['month'] = $scparams['month'];
            if (isset($scparams['week'])) $date_query['week'] = $scparams['week'];
            if (isset($scparams['day'])) $date_query['day'] = $scparams['day'];
            $params['date_query'][] = $date_query;
        }

        $order_fields = array('__wpdm_download_count', '__wpdm_view_count', '__wpdm_package_size_b');
        if (!in_array("__wpdm_" . $order_field, $order_fields)) {
            $scparams['orderby'] = $params['orderby'] = $order_field;
            $scparams['order'] = $params['order'] = $order;
        } else {
            $scparams['orderby'] = $order_field;
            $params['orderby'] = 'meta_value_num';
            $params['meta_key'] = "__wpdm_" . $order_field;
            $scparams['order'] = $params['order'] = $order;
        }

        $params = apply_filters("wpdm_packages_query_params", $params);

		$packs = new \WP_Query($params);

        $total = $packs->found_posts;

        $pages = ceil($total / $items_per_page);
        $page = isset($_GET['cp']) ? (int)$_GET['cp'] : 1;
        $start = ($page - 1) * $items_per_page;

        $all_downloads = $packs->get_posts();

        $colheads =  explode("|", wpdm_valueof($scparams,  'colheads'));
        $cols =  explode("|", wpdm_valueof($scparams,  'cols'));

        $packages = [];
        foreach ($all_downloads as $download) {
            $package = [];
            $author = get_user_by('id', $download->post_author);
            $download->author_package_count = count_user_posts($download->post_author, "wpdmpro");
            $download->author_name = $author->display_name;
            $download->author_pic = get_avatar($author->ID, 32, '', '', ['class' => 'mr-2']);
            $download_link = WPDM()->package->userCanDownload($download->ID) ? WPDM()->package->downloadLink($download->ID, 0, ['template_type'  =>  'link']) : '<a href="'.get_permalink($download->ID).'" class="btn btn-block btn-danger">Unlock</a>';
            if(function_exists('wpdmpp_currency_sign') &&  (wpdmpp_effective_price($download->ID) > 0 || (int)get_post_meta($download->ID, '__wpdm_pay_as_you_want',  true) === 1))
                $download_link = wpdmpp_waytocart((array)$download, 'btn-primary');
	        $btnstyle = wpdm_download_button_style();
            foreach ($cols as $col_index => $data_field_pack) {
                $data_field_parts = explode(",", $data_field_pack);
                foreach ($data_field_parts  as $part_index => $data_field) {
                    $xclass = ($part_index > 0)?'small-txt':'';
                    switch ($data_field) {
                        case 'thumb':
                            $package[$data_field] = "<a href='".get_permalink($download->ID)."'>" . wpdm_thumb($download, [96,96], false, ['crop' => true, 'class' =>  'datatable-thumb']) . "</a>";
                            break;
                        case 'icon':
                            $package[$data_field] = "<a href='".get_permalink($download->ID)."'>" . WPDM()->package->icon($download->ID,  true, 'datatable-icon') . "</a>";
                            break;
                        case 'title':
                            $package[$data_field] = "<strong class='d-block'>" . $download->post_title . "</strong>";
                            break;
                        case 'page_link':
                            $package[$data_field] = "<a class=\"package-title d-block\" href='" . get_the_permalink($download->ID) . "'>" . $download->post_title . "</a>";
                            break;
                        case 'excerpt':
                        case (preg_match('/excerpt_.+/', $data_field) ? true : false) :
                            $xcol = explode("_", $data_field);
                            $len = isset($xcol[1]) ? $xcol[1] : false;
                            $cont = strip_tags($download->post_content);

                            if (!$len)
                                $package[$data_field] = "<div class='__dt_excerpt {$xclass}'>" . get_the_excerpt() . "</div>";
                            else {
                                $excerpt = strlen($cont) > $len ? substr($cont, 0, strpos($cont, ' ', $len)) : $cont;
                                $package[$data_field] = "<div class='__dt_excerpt {$xclass}'>" . $excerpt . "</div>";
                            }
                            break;
                        case 'file_count':
                            $file_count  = WPDM()->package->fileCount($download->ID);
                            if ($part_index > 0)
                                $package[$data_field] = "<span class='__dt_file_count {$xclass}'><i class=\"far fa-copy\"></i> " . $file_count . " " . __('file(s)', 'download-manager') . "</span>";
                            else
                                $package[$data_field] = "<span class=\"hidden-md hidden-lg td-mobile\">{$colheads[$col_index]}: </span><span class='__dt_file_count {$xclass}'>" . $file_count . "</span>";
                            break;
                        case 'download_count':
                            $download_count = (int)get_post_meta($download->ID, '__wpdm_download_count', true);
                            if ($part_index > 0)
                                $package[$data_field] = "<span class='__dt_download_count {$xclass}'><i class=\"far fa-arrow-alt-circle-down\"></i> " . $download_count . " " . ($download_count > 1 ? __('downloads', 'download-manager') : __('download', 'download-manager')) . "</span>";
                            else
                                $package[$data_field] = "<span class=\"hidden-md hidden-lg td-mobile\">{$colheads[$col_index]}: </span><span class='__dt_download_count {$xclass}'>{$download_count}</span>";
                            break;
                        case 'view_count':
                            $view_count = (int)get_post_meta($download->ID, '__wpdm_view_count', true);
                            if ($part_index > 0)
                                $package[$data_field] = "<span class='__dt_view_count {$xclass}'><i class=\"fa fa-eye\"></i> " . ($view_count ? $view_count : 0) . " " . ($view_count > 1 ? __('views', 'download-manager') : __('view', 'download-manager')) . "</span>";
                            else
                                $package[$data_field] = "<span class=\"hidden-md hidden-lg td-mobile\">{$colheads[$col_index]}: </span><span class='__dt_view_count'>{$view_count}</span>";
                            break;
                        case 'categories':
                            $cats = wp_get_post_terms($download->ID, 'wpdmcategory');
                            $fcats = array();
                            foreach($cats as $cat){
                                $fcats[] = "<a class='sbyc' href='#'>{$cat->name}</a>";
                            }
                            $cats = @implode(", ", $fcats);
                            $package[$data_field] = "<span class='__dt_categories {$xclass}'>" . $cats . "</span>";
                            break;
                        case 'tags':
                            $cats = wp_get_post_terms($download->ID, 'wpdmtag');
                            $fcats = array();
                            foreach($cats as $cat){
                                $fcats[] = "<a class='sbyc' href='#'>{$cat->name}</a>";
                            }
                            $cats = @implode(", ", $fcats);
                            $package[$data_field] = "<span class='__dt_tags {$xclass}'>" . $cats . "</span>";
                            break;
                        case 'update_date':
                            $package[$data_field] = "<span class='__dt_update_date {$xclass}'>" . get_the_modified_date('', $download->ID) . "</span>";
                            break;
                        case 'date':
                        case 'publish_date':
                            $package[$data_field] = "<span class='__dt_publish_date {$xclass}'>" . get_the_date(get_option('date_format'), $download) . "</span>";
                            break;
                        case 'download_link':
                            $package[$data_field] = $download_link ? $download_link : '<button type="button" disabled="disabled" class="btn btn-danger">' . WPDM()->package->getLinkLabel($download->ID) . '</button>';
                            break;
                        case 'details_link':
                            $package[$data_field] = '<a href="'.get_permalink($download->ID).'" class="'.$btnstyle.'">' . WPDM()->package->getLinkLabel($download->ID) . '</a>';
                            break;
                        case 'audio_player':
                            $data['files'] = WPDM()->package->getFiles($download->ID);
                            $package[$data_field] = WPDM()->package->audioPlayer($data, true, 'success');
                            break;
                        default:
                            if (isset($download->$data_field)) {
                                    $field_data = $download->$data_field;
                            } else {
                                $field_data = get_post_meta($download->ID, '__wpdm_'.$data_field, true);
                            }
                            if ($part_index > 0) {
                                $package[$data_field] = "<span class='__dt_{$data_field} {$xclass}'>" . $field_data . "</span>";
                            } else {
                                $package[$data_field] = $field_data;
                            }
                            break;
                    }
                }
                if(count($data_field_parts) > 1) {
                    $package[str_replace(",", "__", $data_field_pack)] = "";
                    foreach ($data_field_parts as $data_field_part) {
                        $package[str_replace(",", "__", $data_field_pack)] .= $package[$data_field_part];
                    }
                }
            }
            $packages[] = $package;
        }
        wp_send_json(['packages' => $packages, 'pages' => $pages, '_scparams' => Crypt::encrypt($scparams), 'total' => $total, 'params' => $params]);
        die();
    }





}

new RestAPI();
