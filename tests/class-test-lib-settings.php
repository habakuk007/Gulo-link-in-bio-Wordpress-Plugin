<?php
/**
 * Tests for LIB_Settings.
 *
 * @package LinkInBio
 */

/**
 * Class Test_LIB_Settings
 *
 * @covers LIB_Settings
 */
class Test_LIB_Settings extends WP_UnitTestCase {

	/**
	 * Clean up options between tests.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		delete_option( LIB_Settings::OPTION_SETTINGS );
		delete_option( LIB_Settings::OPTION_LINKS );
		parent::tear_down();
	}

	// ── Defaults ────────────────────────────────────────────────────

	public function test_get_defaults_returns_array(): void {
		$defaults = LIB_Settings::get_defaults();
		$this->assertIsArray( $defaults );
	}

	public function test_get_defaults_has_expected_keys(): void {
		$keys = array_keys( LIB_Settings::get_defaults() );
		$this->assertContains( 'profile_name', $keys );
		$this->assertContains( 'gradient_start', $keys );
		$this->assertContains( 'button_bg_color', $keys );
	}

	// ── get() ────────────────────────────────────────────────────────

	public function test_get_falls_back_to_defaults_when_option_missing(): void {
		$name = LIB_Settings::get( 'profile_name' );
		// Default is the blog name.
		$this->assertSame( get_bloginfo( 'name' ), $name );
	}

	public function test_get_returns_saved_value(): void {
		update_option(
			LIB_Settings::OPTION_SETTINGS,
			array( 'profile_name' => 'Test Name' )
		);
		$this->assertSame( 'Test Name', LIB_Settings::get( 'profile_name' ) );
	}

	public function test_get_all_returns_array(): void {
		$settings = LIB_Settings::get();
		$this->assertIsArray( $settings );
	}

	// ── get_links() ──────────────────────────────────────────────────

	public function test_get_links_returns_empty_array_when_not_set(): void {
		$this->assertSame( array(), LIB_Settings::get_links() );
	}

	public function test_get_links_returns_parsed_array(): void {
		$links = array(
			array(
				'title'  => 'My Site',
				'url'    => 'https://example.com',
				'active' => true,
			),
		);
		update_option( LIB_Settings::OPTION_LINKS, wp_json_encode( $links ) );
		$this->assertSame( $links, LIB_Settings::get_links() );
	}

	// ── sanitize_settings() ──────────────────────────────────────────

	public function test_sanitize_strips_html_from_text_fields(): void {
		$input  = array( 'profile_name' => '<script>alert(1)</script>Hello' );
		$result = LIB_Settings::sanitize_settings( $input );
		$this->assertStringNotContainsString( '<script>', $result['profile_name'] );
	}

	public function test_sanitize_accepts_valid_hex_color(): void {
		$input  = array( 'button_bg_color' => '#ff5733' );
		$result = LIB_Settings::sanitize_settings( $input );
		$this->assertSame( '#ff5733', $result['button_bg_color'] );
	}

	public function test_sanitize_rejects_invalid_hex_color(): void {
		$input  = array( 'button_bg_color' => 'not-a-color' );
		$result = LIB_Settings::sanitize_settings( $input );
		$this->assertArrayNotHasKey( 'button_bg_color', $result );
	}

	public function test_sanitize_returns_defaults_for_non_array_input(): void {
		$result = LIB_Settings::sanitize_settings( 'invalid' );
		$this->assertSame( LIB_Settings::get_defaults(), $result );
	}

	// ── sanitize_links() ─────────────────────────────────────────────

	public function test_sanitize_links_returns_json_string(): void {
		$input  = wp_json_encode( array() );
		$result = LIB_Settings::sanitize_links( $input );
		$this->assertJson( $result );
	}

	public function test_sanitize_links_skips_empty_entries(): void {
		$input  = wp_json_encode(
			array(
				array(
					'title'  => '',
					'url'    => '',
					'active' => true,
				),
			)
		);
		$result = json_decode( LIB_Settings::sanitize_links( $input ), true );
		$this->assertCount( 0, $result );
	}

	public function test_sanitize_links_sanitizes_text_and_url(): void {
		$input  = wp_json_encode(
			array(
				array(
					'title'  => '<b>Bold</b>',
					'url'    => 'https://example.com',
					'active' => true,
				),
			)
		);
		$result = json_decode( LIB_Settings::sanitize_links( $input ), true );
		$this->assertStringNotContainsString( '<b>', $result[0]['title'] );
		$this->assertSame( 'https://example.com', $result[0]['url'] );
	}
}
