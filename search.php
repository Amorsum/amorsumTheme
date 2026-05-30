<?php
/**
 * 搜索页模板 — Search
 *
 * @package Amorsum
 */

get_header();
?>

<header class="page-header">
    <h1 class="page-header__title">
        <?php
        printf(
            esc_html__('搜索：%s', 'amorsum'),
            '<span>' . get_search_query() . '</span>'
        );
        ?>
    </h1>
    <p class="page-header__desc">
        <?php
        global $wp_query;
        printf(
            esc_html(_n('找到 %d 条结果', '找到 %d 条结果', $wp_query->found_posts, 'amorsum')),
            $wp_query->found_posts
        );
        ?>
    </p>
</header>

<?php if (have_posts()): ?>
<div class="post-list__grid">
    <?php while (have_posts()): the_post(); ?>
        <?php get_template_part('template-parts/post-card'); ?>
    <?php endwhile; ?>
</div>

<?php
$pagination = paginate_links([
    'prev_text' => '<i class="ph ph-caret-left"></i> ' . esc_html__('上一页', 'amorsum'),
    'next_text' => esc_html__('下一页', 'amorsum') . ' <i class="ph ph-caret-right"></i>',
    'type'      => 'array',
]);
if ($pagination):
?>
<nav class="pagination">
    <?php
    foreach ($pagination as $link) {
        if (strpos($link, 'current') !== false) {
            echo '<span class="page-numbers current">' . wp_kses_post($link) . '</span>';
        } else {
            echo str_replace('page-numbers', 'page-number', $link);
        }
    }
    ?>
</nav>
<?php endif; ?>

<?php else: ?>
    <?php get_template_part('template-parts/none'); ?>
<?php endif; ?>

<?php
get_sidebar();
get_footer();
