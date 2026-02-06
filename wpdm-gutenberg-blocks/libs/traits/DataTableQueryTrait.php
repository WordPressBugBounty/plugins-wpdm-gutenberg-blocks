<?php
/**
 * DataTable Query Trait
 *
 * Shared logic for building WP_Query params and formatting package data
 * Used by both RestAPI.php and datatable.php to avoid code duplication
 *
 * @package WPDM\Block
 * @since 2.5.0
 */

namespace WPDM\Block\traits;

trait DataTableQueryTrait
{
    /**
     * Build WP_Query parameters from shortcode parameters
     *
     * @param array $scparams Shortcode parameters
     * @return array WP_Query compatible parameters
     */
    protected function buildPackageQuery(array $scparams): array
    {
        global $current_user;

        // Get pagination and ordering from params or request
        $items_per_page = isset($scparams['items_per_page']) && $scparams['items_per_page'] > 0
            ? (int)$scparams['items_per_page']
            : 10;

        $order_field = $scparams['order_by'] ?? ($scparams['order_field'] ?? 'date');
        $order_field = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : $order_field;

        $order = $scparams['order'] ?? 'desc';
        $order = isset($_GET['order']) ? esc_attr($_GET['order']) : $order;

        $cp = wpdm_query_var('cp', 'num');
        if (!$cp) $cp = 1;

        // Base query params
        $params = array(
            'post_type' => 'wpdmpro',
            'paged' => $cp,
            'posts_per_page' => $items_per_page,
        );

        // Search
        if (!empty($scparams['s'])) $params['s'] = $scparams['s'];
        if (isset($_GET['skw']) && $_GET['skw'] != '') $params['s'] = wpdm_query_var('skw', 'txt');
        if (!empty($scparams['search'])) $params['s'] = $scparams['search'];

        // Author filters
        if (!empty($scparams['author'])) $params['author'] = $scparams['author'];
        if (!empty($scparams['author_name'])) $params['author_name'] = $scparams['author_name'];
        if (!empty($scparams['author__not_in'])) $params['author__not_in'] = explode(",", $scparams['author__not_in']);

        // Tag filters - use tax_query with WPDM_TAG taxonomy
        $this->addTagQueries($params, $scparams);

        // Post inclusion/exclusion
        if (!empty($scparams['post__in'])) $params['post__in'] = explode(",", $scparams['post__in']);
        if (!empty($scparams['post__not_in'])) $params['post__not_in'] = explode(",", $scparams['post__not_in']);

        // Category from URL
        if (wpdm_query_var('category') !== '') {
            $cat = get_term(wpdm_query_var('category', 'int'), 'wpdmcategory');
            $scparams['categories'] = is_object($cat) ? $cat->slug : '';
        }

        // Category filters
        $this->addCategoryQueries($params, $scparams);

        // Set tax_query relation
        if (isset($params['tax_query']) && count($params['tax_query']) > 1) {
            $params['tax_query']['relation'] = 'AND';
        }

        // Access control meta query
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

        // Date query
        $this->addDateQuery($params, $scparams);

        // Order by
        $order_fields = array('__wpdm_download_count', '__wpdm_view_count', '__wpdm_package_size_b');
        if (!in_array("__wpdm_" . $order_field, $order_fields)) {
            $params['orderby'] = $order_field;
            $params['order'] = $order;
        } else {
            $params['orderby'] = 'meta_value_num';
            $params['meta_key'] = "__wpdm_" . $order_field;
            $params['order'] = $order;
        }

        return apply_filters("wpdm_packages_query_params", $params);
    }

    /**
     * Add tag-related tax_query parameters
     *
     * @param array $params Query params (passed by reference)
     * @param array $scparams Shortcode params
     */
    protected function addTagQueries(array &$params, array $scparams): void
    {
        // Tag by slug
        if (!empty($scparams['tag'])) {
            $tag_terms = array_map('trim', explode(",", $scparams['tag']));
            $params['tax_query'][] = array(
                'taxonomy' => WPDM_TAG,
                'field' => 'slug',
                'terms' => $tag_terms,
                'operator' => 'IN'
            );
        }

        // Tag by ID
        if (!empty($scparams['tag_id'])) {
            $tag_ids = array_map('intval', explode(",", $scparams['tag_id']));
            $params['tax_query'][] = array(
                'taxonomy' => WPDM_TAG,
                'field' => 'term_id',
                'terms' => $tag_ids,
                'operator' => 'IN'
            );
        }

        // Tag AND (must have all)
        if (!empty($scparams['tag__and'])) {
            $tag_ids = array_map('intval', explode(",", $scparams['tag__and']));
            $params['tax_query'][] = array(
                'taxonomy' => WPDM_TAG,
                'field' => 'term_id',
                'terms' => $tag_ids,
                'operator' => 'AND'
            );
        }

        // Tag IN (have any)
        if (!empty($scparams['tag__in'])) {
            $tag_ids = array_map('intval', explode(",", $scparams['tag__in']));
            $params['tax_query'][] = array(
                'taxonomy' => WPDM_TAG,
                'field' => 'term_id',
                'terms' => $tag_ids,
                'operator' => 'IN'
            );
        }

        // Tag NOT IN (exclude)
        if (!empty($scparams['tag__not_in'])) {
            $tag_terms = array_map('trim', explode(",", $scparams['tag__not_in']));
            $tag_ids = array();
            foreach ($tag_terms as $tg) {
                if (is_numeric($tg)) {
                    $tag_ids[] = intval($tg);
                } else {
                    $term = get_term_by('slug', $tg, WPDM_TAG);
                    if ($term) {
                        $tag_ids[] = $term->term_id;
                    }
                }
            }
            if (!empty($tag_ids)) {
                $params['tax_query'][] = array(
                    'taxonomy' => WPDM_TAG,
                    'field' => 'term_id',
                    'terms' => $tag_ids,
                    'operator' => 'NOT IN'
                );
            }
        }

        // Tag slug AND
        if (!empty($scparams['tag_slug__and'])) {
            $tag_slugs = array_map('trim', explode(",", $scparams['tag_slug__and']));
            $params['tax_query'][] = array(
                'taxonomy' => WPDM_TAG,
                'field' => 'slug',
                'terms' => $tag_slugs,
                'operator' => 'AND'
            );
        }

        // Tag slug IN
        if (!empty($scparams['tag_slug__in'])) {
            $tag_slugs = array_map('trim', explode(",", $scparams['tag_slug__in']));
            $params['tax_query'][] = array(
                'taxonomy' => WPDM_TAG,
                'field' => 'slug',
                'terms' => $tag_slugs,
                'operator' => 'IN'
            );
        }
    }

    /**
     * Add category-related tax_query parameters
     *
     * @param array $params Query params (passed by reference)
     * @param array $scparams Shortcode params
     */
    protected function addCategoryQueries(array &$params, array $scparams): void
    {
        // Include categories
        if (!empty($scparams['categories'])) {
            $operator = $scparams['operator'] ?? 'IN';
            $categories = trim($scparams['categories'], ",");
            $categories = explode(",", $categories);

            $cat_ids = [];
            foreach ($categories as $cat) {
                $cat = trim($cat);
                if (is_numeric($cat)) {
                    $cat_ids[] = intval($cat);
                } else {
                    $_term = get_term_by("slug", $cat, 'wpdmcategory');
                    if ($_term) {
                        $cat_ids[] = $_term->term_id;
                    }
                }
            }

            if (!empty($cat_ids)) {
                $params['tax_query'][] = array(
                    'taxonomy' => 'wpdmcategory',
                    'field' => 'term_id',
                    'terms' => $cat_ids,
                    'include_children' => !empty($scparams['include_children']),
                    'operator' => $operator
                );
            }
        }

        // Exclude categories
        if (!empty($scparams['xcats'])) {
            $xcats = explode(",", $scparams['xcats']);
            $xcat_ids = [];
            foreach ($xcats as $xcat) {
                $xcat = trim($xcat);
                if (is_numeric($xcat)) {
                    $xcat_ids[] = intval($xcat);
                } else if ($xcat !== '') {
                    $xct = get_term_by('slug', $xcat, 'wpdmcategory');
                    if ($xct) {
                        $xcat_ids[] = $xct->term_id;
                    }
                }
            }
            if (!empty($xcat_ids)) {
                $params['tax_query'][] = array(
                    'taxonomy' => 'wpdmcategory',
                    'field' => 'term_id',
                    'terms' => $xcat_ids,
                    'operator' => 'NOT IN',
                );
            }
        }
    }

    /**
     * Add date query parameters
     *
     * @param array $params Query params (passed by reference)
     * @param array $scparams Shortcode params
     */
    protected function addDateQuery(array &$params, array $scparams): void
    {
        if (isset($scparams['year']) || isset($scparams['month']) || isset($scparams['day']) || isset($scparams['week'])) {
            $date_query = array();

            // Handle special values
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
    }

    /**
     * Format package data for table display
     *
     * @param array $downloads Array of WP_Post objects
     * @param array $cols Column definitions
     * @param array $colheads Column headers
     * @return array Formatted package data
     */
    protected function formatPackageData(array $downloads, array $cols, array $colheads): array
    {
        $packages = [];
        $btnstyle = wpdm_download_button_style();

        foreach ($downloads as $download) {
            $package = [];

            // Get author info
            $author = get_user_by('id', $download->post_author);
            $download->author_package_count = count_user_posts($download->post_author, "wpdmpro");
            $download->author_name = $author ? $author->display_name : '';
            $download->author_pic = $author ? get_avatar($author->ID, 32, '', '', ['class' => 'mr-2']) : '';

            // Get download link
            $download_link = WPDM()->package->userCanDownload($download->ID)
                ? WPDM()->package->downloadLink($download->ID, 0, ['template_type' => 'link'])
                : '<a href="'.get_permalink($download->ID).'" class="btn btn-block btn-danger">Unlock</a>';

            // Check for premium packages
            if (function_exists('wpdmpp_currency_sign') &&
                (wpdmpp_effective_price($download->ID) > 0 || (int)get_post_meta($download->ID, '__wpdm_pay_as_you_want', true) === 1)) {
                $download_link = wpdmpp_waytocart((array)$download, 'btn-primary');
            }

            // Format each column
            foreach ($cols as $col_index => $data_field_pack) {
                $data_field_parts = explode(",", $data_field_pack);
                foreach ($data_field_parts as $part_index => $data_field) {
                    $xclass = ($part_index > 0) ? 'small-txt' : '';
                    $colhead = $colheads[$col_index] ?? '';

                    $package[$data_field] = $this->formatField(
                        $data_field,
                        $download,
                        $download_link,
                        $btnstyle,
                        $xclass,
                        $colhead,
                        $part_index
                    );
                }

                // Combine multi-field columns
                if (count($data_field_parts) > 1) {
                    $combined_key = str_replace(",", "__", $data_field_pack);
                    $package[$combined_key] = "";
                    foreach ($data_field_parts as $data_field_part) {
                        $package[$combined_key] .= $package[$data_field_part];
                    }
                }
            }

            $packages[] = $package;
        }

        return $packages;
    }

    /**
     * Format a single field value
     *
     * @param string $data_field Field name
     * @param \WP_Post $download Post object
     * @param string $download_link Download link HTML
     * @param string $btnstyle Button style class
     * @param string $xclass Extra CSS class
     * @param string $colhead Column header text
     * @param int $part_index Part index for multi-field columns
     * @return string Formatted field HTML
     */
    protected function formatField(
        string $data_field,
        \WP_Post $download,
        string $download_link,
        string $btnstyle,
        string $xclass,
        string $colhead,
        int $part_index
    ): string {
        switch ($data_field) {
            case 'thumb':
                return "<a href='".get_permalink($download->ID)."'>" .
                    wpdm_thumb($download, [96,96], false, ['crop' => true, 'class' => 'datatable-thumb']) . "</a>";

            case 'icon':
                return "<a href='".get_permalink($download->ID)."'>" .
                    WPDM()->package->icon($download->ID, true, 'datatable-icon') . "</a>";

            case 'title':
                return "<strong class='d-block'>" . esc_html($download->post_title) . "</strong>";

            case 'page_link':
                return "<a class=\"package-title d-block\" href='" . get_the_permalink($download->ID) . "'>" .
                    esc_html($download->post_title) . "</a>";

            case 'excerpt':
            case (preg_match('/excerpt_.+/', $data_field) ? true : false):
                $xcol = explode("_", $data_field);
                $len = isset($xcol[1]) ? (int)$xcol[1] : false;
                $cont = strip_tags($download->post_content);

                if (!$len) {
                    return "<div class='__dt_excerpt {$xclass}'>" . get_the_excerpt($download) . "</div>";
                } else {
                    $excerpt = strlen($cont) > $len ? substr($cont, 0, strpos($cont, ' ', $len)) : $cont;
                    return "<div class='__dt_excerpt {$xclass}'>" . esc_html($excerpt) . "</div>";
                }

            case 'file_count':
                $file_count = WPDM()->package->fileCount($download->ID);
                if ($part_index > 0) {
                    return "<span class='__dt_file_count {$xclass}'><i class=\"far fa-copy\"></i> " .
                        $file_count . " " . __('file(s)', 'download-manager') . "</span>";
                }
                return "<span class=\"hidden-md hidden-lg td-mobile\">{$colhead}: </span>" .
                    "<span class='__dt_file_count {$xclass}'>" . $file_count . "</span>";

            case 'download_count':
                $download_count = (int)get_post_meta($download->ID, '__wpdm_download_count', true);
                if ($part_index > 0) {
                    return "<span class='__dt_download_count {$xclass}'><i class=\"far fa-arrow-alt-circle-down\"></i> " .
                        $download_count . " " .
                        ($download_count > 1 ? __('downloads', 'download-manager') : __('download', 'download-manager')) . "</span>";
                }
                return "<span class=\"hidden-md hidden-lg td-mobile\">{$colhead}: </span>" .
                    "<span class='__dt_download_count {$xclass}'>{$download_count}</span>";

            case 'view_count':
                $view_count = (int)get_post_meta($download->ID, '__wpdm_view_count', true);
                if ($part_index > 0) {
                    return "<span class='__dt_view_count {$xclass}'><i class=\"fa fa-eye\"></i> " .
                        ($view_count ?: 0) . " " .
                        ($view_count > 1 ? __('views', 'download-manager') : __('view', 'download-manager')) . "</span>";
                }
                return "<span class=\"hidden-md hidden-lg td-mobile\">{$colhead}: </span>" .
                    "<span class='__dt_view_count'>{$view_count}</span>";

            case 'categories':
                $cats = wp_get_post_terms($download->ID, 'wpdmcategory');
                $fcats = array();
                foreach ($cats as $cat) {
                    $fcats[] = "<a class='sbyc' href='#'>" . esc_html($cat->name) . "</a>";
                }
                return "<span class='__dt_categories {$xclass}'>" . implode(", ", $fcats) . "</span>";

            case 'tags':
                $tags = wp_get_post_terms($download->ID, WPDM_TAG);
                $ftags = array();
                foreach ($tags as $tag) {
                    $ftags[] = "<a class='sbyc' href='#'>" . esc_html($tag->name) . "</a>";
                }
                return "<span class='__dt_tags {$xclass}'>" . implode(", ", $ftags) . "</span>";

            case 'update_date':
                return "<span class='__dt_update_date {$xclass}'>" . get_the_modified_date('', $download->ID) . "</span>";

            case 'date':
            case 'publish_date':
                return "<span class='__dt_publish_date {$xclass}'>" . get_the_date(get_option('date_format'), $download) . "</span>";

            case 'download_link':
                return $download_link ?: '<button type="button" disabled="disabled" class="btn btn-danger">' .
                    WPDM()->package->getLinkLabel($download->ID) . '</button>';

            case 'details_link':
                return '<a href="'.get_permalink($download->ID).'" class="'.$btnstyle.'">' .
                    WPDM()->package->getLinkLabel($download->ID) . '</a>';

            case 'audio_player':
                $data['files'] = WPDM()->package->getFiles($download->ID);
                return WPDM()->package->audioPlayer($data, true, 'success');

            default:
                if (isset($download->$data_field)) {
                    $field_data = $download->$data_field;
                } else {
                    $field_data = get_post_meta($download->ID, '__wpdm_' . $data_field, true);
                }
                if ($part_index > 0) {
                    return "<span class='__dt_{$data_field} {$xclass}'>" . esc_html($field_data) . "</span>";
                }
                return esc_html($field_data);
        }
    }
}
