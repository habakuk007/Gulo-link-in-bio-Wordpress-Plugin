<?php
/**
 * Settings helper — reads, sanitizes, and provides defaults.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Settings
 *
 * Static helper for reading/sanitizing plugin options.
 * No instances needed — all methods are static.
 */
class LIB_Settings {

	/** WordPress option key for general settings. */
	const OPTION_SETTINGS = 'lib_settings';

	/** WordPress option key for the links JSON array. */
	const OPTION_LINKS = 'lib_links';

	/**
	 * Returns default settings values.
	 *
	 * @return array<string, string>
	 */
	public static function get_defaults(): array {
		return array(
			'profile_name'       => get_bloginfo( 'name' ),
			'profile_bio'        => get_bloginfo( 'description' ),
			'profile_image'      => '',
			'background_type'    => 'gradient',
			'background_color'   => '#1a1a2e',
			'gradient_start'     => '#1a1a2e',
			'gradient_end'       => '#16213e',
			'button_style'       => 'filled',
			'button_bg_color'    => '#ffffff',
			'button_text_color'  => '#1a1a1a',
			'profile_text_color' => '#ffffff',
		);
	}

	/**
	 * Retrieves a single setting or all settings, merged with defaults.
	 *
	 * @param string $key      Option key. Empty string returns all settings.
	 * @param mixed  $fallback Fallback if key not found.
	 * @return mixed
	 */
	public static function get( string $key = '', $fallback = null ) {
		$saved    = get_option( self::OPTION_SETTINGS, array() );
		$settings = wp_parse_args( is_array( $saved ) ? $saved : array(), self::get_defaults() );

		if ( '' === $key ) {
			return $settings;
		}

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $fallback;
	}

	/**
	 * Retrieves the links array.
	 *
	 * @return array<int, array{title: string, url: string, active: bool}>
	 */
	public static function get_links(): array {
		$json  = get_option( self::OPTION_LINKS, '[]' );
		$links = json_decode( $json, true );
		return is_array( $links ) ? $links : array();
	}

	/**
	 * Sanitize callback for the settings option.
	 *
	 * @param array<string, mixed> $input Raw input from the form.
	 * @return array<string, string>
	 */
	public static function sanitize_settings( $input ): array {
		if ( ! is_array( $input ) ) {
			return self::get_defaults();
		}

		$output = array();

		$text_fields = array( 'profile_name', 'profile_bio', 'background_type', 'button_style' );
		foreach ( $text_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$output[ $field ] = sanitize_text_field( $input[ $field ] );
			}
		}

		if ( isset( $input['profile_image'] ) ) {
			$output['profile_image'] = esc_url_raw( trim( $input['profile_image'] ) );
		}

		$color_fields = array(
			'background_color',
			'gradient_start',
			'gradient_end',
			'button_bg_color',
			'button_text_color',
			'profile_text_color',
		);
		foreach ( $color_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized = sanitize_hex_color( $input[ $field ] );
				if ( $sanitized ) {
					$output[ $field ] = $sanitized;
				}
			}
		}

		return $output;
	}

	/**
	 * Sanitize callback for the links JSON option.
	 *
	 * @param string $input Raw JSON string from the form.
	 * @return string Sanitized JSON string.
	 */
	public static function sanitize_links( $input ): string {
		$links = json_decode( $input, true );

		if ( ! is_array( $links ) ) {
			return wp_json_encode( array() );
		}

		$sanitized = array();
		foreach ( $links as $link ) {
			if ( ! is_array( $link ) ) {
				continue;
			}

			$title = sanitize_text_field( $link['title'] ?? '' );
			$url   = esc_url_raw( $link['url'] ?? '' );

			// Skip empty links.
			if ( '' === $title && '' === $url ) {
				continue;
			}

			$sanitized[] = array(
				'title'  => $title,
				'url'    => $url,
				'active' => (bool) ( $link['active'] ?? true ),
			);
		}

		return wp_json_encode( $sanitized );
	}
}
