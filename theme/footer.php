<?php
use function Aberdeen\{ asset, track_attr };
?>
<footer id="contact" class="site-footer">
	<div class="wrap footer-main">
		<div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track="<?php echo track_attr( 'link', 'home', 'site-footer', 'componentLink' ); ?>"><img class="brand" src="<?php echo esc_url( asset( 'images/brand.png' ) ); ?>" alt="University of Aberdeen Mumbai"></a>
			<p>The University of Aberdeen<br>India Campus<br>Mumbai</p>
			<button class="text-button" type="button" data-open-enquiry data-track="<?php echo track_attr( 'button', 'contact-us', 'site-footer', 'componentButton' ); ?>">Contact Us</button>
			<a href="https://www.abdn.ac.uk/students/academic-life/academic-policies/" data-track="<?php echo track_attr( 'link', 'student-policies', 'site-footer', 'componentLink' ); ?>">Student Policies</a>
		</div>
		<div class="social">
			<strong>Connect With Us</strong>
			<div>
				<?php foreach ( array( 'X' => 'https://x.com/aberdeenuni', 'Instagram' => 'https://www.instagram.com/uniofaberdeen/', 'LinkedIn' => 'https://www.linkedin.com/school/university-of-aberdeen/' ) as $label => $url ) : ?>
					<a href="<?php echo esc_url( $url ); ?>" data-track="<?php echo track_attr( 'link', $label, 'site-footer', 'componentLink' ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="footer-legal">
		<?php foreach ( array( 'Privacy' => 'privacy', 'Accessibility' => 'accessibility', 'Cookies' => 'cookies' ) as $label => $path ) : ?>
			<a href="<?php echo esc_url( 'https://www.abdn.ac.uk/' . $path . '/' ); ?>" data-track="<?php echo track_attr( 'link', $label, 'site-footer', 'componentLink' ); ?>"><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
		<p>© University of Aberdeen</p>
	</div>
</footer>
<?php get_template_part( 'template-parts/lead-form' ); ?>
<?php wp_footer(); ?>
</body>
</html>
