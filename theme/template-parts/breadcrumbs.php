<?php
use function Aberdeen\{ archive_url, track_attr };
?>
<nav class="breadcrumbs" aria-label="Breadcrumb">
	<ol>
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" data-track="<?php echo track_attr( 'link', 'home', 'breadcrumbs', 'componentLink' ); ?>">Home</a></li>
		<?php if ( ! empty( $args['programme_root'] ) ) : ?>
			<li><a href="<?php echo esc_url( archive_url() ); ?>" data-track="<?php echo track_attr( 'link', 'programmes', 'breadcrumbs', 'componentLink' ); ?>">Programmes</a></li>
		<?php endif; ?>
		<li aria-current="page" <?php echo empty( $args['interactive'] ) ? '' : 'data-wp-text="context.title"'; ?>><?php echo esc_html( $args['current'] ); ?></li>
	</ol>
</nav>
