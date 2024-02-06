<?php
/**
 * @global int $form_id
 */

?>
<div id="rs-downloads-popup" class="rsd-popup" style="display:none;">
	
	<div class="rsd-popup--frame">
		
		<div class="rsd-popup--inner">
			
			<div class="rsd-popup--content">
				
				<a href="#" class="rsd-popup--close-link" title="Close"><span class="close-icon" aria-hidden="true">&times;</span> <span class="close-text">Close</span></a>
				
				<?php
				
				// Display the intro message from settings
				$form_intro_message = get_field( 'form_intro_message', 'rs_downloads' );
				
				if ( $form_intro_message ) {
					echo '<div class="rs-form-intro-message">';
					echo wpautop($form_intro_message);
					echo '</div>';
				}
				
				// Display the form itself. Form ID is required but let's check just to be sure.
				if ( $form_id ) {
					echo '<div class="rs-form rs-form-id-'. $form_id .'">';
					echo RS_Downloads()->Gravity_Forms->get_form_html( $form_id );
					echo '</div>';
				}else{
					echo '<p>Error: Form ID not found.</p>';
				}
				?>
				
			</div>
	
		</div>
		
	</div>
	
</div>