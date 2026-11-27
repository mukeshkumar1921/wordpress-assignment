<?php
use function Aberdeen\{ archive_url, asset, track_attr };
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main" data-track="<?php echo track_attr( 'link', 'skip-to-content', 'site-header', 'componentLink' ); ?>">Skip to content</a>
<header class="site-header">
	<div class="wrap masthead">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track="<?php echo track_attr( 'link', 'home', 'site-header', 'componentLink' ); ?>"><img class="brand" src="<?php echo esc_url( asset( 'images/brand.png' ) ); ?>" alt="University of Aberdeen Mumbai"></a>
		<nav aria-label="Primary navigation">
			<?php foreach ( array( 'Study' => archive_url(), 'Admission' => home_url( '/#programmes' ), 'About Us' => home_url( '/#about' ), 'Alumni' => home_url( '/#careers' ), 'Contact Us' => home_url( '/#contact' ) ) as $label => $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" data-track="<?php echo track_attr( 'link', $label, 'site-header', 'componentLink' ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
			<button class="pill" type="button" data-open-enquiry data-track="<?php echo track_attr( 'button', 'apply-now', 'site-header', 'componentButton' ); ?>">Apply Now</button>
		</nav>
	</div>
	<div class="site-affiliation"><div class="wrap"><a href="<?php echo esc_url( 'https://www.abdn.ac.uk/' ); ?>" data-track="<?php echo track_attr( 'link', 'university-of-aberdeen-uk', 'site-header', 'componentLink' ); ?>">University of Aberdeen UK</a></div></div>
</header>
