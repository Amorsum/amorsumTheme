<?php
/**
 * 头部模板 — Header
 *
 * @package Amorsum
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr(amorsum_theme('theme_mode', 'dark')); ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="view-transition" content="same-origin">
    <!-- 防止主题闪烁：在 CSS 加载前立即应用用户主题偏好 -->
    <script>
    (function(){var t=localStorage.getItem('amorsum-theme');if(!t){var a=document.documentElement.getAttribute('data-theme');if(a==='auto'){t=window.matchMedia('(prefers-color-scheme:light)').matches?'light':'dark'}else{t=a||'dark'}}if(t==='auto'){t=window.matchMedia('(prefers-color-scheme:light)').matches?'light':'dark'}document.documentElement.setAttribute('data-theme',t)})()
    </script>
    <?php wp_head(); ?>
    <?php
    $bg_image = amorsum_theme('bg_image_url', '');
    $has_bg   = !empty($bg_image) || (amorsum_theme('bg_video_enable') && amorsum_theme('bg_video_url'));
    if ($has_bg):
        $opacity = floatval(intval(amorsum_theme('bg_video_opacity', 60)) / 100);
    ?>
    <style>
        :root{--video-overlay-opacity:<?php echo $opacity; ?>}
        <?php if (!empty($bg_image)): ?>body.has-bg-static{background-image:url(<?php echo esc_url($bg_image); ?>);background-size:cover;background-position:center;background-repeat:no-repeat}<?php endif; ?>
    </style>
    <?php endif; ?>
</head>
<body <?php body_class(!empty($bg_image) ? 'has-bg-static' : (amorsum_theme('bg_video_enable') && amorsum_theme('bg_video_url') ? 'has-bg-video' : '')); ?>>

<?php wp_body_open(); ?>

<!-- 跳过导航链接（可访问性） -->
<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e('跳至主内容', 'amorsum'); ?>
</a>

<?php
$bg_image = amorsum_theme('bg_image_url', '');
if (!empty($bg_image)):
    // 静态壁纸 — 通过 CSS 变量挂到 body::before，全设备通用
?>
<?php elseif (amorsum_theme('bg_video_enable') && amorsum_theme('bg_video_url')): ?>
<!-- 背景动态壁纸（Canvas 渲染，浏览器不会识别为视频） -->
<?php $poster = amorsum_theme('bg_video_poster', ''); ?>
<canvas class="bg-video" id="bgCanvas" data-src="<?php echo esc_url(amorsum_theme('bg_video_url')); ?>"<?php if ($poster): ?> data-poster="<?php echo esc_url($poster); ?>" style="background-image:url(<?php echo esc_url($poster); ?>);background-size:cover;background-position:center"<?php endif; ?>></canvas>
<!-- 视频叠加层（保证文字可读性） -->
<div class="bg-video-overlay"></div>
<?php endif; ?>

<?php if (amorsum_theme('show_bg_decor', true)): ?>
<!-- 背景装饰 -->
<div class="bg-decor">
    <div class="bg-decor__orb bg-decor__orb--1"></div>
    <div class="bg-decor__orb bg-decor__orb--2"></div>
    <div class="bg-decor__grid"></div>
</div>
<?php endif; ?>

<div class="app">

<!-- 顶部导航栏 -->
<header class="header glass">
    <div class="header__inner container">
        <!-- Logo / 站点名 -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="header__logo">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <span class="header__logo-text"><?php bloginfo('name'); ?></span>
                <span class="header__logo-dot"></span>
            <?php endif; ?>
        </a>

        <!-- 主导航菜单 -->
        <nav class="header__nav" id="headerNav" aria-label="<?php esc_attr_e('主导航', 'amorsum'); ?>">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'menu_class'     => 'header__nav-list',
                    'depth'          => 2,
                    'fallback_cb'    => false,
                    'walker'         => new Amorsum_Nav_Walker(),
                ]);
            } else {
                // 默认菜单 — 通过页面别名动态获取链接，兼容所有固定链接格式
                echo '<ul class="header__nav-list">';
                // 首页
                echo '<li><a href="' . esc_url(home_url('/')) . '" class="header__nav-link' . (is_front_page() ? ' active' : '') . '"><span>' . esc_html__('首页', 'amorsum') . '</span></a></li>';
                // 归档/分类/关于 — 通过 slug 查找页面，自动适配 /index.php/archives/ 等格式
                $nav_pages = [
                    'archives'   => esc_html__('归档', 'amorsum'),
                    'categories' => esc_html__('分类', 'amorsum'),
                    'about'      => esc_html__('关于', 'amorsum'),
                ];
                foreach ($nav_pages as $slug => $label) {
                    $page = get_page_by_path($slug);
                    $url  = $page ? get_permalink($page) : home_url('/' . $slug);
                    $active = is_page($slug) ? ' active' : '';
                    echo '<li><a href="' . esc_url($url) . '" class="header__nav-link' . $active . '"><span>' . $label . '</span></a></li>';
                }
                echo '</ul>';
            }
            ?>
        </nav>

        <!-- 右侧操作 -->
        <div class="header__actions">
            <!-- 搜索按钮 -->
            <button class="header__action-btn" aria-label="<?php esc_attr_e('搜索', 'amorsum'); ?>" id="searchBtn">
                <i class="ph ph-magnifying-glass"></i>
            </button>

            <!-- 主题切换 -->
            <button class="header__action-btn" aria-label="<?php esc_attr_e('切换主题', 'amorsum'); ?>" id="themeToggle">
                <i class="ph ph-sun-dim"></i>
                <i class="ph ph-moon-stars"></i>
            </button>

            <!-- 移动端菜单按钮 -->
            <button class="header__action-btn header__menu-btn" aria-label="<?php esc_attr_e('菜单', 'amorsum'); ?>" id="mobileMenuBtn">
                <i class="ph ph-list"></i>
            </button>
        </div>
    </div>

    <!-- 移动端下拉菜单 -->
    <div class="header__mobile-menu" id="mobileMenu">
        <?php
        if (has_nav_menu('primary')) {
            wp_nav_menu([
                'theme_location' => 'primary',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'depth'          => 1,
                'fallback_cb'    => false,
                'link_before'    => '',
                'link_after'     => '',
            ]);
        }
        ?>
    </div>

    <!-- 搜索下拉面板 -->
    <div class="header__search-panel" id="searchPanel">
        <div class="container">
            <form class="header__search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" autocomplete="off">
                <i class="ph ph-magnifying-glass header__search-icon"></i>
                <input
                    type="search"
                    id="liveSearchInput"
                    name="s"
                    class="header__search-input"
                    placeholder="<?php esc_attr_e('输入关键词搜索文章...', 'amorsum'); ?>"
                    autocomplete="off"
                />
            </form>
            <!-- 实时搜索结果 -->
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>
</header>

<main id="main-content" class="app__main">
