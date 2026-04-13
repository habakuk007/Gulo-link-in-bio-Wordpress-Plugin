<?php
/**
 * Frontend display template for the [link_in_bio] shortcode.
 *
 * Variables available from LIB_Frontend::render_shortcode():
 *   $settings   array   Plugin settings (profile, colors, etc.)
 *   $links      array   Active link items [{title, url, active}]
 *   $custom_css string  Inline CSS custom-property overrides
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

$active_links = array_filter( $links, static fn( array $link ) => ! empty( $link['active'] ) );
?>
<style><?php echo $custom_css; // Already escaped — contains only controlled property values. ?></style>

<div class="lib-container">

	<!-- Skip navigation for keyboard and assistive-technology users -->
	<?php if ( ! empty( $active_links ) ) : ?>
		<a class="lib-skip-link" href="#lib-links">
			<?php esc_html_e( 'Skip to links', 'link-in-bio' ); ?>
		</a>
	<?php endif; ?>

	<!-- ── Profile ────────────────────────────────────────────── -->
	<section class="lib-profile" aria-label="<?php esc_attr_e( 'Profile', 'link-in-bio' ); ?>">

		<?php if ( ! empty( $settings['profile_image'] ) ) : ?>
			<div class="lib-avatar">
				<img
					src="<?php echo esc_url( $settings['profile_image'] ); ?>"
					alt="<?php
					printf(
						/* translators: %s: profile name */
						esc_attr__( 'Profile photo of %s', 'link-in-bio' ),
						esc_attr( $settings['profile_name'] )
					);
					?>"
					width="96"
					height="96"
					loading="lazy"
					decoding="async"
				/>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $settings['profile_name'] ) ) : ?>
			<h1 class="lib-name">
				<?php echo esc_html( $settings['profile_name'] ); ?>
			</h1>
		<?php endif; ?>

		<?php if ( ! empty( $settings['profile_bio'] ) ) : ?>
			<p class="lib-bio">
				<?php echo esc_html( $settings['profile_bio'] ); ?>
			</p>
		<?php endif; ?>

	</section>

	<!-- ── Links ──────────────────────────────────────────────── -->
	<?php if ( ! empty( $active_links ) ) : ?>
		<nav
			class="lib-links"
			id="lib-links"
			aria-label="<?php esc_attr_e( 'Profile links', 'link-in-bio' ); ?>"
			tabindex="-1"
		>
			<ul class="lib-links-list" role="list">
				<?php foreach ( $active_links as $link ) : ?>
					<li class="lib-link-item" role="listitem">
						<a
							href="<?php echo esc_url( $link['url'] ); ?>"
							class="lib-link-btn"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?php
							printf(
								/* translators: %s: link title */
								esc_attr__( '%s (opens in new tab)', 'link-in-bio' ),
								esc_attr( $link['title'] )
							);
							?>"
						>
							<span class="lib-link-title"><?php echo esc_html( $link['title'] ); ?></span>
							<span class="lib-link-arrow" aria-hidden="true">
								<svg
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 24 24"
									fill="none"
									stroke="currentColor"
									stroke-width="2"
									stroke-linecap="round"
									stroke-linejoin="round"
									width="14"
									height="14"
									focusable="false"
								>
									<path d="M7 17L17 7M17 7H7M17 7v10"/>
								</svg>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<!-- ── Footer ─────────────────────────────────────────────── -->
	<footer class="lib-footer">
		<p class="lib-footer-text">
			<?php
			printf(
				/* translators: %s: site name as a link */
				esc_html__( 'Powered by %s', 'link-in-bio' ),
				sprintf(
					'<a href="%s" class="lib-footer-link">%s</a>',
					esc_url( home_url( '/' ) ),
					esc_html( get_bloginfo( 'name' ) )
				)
			);
			?>
		</p>
	</footer>

</div>
