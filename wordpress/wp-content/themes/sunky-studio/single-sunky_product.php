<?php
get_header();
$work_id  = get_the_ID();
$version  = sunky_studio_version( $work_id );
$tagline  = sunky_studio_meta( $work_id, 'tagline' );
$repo     = sunky_studio_meta( $work_id, 'repo_url' );
$site     = sunky_studio_meta( $work_id, 'site_url' );
$default  = sunky_studio_meta( $work_id, 'download_url' );
$msi      = sunky_studio_meta( $work_id, 'download_msi_url' );
$zip      = sunky_studio_meta( $work_id, 'download_zip_url' );
$edge     = sunky_studio_meta( $work_id, 'download_edge_url' );
$primary  = $msi ? $msi : ( $default ? $default : ( $zip ? $zip : $edge ) );
$name     = get_the_title( $work_id );
$preview  = has_post_thumbnail( $work_id ) ? get_the_post_thumbnail_url( $work_id, 'large' ) : ( strtolower( $name ) === 'mdview' ? get_theme_file_uri( 'assets/mdview-screen.webp' ) : sunky_studio_art( $work_id ) );
$features = array();
$highlights = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$highlight_title = sunky_studio_meta( $work_id, 'highlight_' . $i . '_title' );
	$highlight_body  = sunky_studio_meta( $work_id, 'highlight_' . $i . '_body' );
	if ( $highlight_title || $highlight_body ) {
		$highlights[] = array( 'title' => $highlight_title, 'body' => $highlight_body );
	}
	$title = sunky_studio_meta( $work_id, 'feature_' . $i . '_title' );
	$body  = sunky_studio_meta( $work_id, 'feature_' . $i . '_body' );
	if ( $title || $body ) {
		$features[] = array( 'title' => $title, 'body' => $body );
	}
}
$releases = get_posts( array(
	'post_type'      => 'sunky_release',
	'post_status'    => 'publish',
	'posts_per_page' => 5,
	'meta_key'       => '_sunky_product_id',
	'meta_value'     => $work_id,
) );
?>
<main id="content" class="shell product-page">
	<nav class="breadcrumb" aria-label="面包屑"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">作品</a><span aria-hidden="true">/</span><span><?php echo esc_html( $name ); ?></span></nav>
	<section class="product-hero">
		<div class="product-hero__copy">
			<p class="eyebrow">独立软件 · <?php echo esc_html( $name ); ?></p>
			<h1><?php echo esc_html( $name ); ?></h1>
			<?php if ( $version ) : ?><p class="product-version"><?php echo esc_html( $version ); ?></p><?php endif; ?>
			<?php if ( $tagline ) : ?><h2><?php echo esc_html( $tagline ); ?></h2><?php endif; ?>
			<p class="product-hero__summary"><?php echo esc_html( sunky_studio_product_excerpt( $work_id ) ); ?></p>
			<div class="product-hero__actions">
				<?php if ( $primary ) : ?><a class="button button--primary" href="<?php echo esc_url( $primary ); ?>">下载<?php echo esc_html( $name ); ?> <span aria-hidden="true">↓</span></a><?php endif; ?>
				<?php if ( $repo ) : ?><a class="text-link" href="<?php echo esc_url( $repo ); ?>" target="_blank" rel="noopener noreferrer">查看源码 <span aria-hidden="true">↗</span></a><?php endif; ?>
				<?php if ( $site ) : ?><a class="text-link" href="<?php echo esc_url( $site ); ?>" target="_blank" rel="noopener noreferrer">作品网站 <span aria-hidden="true">↗</span></a><?php endif; ?>
			</div>
		</div>
		<?php if ( $preview ) : ?><div class="product-hero__visual"><img src="<?php echo esc_url( $preview ); ?>" alt="<?php echo esc_attr( $name . ' 界面预览' ); ?>" loading="eager"></div><?php endif; ?>
	</section>

	<?php if ( $highlights ) : ?><section class="product-highlights" aria-label="作品亮点"><div class="feature-grid">
	<?php foreach ( $highlights as $index => $highlight ) : ?><article class="feature-item"><span class="feature-item__number">0<?php echo esc_html( $index + 1 ); ?></span><h3><?php echo esc_html( $highlight['title'] ); ?></h3><p><?php echo esc_html( $highlight['body'] ); ?></p></article><?php endforeach; ?>
	</div></section><?php endif; ?>

	<?php if ( $features ) : ?>
	<section class="product-section" aria-labelledby="features-title">
		<div class="section-heading"><div><h2 id="features-title">主要功能</h2><p>为日常使用打磨的细节。</p></div></div>
		<div class="feature-grid">
		<?php foreach ( $features as $index => $feature ) : ?><article class="feature-item"><span class="feature-item__number">0<?php echo esc_html( $index + 1 ); ?></span><h3><?php echo esc_html( $feature['title'] ); ?></h3><p><?php echo esc_html( $feature['body'] ); ?></p></article><?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( trim( get_post_field( 'post_content', $work_id ) ) ) : ?>
	<section class="product-section product-story" aria-labelledby="story-title"><h2 id="story-title">关于<?php echo esc_html( $name ); ?></h2><div class="prose"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $work_id ) ); ?></div></section>
	<?php endif; ?>

	<?php if ( $msi || $zip || $edge || $default ) : ?>
	<section class="download-section" id="download" aria-labelledby="download-title">
		<div class="section-heading"><div><h2 id="download-title">下载<?php echo esc_html( $name ); ?></h2><?php if ( $version ) : ?><p>当前版本 <?php echo esc_html( $version ); ?></p><?php endif; ?></div></div>
		<div class="download-list">
		<?php foreach ( array( 'Windows 安装版（MSI）' => $msi, 'Windows 便携版（ZIP）' => $zip, 'Edge 扩展（ZIP）' => $edge ) as $label => $url ) : if ( ! $url ) { continue; } ?><a class="download-row" href="<?php echo esc_url( $url ); ?>"><strong><?php echo esc_html( $label ); ?></strong><span><?php echo esc_html( $version ); ?></span><b>下载 ↓</b></a><?php endforeach; ?>
		<?php if ( $default && ! $msi && ! $zip && ! $edge ) : ?><a class="download-row" href="<?php echo esc_url( $default ); ?>"><strong>下载安装包</strong><span><?php echo esc_html( $version ); ?></span><b>下载 ↓</b></a><?php endif; ?>
		</div>
		<?php if ( strtolower( $name ) === 'mdview' ) : ?><p class="download-note">Windows 版需要预先安装 <a href="https://developer.microsoft.com/microsoft-edge/webview2/" target="_blank" rel="noopener noreferrer">WebView2 Runtime</a>。</p><?php endif; ?>
	</section>
	<?php endif; ?>

	<?php if ( $releases ) : ?><section class="product-section release-section" aria-labelledby="releases-title"><div class="section-heading"><div><h2 id="releases-title">版本记录</h2><p>了解这个作品的更新。</p></div></div><div class="release-list">
	<?php foreach ( $releases as $release ) : ?><a class="release-item" href="<?php echo esc_url( get_permalink( $release ) ); ?>"><strong><?php echo esc_html( sunky_studio_meta( $release->ID, 'version' ) ?: get_the_title( $release ) ); ?></strong><span><?php echo esc_html( get_the_title( $release ) ); ?></span><time datetime="<?php echo esc_attr( get_the_date( 'c', $release ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d', $release ) ); ?></time><span aria-hidden="true">→</span></a><?php endforeach; ?>
	</div></section><?php endif; ?>
</main>
<?php get_footer(); ?>
