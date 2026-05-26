<?php
/**
 * Unit tests for GULOLI_Settings.
 *
 * WordPress functions are stubbed with Brain\Monkey — no WP environment needed.
 *
 * @package GuloLinkInBio
 */

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Test_GULOLI_Settings_Unit class
 */
final class Test_GULOLI_Settings extends TestCase {

	/**
	 * Sets up Brain\Monkey before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tears down Brain\Monkey after each test.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	// ── Helpers ───────────────────────────────────────────────────────────

	/**
	 * Registers Brain\Monkey aliases that mimic real WP sanitisation functions.
	 */
	private function stub_sanitizers(): void {
		Functions\when( 'absint' )->alias(
			function ( $val ) {
				return abs( (int) $val );
			}
		);
		Functions\when( 'sanitize_text_field' )->alias(
			function ( $val ) {
				return trim( wp_strip_all_tags( (string) $val ) );
			}
		);
		Functions\when( 'sanitize_hex_color' )->alias(
			function ( $color ) {
				return preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', $color ) ? $color : null;
			}
		);
		Functions\when( 'esc_url_raw' )->alias(
			function ( $url ) {
				$url      = trim( (string) $url );
				$filtered = filter_var( $url, FILTER_SANITIZE_URL );
				return false !== $filtered ? $filtered : '';
			}
		);
		Functions\when( 'wp_strip_all_tags' )->alias( 'strip_tags' );
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );
	}

	// ── get_defaults ──────────────────────────────────────────────────────

	/**
	 * Tests that get_defaults returns all expected keys.
	 */
	public function test_get_defaults_contains_all_expected_keys(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$defaults = GULOLI_Settings::get_defaults();
		$expected = array(
			'page_id',
			'profile_name',
			'profile_bio',
			'profile_image',
			'background_type',
			'background_color',
			'gradient_start',
			'gradient_end',
			'button_style',
			'button_bg_color',
			'button_text_color',
			'profile_text_color',
			'seo_noindex',
			'imprint_url',
			'privacy_url',
		);

		foreach ( $expected as $key ) {
			$this->assertArrayHasKey( $key, $defaults, "Missing key: {$key}" );
		}
	}

	/**
	 * Tests that seo_noindex defaults to false.
	 */
	public function test_get_defaults_seo_noindex_is_false(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$this->assertFalse( GULOLI_Settings::get_defaults()['seo_noindex'] );
	}

	/**
	 * Tests that page_id defaults to zero.
	 */
	public function test_get_defaults_page_id_is_zero(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$this->assertSame( 0, GULOLI_Settings::get_defaults()['page_id'] );
	}

	/**
	 * Tests that get_defaults uses get_bloginfo for profile name and bio.
	 */
	public function test_get_defaults_uses_bloginfo_for_name_and_bio(): void {
		Functions\expect( 'get_bloginfo' )
			->twice()
			->andReturnValues( array( 'My Site', 'Just another site' ) );

		$defaults = GULOLI_Settings::get_defaults();

		$this->assertSame( 'My Site', $defaults['profile_name'] );
		$this->assertSame( 'Just another site', $defaults['profile_bio'] );
	}

	// ── sanitize_settings — general ───────────────────────────────────────
	/**
	 * Tests that sanitize_settings returns defaults for non-array input.
	 */ public function test_sanitize_settings_returns_defaults_for_non_array(): void {
		Functions\when( 'get_bloginfo' )->justReturn( '' );

		$result = GULOLI_Settings::sanitize_settings( 'not-an-array' );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'page_id', $result );
}

	/**
	 * Tests that sanitize_settings returns defaults for null input.
	 */
public function test_sanitize_settings_returns_defaults_for_null(): void {
	Functions\when( 'get_bloginfo' )->justReturn( '' );

	$result = GULOLI_Settings::sanitize_settings( null );

	$this->assertIsArray( $result );
}

	// ── sanitize_settings — page_id ───────────────────────────────────────
	/**
	 * Tests that page_id string is cast to int.
	 */ public function test_sanitize_settings_page_id_string_cast_to_int(): void {
		$this->stub_sanitizers();

		$result = GULOLI_Settings::sanitize_settings( array( 'page_id' => '42' ) );

		$this->assertSame( 42, $result['page_id'] );
}

	/**
	 * Tests that a negative page_id becomes positive.
	 */
public function test_sanitize_settings_page_id_negative_becomes_positive(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'page_id' => '-7' ) );

	$this->assertSame( 7, $result['page_id'] );
}

	/**
	 * Tests that letters are stripped from an alphanumeric page_id.
	 */
public function test_sanitize_settings_page_id_alphanumeric_strips_letters(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'page_id' => '10abc' ) );

	$this->assertSame( 10, $result['page_id'] );
}

	// ── sanitize_settings — seo_noindex ──────────────────────────────────
	/**
	 * Tests that seo_noindex is true when value is 1.
	 */ public function test_sanitize_settings_seo_noindex_true_when_value_is_1(): void {
		$result = GULOLI_Settings::sanitize_settings( array( 'seo_noindex' => '1' ) );

		$this->assertTrue( $result['seo_noindex'] );
}

	/**
	 * Tests that seo_noindex is false when absent.
	 */
public function test_sanitize_settings_seo_noindex_false_when_absent(): void {
	$result = GULOLI_Settings::sanitize_settings( array() );

	$this->assertFalse( $result['seo_noindex'] );
}

	/**
	 * Tests that seo_noindex is false when given an empty string.
	 */
public function test_sanitize_settings_seo_noindex_false_when_empty_string(): void {
	$result = GULOLI_Settings::sanitize_settings( array( 'seo_noindex' => '' ) );

	$this->assertFalse( $result['seo_noindex'] );
}

	// ── sanitize_settings — text fields ──────────────────────────────────
	/**
	 * Tests that HTML is stripped from profile_name.
	 */ public function test_sanitize_settings_strips_html_from_profile_name(): void {
		$this->stub_sanitizers();

		$result = GULOLI_Settings::sanitize_settings( array( 'profile_name' => '<b>Stefan</b>' ) );

		$this->assertSame( 'Stefan', $result['profile_name'] );
}

	/**
	 * Tests that a script tag is stripped from profile_bio.
	 */
public function test_sanitize_settings_strips_script_tag_from_bio(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'profile_bio' => '<script>alert(1)</script>Bio' ) );

	$this->assertSame( 'alert(1)Bio', $result['profile_bio'] );
}

	/**
	 * Tests that whitespace is trimmed from text fields.
	 */
public function test_sanitize_settings_trims_whitespace_from_text_fields(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'profile_name' => '  Stefan  ' ) );

	$this->assertSame( 'Stefan', $result['profile_name'] );
}

	// ── sanitize_settings — color fields ─────────────────────────────────
	/**
	 * Tests that a valid six-digit hex color is accepted.
	 */ public function test_sanitize_settings_accepts_valid_six_digit_hex(): void {
		$this->stub_sanitizers();

		$result = GULOLI_Settings::sanitize_settings( array( 'button_bg_color' => '#1a2b3c' ) );

		$this->assertSame( '#1a2b3c', $result['button_bg_color'] );
}

	/**
	 * Tests that a valid three-digit hex color is accepted.
	 */
public function test_sanitize_settings_accepts_valid_three_digit_hex(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'gradient_start' => '#fff' ) );

	$this->assertSame( '#fff', $result['gradient_start'] );
}

	/**
	 * Tests that an invalid color string is rejected.
	 */
public function test_sanitize_settings_rejects_invalid_color_string(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'button_bg_color' => 'red' ) );

	$this->assertArrayNotHasKey( 'button_bg_color', $result );
}

	/**
	 * Tests that a hex color without a hash prefix is rejected.
	 */
public function test_sanitize_settings_rejects_color_without_hash(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'button_bg_color' => 'ffffff' ) );

	$this->assertArrayNotHasKey( 'button_bg_color', $result );
}

	// ── sanitize_settings — URL fields ───────────────────────────────────
	/**
	 * Tests that a valid profile image URL is accepted.
	 */ public function test_sanitize_settings_accepts_valid_profile_image_url(): void {
		$this->stub_sanitizers();

		$result = GULOLI_Settings::sanitize_settings( array( 'profile_image' => 'https://example.com/avatar.jpg' ) );

		$this->assertSame( 'https://example.com/avatar.jpg', $result['profile_image'] );
}

	/**
	 * Tests that whitespace is trimmed from URL fields.
	 */
public function test_sanitize_settings_trims_whitespace_from_url(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'imprint_url' => '  https://example.com/imprint  ' ) );

	$this->assertSame( 'https://example.com/imprint', $result['imprint_url'] );
}

	/**
	 * Tests that a valid privacy URL is accepted.
	 */
public function test_sanitize_settings_accepts_valid_privacy_url(): void {
	$this->stub_sanitizers();

	$result = GULOLI_Settings::sanitize_settings( array( 'privacy_url' => 'https://example.com/privacy' ) );

	$this->assertSame( 'https://example.com/privacy', $result['privacy_url'] );
}

	// ── sanitize_links ────────────────────────────────────────────────────
	/**
	 * Tests that sanitize_links returns empty JSON for invalid JSON input.
	 */ public function test_sanitize_links_returns_empty_json_for_invalid_json(): void {
		Functions\when( 'wp_json_encode' )->alias( 'json_encode' );

		$result = GULOLI_Settings::sanitize_links( 'not-valid-json' );

		$this->assertSame( '[]', $result );
}

	/**
	 * Tests that sanitize_links returns empty JSON for null input.
	 */
public function test_sanitize_links_returns_empty_json_for_null(): void {
	Functions\when( 'wp_json_encode' )->alias( 'json_encode' );

	$result = GULOLI_Settings::sanitize_links( null );

	$this->assertSame( '[]', $result );
}

	/**
	 * Tests that an entry with empty title and URL is skipped.
	 */
public function test_sanitize_links_skips_entry_with_empty_title_and_url(): void {
	$this->stub_sanitizers();

	$input  = wp_json_encode(
		array(
			array(
				'title'  => '',
				'url'    => '',
				'active' => true,
			),
		)
	);
	$result = GULOLI_Settings::sanitize_links( $input );

	$this->assertSame( '[]', $result );
}

	/**
	 * Tests that non-array entries are skipped.
	 */
public function test_sanitize_links_skips_non_array_entries(): void {
	$this->stub_sanitizers();

	$input   = wp_json_encode(
		array(
			'not-an-array',
			array(
				'title' => 'GitHub',
				'url'   => 'https://github.com',
			),
		)
	);
	$result  = GULOLI_Settings::sanitize_links( $input );
	$decoded = json_decode( $result, true );

	$this->assertCount( 1, $decoded );
}

	/**
	 * Tests that a valid link is preserved after sanitization.
	 */
public function test_sanitize_links_preserves_valid_link(): void {
	$this->stub_sanitizers();

	$input   = wp_json_encode(
		array(
			array(
				'title'  => 'GitHub',
				'url'    => 'https://github.com',
				'active' => true,
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
	 * Tests that active defaults to true when absent.
	 */
public function test_sanitize_links_active_defaults_to_true_when_absent(): void {
	$this->stub_sanitizers();

	$input   = wp_json_encode(
		array(
			array(
				'title' => 'Link',
				'url'   => 'https://example.com',
			),
		)
	);
	$result  = GULOLI_Settings::sanitize_links( $input );
	$decoded = json_decode( $result, true );

	$this->assertTrue( $decoded[0]['active'] );
}

	/**
	 * Tests that active false is preserved.
	 */
public function test_sanitize_links_preserves_active_false(): void {
	$this->stub_sanitizers();

	$input   = wp_json_encode(
		array(
			array(
				'title'  => 'Link',
				'url'    => 'https://example.com',
				'active' => false,
			),
		)
	);
	$result  = GULOLI_Settings::sanitize_links( $input );
	$decoded = json_decode( $result, true );

	$this->assertFalse( $decoded[0]['active'] );
}

	/**
	 * Tests that HTML is stripped from link titles.
	 */
public function test_sanitize_links_strips_html_from_title(): void {
	$this->stub_sanitizers();

	$input   = wp_json_encode(
		array(
			array(
				'title'  => '<b>GitHub</b>',
				'url'    => 'https://github.com',
				'active' => true,
			),
		)
	);
	$result  = GULOLI_Settings::sanitize_links( $input );
	$decoded = json_decode( $result, true );

	$this->assertSame( 'GitHub', $decoded[0]['title'] );
}

	/**
	 * Tests that multiple valid links are preserved.
	 */
public function test_sanitize_links_preserves_multiple_valid_links(): void {
	$this->stub_sanitizers();

	$input = wp_json_encode(
		array(
			array(
				'title'  => 'GitHub',
				'url'    => 'https://github.com',
				'active' => true,
			),
			array(
				'title'  => 'LinkedIn',
				'url'    => 'https://linkedin.com',
				'active' => false,
			),
		)
	);

	$result  = GULOLI_Settings::sanitize_links( $input );
	$decoded = json_decode( $result, true );

	$this->assertCount( 2, $decoded );
	$this->assertSame( 'GitHub', $decoded[0]['title'] );
	$this->assertSame( 'LinkedIn', $decoded[1]['title'] );
	$this->assertFalse( $decoded[1]['active'] );
}

	/**
	 * Tests that an entry with a title but no URL is kept.
	 */
public function test_sanitize_links_entry_with_title_only_is_kept(): void {
	$this->stub_sanitizers();

	$input   = wp_json_encode(
		array(
			array(
				'title' => 'Label only',
				'url'   => '',
			),
		)
	);
	$result  = GULOLI_Settings::sanitize_links( $input );
	$decoded = json_decode( $result, true );

	$this->assertCount( 1, $decoded );
}
}
