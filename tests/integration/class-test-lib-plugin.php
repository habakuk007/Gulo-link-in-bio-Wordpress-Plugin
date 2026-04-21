<?php
/**
 * Integration tests — requires a real WordPress environment.
 *
 * Run via CI (bin/install-wp-tests.sh) or locally with wp-env + Docker.
 *
 * @package LinkInBio
 */

/**
 * Class Test_LIB_Plugin_Integration
 */
final class Test_LIB_Plugin_Integration extends WP_UnitTestCase {

	public function test_lib_settings_class_is_loaded(): void {
		$this->assertTrue( class_exists( 'LIB_Settings' ) );
	}

	public function test_lib_plugin_class_is_loaded(): void {
		$this->assertTrue( class_exists( 'LIB_Plugin' ) );
	}

	public function test_get_defaults_returns_expected_structure(): void {
		$defaults = LIB_Settings::get_defaults();

		$this->assertIsArray( $defaults );
		$this->assertSame( 0, $defaults['page_id'] );
		$this->assertFalse( $defaults['seo_noindex'] );
		$this->assertSame( 'gradient', $defaults['background_type'] );
		$this->assertSame( 'filled', $defaults['button_style'] );
	}

	public function test_sanitize_settings_with_real_wp_functions(): void {
		$input = array(
			'page_id'        => '10',
			'profile_name'   => '<b>Test Name</b>',
			'seo_noindex'    => '1',
			'button_bg_color' => '#ff0000',
		);

		$result = LIB_Settings::sanitize_settings( $input );

		$this->assertSame( 10, $result['page_id'] );
		$this->assertSame( 'Test Name', $result['profile_name'] );
		$this->assertTrue( $result['seo_noindex'] );
		$this->assertSame( '#ff0000', $result['button_bg_color'] );
	}

	public function test_sanitize_settings_rejects_invalid_hex_color(): void {
		$result = LIB_Settings::sanitize_settings( array( 'button_bg_color' => 'not-a-color' ) );

		$this->assertArrayNotHasKey( 'button_bg_color', $result );
	}

	public function test_sanitize_settings_seo_noindex_false_when_absent(): void {
		$result = LIB_Settings::sanitize_settings( array( 'profile_name' => 'Test' ) );

		$this->assertFalse( $result['seo_noindex'] );
	}

	public function test_sanitize_links_with_real_wp_functions(): void {
		$input = wp_json_encode(
			array(
				array( 'title' => 'GitHub',  'url' => 'https://github.com', 'active' => true ),
				array( 'title' => '',        'url' => '',                   'active' => false ),
			)
		);

		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertCount( 1, $decoded );
		$this->assertSame( 'GitHub', $decoded[0]['title'] );
		$this->assertSame( 'https://github.com', $decoded[0]['url'] );
		$this->assertTrue( $decoded[0]['active'] );
	}

	public function test_sanitize_links_returns_empty_for_invalid_json(): void {
		$result  = LIB_Settings::sanitize_links( 'not-valid-json' );
		$decoded = json_decode( $result, true );

		$this->assertSame( array(), $decoded );
	}
}
