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
    <!-- 防止主题闪烁：在 CSS 加载前立即应用用户主题偏好 -->
    <script>
    (function(){var t=localStorage.getItem('amorsum-theme');if(!t){var a=document.documentElement.getAttribute('data-theme');if(a==='auto'){t=window.matchMedia('(prefers-color-scheme:light)').matches?'light':'dark'}else{t=a||'dark'}}if(t==='auto'){t=window.matchMedia('(prefers-color-scheme:light)').matches?'light':'dark'}document.documentElement.setAttribute('data-theme',t)})()
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<!-- 跳过导航链接（可访问性） -->
<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e('跳至主内容', 'amorsum'); ?>
</a>

<!-- 背景装饰 -->
<div class="bg-decor">
    <div class="bg-decor__orb bg-decor__orb--1"></div>
    <div class="bg-decor__orb bg-decor__orb--2"></div>
    <div class="bg-decor__grid"></div>
</div>

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
                // 默认菜单
                echo '<ul class="header__nav-list">';
                $defaults = [
                    '首页' => home_url('/'),
                    '归档' => home_url('/archives'),
                    '分类' => home_url('/categories'),
                    '关于' => home_url('/about'),
                ];
                foreach ($defaults as $name => $url) {
                    $active = (is_home() && $name === '首页') || (is_page($name)) ? ' active' : '';
                    echo '<li><a href="' . esc_url($url) . '" class="header__nav-link' . $active . '"><span>' . esc_html($name) . '</span></a></li>';
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

            <!-- 搜索弹出层 -->
            <div class="header__search-overlay" id="searchOverlay">
                <div class="header__search-box">
                    <?php get_search_form(); ?>
                </div>
                <button class="header__search-close" id="searchClose">
                    <i class="ph ph-x"></i>
                </button>
            </div>

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
</header>

<main id="main-content" class="app__main">
