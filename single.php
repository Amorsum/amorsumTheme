<?php
/**
 * 文章详情页模板 — Single Post
 *
 * @package Amorsum
 */

get_header();

// 阅读进度条
echo '<div class="progress-bar" id="progressBar"></div>';
?>

<?php while (have_posts()): the_post(); ?>
<article <?php post_class('post'); ?>>

    <!-- 文章头部 -->
    <header class="post__header">
        <div class="container">
            <!-- 面包屑 -->
            <div class="post__breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>"><i class="ph ph-house"></i> <?php esc_html_e('首页', 'amorsum'); ?></a>
                <i class="ph ph-caret-right"></i>
                <?php
                $categories = get_the_category();
                if (!empty($categories)):
                ?>
                <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>"><?php echo esc_html($categories[0]->name); ?></a>
                <i class="ph ph-caret-right"></i>
                <?php endif; ?>
                <span><?php the_title(); ?></span>
            </div>

            <!-- 标题 -->
            <h1 class="post__title"><?php the_title(); ?></h1>

            <!-- 元信息 -->
            <div class="post__meta">
                <div class="post__meta-left">
                    <span class="post__meta-item">
                        <i class="ph ph-calendar"></i>
                        <?php echo get_the_date('Y-m-d'); ?>
                    </span>
                    <?php if (get_the_modified_date('Y-m-d') !== get_the_date('Y-m-d')): ?>
                    <span class="post__meta-item post__meta-item--updated">
                        <i class="ph ph-pencil"></i>
                        <?php esc_html_e('更新于', 'amorsum'); ?> <?php echo get_the_modified_date('Y-m-d'); ?>
                    </span>
                    <?php endif; ?>
                    <span class="post__meta-item">
                        <i class="ph ph-clock"></i>
                        <?php echo esc_html(amorsum_reading_time()); ?> <?php esc_html_e('分钟阅读', 'amorsum'); ?>
                    </span>
                </div>
                <div class="post__meta-right">
                    <span class="post__meta-item">
                        <i class="ph ph-eye"></i>
                        <?php echo esc_html(amorsum_get_post_views(get_the_ID())); ?> <?php esc_html_e('次阅读', 'amorsum'); ?>
                    </span>
                </div>
            </div>

            <!-- 标签 -->
            <?php
            $tags = get_the_tags();
            if (!empty($tags)):
            ?>
            <div class="post__tags">
                <?php foreach ($tags as $tag): ?>
                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post__tag">
                    <i class="ph ph-tag"></i> <?php echo esc_html($tag->name); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- 文章内容 -->
    <div class="post__body container">
        <div class="post__content">
            <!-- 封面图 -->
            <?php if (has_post_thumbnail()): ?>
            <div class="post__cover">
                <?php the_post_thumbnail('amorsum-cover', ['alt' => get_the_title()]); ?>
            </div>
            <?php endif; ?>

            <!-- 正文 -->
            <div class="post__article">
                <?php the_content(); ?>
            </div>

            <!-- 分页链接 -->
            <?php
            wp_link_pages([
                'before'      => '<div class="post__page-links"><span>' . esc_html__('分页：', 'amorsum') . '</span>',
                'after'       => '</div>',
                'link_before' => '<span class="post__page-number">',
                'link_after'  => '</span>',
            ]);
            ?>
        </div>
    </div>

    <!-- 文章底部 -->
    <footer class="post__footer container">
        <!-- 版权声明 -->
        <?php if (amorsum_theme('show_copyright', true)): ?>
        <div class="post__copyright glass-card">
            <div class="post__copyright-icon">
                <i class="ph ph-copyright"></i>
            </div>
            <div class="post__copyright-text">
                <p><strong><?php esc_html_e('本文作者：', 'amorsum'); ?></strong><?php echo esc_html(get_the_author()); ?></p>
                <p><strong><?php esc_html_e('本文链接：', 'amorsum'); ?></strong><a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a></p>
                <p><strong><?php esc_html_e('版权声明：', 'amorsum'); ?></strong><?php esc_html_e('本作品采用', 'amorsum'); ?> <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank" rel="noopener">CC BY-NC-SA 4.0</a> <?php esc_html_e('许可协议，转载请注明出处。', 'amorsum'); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- 上一篇 / 下一篇 -->
        <div class="post__nav">
            <?php
            $prev_post = get_previous_post();
            $next_post = get_next_post();
            ?>
            <?php if ($prev_post): ?>
            <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="post__nav-link post__nav-link--prev">
                <span class="post__nav-label"><i class="ph ph-caret-left"></i> <?php esc_html_e('上一篇', 'amorsum'); ?></span>
                <span class="post__nav-title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
            </a>
            <?php else: ?>
            <div class="post__nav-link post__nav-link--placeholder"></div>
            <?php endif; ?>

            <?php if ($next_post): ?>
            <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="post__nav-link post__nav-link--next">
                <span class="post__nav-label"><?php esc_html_e('下一篇', 'amorsum'); ?> <i class="ph ph-caret-right"></i></span>
                <span class="post__nav-title"><?php echo esc_html(get_the_title($next_post)); ?></span>
            </a>
            <?php endif; ?>
        </div>

        <!-- 相关文章 -->
        <?php
        $related = amorsum_get_related_posts();
        if (!empty($related)):
        ?>
        <div class="post__related">
            <h3 class="post__related-title"><?php esc_html_e('相关文章', 'amorsum'); ?></h3>
            <div class="post__related-grid">
                <?php foreach ($related as $post): setup_postdata($post); ?>
                <a href="<?php the_permalink(); ?>" class="post__related-card glass-card">
                    <h4><?php the_title(); ?></h4>
                    <time><?php echo get_the_date('Y-m-d'); ?></time>
                </a>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- 评论区 -->
        <?php
        if (comments_open() || get_comments_number()):
            comments_template();
        endif;
        ?>
    </footer>

</article>
<?php endwhile; ?>

<?php
get_sidebar();
get_footer();
