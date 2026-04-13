<?php
/**
 * Admin settings page — menu, settings registration, and page render.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class LIB_Admin
 *
 * Manages the WordPress admin settings page under Settings > Link in Bio.
 * Uses the Settings API for saving; renders the form HTML directly for
 * maximum control over layout and the dynamic links repeater.
 */
class LIB_Admin {

	/** Admin page slug used as screen ID. */
	const PAGE_SLUG = 'link-in-bio';

	/** Settings group used with settings_fields(). */
	const SETTINGS_GROUP = 'lib_settings_group';

	/** Constructor — registers hooks. */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . LIB_PLUGIN_BASENAME, array( $this, 'add_action_links' ) );
	}

	/**
	 * Adds the plugin settings page under the Settings menu.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		add_options_page(
			__( 'Link in Bio Settings', 'link-in-bio' ),
			__( 'Link in Bio', 'link-in-bio' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
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
			LIB_Settings::OPTION_SETTINGS,
			array(
				'sanitize_callback' => array( 'LIB_Settings', 'sanitize_settings' ),
				'default'           => LIB_Settings::get_defaults(),
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			LIB_Settings::OPTION_LINKS,
			array(
				'sanitize_callback' => array( 'LIB_Settings', 'sanitize_links' ),
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
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();

		wp_enqueue_style(
			'lib-admin',
			LIB_PLUGIN_URL . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			LIB_VERSION
		);

		wp_enqueue_script(
			'lib-admin',
			LIB_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable' ),
			LIB_VERSION,
			true
		);

		wp_localize_script(
			'lib-admin',
			'libAdmin',
			array(
				'links'         => LIB_Settings::get_links(),
				'mediaTitle'    => __( 'Select Profile Image', 'link-in-bio' ),
				'mediaButton'   => __( 'Use this image', 'link-in-bio' ),
				'removeConfirm' => __( 'Remove this link?', 'link-in-bio' ),
				'nonce'         => wp_create_nonce( 'lib-admin' ),
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
			esc_url( admin_url( 'options-general.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( 'Settings', 'link-in-bio' )
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'link-in-bio' ) );
		}

		$s = LIB_Settings::get();
		?>
		<div class="wrap lib-admin-wrap">
			<h1><?php esc_html_e( 'Link in Bio', 'link-in-bio' ); ?></h1>

			<?php settings_errors( LIB_Settings::OPTION_SETTINGS ); ?>

			<p class="lib-shortcode-tip">
				<?php
				printf(
					/* translators: %s: shortcode */
					esc_html__( 'Use the shortcode %s to embed your Link in Bio page anywhere.', 'link-in-bio' ),
					'<code>[link_in_bio]</code>'
				);
				?>
			</p>

			<form method="post" action="options.php" novalidate>
				<?php settings_fields( self::SETTINGS_GROUP ); ?>

				<!-- ── Profile ──────────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Profile', 'link-in-bio' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="lib-profile-name"><?php esc_html_e( 'Name', 'link-in-bio' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									id="lib-profile-name"
									name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[profile_name]"
									value="<?php echo esc_attr( $s['profile_name'] ); ?>"
									class="regular-text"
									autocomplete="name"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-profile-bio"><?php esc_html_e( 'Bio / Tagline', 'link-in-bio' ); ?></label>
							</th>
							<td>
								<textarea
									id="lib-profile-bio"
									name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[profile_bio]"
									rows="3"
									class="large-text"
								><?php echo esc_textarea( $s['profile_bio'] ); ?></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-profile-image"><?php esc_html_e( 'Profile Image URL', 'link-in-bio' ); ?></label>
							</th>
							<td>
								<div class="lib-image-field">
									<input
										type="url"
										id="lib-profile-image"
										name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[profile_image]"
										value="<?php echo esc_url( $s['profile_image'] ); ?>"
										class="regular-text"
										autocomplete="off"
									/>
									<button type="button" id="lib-upload-image" class="button">
										<?php esc_html_e( 'Select Image', 'link-in-bio' ); ?>
									</button>
									<button type="button" id="lib-remove-image" class="button button-link-delete<?php echo empty( $s['profile_image'] ) ? ' hidden' : ''; ?>">
										<?php esc_html_e( 'Remove', 'link-in-bio' ); ?>
									</button>
								</div>
								<?php if ( ! empty( $s['profile_image'] ) ) : ?>
									<div class="lib-image-preview-wrap">
										<img
											id="lib-image-preview"
											src="<?php echo esc_url( $s['profile_image'] ); ?>"
											alt="<?php esc_attr_e( 'Profile image preview', 'link-in-bio' ); ?>"
											width="80"
											height="80"
										/>
									</div>
								<?php else : ?>
									<div class="lib-image-preview-wrap hidden">
										<img id="lib-image-preview" src="" alt="<?php esc_attr_e( 'Profile image preview', 'link-in-bio' ); ?>" width="80" height="80" />
									</div>
								<?php endif; ?>
							</td>
						</tr>
					</table>
				</div>

				<!-- ── Appearance ───────────────────────────────────── -->
				<div class="lib-section">
					<h2 class="lib-section-title"><?php esc_html_e( 'Appearance', 'link-in-bio' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php esc_html_e( 'Background', 'link-in-bio' ); ?></th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><?php esc_html_e( 'Background type', 'link-in-bio' ); ?></legend>
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[background_type]"
											value="gradient"
											<?php checked( $s['background_type'], 'gradient' ); ?>
											class="lib-bg-type-radio"
										/>
										<?php esc_html_e( 'Gradient', 'link-in-bio' ); ?>
									</label>
									&nbsp;&nbsp;
									<label>
										<input
											type="radio"
											name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[background_type]"
											value="solid"
											<?php checked( $s['background_type'], 'solid' ); ?>
											class="lib-bg-type-radio"
										/>
										<?php esc_html_e( 'Solid color', 'link-in-bio' ); ?>
									</label>
								</fieldset>

								<div id="lib-bg-gradient" class="lib-color-group<?php echo 'solid' === $s['background_type'] ? ' hidden' : ''; ?>">
									<label for="lib-gradient-start"><?php esc_html_e( 'Gradient Start', 'link-in-bio' ); ?></label>
									<input
										type="text"
										id="lib-gradient-start"
										name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[gradient_start]"
										value="<?php echo esc_attr( $s['gradient_start'] ); ?>"
										class="lib-color-picker"
										data-default-color="#1a1a2e"
									/>
									<label for="lib-gradient-end"><?php esc_html_e( 'Gradient End', 'link-in-bio' ); ?></label>
									<input
										type="text"
										id="lib-gradient-end"
										name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[gradient_end]"
										value="<?php echo esc_attr( $s['gradient_end'] ); ?>"
										class="lib-color-picker"
										data-default-color="#16213e"
									/>
								</div>

								<div id="lib-bg-solid" class="lib-color-group<?php echo 'gradient' === $s['background_type'] ? ' hidden' : ''; ?>">
									<label for="lib-bg-color"><?php esc_html_e( 'Background Color', 'link-in-bio' ); ?></label>
									<input
										type="text"
										id="lib-bg-color"
										name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[background_color]"
										value="<?php echo esc_attr( $s['background_color'] ); ?>"
										class="lib-color-picker"
										data-default-color="#1a1a2e"
									/>
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="lib-text-color"><?php esc_html_e( 'Profile Text Color', 'link-in-bio' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									id="lib-text-color"
									name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[profile_text_color]"
									value="<?php echo esc_attr( $s['profile_text_color'] ); ?>"
									class="lib-color-picker"
									data-default-color="#ffffff"
								/>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Link Buttons', 'link-in-bio' ); ?></th>
							<td>
								<div class="lib-color-group">
									<label for="lib-btn-bg"><?php esc_html_e( 'Button Background', 'link-in-bio' ); ?></label>
									<input
										type="text"
										id="lib-btn-bg"
										name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[button_bg_color]"
										value="<?php echo esc_attr( $s['button_bg_color'] ); ?>"
										class="lib-color-picker"
										data-default-color="#ffffff"
									/>
									<label for="lib-btn-text"><?php esc_html_e( 'Button Text', 'link-in-bio' ); ?></label>
									<input
										type="text"
										id="lib-btn-text"
										name="<?php echo esc_attr( LIB_Settings::OPTION_SETTINGS ); ?>[button_text_color]"
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
					<h2 class="lib-section-title"><?php esc_html_e( 'Links', 'link-in-bio' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Drag rows to reorder. Uncheck "Active" to hide a link without deleting it.', 'link-in-bio' ); ?>
					</p>

					<div
						id="lib-links-list"
						class="lib-links-list"
						role="list"
						aria-label="<?php esc_attr_e( 'Manage links', 'link-in-bio' ); ?>"
					>
						<!-- Populated by admin.js from libAdmin.links -->
					</div>

					<button type="button" id="lib-add-link" class="button button-secondary lib-add-link-btn">
						<span aria-hidden="true">+</span>
						<?php esc_html_e( 'Add Link', 'link-in-bio' ); ?>
					</button>

					<!-- Hidden input: JS serializes link rows to JSON here before submit -->
					<input
						type="hidden"
						id="lib-links-json"
						name="<?php echo esc_attr( LIB_Settings::OPTION_LINKS ); ?>"
						value="<?php echo esc_attr( wp_json_encode( LIB_Settings::get_links() ) ); ?>"
					/>
				</div>

				<?php submit_button( __( 'Save Settings', 'link-in-bio' ) ); ?>
			</form>
		</div>
		<?php
	}
}
