<?php
/**
 * 文章卡片组件 — Post Card
 *
 * @package Amorsum
 */
?>
<article <?php post_class('post-card glass-card animate-fade-up'); ?>>
    <?php if (has_post_thumbnail()): ?>
    <div class="post-card__cover">
        <a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php the_post_thumbnail('amorsum-card', ['alt' => get_the_title(), 'loading' => 'lazy']); ?>
        </a>
    </div>
    <?php endif; ?>

    <div class="post-card__body">
        <!-- 元信息 -->
        <div class="post-card__meta">
            <?php
            $categories = get_the_category();
            if (!empty($categories)):
            ?>
            <a href="<?php echo esc_url(get_category_link($categories[0]->term_id)); ?>" class="post-card__category">
                <i class="ph ph-folder"></i>
                <?php echo esc_html($categories[0]->name); ?>
            </a>
            <?php endif; ?>
            <time class="post-card__date" datetime="<?php echo get_the_date('Y-m-d'); ?>">
                <i class="ph ph-calendar"></i>
                <?php echo get_the_date('Y-m-d'); ?>
            </time>
        </div>

        <!-- 标题 -->
        <h2 class="post-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <!-- 摘要 -->
        <?php if (has_excerpt() || get_the_excerpt()): ?>
        <p class="post-card__excerpt"><?php echo esc_html(amorsum_excerpt(150)); ?></p>
        <?php endif; ?>

        <!-- 标签 -->
        <?php
        $tags = get_the_tags();
        if (!empty($tags)):
        ?>
        <div class="post-card__tags">
            <?php
            $i = 0;
            foreach ($tags as $tag):
                if ($i++ >= 4) break; // 最多显示4个标签
            ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-card__tag">#<?php echo esc_html($tag->name); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- 阅读更多 -->
        <a href="<?php the_permalink(); ?>" class="post-card__readmore">
            <?php esc_html_e('阅读更多', 'amorsum'); ?> <i class="ph ph-arrow-right"></i>
        </a>
    </div>
</article>
