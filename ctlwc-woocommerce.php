<?php
/*



*/


// Include everything
include (dirname(__FILE__) . '/ctlwc-include-all.php');

//---------------------------------------------------------------------------
// Add hooks and filters

// create custom plugin settings menu
add_action( 'admin_menu',                   'ctlWC_create_menu' );

register_activation_hook(__FILE__,          'ctlWC_activate');
register_deactivation_hook(__FILE__,        'ctlWC_deactivate');
register_uninstall_hook(__FILE__,           'ctlWC_uninstall');

add_filter ('cron_schedules',               'ctlWC__add_custom_scheduled_intervals');
add_action ('ctlWC_cron_action',             'ctlWC_cron_job_worker');     // Multiple functions can be attached to 'ctlWC_cron_action' action

ctlWC_set_lang_file();
//---------------------------------------------------------------------------

//===========================================================================
// activating the default values
function ctlWC_activate()
{
    global  $g_ctlWC__config_defaults;

    $ctlwc_default_options = $g_ctlWC__config_defaults;

    // This will overwrite default options with already existing options but leave new options (in case of upgrading to new version) untouched.
    $ctlwc_settings = ctlWC__get_settings ();

    foreach ($ctlwc_settings as $key=>$value)
    	$ctlwc_default_options[$key] = $value;

    update_option (ctlWC_SETTINGS_NAME, $ctlwc_default_options);

    // Re-get new settings.
    $ctlwc_settings = ctlWC__get_settings ();

    // Create necessary database tables if not already exists...
    ctlWC__create_database_tables ($ctlwc_settings);
    ctlWC__SubIns ();

    //----------------------------------
    // Setup cron jobs

    if ($ctlwc_settings['enable_soft_cron_job'] && !wp_next_scheduled('ctlWC_cron_action'))
    {
    	$cron_job_schedule_name = $ctlwc_settings['soft_cron_job_schedule_name'];
    	wp_schedule_event(time(), $cron_job_schedule_name, 'ctlWC_cron_action');
    }
    //----------------------------------

}
//---------------------------------------------------------------------------
// Cron Subfunctions
function ctlWC__add_custom_scheduled_intervals ($schedules)
{
	$schedules['seconds_30']     = array('interval'=>30,     'display'=>__('Once every 30 seconds'));
	$schedules['minutes_1']      = array('interval'=>1*60,   'display'=>__('Once every 1 minute'));
	$schedules['minutes_2.5']    = array('interval'=>2.5*60, 'display'=>__('Once every 2.5 minutes'));
	$schedules['minutes_5']      = array('interval'=>5*60,   'display'=>__('Once every 5 minutes'));

	return $schedules;
}
//---------------------------------------------------------------------------
//===========================================================================

//===========================================================================
// deactivating
function ctlWC_deactivate ()
{
    // Do deactivation cleanup. Do not delete previous settings in case user will reactivate plugin again...

    //----------------------------------
    // Clear cron jobs
    wp_clear_scheduled_hook ('ctlWC_cron_action');
    //----------------------------------
}
//===========================================================================

//===========================================================================
// uninstalling
function ctlWC_uninstall ()
{
    $ctlwc_settings = ctlWC__get_settings();

    if ($ctlwc_settings['delete_db_tables_on_uninstall'])
    {
        // delete all settings.
        delete_option(ctlWC_SETTINGS_NAME);

        // delete all DB tables and data.
        ctlWC__delete_database_tables ();
    }
}
//===========================================================================

//===========================================================================
function ctlWC_create_menu()
{

    // create new top-level menu
    // http://www.fileformat.info/info/unicode/char/e3f/index.htm
    add_menu_page (
        __('Woo Citadel', ctlWC_I18N_DOMAIN),                    // Page title
        __('Citadel', ctlWC_I18N_DOMAIN),                        // Menu Title - lower corner of admin menu
        'administrator',                                        // Capability
        'ctlwc-settings',                                        // Handle - First submenu's handle must be equal to parent's handle to avoid duplicate menu entry.
        'ctlWC__render_general_settings_page',                   // Function
        plugins_url('/images/Citadel_16x.png', __FILE__)      // Icon URL
        );

    add_submenu_page (
        'ctlwc-settings',                                        // Parent
        __("WooCommerce Citadel Gateway", ctlWC_I18N_DOMAIN),                   // Page title
        __("General Settings", ctlWC_I18N_DOMAIN),               // Menu Title
        'administrator',                                        // Capability
        'ctlwc-settings',                                        // Handle - First submenu's handle must be equal to parent's handle to avoid duplicate menu entry.
        'ctlWC__render_general_settings_page'                    // Function
        );

}
//===========================================================================

//===========================================================================
// load language files
function ctlWC_set_lang_file()
{
    # set the language file
    $currentLocale = get_locale();
    if(!empty($currentLocale))
    {
        $moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
        if (@file_exists($moFile) && is_readable($moFile))
        {
            load_textdomain(ctlWC_I18N_DOMAIN, $moFile);
        }

    }
}
//===========================================================================
