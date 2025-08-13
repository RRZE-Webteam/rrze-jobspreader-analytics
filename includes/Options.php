<?php

namespace RRZE\JobspreaderAnalytics;

defined('ABSPATH') || exit;

/**
 * Class Options
 * 
 * This class provides methods to manage plugin options, including getting default options,
 * retrieving stored options, and sanitizing input before saving.
 *
 * @package RRZE\JobspreaderAnalytics
 * @since 1.0.0
 */
final class Options
{
    /**
     * Option name for the plugin.
     *
     * @var string
     */
    protected static $optionName = 'rrze_jobspreader_analytics';

    /**
     * Get the option name for the plugin.
     *
     * @return string
     */
    public static function getOptionName(): string
    {
        return self::$optionName;
    }

    /**
     * Get the default options for the plugin.
     *
     * @return \stdClass
     */
    public static function getDefaultOptions(): \stdClass
    {
        return (object) [
            'api_key'            => '',
            'script_placement'   => 'body', // 'head' | 'body'
            'tracked_post_types' => 'job',  // one slug per line, default to 'job'
        ];
    }

    /**
     * Get the options from the database, merging with defaults.
     *
     * @return \stdClass
     */
    public static function getOptions(): \stdClass
    {
        $stored   = get_option(self::getOptionName(), []);
        $defaults = (array) self::getDefaultOptions();

        $merged = wp_parse_args(is_array($stored) ? $stored : (array) $stored, $defaults);

        return (object) $merged;
    }

    /**
     * Update the options in the database.
     *
     * @param array $values The values to update.
     * @return void
     */
    public static function updateOptions(array $values): void
    {
        $sanitized = self::sanitize($values);
        update_option(self::getOptionName(), $sanitized);
    }

    /**
     * Sanitize the options before saving.
     *
     * @param array $values The values to sanitize.
     * @return array The sanitized values.
     */
    public static function sanitize($values): array
    {
        $defaults = (array) self::getDefaultOptions();
        $values   = wp_parse_args((array) $values, $defaults);

        $out = [];

        $out['api_key'] = isset($values['api_key']) ? sanitize_text_field($values['api_key']) : '';

        $placement = isset($values['script_placement']) ? $values['script_placement'] : 'body';
        $out['script_placement'] = in_array($placement, ['head', 'body'], true) ? $placement : 'body';

        // Sanitize tracked_post_types
        $raw   = sanitize_textarea_field($values['tracked_post_types']);
        $lines = array_filter(
            array_map('trim', preg_split("/\r\n|\r|\n/", $raw))
        );
        $lines = array_map('sanitize_key', $lines);
        $out['tracked_post_types'] = implode("\n", array_unique($lines));

        return $out;
    }
}
