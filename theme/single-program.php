<?php
use function Aberdeen\{ archive_url, track_attr };
get_header();
?>
<main id="main">
	<?php while ( have_posts() ) : the_post(); ?>
		<article>
			<header class="detail-header">
				<div class="wrap">
					<?php get_template_part( 'template-parts/breadcrumbs', null, array( 'current' => get_the_title(), 'programme_root' => true ) ); ?>
					<div class="detail-intro">
						<div>
							<?php foreach ( get_the_terms( get_the_ID(), 'program-type' ) ?: array() as $type ) : ?>
								<a class="type-link" href="<?php echo esc_url( get_term_link( $type ) ); ?>" data-track="<?php echo track_attr( 'link', $type->slug, 'programme-overview', 'componentLink' ); ?>"><?php echo esc_html( $type->name ); ?></a>
							<?php endforeach; ?>
							<h1 class="ruled"><?php the_title(); ?></h1>
							<dl class="programme-meta">
								<div><dt>Academic year</dt><dd><?php echo esc_html( get_post_meta( get_the_ID(), '_academic_year', true ) ); ?></dd></div>
								<div><dt>Duration</dt><dd><?php echo esc_html( get_post_meta( get_the_ID(), '_duration', true ) ); ?></dd></div>
							</dl>
							<div class="detail-actions">
								<a class="pill" href="#register-interest" data-open-enquiry data-track="<?php echo track_attr( 'link', 'apply-now', 'programme-overview', 'componentLink' ); ?>">Apply now</a>
								<button type="button" class="pill outline" data-open-enquiry data-track="<?php echo track_attr( 'button', 'open-enquiry-form', 'programme-overview', 'componentButton' ); ?>">Enquire now</button>
							</div>
						</div>
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				</div>
			</header>
			<div class="wrap detail-body">
				<div class="programme-copy"><?php the_content(); ?></div>
				<?php foreach ( (array) get_post_meta( get_the_ID(), '_content_sections', true ) as $section ) : ?>
					<?php if ( ! is_array( $section ) || ! array_filter( $section ) ) { continue; } ?>
					<section class="content-section">
						<?php if ( ! empty( $section['title'] ) ) : ?><h2><?php echo esc_html( $section['title'] ); ?></h2><?php endif; ?>
						<?php echo wpautop( esc_html( $section['description'] ?? '' ) ); ?>
						<?php if ( ! empty( $section['image'] ) ) { echo wp_get_attachment_image( $section['image'], 'large' ); } ?>
					</section>
				<?php endforeach; ?>
				<a class="back-link" href="<?php echo esc_url( archive_url() ); ?>" data-track="<?php echo track_attr( 'link', 'back-to-programmes', 'programme-overview', 'componentLink' ); ?>">← Back to programmes</a>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
