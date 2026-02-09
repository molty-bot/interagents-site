<?php
/**
 * Main template file (fallback)
 *
 * @package InterAgents
 */

get_header();
?>

<section class="hero" id="hero">
	<div class="container hero-inner">
		<h1 class="reveal"><?php esc_html_e( 'InterAgents.ai', 'interagents' ); ?></h1>
		<p class="reveal"><?php esc_html_e( 'Inteligentne rozwiązania AI dla Twojego biznesu.', 'interagents' ); ?></p>
	</div>
</section>

<?php if ( have_posts() ) : ?>
<section class="section">
	<div class="container">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2><?php the_title(); ?></h2>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
