<?php
/**
 * 无内容提示组件 — Content None
 *
 * @package Amorsum
 */
?>
<div class="none glass-card">
    <div class="none__icon">
        <i class="ph ph-smiley-sad"></i>
    </div>

    <?php if (is_search()): ?>
        <h2 class="none__title"><?php esc_html_e('没有找到相关内容', 'amorsum'); ?></h2>
        <p class="none__desc"><?php esc_html_e('试试其他关键词？', 'amorsum'); ?></p>
        <div class="none__search">
            <?php get_search_form(); ?>
        </div>
    <?php elseif (is_404()): ?>
        <h2 class="none__title"><?php esc_html_e('页面未找到', 'amorsum'); ?></h2>
        <p class="none__desc"><?php esc_html_e('这个页面不存在或已被移除。', 'amorsum'); ?></p>
    <?php else: ?>
        <h2 class="none__title"><?php esc_html_e('暂无内容', 'amorsum'); ?></h2>
        <p class="none__desc"><?php esc_html_e('这里还没有发布任何内容，敬请期待。', 'amorsum'); ?></p>
    <?php endif; ?>
</div>
