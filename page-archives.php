<?php
/**
 * 文章归档页模板 — Archives
 *
 * Template Name: 文章归档
 * @package Amorsum
 */

get_header();

// 查询所有已发布文章
$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$posts_per_page = 50; // 每页显示50篇文章
$all_posts = new WP_Query([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'ignore_sticky_posts' => 1,
]);
?>

<header class="page-header">
    <h1 class="page-header__title"><?php the_title(); ?></h1>
    <?php if (has_excerpt()): ?>
    <p class="page-header__desc"><?php echo esc_html(get_the_excerpt()); ?></p>
    <?php endif; ?>
</header>

<?php if ($all_posts->have_posts()): ?>
<div class="archive__timeline">
    <?php
    $current_year = 0;
    while ($all_posts->have_posts()): $all_posts->the_post();
        $year = get_the_date('Y');
        if ($year !== $current_year):
            $current_year = $year;
    ?>
    <div class="archive__year">
        <span class="archive__year-label"><?php echo esc_html($year); ?></span>
        <span class="archive__year-line"></span>
    </div>
    <?php endif; ?>

    <div class="archive__item animate-fade-up">
        <div class="archive__item-dot"></div>
        <time class="archive__item-date" datetime="<?php echo get_the_date('Y-m-d'); ?>">
            <?php echo get_the_date('m-d'); ?>
        </time>
        <a href="<?php the_permalink(); ?>" class="archive__item-title"><?php the_title(); ?></a>
        <?php
        $cats = get_the_category();
        if (!empty($cats)):
        ?>
        <span class="archive__item-category">
            in <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>"><?php echo esc_html($cats[0]->name); ?></a>
        </span>
        <?php endif; ?>
    </div>
    <?php endwhile; wp_reset_postdata(); ?>
</div>

<?php
// 分页
$pagination = paginate_links([
    'total'     => $all_posts->max_num_pages,
    'current'   => $paged,
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
