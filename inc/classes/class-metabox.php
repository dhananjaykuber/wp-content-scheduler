<?php
/**
 * Register the metabox.
 *
 * @package WP_Content_Scheduler
 */

namespace WP_Content_Scheduler\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Metabox Class.
 */
class Metabox {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_metabox_css' ) );
	}

	/**
	 * Add Metabox.
	 */
	public function add_metabox() {
		$enabled_post_types = get_option( 'wp_content_scheduler_options' );

		if ( ! is_array( $enabled_post_types ) || empty( $enabled_post_types['post_types'] ) ) {
			return;
		}

		add_meta_box(
			'content_scheduler_metabox',
			__( 'Content Scheduler', 'content-scheduler' ),
			array( $this, 'render_metabox' ),
			$enabled_post_types['post_types'],
			'side',
			'high'
		);
	}

	/**
	 * Render Metabox.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox( $post ) {
		wp_nonce_field( 'content_scheduler_meta_box', 'content_scheduler_meta_box_nonce' );

		$scheduled_date   = get_post_meta( $post->ID, '_content_scheduled_date', true );
		$scheduled_time   = get_post_meta( $post->ID, '_content_scheduled_time', true );
		$scheduled_status = get_post_meta( $post->ID, '_content_scheduled_status', true );

		?>

		<div class="content-scheduler-metabox">
			<label for="content_scheduler_date"><?php esc_html_e( 'Scheduled Date:', 'content-scheduler' ); ?></label>
			<input type="date" id="content_scheduler_date" name="content_scheduler_date" value="<?php echo esc_attr( $scheduled_date ); ?>">
		</div>
		<div class="content-scheduler-metabox">
			<label for="content_scheduler_time"><?php esc_html_e( 'Scheduled Time:', 'content-scheduler' ); ?></label>
			<input type="time" id="content_scheduler_time" name="content_scheduler_time" value="<?php echo esc_attr( $scheduled_time ); ?>">
		</div>
		<div class="content-scheduler-metabox">
			<label for="content_scheduler_status"><?php esc_html_e( 'Scheduled Status:', 'content-scheduler' ); ?></label>
			<select id="content_scheduler_status" name="content_scheduler_status">
				<option value="draft" <?php selected( $scheduled_status, 'draft' ); ?>><?php esc_html_e( 'Draft', 'content-scheduler' ); ?></option>
				<option value="publish" <?php selected( $scheduled_status, 'publish' ); ?>><?php esc_html_e( 'Publish', 'content-scheduler' ); ?></option>
				<option value="private" <?php selected( $scheduled_status, 'private' ); ?>><?php esc_html_e( 'Private', 'content-scheduler' ); ?></option>
				<option value="trash" <?php selected( $scheduled_status, 'trash' ); ?>><?php esc_html_e( 'Trash', 'content-scheduler' ); ?></option>

			</select>
		</div>
		
		<?php
	}

	/**
	 * Save Metabox.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_metabox( $post_id ) {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_POST['content_scheduler_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['content_scheduler_meta_box_nonce'], 'content_scheduler_meta_box' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['content_scheduler_date'] ) ) {
			update_post_meta( $post_id, '_content_scheduled_date', sanitize_text_field( wp_unslash( $_POST['content_scheduler_date'] ) ) );
		}

		if ( isset( $_POST['content_scheduler_time'] ) ) {
			update_post_meta( $post_id, '_content_scheduled_time', sanitize_text_field( wp_unslash( $_POST['content_scheduler_time'] ) ) );
		}

		if ( isset( $_POST['content_scheduler_status'] ) ) {
			update_post_meta( $post_id, '_content_scheduled_status', sanitize_text_field( wp_unslash( $_POST['content_scheduler_status'] ) ) );
		}
	}

	/**
	 * Enqueue Metabox CSS.
	 */
	public function enqueue_metabox_css() {
		wp_enqueue_style( 'content-scheduler-metabox', WP_CONTENT_SCHEDULER_PLUGIN_URL . '/assets/metabox.css', array(), WP_CONTENT_SCHEDULER_VERSION );
	}
}
