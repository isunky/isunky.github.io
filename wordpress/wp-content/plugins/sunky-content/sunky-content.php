<?php
/**
 * Plugin Name: Sunky 作品内容
 * Description: 为 Sunky 站点提供作品、版本记录和下载链接管理。
 * Version: 1.2.0
 * Requires PHP: 8.0
 * Text Domain: sunky-content
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sunky_content_register_types() {
	register_post_type(
		'sunky_product',
		array(
			'labels'       => array(
				'name'          => '作品',
				'singular_name' => '作品',
				'add_new_item'  => '添加作品',
				'edit_item'     => '编辑作品',
			),
			'public'       => true,
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'works', 'with_front' => false ),
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-products',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		),
	);

	register_post_type(
		'sunky_release',
		array(
			'labels'       => array(
				'name'          => '版本记录',
				'singular_name' => '版本记录',
				'add_new_item'  => '发布新版本',
				'edit_item'     => '编辑版本记录',
			),
			'public'       => true,
				'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'product-logs', 'with_front' => false ),
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-update',
			'supports'     => array( 'title', 'editor', 'excerpt', 'revisions' ),
		),
	);
}
add_action( 'init', 'sunky_content_register_types' );

function sunky_content_activate() {
	sunky_content_register_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'sunky_content_activate' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

function sunky_content_add_meta_boxes() {
	add_meta_box( 'sunky_product_info', '作品资料', 'sunky_content_product_meta_box', 'sunky_product', 'normal', 'high' );
	add_meta_box( 'sunky_release_info', '版本资料', 'sunky_content_release_meta_box', 'sunky_release', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sunky_content_add_meta_boxes' );

function sunky_content_field( $name, $label, $post, $type = 'text', $hint = '' ) {
	$value = get_post_meta( $post->ID, '_sunky_' . $name, true );
	printf(
		'<p><label for="sunky-%1$s"><strong>%2$s</strong></label><br><input class="widefat" id="sunky-%1$s" name="sunky_%1$s" type="%3$s" value="%4$s">%5$s</p>',
		esc_attr( $name ),
		esc_html( $label ),
		esc_attr( $type ),
		esc_attr( $value ),
		$hint ? '<small>' . esc_html( $hint ) . '</small>' : ''
	);
}

function sunky_content_product_meta_box( $post ) {
	wp_nonce_field( 'sunky_content_save', 'sunky_content_nonce' );
	echo '<p class="description">首页卡片的一句话介绍请填写编辑器中的「摘要」；特色图片会覆盖主题自带的作品配图。</p>';
	sunky_content_field( 'status', '状态', $post, 'text', '例如：开发中、已发布、暂停维护' );
	sunky_content_field( 'version', '当前版本号', $post, 'text', '例如：v3.4.3。首页和作品页都会显示。' );
	sunky_content_field( 'tagline', '一句话标题', $post, 'text', '用于作品详情页，例如：让 Markdown 读起来更舒服。' );
	echo '<hr><h3>作品链接与下载</h3>';
	sunky_content_field( 'repo_url', '源码仓库地址', $post, 'url' );
	sunky_content_field( 'site_url', '作品网站地址', $post, 'url' );
	sunky_content_field( 'download_url', '默认下载地址', $post, 'url', '可填写虚拟主机或对象存储的文件地址。发布版本时可填写该版本专属地址。' );
	sunky_content_field( 'download_msi_url', 'Windows 安装版（MSI）', $post, 'url' );
	sunky_content_field( 'download_zip_url', 'Windows 便携版（ZIP）', $post, 'url' );
	sunky_content_field( 'download_edge_url', 'Edge 扩展（ZIP）', $post, 'url' );
	echo '<hr><h3>作品亮点</h3>';
	for ( $index = 1; $index <= 3; $index++ ) {
		sunky_content_field( 'highlight_' . $index . '_title', '作品亮点 ' . $index . ' 标题', $post );
		sunky_content_field( 'highlight_' . $index . '_body', '作品亮点 ' . $index . ' 简介', $post );
	}
	echo '<hr><h3>主要功能</h3>';
	for ( $index = 1; $index <= 3; $index++ ) {
		sunky_content_field( 'feature_' . $index . '_title', '功能 ' . $index . ' 标题', $post );
		sunky_content_field( 'feature_' . $index . '_body', '功能 ' . $index . ' 简介', $post );
	}
}

function sunky_content_release_meta_box( $post ) {
	wp_nonce_field( 'sunky_content_save', 'sunky_content_nonce' );
	$selected = (int) get_post_meta( $post->ID, '_sunky_product_id', true );
	$products = get_posts( array( 'post_type' => 'sunky_product', 'post_status' => array( 'publish', 'draft', 'private' ), 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	echo '<p><label for="sunky-product-id"><strong>所属作品</strong></label><br><select class="widefat" id="sunky-product-id" name="sunky_product_id"><option value="0">请选择作品</option>';
	foreach ( $products as $product ) {
		printf( '<option value="%1$d"%2$s>%3$s</option>', (int) $product->ID, selected( $selected, $product->ID, false ), esc_html( $product->post_title ) );
	}
	echo '</select></p>';
	sunky_content_field( 'version', '版本号', $post, 'text', '例如：v1.2.0' );
	sunky_content_field( 'platform', '适用平台', $post, 'text', '例如：Windows / macOS' );
	sunky_content_field( 'download_url', '该版本下载地址', $post, 'url' );
}

function sunky_content_save_meta( $post_id ) {
	if ( ! isset( $_POST['sunky_content_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sunky_content_nonce'] ) ), 'sunky_content_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$type = get_post_type( $post_id );
	$fields = 'sunky_product' === $type
		? array( 'status' => 'text', 'version' => 'text', 'tagline' => 'text', 'repo_url' => 'url', 'site_url' => 'url', 'download_url' => 'url', 'download_msi_url' => 'url', 'download_zip_url' => 'url', 'download_edge_url' => 'url', 'highlight_1_title' => 'text', 'highlight_1_body' => 'text', 'highlight_2_title' => 'text', 'highlight_2_body' => 'text', 'highlight_3_title' => 'text', 'highlight_3_body' => 'text', 'feature_1_title' => 'text', 'feature_1_body' => 'text', 'feature_2_title' => 'text', 'feature_2_body' => 'text', 'feature_3_title' => 'text', 'feature_3_body' => 'text' )
		: ( 'sunky_release' === $type ? array( 'version' => 'text', 'platform' => 'text', 'download_url' => 'url' ) : array() );
	foreach ( $fields as $name => $format ) {
		$key = 'sunky_' . $name;
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw   = wp_unslash( $_POST[ $key ] );
		$value = 'url' === $format ? esc_url_raw( $raw ) : sanitize_text_field( $raw );
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_sunky_' . $name );
		} else {
			update_post_meta( $post_id, '_sunky_' . $name, $value );
		}
	}
	if ( 'sunky_release' === $type && isset( $_POST['sunky_product_id'] ) ) {
		$product_id = absint( $_POST['sunky_product_id'] );
		if ( $product_id && 'sunky_product' === get_post_type( $product_id ) ) {
			update_post_meta( $post_id, '_sunky_product_id', $product_id );
		} else {
			delete_post_meta( $post_id, '_sunky_product_id' );
		}
	}
}
add_action( 'save_post', 'sunky_content_save_meta' );

function sunky_content_product_columns( $columns ) {
	$custom = array();
	foreach ( $columns as $key => $label ) {
		$custom[ $key ] = $label;
		if ( 'title' === $key ) {
			$custom['sunky_version'] = '当前版本';
			$custom['sunky_status']  = '状态';
		}
	}
	return $custom;
}
add_filter( 'manage_sunky_product_posts_columns', 'sunky_content_product_columns' );

function sunky_content_product_column( $column, $post_id ) {
	if ( 'sunky_version' === $column || 'sunky_status' === $column ) {
		$name  = 'sunky_version' === $column ? '_sunky_version' : '_sunky_status';
		$value = get_post_meta( $post_id, $name, true );
		echo $value ? esc_html( $value ) : '—';
	}
}
add_action( 'manage_sunky_product_posts_custom_column', 'sunky_content_product_column', 10, 2 );

function sunky_content_release_columns( $columns ) {
	$custom = array();
	foreach ( $columns as $key => $label ) {
		$custom[ $key ] = $label;
		if ( 'title' === $key ) {
			$custom['sunky_product']         = '所属作品';
			$custom['sunky_release_version'] = '版本号';
		}
	}
	return $custom;
}
add_filter( 'manage_sunky_release_posts_columns', 'sunky_content_release_columns' );

function sunky_content_release_column( $column, $post_id ) {
	if ( 'sunky_product' === $column ) {
		$product_id = (int) get_post_meta( $post_id, '_sunky_product_id', true );
		echo $product_id ? esc_html( get_the_title( $product_id ) ) : '—';
	} elseif ( 'sunky_release_version' === $column ) {
		$version = get_post_meta( $post_id, '_sunky_version', true );
		echo $version ? esc_html( $version ) : '—';
	}
}
add_action( 'manage_sunky_release_posts_custom_column', 'sunky_content_release_column', 10, 2 );

function sunky_content_release_query( $limit = 5, $product_id = 0 ) {
	$args = array( 'post_type' => 'sunky_release', 'post_status' => 'publish', 'posts_per_page' => $limit, 'ignore_sticky_posts' => true );
	if ( $product_id ) {
		$args['meta_query'] = array( array( 'key' => '_sunky_product_id', 'value' => $product_id, 'compare' => '=' ) );
	}
	return new WP_Query( $args );
}

function sunky_content_release_archive_page_size( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'sunky_release' ) ) {
		$query->set( 'posts_per_page', 10 );
	}
}
add_action( 'pre_get_posts', 'sunky_content_release_archive_page_size' );

function sunky_content_download_link( $url, $label = '下载软件' ) {
	if ( ! $url ) {
		return '';
	}
	return '<a class="sunky-button sunky-button--primary" href="' . esc_url( $url ) . '" rel="noopener">' . esc_html( $label ) . ' <span aria-hidden="true">↗</span></a>';
}

function sunky_content_products_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'limit' => 6 ), $atts, 'sunky_products' );
	$limit = 'all' === $atts['limit'] ? -1 : min( 50, max( 1, absint( $atts['limit'] ) ) );
	$items = get_posts( array( 'post_type' => 'sunky_product', 'post_status' => 'publish', 'numberposts' => $limit, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
	if ( ! $items ) {
		return '<p class="sunky-empty">作品即将发布。</p>';
	}
	$out = '<div class="sunky-product-grid">';
	foreach ( $items as $item ) {
		$status = get_post_meta( $item->ID, '_sunky_status', true );
		$latest = sunky_content_release_query( 1, $item->ID );
		$excerpt = has_excerpt( $item ) ? get_the_excerpt( $item ) : wp_trim_words( wp_strip_all_tags( $item->post_content ), 28 );
		$out .= '<a class="sunky-product-card" href="' . esc_url( get_permalink( $item ) ) . '"><div class="sunky-card-head"><h3>' . esc_html( get_the_title( $item ) ) . '</h3>';
		if ( $status ) {
			$out .= '<span class="sunky-status">' . esc_html( $status ) . '</span>';
		}
		$out .= '</div><p>' . esc_html( $excerpt ) . '</p><div class="sunky-card-foot">';
		if ( $latest->have_posts() ) {
			$release = $latest->posts[0];
			$version = get_post_meta( $release->ID, '_sunky_version', true );
			$out .= '<span class="sunky-kicker">版本更新' . ( $version ? ' · ' . esc_html( $version ) : '' ) . '</span><span class="sunky-card-update">' . esc_html( get_the_title( $release ) ) . '</span>';
		} else {
			$out .= '<span class="sunky-kicker">作品介绍</span>';
		}
		$out .= '<span class="sunky-card-link">查看作品详情 <span aria-hidden="true">→</span></span></div></a>';
	}
	return $out . '</div>';
}
add_shortcode( 'sunky_products', 'sunky_content_products_shortcode' );

function sunky_content_releases_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'limit' => 5, 'product' => 0, 'paginate' => 'false' ), $atts, 'sunky_releases' );
	$paginate = 'true' === $atts['paginate'];
	$limit = min( 50, max( 1, absint( $atts['limit'] ) ) );
	if ( $paginate ) {
		$query = new WP_Query(
			array(
				'post_type'           => 'sunky_release',
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'paged'               => max( 1, get_query_var( 'paged' ) ),
				'ignore_sticky_posts' => true,
			)
		);
	} else {
		$query = sunky_content_release_query( $limit, absint( $atts['product'] ) );
	}
	if ( ! $query->have_posts() ) {
		return '<p class="sunky-empty">还没有公开的版本更新。</p>';
	}
	$out = '<div class="sunky-release-list">';
	foreach ( $query->posts as $release ) {
		$product_id = (int) get_post_meta( $release->ID, '_sunky_product_id', true );
		$version    = get_post_meta( $release->ID, '_sunky_version', true );
		$excerpt    = has_excerpt( $release ) ? get_the_excerpt( $release ) : wp_trim_words( wp_strip_all_tags( $release->post_content ), 30 );
		$out .= '<article class="sunky-release-row"><div><span class="sunky-kicker">' . ( $product_id ? esc_html( get_the_title( $product_id ) ) : '作品更新' ) . ( $version ? ' · ' . esc_html( $version ) : '' ) . '</span><h3><a href="' . esc_url( get_permalink( $release ) ) . '">' . esc_html( get_the_title( $release ) ) . '</a></h3><p>' . esc_html( $excerpt ) . '</p></div><time datetime="' . esc_attr( get_the_date( 'c', $release ) ) . '">' . esc_html( get_the_date( 'Y-m-d', $release ) ) . '</time></article>';
	}
	$out .= '</div>';
	if ( $paginate && $query->max_num_pages > 1 ) {
		$out .= '<nav class="sunky-pagination" aria-label="版本更新分页">' . paginate_links(
			array(
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total'   => $query->max_num_pages,
				'type'    => 'plain',
			)
		) . '</nav>';
	}
	return $out;
}
add_shortcode( 'sunky_releases', 'sunky_content_releases_shortcode' );

function sunky_content_product_details_shortcode() {
	if ( ! is_singular( 'sunky_product' ) ) {
		return '';
	}
	$id       = get_the_ID();
	$status   = get_post_meta( $id, '_sunky_status', true );
	$repo     = get_post_meta( $id, '_sunky_repo_url', true );
	$site     = get_post_meta( $id, '_sunky_site_url', true );
	$download = get_post_meta( $id, '_sunky_download_url', true );
	$out      = '<div class="sunky-product-actions">';
	if ( $status ) {
		$out .= '<span class="sunky-status">' . esc_html( $status ) . '</span>';
	}
	$out .= sunky_content_download_link( $download );
	foreach ( array( '作品网站' => $site, '源码仓库' => $repo ) as $label => $url ) {
		if ( $url ) {
			$out .= '<a class="sunky-button sunky-button--outline" href="' . esc_url( $url ) . '" rel="noopener noreferrer" target="_blank">' . esc_html( $label ) . ' ↗</a>';
		}
	}
	return $out . '</div>';
}
add_shortcode( 'sunky_product_details', 'sunky_content_product_details_shortcode' );

function sunky_content_product_releases_shortcode() {
	if ( ! is_singular( 'sunky_product' ) ) {
		return '';
	}
	return sunky_content_releases_shortcode( array( 'limit' => 10, 'product' => get_the_ID() ) );
}
add_shortcode( 'sunky_product_releases', 'sunky_content_product_releases_shortcode' );

function sunky_content_release_details_shortcode() {
	if ( ! is_singular( 'sunky_release' ) ) {
		return '';
	}
	$id       = get_the_ID();
	$product  = (int) get_post_meta( $id, '_sunky_product_id', true );
	$version  = get_post_meta( $id, '_sunky_version', true );
	$platform = get_post_meta( $id, '_sunky_platform', true );
	$download = get_post_meta( $id, '_sunky_download_url', true );
	$out      = '<div class="sunky-release-details">';
	if ( $product && 'publish' === get_post_status( $product ) ) {
		$out .= '<a href="' . esc_url( get_permalink( $product ) ) . '">' . esc_html( get_the_title( $product ) ) . '</a>';
	}
	foreach ( array( $version, $platform ) as $value ) {
		if ( $value ) {
			$out .= '<span>' . esc_html( $value ) . '</span>';
		}
	}
	$out .= '</div><div class="sunky-product-actions">' . sunky_content_download_link( $download, '下载此版本' ) . '</div>';
	return $out;
}
add_shortcode( 'sunky_release_details', 'sunky_content_release_details_shortcode' );
