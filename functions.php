<?php
/**
 * Amorsum Theme — 现代科技风 WordPress 主题
 *
 * @package Amorsum
 */

// ============================================
// 1. 主题初始化
// ============================================
function amorsum_setup() {
    // 让 WordPress 管理 <title> 标签
    add_theme_support('title-tag');

    // 缩略图支持
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 450, true); // 16:9
    add_image_size('amorsum-card', 640, 360, true);
    add_image_size('amorsum-cover', 1200, 675, true);

    // HTML5 语义标签
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);

    // 自定义 Logo
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // RSS Feed
    add_theme_support('automatic-feed-links');

    // 编辑器样式
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');

    // 注册菜单位置
    register_nav_menus([
        'primary' => esc_html__('主导航菜单', 'amorsum'),
        'footer'  => esc_html__('页脚菜单', 'amorsum'),
        'social'  => esc_html__('社交链接菜单', 'amorsum'),
    ]);
}
add_action('after_setup_theme', 'amorsum_setup');

// ============================================
// 2. 加载 CSS / JS
// ============================================
function amorsum_scripts() {
    // 主样式
    wp_enqueue_style(
        'amorsum-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme()->get('Version')
    );

    // Phosphor Icons
    wp_enqueue_script(
        'phosphor-icons',
        'https://unpkg.com/@phosphor-icons/web@2.1.1',
        [],
        null,
        true
    );

    // 主题脚本
    wp_enqueue_script(
        'amorsum-main',
        get_template_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'amorsum_scripts');

// 预连接 Google Fonts（性能优化）
function amorsum_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'amorsum_preconnect', 1);

// 加载 Google Fonts
function amorsum_fonts() {
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <?php
}
add_action('wp_head', 'amorsum_fonts', 5);

// ============================================
// 3. 注册侧栏（Widget Areas）
// ============================================
function amorsum_widgets_init() {
    register_sidebar([
        'name'          => esc_html__('侧栏', 'amorsum'),
        'id'            => 'sidebar-main',
        'description'   => esc_html__('博客主侧栏，显示在文章列表和文章详情页侧边', 'amorsum'),
        'before_widget' => '<div id="%1$s" class="sidebar__widget glass-card %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="sidebar__widget-title">',
        'after_title'   => '</h4>',
    ]);

    register_sidebar([
        'name'          => esc_html__('页脚 - 左', 'amorsum'),
        'id'            => 'footer-left',
        'before_widget' => '<div class="footer__widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer__widget-title">',
        'after_title'   => '</h4>',
    ]);

    register_sidebar([
        'name'          => esc_html__('页脚 - 中', 'amorsum'),
        'id'            => 'footer-center',
        'before_widget' => '<div class="footer__widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer__widget-title">',
        'after_title'   => '</h4>',
    ]);

    register_sidebar([
        'name'          => esc_html__('页脚 - 右', 'amorsum'),
        'id'            => 'footer-right',
        'before_widget' => '<div class="footer__widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer__widget-title">',
        'after_title'   => '</h4>',
    ]);
}
add_action('widgets_init', 'amorsum_widgets_init');

// ============================================
// 4. 主题自定义设置 (Customizer API)
// ============================================
function amorsum_customize_register($wp_customize) {

    // --- 主题外观 ---
    $wp_customize->add_section('amorsum_appearance', [
        'title'    => esc_html__('主题外观', 'amorsum'),
        'priority' => 30,
    ]);

    // 主题模式
    $wp_customize->add_setting('amorsum_theme_mode', [
        'default'           => 'dark',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ]);
    $wp_customize->add_control('amorsum_theme_mode', [
        'label'   => esc_html__('主题模式', 'amorsum'),
        'section' => 'amorsum_appearance',
        'type'    => 'select',
        'choices' => [
            'dark'  => esc_html__('深色（默认）', 'amorsum'),
            'light' => esc_html__('浅色', 'amorsum'),
            'auto'  => esc_html__('跟随系统', 'amorsum'),
        ],
    ]);

    // --- 首页 Hero 设置 ---
    $wp_customize->add_section('amorsum_hero', [
        'title'    => esc_html__('首页 Hero', 'amorsum'),
        'priority' => 35,
    ]);

    // Home 布局
    $wp_customize->add_setting('amorsum_home_layout', [
        'default'           => 'hero',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('amorsum_home_layout', [
        'label'   => esc_html__('首页布局', 'amorsum'),
        'section' => 'amorsum_hero',
        'type'    => 'select',
        'choices' => [
            'hero' => esc_html__('Hero 大标题 + 文章列表', 'amorsum'),
            'list' => esc_html__('纯文章列表', 'amorsum'),
        ],
    ]);

    // Hero 标题（JSON 数组）
    $wp_customize->add_setting('amorsum_hero_title', [
        'default'           => '["Hello, World.", "Welcome to Amorsum."]',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('amorsum_hero_title', [
        'label'       => esc_html__('Hero 标题（JSON 数组）', 'amorsum'),
        'description' => esc_html__('打字机效果的多段标题，格式：["文字1", "文字2", ...]', 'amorsum'),
        'section'     => 'amorsum_hero',
        'type'        => 'text',
    ]);

    // Hero 副标题
    $wp_customize->add_setting('amorsum_hero_subtitle', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('amorsum_hero_subtitle', [
        'label'   => esc_html__('Hero 副标题', 'amorsum'),
        'section' => 'amorsum_hero',
        'type'    => 'text',
    ]);

    // --- 文章设置 ---
    $wp_customize->add_section('amorsum_post', [
        'title'    => esc_html__('文章设置', 'amorsum'),
        'priority' => 40,
    ]);

    $wp_customize->add_setting('amorsum_show_toc', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('amorsum_show_toc', [
        'label'   => esc_html__('显示文章目录 (TOC)', 'amorsum'),
        'section' => 'amorsum_post',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('amorsum_show_copyright', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('amorsum_show_copyright', [
        'label'   => esc_html__('显示版权声明', 'amorsum'),
        'section' => 'amorsum_post',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('amorsum_related_count', [
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('amorsum_related_count', [
        'label'   => esc_html__('相关文章数量', 'amorsum'),
        'section' => 'amorsum_post',
        'type'    => 'number',
        'input_attrs' => ['min' => 0, 'max' => 6],
    ]);

    // --- 页脚设置 ---
    $wp_customize->add_section('amorsum_footer', [
        'title'    => esc_html__('页脚设置', 'amorsum'),
        'priority' => 45,
    ]);

    $wp_customize->add_setting('amorsum_footer_text', [
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('amorsum_footer_text', [
        'label'   => esc_html__('页脚额外文字（如 ICP 备案）', 'amorsum'),
        'section' => 'amorsum_footer',
        'type'    => 'textarea',
    ]);

    // --- 背景设置 ---
    $wp_customize->add_section('amorsum_background', [
        'title'    => esc_html__('背景设置', 'amorsum'),
        'priority' => 46,
    ]);

    // 背景视频开关
    $wp_customize->add_setting('amorsum_bg_video_enable', [
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('amorsum_bg_video_enable', [
        'label'   => esc_html__('启用背景视频', 'amorsum'),
        'section' => 'amorsum_background',
        'type'    => 'checkbox',
    ]);

    // 背景视频 URL
    $wp_customize->add_setting('amorsum_bg_video_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('amorsum_bg_video_url', [
        'label'       => esc_html__('背景视频地址', 'amorsum'),
        'description' => esc_html__('上传 mp4/webm 到媒体库，粘贴文件地址。建议 1080p、10 秒以内循环片段。', 'amorsum'),
        'section'     => 'amorsum_background',
        'type'        => 'url',
    ]);

    // 背景海报图（视频加载前的静态占位图）
    $wp_customize->add_setting('amorsum_bg_video_poster', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('amorsum_bg_video_poster', [
        'label'       => esc_html__('背景海报图（可选）', 'amorsum'),
        'description' => esc_html__('视频加载前显示的静态图片，避免黑屏。建议截取视频一帧作为海报。', 'amorsum'),
        'section'     => 'amorsum_background',
        'type'        => 'url',
    ]);

    // 视频叠加不透明度
    $wp_customize->add_setting('amorsum_bg_video_opacity', [
        'default'           => 60,
        'sanitize_callback' => 'absint',
    ]);
    $wp_customize->add_control('amorsum_bg_video_opacity', [
        'label'       => esc_html__('背景遮盖深度（%）', 'amorsum'),
        'description' => esc_html__('数值越高，视频越暗/越模糊，文字越清晰。建议 40~80。', 'amorsum'),
        'section'     => 'amorsum_background',
        'type'        => 'range',
        'input_attrs' => ['min' => 0, 'max' => 100, 'step' => 5],
    ]);

    // 显示/隐藏装饰元素
    $wp_customize->add_setting('amorsum_show_bg_decor', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('amorsum_show_bg_decor', [
        'label'   => esc_html__('显示背景装饰（光球 + 网格）', 'amorsum'),
        'section' => 'amorsum_background',
        'type'    => 'checkbox',
    ]);
}
add_action('customize_register', 'amorsum_customize_register');

// ============================================
// 5. Helper 函数
// ============================================

// 获取主题设置值
function amorsum_theme($setting, $default = null) {
    $defaults = [
        'theme_mode'       => 'dark',
        'home_layout'      => 'hero',
        'hero_title'       => '["Hello, World.", "Welcome to Amorsum."]',
        'hero_subtitle'    => 'Developer · Writer · Dreamer',
        'show_toc'         => true,
        'show_copyright'   => true,
        'related_count'    => 3,
        'footer_text'      => '',
        'bg_video_enable'   => false,
        'bg_video_url'      => '',
        'bg_video_poster'   => '',
        'bg_video_opacity' => 60,
        'show_bg_decor'    => true,
    ];
    $default = $default ?? ($defaults[$setting] ?? '');
    return get_theme_mod('amorsum_' . $setting, $default);
}

// 计算阅读时间
function amorsum_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $content = wp_strip_all_tags($content);
    $word_count = mb_strlen(preg_replace('/\s+/', '', $content));
    // 中文按每分钟 400 字，英文按每分钟 250 词
    $minutes = max(1, ceil($word_count / 400));
    return $minutes;
}

// 获取文章摘要（带长度限制）
function amorsum_excerpt($length = 150) {
    $excerpt = get_the_excerpt();
    if (empty($excerpt)) {
        $excerpt = wp_strip_all_tags(get_the_content());
    }
    return mb_substr($excerpt, 0, $length) . '...';
}

// 获取相关文章
function amorsum_get_related_posts($post_id = null, $count = 3) {
    if (!$post_id) $post_id = get_the_ID();
    $count = amorsum_theme('related_count', 3);
    if ($count <= 0) return [];

    $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);
    $cats = wp_get_post_categories($post_id, ['fields' => 'ids']);

    $args = [
        'post__not_in'   => [$post_id],
        'posts_per_page' => $count,
        'ignore_sticky_posts' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if (!empty($tags)) {
        $args['tag__in'] = $tags;
    } elseif (!empty($cats)) {
        $args['category__in'] = $cats;
    }

    return get_posts($args);
}

// 生成文章目录 (TOC)
function amorsum_generate_toc($content) {
    if (!preg_match_all('/<h([2-4])[^>]*>(.*?)<\/h[2-4]>/i', $content, $matches, PREG_SET_ORDER)) {
        return false;
    }

    $toc = '<nav class="toc"><ul class="toc__list">';
    $min_level = 6;

    foreach ($matches as $match) {
        $level = $match[1];
        $title = wp_strip_all_tags($match[2]);
        $slug  = sanitize_title($title);
        $min_level = min($min_level, $level);
    }

    foreach ($matches as $match) {
        $level = $match[1];
        $title = wp_strip_all_tags($match[2]);
        $slug  = sanitize_title($title);
        $indent = $level - $min_level;

        $toc .= '<li class="toc__item toc__item--level' . $indent . '">';
        $toc .= '<a href="#' . $slug . '">' . $title . '</a>';
        $toc .= '</li>';
    }

    $toc .= '</ul></nav>';
    return $toc;
}

// 在文章内容中给标题添加 id（配合 TOC）
function amorsum_add_heading_ids($content) {
    return preg_replace_callback('/<h([2-4])([^>]*)>(.*?)<\/h[2-4]>/i', function($matches) {
        $level = $matches[1];
        $attrs = $matches[2];
        $title = wp_strip_all_tags($matches[3]);
        $slug  = sanitize_title($title);

        // 避免重复 id
        if (strpos($attrs, 'id=') === false) {
            $attrs .= ' id="' . $slug . '"';
        }

        return "<h{$level}{$attrs}>{$matches[3]}</h{$level}>";
    }, $content);
}
add_filter('the_content', 'amorsum_add_heading_ids', 5);

// 获取年月归档
function amorsum_get_archives_grouped() {
    global $wpdb;
    $query = "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
              FROM $wpdb->posts
              WHERE post_type = 'post' AND post_status = 'publish'
              ORDER BY post_date DESC";
    $results = $wpdb->get_results($query);

    $archives = [];
    foreach ($results as $row) {
        $archives[(int)$row->year][] = (int)$row->month;
    }
    return $archives;
}

// ============================================
// 6. 自定义导航 Walker（适配主题 class）
// ============================================
class Amorsum_Nav_Walker extends Walker_Nav_Menu {

    public function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="header__nav-submenu">';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes   = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'header__nav-link';
        if (in_array('current-menu-item', $classes) || in_array('current_page_item', $classes)) {
            $classes[] = 'active';
        }

        $class_names = implode(' ', array_filter($classes));
        $output .= '<li>';
        $output .= '<a href="' . esc_url($item->url) . '" class="' . esc_attr($class_names) . '">';
        $output .= '<span>' . esc_html($item->title) . '</span>';
        $output .= '</a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= '</li>';
    }
}

// ============================================
// 7. 评论回调函数
// ============================================
function amorsum_comment_callback($comment, $args, $depth) {
    $tag = ($args['style'] === 'ol' || $args['style'] === 'ul') ? 'li' : 'div';
    ?>
    <<?php echo $tag; ?> <?php comment_class('comments__item glass-card'); ?> id="comment-<?php comment_ID(); ?>">
        <div class="comments__item-inner">
            <div class="comments__avatar">
                <?php echo get_avatar($comment, $args['avatar_size'], '', '', ['class' => 'comments__avatar-img']); ?>
            </div>
            <div class="comments__body">
                <div class="comments__meta">
                    <span class="comments__author"><?php comment_author_link(); ?></span>
                    <time class="comments__date" datetime="<?php comment_time('c'); ?>">
                        <?php printf(esc_html__('%1$s', 'amorsum'), get_comment_date('Y-m-d H:i')); ?>
                    </time>
                    <?php if ($comment->comment_approved === '0'): ?>
                    <span class="comments__pending"><?php esc_html_e('审核中', 'amorsum'); ?></span>
                    <?php endif; ?>
                </div>
                <div class="comments__text">
                    <?php comment_text(); ?>
                </div>
                <div class="comments__reply">
                    <?php
                    comment_reply_link(array_merge($args, [
                        'reply_text' => '<i class="ph ph-arrow-bend-up-right"></i> ' . esc_html__('回复', 'amorsum'),
                        'depth'      => $depth,
                        'max_depth'  => $args['max_depth'],
                    ]));
                    ?>
                </div>
            </div>
        </div>
    <?php
    // 注意：闭合标签 </li> 由 WordPress 自动处理
}

// ============================================
// 8. 文章阅读量（基于自定义字段）
// ============================================
function amorsum_get_post_views($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    $views = get_post_meta($post_id, 'amorsum_views', true);
    return $views ? intval($views) : 0;
}

function amorsum_increment_post_views() {
    if (!is_single() || is_bot()) return;

    $post_id = get_the_ID();
    $views   = get_post_meta($post_id, 'amorsum_views', true);
    $views   = $views ? intval($views) + 1 : 1;
    update_post_meta($post_id, 'amorsum_views', $views);
}
add_action('wp_head', 'amorsum_increment_post_views');

function is_bot() {
    if (empty($_SERVER['HTTP_USER_AGENT'])) return false;
    $bots = ['bot', 'spider', 'crawler', 'slurp', 'mediapartners', 'google', 'baidu', 'bing', 'yandex', 'duckduck'];
    $ua   = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($bots as $bot) {
        if (strpos($ua, $bot) !== false) return true;
    }
    return false;
}

// ============================================
// 9. AJAX: 保存主题偏好
// ============================================
function amorsum_save_theme_preference() {
    check_ajax_referer('amorsum_theme_nonce', '_wpnonce');

    $theme = isset($_POST['theme']) ? sanitize_text_field($_POST['theme']) : '';
    if (in_array($theme, ['dark', 'light'])) {
        if (is_user_logged_in()) {
            update_user_meta(get_current_user_id(), 'amorsum_theme', $theme);
        }
        setcookie('amorsum-theme', $theme, time() + YEAR_IN_SECONDS, '/');
    }
    wp_die();
}
add_action('wp_ajax_amorsum_save_theme', 'amorsum_save_theme_preference');
add_action('wp_ajax_nopriv_amorsum_save_theme', 'amorsum_save_theme_preference');

// ============================================
// 10. AJAX: 实时搜索
// ============================================
function amorsum_live_search() {
    check_ajax_referer('amorsum_theme_nonce', '_wpnonce');

    $query = sanitize_text_field($_GET['q'] ?? '');
    if (mb_strlen($query) < 2) {
        wp_send_json_success(['results' => [], 'query' => $query]);
    }

    $search = new WP_Query([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 8,
        's'              => $query,
        'orderby'        => 'relevance',
    ]);

    $results = [];
    if ($search->have_posts()) {
        while ($search->have_posts()) {
            $search->the_post();
            $title   = get_the_title();
            $content = wp_strip_all_tags(get_the_content());
            $url     = get_permalink();
            $date    = get_the_date('Y-m-d');

            // 判断关键词是否在标题中
            $in_title = (mb_stripos($title, $query) !== false);

            // 如果关键词不在标题中，提取内容中的上下文
            $excerpt = '';
            if (!$in_title) {
                $pos = mb_stripos($content, $query);
                if ($pos !== false) {
                    $start  = max(0, $pos - 35);
                    $length = mb_strlen($query) + 70;
                    $snippet = mb_substr($content, $start, $length);
                    // 清理：截断到最近的完整字符边界
                    if ($start > 0) $snippet = '…' . $snippet;
                    if (($start + $length) < mb_strlen($content)) $snippet .= '…';
                    $excerpt = $snippet;
                }
            }

            $results[] = [
                'title'    => $title,
                'url'      => $url,
                'date'     => $date,
                'in_title' => $in_title,
                'excerpt'  => $excerpt,
            ];
        }
        wp_reset_postdata();
    }

    wp_send_json_success(compact('results', 'query'));
}
add_action('wp_ajax_amorsum_live_search', 'amorsum_live_search');
add_action('wp_ajax_nopriv_amorsum_live_search', 'amorsum_live_search');

// 本地化 AJAX URL 和 nonce
function amorsum_localize_script() {
    wp_localize_script('amorsum-main', 'amorsumAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('amorsum_theme_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'amorsum_localize_script', 20);
