<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>
<footer class="site-footer">
	<div class="site-footer__inner shell">
		<div class="site-footer__identity"><a class="brand brand--small" href="<?php echo esc_url( home_url( '/' ) ); ?>">sunky<span class="brand__dot" aria-hidden="true"></span></a><span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Sunky</span></div>
		<nav aria-label="页脚导航"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">作品</a><a href="<?php echo esc_url( sunky_studio_posts_url() ); ?>">文章</a></nav>
		<p>为日常创造更顺手的工具。</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
