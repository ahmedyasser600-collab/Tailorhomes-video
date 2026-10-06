<?php
/**
 * ════════════════════════════════════════════════════════════════════════
 *  Tailor Homes — Blog Post Image Slots
 *  File: inc/blog-image-slots.php
 *  Place in: /wp-content/themes/tailor-homes-theme/inc/blog-image-slots.php
 *  Then add to functions.php: require get_template_directory() . '/inc/blog-image-slots.php';
 * ════════════════════════════════════════════════════════════════════════
 *
 *  Adds a meta box to every blog post with FOUR named drag-and-drop image
 *  slots — exactly like the Customizer slots on the homepage.
 *
 *  Slots:
 *    • Section Image 1
 *    • Section Image 2
 *    • Section Image 3
 *    • Section Image 4 (optional — only used if filled)
 *
 *  Each slot is a media uploader (drag-drop, library, URL — same UI as
 *  Customizer). Once uploaded, place the image in the post body using the
 *  shortcode:
 *
 *    [th_image n="1"]                                   ← just the image
 *    [th_image n="1" caption="Prato della Valle"]       ← image + caption
 *    [th_image n="2" caption="..."]
 *
 *  Plus convenience function for the template:
 *    th_get_blog_image(1)  → returns ['url'=>..., 'alt'=>...] or null
 * ════════════════════════════════════════════════════════════════════════
 */

if (!defined('ABSPATH')) { exit; }

/* ─────────────────────────────────────────────────────────────────
   1. REGISTER THE META BOX
   ───────────────────────────────────────────────────────────────── */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'th_blog_image_slots',                              // ID
        'Tailor Homes — Blog Image Slots',                  // Title shown in editor
        'th_render_blog_image_slots_meta_box',              // Callback below
        'post',                                              // Post type (default blog posts)
        'side',                                              // Position: sidebar (like Featured Image)
        'high'                                               // Priority — show near top
    );
});

/* ─────────────────────────────────────────────────────────────────
   2. ENQUEUE THE WP MEDIA UPLOADER ON POST EDIT SCREENS
   ───────────────────────────────────────────────────────────────── */
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'])) { return; }
    wp_enqueue_media();
});

/* ─────────────────────────────────────────────────────────────────
   3. RENDER THE META BOX (HTML for the editor sidebar)
   ───────────────────────────────────────────────────────────────── */
function th_render_blog_image_slots_meta_box($post) {
    wp_nonce_field('th_blog_image_slots_save', 'th_blog_image_slots_nonce');
    ?>
    <style>
      .th-slot { margin: 14px 0; padding-bottom: 14px; border-bottom: 1px solid #ddd; }
      .th-slot:last-child { border-bottom: none; }
      .th-slot__label { display: block; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; color: #1d2327; margin-bottom: 6px; }
      .th-slot__hint  { font-size: 11px; color: #757575; margin-bottom: 8px; }
      .th-slot__preview { margin: 8px 0; min-height: 60px; background: #f0f0f1; border: 1px dashed #c3c4c7; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 2px; }
      .th-slot__preview img { max-width: 100%; height: auto; max-height: 120px; display: block; }
      .th-slot__preview-empty { color: #8c8f94; font-size: 12px; font-style: italic; }
      .th-slot__buttons { display: flex; gap: 6px; }
      .th-slot__buttons .button { flex: 1; }
      .th-slot__shortcode { display: block; margin-top: 6px; padding: 4px 8px; background: #1d2327; color: #f0f0f1; font-family: monospace; font-size: 11px; border-radius: 2px; user-select: all; }
    </style>

    <p style="font-size:12px;color:#646970;margin:0 0 12px;">
      Drag-and-drop image slots. After uploading, copy the shortcode and
      paste it anywhere in your post body where you want the image to appear.
    </p>

    <?php for ($i = 1; $i <= 4; $i++) :
        $image_id  = (int) get_post_meta($post->ID, "_th_blog_image_$i", true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $caption_val = get_post_meta($post->ID, "_th_blog_image_{$i}_caption", true);
    ?>
        <div class="th-slot" data-slot="<?php echo $i; ?>">
            <label class="th-slot__label">Section Image <?php echo $i; ?></label>
            <p class="th-slot__hint">For use inside the post body.</p>

            <div class="th-slot__preview" id="th-slot-preview-<?php echo $i; ?>">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="" />
                <?php else : ?>
                    <span class="th-slot__preview-empty">No image selected</span>
                <?php endif; ?>
            </div>

            <input
                type="hidden"
                name="th_blog_image_<?php echo $i; ?>"
                id="th-slot-input-<?php echo $i; ?>"
                value="<?php echo esc_attr($image_id); ?>"
            />

            <div class="th-slot__buttons">
                <button
                    type="button"
                    class="button th-slot__upload"
                    data-slot="<?php echo $i; ?>"
                ><?php echo $image_url ? 'Replace' : 'Upload / Choose'; ?></button>

                <button
                    type="button"
                    class="button th-slot__remove"
                    data-slot="<?php echo $i; ?>"
                    style="<?php echo $image_url ? '' : 'display:none;'; ?>"
                >Remove</button>
            </div>

            <input
                type="text"
                name="th_blog_image_<?php echo $i; ?>_caption"
                placeholder="Optional caption"
                value="<?php echo esc_attr($caption_val); ?>"
                style="width:100%;margin-top:8px;font-size:12px;"
            />

            <code class="th-slot__shortcode">[th_image n="<?php echo $i; ?>"]</code>
        </div>
    <?php endfor; ?>

    <script>
    (function ($) {
        $(document).ready(function () {
            $('.th-slot__upload').on('click', function (e) {
                e.preventDefault();
                var $btn = $(this);
                var slot = $btn.data('slot');

                var frame = wp.media({
                    title: 'Select Image for Section Image ' + slot,
                    button: { text: 'Use this image' },
                    multiple: false,
                    library: { type: 'image' }
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var url = attachment.sizes && attachment.sizes.medium
                        ? attachment.sizes.medium.url
                        : attachment.url;

                    $('#th-slot-input-' + slot).val(attachment.id);
                    $('#th-slot-preview-' + slot).html('<img src="' + url + '" alt="" />');
                    $btn.text('Replace');
                    $btn.siblings('.th-slot__remove').show();
                });

                frame.open();
            });

            $('.th-slot__remove').on('click', function (e) {
                e.preventDefault();
                var slot = $(this).data('slot');
                $('#th-slot-input-' + slot).val('');
                $('#th-slot-preview-' + slot).html('<span class="th-slot__preview-empty">No image selected</span>');
                $('.th-slot__upload[data-slot="' + slot + '"]').text('Upload / Choose');
                $(this).hide();
            });
        });
    })(jQuery);
    </script>
    <?php
}

/* ─────────────────────────────────────────────────────────────────
   4. SAVE META BOX VALUES WHEN POST IS SAVED
   ───────────────────────────────────────────────────────────────── */
add_action('save_post_post', function ($post_id) {
    if (!isset($_POST['th_blog_image_slots_nonce']) ||
        !wp_verify_nonce($_POST['th_blog_image_slots_nonce'], 'th_blog_image_slots_save')) { return; }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $post_id)) { return; }

    for ($i = 1; $i <= 4; $i++) {
        $image_field   = "th_blog_image_$i";
        $caption_field = "th_blog_image_{$i}_caption";

        if (isset($_POST[$image_field])) {
            $val = (int) $_POST[$image_field];
            if ($val > 0) {
                update_post_meta($post_id, "_th_blog_image_$i", $val);
            } else {
                delete_post_meta($post_id, "_th_blog_image_$i");
            }
        }

        if (isset($_POST[$caption_field])) {
            $cap = sanitize_text_field(wp_unslash($_POST[$caption_field]));
            if ($cap !== '') {
                update_post_meta($post_id, "_th_blog_image_{$i}_caption", $cap);
            } else {
                delete_post_meta($post_id, "_th_blog_image_{$i}_caption");
            }
        }
    }
});

/* ─────────────────────────────────────────────────────────────────
   5. HELPER FUNCTION (use in templates)
   ───────────────────────────────────────────────────────────────── */
function th_get_blog_image($n, $post_id = null) {
    if ($post_id === null) { $post_id = get_the_ID(); }
    $image_id = (int) get_post_meta($post_id, "_th_blog_image_$n", true);
    if (!$image_id) { return null; }

    $url = wp_get_attachment_image_url($image_id, 'large');
    if (!$url) { return null; }

    $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    if (!$alt) { $alt = get_the_title($post_id); }

    $caption = get_post_meta($post_id, "_th_blog_image_{$n}_caption", true);

    return ['url' => $url, 'alt' => $alt, 'caption' => $caption, 'id' => $image_id];
}

/* ─────────────────────────────────────────────────────────────────
   6. SHORTCODE — [th_image n="1"]
   ───────────────────────────────────────────────────────────────── */
add_shortcode('th_image', function ($atts) {
    $atts = shortcode_atts(['n' => '1', 'caption' => ''], $atts, 'th_image');
    $n = (int) $atts['n'];
    if ($n < 1 || $n > 4) { return ''; }

    $img = th_get_blog_image($n);
    if (!$img) { return ''; }

    // Use shortcode caption if provided, else fall back to the meta-box caption
    $caption = $atts['caption'] !== '' ? $atts['caption'] : $img['caption'];

    $html  = '<figure class="th-blog-img" style="margin:40px 0 12px;">';
    $html .=   '<div style="aspect-ratio:16/9;overflow:hidden;background:#E5E1DA;">';
    $html .=     '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" style="width:100%;height:100%;object-fit:cover;display:block;" />';
    $html .=   '</div>';
    if ($caption) {
        $html .= '<figcaption style="font-size:12px;color:#8A8780;text-align:center;font-style:italic;margin-top:8px;">' . esc_html($caption) . '</figcaption>';
    }
    $html .= '</figure>';

    return $html;
});
