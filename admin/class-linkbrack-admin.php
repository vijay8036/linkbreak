<?php

class LinkBrack_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/linkbrack-admin.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/linkbrack-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'linkbrack_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'linkbrack_scan_nonce' )
		));
	}

	public function add_plugin_admin_menu() {
		add_menu_page(
			'LinkBrack Scanner', 
			'LinkBrack', 
			'manage_options', 
			'linkbrack', 
			array( $this, 'display_plugin_admin_page' ), 
			'dashicons-search', 
			6
		);
	}

	public function display_plugin_admin_page() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/page-dashboard.php';
	}
}
