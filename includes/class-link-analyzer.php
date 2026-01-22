<?php

class Link_Analyzer {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'LINK_ANALYZER_VERSION' ) ) {
			$this->version = LINK_ANALYZER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'link-analyzer';

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-link-analyzer-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-link-analyzer-admin.php';
		
		// Future dependencies (Scanner, Ajax, etc.)
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-link-analyzer-scanner.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-link-analyzer-ajax.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-link-analyzer-export.php';

		$this->loader = new Link_Analyzer_Loader();

	}

	private function define_admin_hooks() {
		$plugin_admin = new Link_Analyzer_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Ajax hooks
		$plugin_ajax = new Link_Analyzer_Ajax();
		$this->loader->add_action( 'wp_ajax_link_analyzer_start_scan', $plugin_ajax, 'start_scan' );
		$this->loader->add_action( 'wp_ajax_link_analyzer_scan_batch', $plugin_ajax, 'scan_batch' );
		
		// Export hooks
		$plugin_export = new Link_Analyzer_Export();
		$this->loader->add_action( 'wp_ajax_link_analyzer_export_csv', $plugin_export, 'export_to_csv' );
		$this->loader->add_action( 'wp_ajax_link_analyzer_export_pdf', $plugin_export, 'export_to_pdf' );
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
