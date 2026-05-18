<?php
/**
 * Admin settings page — menu, settings registration, and page render.
 *
 * @package SimpleBioLinks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class GULOLI_Admin
 *
 * Manages the WordPress admin settings page under the Gulo Link-in-Bio menu.
 * Uses the Settings API for saving; renders the form HTML directly for
 * maximum control over layout and the dynamic links repeater.
 */
class GULOLI_Admin {

	/** Admin page slug used as screen ID. */
	const PAGE_SLUG = 'simple-bio-links';

	/** Settings group used with settings_fields(). */
	const SETTINGS_GROUP = 'guloli_settings_group';

	/** Constructor — registers hooks. */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . GULOLI_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );

		// Purge page cache after settings are saved.
		add_action( 'update_option_' . GULOLI_Settings::OPTION_SETTINGS, array( $this, 'purge_page_cache' ), 10, 2 );
	}

	/**
	 * Adds the plugin settings page as a top-level admin menu item.
	 *
	 * Using a top-level page (rather than a sub-page of Settings) allows editors,
	 * who do not have manage_options, to access it via the guloli_manage_settings cap.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		add_menu_page(
			__( 'Gulo Link-in-Bio Settings', 'simple-bio-links' ),
			__( 'Gulo Link-in-Bio', 'simple-bio-links' ),
			'guloli_manage_settings',
			self::PAGE_SLUG,
			array( $this, 'render_page' ),
			'dashicons-admin-links',
			81
		);
	}

	/**
	 * Registers settings with the Settings API.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			self::SETTINGS_GROUP,
			GULOLI_Settings::OPTION_SETTINGS,
			array(
				'sanitize_callback' => array( 'GULOLI_Settings', 'sanitize_settings' ),
				'default'           => GULOLI_Settings::get_defaults(),
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			GULOLI_Settings::OPTION_LINKS,
			array(
				'sanitize_callback' => array( 'GULOLI_Settings', 'sanitize_links' ),
				'default'           => wp_json_encode( array() ),
			)
		);
	}

	/**
	 * Enqueues admin assets only on the plugin settings page.
	 *
	 * @param string $hook Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();

		wp_enqueue_style(
			'guloli-admin',
			GULOLI_PLUGIN_URL . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			GULOLI_VERSION
		);

		wp_enqueue_script(
			'guloli-admin',
			GULOLI_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ),
			GULOLI_VERSION,
			true
		);

		wp_localize_script(
			'guloli-admin',
			'guloAdmin',
			array(
				'links'         => GULOLI_Settings::get_links(),
				'mediaTitle'    => __( 'Select Profile Image', 'simple-bio-links' ),
				'mediaButton'   => __( 'Use this image', 'simple-bio-links' ),
				'removeConfirm' => __( 'Remove this link?', 'simple-bio-links' ),
				'nonce'         => wp_create_nonce( 'guloli-admin' ),
			)
		);
	}

	/**
	 * Adds a "Settings" action link on the Plugins screen.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_action_links( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( 'Settings', 'simple-bio-links' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Renders the admin settings page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'guloli_manage_settings' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'simple-bio-links' ) );
		}

		$s = GULOLI_Settings::get();
		?>
		<div class="wrap lib-admin-wrap">
			<h1><?php esc_html_e( 'Gulo Link-in-Bio', 'simple-bio-links' ); ?></h1>

			<?php settings_errors( GULOLI_Settings::OPTION_SETTINGS ); ?>

			<p class="lib-shortcode-tip">
				<?php esc_html_e( 'Create any WordPress Page, then select it below — the plugin will serve the Gulo Link-in-Bio layout for that page automatically.', 'simple-bio-links' ); ?>
			</p>

			<form method="post" action="options.php" novalidate>
				<?php settings_fields( self::SETTINGS_GROUP ); ?>

				<!-- ── Page ─────────────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Gulo Link-in-Bio Page', 'simple-bio-links' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="lib-page-id"><?php esc_html_e( 'Page', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<?php
								wp_dropdown_pages(
									array(
										'name'             => esc_attr( GULOLI_Settings::OPTION_SETTINGS ) . '[page_id]',
										'id'               => 'lib-page-id',
										'selected'         => (int) $s['page_id'],
										'show_option_none' => esc_html__( '— Select a page —', 'simple-bio-links' ),
										'option_none_value' => '0',
									)
								);
								?>
								<p class="description">
									<?php esc_html_e( 'The selected page will display the Gulo Link-in-Bio profile layout, bypassing the active theme.', 'simple-bio-links' ); ?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<!-- ── Profile ──────────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Profile', 'simple-bio-links' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="lib-profile-name"><?php esc_html_e( 'Name', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									id="lib-profile-name"
									name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[profile_name]"
									value="<?php echo esc_attr( $s['profile_name'] ); ?>"
									class="regular-text"
									autocomplete="name"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-profile-bio"><?php esc_html_e( 'Bio / Tagline', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<textarea
									id="lib-profile-bio"
									name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[profile_bio]"
									rows="3"
									class="large-text"
								><?php echo esc_textarea( $s['profile_bio'] ); ?></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-profile-image"><?php esc_html_e( 'Profile Image URL', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<div class="lib-image-field">
									<input
										type="url"
										id="lib-profile-image"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[profile_image]"
										value="<?php echo esc_url( $s['profile_image'] ); ?>"
										class="regular-text"
										autocomplete="off"
									/>
									<button type="button" id="lib-upload-image" class="button">
										<?php esc_html_e( 'Select Image', 'simple-bio-links' ); ?>
									</button>
									<button type="button" id="lib-remove-image" class="button button-link-delete<?php echo empty( $s['profile_image'] ) ? ' hidden' : ''; ?>">
										<?php esc_html_e( 'Remove', 'simple-bio-links' ); ?>
									</button>
								</div>
								<?php if ( ! empty( $s['profile_image'] ) ) : ?>
									<div class="lib-image-preview-wrap">
										<img
											id="lib-image-preview"
											src="<?php echo esc_url( $s['profile_image'] ); ?>"
											alt="<?php esc_attr_e( 'Profile image preview', 'simple-bio-links' ); ?>"
											width="80"
											height="80"
										/>
									</div>
								<?php else : ?>
									<div class="lib-image-preview-wrap hidden">
										<img id="lib-image-preview" src="" alt="<?php esc_attr_e( 'Profile image preview', 'simple-bio-links' ); ?>" width="80" height="80" />
									</div>
								<?php endif; ?>
							</td>
						</tr>
					</table>
				</div>

				<!-- ── Appearance ───────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Appearance', 'simple-bio-links' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php esc_html_e( 'Background', 'simple-bio-links' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php esc_html_e( 'Background type', 'simple-bio-links' ); ?></legend>
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[background_type]"
											value="gradient"
											<?php checked( $s['background_type'], 'gradient' ); ?>
											class="lib-bg-type-radio"
										/>
										<?php esc_html_e( 'Gradient', 'simple-bio-links' ); ?>
									</label>
									&nbsp;&nbsp;
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[background_type]"
											value="solid"
											<?php checked( $s['background_type'], 'solid' ); ?>
											class="lib-bg-type-radio"
										/>
										<?php esc_html_e( 'Solid color', 'simple-bio-links' ); ?>
									</label>
								</fieldset>

								<div id="lib-bg-gradient" class="lib-color-group<?php echo 'solid' === $s['background_type'] ? ' hidden' : ''; ?>">
									<label for="lib-gradient-start"><?php esc_html_e( 'Gradient Start', 'simple-bio-links' ); ?></label>
									<input
										type="text"
										id="lib-gradient-start"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[gradient_start]"
										value="<?php echo esc_attr( $s['gradient_start'] ); ?>"
										class="lib-color-picker"
										data-default-color="#1a1a2e"
									/>
									<label for="lib-gradient-end"><?php esc_html_e( 'Gradient End', 'simple-bio-links' ); ?></label>
									<input
										type="text"
										id="lib-gradient-end"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[gradient_end]"
										value="<?php echo esc_attr( $s['gradient_end'] ); ?>"
										class="lib-color-picker"
										data-default-color="#16213e"
									/>
								</div>

								<div id="lib-bg-solid" class="lib-color-group<?php echo 'gradient' === $s['background_type'] ? ' hidden' : ''; ?>">
									<label for="lib-bg-color"><?php esc_html_e( 'Background Color', 'simple-bio-links' ); ?></label>
									<input
										type="text"
										id="lib-bg-color"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[background_color]"
										value="<?php echo esc_attr( $s['background_color'] ); ?>"
										class="lib-color-picker"
										data-default-color="#1a1a2e"
									/>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-text-color"><?php esc_html_e( 'Profile Text Color', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									id="lib-text-color"
									name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[profile_text_color]"
									value="<?php echo esc_attr( $s['profile_text_color'] ); ?>"
									class="lib-color-picker"
									data-default-color="#ffffff"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Button Style', 'simple-bio-links' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php esc_html_e( 'Button style', 'simple-bio-links' ); ?></legend>
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[button_style]"
											value="filled"
											<?php checked( $s['button_style'], 'filled' ); ?>
										/>
										<?php esc_html_e( 'Solid', 'simple-bio-links' ); ?>
									</label>
									&nbsp;&nbsp;
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[button_style]"
											value="glass"
											<?php checked( $s['button_style'], 'glass' ); ?>
										/>
										<?php esc_html_e( 'Glass (frosted)', 'simple-bio-links' ); ?>
									</label>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Button Colors', 'simple-bio-links' ); ?></th>
							<td>
								<div class="lib-color-group">
									<label for="lib-btn-bg"><?php esc_html_e( 'Button Background', 'simple-bio-links' ); ?></label>
									<input
										type="text"
										id="lib-btn-bg"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[button_bg_color]"
										value="<?php echo esc_attr( $s['button_bg_color'] ); ?>"
										class="lib-color-picker"
										data-default-color="#ffffff"
									/>
									<label for="lib-btn-text"><?php esc_html_e( 'Button Text', 'simple-bio-links' ); ?></label>
									<input
										type="text"
										id="lib-btn-text"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[button_text_color]"
										value="<?php echo esc_attr( $s['button_text_color'] ); ?>"
										class="lib-color-picker"
										data-default-color="#1a1a1a"
									/>
								</div>
							</td>
						</tr>
					</table>
				</div>

				<!-- ── Links ────────────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Links', 'simple-bio-links' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Drag rows to reorder. Uncheck "Active" to hide a link without deleting it.', 'simple-bio-links' ); ?>
					</p>

					<div
						id="lib-links-list"
						class="lib-links-list"
						role="list"
						aria-label="<?php esc_attr_e( 'Manage links', 'simple-bio-links' ); ?>"
					>
						<!-- Populated by admin.js from libAdmin.links -->
					</div>

					<button type="button" id="lib-add-link" class="button button-secondary lib-add-link-btn">
						<span aria-hidden="true">+</span>
						<?php esc_html_e( 'Add Link', 'simple-bio-links' ); ?>
					</button>

					<!-- Hidden input: JS serializes link rows to JSON here before submit -->
					<input
						type="hidden"
						id="lib-links-json"
						name="<?php echo esc_attr( GULOLI_Settings::OPTION_LINKS ); ?>"
						value="<?php echo esc_attr( wp_json_encode( GULOLI_Settings::get_links() ) ); ?>"
					/>
				</div>

				<!-- ── Legal ───────────────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Legal', 'simple-bio-links' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Optional links shown in the page footer above "Powered by". Leave blank to hide.', 'simple-bio-links' ); ?>
					</p>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="lib-imprint-url"><?php esc_html_e( 'Imprint URL (Impressum)', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<input
									type="url"
									id="lib-imprint-url"
									name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[imprint_url]"
									value="<?php echo esc_url( $s['imprint_url'] ); ?>"
									class="regular-text"
									placeholder="https://"
									autocomplete="off"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-privacy-url"><?php esc_html_e( 'Privacy Policy URL (Datenschutzerklärung)', 'simple-bio-links' ); ?></label>
							</th>
							<td>
								<input
									type="url"
									id="lib-privacy-url"
									name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[privacy_url]"
									value="<?php echo esc_url( $s['privacy_url'] ); ?>"
									class="regular-text"
									placeholder="https://"
									autocomplete="off"
								/>
							</td>
						</tr>
					</table>
				</div>

				<!-- ── SEO ──────────────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'SEO', 'simple-bio-links' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php esc_html_e( 'Search Engines', 'simple-bio-links' ); ?></th>
							<td>
								<label>
									<input
										type="checkbox"
										name="<?php echo esc_attr( GULOLI_Settings::OPTION_SETTINGS ); ?>[seo_noindex]"
										value="1"
										<?php checked( $s['seo_noindex'] ); ?>
									/>
									<?php esc_html_e( 'Exclude this page from search engines (noindex)', 'simple-bio-links' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'Adds a noindex tag to the page. Use this if you prefer the page not to appear in Google or Bing results.', 'simple-bio-links' ); ?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<?php submit_button( __( 'Save Settings', 'simple-bio-links' ) ); ?>
			</form>

			<p class="lib-donate">
				<a
					href="<?php echo esc_url( 'https://trumpkin.de/donate' ); ?>"
					target="_blank"
					rel="noopener noreferrer"
					aria-label="<?php esc_attr_e( 'Buy me a coffee (opens in new tab)', 'simple-bio-links' ); ?>"
				><?php esc_html_e( 'Buy me a coffee', 'simple-bio-links' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Purges the Gulo Link-in-Bio page from all known caching layers when settings are saved.
	 *
	 * Fires on the update_option_GULOLI_Settings action so that changes to the profile,
	 * links, colors, or designated page are visible immediately without a manual flush.
	 * Handles both old and new page IDs so that re-assigning the page also clears
	 * the previously designated one.
	 *
	 * @param mixed $old_value Previous option value (array or false when not yet set).
	 * @param mixed $new_value New option value (array).
	 * @return void
	 */
	public function purge_page_cache( $old_value, $new_value ): void {
		$ids = array_unique(
			array_filter(
				array(
					isset( $old_value['page_id'] ) ? (int) $old_value['page_id'] : 0,
					isset( $new_value['page_id'] ) ? (int) $new_value['page_id'] : 0,
				)
			)
		);

		foreach ( $ids as $page_id ) {
			$this->purge_single_page( $page_id );
		}
	}

	/**
	 * Clears all known caching layers for a single WordPress page.
	 *
	 * Covers: WordPress object cache, WP Super Cache, WP Rocket, W3 Total Cache,
	 * WP Fastest Cache, LiteSpeed Cache, and Cache Enabler.
	 *
	 * @param int $page_id WordPress post/page ID.
	 * @return void
	 */
	private function purge_single_page( int $page_id ): void {
		// WordPress core object cache.
		clean_post_cache( $page_id );

		// WP Super Cache.
		if ( function_exists( 'wpsc_delete_post_cache' ) ) {
			wpsc_delete_post_cache( $page_id );
		}

		// WP Rocket.
		if ( function_exists( 'rocket_clean_post' ) ) {
			rocket_clean_post( $page_id );
		}

		// W3 Total Cache.
		if ( function_exists( 'w3tc_flush_post' ) ) {
			w3tc_flush_post( $page_id );
		}

		// WP Fastest Cache.
		if ( function_exists( 'wpfc_clear_post_cache_by_id' ) ) {
			wpfc_clear_post_cache_by_id( $page_id );
		}

		// LiteSpeed Cache (event-driven API).
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'litespeed_purge_post', $page_id );

		// Cache Enabler.
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		do_action( 'cache_enabler_clear_page_cache_by_post', $page_id );
	}
}
