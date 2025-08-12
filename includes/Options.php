<?php

namespace RRZE\JobspreaderAnalytics;

defined('ABSPATH') || exit;

/**
 * Options class for RRZE Jobspreader Analytics plugin.
 * 
 * This class provides methods to manage plugin options, including getting,
 * setting, and sanitizing options.
 * 
 * @package RRZE\JobspreaderAnalytics
 * @since 1.0.0
 */
final class Options
{
    /** @var string */
    protected static $optionName = 'rrze_jobspreader_analytics';

    public static function getOptionName(): string
    {
        return self::$optionName;
    }

    public static function getDefaultOptions(): \stdClass
    {
        return (object) [
            'api_key'   => '',
            'script_placement' => 'body', // 'head' | 'body'
        ];
    }

    public static function getOptions(): \stdClass
    {
        $stored = get_option(self::getOptionName(), []);
        $defaults = (array) self::getDefaultOptions();

        // Merge, ensuring all keys exist
        $merged = wp_parse_args(is_array($stored) ? $stored : (array) $stored, $defaults);

        return (object) $merged;
    }

    public static function updateOptions(array $values): void
    {
        $sanitized = self::sanitize($values);
        update_option(self::getOptionName(), $sanitized);
    }

    public static function sanitize($values): array
    {
        $defaults = (array) self::getDefaultOptions();
        $values = wp_parse_args((array) $values, $defaults);

        $out = [];
        $out['api_key'] = isset($values['api_key']) ? sanitize_text_field($values['api_key']) : '';
        $scriptPlacement = isset($values['script_placement']) ? $values['script_placement'] : 'head';
        $out['script_placement'] = in_array($scriptPlacement, ['head', 'body'], true) ? $scriptPlacement : 'head';

        return $out;
    }
}
