<?php
/**
 * @package tiktok-pixel-for-elementor
 */
namespace WPL\Tiktok_Pixel_Tracker_For_Elementor;

use Elementor\Settings;

class Options {
	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		add_action( 'elementor/admin/after_create_settings/elementor', [ $this, 'register_settings' ] );
	}

	/**
	 * Create Setting Tab
	 *
	 * @param Settings $settings Elementor "Settings" page in WordPress Dashboard.
	 *
	 * @since 1.3
	 *
	 * @access public
	 */  	
	public function register_settings( Settings $settings ) {
		$settings->add_tab(
			WPL_ELEMENTOR_EVENTS_TRACKER_SLUG,
			[
				'label'    => __( 'GH Tiktok Pixel', 'tiktok-pixel-for-elementor' ),
				'sections' => [
					
					WPL_ELEMENTOR_EVENTS_TRACKER_SLUG . '_tiktok' => [
						'label'  => __( 'Tiktok Pixel', 'tiktok-pixel-for-elementor' ),
							'callback' => function() {
							echo '<div class="notice" style="position:relative!important;"><h3>Tertarik belajar SEO?</h3><p >belajar seo dan growthhack bisa <a href="https://growthhackerid.com/">klik di sini</a></p></div>';
						},
						'fields' => [
							WPL_ELEMENTOR_EVENTS_TRACKER_SLUG . '_tiktok_pixel_id' => [
								'label'      => __( 'TikTok Pixel ID', 'tiktok-pixel-for-elementor' ),
								'field_args' => [
									'type' => 'text',
								],
							],
						],
					],
				],
			]
		);
	}
}

// eol.
