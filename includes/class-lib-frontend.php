<?php
/**
 * Frontend — page template serving and asset enqueueing.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Frontend
 *
 * Serves the Link in Bio full-page template for whichever WordPress Page
 * the admin has designated in Settings → Link in Bio, and loads frontend
 * assets only on that page.
 */
class LIB_Frontend {

	/** Constructor — registers hooks. */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'load_template' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
	}

	/**
	 * Serves the plugin template file when the designated page is viewed.
	 *
	 * @param string $template Resolved template path.
	 * @return string
	 */
	public function load_template( string $template ): string {
		if ( $this->is_lib_page() ) {
			return LIB_PLUGIN_DIR . 'templates/page-link-in-bio.php';
		}
		return $template;
	}

	/**
	 * Registers and enqueues frontend assets (stylesheet + inline CSS custom
	 * properties) only on the designated Link in Bio page.
	 *
	 * @return void
	 */
	public function maybe_enqueue_assets(): void {
		if ( ! $this->is_lib_page() ) {
			return;
		}

		wp_enqueue_style(
			'lib-frontend',
			LIB_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			LIB_VERSION
		);

		wp_add_inline_style(
			'lib-frontend',
			$this->build_custom_css( LIB_Settings::get() )
		);
	}

	/**
	 * Checks whether the current request is for the designated Link in Bio page.
	 *
	 * @return bool
	 */
	private function is_lib_page(): bool {
		$page_id = (int) LIB_Settings::get( 'page_id' );
		return $page_id > 0 && is_page( $page_id );
	}

	/**
	 * Generates inline CSS custom properties from saved settings.
	 *
	 * @param array<string, mixed> $settings Plugin settings.
	 * @return string CSS rule block (no surrounding tags).
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
