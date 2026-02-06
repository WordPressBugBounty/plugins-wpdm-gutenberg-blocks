<?php
/**
 * User: shahnuralam
 * Date: 8/21/18
 * Time: 7:15 AM
 */

namespace WPDM\Block\libs;

use WPDM\__\Crypt;
use WPDM\Block\traits\DataTableQueryTrait;

class RestAPI
{
    use DataTableQueryTrait;

    /**
     * REST API namespace with version
     */
    const API_NAMESPACE = 'wpdm/v1';

    /**
     * Allowed taxonomies for the categories endpoint
     */
    const ALLOWED_TAXONOMIES = ['wpdmcategory', 'wpdmtag', 'category', 'post_tag'];

    /**
     * Cache TTL constants (in seconds)
     */
    const CACHE_TTL_CATEGORIES = 300;      // 5 minutes
    const CACHE_TTL_TEMPLATES = 3600;      // 1 hour
    const CACHE_TTL_LAYOUTS = 3600;        // 1 hour

    function __construct()
    {
        add_action('rest_api_init', array($this, 'restAPIInit'));

        // Clear caches when relevant content changes
        add_action('created_term', [$this, 'clearCategoryCache'], 10, 3);
        add_action('edited_term', [$this, 'clearCategoryCache'], 10, 3);
        add_action('delete_term', [$this, 'clearCategoryCache'], 10, 3);
        add_action('switch_theme', [$this, 'clearTemplateCache']);
    }

    /**
     * Clear category cache when terms change
     *
     * @param int    $term_id  Term ID
     * @param int    $tt_id    Term taxonomy ID
     * @param string $taxonomy Taxonomy slug
     */
    public function clearCategoryCache($term_id, $tt_id, $taxonomy): void
    {
        if (in_array($taxonomy, self::ALLOWED_TAXONOMIES, true)) {
            delete_transient('wpdm_gb_categories_' . $taxonomy);
        }
    }

    /**
     * Clear template caches when theme changes
     */
    public function clearTemplateCache(): void
    {
        delete_transient('wpdm_gb_link_templates');
        delete_transient('wpdm_gb_post_templates');
        delete_transient('wpdm_gb_layouts');
    }

    /**
     * Permission callback for editor-only endpoints
     * Requires user to have edit_posts capability (can use block editor)
     *
     * @return bool
     */
    function canEditPosts()
    {
        return current_user_can('edit_posts');
    }

    function restAPIInit()
    {
        // Register versioned routes (wpdm/v1)
        $this->registerRoutes(self::API_NAMESPACE);

        // Also register legacy routes (wpdm) for backwards compatibility
        $this->registerRoutes('wpdm');
    }

    /**
     * Register REST routes under a given namespace
     *
     * @param string $namespace REST API namespace
     */
    private function registerRoutes(string $namespace): void
    {
        // Public endpoint - package data respects visibility settings
        register_rest_route($namespace, '/alldownloads', array(
            'methods' => 'GET',
            'callback' => array($this, 'dataTable'),
            'permission_callback' => '__return_true'
        ));

        // Editor-only: search packages for block editor
        register_rest_route($namespace, '/search-package', array(
            'methods' => 'GET',
            'callback' => array($this, 'searchPackages'),
            'permission_callback' => array($this, 'canEditPosts'),
            'args' => array(
                's' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'selected' => array(
                    'required' => false,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                ),
            ),
        ));

        // Editor-only: get link templates for block settings
        register_rest_route($namespace, '/link-templates', array(
            'methods' => 'GET',
            'callback' => array($this, 'linkTemplates'),
            'permission_callback' => array($this, 'canEditPosts')
        ));

        // Editor-only: get post templates for block settings
        register_rest_route($namespace, '/post-templates', array(
            'methods' => 'GET',
            'callback' => array($this, 'postTemplates'),
            'permission_callback' => array($this, 'canEditPosts')
        ));

        // Public endpoint - categories are public information
        register_rest_route($namespace, '/categories', array(
            'methods' => 'GET',
            'callback' => array($this, 'categories'),
            'permission_callback' => '__return_true',
            'args' => array(
                'tax' => array(
                    'required' => false,
                    'type' => 'string',
                    'default' => 'wpdmcategory',
                    'sanitize_callback' => 'sanitize_key',
                    'validate_callback' => array($this, 'validateTaxonomy'),
                ),
            ),
        ));

        // Editor-only: get layout files for block editor
        register_rest_route($namespace, '/layouts', array(
            'methods' => 'GET',
            'callback' => array($this, 'layouts'),
            'permission_callback' => array($this, 'canEditPosts')
        ));

        // Editor-only: get layout content (with path validation)
        register_rest_route($namespace, '/getlayout', array(
            'methods' => 'GET',
            'callback' => array($this, 'getlayout'),
            'permission_callback' => array($this, 'canEditPosts'),
            'args' => array(
                'layout' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
    }

    /**
     * Validate taxonomy parameter against allowed list
     *
     * @param string $value Taxonomy name
     * @return bool|WP_Error
     */
    function validateTaxonomy($value)
    {
        if (!in_array($value, self::ALLOWED_TAXONOMIES, true)) {
            return new \WP_Error(
                'invalid_taxonomy',
                sprintf('Taxonomy must be one of: %s', implode(', ', self::ALLOWED_TAXONOMIES)),
                array('status' => 400)
            );
        }
        return true;
    }

    /**
     * Get link templates for block editor (cached)
     */
    function linkTemplates()
    {
        $cache_key = 'wpdm_gb_link_templates';
        $data = get_transient($cache_key);

        if ($data === false) {
            $data = array();
            $ctpls = WPDM()->packageTemplate->getTemplates("link", true);

            foreach ($ctpls as $ctpl) {
                if (!is_array($ctpl)) {
                    $tmpdata = file_get_contents($ctpl);
                    $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
                    if (preg_match($regx, $tmpdata, $matches)) {
                        $data[] = array(
                            'value' => sanitize_file_name(basename($ctpl)),
                            'label' => sanitize_text_field(trim($matches[1]))
                        );
                    }
                } else {
                    $data[] = array(
                        'value' => sanitize_text_field($ctpl['ID']),
                        'label' => sanitize_text_field($ctpl['name'])
                    );
                }
            }

            set_transient($cache_key, $data, self::CACHE_TTL_TEMPLATES);
        }

        wp_send_json($data);
        die();
    }

    /**
     * Get post templates for block editor
     */
    function postTemplates()
    {
        $templates = array();

        // Plugin templates
        $plugin_path = __WPDM_GBDIR__ . '/blocks/tpls/post/';
        if (is_dir($plugin_path)) {
            $files = scandir($plugin_path);
            foreach ($files as $file) {
                if (strpos($file, '.php') !== false && $file !== '.' && $file !== '..') {
                    $safe_file = sanitize_file_name($file);
                    $label = ucfirst(str_replace(".php", "", $safe_file));
                    $templates[] = array(
                        'value' => $safe_file,
                        'label' => "Plugin / " . sanitize_text_field($label)
                    );
                }
            }
        }

        // Theme templates
        $theme_path = get_template_directory() . "/download-manager/gutenberg/post/";
        if (is_dir($theme_path)) {
            $files = scandir($theme_path);
            foreach ($files as $file) {
                if (strpos($file, '.php') !== false && $file !== '.' && $file !== '..') {
                    $safe_file = sanitize_file_name($file);
                    $label = ucfirst(str_replace(".php", "", $safe_file));
                    $templates[] = array(
                        'value' => $safe_file,
                        'label' => "Theme / " . sanitize_text_field($label)
                    );
                }
            }
        }

        // Child theme templates
        if (get_stylesheet_directory() !== get_template_directory()) {
            $child_path = get_stylesheet_directory() . "/gutenberg/layouts/";
            if (is_dir($child_path)) {
                $files = scandir($child_path);
                foreach ($files as $file) {
                    if (strpos($file, '.php') !== false && $file !== '.' && $file !== '..') {
                        $safe_file = sanitize_file_name($file);
                        $label = ucfirst(str_replace(".php", "", $safe_file));
                        $templates[] = array(
                            'value' => $safe_file,
                            'label' => "Child Theme / " . sanitize_text_field($label)
                        );
                    }
                }
            }
        }

        wp_send_json($templates);
    }

    /**
     * Search packages for block editor
     *
     * @param \WP_REST_Request $request REST request object
     */
    function searchPackages(\WP_REST_Request $request)
    {
        $search = $request->get_param('s') ?: '';
        $selected_id = $request->get_param('selected') ?: 0;

        $data = array();

        // Add selected package first if specified
        if ($selected_id > 0) {
            $selected = get_post($selected_id);
            if ($selected && $selected->post_type === 'wpdmpro') {
                $data[] = array(
                    'value' => (int)$selected->ID,
                    'label' => esc_html($selected->post_title)
                );
            }
        }

        // Search packages
        $packs = get_posts(array(
            'post_type' => 'wpdmpro',
            's' => $search,
            'posts_per_page' => 50, // Limit results for performance
            'post_status' => 'publish',
        ));

        foreach ($packs as $pack) {
            // Skip if already added as selected
            if ($selected_id > 0 && $pack->ID === $selected_id) {
                continue;
            }
            $data[] = array(
                'value' => (int)$pack->ID,
                'label' => esc_html($pack->post_title)
            );
        }

        wp_send_json($data);
        die();
    }

    /**
     * Get categories/terms for block editor (cached)
     *
     * @param \WP_REST_Request $request REST request object
     */
    function categories(\WP_REST_Request $request)
    {
        $tax = $request->get_param('tax') ?: 'wpdmcategory';

        // Double-check taxonomy is allowed (validation should have caught this)
        if (!in_array($tax, self::ALLOWED_TAXONOMIES, true)) {
            $tax = 'wpdmcategory';
        }

        // Check cache first
        $cache_key = 'wpdm_gb_categories_' . $tax;
        $data = get_transient($cache_key);

        if ($data === false) {
            $cats = get_terms(array(
                'taxonomy' => $tax,
                'hide_empty' => false,
            ));

            $data = array();

            if (!is_wp_error($cats)) {
                foreach ($cats as $cat) {
                    $data[] = array(
                        'value' => sanitize_title($cat->slug),
                        'id' => (int)$cat->term_id,
                        'label' => esc_html($cat->name)
                    );
                }
            }

            set_transient($cache_key, $data, self::CACHE_TTL_CATEGORIES);
        }

        wp_send_json($data);
        die();
    }

    /**
     * Get layout files for block editor (cached)
     */
    function layouts()
    {
        $cache_key = 'wpdm_gb_layouts';
        $layouts = get_transient($cache_key);

        if ($layouts === false) {
            $layouts = array();

            // Theme layouts
            $theme_path = get_template_directory() . "/gutenberg/layouts/";
            if (is_dir($theme_path)) {
                $this->scanLayoutDirectory($theme_path, $layouts);
            }

            // Child theme layouts (if different from parent)
            if (get_stylesheet_directory() !== get_template_directory()) {
                $child_path = get_stylesheet_directory() . "/gutenberg/layouts/";
                if (is_dir($child_path)) {
                    $this->scanLayoutDirectory($child_path, $layouts);
                }
            }

            set_transient($cache_key, $layouts, self::CACHE_TTL_LAYOUTS);
        }

        wp_send_json(array_values($layouts));
    }

    /**
     * Scan directory for layout JSON files
     *
     * @param string $path Directory path
     * @param array $layouts Layouts array (passed by reference)
     */
    private function scanLayoutDirectory(string $path, array &$layouts): void
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $file_path = $path . $file;
                $content = file_get_contents($file_path);
                $template = json_decode($content);

                if ($template && isset($template->title)) {
                    $id = md5($file_path);
                    $layouts[$id] = array(
                        'id' => $id,
                        'path' => Crypt::encrypt($file_path),
                        'title' => sanitize_text_field($template->title),
                        'preview' => isset($template->preview) ? esc_url($template->preview) : ''
                    );
                }
            }
        }
    }

    /**
     * Get layout content with path validation to prevent path traversal
     */
    function getlayout(){
        $layout = Crypt::decrypt(wpdm_query_var('layout'));

        // Validate the decrypted path
        if (empty($layout)) {
            wp_send_json_error(['message' => 'Invalid layout parameter'], 400);
            return;
        }

        // Resolve the real path (handles symlinks and ../ sequences)
        $real_path = realpath($layout);

        if ($real_path === false || !file_exists($real_path)) {
            wp_send_json_error(['message' => 'Layout file not found'], 404);
            return;
        }

        // Define allowed directories
        $allowed_dirs = array(
            realpath(get_template_directory() . '/gutenberg/layouts'),
            realpath(get_stylesheet_directory() . '/gutenberg/layouts'),
        );

        // Remove false values (directories that don't exist)
        $allowed_dirs = array_filter($allowed_dirs);

        // Check if the file is within an allowed directory
        $is_allowed = false;
        foreach ($allowed_dirs as $allowed_dir) {
            if (strpos($real_path, $allowed_dir) === 0) {
                $is_allowed = true;
                break;
            }
        }

        if (!$is_allowed) {
            wp_send_json_error(['message' => 'Access denied'], 403);
            return;
        }

        // Ensure file has .json extension
        if (pathinfo($real_path, PATHINFO_EXTENSION) !== 'json') {
            wp_send_json_error(['message' => 'Invalid file type'], 400);
            return;
        }

        // Read and return the template
        $template = file_get_contents($real_path);
        $template = json_decode($template);

        if ($template === null) {
            wp_send_json_error(['message' => 'Invalid JSON content'], 400);
            return;
        }

        wp_send_json($template);
        die();
    }

    /**
     * DataTable REST API endpoint handler
     * Uses DataTableQueryTrait for query building and data formatting
     */
    function dataTable()
    {
        $scparams = Crypt::decrypt(wpdm_query_var('_scparams'), true);

        if (!is_array($scparams)) {
            $scparams = [];
        }

        // Build query using trait method
        $params = $this->buildPackageQuery($scparams);

        // Execute query
        $packs = new \WP_Query($params);
        $total = $packs->found_posts;

        $items_per_page = isset($scparams['items_per_page']) && $scparams['items_per_page'] > 0
            ? (int)$scparams['items_per_page']
            : 10;

        $pages = ceil($total / $items_per_page);
        $all_downloads = $packs->get_posts();

        // Get column configuration
        $colheads = explode("|", wpdm_valueof($scparams, 'colheads'));
        $cols = explode("|", wpdm_valueof($scparams, 'cols'));

        // Format package data using trait method
        $packages = $this->formatPackageData($all_downloads, $cols, $colheads);

        wp_send_json([
            'packages' => $packages,
            'pages' => $pages,
            '_scparams' => Crypt::encrypt($scparams),
            'total' => $total,
            'params' => $params
        ]);
        die();
    }





}

new RestAPI();
