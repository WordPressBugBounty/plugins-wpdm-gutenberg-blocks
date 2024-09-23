<?php
/**
 * User: shahnuralam
 * Date: 8/19/18
 * Time: 1:01 PM
 */

/**
 * Plugin Name:  WPDM - Gutenberg Blocks
 * Plugin URI: https://www.wpdownloadmanager.com/download/gutenberg-blocks/
 * Description: Gutenberg Blocks for WordPress Download Manager
 * Author: WordPress Download Manager
 * Version:  2.3.0
 * Author URI: https://www.wpdownloadmanager.com/
 */
namespace WPDM\Block;

define("__WPDM_GB__", __FILE__);
define("__WPDM_GBDIR__", __DIR__);
define("__WPDM_GBURL__", plugins_url('/', __FILE__));

class Blocks
{

    function __construct()
    {


        include dirname(__FILE__) . '/libs/RestAPI.php';
        include dirname(__FILE__) . '/libs/blocks/packages.php';
        include dirname(__FILE__) . '/libs/blocks/category.php';
        include dirname(__FILE__) . '/libs/blocks/category-cards.php';
        include dirname(__FILE__) . '/libs/blocks/package.php';
        include dirname(__FILE__) . '/libs/blocks/signup-form.php';
        include dirname(__FILE__) . '/libs/blocks/signin-form.php';
        include dirname(__FILE__) . '/libs/blocks/search.php';
        include dirname(__FILE__) . '/libs/blocks/dashboard.php';
        include dirname(__FILE__) . '/libs/blocks/datatable.php';
        include dirname(__FILE__) . '/libs/blocks/dropzone.php';

        add_filter('block_categories_all', function ($categories, $post) {

            $categories = array_merge(
                $categories,
                array(
                    array(
                        'slug' => 'wpdm-blocks',
                        'title' => __('Download Manager Blocks', 'wpdm-gblocks'),
                    ),
                )
            );
         ;
            return $categories;
        }, 10, 2);

        add_action("admin_head", array($this, 'wpAdminHead'), 10);
        add_action("init", array($this, 'registerScripts'), 10);
        add_action("enqueue_block_assets", array($this, 'enqueueScripts'), 1);
        add_action("enqueue_block_editor_assets", array($this, 'adminEnqueueScripts'), 1);



    }

    function wpAdminHead($vars)
    {
        ?>
        <script>
            var wpdmgb_route_base = '<?php echo get_rest_url(); ?>';
            <?php
            $cats = get_terms(array('taxonomy' => 'wpdmcategory',
                'hide_empty' => false));
            $data = array();
            foreach ($cats as $cat){
                $data[] = array('value' => $cat->slug, 'label' => $cat->name);
            }
            ?>
            var wpdm_categories = <?php echo json_encode($data); ?>;
        </script>
        <?php
    }

    function registerScripts()
    {

        wp_register_style(
            'wpdm-block-style-front',
            plugins_url('css/block-front.css', __FILE__)
        );

    }

    function enqueueScripts()
    {
        $url = untrailingslashit( plugin_dir_url( __FILE__ ) );

        wp_enqueue_style(
            'my-custom-block-frontend-css', // Handle.
            $url . '/build/style.css'
        );

    }

    function adminEnqueueScripts($hook)
    {
        if (get_post_type() !== 'wpdmpro') {

            $url = untrailingslashit( plugin_dir_url( __FILE__ ) );
            // Scripts.
            wp_enqueue_script(
                'wpdm-gutenberg-blocks', // Handle.
                $url . '/build/index.js',
                array( 'wp-blocks', 'wp-i18n', 'wp-element',  'wp-editor' )
            );

            wp_deregister_style('wpdm-admin-bootstrap');
            wp_dequeue_style('wpdm-admin-bootstrap');
            wp_dequeue_style('wpdm-admin-styles');
            wp_register_script('wpdm-admin', plugins_url('/download-manager/assets/js/wpdm-admin.js'), array('jquery'));
            // Styles.
            $deps = ['wp-edit-blocks'];
            if(!defined('ATTIRE_BLOCKS_VERSION')) {
                wp_register_style('wpdm-gb-bootstrap', plugins_url('/download-manager/assets/bootstrap/css/bootstrap.min.css'));
                $deps[] = 'wpdm-gb-bootstrap';
            }
            wp_enqueue_style(
                'wpdm-gutenberg-blocks-editor-css', // Handle.
                $url . '/build/editor.css',
                $deps
            );


        }

    }


}

if (defined('WPDM_VERSION'))
    new Blocks();



