<?php

class Link_Analyzer_Admin
{

	private $plugin_name;
	private $version;

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/link-analyzer-admin.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/link-analyzer-admin.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_name, 'link_analyzer_ajax', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('link_analyzer_scan_nonce')
		));
	}

	public function add_plugin_admin_menu()
	{
		add_menu_page(
			'Link Analyzer Scanner',
			'Link Analyzer',
			'manage_options',
			'link-analyzer',
			array($this, 'display_plugin_admin_page'),
			'dashicons-search',
			6
		);
	}

	public function display_plugin_admin_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/page-dashboard.php';
	}
}
