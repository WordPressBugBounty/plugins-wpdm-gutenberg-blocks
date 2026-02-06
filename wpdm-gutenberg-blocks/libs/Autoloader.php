<?php
/**
 * PSR-4 Autoloader for WPDM Gutenberg Blocks
 *
 * @package WPDM\Block
 */

namespace WPDM\Block;

/**
 * Autoloader class for automatically loading plugin classes.
 */
class Autoloader
{
    /**
     * Namespace prefix for this autoloader
     */
    private const PREFIX = 'WPDM\\Block\\';

    /**
     * Base directory for the namespace prefix
     */
    private static string $baseDir;

    /**
     * Class map for non-standard file locations
     *
     * @var array<string, string>
     */
    private static array $classMap = [];

    /**
     * Register the autoloader
     */
    public static function register(): void
    {
        self::$baseDir = __WPDM_GBDIR__ . '/libs/';

        // Build class map for blocks and other classes with non-standard names
        self::buildClassMap();

        spl_autoload_register([__CLASS__, 'loadClass']);
    }

    /**
     * Build the class map for non-standard file locations
     */
    private static function buildClassMap(): void
    {
        self::$classMap = [
            // Traits
            'WPDM\\Block\\traits\\BlockJsonTrait' => self::$baseDir . 'traits/BlockJsonTrait.php',
            'WPDM\\Block\\traits\\DataTableQueryTrait' => self::$baseDir . 'traits/DataTableQueryTrait.php',

            // Core classes
            'WPDM\\Block\\libs\\RestAPI' => self::$baseDir . 'RestAPI.php',

            // Block classes - map class names to file paths
            'WPDM\\Block\\Packages' => self::$baseDir . 'blocks/packages.php',
            'WPDM\\Block\\Category' => self::$baseDir . 'blocks/category.php',
            'WPDM\\Block\\CategoryCards' => self::$baseDir . 'blocks/category-cards.php',
            'WPDM\\Block\\Package' => self::$baseDir . 'blocks/package.php',
            'WPDM\\Block\\SignupForm' => self::$baseDir . 'blocks/signup-form.php',
            'WPDM\\Block\\SigninForm' => self::$baseDir . 'blocks/signin-form.php',
            'WPDM\\Block\\Search' => self::$baseDir . 'blocks/search.php',
            'WPDM\\Block\\Dashboard' => self::$baseDir . 'blocks/dashboard.php',
            'WPDM\\Block\\DataTable' => self::$baseDir . 'blocks/datatable.php',
            'WPDM\\Block\\DropZone' => self::$baseDir . 'blocks/dropzone.php',
            'WPDM\\Block\\Call2Action' => self::$baseDir . 'blocks/call2action.php',
            'WPDM\\Block\\CardSlider' => self::$baseDir . 'blocks/card-slider.php',
            'WPDM\\Block\\Panel' => self::$baseDir . 'blocks/panel.php',
            'WPDM\\Block\\Posts' => self::$baseDir . 'blocks/posts.php',
            'WPDM\\Block\\Row' => self::$baseDir . 'blocks/row.php',
            'WPDM\\Block\\Section' => self::$baseDir . 'blocks/section.php',
            'WPDM\\Block\\Text' => self::$baseDir . 'blocks/text.php',
            'WPDM\\Block\\TheCategory' => self::$baseDir . 'blocks/the-category.php',
        ];
    }

    /**
     * Load a class file
     *
     * @param string $class The fully-qualified class name
     */
    public static function loadClass(string $class): void
    {
        // Check class map first
        if (isset(self::$classMap[$class])) {
            $file = self::$classMap[$class];
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }

        // Check if the class uses our namespace prefix
        if (strpos($class, self::PREFIX) !== 0) {
            return;
        }

        // Get the relative class name
        $relativeClass = substr($class, strlen(self::PREFIX));

        // Convert namespace separators to directory separators
        $file = self::$baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }

    /**
     * Load all block files (for backwards compatibility during transition)
     * This ensures all blocks are instantiated even if not using namespaced class names
     */
    public static function loadAllBlocks(): void
    {
        $blockFiles = [
            'blocks/packages.php',
            'blocks/category.php',
            'blocks/category-cards.php',
            'blocks/package.php',
            'blocks/signup-form.php',
            'blocks/signin-form.php',
            'blocks/search.php',
            'blocks/dashboard.php',
            'blocks/datatable.php',
            'blocks/dropzone.php',
            'blocks/call2action.php',
            'blocks/card-slider.php',
            'blocks/panel.php',
            'blocks/posts.php',
            'blocks/row.php',
            'blocks/section.php',
            'blocks/text.php',
            'blocks/the-category.php',
        ];

        foreach ($blockFiles as $file) {
            $fullPath = self::$baseDir . $file;
            if (file_exists($fullPath)) {
                require_once $fullPath;
            }
        }
    }
}
