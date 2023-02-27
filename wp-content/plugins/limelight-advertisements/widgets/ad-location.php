<?php

class ldAdLocation extends WP_Widget
{

	// Register widget with WordPress.
	public function __construct() {
		parent::__construct( 'ldAdLocation', // Base ID
			'Ad Location', // Name
			array( 'description' => 'Embed an ad location as a widget.' ) // Args
		);
	}

	// Front-end display of widget.
	public function widget( $widget, $instance ) {
		$location = $instance['ad-location'];

		$args = array(
			'location' => $location,
			'allow_repeat' => 1,
		);

		$ad = ld_ad_shortcode( $args );

		if ( empty($ad) ) return;

		echo $widget['before_widget'];

		?>
		<div class="adwidget"><?php echo $ad; ?></div>
		<?php

		echo $widget['after_widget'];
	}

	// Sanitize widget form values as they are saved.
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['ad-location'] = $new_instance['ad-location'];

		return $instance;
	}


	public function form( $instance ) {
		// Retrieve all of our fields from the $instance variable
		$fields = array(
			'ad-location',
		);

		// Format each field's data into an array
		foreach ( $fields as $name ) {
			$fields[$name] = array(
				'id'    => $this->get_field_id( $name ),
				'name'  => $this->get_field_name( $name ),
				'value' => null,
			);

			if ( isset( $instance[$name] ) ) $fields[$name]['value'] = $instance[$name];
		}

		$locations = get_field( 'ad-locations', 'option' );

		// Display the widget fields
		?>

		<p>
			<label for="<?php echo esc_attr( $fields['ad-location']['id'] ); ?>"><?php _e( 'Ad Location:' ); ?></label>
			<br>

			<?php if ( !empty($locations) ) { ?>
			<select id="<?php echo esc_attr( $fields['ad-location']['id'] ); ?>" name="<?php echo esc_attr( $fields['ad-location']['name'] ); ?>">
				<option value="">&ndash; Select &ndash;</option>
				<?php
				foreach( $locations as $loc ) {
					$location = $loc['location'];
					$display_type = $loc['display_type'];

					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr($location),
						selected($fields['ad-location']['value'], $location, false ),
						esc_html($location)
					);
				}
				?>
			</select>
			<?php }else{ ?>
				<em>No ad locations have been specified.</em>
			<?php } ?>
		</p>
		<?php
	}

} // class ldAdLocation

add_action( 'widgets_init', 'ldad_register_ad_widget' );

function ldad_register_ad_widget() {
	register_widget( 'ldAdLocation' );
}