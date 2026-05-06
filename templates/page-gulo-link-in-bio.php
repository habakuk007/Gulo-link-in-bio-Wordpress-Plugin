<?php
/**
 * Gulo Link-in-Bio — full-page site template.
 *
 * Selected from the Page Attributes panel in the Block Editor or Classic Editor.
 * Bypasses the active theme entirely and renders the Gulo Link-in-Bio profile page.
 *
 * @package GuloLinkInBio
 */

defined( 'ABSPATH' ) || exit;

$gulo_settings = GULO_Settings::get();
$gulo_links    = GULO_Settings::get_links();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'lib-page' ); ?>>
<?php wp_body_open(); ?>

<?php require __DIR__ . '/display.php'; ?>

<?php wp_footer(); ?>
</body>
</html>
