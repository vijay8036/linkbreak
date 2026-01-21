<?php

class LinkBrack {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'LINKBRACK_VERSION' ) ) {
			$this->version = LINKBRACK_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'linkbrack';

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-linkbrack-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-linkbrack-admin.php';
		
		// Future dependencies (Scanner, Ajax, etc.)
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-linkbrack-scanner.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-linkbrack-ajax.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-linkbrack-export.php';

		$this->loader = new LinkBrack_Loader();

	}

	private function define_admin_hooks() {
		$plugin_admin = new LinkBrack_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Ajax hooks
		$plugin_ajax = new LinkBrack_Ajax();
		$this->loader->add_action( 'wp_ajax_linkbrack_start_scan', $plugin_ajax, 'start_scan' );
		$this->loader->add_action( 'wp_ajax_linkbrack_scan_batch', $plugin_ajax, 'scan_batch' );
		
		// Export hooks
		$plugin_export = new LinkBrack_Export();
		$this->loader->add_action( 'wp_ajax_linkbrack_export_csv', $plugin_export, 'export_to_csv' );
		$this->loader->add_action( 'wp_ajax_linkbrack_export_pdf', $plugin_export, 'export_to_pdf' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_version() {
		return $this->version;
	}

	public function get_loader() {
		return $this->loader;
	}
}
