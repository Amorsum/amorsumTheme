<?php
/**
 * 分类汇总页模板 — All Categories
 *
 * Template Name: 文章分类
 * @package Amorsum
 */

get_header();
?>

<header class="page-header">
    <h1 class="page-header__title"><?php the_title(); ?></h1>
    <?php if (has_excerpt()): ?>
    <p class="page-header__desc"><?php echo esc_html(get_the_excerpt()); ?></p>
    <?php endif; ?>
</header>

<?php
$categories = get_categories([
    'orderby' => 'count',
    'order'   => 'DESC',
]);

if (!empty($categories)):
?>
<div class="categories-list">
    <?php foreach ($categories as $cat): ?>
    <div class="categories-list__group animate-fade-up">
        <!-- 分类头部 -->
        <div class="categories-list__header glass-card">
            <span class="categories-list__name">
                <i class="ph ph-folder"></i>
                <?php echo esc_html($cat->name); ?>
            </span>
            <span class="categories-list__count"><?php echo esc_html($cat->count); ?> <?php esc_html_e('篇文章', 'amorsum'); ?></span>
        </div>

        <!-- 分类描述 -->
        <?php if (!empty($cat->description)): ?>
        <p class="categories-list__desc" style="font-size:var(--text-sm);color:var(--text-muted);margin:8px 24px 0;">
            <?php echo esc_html($cat->description); ?>
        </p>
        <?php endif; ?>

        <!-- 该分类下的最新文章 -->
        <?php
        $cat_posts = new WP_Query([
            'cat'              => $cat->term_id,
            'posts_per_page'   => 5,
            'ignore_sticky_posts' => 1,
        ]);
        if ($cat_posts->have_posts()):
        ?>
        <div class="categories-list__posts">
            <?php while ($cat_posts->have_posts()): $cat_posts->the_post(); ?>
            <div class="categories-list__post">
                <time datetime="<?php echo get_the_date('Y-m-d'); ?>"><?php echo get_the_date('Y-m-d'); ?></time>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

        <?php if ($cat->count > 5): ?>
        <div class="categories-list__more">
            <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                <?php esc_html_e('查看全部', 'amorsum'); ?> <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
    <?php get_template_part('template-parts/none'); ?>
<?php endif; ?>

<?php
get_sidebar();
get_footer();
