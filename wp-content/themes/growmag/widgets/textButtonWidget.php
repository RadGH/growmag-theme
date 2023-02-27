<?php

class textButtonWidget extends WP_Widget
{

	// Register widget with WordPress.
	public function __construct() {
		parent::__construct( 'textButtonWidget', // Base ID
			'Text & Button', // Name
			array( 'description' => 'Display arbitrary text or HTML with a button.' ) // Args
		);
	}

	// Front-end display of widget.
	public function widget( $widget, $instance ) {
		$title = !empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$text = !empty( $instance['text'] ) ? wpautop( $instance['text'] ) : '';

		$button_url = $instance['button_url'];
		$button_text = $instance['button_text'];
		$button_external = !empty( $instance['button_external'] ) ? 1 : 0;

		if ( !$title && !$text ) {
			return;
		}

		if ( $button_url ) {
			if ( $button_external ) {
				$text .= "\n\n" . sprintf( '<a href="%s" class="button" rel="external">%s</a>', esc_attr( external_url( $button_url ) ), esc_html( $button_text ? $button_text : 'Read More' ) );
			}else{
				$text .= "\n\n" . sprintf( '<a href="%s" class="button">%s</a>', esc_attr( $button_url ), esc_html( $button_text ? $button_text : 'Read More' ) );
			}
		}

		echo $widget['before_widget'];

		?>
		<div class="text-widget">
			<?php if ( $title ) echo $widget['before_title'], esc_html( $title ), $widget['after_title']; ?>

			<div class="text-widget-content widgettext"><?php echo wpautop( $text ); ?></div>
		</div>
		<?php

		echo $widget['after_widget'];
	}

	// Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text'] = $new_instance['text'];
		$instance['button_url'] = $new_instance['button_url'];
		$instance['button_text'] = $new_instance['button_text'];
		$instance['button_external'] = !empty( $new_instance['button_external'] ) ? 1 : 0;

		return $instance;
	}


	public function form( $instance ) {
		// Retrieve all of our fields from the $instance variable
		$fields = array(
			'title',
			'text',
			'button_url',
			'button_text',
			'button_external',
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

		if ( $fields['button_text']['value'] === null ) {
			$fields['button_text']['value'] = '';
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
			<label for="<?php echo esc_attr( $fields['button_url']['id'] ); ?>"><?php _e( 'Button Settings <small>(optional)</small>:' ); ?></label>
		</p>

		<p>
			<input class="widefat" type="text"
			       id="<?php echo esc_attr( $fields['button_url']['id'] ); ?>"
			       name="<?php echo esc_attr( $fields['button_url']['name'] ); ?>"
			       placeholder="/example-page/"
			       value="<?php echo esc_attr( $fields['button_url']['value'] ); ?>" />
		</p>

		<p>
			<label class="screen-reader-text" for="<?php echo esc_attr( $fields['button_text']['id'] ); ?>"><?php _e( 'Button Text:' ); ?></label>
			<input class="widefat" type="text"
			       id="<?php echo esc_attr( $fields['button_text']['id'] ); ?>"
			       name="<?php echo esc_attr( $fields['button_text']['name'] ); ?>"
			       placeholder="Read More"
			       value="<?php echo esc_attr( $fields['button_text']['value'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $fields['button_external']['id'] ); ?>">
				<input type="checkbox"
				       id="<?php echo esc_attr( $fields['button_external']['id'] ); ?>"
				       name="<?php echo esc_attr( $fields['button_external']['name'] ); ?>"
					<?php checked( $fields['button_external']['value'] ); ?> />
				<?php _e( 'Link is external' ); ?>
			</label>
		</p>

		<?php
	}

} // class textButtonWidget

function textButtonWidget_register_widget() {
	register_widget( 'textButtonWidget' );
}
add_action( 'widgets_init', 'textButtonWidget_register_widget' );