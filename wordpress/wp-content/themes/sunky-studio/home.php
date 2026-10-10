<?php get_header(); ?>
<main id="content" class="shell article-index">
	<header class="article-index__heading"><h1>文章</h1><p>记录作品背后的思考、取舍与实践。</p></header>
	<?php if ( have_posts() ) : ?>
		<?php $index = 0; while ( have_posts() ) : the_post(); $index++; ?>
		<?php if ( 1 === $index && ! is_paged() ) : ?>
		<a class="article-feature<?php echo has_post_thumbnail() ? '' : ' article-feature--text-only'; ?>" href="<?php the_permalink(); ?>">
			<span class="article-feature__copy"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time><strong><?php the_title(); ?></strong><span class="article-feature__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></span><span class="text-link">阅读文章 <span aria-hidden="true">↗</span></span></span>
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'large', array( 'class' => 'article-feature__image' ) ); else : ?><span class="article-feature__serial" aria-hidden="true">01<span> / JOURNAL</span></span><?php endif; ?>
		</a>
		<?php else : ?>
		<?php if ( 2 === $index || ( 1 === $index && is_paged() ) ) : ?><section class="article-list" aria-labelledby="more-articles-title"><h2 id="more-articles-title">所有文章</h2><?php endif; ?>
		<a class="article-row" href="<?php the_permalink(); ?>"><span class="article-row__number"><?php echo esc_html( str_pad( (string) ( ( max( 1, get_query_var( 'paged' ) ) - 1 ) * (int) get_option( 'posts_per_page' ) + $index ), 2, '0', STR_PAD_LEFT ) ); ?></span><span class="article-row__copy"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time><strong><?php the_title(); ?></strong><span><?php echo esc_html( get_the_excerpt() ); ?></span></span><span class="article-row__arrow" aria-hidden="true">→</span></a>
		<?php endif; ?>
		<?php endwhile; if ( $index > 1 || is_paged() ) : ?></section><?php endif; ?>
		<nav class="pagination" aria-label="文章分页"><?php echo paginate_links( array( 'prev_text' => '← 上一页', 'next_text' => '下一页 →' ) ); ?></nav>
	<?php else : ?><p class="empty-state">文章即将发布。</p><?php endif; ?>
</main>
<?php get_footer(); ?>
