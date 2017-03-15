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
			$title = apply_filters( 'genesis_simple_love_widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$post_type 	= ! empty( $instance['post_type'] ) ? $instance['post_type'] : 'post';
			$number 	= ! empty( $instance['number'] ) ? $instance['number'] : 5;

			$query_args = array(
					'post_type' 	 => $post_type,
					'posts_per_page' => $number,
					'post_status' 	 => 'publish',
					'orderby'   	 => 'meta_value_num',
					'meta_key' 		 => '_genesis_simple_love_',

			);

			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			$query = new WP_Query( $query_args );

			if( $query->have_posts() ){
				echo '<ul>';
            	while ( $query->have_posts() ) { $query->the_post();
			?>
				<li><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
			<?php }
				echo '</ul>';
				wp_reset_query();
				wp_reset_postdata();
			}else{
				//show recent posts whenever there's no loved posts yet to avoid empty widget
				$query_args['orderby'] 	= 'date';
				$query_args['order'] 	= 'DESC';
				$query_args['meta_key'] = '';
				$query2 = new WP_Query( $query_args );

				if( $query2->have_posts() ){
					echo '<ul>';
	            	while ( $query2->have_posts() ) { $query2->the_post(); ?>
							<li><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
					<?php }
					echo '</ul>';
					wp_reset_query();
					wp_reset_postdata();
				}
			}
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
			$instance 				= $old_instance;
			$instance['title'] 		= sanitize_text_field( $new_instance['title'] );
			$instance['post_type'] 	= sanitize_text_field( $new_instance['post_type'] );
			$instance['number'] 	= sanitize_text_field( $new_instance['number'] );

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
			$instance 	= wp_parse_args( (array) $instance, array( 'title' => '', 'post_type' => 'post', 'number' => '5' ) );
			$filter 	= isset( $instance['filter'] ) ? $instance['filter'] : 0;
			$title 		= sanitize_text_field( $instance['title'] );
			$type 		= sanitize_text_field( $instance['post_type'] );
			$number 	= sanitize_text_field( $instance['number'] );

			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );

			?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'genesis-simple-love' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:', 'genesis-simple-love' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
				<?php if( !empty( $post_types ) ){
					foreach ( $post_types as $post_type ) {
						echo '<option value="'. $post_type .'" '. ( ( $type == $post_type ) ? 'selected="selected"' : '' ) .' >'. $post_type .'</option>';
					}
				}?>
			</select>

			<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'genesis-simple-love' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>" size="3" step="1" min="1" /></p>

			<?php if( !class_exists('PHPBITS_extendedWidgetsDisplay') ):?>
				<div class="genesis-simple-love-widget--after">
					<a href="http://widget-options.com?utm_source=genesis-simple-love-widget" target="_blank" style="display:block;text-decoration:none;color:#31708f;background:#d9edf7;border:1px solid #bcdff1;padding:5px 7px;font-size:12px;margin-bottom:15px;-webkit-border-radius:3px;-moz-border-radius:3px;-ms-border-radius:3px;-o-border-radius:3px;border-radius:3px" ><?php _e( '<strong>Manage your widgets</strong> visibility, styling, alignment, columns, restrictions and more. Click here to learn more. ', 'genesis-simple-love' );?></a>
				</div>
			<?php endif;?>

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
