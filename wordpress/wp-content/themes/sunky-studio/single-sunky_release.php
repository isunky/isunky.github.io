<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); $product_id = (int) sunky_studio_meta( get_the_ID(), 'product_id' ); ?>
<main id="content" class="shell article-page">
	<nav class="breadcrumb" aria-label="面包屑"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">作品</a><span aria-hidden="true">/</span><?php if ( $product_id ) : ?><a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo esc_html( get_the_title( $product_id ) ); ?></a><span aria-hidden="true">/</span><?php endif; ?><span>版本记录</span></nav>
	<article><header class="article-page__header"><p class="eyebrow">版本记录 <?php echo esc_html( sunky_studio_meta( get_the_ID(), 'version' ) ); ?></p><h1><?php the_title(); ?></h1><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time></header><div class="prose article-page__content"><?php the_content(); ?></div><?php $download = sunky_studio_meta( get_the_ID(), 'download_url' ); if ( $download ) : ?><p><a class="button button--primary" href="<?php echo esc_url( $download ); ?>">下载此版本 ↓</a></p><?php endif; ?></article>
</main>
<?php endwhile; get_footer(); ?>
