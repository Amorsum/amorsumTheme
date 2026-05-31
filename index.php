<?php
/**
 * 首页模板 — Index
 *
 * @package Amorsum
 */

get_header();

$home_layout = amorsum_theme('home_layout', 'hero');
?>

<?php if ($home_layout === 'hero' && is_home() && !is_paged()): ?>
<!-- Hero 区域 -->
<section class="hero">
    <div class="hero__inner container">
        <div class="hero__content">
            <h1 class="hero__title">
                <span class="hero__title-text" id="heroTypewriter">
                    <?php
                    $hero_title = amorsum_theme('hero_title', '["Hello, World.", "Welcome to Amorsum."]');
                    $titles = json_decode($hero_title, true);
                    echo esc_html(is_array($titles) && !empty($titles) ? $titles[0] : 'Hello, World.');
                    ?>
                </span>
                <span class="hero__title-cursor">|</span>
            </h1>
            <p class="hero__subtitle"><?php echo esc_html(amorsum_theme('hero_subtitle', '')); ?></p>
            <div class="hero__actions">
                <?php $archives_page = get_page_by_path('archives'); ?>
                <a href="<?php echo esc_url($archives_page ? get_permalink($archives_page) : home_url('/archives')); ?>" class="hero__btn hero__btn--primary">
                    <span><?php esc_html_e('浏览文章', 'amorsum'); ?></span>
                    <i class="ph ph-arrow-right"></i>
                </a>
                <?php $about_page = get_page_by_path('about'); ?>
                <a href="<?php echo esc_url($about_page ? get_permalink($about_page) : home_url('/about')); ?>" class="hero__btn hero__btn--ghost">
                    <span><?php esc_html_e('关于我', 'amorsum'); ?></span>
                </a>
            </div>

            <!-- 打字机多项文本 -->
            <?php
            $hero_title = amorsum_theme('hero_title', '["Hello, World.", "Welcome to Amorsum."]');
            $titles = json_decode($hero_title, true);
            if (is_array($titles) && count($titles) > 1):
            ?>
            <script id="heroTypewriterData" type="application/json"><?php echo wp_json_encode($titles); ?></script>
            <?php endif; ?>
        </div>

        <!-- Hero 装饰图形 -->
        <div class="hero__visual">
            <div class="hero__orb hero__orb--large"></div>
            <div class="hero__orb hero__orb--small"></div>
            <div class="hero__ring"></div>
        </div>
    </div>

    <div class="hero__scroll-hint">
        <span>Scroll</span>
        <div class="hero__scroll-line"></div>
    </div>
</section>
<?php endif; ?>

<!-- 文章列表 -->
<section class="post-list">
    <div class="container">
        <?php if ($home_layout === 'hero' && is_home() && !is_paged()): ?>
        <h2 class="section-title">
            <span class="section-title__text"><?php esc_html_e('最新文章', 'amorsum'); ?></span>
            <span class="section-title__line"></span>
        </h2>
        <?php endif; ?>

        <?php if (have_posts()): ?>
        <div class="post-list__grid">
            <?php while (have_posts()): the_post(); ?>
                <?php get_template_part('template-parts/post-card'); ?>
            <?php endwhile; ?>
        </div>

        <!-- 分页 -->
        <?php
        $pagination = paginate_links([
            'prev_text' => '<i class="ph ph-caret-left"></i> ' . esc_html__('上一页', 'amorsum'),
            'next_text' => esc_html__('下一页', 'amorsum') . ' <i class="ph ph-caret-right"></i>',
            'type'      => 'array',
        ]);
        if ($pagination):
        ?>
        <nav class="pagination" aria-label="<?php esc_attr_e('分页导航', 'amorsum'); ?>">
            <?php
            foreach ($pagination as $link) {
                if (strpos($link, 'current') !== false) {
                    echo '<span class="page-numbers current">' . wp_kses_post($link) . '</span>';
                } else {
                    // 将 span/class 转换为主题 class
                    $link = str_replace('page-numbers', 'page-number', $link);
                    $link = str_replace('prev ', 'extend prev ', $link);
                    $link = str_replace('next ', 'extend next ', $link);
                    echo $link;
                }
            }
            ?>
        </nav>
        <?php endif; ?>

        <?php else: ?>
            <?php get_template_part('template-parts/none'); ?>
        <?php endif; ?>
    </div>
</section>

<?php
get_sidebar();
get_footer();
