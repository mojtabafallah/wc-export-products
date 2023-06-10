<?php
/*
Plugin Name: خروجی گرفتن از محصولات ووکامرس
Plugin URI: https://github.com/mojtabafallah
Description:  پلاگین خروجی گرفتن از محصولات ووکامرس
Version: 1.0.0
Author: Mojtaba Fallah
Author URI: https://github.com/mojtabafallah
*/


use Mojtaba\WcExportProducts\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class wcExportProduct {
	private static $instance;

	public function __construct() {
		require_once 'vendor/autoload.php';
		define( 'PATH_PLUGIN', plugin_dir_path( __FILE__ ) );
		new Admin();
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
	}
}

wcExportProduct::get_instance();