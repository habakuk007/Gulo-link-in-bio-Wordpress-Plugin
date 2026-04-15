<?php
/**
 * Frontend — page template registration and asset enqueueing.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Frontend
 *
 * Registers the "Link in Bio" WordPress page template and loads frontend
 * assets only on pages that use it.
 */
class LIB_Frontend {

	/**
	 * Internal template identifier used in post meta and filter callbacks.
	 */
	const TEMPLATE_KEY = 'link-in-bio-template';

	/** Constructor — registers hooks. */
	public function __construct() {
		add_filter( 'theme_page_templates', array( $this, 'add_page_template' ) );
		add_filter( 'template_include', array( $this, 'load_template' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
	}

	/**
	 * Adds the plugin template to WordPress's page template dropdown.
	 *
	 * @param array<string, string> $templates Registered page templates keyed by filename.
	 * @return array<string, string>
	 */
	public function add_page_template( array $templates ): array {
		$templates[ self::TEMPLATE_KEY ] = __( 'Link in Bio', 'link-in-bio' );
		return $templates;
	}

	/**
	 * Serves the plugin template file when a page uses the Link in Bio template.
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
	 * properties) only on pages that use the Link in Bio template.
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
	 * Checks whether the current page uses the Link in Bio template.
	 *
	 * @return bool
	 */
	private function is_lib_page(): bool {
		if ( ! is_singular( 'page' ) ) {
			return false;
		}
		return self::TEMPLATE_KEY === get_post_meta( get_the_ID(), '_wp_page_template', true );
	}

	/**
	 * Generates inline CSS custom properties from saved settings.
	 *
	 * @param array<string, string> $settings Plugin settings.
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
