<?php

 $databases = array();

$config_directories = array();

$update_free_access = FALSE;
$drupal_hash_salt = '';

$databases['default']['default'] = array (
  'database' => $_SERVER['RDS_DB_NAME'],
  'username' => $_SERVER['RDS_USERNAME'],
  'password' => $_SERVER['RDS_PASSWORD'],
  'prefix' => '',
  'host' => $_SERVER['RDS_HOSTNAME'],
  'port' => $_SERVER['RDS_PORT'],
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

/**
 * Some distributions of Linux (most notably Debian) ship their PHP
 * installations with garbage collection (gc) disabled. Since Drupal depends on
 * PHP's garbage collection for clearing sessions, ensure that garbage
 * collection occurs by using the most common settings.
 */
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

/**
 * Set session lifetime (in seconds), i.e. the time from the user's last visit
 * to the active session may be deleted by the session garbage collector. When
 * a session is deleted, authenticated users are logged out, and the contents
 * of the user's $_SESSION variable is discarded.
 */
ini_set('session.gc_maxlifetime', 200000);

/**
 * Set session cookie lifetime (in seconds), i.e. the time from the session is
 * created to the cookie expires, i.e. when the browser is expected to discard
 * the cookie. The value 0 means "until the browser is closed".
 */
ini_set('session.cookie_lifetime', 2000000);

$conf['404_fast_paths_exclude'] = '/\/(?:styles)|(?:system\/files)\//';
$conf['404_fast_paths'] = '/\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';
$conf['404_fast_html'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';

/**
 * For Memcached configuration
 */
$conf['cache_backends'][] = 'sites/all/modules/memcache/memcache.inc';
$conf['lock_inc'] = 'sites/all/modules/memcache/memcache-lock.inc';
$conf['memcache_stampede_protection'] = TRUE;
$conf['cache_default_class'] = 'MemCacheDrupal';
// The 'cache_form' bin must be assigned to non-volatile storage.
$conf['cache_class_cache_form'] = 'DrupalDatabaseCache';
// Don't bootstrap the database when serving pages from the cache.
$conf['page_cache_without_database'] = TRUE;
$conf['page_cache_invoke_hooks'] = FALSE;
// If this server has multiple Drupal installation
// assign unique key for memcache namespace purposes
$conf['memcache_key_prefix'] = 'Lacity_sites';
$conf['memcache_bins'] = array('cache' => 'default');
$conf['memcache_options'] = array(
  Memcached::OPT_COMPRESSION => TRUE,
  Memcached::OPT_BINARY_PROTOCOL => TRUE,
  );
$conf['memcache_persistent'] = TRUE;

// reverse proxy support to make sure the real ip gets logged by Drupal
// https://www.karelbemelmans.com/2015/04/reverse-proxy-configuration-for-drupal-7-sites/
$conf['reverse_proxy'] = TRUE;
$elbAddresses = array_map('gethostbyname', array_map('gethostbyaddr', gethostbynamel($_SERVER['HTTP_HOST'])));
$conf['reverse_proxy_addresses'] = array('127.0.0.1');
$conf['reverse_proxy_addresses'] = array_merge($conf['reverse_proxy_addresses'], $elbAddresses);
$conf['reverse_proxy_header'] = 'HTTP_X_FORWARDED_FOR'; //work

/**
 * Varnishd Configuration
 *
 * This cache implementation can be used together with Varnish. You can't really use it to store or get any values, but you can use it to purge your caches.
 * This cache implementation should ONLY be used for cache_page and no other cache bin!
 */
// Add Varnish as the page cache handler.
$conf['cache_backends'][] = 'sites/all/modules/varnish/varnish.cache.inc';
//If you plan to use the expire module to be selective with your cache clearing you
//should add as a new cache bin.
$conf['cache_class_external_varnish_page'] = 'VarnishCache';
// Drupal 7 does not cache pages when we invoke hooks during bootstrap. This needs to be disabled.
$conf['page_cache_invoke_hooks'] = FALSE;
$conf['cache_class_cache_page'] = 'VarnishCache';


/*
$config_directories['sync'] = $_SERVER['SYNC_DIR'];
$settings['hash_salt'] = $_SERVER['HASH_SALT'];
$settings['container_yamls'][] = __DIR__ . '/services.yml';
$settings['install_profile'] = 'standard';
$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];
*/
