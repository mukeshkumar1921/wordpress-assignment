<?php
use function Aberdeen\{ catalogue, lead_choices, track_attr };
$choices = lead_choices();
$current = is_singular( 'program' ) ? get_queried_object_id() : 0;
$field_labels = array(
	'role' => 'I am enquiring as', 'level' => 'Level of interest', 'programme' => 'Programme of interest',
	'first_name' => 'First name', 'last_name' => 'Last name', 'country' => 'Country',
	'city' => 'City', 'phone' => 'Phone number', 'email' => 'Email',
);
?>
<dialog id="register-interest" class="enquiry" aria-labelledby="enquiry-title" data-programme="<?php echo esc_attr( $current ); ?>">
	<header>
		<h2 id="enquiry-title">Register your interest</h2>
		<button type="button" data-close-enquiry aria-label="Close registration form" data-track="<?php echo track_attr( 'button', 'close-registration', 'lead-form', 'componentButton' ); ?>">×</button>
	</header>
	<form data-enquiry-form>
		<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'aberdeen_lead' ) ); ?>">
		<div class="enquiry-grid">
			<?php foreach ( $field_labels as $name => $title ) : ?>
				<label <?php echo 'role' === $name ? 'class="wide"' : ''; ?>>
					<?php echo esc_html( $title ); ?> <span aria-hidden="true">*</span>
					<?php if ( isset( $choices[ $name ] ) || 'programme' === $name ) : ?>
						<select required name="<?php echo esc_attr( $name ); ?>">
							<option value="">Select an option</option>
							<?php if ( 'programme' === $name ) : ?>
								<?php foreach ( catalogue() as $program ) : ?>
									<?php $levels = wp_get_post_terms( $program->ID, 'program-type', array( 'fields' => 'slugs' ) ); ?>
									<option value="<?php echo esc_attr( $program->ID ); ?>" data-levels="<?php echo esc_attr( is_wp_error( $levels ) ? '' : implode( ' ', $levels ) ); ?>"><?php echo esc_html( get_the_title( $program ) ); ?></option>
								<?php endforeach; ?>
							<?php else : ?>
								<?php foreach ( $choices[ $name ] as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					<?php else : ?>
						<?php
						$input_types = array( 'phone' => 'tel', 'email' => 'email' );
						$autocomplete = array( 'first_name' => 'given-name', 'last_name' => 'family-name', 'phone' => 'tel', 'email' => 'email' );
						?>
						<input required name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $input_types[ $name ] ?? 'text' ); ?>"
							autocomplete="<?php echo esc_attr( $autocomplete[ $name ] ); ?>" maxlength="160" <?php echo 'phone' === $name ? 'placeholder="+91"' : ''; ?>>
					<?php endif; ?>
				</label>
			<?php endforeach; ?>
			<label hidden>Website <input name="website" tabindex="-1" autocomplete="off"></label>
		</div>
		<div class="enquiry-actions">
			<p>By clicking the button below, you agree to receive communications about this programme and other academic programmes from the University of Aberdeen and its partners. <a href="https://www.abdn.ac.uk/privacy/" data-track="<?php echo track_attr( 'link', 'privacy-policy', 'lead-form', 'componentLink' ); ?>">Privacy Policy</a></p>
			<p class="form-error" role="alert" hidden></p>
			<button class="pill" type="submit" data-track="<?php echo track_attr( 'button', 'submit-registration', 'lead-form', 'componentButton' ); ?>">Submit</button>
		</div>
	</form>
	<div class="enquiry-confirmation" data-enquiry-success hidden tabindex="-1">
		<div role="status"><strong>✓ &nbsp;Thank you for registering your interest</strong><br>We’ll send you useful information and updates about this programme, the application process, and more.</div>
	</div>
</dialog>
