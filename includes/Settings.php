<?php

namespace RRZE\JobspreaderAnalytics;

defined('ABSPATH') || exit;

use RRZE\JobspreaderAnalytics\Options;

/**
 * Settings class for RRZE Jobspreader Analytics plugin.
 * * This class provides methods to manage plugin settings, including adding
 * a settings page, registering settings, and rendering fields.
 *
 * @package RRZE\JobspreaderAnalytics
 * @since 1.0.0
 */
final class Settings
{
    /**
     * Option name
     * 
     * @var string
     */
    protected $optionName;

    /**
     * Options
     * 
     * @var \stdClass
     */
    protected $options;


    /**
     * Default options
     * 
     * @var \stdClass
     */
    protected $defaultOptions;

    /**
     * Menu page slug
     * 
     * @var string
     */
    protected $menuSlug = 'rrze-jobspreader-analytics';

    /**
     * Settings section name
     * 
     * @var string
     */
    protected $sectionName;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->optionName = Options::getOptionName();
        $this->options = Options::getOptions();
        $this->defaultOptions = Options::getDefaultOptions();

        add_action('admin_menu', [$this, 'addMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * Get the menu page slug.
     *
     * @return string
     */
    public function getMenuSlug(): string
    {
        return $this->menuSlug;
    }

    public function addMenu(): void
    {
        add_options_page(
            __('Jobspreader Analytics', 'rrze-jobspreader-analytics'),
            __('Jobspreader Analytics', 'rrze-jobspreader-analytics'),
            'manage_options',
            $this->menuSlug,
            [$this, 'renderPage']
        );
    }

    /**
     * Register settings for the plugin.
     *
     * This method registers the settings for the plugin, including the option name,
     * default values, and sanitization callback.
     *
     * @return void
     */
    public function registerSettings(): void
    {
        register_setting(
            'rrze_jobspreader_analytics_section',
            $this->optionName,
            [
                'type'              => 'array',
                'sanitize_callback' => [Options::class, 'sanitize'],
                'default'           => (array) $this->defaultOptions,
                'show_in_rest'      => false,
            ]
        );

        add_settings_section(
            'rrze_jobspreader_analytics_main',
            __('Settings', 'rrze-jobspreader-analytics'),
            function () {
                echo '<p>' . esc_html__('Configuration of the Jobspreader Analytics loader.', 'rrze-jobspreader-analytics') . '</p>';
            },
            $this->menuSlug
        );


        add_settings_field(
            'api_key',
            __('API Key', 'rrze-jobspreader-analytics'),
            [$this, 'fieldApiKey'],
            $this->menuSlug,
            'rrze_jobspreader_analytics_main',
            ['label_for' => 'api_key']
        );

        // Script Placement
        add_settings_field(
            'script_placement',
            __('Script Placement', 'rrze-jobspreader-analytics'),
            [$this, 'fieldScriptPlacement'],
            $this->menuSlug,
            'rrze_jobspreader_analytics_main',
            ['label_for' => 'script_placement']
        );

        // Tracked post types
        add_settings_field(
            'tracked_post_types',
            __('Tracked post types', 'rrze-jobspreader-analytics'),
            [$this, 'fieldTrackedPostTypes'],
            $this->menuSlug,
            'rrze_jobspreader_analytics_main',
            ['label_for' => 'tracked_post_types']
        );
    }

    /**
     * Render the API key input field.
     * 
     * @param array $args
     * @return void
     */
    public function fieldApiKey(array $args): void
    {
        $value = $this->options->api_key ?? '';
        printf(
            '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" placeholder="abcdef0123456789abcdef0123456789">',
            esc_attr($args['label_for']),
            esc_attr($this->optionName),
            esc_attr($value)
        );
        echo '<p class="description">' .
            sprintf(
                // translators: %s is a placeholder for the support email address.
                esc_html__('Please enter the Jobspreader Analytics API key received by Wollmilchsau. Get support at %s.', 'rrze-jobspreader-analytics'),
                '<a href="mailto:support@wollmilchsau.de">support@wollmilchsau.de</a>'
            ) . '</p>';
    }

    /**
     * Render the script placement select field.
     * 
     * @param array $args
     * @return void
     */
    public function fieldScriptPlacement(array $args): void
    {
        $value = $this->options->script_placement ?? 'head';
?>
        <select id="<?php echo esc_attr($args['label_for']); ?>"
            name="<?php echo esc_attr($this->optionName); ?>[<?php echo esc_attr($args['label_for']); ?>]">
            <option value="head" <?php selected($value, 'head'); ?>><?php esc_html_e('Head', 'rrze-jobspreader-analytics'); ?></option>
            <option value="body" <?php selected($value, 'body'); ?>><?php esc_html_e('Body (footer)', 'rrze-jobspreader-analytics'); ?></option>
        </select>
        <p class="description">
            <?php esc_html_e('Choose where to inject the external Jobspreader script.', 'rrze-jobspreader-analytics'); ?>
        </p>
    <?php
    }

    public function fieldTrackedPostTypes(array $args): void
    {
        $value = $this->options->tracked_post_types ?? '';

        // Build placeholder with public post types names
        $public = get_post_types(['public' => true], 'names');
        sort($public, SORT_NATURAL);
        $placeholder = implode("\n", array_map('esc_html', $public));
    ?>
        <textarea id="<?php echo esc_attr($args['label_for']); ?>"
            name="<?php echo esc_attr($this->optionName); ?>[<?php echo esc_attr($args['label_for']); ?>]"
            rows="4" cols="40" class="code"
            placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">
            <?php esc_html_e('Enter one post type slug per line to enable tracking only for those post types. If left empty, the tracker will not load on any post type.', 'rrze-jobspreader-analytics'); ?>
        </p>
    <?php
    }

    /**
     * Render the settings page.
     * 
     * This method outputs the HTML for the settings page, including the form and fields.
     * It checks if the user has permission to manage options before rendering the page.
     * 
     * @return void
     */
    public function renderPage(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }
    ?>
        <div class="wrap">
            <h1><?php esc_html_e('Jobspreader Analytics Settings', 'rrze-jobspreader-analytics'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('rrze_jobspreader_analytics_section');
                do_settings_sections('rrze-jobspreader-analytics');
                submit_button(__('Save Changes', 'rrze-jobspreader-analytics'));
                ?>
            </form>
        </div>
<?php
    }
}
