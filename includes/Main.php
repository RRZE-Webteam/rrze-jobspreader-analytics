<?php

namespace RRZE\JobspreaderAnalytics;

defined('ABSPATH') || exit;

use RRZE\JobspreaderAnalytics\Options;
use RRZE\JobspreaderAnalytics\Settings;
use function RRZE\JobspreaderAnalytics\plugin;

/**
 * Main class for the RRZE Jobspreader Analytics plugin.
 * 
 * This class initializes the plugin, registers activation and deactivation hooks,
 * and sets up the settings page and scripts.
 * 
 * @package RRZE\JobspreaderAnalytics
 * @since 1.0.0
 */
class Main
{
    /**
     * Options
     * 
     * @var \stdClass
     */
    protected $options;

    /**
     * Settings instance
     * 
     * @var Settings
     */
    protected $settings;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->options = Options::getOptions();

        $this->settings = new Settings();

        add_filter('plugin_action_links_' . plugin()->getBaseName(), [$this, 'settingsLink']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Add a settings link to the plugin action links.
     * 
     * @param array $links
     * @return array
     */
    public function settingsLink($links): array
    {
        $settingsLink = sprintf(
            '<a href="%s">%s</a>',
            admin_url('options-general.php?page=' . $this->settings->getMenuSlug()),
            __('Settings', 'rrze-jobspreader-analytics')
        );
        array_unshift($links, $settingsLink);
        return $links;
    }

    /**
     * Enqueue admin scripts
     *
     * @return void
     */
    public function enqueueScripts()
    {
        if (empty(trim($this->options->api_key))) {
            return;
        }

        $assetFile = include plugin()->getPath('build') . 'jobspreader-analytics.asset.php';
        $inFooter = ($this->options->script_placement === 'body');

        wp_register_script(
            'rrze-jobspreader-analytics',
            plugins_url('build/jobspreader-analytics.js', plugin()->getBasename()),
            $assetFile['dependencies'] ?? [],
            $assetFile['version'] ?? plugin()->getVersion(),
            $inFooter
        );

        wp_localize_script('rrze-jobspreader-analytics', 'JobSpreaderData', [
            'apiKey'    => $this->options->api_key,
            'scriptPlacement' => $this->options->script_placement,
        ]);
    }
}
