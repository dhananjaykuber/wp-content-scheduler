<?php
/**
 * Add Settings Option.
 *
 * @package WP_Content_Scheduler
 */

namespace WP_Content_Scheduler\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Class.
 */
class Settings {

	/**
	 * Options.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		$this->options = get_option( 'wp_content_scheduler_options' );
	}

	/**
	 * Add Admin Menu.
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'WP Content Scheduler', 'content-scheduler' ),
			__( 'WP Content Scheduler', 'content-scheduler' ),
			'manage_options',
			'wp-content-scheduler',
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Create Admin Page.
	 */
	public function admin_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'WP Content Scheduler Settings', 'content-scheduler' ); ?></h2>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'wp_content_scheduler_option_group' );
				do_settings_sections( 'wp-content-scheduler' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Settings.
	 */
	public function register_settings() {
		register_setting( 'wp_content_scheduler_option_group', 'wp_content_scheduler_options', array( $this, 'sanitize' ) );

		add_settings_section(
			'wp_content_scheduler_setting_section',
			__( 'WP Content Scheduler Settings', 'content-scheduler' ),
			array( $this, 'section_info' ),
			'wp-content-scheduler'
		);

		add_settings_field(
			'post_types',
			__( 'Post Types', 'content-scheduler' ),
			array( $this, 'post_types_callback' ),
			'wp-content-scheduler',
			'wp_content_scheduler_setting_section'
		);
	}

	/**
	 * Sanitize.
	 *
	 * @param array $input Input.
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['post_types'] ) ) {
			$new_input['post_types'] = array_map( 'sanitize_text_field', $input['post_types'] );
		}

		return $new_input;
	}

	/**
	 * Section Info.
	 */
	public function section_info() {
		echo '<p>' . esc_html__( 'Select post types where you want to add the Content Scheduler metabox.', 'content-scheduler' ) . '</p>';
	}

	/**
	 * Post Types Callback.
	 */
	public function post_types_callback() {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		foreach ( $post_types as $post_type ) {
			if ( 'attachment' === $post_type->name ) {
				continue;
			}

			$checked = isset( $this->options['post_types'] ) && in_array( $post_type->name, $this->options['post_types'], true ) ? 'checked' : '';

			echo '<label>';
			echo '<input type="checkbox" name="wp_content_scheduler_options[post_types][]" value="' . esc_attr( $post_type->name ) . '" ' . esc_attr( $checked ) . '> ';
			echo esc_html( $post_type->label );
			echo '</label><br>';
		}
	}
}