<?php
/**
 * 搜索表单模板 — Search Form
 *
 * @package Amorsum
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="search-form__inner">
        <i class="ph ph-magnifying-glass search-form__icon"></i>
        <input type="search"
               class="search-form__input"
               placeholder="<?php esc_attr_e('搜索文章...', 'amorsum'); ?>"
               value="<?php echo get_search_query(); ?>"
               name="s"
               aria-label="<?php esc_attr_e('搜索', 'amorsum'); ?>" />
        <button type="submit" class="search-form__submit">
            <?php esc_html_e('搜索', 'amorsum'); ?>
        </button>
    </div>
</form>
