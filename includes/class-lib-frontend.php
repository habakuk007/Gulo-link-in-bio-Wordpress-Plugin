<?php
/**
 * Frontend — page template serving, asset enqueueing, and SEO meta output.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Frontend
 *
 * Serves the Link in Bio full-page template for whichever WordPress Page
 * the admin has designated in Settings → Link in Bio, loads frontend
 * assets, and injects SEO / Open Graph meta tags via wp_head.
 */
class LIB_Frontend {

	/** Constructor — registers hooks. */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'load_template' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
		add_action( 'wp_head', array( $this, 'output_seo_meta' ), 5 );
		add_filter( 'document_title_parts', array( $this, 'filter_document_title' ) );
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
	 * Outputs SEO and Open Graph meta tags in wp_head, only on the Link in Bio page.
	 *
	 * @return void
	 */
	public function output_seo_meta(): void {
		if ( ! $this->is_lib_page() ) {
			return;
		}

		$s        = LIB_Settings::get();
		$page_url = get_permalink( (int) $s['page_id'] );

		// Robots — noindex when opted in.
		if ( ! empty( $s['seo_noindex'] ) ) {
			echo '<meta name="robots" content="noindex,follow">' . "\n";
		}

		// Description.
		if ( ! empty( $s['profile_bio'] ) ) {
			echo '<meta name="description" content="' . esc_attr( $s['profile_bio'] ) . '">' . "\n";
		}

		// Open Graph.
		echo '<meta property="og:type" content="profile">' . "\n";

		if ( ! empty( $s['profile_name'] ) ) {
			echo '<meta property="og:title" content="' . esc_attr( $s['profile_name'] ) . '">' . "\n";
		}

		if ( ! empty( $s['profile_bio'] ) ) {
			echo '<meta property="og:description" content="' . esc_attr( $s['profile_bio'] ) . '">' . "\n";
		}

		if ( ! empty( $s['profile_image'] ) ) {
			echo '<meta property="og:image" content="' . esc_url( $s['profile_image'] ) . '">' . "\n";
		}

		if ( $page_url ) {
			echo '<meta property="og:url" content="' . esc_url( $page_url ) . '">' . "\n";
			echo '<link rel="canonical" href="' . esc_url( $page_url ) . '">' . "\n";
		}

		// Twitter / X card.
		$twitter_card = ! empty( $s['profile_image'] ) ? 'summary_large_image' : 'summary';
		echo '<meta name="twitter:card" content="' . esc_attr( $twitter_card ) . '">' . "\n";

		if ( ! empty( $s['profile_name'] ) ) {
			echo '<meta name="twitter:title" content="' . esc_attr( $s['profile_name'] ) . '">' . "\n";
		}

		if ( ! empty( $s['profile_bio'] ) ) {
			echo '<meta name="twitter:description" content="' . esc_attr( $s['profile_bio'] ) . '">' . "\n";
		}

		if ( ! empty( $s['profile_image'] ) ) {
			echo '<meta name="twitter:image" content="' . esc_url( $s['profile_image'] ) . '">' . "\n";
		}

		// JSON-LD — Schema.org Person.
		$ld = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Person',
		);

		if ( ! empty( $s['profile_name'] ) ) {
			$ld['name'] = $s['profile_name'];
		}

		if ( ! empty( $s['profile_image'] ) ) {
			$ld['image'] = $s['profile_image'];
		}

		if ( $page_url ) {
			$ld['url'] = $page_url;
		}

		if ( ! empty( $s['profile_bio'] ) ) {
			$ld['description'] = $s['profile_bio'];
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $ld ) . '</script>' . "\n";
	}

	/**
	 * Sets the browser <title> to the profile name on the Link in Bio page.
	 *
	 * @param array<string, string> $parts Title parts.
	 * @return array<string, string>
	 */
	public function filter_document_title( array $parts ): array {
		if ( ! $this->is_lib_page() ) {
			return $parts;
		}

		$name = LIB_Settings::get( 'profile_name' );
		if ( $name ) {
			$parts['title'] = $name;
		}

		return $parts;
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
