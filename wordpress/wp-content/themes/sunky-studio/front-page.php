<?php get_header(); ?>
<main id="content" class="shell home-main">
	<section class="home-heading" aria-labelledby="works-title">
		<div><h1 id="works-title">作品</h1><p>为日常创造更顺手的工具。</p></div>
		<span class="home-heading__note">INDEPENDENT SOFTWARE / 持续更新</span>
	</section>
	<?php
	$works = post_type_exists( 'sunky_product' ) ? get_posts( array(
		'post_type'      => 'sunky_product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	) ) : array();
	if ( $works ) :
	?>
	<div class="works-gallery">
		<?php foreach ( $works as $index => $work ) { sunky_studio_product_card( $work->ID, $index ); } ?>
	</div>
	<?php else : ?>
	<p class="empty-state">作品即将发布。启用作品插件后，可在后台添加作品。</p>
	<?php endif; ?>

	<section class="home-articles" aria-labelledby="articles-title">
		<div class="section-heading">
			<div><h2 id="articles-title">文章</h2><p>记录思考、分享经验，也为更好的创作做准备。</p></div>
			<a class="text-link" href="<?php echo esc_url( sunky_studio_posts_url() ); ?>">查看全部文章 <span aria-hidden="true">→</span></a>
		</div>
		<?php $articles = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3 ) ); ?>
		<?php if ( $articles ) : ?><div class="home-article-grid">
			<?php foreach ( $articles as $article ) : ?>
			<a class="home-article" href="<?php echo esc_url( get_permalink( $article ) ); ?>">
				<span class="home-article__top"><time datetime="<?php echo esc_attr( get_the_date( 'c', $article ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d', $article ) ); ?></time><span aria-hidden="true">↗</span></span>
				<h3><?php echo esc_html( get_the_title( $article ) ); ?></h3>
				<p><?php echo esc_html( has_excerpt( $article ) ? get_the_excerpt( $article ) : wp_trim_words( wp_strip_all_tags( $article->post_content ), 28 ) ); ?></p>
			</a>
			<?php endforeach; ?>
		</div><?php else : ?><p class="empty-state">文章即将发布。</p><?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
