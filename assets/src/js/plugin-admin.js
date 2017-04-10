( function( $ ) {
	'use strict';

	/**
	 * Taxonomy Icons
	 *
	 * Add icons to taxonomy meta
	 */
	function format_select2_icon( icon ) {
	    var original = icon.element;
	    return '<i class="fa fa-' + $( original ).val() + '"></i> - ' + icon.text;
	}

	$( 'select.fa-select:not(.select2-hidden-accessible), .fa-select select:not(.select2-hidden-accessible)' ).select2( {
	    templateResult: format_select2_icon,
	    templateSelection: format_select2_icon,
		escapeMarkup: function( m ) {
			return m;
		}
	} );


	/**
	 * Document management
	 *
	 * Version disabled unless file uploaded
	 */
	$( '#mkdo_binder_draft' ).attr( 'disabled', 'disabled' );
	$( '.meta-box__item:not(.meta-box__item--version-select) #mkdo_binder_version' ).attr( 'disabled', 'disabled' );

	$( '#mkdo_binder_file_upload' ).change( function() {
		var file = $(this).val();
		if ( '' !== file && null !== file && undefined !== file ) {
			$( '#mkdo_binder_draft' ).removeAttr( 'disabled' );
			$( '.meta-box__item:not(.meta-box__item--version-select) #mkdo_binder_version' ).removeAttr( 'disabled' );
		} else {
			$( '#mkdo_binder_draft' ).attr( 'disabled', 'disabled' );
			$( '.meta-box__item:not(.meta-box__item--version-select) #mkdo_binder_version' ).attr( 'disabled', 'disabled' );
		}
	} );

	/**
	 * Document Picker
	 *
	 * Populate the version dropdown
	 */
	$( '[data-js-select2=select2]' ).select2();

	function mkdo_document_list_document_changed() {
		$( '[data-js-select2=select2]' ).select2();
		$( '[data-js-mkdo-document-list-document=document]' ).change();
	}

	if ( typeof wp !== 'undefined' && typeof wp.shortcake !== 'undefined' && typeof wp.shortcake.hooks !== 'undefined' ) {
		wp.shortcake.hooks.addAction( 'shortcode-ui.render_new', mkdo_document_list_document_changed );
		wp.shortcake.hooks.addAction( 'shortcode-ui.render_edit', mkdo_document_list_document_changed );
	}

	$( document ).on( 'change', '[data-js-mkdo-document-list-document=document]', function() {
		var document_id = $(this).val();
		jQuery.ajax( {
			url : binder_admin.ajax_url,
			type : 'post',
			data : {
				action : 'mkdo_binder_get_document_versions',
				document_id : document_id
			},
			success : function( response ) {
				$( '[data-js-mkdo-document-list-version=version]' ).html( response );
			}
		} );
	} );

} )( jQuery );
