<?php
use function Aberdeen\track_attr;
$id = absint( $args['post_id'] ?? 0 );
if ( 'program' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
	return;
}
?>
<article class="programme-card">
	<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" data-track="<?php echo track_attr( 'link', get_post_field( 'post_name', $id ), 'programme-listing', 'componentCard' ); ?>">
		<?php echo get_the_post_thumbnail( $id, 'medium_large', array( 'loading' => 'lazy' ) ); ?>
		<div class="card-body">
			<h3 class="ruled"><?php echo esc_html( get_the_title( $id ) ); ?></h3>
			<dl class="programme-meta">
				<div><dt>Academic year</dt><dd><?php echo esc_html( get_post_meta( $id, '_academic_year', true ) ); ?></dd></div>
				<div><dt>Duration</dt><dd><?php echo esc_html( get_post_meta( $id, '_duration', true ) ); ?></dd></div>
			</dl>
		</div>
	</a>
</article>
