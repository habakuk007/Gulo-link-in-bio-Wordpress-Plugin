<?php
/**
 * Simple Bio Links — full-page site template.
 *
 * Selected from the Page Attributes panel in the Block Editor or Classic Editor.
 * Bypasses the active theme entirely and renders the Simple Bio Links profile page.
 *
 * @package LinkInBio
 */

defined( 'ABSPATH' ) || exit;

$lib_settings = LIB_Settings::get();
$lib_links    = LIB_Settings::get_links();
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
