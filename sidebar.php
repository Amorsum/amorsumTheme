<?php
/**
 * 侧栏模板 — Sidebar
 *
 * @package Amorsum
 */

if (!is_active_sidebar('sidebar-main') && !is_single()) {
    return;
}
?>

<aside class="sidebar">
    <?php
    // 作者信息卡（Always visible）
    ?>
    <div class="sidebar__widget glass-card">
        <div class="sidebar__author">
            <?php
            $avatar_url = get_avatar_url(get_the_author_meta('ID'), ['size' => 160]);
            // 如果站点有自定义 logo，用它作为头像；否则用 Gravatar
            ?>
            <img class="sidebar__author-avatar"
                 src="<?php echo esc_url($avatar_url); ?>"
                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" />
            <h3 class="sidebar__author-name"><?php bloginfo('name'); ?></h3>
            <p class="sidebar__author-bio"><?php bloginfo('description'); ?></p>
            <div class="sidebar__author-stats">
                <?php
                $post_count = wp_count_posts()->publish;
                $cat_count  = wp_count_terms('category');
                $tag_count  = wp_count_terms('post_tag');
                ?>
                <div class="sidebar__stat">
                    <span class="sidebar__stat-num"><?php echo esc_html($post_count); ?></span>
                    <span class="sidebar__stat-label"><?php esc_html_e('文章', 'amorsum'); ?></span>
                </div>
                <div class="sidebar__stat">
                    <span class="sidebar__stat-num"><?php echo esc_html($tag_count); ?></span>
                    <span class="sidebar__stat-label"><?php esc_html_e('标签', 'amorsum'); ?></span>
                </div>
                <div class="sidebar__stat">
                    <span class="sidebar__stat-num"><?php echo esc_html($cat_count); ?></span>
                    <span class="sidebar__stat-label"><?php esc_html_e('分类', 'amorsum'); ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php
    // 文章目录（仅文章详情页）
    if (is_single() && amorsum_theme('show_toc', true)):
        $toc = amorsum_generate_toc(get_the_content());
        if ($toc):
    ?>
    <div class="sidebar__widget glass-card">
        <h4 class="sidebar__widget-title"><?php esc_html_e('目录', 'amorsum'); ?></h4>
        <?php echo $toc; ?>
    </div>
    <?php
        endif;
    endif;
    ?>

    <?php
    // 最近文章
    $recent_posts = wp_get_recent_posts(['numberposts' => 5, 'post_status' => 'publish']);
    if (!empty($recent_posts)):
    ?>
    <div class="sidebar__widget glass-card">
        <h4 class="sidebar__widget-title"><?php esc_html_e('最近文章', 'amorsum'); ?></h4>
        <ul class="sidebar__recent-list">
            <?php foreach ($recent_posts as $post): ?>
            <li class="sidebar__recent-item">
                <a href="<?php echo esc_url(get_permalink($post['ID'])); ?>" class="sidebar__recent-link">
                    <span class="sidebar__recent-title"><?php echo esc_html($post['post_title']); ?></span>
                    <span class="sidebar__recent-date"><?php echo mysql2date('m-d', $post['post_date']); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php
    // 标签云
    $tags = get_tags(['orderby' => 'count', 'order' => 'DESC', 'number' => 20]);
    if (!empty($tags)):
    ?>
    <div class="sidebar__widget glass-card">
        <h4 class="sidebar__widget-title"><?php esc_html_e('标签', 'amorsum'); ?></h4>
        <div class="sidebar__tags">
            <?php foreach ($tags as $tag): ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="sidebar__tag">
                <?php echo esc_html($tag->name); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php
    // WordPress 原生 Widget
    dynamic_sidebar('sidebar-main');
    ?>
</aside>
