<?php
/**
 * Add Expiry Date column to posts list.
 *
 * @package WP_Content_Scheduler
 */

namespace WP_Content_Scheduler\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Posts Column Class.
 */
class Posts_Column {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$enabled_post_types = get_option( 'wp_content_scheduler_options' );

		if ( ! is_array( $enabled_post_types ) || empty( $enabled_post_types['post_types'] ) ) {
			return;
		}

		foreach ( $enabled_post_types['post_types'] as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_expiry_date_column' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'render_expiry_date_column' ), 10, 2 );
			add_filter( "manage_edit-{$post_type}_sortable_columns", array( $this, 'make_expiry_column_sortable' ) );
		}
	}

	/**
	 * Add Expiry Date Column.
	 *
	 * @param array $columns The column names.
	 */
	public function add_expiry_date_column( $columns ) {
		$columns['expiry_date'] = __( 'Expiry Date', 'content-scheduler' );
		return $columns;
	}

	/**
	 * Render Expiry Date Column.
	 *
	 * @param string $column_name The column name.
	 * @param int    $post_id     The post ID.
	 */
	public function render_expiry_date_column( $column_name, $post_id ) {
		if ( 'expiry_date' !== $column_name ) {
			return;
		}

		$post_status = get_post_meta( $post_id, '_content_scheduled_status', true );
		if ( 'trash' !== $post_status ) {
			echo '-';
			return;
		}

		$expiry_date = get_post_meta( $post_id, '_content_scheduled_date', true );
		$expiry_time = get_post_meta( $post_id, '_content_scheduled_time', true );

		if ( ! empty( $expiry_date ) && ! empty( $expiry_time ) ) {
			$expiry_datetime = new \DateTime( $expiry_date . ' ' . $expiry_time );
			echo esc_html( $expiry_datetime->format( 'Y/m/d \a\t g:i a' ) );
		} else {
			echo '-';
		}
	}

	/**
	 * Make Expiry Column Sortable.
	 *
	 * @param array $columns The column names.
	 */
	public function make_expiry_column_sortable( $columns ) {
		$columns['expiry_date'] = 'expiry_date';
		return $columns;
	}
}
