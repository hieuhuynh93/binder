<?php
/**
 * View Binder Document List
 *
 * If you wish to override this file, you can do so by creating a version in your
 * theme, and using the `MKDO_BINDER_PREFIX . '_view_template_folder` hook
 * to set the right location.
 *
 * @package mkdo\binder
 */

/**
 * Variables
 *
 * The following variables can be used in this view.
 */
$attr = wp_parse_args(
	(array) $attr,
	array(
		'version'          => '',
		'alternative_text' => '',
		'file_size'        => '',
		'date'             => '',
		'extension'        => '',
		'show_version'     => '',
		'icon'             => '',
		'image'            => '',
		'document_reader'  => '',
		'sort_by'          => '',
		'sort_order'       => '',
	)
);

$documents                 = $document_posts; // The main list of documents.
$show_file_size            = 'true' === $attr['file_size'] ? true : false;
$show_file_date            = 'true' === $attr['date'] ? true : false;
$show_file_extension       = 'true' === $attr['extension'] ? true : false;
$show_file_icon            = 'true' === $attr['icon'] ? true : false;
$show_file_image           = 'true' === $attr['image'] ? true : false; // Only used on card-list and card-grid views.
$show_document_reader_text = 'true' === $attr['document_reader'] ? true : false; // The text is set in the plugin settings.
$sort_by                   = esc_attr( $attr['sort_by'] ); // Can be: alphabet, date or size.
$sort_order                = esc_attr( $attr['sort_order'] ); // Can be: ascending or descending.

$document_list = array();

foreach ( $documents as $document_post ) {
	$document = \mkdo\binder\Binder::get_latest_document_by_post_id( $document_post->ID );
	if ( ! empty( $version ) ) {
		$document = \mkdo\binder\Binder::get_document_by_version( $document_post->ID, $version );
	}
	$name     = $document_post->post_title;
	$link     = get_the_permalink( $document_post->ID );
	$excerpt  = get_the_excerpt( $document );
	$size     = $document->size;
	$image    = $document->get_thumbnail( $document->binder_id, 'thumbnail' );
	$uploaded = $document->upload_date;
	$uploads  = wp_upload_dir();
	$type     = '';
	$icon     = '';
	$term     = wp_get_object_terms( $document_post->ID, 'binder_type' );

	if ( ! empty( $term ) ) {
		$term = $term[0];
		$type = $term->name;
		$icon = get_term_meta( $term->term_id, MKDO_BINDER_PREFIX . '_type_icon', true );
	}

	// Term fallback.
	if ( empty( $term ) ) {
		$type = $document->type;
		$term = get_term_by( 'slug', $type, 'binder_type' );
		if ( ! empty( $term ) ) {
			$term = $term;
			$type = $term->name;
			$icon = get_term_meta( $term->term_id, MKDO_BINDER_PREFIX . '_type_icon', true );
		}
	}

	// If there is no type, there probably hasn't been a document attached.
	if ( ! empty( $type ) ) {
		$document_list[] = array(
			'post_id' => $document_post->ID,
			'name'    => $name,
			'link'    => $link,
			'excerpt' => $excerpt,
			'size'    => $size,
			'icon'    => $icon,
			'type'    => $type,
			'date'    => $uploaded,
		);
	}
}

// Do the sorting.
if ( 'alphabet' === $sort_by ) {
	$sort_by = 'name';
}

if ( 'ascending' === $sort_order ) {
	usort( $document_list, function ( $a, $b ) use ( $sort_by ) {
		return $a[ $sort_by ] >= $b[ $sort_by ];
	} );
} else {
	usort( $document_list, function ( $a, $b ) use ( $sort_by ) {
		return $b[ $sort_by ] >= $a[ $sort_by ];
	} );
}

/**
 * Output
 *
 * Here is the HTML output, this can be styled however.
 * Do not alter this file, instead duplicate it into your theme.
 */
if ( ! empty( $document_list ) ) {
	?>
	<ul class="c-binder-document-list">
		<?php
		foreach ( $document_list as $document ) {
			$document_class = 'binder-link';
			$document_class = apply_filters( MKDO_BINDER_PREFIX . '_document_link_class', $document_class, $document['post_id'] );
			$meta      = '';
			$date_meta = '';
			if ( $show_file_date ) {
				$date = $document['date'];
				$date = DateTime::createFromFormat( 'Y-m-d H:i:s', $date );
				$date_meta = ' - ' . date_format( $date, 'j F Y' ) . ' ';
			}
			if ( $show_file_extension || $show_file_icon || $show_file_size ) {
				$meta .= ' ( ';
				if ( $show_file_icon ) {
					$meta .= '<i class="fa fa-' . esc_attr( $document['icon'] ) . '"></i> ';
				}
				if ( $show_file_extension ) {
					$meta .= esc_html( $document['type'] ) . ' ';
				}
				if ( $show_file_size ) {
					if ( ' ( ' !== $meta ) {
						$meta .= ' - ';
					}
					$meta .= esc_html( $document['size'] ) . ' ';
				}
				$meta .= ')';
				$meta = str_replace( '( ', '(', $meta );
				$meta = str_replace( ' )', ')', $meta );
			}
			?>
			<li class="c-binder-document-list__item">
				<a href="<?php echo esc_url( $document['link'] );?>" class="<?php echo esc_attr( $document_class );?>">
					<?php echo esc_html( $document['name'] );?>
					<?php
						echo wp_kses(
							$date_meta . $meta,
							array(
							    'i' => array(
							        'class' => array(),
							    ),
							)
						);
					?>
				</a>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}
