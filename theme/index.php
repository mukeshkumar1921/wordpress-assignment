<?php get_header(); ?>
<main class="wrap standard-page" id="main">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article><h1><?php the_title(); ?></h1><?php the_content(); ?></article>
		<?php endwhile; ?>
	<?php else : ?>
		<h1>Page not found</h1>
		<p><a href="<?php echo esc_url( Aberdeen\archive_url() ); ?>" data-track="<?php echo Aberdeen\track_attr( 'link', 'browse-programmes', 'not-found', 'componentLink' ); ?>">Browse programmes</a></p>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
