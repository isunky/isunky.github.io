<?php get_header(); ?>
<main id="content" class="shell article-page">
	<?php while ( have_posts() ) : the_post(); ?>
	<nav class="breadcrumb" aria-label="面包屑"><a href="<?php echo esc_url( sunky_studio_posts_url() ); ?>">文章</a><span aria-hidden="true">/</span><span><?php the_title(); ?></span></nav>
	<article>
		<header class="article-page__header"><span class="article-page__overline">JOURNAL / 文章</span><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) : ?><p><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?></header>
		<?php if ( has_post_thumbnail() ) : ?><div class="article-page__image"><?php the_post_thumbnail( 'full' ); ?></div><?php endif; ?>
		<div class="prose article-page__content"><?php the_content(); ?></div>
		<footer class="article-page__footer"><a class="text-link" href="<?php echo esc_url( sunky_studio_posts_url() ); ?>">← 返回文章列表</a></footer>
	</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
