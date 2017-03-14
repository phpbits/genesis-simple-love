<?php
/**
 * Widget API: WP_Widget_Genesis_Simple_Love class
 *
 */

/**
 * Core class used to implement a Rich Text widget.
 *
 * @since 1.0
 *
 * @see WP_Widget
 */
if( !class_exists( 'WP_Widget_Genesis_Simple_Love' ) ):
	class WP_Widget_Genesis_Simple_Love extends WP_Widget {

		/**
		 * Sets up a new Text widget instance.
		 *
		 * @since 1.0
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname' => 'widget_genesis_simple_love',
				'description' => __( 'Your site\'s most loved posts or post types', 'genesis-simple-love' ),
				'customize_selective_refresh' => true,
			);
			
			parent::__construct( 'genesis_simple_love', __( 'Genesis - Simple Love' ), $widget_ops );
		}

		/**
		 * Outputs the content for the current Text widget instance.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param array $args     Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Text widget instance.
		 */
		public function widget( $args, $instance ) {

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'easy_rich_text_widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$widget_text = ! empty( $instance['text'] ) ? $instance['text'] : '';

			/**
			 * Filters the content of the Text widget.
			 *
			 * @since 1.0
			 * @since 1.0 Added the `$this` parameter.
			 *
			 * @param string         						$widget_text The widget content.
			 * @param array          						$instance    Array of settings for the current widget.
			 * @param WP_Widget_Genesis_Simple_Love $this        Current Text widget instance.
			 */
			$text = apply_filters( 'easy_rich_text_widget_text', $widget_text, $instance, $this );

			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			} ?>
				<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Handles updating settings for the current Text widget instance.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param array $new_instance New settings for this instance as input by the user via
		 *                            WP_Widget::form().
		 * @param array $old_instance Old settings for this instance.
		 * @return array Settings to save or bool false to cancel saving.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = sanitize_text_field( $new_instance['title'] );
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['text'] = $new_instance['text'];
			} else {
				$instance['text'] = wp_kses_post( $new_instance['text'] );
			}
			$instance['filter'] = ! empty( $new_instance['filter'] );
			return $instance;
		}

		/**
		 * Outputs the Text widget settings form.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @param array $instance Current settings.
		 */
		public function form( $instance ) {
			$instance 	= wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
			$filter 	= isset( $instance['filter'] ) ? $instance['filter'] : 0;
			$title 		= sanitize_text_field( $instance['title'] );
			?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'genesis-simple-love' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>


			<?php
		}
	}
endif;

if( !function_exists( 'register_genesis_simple_love_widget' ) ):
	function register_genesis_simple_love_widget() {
	    register_widget( 'WP_Widget_Genesis_Simple_Love' );
	}
	add_action( 'widgets_init', 'register_genesis_simple_love_widget' );
endif;
