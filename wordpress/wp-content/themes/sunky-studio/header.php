<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content">跳转到内容</a>
<header class="site-header">
	<div class="site-header__inner shell">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="sunky 首页">sunky<span class="brand__dot" aria-hidden="true"></span></a>
		<nav class="site-nav" aria-label="主导航">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo ( is_front_page() || is_singular( 'sunky_product' ) ) ? 'aria-current="page"' : ''; ?>>作品</a>
			<a href="<?php echo esc_url( sunky_studio_posts_url() ); ?>" <?php echo ( is_home() || is_singular( 'post' ) ) ? 'aria-current="page"' : ''; ?>>文章</a>
		</nav>
	</div>
</header>
