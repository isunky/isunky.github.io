<?php get_header(); ?>
<main id="content" class="shell article-page">
	<?php while ( have_posts() ) : the_post(); ?><article><header class="article-page__header"><h1><?php the_title(); ?></h1></header><div class="prose article-page__content"><?php the_content(); ?></div></article><?php endwhile; ?>
</main>
<?php get_footer(); ?>
