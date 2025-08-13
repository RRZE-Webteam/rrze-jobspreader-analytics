# RRZE Jobspreader Analytics

An adapted version of the Jobspreader Analytics Plugin by Wollmilchsau for WordPress, developed by the RRZE Webteam.

## Description

This WordPress plugin provides integration with Jobspreader Analytics, allowing you to track job-related analytics on your WordPress site. The plugin automatically loads the Jobspreader tracking script with your configured API key.

## Features

- Easy configuration through WordPress admin settings
- Configurable script placement (head or footer)
- Automatic script loading with cache-busting
- Multilingual support (German translations included)
- Clean and secure code following WordPress standards

## Requirements

- WordPress 6.8 or higher
- PHP 8.2 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/rrze-jobspreader-analytics` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure the plugin by going to Settings > Jobspreader Analytics

## Configuration

1. Navigate to **Settings > Jobspreader Analytics** in your WordPress admin
2. Enter your **API Key** received from Wollmilchsau
3. Choose the **Script Placement** (Head or Body/footer)
4. Enter **Tracked Post Types** — one post type slug per line where the tracker should be loaded.  
   Default: `job`.  
   If left empty, the tracker will **not** load on any page.
5. Save your changes

## Support

For support with the Jobspreader Analytics service, please contact: [support@wollmilchsau.de](mailto:support@wollmilchsau.de)

For plugin-related issues, please visit the [GitHub repository](https://github.com/RRZE-Webteam/rrze-jobspreader-analytics).

## License

This plugin is licensed under the GNU General Public License Version 3. See [LICENSE](LICENSE) for more details.

## Development

### Building Assets

The plugin uses WordPress scripts for building JavaScript assets:

```bash
# Install dependencies
npm install

# Development build with watch
npm run start

# Production build
npm run build
```

### Internationalization

To generate translation files:

```bash
wp i18n make-pot ./ languages/rrze-jobspreader-analytics.pot --domain=rrze-jobspreader-analytics --exclude=node_modules,build
```

## Credits

- Original Jobspreader Analytics Plugin by Wollmilchsau
- Adapted by RRZE Webteam
- Visit: [https://www.wp.rrze.fau.de/](https://www.wp.rrze.fau.de/)