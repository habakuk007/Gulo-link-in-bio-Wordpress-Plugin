<?php
/**
 * Unit tests for LIB_Settings.
 *
 * WordPress functions are stubbed with Brain\Monkey — no WP environment needed.
 *
 * @package LinkInBio
 */

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Class Test_LIB_Settings_Unit
 */
final class Test_LIB_Settings extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	// ── Helpers ───────────────────────────────────────────────────────────

	/**
	 * Registers Brain\Monkey aliases that mimic real WP sanitisation functions.
	 */
	private function stub_sanitizers(): void {
		Functions\when( 'absint' )->alias( function ( $val ) { return abs( (int) $val ); } );
		Functions\when( 'sanitize_text_field' )->alias( function ( $val ) { return trim( strip_tags( (string) $val ) ); } );
		Functions\when( 'sanitize_hex_color' )->alias( function ( $color ) {
			return preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color ) ? $color : null;
		} );
		Functions\when( 'esc_url_raw' )->alias( function ( $url ) {
			$url = trim( (string) $url );
			return filter_var( $url, FILTER_SANITIZE_URL ) ?: '';
		} );
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );
	}

	// ── get_defaults ──────────────────────────────────────────────────────

	public function test_get_defaults_contains_all_expected_keys(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$defaults = LIB_Settings::get_defaults();
		$expected = array(
			'page_id', 'profile_name', 'profile_bio', 'profile_image',
			'background_type', 'background_color', 'gradient_start', 'gradient_end',
			'button_style', 'button_bg_color', 'button_text_color', 'profile_text_color',
			'seo_noindex', 'imprint_url', 'privacy_url',
		);

		foreach ( $expected as $key ) {
			$this->assertArrayHasKey( $key, $defaults, "Missing key: {$key}" );
		}
	}

	public function test_get_defaults_seo_noindex_is_false(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$this->assertFalse( LIB_Settings::get_defaults()['seo_noindex'] );
	}

	public function test_get_defaults_page_id_is_zero(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$this->assertSame( 0, LIB_Settings::get_defaults()['page_id'] );
	}

	public function test_get_defaults_uses_bloginfo_for_name_and_bio(): void {
		Functions\expect( 'get_bloginfo' )
			->twice()
			->andReturnValues( array( 'My Site', 'Just another site' ) );

		$defaults = LIB_Settings::get_defaults();

		$this->assertSame( 'My Site', $defaults['profile_name'] );
		$this->assertSame( 'Just another site', $defaults['profile_bio'] );
	}

	// ── sanitize_settings — general ───────────────────────────────────────

	public function test_sanitize_settings_returns_defaults_for_non_array(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$result = LIB_Settings::sanitize_settings( 'not-an-array' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'page_id', $result );
	}

	public function test_sanitize_settings_returns_defaults_for_null(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$result = LIB_Settings::sanitize_settings( null );

		$this->assertIsArray( $result );
	}

	// ── sanitize_settings — page_id ───────────────────────────────────────

	public function test_sanitize_settings_page_id_string_cast_to_int(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'page_id' => '42' ) );

		$this->assertSame( 42, $result['page_id'] );
	}

	public function test_sanitize_settings_page_id_negative_becomes_positive(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'page_id' => '-7' ) );

		$this->assertSame( 7, $result['page_id'] );
	}

	public function test_sanitize_settings_page_id_alphanumeric_strips_letters(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'page_id' => '10abc' ) );

		$this->assertSame( 10, $result['page_id'] );
	}

	// ── sanitize_settings — seo_noindex ──────────────────────────────────

	public function test_sanitize_settings_seo_noindex_true_when_value_is_1(): void {
		$result = LIB_Settings::sanitize_settings( array( 'seo_noindex' => '1' ) );

		$this->assertTrue( $result['seo_noindex'] );
	}

	public function test_sanitize_settings_seo_noindex_false_when_absent(): void {
		$result = LIB_Settings::sanitize_settings( array() );

		$this->assertFalse( $result['seo_noindex'] );
	}

	public function test_sanitize_settings_seo_noindex_false_when_empty_string(): void {
		$result = LIB_Settings::sanitize_settings( array( 'seo_noindex' => '' ) );

		$this->assertFalse( $result['seo_noindex'] );
	}

	// ── sanitize_settings — text fields ──────────────────────────────────

	public function test_sanitize_settings_strips_html_from_profile_name(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'profile_name' => '<b>Stefan</b>' ) );

		$this->assertSame( 'Stefan', $result['profile_name'] );
	}

	public function test_sanitize_settings_strips_script_tag_from_bio(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'profile_bio' => '<script>alert(1)</script>Bio' ) );

		$this->assertSame( 'alert(1)Bio', $result['profile_bio'] );
	}

	public function test_sanitize_settings_trims_whitespace_from_text_fields(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'profile_name' => '  Stefan  ' ) );

		$this->assertSame( 'Stefan', $result['profile_name'] );
	}

	// ── sanitize_settings — color fields ─────────────────────────────────

	public function test_sanitize_settings_accepts_valid_six_digit_hex(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'button_bg_color' => '#1a2b3c' ) );

		$this->assertSame( '#1a2b3c', $result['button_bg_color'] );
	}

	public function test_sanitize_settings_accepts_valid_three_digit_hex(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'gradient_start' => '#fff' ) );

		$this->assertSame( '#fff', $result['gradient_start'] );
	}

	public function test_sanitize_settings_rejects_invalid_color_string(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'button_bg_color' => 'red' ) );

		$this->assertArrayNotHasKey( 'button_bg_color', $result );
	}

	public function test_sanitize_settings_rejects_color_without_hash(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'button_bg_color' => 'ffffff' ) );

		$this->assertArrayNotHasKey( 'button_bg_color', $result );
	}

	// ── sanitize_settings — URL fields ───────────────────────────────────

	public function test_sanitize_settings_accepts_valid_profile_image_url(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'profile_image' => 'https://example.com/avatar.jpg' ) );

		$this->assertSame( 'https://example.com/avatar.jpg', $result['profile_image'] );
	}

	public function test_sanitize_settings_trims_whitespace_from_url(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'imprint_url' => '  https://example.com/imprint  ' ) );

		$this->assertSame( 'https://example.com/imprint', $result['imprint_url'] );
	}

	public function test_sanitize_settings_accepts_valid_privacy_url(): void {
		$this->stub_sanitizers();

		$result = LIB_Settings::sanitize_settings( array( 'privacy_url' => 'https://example.com/privacy' ) );

		$this->assertSame( 'https://example.com/privacy', $result['privacy_url'] );
	}

	// ── sanitize_links ────────────────────────────────────────────────────

	public function test_sanitize_links_returns_empty_json_for_invalid_json(): void {
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );

		$result = LIB_Settings::sanitize_links( 'not-valid-json' );

		$this->assertSame( '[]', $result );
	}

	public function test_sanitize_links_returns_empty_json_for_null(): void {
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );

		$result = LIB_Settings::sanitize_links( null );

		$this->assertSame( '[]', $result );
	}

	public function test_sanitize_links_skips_entry_with_empty_title_and_url(): void {
		$this->stub_sanitizers();

		$input  = json_encode( array( array( 'title' => '', 'url' => '', 'active' => true ) ) );
		$result = LIB_Settings::sanitize_links( $input );

		$this->assertSame( '[]', $result );
	}

	public function test_sanitize_links_skips_non_array_entries(): void {
		$this->stub_sanitizers();

		$input   = json_encode( array( 'not-an-array', array( 'title' => 'GitHub', 'url' => 'https://github.com' ) ) );
		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertCount( 1, $decoded );
	}

	public function test_sanitize_links_preserves_valid_link(): void {
		$this->stub_sanitizers();

		$input   = json_encode( array( array( 'title' => 'GitHub', 'url' => 'https://github.com', 'active' => true ) ) );
		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertCount( 1, $decoded );
		$this->assertSame( 'GitHub', $decoded[0]['title'] );
		$this->assertSame( 'https://github.com', $decoded[0]['url'] );
		$this->assertTrue( $decoded[0]['active'] );
	}

	public function test_sanitize_links_active_defaults_to_true_when_absent(): void {
		$this->stub_sanitizers();

		$input   = json_encode( array( array( 'title' => 'Link', 'url' => 'https://example.com' ) ) );
		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertTrue( $decoded[0]['active'] );
	}

	public function test_sanitize_links_preserves_active_false(): void {
		$this->stub_sanitizers();

		$input   = json_encode( array( array( 'title' => 'Link', 'url' => 'https://example.com', 'active' => false ) ) );
		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertFalse( $decoded[0]['active'] );
	}

	public function test_sanitize_links_strips_html_from_title(): void {
		$this->stub_sanitizers();

		$input   = json_encode( array( array( 'title' => '<b>GitHub</b>', 'url' => 'https://github.com', 'active' => true ) ) );
		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertSame( 'GitHub', $decoded[0]['title'] );
	}

	public function test_sanitize_links_preserves_multiple_valid_links(): void {
		$this->stub_sanitizers();

		$input = json_encode( array(
			array( 'title' => 'GitHub',   'url' => 'https://github.com',  'active' => true ),
			array( 'title' => 'LinkedIn', 'url' => 'https://linkedin.com', 'active' => false ),
		) );

		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertCount( 2, $decoded );
		$this->assertSame( 'GitHub', $decoded[0]['title'] );
		$this->assertSame( 'LinkedIn', $decoded[1]['title'] );
		$this->assertFalse( $decoded[1]['active'] );
	}

	public function test_sanitize_links_entry_with_title_only_is_kept(): void {
		$this->stub_sanitizers();

		$input   = json_encode( array( array( 'title' => 'Label only', 'url' => '' ) ) );
		$result  = LIB_Settings::sanitize_links( $input );
		$decoded = json_decode( $result, true );

		$this->assertCount( 1, $decoded );
	}
}
