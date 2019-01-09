<?php

include_once 'SlickPerformance_LifeCycle.php';

class SlickPerformance_Plugin extends SlickPerformance_LifeCycle
{
/**
 * See: http://plugin.michael-simpson.com/?page_id=31
 * @return array of option meta data.
 */
    public function getOptionMetaData()
    {
//  http://plugin.michael-simpson.com/?page_id=31
        return array(
            'SiteCode' => array(__('Site Code', 'slick-performance')),
            'WhichPages' => array(__('Apply to pages', 'slick-performance'), 'All', 'Only those tagged with "slick-perf"'),
            'ScriptUrl' => array(__('Script URL (optional)', 'slick-performance')),
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
    //        $i18nValue = parent::getOptionValueI18nString($optionValue);
    //        return $i18nValue;
    //    }

    protected function initOptions()
    {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr) > 1) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName()
    {
        return 'Slick Performance';
    }

    protected function getMainPluginFileName()
    {
        return 'slick-performance.php';
    }

/**
 * See: http://plugin.michael-simpson.com/?page_id=101
 * Called by install() to create any database tables if needed.
 * Best Practice:
 * (1) Prefix all table names with $wpdb->prefix
 * (2) make table names lower case only
 * @return void
 */
    protected function installDatabaseTables()
    {
//        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

/**
 * See: http://plugin.michael-simpson.com/?page_id=101
 * Drop plugin-created tables on uninstall.
 * @return void
 */
    protected function unInstallDatabaseTables()
    {
//        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }

/**
 * Perform actions when upgrading from version X to version Y
 * See: http://plugin.michael-simpson.com/?page_id=35
 * @return void
 */
    public function upgrade()
    {
    }

    public function addActionsAndFilters()
    {
// Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

// Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }

// Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37

        add_action('get_header', array(&$this, 'onGetHeader'));
        add_action('wp_head', array(&$this, 'onWpHead', 100));

// Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));

// Register short codes
        // http://plugin.michael-simpson.com/?page_id=39

// Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

        $prefix = is_network_admin() ? 'network_admin_' : '';
        $plugin_file = plugin_basename($this->getPluginDir() . DIRECTORY_SEPARATOR . $this->getMainPluginFileName()); //plugin_basename( $this->getMainPluginFileName() );
        $this->guildLog('Adding filter ' . "{$prefix}plugin_action_links_{$plugin_file}");
        add_filter("{$prefix}plugin_action_links_{$plugin_file}", array(&$this, 'onActionLinks'));
    }

    public function onActionLinks($links)
    {
        $this->guildLog('onActionLinks ' . admin_url('options-general.php?page=SlickPerformance_PluginSettings'));
        $mylinks = array('<a href="' . admin_url('options-general.php?page=SlickPerformance_PluginSettings') . '">Settings</a>');
        return array_merge($links, $mylinks);
    }

    public function onGetHeader()
    {
        ob_start(array(&$this, 'obStartCallback'));
    }

    public function onWpHead()
    {
        ob_end_flush();
    }

    public function obStartCallback($buffer)
    {
        $siteCode = $this->getOption('SiteCode');
        if ($siteCode) {
            $whichPages = $this->getOption('WhichPages', 'All');
            if ($whichPages == 'All' || has_tag('slick-perf')) {
                $serverUrl = $this->getOption('ScriptUrl', 'https://poweredbyslick.com/e2/slick-perf.js');
                $serverUrl = $serverUrl . '?site=' . $siteCode;
                $buffer = preg_replace('/\<head\s*\>/i', '<head>' . "\n" . '<script src="' . $serverUrl . '"></script>' . "\n", $buffer);
            }
        }
        return $buffer;
    }
}
