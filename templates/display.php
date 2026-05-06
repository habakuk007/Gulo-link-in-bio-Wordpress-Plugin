<?php
/**
 * Frontend display partial — profile, links, and footer.
 *
 * Included from templates/page-gulo-link-in-bio.php, which sets:
 *   $gulo_settings   array   Plugin settings (profile, colors, etc.)
 *   $gulo_links      array   All link items [{title, url, active}]
 *
 * @package GuloLinkInBio
 */

defined( 'ABSPATH' ) || exit;

$gulo_active_links = array_filter( $gulo_links, static fn( array $gulo_link ) => ! empty( $gulo_link['active'] ) );

// CSS class added to container when glass button style is selected.
$gulo_container_class = 'glass' === $gulo_settings['button_style'] ? ' lib-btn-glass' : '';
?>
<div class="lib-container<?php echo esc_attr( $gulo_container_class ); ?>">

	<!-- Skip navigation for keyboard and assistive-technology users -->
	<?php if ( ! empty( $gulo_active_links ) ) : ?>
		<a class="lib-skip-link" href="#lib-links">
			<?php esc_html_e( 'Skip to links', 'gulo-link-in-bio' ); ?>
		</a>
	<?php endif; ?>

	<!-- ── Profile ────────────────────────────────────────────── -->
	<section class="lib-profile" aria-label="<?php esc_attr_e( 'Profile', 'gulo-link-in-bio' ); ?>">

		<?php if ( ! empty( $gulo_settings['profile_image'] ) ) : ?>
			<div class="lib-avatar">
				<img
					src="<?php echo esc_url( $gulo_settings['profile_image'] ); ?>"
					alt="
					<?php
					printf(
						/* translators: %s: profile name */
						esc_attr__( 'Profile photo of %s', 'gulo-link-in-bio' ),
						esc_attr( $gulo_settings['profile_name'] )
					);
					?>
					"
					width="96"
					height="96"
					loading="eager"
					decoding="async"
				/>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $gulo_settings['profile_name'] ) ) : ?>
			<h1 class="lib-name">
				<?php echo esc_html( $gulo_settings['profile_name'] ); ?>
			</h1>
		<?php endif; ?>

		<?php if ( ! empty( $gulo_settings['profile_bio'] ) ) : ?>
			<p class="lib-bio">
				<?php echo esc_html( $gulo_settings['profile_bio'] ); ?>
			</p>
		<?php endif; ?>

	</section>

	<!-- ── Links ──────────────────────────────────────────────── -->
	<?php if ( ! empty( $gulo_active_links ) ) : ?>
		<nav
			class="lib-links"
			id="lib-links"
			aria-label="<?php esc_attr_e( 'Profile links', 'gulo-link-in-bio' ); ?>"
			tabindex="-1"
		>
			<ul class="lib-links-list" role="list">
				<?php
				$gulo_index = 0;
				foreach ( $gulo_active_links as $gulo_link ) :
					?>
					<li
						class="lib-link-item"
						role="listitem"
						style="--lib-item-index:<?php echo esc_attr( (string) $gulo_index ); ?>"
					>
						<a
							href="<?php echo esc_url( $gulo_link['url'] ); ?>"
							class="lib-link-btn"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="
							<?php
							printf(
								/* translators: %s: link title */
								esc_attr__( '%s (opens in new tab)', 'gulo-link-in-bio' ),
								esc_attr( $gulo_link['title'] )
							);
							?>
							"
						>
							<span class="lib-link-title"><?php echo esc_html( $gulo_link['title'] ); ?></span>
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
					<?php
					++$gulo_index;
				endforeach;
				?>
			</ul>
		</nav>
	<?php endif; ?>

	<!-- ── Footer ─────────────────────────────────────────────── -->
	<footer class="lib-footer">

		<?php if ( ! empty( $gulo_settings['imprint_url'] ) || ! empty( $gulo_settings['privacy_url'] ) ) : ?>
			<p class="lib-footer-text lib-footer-legal">
				<?php if ( ! empty( $gulo_settings['imprint_url'] ) ) : ?>
					<a
						href="<?php echo esc_url( $gulo_settings['imprint_url'] ); ?>"
						class="lib-footer-link"
					><?php esc_html_e( 'Imprint', 'gulo-link-in-bio' ); ?></a>
				<?php endif; ?>
				<?php if ( ! empty( $gulo_settings['imprint_url'] ) && ! empty( $gulo_settings['privacy_url'] ) ) : ?>
					<span aria-hidden="true"> · </span>
				<?php endif; ?>
				<?php if ( ! empty( $gulo_settings['privacy_url'] ) ) : ?>
					<a
						href="<?php echo esc_url( $gulo_settings['privacy_url'] ); ?>"
						class="lib-footer-link"
					><?php esc_html_e( 'Privacy Policy', 'gulo-link-in-bio' ); ?></a>
				<?php endif; ?>
			</p>
		<?php endif; ?>

		<p class="lib-footer-text">
			<?php
			printf(
				/* translators: %s: site name as a link */
				esc_html__( 'Powered by %s', 'gulo-link-in-bio' ),
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
