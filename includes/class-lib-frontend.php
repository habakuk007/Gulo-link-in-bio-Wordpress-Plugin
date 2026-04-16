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

		// Admin bar shortcut to settings — shown to admins and editors on the lib page.
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_node' ), 100 );

		// Yoast SEO — override its output on the lib page instead of emitting
		// duplicate tags. Hooks are registered unconditionally; each callback
		// is a no-op when not on the lib page.
		if ( defined( 'WPSEO_VERSION' ) ) {
			add_filter( 'wpseo_title', array( $this, 'filter_yoast_title' ) );
			add_filter( 'wpseo_opengraph_type', array( $this, 'filter_yoast_og_type' ) );
			add_filter( 'wpseo_opengraph_title', array( $this, 'filter_yoast_og_title' ) );
			add_filter( 'wpseo_robots', array( $this, 'filter_yoast_robots' ) );
		}
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
	 * When Yoast SEO is active it already emits canonical, og:type, og:title,
	 * og:url, and Twitter card tags (all corrected via the wpseo_* filters above).
	 * We skip those to avoid duplicate tags and only add what Yoast omits:
	 * og:description, og:image, and the JSON-LD Person schema.
	 *
	 * @return void
	 */
	public function output_seo_meta(): void {
		if ( ! $this->is_lib_page() ) {
			return;
		}

		$s            = LIB_Settings::get();
		$page_url     = get_permalink( (int) $s['page_id'] );
		$yoast_active = defined( 'WPSEO_VERSION' );

		// Robots — noindex when opted in. Yoast handles this via filter_yoast_robots()
		// when active, so only emit the tag when Yoast is absent.
		if ( ! $yoast_active && ! empty( $s['seo_noindex'] ) ) {
			echo '<meta name="robots" content="noindex,follow">' . "\n";
		}

		// Description — Yoast only outputs this when a per-post Yoast meta desc is
		// saved, which won't be set for the lib page. Safe to always emit.
		if ( ! empty( $s['profile_bio'] ) ) {
			echo '<meta name="description" content="' . esc_attr( $s['profile_bio'] ) . '">' . "\n";
		}

		// Open Graph core tags — emitted by Yoast and corrected via its filters;
		// skip them here to prevent duplicates.
		if ( ! $yoast_active ) {
			echo '<meta property="og:type" content="profile">' . "\n";

			if ( ! empty( $s['profile_name'] ) ) {
				echo '<meta property="og:title" content="' . esc_attr( $s['profile_name'] ) . '">' . "\n";
			}

			if ( $page_url ) {
				echo '<meta property="og:url" content="' . esc_url( $page_url ) . '">' . "\n";
				echo '<link rel="canonical" href="' . esc_url( $page_url ) . '">' . "\n";
			}
		}

		// og:description — Yoast does not output this for pages without a Yoast
		// meta desc entry, so always emit it.
		if ( ! empty( $s['profile_bio'] ) ) {
			echo '<meta property="og:description" content="' . esc_attr( $s['profile_bio'] ) . '">' . "\n";
		}

		// og:image — Yoast only outputs this when the page has a featured image.
		// The profile image comes from plugin settings, not the post thumbnail.
		if ( ! empty( $s['profile_image'] ) ) {
			echo '<meta property="og:image" content="' . esc_url( $s['profile_image'] ) . '">' . "\n";
		}

		// Twitter / X card — emitted and corrected by Yoast; skip when active.
		if ( ! $yoast_active ) {
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
		}

		// JSON-LD — Schema.org Person. Yoast's own schema does not include a
		// Person type with profile image, so always emit this block.
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
	 * Overrides the Yoast SEO <title> with the profile name on the lib page.
	 *
	 * @param string $title Yoast-formatted title string.
	 * @return string
	 */
	public function filter_yoast_title( string $title ): string {
		if ( ! $this->is_lib_page() ) {
			return $title;
		}

		$name = LIB_Settings::get( 'profile_name' );

		if ( ! $name ) {
			return $title;
		}

		return $name . ' - ' . get_bloginfo( 'name' );
	}

	/**
	 * Overrides Yoast's og:type to "profile" on the lib page.
	 *
	 * @param string $type Open Graph type value.
	 * @return string
	 */
	public function filter_yoast_og_type( string $type ): string {
		return $this->is_lib_page() ? 'profile' : $type;
	}

	/**
	 * Overrides Yoast's og:title with the profile name on the lib page.
	 *
	 * @param string $title Open Graph title value.
	 * @return string
	 */
	public function filter_yoast_og_title( string $title ): string {
		if ( ! $this->is_lib_page() ) {
			return $title;
		}

		$name = LIB_Settings::get( 'profile_name' );

		return $name ? $name : $title;
	}

	/**
	 * Enforces noindex on the lib page when the admin has opted in via settings.
	 *
	 * @param string $robots Yoast robots directive string (e.g. "index, follow, …").
	 * @return string
	 */
	public function filter_yoast_robots( string $robots ): string {
		if ( ! $this->is_lib_page() || empty( LIB_Settings::get( 'seo_noindex' ) ) ) {
			return $robots;
		}

		// Replace "index" with "noindex" only when not already noindex.
		if ( false === strpos( $robots, 'noindex' ) ) {
			$robots = str_replace( 'index', 'noindex', $robots );
		}

		return $robots;
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
	 * Adds an "Edit Link in Bio" shortcut to the WordPress admin bar.
	 *
	 * Only shown when viewing the Link in Bio page and the current user has the
	 * lib_manage_settings capability (administrators and editors).
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public function add_admin_bar_node( WP_Admin_Bar $wp_admin_bar ): void {
		if ( ! $this->is_lib_page() || ! current_user_can( 'lib_manage_settings' ) ) {
			return;
		}

		$wp_admin_bar->add_node(
			array(
				'id'    => 'lib-settings',
				'title' => __( 'Edit Link in Bio', 'link-in-bio' ),
				'href'  => admin_url( 'admin.php?page=link-in-bio' ),
			)
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
