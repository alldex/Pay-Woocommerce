<?php
/*
Citadel for WooCommerce

*/

//---------------------------------------------------------------------------
// Global definitions
if (!defined('ctlWC_PLUGIN_NAME'))
  {
  define('ctlWC_VERSION',           '0.01');

  //-----------------------------------------------
  define('ctlWC_EDITION',           'Standard');

  //-----------------------------------------------
  define('ctlWC_SETTINGS_NAME',     'ctlWC-Settings');
  define('ctlWC_PLUGIN_NAME',       'Citadel for WooCommerce');


  // i18n plugin domain for language files
  define('ctlWC_I18N_DOMAIN',       'ctlwc');

  }
//---------------------------------------------------------------------------

//------------------------------------------
// Load wordpress for POSTback, WebHook and API pages that are called by external services directly.
if (defined('ctlWC_MUST_LOAD_WP') && !defined('WP_USE_THEMES') && !defined('ABSPATH'))
   {
   $g_blog_dir = preg_replace ('|(/+[^/]+){4}$|', '', str_replace ('\\', '/', __FILE__)); // For love of the art of regex-ing
   define('WP_USE_THEMES', false);
   require_once ($g_blog_dir . '/wp-blog-header.php');

   // Force-elimination of header 404 for non-wordpress pages.
   header ("HTTP/1.1 200 OK");
   header ("Status: 200 OK");

   require_once ($g_blog_dir . '/wp-admin/includes/admin.php');
   }
//------------------------------------------


// This loads necessary modules
require_once (dirname(__FILE__) . '/libs/CitadelWalletdAPI.php');

require_once (dirname(__FILE__) . '/ctlwc-cron.php');
require_once (dirname(__FILE__) . '/ctlwc-utils.php');
require_once (dirname(__FILE__) . '/ctlwc-admin.php');
require_once (dirname(__FILE__) . '/ctlwc-render-settings.php');
require_once (dirname(__FILE__) . '/ctlwc-Citadel-gateway.php');

?>
