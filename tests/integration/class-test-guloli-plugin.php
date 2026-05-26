<?php
/**
 * Integration tests — requires a real WordPress environment.
 *
 * Run via CI (bin/install-wp-tests.sh) or locally with wp-env + Docker.
 *
 * @package GuloLinkInBio
 */

/**
 * Test_GULOLI_Plugin_Integration class
 */
final class Test_GULOLI_Plugin_Integration extends WP_UnitTestCase {

	/**
	 * Tests that GULOLI_Settings class is loaded.
	 */
	public function test_guloli_settings_class_is_loaded(): void {
		$this->assertTrue( class_exists( 'GULOLI_Settings' ) );
	}

	/**
	 * Tests that GULOLI_Plugin class is loaded.
	 */
	public function test_guloli_plugin_class_is_loaded(): void {
		$this->assertTrue( class_exists( 'GULOLI_Plugin' ) );
	}

	/**
	 * Tests that get_defaults returns the expected structure.
	 */
	public function test_get_defaults_returns_expected_structure(): void {
		$defaults = GULOLI_Settings::get_defaults();

		$this->assertIsArray( $defaults );
		$this->assertSame( 0, $defaults['page_id'] );
		$this->assertFalse( $defaults['seo_noindex'] );
		$this->assertSame( 'gradient', $defaults['background_type'] );
		$this->assertSame( 'filled', $defaults['button_style'] );
	}

	/**
	 * Tests sanitize_settings with real WP functions.
	 */
	public function test_sanitize_settings_with_real_wp_functions(): void {
		$input = array(
			'page_id'         => '10',
			'profile_name'    => '<b>Test Name</b>',
			'seo_noindex'     => '1',
			'button_bg_color' => '#ff0000',
		);

		$result = GULOLI_Settings::sanitize_settings( $input );

		$this->assertSame( 10, $result['page_id'] );
		$this->assertSame( 'Test Name', $result['profile_name'] );
		$this->assertTrue( $result['seo_noindex'] );
		$this->assertSame( '#ff0000', $result['button_bg_color'] );
	}

	/**
	 * Tests that sanitize_settings rejects an invalid hex color.
	 */
	public function test_sanitize_settings_rejects_invalid_hex_color(): void {
		$result = GULOLI_Settings::sanitize_settings( array( 'button_bg_color' => 'not-a-color' ) );

		$this->assertArrayNotHasKey( 'button_bg_color', $result );
	}

	/**
	 * Tests that seo_noindex is false when absent from input.
	 */
	public function test_sanitize_settings_seo_noindex_false_when_absent(): void {
		$result = GULOLI_Settings::sanitize_settings( array( 'profile_name' => 'Test' ) );

		$this->assertFalse( $result['seo_noindex'] );
	}

	/**
	 * Tests sanitize_links with real WP functions.
	 */
	public function test_sanitize_links_with_real_wp_functions(): void {
		$input = wp_json_encode(
			array(
				array(
					'title'  => 'GitHub',
					'url'    => 'https://github.com',
					'active' => true,
				),
				array(
					'title'  => '',
					'url'    => '',
					'active' => false,
				),
			)
		);

		$result  = GULOLI_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertCount( 1, $decoded );
		$this->assertSame( 'GitHub', $decoded[0]['title'] );
		$this->assertSame( 'https://github.com', $decoded[0]['url'] );
		$this->assertTrue( $decoded[0]['active'] );
	}

	/**
	 * Tests that sanitize_links returns an empty array for invalid JSON.
	 */
	public function test_sanitize_links_returns_empty_for_invalid_json(): void {
		$result  = GULOLI_Settings::sanitize_links( 'not-valid-json' );
		$decoded = json_decode( $result, true );

		$this->assertSame( array(), $decoded );
	}
}
