<?php
/**
 * Register the cron.
 *
 * @package WP_Content_Scheduler
 */

namespace WP_Content_Scheduler\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cron Class.
 */
class Cron {

	/**
	 * Batch Size.
	 *
	 * @var int
	 */
	private $batch_size = 100;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'schedule_cron' ) );
		add_action( 'wp_content_scheduler_cron', array( $this, 'scheduled_posts' ) );
	}

	/**
	 * Schedule Cron.
	 */
	public function schedule_cron() {
		if ( ! wp_next_scheduled( 'wp_content_scheduler_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'wp_content_scheduler_cron' );
		}
	}

	/**
	 * Change Status of Scheduled Posts.
	 */
	public function scheduled_posts() {
		$offset             = 0;
		$enabled_post_types = get_option( 'wp_content_scheduler_options' );

		if ( ! is_array( $enabled_post_types ) || empty( $enabled_post_types['post_types'] ) ) {
			return;
		}

		do {
			remove_all_filters( 'posts_orderby' );

			$args = array(
				'post_type'              => $enabled_post_types['post_types'],
				'posts_per_page'         => $this->batch_size,
				'post_status'            => 'any',
                // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'meta_query'             => array(
					array(
						'key'     => '_content_scheduled_date',
						'compare' => 'EXISTS',
					),
				),
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'no_found_rows'          => true,
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'offset'                 => $offset,
			);

			$scheduled_posts = new \WP_Query( $args );
			$post_ids        = $scheduled_posts->posts;

			if ( ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					$this->process_scheduled_post( $post_id );
				}

				$offset += count( $post_ids );
			}

			wp_reset_postdata();

		} while ( ! empty( $post_ids ) );
	}

	/**
	 * Process Scheduled Post.
	 *
	 * @param int $post_id The post ID.
	 */
	private function process_scheduled_post( $post_id ) {
		$scheduled_date   = get_post_meta( $post_id, '_content_scheduled_date', true );
		$scheduled_time   = get_post_meta( $post_id, '_content_scheduled_time', true );
		$scheduled_status = get_post_meta( $post_id, '_content_scheduled_status', true );

		$scheduled_datetime = $scheduled_date . ' ' . $scheduled_time;
		$current_datetime   = current_time( 'mysql' );

		if ( $current_datetime >= $scheduled_datetime ) {
			if ( 'trash' === $scheduled_status ) {
				wp_delete_post( $post_id, true );
			} else {

				wp_update_post(
					array(
						'ID'          => $post_id,
						'post_status' => $scheduled_status,
					)
				);

				delete_post_meta( $post_id, '_content_scheduled_date' );
				delete_post_meta( $post_id, '_content_scheduled_time' );
				delete_post_meta( $post_id, '_content_scheduled_status' );
			}
		}
	}
}
