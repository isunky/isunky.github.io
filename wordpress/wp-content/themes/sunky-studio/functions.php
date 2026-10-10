<?php
/** Sunky Studio theme helpers. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sunky_studio_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_post_type_support( 'sunky_product', 'thumbnail' );
}
add_action( 'after_setup_theme', 'sunky_studio_setup' );

function sunky_studio_styles() {
	wp_enqueue_style( 'sunky-studio', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'sunky_studio_styles' );

function sunky_studio_posts_url() {
	$page_id = (int) get_option( 'page_for_posts' );
	return $page_id ? get_permalink( $page_id ) : home_url( '/articles/' );
}

function sunky_studio_meta( $post_id, $name ) {
	return (string) get_post_meta( $post_id, '_sunky_' . $name, true );
}

function sunky_studio_version( $post_id ) {
	$version = sunky_studio_meta( $post_id, 'version' );
	if ( '' !== $version ) {
		return $version;
	}
	$releases = get_posts( array(
		'post_type'      => 'sunky_release',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => '_sunky_product_id',
		'meta_value'     => $post_id,
	) );
	return $releases ? sunky_studio_meta( $releases[0]->ID, 'version' ) : '';
}

function sunky_studio_art( $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail_url( $post_id, 'medium_large' );
	}
	$slug = strtolower( (string) get_post_field( 'post_name', $post_id ) );
	$name = strtolower( (string) get_the_title( $post_id ) );
	$known = array( 'mdview', 'plainmint', 'qzip', 'dsh-desktop', 'pptx-refactor', 'agent-md-wizard', 'cat-maze-adventure', 'frog-hop' );
	foreach ( $known as $key ) {
		if ( $slug === $key || $name === $key ) {
			$asset = get_theme_file_path( 'assets/' . $key . '.webp' );
			if ( file_exists( $asset ) ) {
				return get_theme_file_uri( 'assets/' . $key . '.webp' );
			}
		}
	}
	return '';
}

function sunky_studio_product_excerpt( $post_id ) {
	$excerpt = get_post_field( 'post_excerpt', $post_id );
	if ( $excerpt ) {
		return wp_strip_all_tags( $excerpt );
	}
	$tagline = sunky_studio_meta( $post_id, 'tagline' );
	return $tagline ? $tagline : wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), 24 );
}

function sunky_studio_product_card( $post_id, $position ) {
	$art = sunky_studio_art( $post_id );
	$version = sunky_studio_version( $post_id );
	$classes = $position === 0 ? 'work-card work-card--featured' : ( $position < 3 ? 'work-card work-card--medium' : 'work-card work-card--small' );
	$classes .= ' work-card--' . ( ( $position % 5 ) + 1 );
	if ( ! $art ) {
		$classes .= ' work-card--no-art';
	}
	if ( 'frog-hop' === strtolower( (string) get_post_field( 'post_name', $post_id ) ) ) {
		$classes .= ' work-card--frog';
	}
	?>
	<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<span class="work-card__copy">
			<?php if ( 0 === $position ) : ?><span class="work-card__eyebrow">01 / 精选作品</span><?php endif; ?>
			<strong class="work-card__name"><?php echo esc_html( get_the_title( $post_id ) ); ?></strong>
			<span class="work-card__version"><?php echo esc_html( $version ? $version : '版本 —' ); ?></span>
			<span class="work-card__description"><?php echo esc_html( sunky_studio_product_excerpt( $post_id ) ); ?></span>
			<span class="work-card__mark" aria-hidden="true"></span>
		</span>
		<?php if ( $art ) : ?><img class="work-card__art" src="<?php echo esc_url( $art ); ?>" alt="" loading="<?php echo 0 === $position ? 'eager' : 'lazy'; ?>"><?php endif; ?>
		<span class="work-card__arrow" aria-hidden="true">↗</span>
	</a>
	<?php
}

function sunky_studio_admin_notice() {
	if ( current_user_can( 'activate_plugins' ) && ! post_type_exists( 'sunky_product' ) ) {
		echo '<div class="notice notice-warning"><p>请启用「Sunky 作品内容」插件，以管理作品、版本和下载地址。</p></div>';
	}
}
add_action( 'admin_notices', 'sunky_studio_admin_notice' );
