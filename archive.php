<?php
/**
 * 归档页模板 — Archive (分类/标签/日期/作者)
 *
 * @package Amorsum
 */

get_header();
?>

<header class="page-header">
    <h1 class="page-header__title"><?php the_archive_title(); ?></h1>
    <?php
    $desc = get_the_archive_description();
    if ($desc): ?>
    <p class="page-header__desc"><?php echo wp_kses_post($desc); ?></p>
    <?php endif; ?>
</header>

<?php if (have_posts()): ?>

    <?php if (is_tag()): ?>
    <!-- 标签页 — 简约列表 -->
    <div class="tags-list">
        <?php while (have_posts()): the_post(); ?>
        <div class="tags-list__post animate-fade-up">
            <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y-m-d'); ?></time>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </div>
        <?php endwhile; ?>
    </div>

    <?php elseif (is_category()): ?>
    <!-- 分类页 — 卡片列表 -->
    <div class="post-list__grid">
        <?php while (have_posts()): the_post(); ?>
            <?php get_template_part('template-parts/post-card'); ?>
        <?php endwhile; ?>
    </div>

    <?php else: ?>
    <!-- 日期/作者归档 — 时间线 -->
    <div class="archive__timeline">
        <?php
        $current_year = 0;
        while (have_posts()): the_post();
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
            <?php $cats = get_the_category();
            if (!empty($cats)): ?>
            <span class="archive__item-category">
                in <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>"><?php echo esc_html($cats[0]->name); ?></a>
            </span>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

    <!-- 分页 -->
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
