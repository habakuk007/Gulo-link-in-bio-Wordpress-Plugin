<?php
/**
 * Frontend — shortcode registration and asset enqueueing.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Frontend
 *
 * Registers the [link_in_bio] shortcode and loads frontend assets
 * only when the shortcode is actually used on a page.
 */
class LIB_Frontend {

	/** @var bool Whether assets have been enqueued in this request. */
	private bool $assets_enqueued = false;

	/** Constructor — registers hooks. */
	public function __construct() {
		add_shortcode( 'link_in_bio', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Registers (but does not enqueue) frontend assets.
	 * The stylesheet is enqueued lazily inside the shortcode.
	 *
	 * @return void
	 */
	public function register_assets(): void {
		wp_register_style(
			'lib-frontend',
			LIB_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			LIB_VERSION
		);
	}

	/**
	 * Shortcode handler for [link_in_bio].
	 *
	 * @param array<string, string>|string $atts Shortcode attributes (unused).
	 * @return string Rendered HTML.
	 */
	public function render_shortcode( $atts = array() ): string {
		if ( ! $this->assets_enqueued ) {
			wp_enqueue_style( 'lib-frontend' );
			$this->assets_enqueued = true;
		}

		$settings   = LIB_Settings::get();
		$links      = LIB_Settings::get_links();
		$custom_css = $this->build_custom_css( $settings );

		ob_start();
		include LIB_PLUGIN_DIR . 'templates/display.php';
		return ob_get_clean();
	}

	/**
	 * Generates inline CSS custom properties from saved settings.
	 *
	 * @param array<string, string> $settings Plugin settings.
	 * @return string Inline <style> block content (no tags).
	 */
	private function build_custom_css( array $settings ): string {
		if ( 'gradient' === $settings['background_type'] ) {
			$bg = sprintf(
				'linear-gradient(160deg, %s 0%%, %s 100%%)',
				esc_attr( $settings['gradient_start'] ),
				esc_attr( $settings['gradient_end'] )
			);
		} else {
			$bg = esc_attr( $settings['background_color'] );
		}

		return sprintf(
			'.lib-container{--lib-bg:%s;--lib-btn-bg:%s;--lib-btn-color:%s;--lib-text-color:%s;}',
			$bg,
			esc_attr( $settings['button_bg_color'] ),
			esc_attr( $settings['button_text_color'] ),
			esc_attr( $settings['profile_text_color'] )
		);
	}
}
