<?php
use function Aberdeen\{ archive_url, asset, track_attr };
get_header();
?>
<main id="main">
	<section id="about" class="hero" data-carousel aria-label="Highlights">
		<?php
		$slides = array(
			array( 'Join the University of Aberdeen in India', 'Global challenges are waiting for new ways of thinking. Discover your true north and start creating a more inclusive and sustainable world.', 'mumbai.jpg', 'Mumbai skyline' ),
			array( 'Careers and Employability', 'Connect your degree to the work you want to do.', 'careers.jpg', 'Students on campus' ),
			array( 'Explore Our Programmes', 'Find the undergraduate or postgraduate programme that fits your plans.', 'engineering.jpg', 'Students working at a computer' ),
		);
		foreach ( $slides as $index => $slide ) :
			?>
			<div class="hero-slide" data-slide <?php echo $index ? 'hidden' : ''; ?>>
				<img src="<?php echo esc_url( asset( 'images/' . $slide[2] ) ); ?>" alt="<?php echo esc_attr( $slide[3] ); ?>" <?php echo $index ? 'loading="lazy"' : 'fetchpriority="high"'; ?>>
				<div class="hero-caption wrap">
					<?php if ( 0 === $index ) : ?><h1><?php echo esc_html( $slide[0] ); ?></h1><?php else : ?><h2><?php echo esc_html( $slide[0] ); ?></h2><?php endif; ?>
					<p><a href="<?php echo esc_url( archive_url() ); ?>" data-track="<?php echo track_attr( 'link', 'explore-programmes', 'home-hero', 'componentLink' ); ?>"><?php echo esc_html( $slide[1] ); ?> <span aria-hidden="true">→</span></a></p>
				</div>
			</div>
		<?php endforeach; ?>
		<button type="button" class="hero-arrow prev" data-slide-step="-1" aria-label="Previous slide" data-track="<?php echo track_attr( 'button', 'previous-slide', 'home-hero', 'componentButton' ); ?>">‹</button>
		<button type="button" class="hero-arrow next" data-slide-step="1" aria-label="Next slide" data-track="<?php echo track_attr( 'button', 'next-slide', 'home-hero', 'componentButton' ); ?>">›</button>
		<div class="hero-dots" aria-label="Choose a slide">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<button type="button" data-slide-index="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( $slide[0] ); ?>" aria-pressed="<?php echo $index ? 'false' : 'true'; ?>" data-track="<?php echo track_attr( 'button', 'slide-' . ( $index + 1 ), 'home-hero', 'componentButton' ); ?>"></button>
			<?php endforeach; ?>
		</div>
	</section>
	<section id="careers" class="careers">
		<div class="wrap">
			<h2 class="ruled">Careers and Employability</h2>
			<div class="careers-grid">
				<img src="<?php echo esc_url( asset( 'images/careers.jpg' ) ); ?>" alt="Two students talking on campus" loading="lazy">
				<div>
					<p>Explore career paths and develop practical skills throughout your time at university.</p>
					<p>Our programmes combine academic study with opportunities to apply what you learn and prepare for your next step.</p>
					<a class="pill" href="<?php echo esc_url( archive_url() ); ?>" data-track="<?php echo track_attr( 'link', 'see-programmes', 'careers', 'componentLink' ); ?>">See More</a>
				</div>
			</div>
		</div>
	</section>
	<section id="programmes" class="home-programmes">
		<div class="wrap">
			<h2 class="ruled centred">Our Programmes</h2>
			<div class="home-programme-grid">
				<?php foreach ( Aberdeen\programme_types() as $slug => $name ) : ?>
					<article class="home-programme">
						<img src="<?php echo esc_url( asset( 'images/' . ( 'undergraduate' === $slug ? 'computing.jpg' : 'business.jpg' ) ) ); ?>" alt="" loading="lazy">
						<div>
							<h3 class="ruled"><?php echo esc_html( $name . ' Programmes' ); ?></h3>
							<p><?php echo 'undergraduate' === $slug ? 'Build a foundation for your career through an undergraduate degree.' : 'Develop specialist knowledge and advance your career with postgraduate study.'; ?></p>
							<a href="<?php echo esc_url( archive_url( $slug ) ); ?>" aria-label="<?php echo esc_attr( 'See ' . $name . ' programmes' ); ?>" data-track="<?php echo track_attr( 'link', $slug, 'home-programmes', 'componentCard' ); ?>">See More <span aria-hidden="true">→</span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
