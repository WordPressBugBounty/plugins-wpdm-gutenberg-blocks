<?php
/**
 * User: shahnuralam
 * Date: 10/01/19
 * Time: 07:46 PM
 */

namespace WPDM\Block;

use WPDM\__\Messages;
use WPDM\__\Template;

if (!defined('ABSPATH')) die();

class DataTable{

    function __construct(){
        add_action( 'init', array($this, 'block'), 9 );
    }

    function block(){

        register_block_type( 'download-manager/datatable', array(
            'attributes'      => array(
                'login' => array(
                    'type'    => 'boolean',
                    'default' => false
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
                'title' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'desc' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'toolbar' => array(
                    'type'    => 'string',
                    'default' => "1"
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
                    'default' => ""
                ),
                'colheads' => array(
                    'type'    => 'string',
                    'default' => ""
                ),

                'orderby' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'order' => array(
                    'type'    => 'string',
                    'default' => ""
                ),
                'async' => array(
                    'type'    => 'string',
                    'default' => "1"
                ),
                'template'      => array(
                    'type'      => 'string',
                    'default'   => 'link-template-panel'
                ),
                'className'      => array(
                    'type'      => 'string',
                    'default'   => ''
                )
            ),
            'render_callback' => array($this, 'output'),
        ) );
    }

    /**
     * @param array $params
     * @return string
     */
    function dataTable($params = array('items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => '', 'cols' => "", 'colheads' => "", 'tags' => '', 'categories' => '', 'year' => '', 'month' => '', 's' => '', 'css_class' => 'wpdm_packages', 'scid' => '', 'async' => 1))
    {
        global $current_user, $post;

        static $wpdm_packages = 0;

        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return WPDM()->user->login->form($params);

        $wpdm_packages++;

        //$params['order_by']  = isset($params['order_field']) && $params['order_field'] != '' && !isset($params['order_by'])?$params['order_field']:$params['order_by'];
        $scparams = $params;
        $defaults = array('author' => '', 'author_name' => '', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => 'link-template-panel', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'css_class' => 'wpdm_packages', 'scid' => 'wpdm_packages_' . $wpdm_packages, 'async' => 1);
        $params = shortcode_atts($defaults, $params, 'wpdm_packages');

        if (is_array($params))
            extract($params);

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
        if (isset($_GET['skw']) && $_GET['skw'] != '') $params['s'] = wpdm_query_var('skw', 'txt');
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
        if (isset($scparams['categories']) && $scparams['categories'] != '') {
            $operator = isset($scparams['operator']) ? $scparams['operator'] : 'IN';
            $scparams['categories'] = trim($scparams['categories'], ",");
            $params['tax_query'] = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => explode(",", $scparams['categories']),
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
            $params['orderby'] = $order_field;
            $params['order'] = $order;
        } else {
            $params['orderby'] = 'meta_value_num';
            $params['meta_key'] = "__wpdm_" . $order_field;
            $params['order'] = $order;
        }

        $params = apply_filters("wpdm_packages_query_params", $params);

        //wpdmprecho($params);
        $packs = new \WP_Query($params);

        $total = $packs->found_posts;
        $pages = ceil($total / $items_per_page);
        $page = isset($_GET['cp']) ? (int)$_GET['cp'] : 1;
        $start = ($page - 1) * $items_per_page;

        global $post;

        $burl = get_permalink();
        $sap = get_option('permalink_structure') ? '?' : '&';
        $burl = $burl . $sap;
        if (isset($_GET['p']) && $_GET['p'] != '') $burl .= 'p=' . esc_attr($_GET['p']) . '&';
        if (isset($_GET['src']) && $_GET['src'] != '') $burl .= 'src=' . esc_attr($_GET['src']) . '&';
        $orderby = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : 'date';
        $order = ucfirst($order);

        $order_field = " " . __(ucwords(str_replace("_", " ", $order_field)), "wpdmpro");
        $ttitle = __("Title", "download-manager");
        $tdls = __("Downloads", "download-manager");
        $tcdate = __("Publish Date", "download-manager");
        $tudate = __("Update Date", "download-manager");
        $tasc = __("Asc", "download-manager");
        $tdsc = __("Desc", "download-manager");
        $tsrc = __("Search", "download-manager");
        $ord = __("Order", "download-manager");
        $order_by_label = __("Order By", "download-manager");

        $css_class = isset($scparams['css_class']) ? sanitize_text_field($scparams['css_class']) : '';
        $desc = isset($scparams['desc']) ? sanitize_text_field($scparams['desc']) : '';

        $title = isset($title) && $title != '' && $total > 0 ? "<h3>$title</h3>" : "";


        $toolbar = isset($toolbar) ? $toolbar : 0;

        $colheads =  explode("|", wpdm_valueof($scparams,  'colheads', ['validate' => 'esc_attr']));
        $cols =  explode("|", wpdm_valueof($scparams,  'cols', ['validate' => 'esc_attr']));

        ob_start();
        include Template::locate("datatable.php", WPDM_SRC_DIR.'Package/views');
        $content = ob_get_clean();
        return $content;
    }


    function output( $attributes, $content){
        if(version_compare(WPDM_VERSION, '5.0', '<')) return Messages::info("This  block is only available with <a href='https://www.wpdownloadmanager.com/pricing/' target=_blank >WordPress Download Manager Pro</a>", -1);
        if($attributes['cols'] === '') unset($attributes['cols']);
        if($attributes['colheads'] === '') unset($attributes['colheads']);
        return $this->dataTable($attributes);
    }

}

new DataTable();

