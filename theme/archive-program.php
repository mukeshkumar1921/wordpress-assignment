<?php
use function Aberdeen\{ archive_url, catalogue, programme_types, render_programme_card, track_attr };

$types = programme_types();
$current = is_tax( 'program-type' ) ? get_queried_object()->slug : 'all';
$groups = array_fill_keys( array_keys( $types ), array() );
foreach ( catalogue() as $program ) {
	foreach ( $types as $slug => $name ) {
		if ( has_term( $slug, 'program-type', $program ) ) {
			$groups[ $slug ][] = $program->ID;
		}
	}
}
$title = 'all' === $current ? 'Our Programmes' : ( $types[ $current ] ?? single_term_title( '', false ) ) . ' Programmes';
$labels = array( 'all' => 'Our Programmes' );
$counts = array_map( 'count', $groups );
foreach ( $types as $slug => $name ) {
	$labels[ $slug ] = $name . ' Programmes';
}
$counts['all'] = array_sum( $counts );
$context = array(
	'current' => $current,
	'title' => $title,
	'loading' => false,
	'hasResults' => ! empty( $counts[ $current ] ),
	'titles' => $labels,
	'counts' => $counts,
	'siteName' => get_bloginfo( 'name' ),
	'urls' => array( 'all' => archive_url(), 'undergraduate' => archive_url( 'undergraduate' ), 'postgraduate' => archive_url( 'postgraduate' ) ),
);
get_header();
?>
<main id="main" data-selected="<?php echo esc_attr( $current ); ?>" data-wp-bind--data-selected="context.current" data-wp-interactive="aberdeen/programmes" <?php echo wp_interactivity_data_wp_context( $context ); ?> data-wp-init="callbacks.watchHistory">
	<header class="listing-banner">
		<h1 class="ruled centred" data-wp-text="context.title"><?php echo esc_html( $title ); ?></h1>
	</header>
	<section class="wrap listing">
		<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'current' => $title, 'interactive' => true ) ); ?>
		<p class="listing-intro">Discover a campus that blends Scottish heritage with Indian innovation. A global education awaits!</p>
		<nav class="filters" aria-label="Filter programmes">
			<?php foreach ( array( 'all' => 'All' ) + $types as $slug => $label ) : ?>
				<a href="<?php echo esc_url( archive_url( 'all' === $slug ? '' : $slug ) ); ?>"
					<?php echo wp_interactivity_data_wp_context( array( 'filter' => $slug ) ); ?>
					data-wp-on--click="actions.filter" data-filter="<?php echo esc_attr( $slug ); ?>"
					<?php if ( $current === $slug ) : ?>aria-current="page"<?php endif; ?>
					data-track="<?php echo track_attr( 'link', $slug, 'programme-listing', 'componentFilter' ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="listing-status" aria-live="polite">
			<p hidden data-wp-bind--hidden="!context.loading" role="status">Loading programmes…</p>
			<p <?php echo empty( $counts[ $current ] ) ? '' : 'hidden'; ?> data-wp-bind--hidden="context.hasResults">No programmes are available for this level.</p>
		</div>
		<div data-wp-bind--aria-busy="context.loading">
			<?php foreach ( $groups as $slug => $programmes ) : ?>
				<?php if ( ! $programmes ) { continue; } ?>
				<section class="programme-group" data-group="<?php echo esc_attr( $slug ); ?>">
					<h2><?php echo esc_html( $types[ $slug ] . ' Programmes' ); ?></h2>
					<div class="programme-grid">
						<?php foreach ( $programmes as $program_id ) { render_programme_card( $program_id ); } ?>
					</div>
				</section>
			<?php endforeach; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
