<?php
/**
 * Plugin Name:  WPDM - Editor Blocks
 * Plugin URI: https://www.wpdownloadmanager.com/download/gutenberg-blocks/
 * Description: Editor Blocks for Download Manager
 * Author: WordPress Download Manager
 * Version:  3.0.2
 * Author URI: https://www.wpdownloadmanager.com/
 * Text Domain: wpdm-gblocks
 * Domain Path: /languages
 *
 * @package WPDM\Block
 */

namespace WPDM\Block;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('WPDM_GB_VERSION', '3.0.2');
define('__WPDM_GB__', __FILE__);
define('__WPDM_GBDIR__', __DIR__);
define('__WPDM_GBURL__', plugins_url('/', __FILE__));

/**
 * Main plugin class
 */
class Blocks
{
    /**
     * Plugin instance
     *
     * @var Blocks|null
     */
    private static ?Blocks $instance = null;

    /**
     * Get plugin instance (singleton)
     *
     * @return Blocks
     */
    public static function instance(): Blocks
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        // Load autoloader and dependencies
        $this->loadDependencies();

        // Register hooks
        add_action('init', [$this, 'loadTextDomain']);
        add_action('init', [$this, 'registerScripts'], 10);
        add_action('admin_head', [$this, 'wpAdminHead'], 10);
        add_action('enqueue_block_assets', [$this, 'enqueueScripts'], 1);
        add_action('enqueue_block_editor_assets', [$this, 'adminEnqueueScripts'], 1);
        add_filter('block_categories_all', [$this, 'registerBlockCategory'], 10, 2);
    }

    /**
     * Load plugin dependencies
     */
    private function loadDependencies(): void
    {
        // Load traits first (required by other classes)
        require_once __WPDM_GBDIR__ . '/libs/traits/BlockJsonTrait.php';
        require_once __WPDM_GBDIR__ . '/libs/traits/DataTableQueryTrait.php';

        // Load autoloader
        require_once __WPDM_GBDIR__ . '/libs/Autoloader.php';
        Autoloader::register();

        // Load REST API
        require_once __WPDM_GBDIR__ . '/libs/RestAPI.php';

        // Load all block files
        Autoloader::loadAllBlocks();
    }

    /**
     * Load plugin text domain for translations
     */
    public function loadTextDomain(): void
    {
        load_plugin_textdomain(
            'wpdm-gblocks',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    /**
     * Register the custom block category
     *
     * @param array    $categories Block categories
     * @param \WP_Post $post       Current post
     * @return array Modified categories
     */
    public function registerBlockCategory(array $categories, $post): array
    {
        return array_merge(
            $categories,
            [
                [
                    'slug'  => 'wpdm-blocks',
                    'title' => __('Download Manager Blocks', 'wpdm-gblocks'),
                    'icon'  => 'download',
                ],
            ]
        );
    }

    /**
     * Output admin head scripts for block editor
     */
    public function wpAdminHead(): void
    {
        $cats = get_terms([
            'taxonomy'   => 'wpdmcategory',
            'hide_empty' => false,
        ]);

        $data = [];
        if (!is_wp_error($cats) && is_array($cats)) {
            foreach ($cats as $cat) {
                $data[] = [
                    'value' => esc_attr($cat->slug),
                    'label' => esc_html($cat->name),
                ];
            }
        }

        // Get user roles for signup form
        global $wp_roles;
        $roles = [];
        if ($wp_roles) {
            foreach ($wp_roles->roles as $key => $role) {
                $roles[] = [
                    'value' => esc_attr($key),
                    'label' => esc_html($role['name']),
                ];
            }
        }
        ?>
        <script>
            var wpdmgb_route_base = <?php echo wp_json_encode(esc_url(get_rest_url())); ?>;
            var wpdm_categories = <?php echo wp_json_encode($data); ?>;
            var __wpdm_roles = <?php echo wp_json_encode($roles); ?>;
        </script>
        <?php
    }

    /**
     * Register plugin scripts and styles
     */
    public function registerScripts(): void
    {
        wp_register_style(
            'wpdm-block-style-front',
            plugins_url('css/block-front.css', __FILE__),
            [],
            WPDM_GB_VERSION
        );
    }

    /**
     * Enqueue frontend block assets
     */
    public function enqueueScripts(): void
    {
        $url = untrailingslashit(plugin_dir_url(__FILE__));

        wp_enqueue_style(
            'wpdm-gutenberg-blocks-frontend',
            $url . '/build/style.css',
            [],
            WPDM_GB_VERSION
        );
    }

    /**
     * Enqueue block editor assets
     *
     * @param string $hook Current admin page
     */
    public function adminEnqueueScripts(string $hook = ''): void
    {
        // Don't load on package edit screen
        if (get_post_type() === 'wpdmpro') {
            return;
        }

        $url = untrailingslashit(plugin_dir_url(__FILE__));

        // Enqueue main block script
        wp_enqueue_script(
            'wpdm-gutenberg-blocks',
            $url . '/build/index.js',
            ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data'],
            WPDM_GB_VERSION,
            true
        );

        // Set script translations for i18n
        wp_set_script_translations(
            'wpdm-gutenberg-blocks',
            'wpdm-gblocks',
            __WPDM_GBDIR__ . '/languages'
        );

        // Deregister conflicting admin styles
        wp_deregister_style('wpdm-admin-bootstrap');
        wp_dequeue_style('wpdm-admin-bootstrap');
        wp_dequeue_style('wpdm-admin-styles');

        // Register WPDM admin script if available
        if (defined('WPDM_BASE_URL')) {
            wp_register_script(
                'wpdm-admin',
                WPDM_BASE_URL . 'assets/js/wpdm-admin.js',
                ['jquery'],
                WPDM_GB_VERSION,
                true
            );
        }

        // Build style dependencies
        $deps = ['wp-edit-blocks'];
        if (!defined('ATTIRE_BLOCKS_VERSION') && defined('WPDM_BASE_URL')) {
            wp_register_style(
                'wpdm-gb-styles',
                WPDM_BASE_URL . 'assets/css/front.min.css',
                [],
                WPDM_GB_VERSION
            );
            $deps[] = 'wpdm-gb-styles';
        }

        // Enqueue editor styles
        wp_enqueue_style(
            'wpdm-gutenberg-blocks-editor',
            $url . '/build/editor.css',
            $deps,
            WPDM_GB_VERSION
        );
    }
}

// Initialize plugin when WPDM is active
if (defined('WPDM_VERSION')) {
    Blocks::instance();
}
