<?php
/**
 * 评论区模板 — Comments
 *
 * @package Amorsum
 */

// 如果受到密码保护则不显示
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments">

    <?php if (have_comments()): ?>
    <h3 class="comments__title">
        <?php
        $comment_count = get_comments_number();
        printf(
            esc_html(_n('%d 条评论', '%d 条评论', $comment_count, 'amorsum')),
            $comment_count
        );
        ?>
    </h3>

    <!-- 评论列表 -->
    <ol class="comments__list">
        <?php
        wp_list_comments([
            'style'       => 'ol',
            'avatar_size' => 48,
            'short_ping'  => true,
            'callback'    => 'amorsum_comment_callback',
        ]);
        ?>
    </ol>

    <!-- 评论分页 -->
    <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
    <nav class="comments__nav">
        <?php
        paginate_comments_links([
            'prev_text' => '<i class="ph ph-caret-left"></i> ' . esc_html__('上一页', 'amorsum'),
            'next_text' => esc_html__('下一页', 'amorsum') . ' <i class="ph ph-caret-right"></i>',
        ]);
        ?>
    </nav>
    <?php endif; ?>

    <?php endif; // have_comments() ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')): ?>
    <p class="comments__closed"><?php esc_html_e('评论已关闭。', 'amorsum'); ?></p>
    <?php endif; ?>

    <?php
    // 评论表单
    comment_form([
        'title_reply'          => esc_html__('发表评论', 'amorsum'),
        'title_reply_before'   => '<h3 class="comments__reply-title">',
        'title_reply_after'    => '</h3>',
        'comment_notes_before' => '',
        'comment_notes_after'  => '',
        'class_submit'         => 'comments__submit-btn',
        'submit_button'        => '<button type="submit" class="comments__submit-btn">' . esc_html__('提交评论', 'amorsum') . '</button>',
        'comment_field'        => '<div class="comments__field"><textarea id="comment" name="comment" rows="5" placeholder="' . esc_attr__('写下你的想法...', 'amorsum') . '" required></textarea></div>',
        'fields'               => [
            'author' => '<div class="comments__field-row"><div class="comments__field"><input id="author" name="author" type="text" placeholder="' . esc_attr__('昵称 *', 'amorsum') . '" required /></div>',
            'email'  => '<div class="comments__field"><input id="email" name="email" type="email" placeholder="' . esc_attr__('邮箱 *', 'amorsum') . '" required /></div>',
            'url'    => '<div class="comments__field"><input id="url" name="url" type="url" placeholder="' . esc_attr__('网站', 'amorsum') . '" /></div></div>',
            'cookies' => '',
        ],
    ]);
    ?>
</div>
