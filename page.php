<?php
/**
 * 通用页面模板 — Page
 *
 * @package Amorsum
 */

get_header();
?>

<?php while (have_posts()): the_post(); ?>
<article <?php post_class('page'); ?>>

    <!-- 页面头部 -->
    <header class="page-header">
        <h1 class="page-header__title"><?php the_title(); ?></h1>
        <?php if ($desc = get_the_excerpt()): ?>
        <p class="page-header__desc"><?php echo esc_html($desc); ?></p>
        <?php endif; ?>
    </header>

    <!-- 页面内容 -->
    <div class="page__content glass-card">
        <?php the_content(); ?>

        <?php
        wp_link_pages([
            'before' => '<div class="post__page-links"><span>' . esc_html__('分页：', 'amorsum') . '</span>',
            'after'  => '</div>',
        ]);
        ?>
    </div>

    <!-- 评论区 -->
    <?php
    if (comments_open() || get_comments_number()):
        comments_template();
    endif;
    ?>

</article>
<?php endwhile; ?>

<?php
get_sidebar();
get_footer();
