jQuery( document ).ready( function( $ ) {

    tinymce.PluginManager.add( 'mkdo_document_management_document', function( editor, url ) {
        editor.addButton( 'mkdo_document_management_document', {
            tooltip: 'Insert a document',
            icon: 'format-aside',
            onclick: function() {
				$( '[id="insert-media-button"]' ).click();
				$( '.media-menu .media-menu-item:contains("' + shortcodeUIData.strings.media_frame_menu_insert_label + '")' ).click();
				$( '[data-shortcode="document_management_document"]' ).click();
            }
        } );
    });

} );
