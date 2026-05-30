<?php
/**
 * 页脚模板 — Footer
 *
 * @package Amorsum
 */
?>

<footer class="footer">
    <div class="footer__inner container">

        <!-- 页脚 Widget 区域 -->
        <?php if (is_active_sidebar('footer-left') || is_active_sidebar('footer-center') || is_active_sidebar('footer-right')): ?>
        <div class="footer__widgets">
            <div class="footer__widget-area">
                <?php dynamic_sidebar('footer-left'); ?>
            </div>
            <div class="footer__widget-area">
                <?php dynamic_sidebar('footer-center'); ?>
            </div>
            <div class="footer__widget-area">
                <?php dynamic_sidebar('footer-right'); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 社交链接 -->
        <?php if (has_nav_menu('social')): ?>
        <div class="footer__social">
            <?php
            wp_nav_menu([
                'theme_location' => 'social',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'depth'          => 1,
                'fallback_cb'    => false,
                'link_before'    => '<span class="screen-reader-text">',
                'link_after'     => '</span>',
            ]);
            ?>
        </div>
        <?php endif; ?>

        <!-- 版权信息 -->
        <div class="footer__copy">
            <span>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></span>
            <?php
            $footer_text = amorsum_theme('footer_text', '');
            if (!empty($footer_text)) {
                echo '<span class="footer__icp">' . wp_kses_post($footer_text) . '</span>';
            }
            ?>
        </div>

        <div class="footer__powered">
            <span><?php esc_html_e('Proudly powered by', 'amorsum'); ?>
                <a href="https://wordpress.org" target="_blank" rel="noopener">WordPress</a>
                &amp;
                <a href="#" target="_blank">Amorsum Theme</a>
            </span>
        </div>
    </div>
</footer>

</div><!-- .app -->

<?php wp_footer(); ?>
</body>
</html>
