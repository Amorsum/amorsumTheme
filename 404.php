<?php
/**
 * 404 页面 — 404 Not Found
 *
 * @package Amorsum
 */

get_header();
?>

<div class="error-404">
    <div class="error-404__inner">
        <div class="error-404__code">404</div>
        <h1 class="error-404__title"><?php esc_html_e('页面未找到', 'amorsum'); ?></h1>
        <p class="error-404__desc"><?php esc_html_e('你访问的页面可能已被删除、更名或暂时不可用。', 'amorsum'); ?></p>
        <div class="error-404__actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="hero__btn hero__btn--primary">
                <i class="ph ph-house"></i>
                <span><?php esc_html_e('返回首页', 'amorsum'); ?></span>
            </a>
            <?php get_search_form(); ?>
        </div>
    </div>
</div>

<?php
get_footer();
