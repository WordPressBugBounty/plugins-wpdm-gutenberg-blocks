<?php
/**
 * Block JSON Trait
 *
 * Provides methods to read defaults from block.json files,
 * establishing block.json as the single source of truth for block attributes.
 *
 * @package WPDM\Block\traits
 */

namespace WPDM\Block\traits;

if (!defined('ABSPATH')) die();

trait BlockJsonTrait
{
    /**
     * Cached defaults from block.json
     *
     * @var array|null
     */
    private static ?array $blockDefaults = null;

    /**
     * Path to block.json file (should be set by the class using this trait)
     *
     * @var string
     */
    private static string $blockJsonPath = '';

    /**
     * Get default values from block.json attributes
     *
     * @param string|null $blockJsonPath Optional path to block.json (uses static property if not provided)
     * @return array Default values keyed by attribute name
     */
    public static function getBlockDefaults(?string $blockJsonPath = null): array
    {
        // Use provided path or fall back to static property
        $path = $blockJsonPath ?? self::$blockJsonPath;

        if (empty($path)) {
            return [];
        }

        // Return cached defaults if available and path matches
        if (self::$blockDefaults !== null && $blockJsonPath === null) {
            return self::$blockDefaults;
        }

        $defaults = [];

        if (file_exists($path)) {
            $content = file_get_contents($path);
            $blockJson = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($blockJson['attributes']) && is_array($blockJson['attributes'])) {
                foreach ($blockJson['attributes'] as $key => $config) {
                    $defaults[$key] = $config['default'] ?? null;
                }
            }
        }

        // Cache if using static property
        if ($blockJsonPath === null) {
            self::$blockDefaults = $defaults;
        }

        return $defaults;
    }

    /**
     * Get full attribute definitions from block.json for register_block_type()
     *
     * @return array Attribute definitions array
     */
    public static function getBlockAttributes(): array
    {
        $path = self::$blockJsonPath;

        if (empty($path) || !file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        $blockJson = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($blockJson['attributes'])) {
            return [];
        }

        return $blockJson['attributes'];
    }

    /**
     * Set the block.json path for this block
     *
     * @param string $path Path to block.json file
     */
    protected static function setBlockJsonPath(string $path): void
    {
        self::$blockJsonPath = $path;
        // Reset cache when path changes
        self::$blockDefaults = null;
    }

    /**
     * Register block using block.json as the source of truth
     *
     * @param callable|null $renderCallback Optional render callback
     * @param array $additionalArgs Additional arguments to pass to register_block_type
     * @return \WP_Block_Type|false The registered block type on success, or false on failure
     */
    protected function registerBlockFromJson(?callable $renderCallback = null, array $additionalArgs = [])
    {
        if (empty(self::$blockJsonPath) || !file_exists(self::$blockJsonPath)) {
            return false;
        }

        $args = $additionalArgs;

        if ($renderCallback !== null) {
            $args['render_callback'] = $renderCallback;
        }

        return register_block_type(self::$blockJsonPath, $args);
    }

    /**
     * Apply block defaults to shortcode parameters
     *
     * @param array $params Shortcode parameters
     * @param array $additionalDefaults Additional defaults not in block.json
     * @param string $shortcodeName Shortcode name for shortcode_atts filter
     * @return array Merged parameters with defaults
     */
    protected function applyBlockDefaults(array $params, array $additionalDefaults = [], string $shortcodeName = ''): array
    {
        $defaults = array_merge($additionalDefaults, self::getBlockDefaults());
        return shortcode_atts($defaults, $params, $shortcodeName);
    }
}
