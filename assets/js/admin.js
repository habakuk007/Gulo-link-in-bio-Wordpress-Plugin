/**
 * Link in Bio — Admin JavaScript
 *
 * Manages:
 *  - Dynamic links repeater (add / remove / reorder via sortable)
 *  - WordPress media uploader for profile image
 *  - Background-type radio toggle (gradient vs. solid)
 *  - wp-color-picker initialization
 *  - JSON serialization of links into the hidden field before form submit
 *
 * @param {Function} $ jQuery object.
 * @package
 */

( function( $ ) {
	'use strict';

	// ── DOM references ──────────────────────────────────────────────

	const $linksList = $( '#lib-links-list' );
	const $linksJson = $( '#lib-links-json' );
	const $addLinkBtn = $( '#lib-add-link' );
	const $uploadBtn = $( '#lib-upload-image' );
	const $removeImgBtn = $( '#lib-remove-image' );
	const $imageField = $( '#lib-profile-image' );
	const $imagePreview = $( '#lib-image-preview' );
	const $bgTypeRadios = $( '.lib-bg-type-radio' );
	const $bgGradient = $( '#lib-bg-gradient' );
	const $bgSolid = $( '#lib-bg-solid' );

	// ── Color pickers ───────────────────────────────────────────────

	$( '.lib-color-picker' ).wpColorPicker();

	// ── Background type toggle ──────────────────────────────────────

	function updateBgVisibility() {
		const type = $bgTypeRadios.filter( ':checked' ).val();
		if ( 'gradient' === type ) {
			$bgGradient.removeClass( 'hidden' );
			$bgSolid.addClass( 'hidden' );
		} else {
			$bgGradient.addClass( 'hidden' );
			$bgSolid.removeClass( 'hidden' );
		}
	}

	$bgTypeRadios.on( 'change', updateBgVisibility );
	updateBgVisibility();

	// ── Media uploader ──────────────────────────────────────────────

	let mediaFrame;

	$uploadBtn.on( 'click', function( e ) {
		e.preventDefault();

		if ( mediaFrame ) {
			mediaFrame.open();
			return;
		}

		mediaFrame = wp.media( {
			title: libAdmin.mediaTitle,
			button: { text: libAdmin.mediaButton },
			multiple: false,
			library: { type: 'image' },
		} );

		mediaFrame.on( 'select', function() {
			const attachment = mediaFrame
				.state()
				.get( 'selection' )
				.first()
				.toJSON();
			$imageField.val( attachment.url );
			$imagePreview.attr( 'src', attachment.url );
			$imagePreview.closest( '.lib-image-preview-wrap' ).removeClass( 'hidden' );
			$removeImgBtn.removeClass( 'hidden' );
		} );

		mediaFrame.open();
	} );

	$removeImgBtn.on( 'click', function( e ) {
		e.preventDefault();
		$imageField.val( '' );
		$imagePreview.attr( 'src', '' );
		$imagePreview.closest( '.lib-image-preview-wrap' ).addClass( 'hidden' );
		$( this ).addClass( 'hidden' );
	} );

	// ── Link row template ───────────────────────────────────────────

	/**
	 * Builds a link-row jQuery element.
	 *
	 * @param {string}  title  Link title.
	 * @param {string}  url    Link URL.
	 * @param {boolean} active Whether the link is active.
	 * @return {jQuery} The constructed row element.
	 */
	function buildLinkRow( title, url, active ) {
		const $row = $( '<div>', {
			class: 'lib-link-row',
			role: 'listitem',
		} );

		const $handle = $( '<span>', {
			class: 'lib-drag-handle',
			'aria-hidden': 'true',
			title: 'Drag to reorder',
			html: '&#9776;',
		} );

		const $fields = $( '<div>', { class: 'lib-link-fields' } );

		const $titleInput = $( '<input>', {
			type: 'text',
			class: 'lib-link-title',
			placeholder: 'Link Title',
			value: title || '',
			'aria-label': 'Link title',
		} );

		const $urlInput = $( '<input>', {
			type: 'url',
			class: 'lib-link-url',
			placeholder: 'https://example.com',
			value: url || '',
			'aria-label': 'Link URL',
		} );

		const $activeLabel = $( '<label>' );
		const $activeCheck = $( '<input>', {
			type: 'checkbox',
			class: 'lib-link-active',
			checked: false !== active,
		} );
		$activeLabel.append( $activeCheck ).append( ' Active' );

		$fields.append( $titleInput, $urlInput, $activeLabel );

		const $removeBtn = $( '<button>', {
			type: 'button',
			class: 'lib-remove-link button button-link-delete',
			html: 'Remove',
			'aria-label': 'Remove link',
		} );

		$row.append( $handle, $fields, $removeBtn );
		return $row;
	}

	// ── Render initial links ────────────────────────────────────────

	if ( libAdmin.links && libAdmin.links.length ) {
		$.each( libAdmin.links, function( i, link ) {
			$linksList.append( buildLinkRow( link.title, link.url, link.active ) );
		} );
	}

	// ── Add link ────────────────────────────────────────────────────

	$addLinkBtn.on( 'click', function() {
		const $row = buildLinkRow( '', '', true );
		$linksList.append( $row );
		$row.find( '.lib-link-title' ).trigger( 'focus' );
	} );

	// ── Remove link (event delegation) ─────────────────────────────

	$linksList.on( 'click', '.lib-remove-link', function() {
		$( this ).closest( '.lib-link-row' ).remove();
	} );

	// ── Drag-to-sort ────────────────────────────────────────────────

	$linksList.sortable( {
		handle: '.lib-drag-handle',
		placeholder: 'lib-link-row ui-sortable-placeholder',
		tolerance: 'pointer',
		axis: 'y',
	} );

	// ── Serialize links to JSON before submit ───────────────────────

	$linksList.closest( 'form' ).on( 'submit', function() {
		const links = [];

		$linksList.find( '.lib-link-row' ).each( function() {
			const $r = $( this );
			links.push( {
				title: $r.find( '.lib-link-title' ).val().trim(),
				url: $r.find( '.lib-link-url' ).val().trim(),
				active: $r.find( '.lib-link-active' ).is( ':checked' ),
			} );
		} );

		$linksJson.val( JSON.stringify( links ) );
	} );
}( jQuery ) );
