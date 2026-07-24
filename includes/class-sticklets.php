<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sticklets {

	private $admin;
	private $public;

	public function __construct() {
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once STICKLETS_PLUGIN_DIR . 'includes/class-post-type.php';
    require_once STICKLETS_PLUGIN_DIR . 'includes/class-meta-boxes.php';
    require_once STICKLETS_PLUGIN_DIR . 'admin/class-admin.php';
    require_once STICKLETS_PLUGIN_DIR . 'public/class-public.php';

		$this->admin  = new Sticklets_Admin();
		$this->public = new Sticklets_Public();
	}

	public function run() {
		$this->admin->init();
		$this->public->init();
	}
}