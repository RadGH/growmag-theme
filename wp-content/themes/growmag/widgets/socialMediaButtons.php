<?php

class socialMediaButtonWidget extends WP_Widget
{

	// Register widget with WordPress.
	public function __construct() {
		parent::__construct( 'socialMediaButtonWidget', // Base ID
			'Social Media Buttons', // Name
			array( 'description' => 'Displays social media icons for the networks set under Theme Options &gt; Branding.' ) // Args
		);
	}

	// Front-end display of widget.
	public function widget( $widget, $instance ) {
		extract( $widget );

		$title = !empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$text = !empty( $instance['text'] ) ? wpautop( $instance['text'] ) : '';

		$networks = $instance['network'];
		$layout_name = $instance['layout'];

		if ( empty($networks) ) return;

		echo $widget['before_widget'];
		?>
		<div class="social-media-button-widget">
			<?php if ( $title ) echo $widget['before_title'], esc_html( $title ), $widget['after_title']; ?>

			<?php if ( $text ) echo '<div class="text-widget-content widgettext">', wpautop( $text ), '</div>'; ?>

			<div class="social-icons"><?php ld_social_menu( $layout_name, $networks ); ?></div>
		</div>
		<?php
		echo $widget['after_widget'];

	}

	// Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text'] = $new_instance['text'];

		$instance['network'] = empty($new_instance['network']) ? array() : $new_instance['network'];
		$instance['layout'] = empty($new_instance['layout']) ? array() : $new_instance['layout'];

		return $instance;
	}


	public function form( $instance ) {
		// Retrieve all of our fields from the $instance variable
		$fields = array(
			'title',
			'text',
			'layout',
			'network',
		);

		// Format each field into ID/Name/Value array
		foreach ( $fields as $name ) {
			$fields[$name] = array(
				'id'    => $this->get_field_id( $name ),
				'name'  => $this->get_field_name( $name ),
				'value' => null,
			);

			if ( isset( $instance[$name] ) ) {
				$fields[$name]['value'] = $instance[$name];
			}
		}

		// Display the widget in admin dashboard:
		?>

		<p>
			<label for="<?php echo esc_attr( $fields['title']['id'] ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" type="text"
			       id="<?php echo esc_attr( $fields['title']['id'] ); ?>"
			       name="<?php echo esc_attr( $fields['title']['name'] ); ?>"
			       value="<?php echo esc_attr( $fields['title']['value'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['text']['id'] ); ?>"><?php _e( 'Text:' ); ?></label>
            <textarea class="widefat"
                      id="<?php echo esc_attr( $fields['text']['id'] ); ?>"
                      name="<?php echo esc_attr( $fields['text']['name'] ); ?>"
                      rows="5" cols="80"><?php echo esc_textarea( $fields['text']['value'] ); ?></textarea>
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['network']['id'] ); ?>"><?php _e( 'Networks:' ); ?></label>
		</p>

		<p>
			<?php
			$networks = get_field( 'social_networks', 'options' );

			if ( $networks ) {
				foreach( $networks as $network ) {
					$checked = false;
					if ( $fields['network']['value'] === null ) $checked = true;
					else if ( is_array($fields['network']['value']) && in_array( $network['name'], $fields['network']['value'] ) ) $checked = true;
					?>
					<label>
						<input
							type="checkbox"
							id="<?php echo esc_attr( $fields['network']['id'] ); ?>"
							name="<?php echo esc_attr( $fields['network']['name'] ); ?>[]"
							value="<?php echo esc_attr( $network['name'] ); ?>"
							<?php checked( $checked, true ); ?>>
						<?php echo esc_html($network['name']); ?>
					</label>
					<?php
					echo '<br>';
				}

				echo '<br>';
			}
			?>
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['layout']['id'] ); ?>"><?php _e( 'Layout:' ); ?></label>

			<select
				id="<?php echo esc_attr( $fields['layout']['id'] ); ?>"
				name="<?php echo esc_attr( $fields['layout']['name'] ); ?>">
				<option value="">&ndash; Select &ndash;</option>
				<?php
				$layouts = get_field( 'social_layouts', 'options' );

				if ( $layouts ) foreach( $layouts as $i => $layout ) {
					$selected = false;
					if ( $fields['layout']['value'] === null && $i == 0 ) $selected = true;
					else if ( $fields['layout']['value'] == $layout['name'] ) $selected = true;

					?>
					<option value="<?php echo esc_attr($layout['name']); ?>" <?php selected($selected, true); ?>><?php echo esc_html($layout['name']); ?></option>
					<?php
				}
				?>
			</select>
		</p>

		<p>
			<em>Add networks and layouts in <a href="<?php echo esc_attr( admin_url('admin.php?pagetheme-options-social') ); ?>" target="_blank">Theme Options</a>.</em>
		</p>
		<?php
	}

} // class socialMediaButtonWidget

function socialMediaButtonWidget_register_widget() {
	register_widget( 'socialMediaButtonWidget' );
}
add_action( 'widgets_init', 'socialMediaButtonWidget_register_widget' );