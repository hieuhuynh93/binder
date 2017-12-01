<?php
/**
 * Class Excerpt
 *
 * @package mkdo\binder
 */

namespace mkdo\binder;

/**
 * Make the excerpt into the summary.
 */
class Meta_Binder_Excerpt {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function run() {
		add_action( 'init', array( $this, 'page_excerpt' ) );
		add_action( 'admin_menu', array( $this, 'remove_meta_boxes' ) );
		add_action( 'cmb2_admin_init', array( $this, 'register_meta_boxes' ), 99 );
	}

	/**
	 * Enable Excerpt for pages
	 */
	public function page_excerpt() {
		add_post_type_support( 'page', 'excerpt' );
	}

	/**
	 * Remove meta boxes
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'binder', 'normal' );
	}

	/**
	 * Register Meta Boxes
	 */
	public function register_meta_boxes() {

		$cmb = new_cmb2_box(
			array(
				'id'            => 'excerpt_override',
				'title'         => __( 'Summary', 'binder' ),
				'object_types'  => array(
					'binder',
				),
				'context'       => defined( 'MKDO_MBAE_ROOT' ) ? 'primary' : 'normal',
				'priority'      => 'high',
				'show_names'    => false,
			)
		);

		$field1 = $cmb->add_field( array(
			'id'               => 'excerpt',
			'type'             => 'wysiwyg',
			'options'          => array(
				'media_buttons' => false,
				'quicktags'     => array(
					'buttons' => 'strong,em,close',
				),
				'tinymce'       => array(
					'toolbar1' => 'bold, italic',
				),
			),
		) );
	}
}
