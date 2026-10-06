<?php
require get_template_directory() . '/inc/blog-image-slots.php';
require get_template_directory() . '/inc/seed-student-housing-blogs.php';
require get_template_directory() . '/inc/seed-booking-blogs.php';
/**
 * Tailor Homes Theme Functions
 */

/**
 * Get the Polylang-translated permalink for a page by its slug.
 * Falls back to home_url('/slug') if Polylang is not active or page not found.
 *
 * Usage: th_url('contact')  →  returns /it/contatti/ when language is IT
 */
function th_url($slug) {
    // Map of English slugs → Italian slugs (since IT is default language)
    $slug_map = array(
        'apartments'          => 'appartamenti',
        'about-us'            => 'chi-siamo',
        'our-services'        => 'i-nostri-servizi',
        'contact'             => 'contatti',
        'work-with-us'        => 'lavora-con-noi',
        'students'            => 'studenti',
        'owners'              => 'proprietari',
        'students-unipd'      => 'studenti-unipd',
        'students-erasmus'    => 'studenti-erasmus',
        'corporate-housing'   => 'alloggi-aziendali',
        'privacy-policy'      => 'privacy-policy',
        'terms-and-conditions' => 'termini-e-condizioni',
        'blog'                => 'blog',
    );

    // Try the given slug first
    $page = get_page_by_path($slug);

    // If not found, try the mapped Italian slug
    if (!$page && isset($slug_map[$slug])) {
        $page = get_page_by_path($slug_map[$slug]);
    }

    // If still not found, try the reverse map (Italian → English)
    if (!$page) {
        $reverse = array_search($slug, $slug_map);
        if ($reverse) {
            $page = get_page_by_path($reverse);
        }
    }

    if ($page && function_exists('pll_get_post')) {
        $translated_id = pll_get_post($page->ID);
        if ($translated_id) {
            return get_permalink($translated_id);
        }
        return get_permalink($page->ID);
    }
    if ($page) {
        return get_permalink($page->ID);
    }
    return home_url('/' . $slug);
}

/**
 * Krossbooking direct booking engine URL.
 * Change it here once and every Book Now / Prenota Ora button follows.
 */
function th_booking_url() {
    return apply_filters('th_booking_url', 'https://tailorhomes-new.kross.travel/');
}

/**
 * Label for the booking CTA, in the current language.
 */
function th_booking_label() {
    $it = (function_exists('pll_current_language') && pll_current_language() === 'it');
    return $it ? 'Prenota Ora' : 'Book Now';
}

/**
 * Ready-made booking CTA anchor for the header / mobile menus.
 */
function th_booking_cta_link($extra_class = '') {
    $class = trim('th-cta-btn th-book-btn ' . $extra_class);
    return '<a href="' . esc_url(th_booking_url()) . '" class="' . esc_attr($class) . '" target="_blank" rel="noopener">' . esc_html(th_booking_label()) . '</a>';
}

/**
 * Shortcodes for use inside blog post content, so URLs are never hardcoded per language.
 *   [th_book_url]                     → the Krossbooking engine URL
 *   [th_page slug='apartments']       → the correct IT/EN permalink for a theme page
 */
add_shortcode('th_book_url', function () {
    return esc_url(th_booking_url());
});

add_shortcode('th_page', function ($atts) {
    $atts = shortcode_atts(array('slug' => ''), $atts, 'th_page');
    if (empty($atts['slug'])) { return esc_url(home_url('/')); }
    return esc_url(th_url($atts['slug']));
});

function tailorhomes_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height' => 200, 'width' => 500, 'flex-height' => true, 'flex-width' => true,
    ));
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'tailorhomes'),
        'footer'  => __('Footer Menu', 'tailorhomes'),
    ));
}
add_action('after_setup_theme', 'tailorhomes_setup');

/* -----------------------------------------------
   PERFORMANCE HELPERS
   ----------------------------------------------- */
/**
 * Self-hosted fonts (assets/fonts, SIL Open Font License — see OFL-*.txt there).
 * Latin variable fonts subset to the characters Italian/English use: ~150 KB total vs ~250 KB
 * from Google, no extra DNS/TLS connections, and the hero heading font is preloaded so the
 * LCP element (the h1 tagline) paints in its real font straight away.
 */
function th_font_face_css() {
    $base = get_template_directory_uri() . '/assets/fonts/';
    $faces = array(
        array('DM Sans', 'normal', '300 800', 'dm-sans.woff2'),
        array('DM Sans', 'italic', '300 400', 'dm-sans-italic.woff2'),
        array('Jost', 'normal', '300 500', 'jost.woff2'),
        array('Cormorant Garamond', 'normal', '300 500', 'cormorant-garamond.woff2'),
        array('Cormorant Garamond', 'italic', '300 400', 'cormorant-garamond-italic.woff2'),
    );
    $css = '';
    foreach ($faces as $f) {
        $css .= "@font-face{font-family:'{$f[0]}';font-style:{$f[1]};font-weight:{$f[2]};font-display:swap;src:url({$base}{$f[3]}) format('woff2')}";
    }
    return $css;
}

// Preload the two fonts visible on first paint: the hero tagline (LCP) and body text.
function tailorhomes_preload_fonts() {
    $base = get_template_directory_uri() . '/assets/fonts/';
    $files = array('dm-sans.woff2');
    if (is_front_page()) array_unshift($files, 'cormorant-garamond-italic.woff2');
    foreach ($files as $f) {
        echo '<link rel="preload" href="' . esc_url($base . $f) . '" as="font" type="font/woff2" crossorigin>' . "\n";
    }
}
add_action('wp_head', 'tailorhomes_preload_fonts', 2);

/**
 * Minified contents of style.css, cached per theme version + file mtime.
 */
function th_inline_css() {
    $file = get_template_directory() . '/style.css';
    if (!is_readable($file)) return '';
    $key = 'th_inline_css_' . md5(filemtime($file) . wp_get_theme()->get('Version'));
    $css = get_transient($key);
    if ($css === false) {
        $css = (string) file_get_contents($file);
        $css = preg_replace('!/\*.*?\*/!s', '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        // Not ':' — the space in ".th-hero :focus-visible" is a descendant combinator.
        $css = preg_replace('/\s*([{};,>])\s*/', '$1', $css);
        $css = str_replace(';}', '}', $css);
        set_transient($key, trim($css), WEEK_IN_SECONDS);
    }
    return $css;
}

// New uploads: generate WebP sub-sizes (hero, cards, gallery) instead of JPEG/PNG.
// Existing images need "Regenerate Thumbnails" (or a re-upload) to pick this up.
function tailorhomes_webp_subsizes($formats) {
    $formats['image/jpeg'] = 'image/webp';
    $formats['image/png']  = 'image/webp';
    return $formats;
}
add_filter('image_editor_output_format', 'tailorhomes_webp_subsizes');

// WordPress emoji detection script + CSS: unused by the theme, costs a request and inline JS.
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/**
 * Responsive <img> for an image URL (Customizer settings store URLs, not IDs).
 * When the URL belongs to the media library we get srcset/sizes and real width/height,
 * so phones download a ~800px file instead of the full-size original.
 * External or theme-bundled URLs fall back to a plain lazy <img>.
 */
function th_image_id_from_url($url) {
    static $cache = array();
    if (empty($url)) return 0;
    if (!isset($cache[$url])) {
        $map = (array) get_option('th_image_url_map', array());
        $plain = set_url_scheme($url, 'https');
        foreach (array($url, $plain, set_url_scheme($url, 'http')) as $u) {
            if (!empty($map[$u])) { $cache[$url] = (int) $map[$u]; return $cache[$url]; }
        }
        $cache[$url] = (int) attachment_url_to_postid($url);
        // Customizer sometimes stores a resized variant (photo-1024x683.jpg).
        if (!$cache[$url]) {
            $orig = preg_replace('/-(\d+x\d+|scaled)(?=\.[a-z]+$)/i', '', $url);
            $candidates = array($orig, preg_replace('/(\.[a-z]+)$/i', '-scaled$1', $orig));
            foreach (array_unique($candidates) as $try) {
                if ($try === $url) continue;
                $cache[$url] = (int) attachment_url_to_postid($try);
                if ($cache[$url]) break;
            }
        }
    }
    return $cache[$url];
}

function th_img($url, $attrs = array(), $size = 'full') {
    if (empty($url)) return '';
    $attrs = array_merge(array('alt' => '', 'loading' => 'lazy', 'decoding' => 'async'), $attrs);
    if (isset($attrs['fetchpriority']) && $attrs['fetchpriority'] === 'high') {
        $attrs['loading'] = 'eager';
    }

    $id = th_image_id_from_url($url);
    if ($id) {
        // WordPress writes the attachment's real width/height itself.
        unset($attrs['width'], $attrs['height']);
        $html = wp_get_attachment_image($id, $size, false, $attrs);
        if ($html) return $html;
    }

    $out = '<img src="' . esc_url($url) . '"';
    foreach ($attrs as $k => $v) {
        if ($v === false || $v === null) continue;
        $out .= ' ' . $k . '="' . esc_attr($v) . '"';
    }
    return $out . '>';
}

/**
 * Home hero image URL — Customizer first, then featured image (current page, then EN page).
 */
function th_get_hero_image() {
    $hero_img = get_theme_mod('th_hero_image', '');
    $page_id  = get_queried_object_id();
    if (empty($hero_img) && $page_id && has_post_thumbnail($page_id)) {
        $hero_img = get_the_post_thumbnail_url($page_id, 'full');
    }
    if (empty($hero_img) && $page_id && function_exists('pll_get_post')) {
        $en_page_id = pll_get_post($page_id, 'en');
        if ($en_page_id && has_post_thumbnail($en_page_id)) {
            $hero_img = get_the_post_thumbnail_url($en_page_id, 'full');
        }
    }
    return $hero_img;
}

// Preload the hero (the LCP element) so the browser fetches it before parsing the body.
function tailorhomes_preload_hero() {
    if (!is_front_page()) return;
    $url = th_get_hero_image();
    if (empty($url)) return;
    $id = th_image_id_from_url($url);
    $srcset = $id ? wp_get_attachment_image_srcset($id, 'full') : '';
    if ($srcset) {
        echo '<link rel="preload" as="image" href="' . esc_url(wp_get_attachment_image_url($id, 'full')) . '" imagesrcset="' . esc_attr($srcset) . '" imagesizes="100vw" fetchpriority="high">' . "\n";
    } else {
        echo '<link rel="preload" as="image" href="' . esc_url($url) . '" fetchpriority="high">' . "\n";
    }
}
add_action('wp_head', 'tailorhomes_preload_hero', 2);

/**
 * Meta description fallback — only when no SEO plugin is handling it.
 */
function tailorhomes_meta_description() {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION') || class_exists('The_SEO_Framework\\Load')) {
        return;
    }
    $it = (function_exists('pll_current_language') && pll_current_language() === 'it');
    $desc = '';

    if (is_singular()) {
        $post = get_queried_object();
        if (!empty($post->post_excerpt)) {
            $desc = $post->post_excerpt;
        } elseif (is_single()) {
            $desc = wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 28, '…');
        }
    }

    if ($desc === '') {
        $desc = $it
            ? 'Gestione immobiliare a Padova: affitti brevi e medio termine, ville di prestigio, corporate housing e alloggi per studenti. Prenota diretto con Tailor Homes.'
            : 'Property management in Padova, Italy: short and mid-term rentals, prestige villas, corporate housing and student stays. Book direct with Tailor Homes.';
        if (!is_front_page() && is_singular()) {
            $desc = get_the_title() . ' — ' . $desc;
        }
    }

    $desc = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($desc)));
    if (function_exists('mb_substr') && mb_strlen($desc) > 160) {
        $desc = rtrim(mb_substr($desc, 0, 157)) . '…';
    }
    echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
}
add_action('wp_head', 'tailorhomes_meta_description', 1);

// Lets you confirm which theme version is live: View Source and search for "tailor-homes-theme".
add_action('wp_head', function () {
    echo '<meta name="tailor-homes-theme" content="' . esc_attr(wp_get_theme(get_template())->get('Version')) . '">' . "\n";
}, 1);

// Enqueue
function tailorhomes_scripts() {
    // style.css is small (~4 KB gzipped): inline it (with the @font-face rules) so first paint
    // does not wait on another request.
    $th_css = th_inline_css();
    if ($th_css !== '') {
        wp_register_style('tailorhomes-style', false, array(), '4.5');
        wp_enqueue_style('tailorhomes-style');
        wp_add_inline_style('tailorhomes-style', th_font_face_css() . $th_css);
    } else {
        wp_enqueue_style('tailorhomes-style', get_stylesheet_uri(), array(), '4.5');
        wp_add_inline_style('tailorhomes-style', th_font_face_css());
    }

    if (is_page_template('templates/page-apartments.php')) {
        wp_enqueue_style('tailorhomes-apartments', get_template_directory_uri() . '/assets/css/apartments.css', array(), '3.1');
    }
    if (
        is_page_template('templates/page-services.php') ||
        is_page_template('templates/page-owners.php') ||
        is_page_template('templates/page-students.php') ||
        is_page_template('templates/page-students-unipd.php') ||
        is_page_template('templates/page-students-erasmus.php') ||
        is_page_template('templates/page-corporate.php')
    ) {
        wp_enqueue_style('tailorhomes-services', get_template_directory_uri() . '/assets/css/services.css', array(), '3.4');
    }

    wp_enqueue_script('tailorhomes-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.3', array('in_footer' => true, 'strategy' => 'defer'));

    // Theme templates are hand-built PHP, not blocks: skip the block-library CSS there.
    if (is_front_page() || (is_page() && get_page_template_slug())) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
    }
}
add_action('wp_enqueue_scripts', 'tailorhomes_scripts');

// Shared line-icon set (thin red stroke, 24x24) used by the About "5 Areas"
// checklists and the Services hub owner points.
if (!function_exists('th_area_icon')) :
function th_area_icon($name) {
    $icons = array(
        'laundry'  => '<path d="M4 4h16v16H4z"/><circle cx="12" cy="13" r="5"/><circle cx="12" cy="13" r="2.2"/><circle cx="7.3" cy="6.2" r=".6" fill="currentColor" stroke="none"/><circle cx="9.3" cy="6.2" r=".6" fill="currentColor" stroke="none"/>',
        'wrench'   => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2-2z"/>',
        'chat'     => '<path d="M21 11.5a8.4 8.4 0 0 1-8.9 8.4 8.6 8.6 0 0 1-3-.6L3 21l1.7-4.3A8.3 8.3 0 0 1 3.6 11 8.5 8.5 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z"/>',
        'doc'      => '<path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 16.5h6M9 9.5h2"/>',
        'sofa'     => '<path d="M5 12V8a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v4"/><path d="M3.5 12h17a1.5 1.5 0 0 1 1.5 1.5V17a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-3.5A1.5 1.5 0 0 1 3.5 12z"/><path d="M4 18v2M20 18v2"/>',
        'camera'   => '<path d="M4 8h3l1.5-2h7L17 8h3a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/><circle cx="12" cy="13" r="3.5"/>',
        'megaphone'=> '<path d="M3 11v3a1 1 0 0 0 1 1h2l4 4v-13l-4 4H4a1 1 0 0 0-1 1z"/><path d="M15 9a3.5 3.5 0 0 1 0 7M18 6.5a7 7 0 0 1 0 12"/>',
        'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        'trend'    => '<path d="M3 17l6-6 4 4 8-8"/><path d="M15 7h6v6"/>',
        'link'     => '<path d="M9.5 14.5l5-5"/><path d="M13 6.5l1.5-1.5a3.5 3.5 0 0 1 5 5L18 11.5"/><path d="M11 17.5L9.5 19a3.5 3.5 0 0 1-5-5L6 12.5"/>',
        'building' => '<path d="M6 21V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v16"/><path d="M3 21h18"/><path d="M9 8h1.5M13.5 8H15M9 12h1.5M13.5 12H15M9 16h1.5M13.5 16H15"/>',
        'ruler'    => '<path d="M3 16.5L16.5 3l4.5 4.5L7.5 21z"/><path d="M13 6.5l2 2M9.5 10l2 2M6 13.5l2 2"/>',
        'scale'    => '<path d="M12 3v18M8 21h8"/><path d="M5 7h6M13 7h6"/><path d="M5 7l-3 6a3 3 0 0 0 6 0zM19 7l-3 6a3 3 0 0 0 6 0z"/>',
        'sparkle'  => '<path d="M12 3l1.9 5.6L19.5 10l-5.6 1.9L12 17.5l-1.9-5.6L4.5 10l5.6-1.4z"/><path d="M18.5 4.5l.7 2 .3.7M5 17l.6 1.8"/>',
        'tag'      => '<path d="M3 12.5V5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.4.6l6 6a2 2 0 0 1 0 2.8l-7.5 7.5a2 2 0 0 1-2.8 0l-6-6a2 2 0 0 1-.6-1.4z"/><circle cx="8" cy="8" r="1.4"/>',
        'key'      => '<circle cx="8" cy="8" r="4.5"/><path d="M11 11l8 8M16 16l2-2M18 18l2-2"/>',
    );
    $p = isset($icons[$name]) ? $icons[$name] : '';
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">' . $p . '</svg>';
}
endif;

// Favicon
function tailorhomes_favicon() {
    echo '<link rel="icon" type="image/svg+xml" href="' . get_template_directory_uri() . '/assets/images/favicon.svg">';
    echo '<link rel="icon" type="image/png" sizes="512x512" href="' . get_template_directory_uri() . '/assets/images/favicon.png">';
    echo '<link rel="apple-touch-icon" href="' . get_template_directory_uri() . '/assets/images/favicon.png">';
}
add_action('wp_head', 'tailorhomes_favicon');

// Simple flat nav walker
class TH_Simple_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = '';
        if (in_array('current-menu-item', $item->classes)) $classes .= ' active';

        $it = (function_exists('pll_current_language') && pll_current_language() === 'it');
        $display_title = $item->title;
        $title_lower = strtolower($item->title);
        $path = strtolower((string) wp_parse_url($item->url, PHP_URL_PATH));

        // Keep legacy WordPress menu items working while renaming the collection page to Gallery.
        if (
            strpos($title_lower, 'collection') !== false ||
            strpos($title_lower, 'collezione') !== false ||
            strpos($title_lower, 'apartments') !== false ||
            strpos($title_lower, 'appartamenti') !== false ||
            strpos($path, '/apartments') !== false ||
            strpos($path, '/appartamenti') !== false
        ) {
            $display_title = $it ? 'Galleria' : 'Gallery';
        }

        // Detect CTA button: by title keyword OR by custom CSS class 'th-cta' added in menu editor
        $display_lower = strtolower($display_title);
        $is_cta = (
            strpos($display_lower, 'book') !== false ||
            strpos($display_lower, 'prenota') !== false ||
            in_array('th-cta', $item->classes)
        );

        // The single header CTA is always the direct booking button (Krossbooking).
        if ($is_cta) {
            $output .= th_booking_cta_link(trim($classes));
            return;
        }

        $output .= '<a href="' . esc_url($item->url) . '" class="' . trim($classes) . '">' . esc_html($display_title) . '</a>';
    }
    function start_lvl(&$output, $depth = 0, $args = null) {}
    function end_lvl(&$output, $depth = 0, $args = null) {}
    function end_el(&$output, $item, $depth = 0, $args = null) {}
}

// Fallback menu
function tailorhomes_fallback_menu() {
    $it = (function_exists('pll_current_language') && pll_current_language() === 'it');
    echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';
    echo '<a href="' . esc_url(th_url('about-us')) . '">' . ($it ? 'Chi Siamo' : 'About Us') . '</a>';
    echo '<a href="' . esc_url(th_url('our-services')) . '">' . ($it ? 'I Nostri Servizi' : 'Our Services') . '</a>';
    echo '<a href="' . esc_url(th_url('work-with-us')) . '">' . ($it ? 'Lavora con Noi' : 'Work With Us') . '</a>';
    echo '<a href="' . esc_url(th_url('contact')) . '">' . ($it ? 'Contatti' : 'Contact') . '</a>';
    echo '<a href="' . esc_url(th_url('apartments')) . '">' . ($it ? 'Galleria' : 'Gallery') . '</a>';
    echo th_booking_cta_link();
}


// Ensure Work With Us / Lavora con Noi appears in the primary header menu even if the saved WP menu was created before this page existed.
function tailorhomes_add_work_with_us_to_primary_menu($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }

    $it = (function_exists('pll_current_language') && pll_current_language() === 'it');
    $work_url = th_url('work-with-us');
    $label = $it ? 'Lavora con Noi' : 'Work With Us';

    if (
        stripos($items, 'work-with-us') !== false ||
        stripos($items, 'lavora-con-noi') !== false ||
        stripos($items, 'Work With Us') !== false ||
        stripos($items, 'Lavora con Noi') !== false
    ) {
        return $items;
    }

    $link = '<a href="' . esc_url($work_url) . '">' . esc_html($label) . '</a>';

    // Keep the Gallery CTA as the final visual CTA when possible.
    $cta_pos = stripos($items, 'th-cta-btn');
    if ($cta_pos !== false) {
        $before_cta_link = strripos(substr($items, 0, $cta_pos), '<a');
        if ($before_cta_link !== false) {
            return substr($items, 0, $before_cta_link) . $link . substr($items, $before_cta_link);
        }
    }

    return $items . $link;
}
add_filter('wp_nav_menu_items', 'tailorhomes_add_work_with_us_to_primary_menu', 10, 2);

/**
 * Keep the header menu coherent after the CTA became the booking button:
 * 1. Make sure a plain Gallery / Galleria link is still present (the old CTA pointed there).
 * 2. Make sure the Book Now / Prenota Ora button is always the last item, even on saved menus
 *    that never had a 'th-cta' item.
 */
function tailorhomes_ensure_gallery_and_booking_cta($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }

    $it = (function_exists('pll_current_language') && pll_current_language() === 'it');

    // Step 1 — Gallery link
    $has_gallery = (
        stripos($items, '/apartments') !== false ||
        stripos($items, '/appartamenti') !== false ||
        stripos($items, '>Gallery<') !== false ||
        stripos($items, '>Galleria<') !== false
    );

    if (!$has_gallery) {
        $gallery_link = '<a href="' . esc_url(th_url('apartments')) . '">' . esc_html($it ? 'Galleria' : 'Gallery') . '</a>';

        $cta_pos = stripos($items, 'th-book-btn');
        if ($cta_pos !== false) {
            $before_cta_link = strripos(substr($items, 0, $cta_pos), '<a');
            $items = ($before_cta_link !== false)
                ? substr($items, 0, $before_cta_link) . $gallery_link . substr($items, $before_cta_link)
                : $items . $gallery_link;
        } else {
            $items .= $gallery_link;
        }
    }

    // Step 2 — Booking CTA
    if (stripos($items, 'th-book-btn') === false) {
        $items .= th_booking_cta_link();
    }

    return $items;
}
add_filter('wp_nav_menu_items', 'tailorhomes_ensure_gallery_and_booking_cta', 20, 2);

/**
 * Keep Blog out of the header menu — it stays in the footer only.
 * The header was getting crowded, and the blog is a discovery surface rather than
 * a primary navigation destination. Runs on menu objects so the item is dropped
 * before any markup is built, whatever it was named in the WP menu editor.
 */
function tailorhomes_remove_blog_from_primary_menu($items, $args) {
    if (empty($args->theme_location) || $args->theme_location !== 'primary') {
        return $items;
    }

    $blog_page_id = (int) get_option('page_for_posts');
    $blog_path    = untrailingslashit(parse_url(th_url('blog'), PHP_URL_PATH));
    $removed_ids  = array();

    foreach ($items as $i => $item) {
        $title_lower = strtolower(trim($item->title));
        $item_path   = untrailingslashit((string) parse_url($item->url, PHP_URL_PATH));

        $is_blog = (
            $title_lower === 'blog' ||
            $title_lower === 'notizie' ||
            ($blog_page_id && (int) $item->object_id === $blog_page_id && $item->object === 'page') ||
            ($blog_path && $item_path === $blog_path)
        );

        if ($is_blog) {
            $removed_ids[] = (int) $item->ID;
            unset($items[$i]);
        }
    }

    // Drop any children of a removed item so nothing is orphaned.
    if (!empty($removed_ids)) {
        foreach ($items as $i => $item) {
            if (in_array((int) $item->menu_item_parent, $removed_ids, true)) {
                unset($items[$i]);
            }
        }
    }

    return array_values($items);
}
add_filter('wp_nav_menu_objects', 'tailorhomes_remove_blog_from_primary_menu', 10, 2);

function tailorhomes_excerpt_length($length) { return 25; }
add_filter('excerpt_length', 'tailorhomes_excerpt_length');

function tailorhomes_widgets_init() {
    register_sidebar(array(
        'name' => 'Blog Sidebar', 'id' => 'blog-sidebar',
        'before_widget' => '<div class="th-widget">', 'after_widget' => '</div>',
        'before_title' => '<h4 class="th-widget__title">', 'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'tailorhomes_widgets_init');

/* -----------------------------------------------
   CUSTOMIZER — Hero Image Setting
   ----------------------------------------------- */
function tailorhomes_customizer($wp_customize) {
    // --- Images Section ---
    $wp_customize->add_section('th_hero_section', array(
        'title'    => __('Site Images', 'tailorhomes'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('th_hero_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'th_hero_image', array(
        'label'    => __('Home Page Hero Image', 'tailorhomes'),
        'description' => __('Upload or select the full-screen hero photo for the home page.', 'tailorhomes'),
        'section'  => 'th_hero_section',
        'settings' => 'th_hero_image',
    )));

    $wp_customize->add_setting('th_about_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'th_about_image', array(
        'label'    => __('About Page Photo', 'tailorhomes'),
        'description' => __('Upload the photo for the About Us page "Our Approach" section.', 'tailorhomes'),
        'section'  => 'th_hero_section',
        'settings' => 'th_about_image',
    )));

    // --- Apartment Galleries Section ---
    $wp_customize->add_section('th_apartments_section', array(
        'title'    => __('Apartment Photos', 'tailorhomes'),
        'priority' => 31,
        'description' => __('Apartments use 6 photo slots; prestige villas (06 Casa Almendro, 07 Villa Graziosa) use 10. Empty slots are skipped automatically.', 'tailorhomes'),
    ));

    $apartments = array(
        '01' => 'Colore & Design',
        '02' => 'Fitness & Charme',
        '03' => 'Locatelli Apartments',
        '04' => 'Marcanova',
        '05' => 'Monselice Apartment',
        '06' => 'Casa Almendro',
        '07' => 'Villa Graziosa',
    );

    // Villas get extra slots (7–10) for richer galleries; apartments stay at 6.
    $villa_keys = array('06', '07');

    foreach ($apartments as $num => $name) {
        // Backwards-compat textarea (legacy URLs) — kept hidden so old data still works
        $wp_customize->add_setting("th_apt_{$num}_gallery", array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $slot_count = in_array($num, $villa_keys, true) ? 10 : 6;

        for ($i = 1; $i <= $slot_count; $i++) {
            $setting_id = "th_apt_{$num}_img_{$i}";
            $wp_customize->add_setting($setting_id, array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            ));
            $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $setting_id, array(
                'label'    => sprintf(__('Apt %s — %s — Photo %d', 'tailorhomes'), $num, $name, $i),
                'section'  => 'th_apartments_section',
                'settings' => $setting_id,
            )));
        }
    }

    // --- Founder Photo (About page) ---
    $wp_customize->add_setting('th_founder_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'th_founder_image', array(
        'label'    => __('Founder Photo (About page)', 'tailorhomes'),
        'description' => __('Upload Alice Baggio\'s portrait. Falls back to the bundled photo if empty.', 'tailorhomes'),
        'section'  => 'th_hero_section',
        'settings' => 'th_founder_image',
    )));

    // --- Forms (Contact Form 7 / WPForms shortcodes) ---
    $wp_customize->add_section('th_forms_section', array(
        'title'       => __('Form Shortcodes', 'tailorhomes'),
        'priority'    => 33,
        'description' => __('Paste the shortcode of your form plugin (e.g. Contact Form 7). Leave blank to show the built-in placeholder form. Example: [contact-form-7 id="123" title="Contact"]', 'tailorhomes'),
    ));
    $wp_customize->add_setting('th_contact_form_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('th_contact_form_shortcode', array(
        'type'    => 'textarea',
        'label'   => __('Contact page — form shortcode', 'tailorhomes'),
        'section' => 'th_forms_section',
    ));
    $wp_customize->add_setting('th_work_form_shortcode', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('th_work_form_shortcode', array(
        'type'    => 'textarea',
        'label'   => __('Work With Us page — form shortcode', 'tailorhomes'),
        'section' => 'th_forms_section',
    ));

    // --- Home Page: Students card photo ---
    $wp_customize->add_section('th_home_cards_section', array(
        'title'       => __('Home — Students Card', 'tailorhomes'),
        'priority'    => 34,
        'description' => __('Photo for the Students & Erasmus card on the home page.', 'tailorhomes'),
    ));
    $wp_customize->add_setting('th_students_card_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'th_students_card_image', array(
        'label'    => __('Students card photo (home page)', 'tailorhomes'),
        'description' => __('Shown beside the Students & Erasmus card. A warm placeholder appears if empty.', 'tailorhomes'),
        'section'  => 'th_home_cards_section',
        'settings' => 'th_students_card_image',
    )));


    $wp_customize->add_setting('th_corporate_card_image', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'th_corporate_card_image', array(
        'label'    => __('Corporate Housing card photo (home page)', 'tailorhomes'),
        'description' => __('Shown beside the Students & Erasmus card. A clean placeholder appears if empty.', 'tailorhomes'),
        'section'  => 'th_home_cards_section',
        'settings' => 'th_corporate_card_image',
    )));

    // --- About Page: Area Photos (4 images for the mosaic) ---
    $wp_customize->add_section('th_about_areas_section', array(
        'title'    => __('About Page — Area Photos', 'tailorhomes'),
        'priority' => 30,
        'description' => __('Upload 4 photos representing your service areas. These appear in the mosaic grid on the About page.', 'tailorhomes'),
    ));

    $about_area_labels = array(
        1 => 'Cleaning / Linen (e.g. fresh towels, clean bed)',
        2 => 'Staging / Design (e.g. styled room, interior)',
        3 => 'Maintenance / Tech (e.g. tools, smart lock)',
        4 => 'Social / Marketing (e.g. phone with app, laptop)',
    );
    foreach ($about_area_labels as $i => $label) {
        $setting_id = "th_about_area_img_{$i}";
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $setting_id, array(
            'label'    => sprintf(__('Area Photo %d — %s', 'tailorhomes'), $i, $label),
            'section'  => 'th_about_areas_section',
            'settings' => $setting_id,
        )));
    }

    // --- Services Page: Section Photos ---
    $wp_customize->add_section('th_services_photos_section', array(
        'title'    => __('Services Page — Photos', 'tailorhomes'),
        'priority' => 30,
        'description' => __('Upload lifestyle photos for each services section. These bring personality and character to the page.', 'tailorhomes'),
    ));

    $svc_labels = array(
        1 => 'Corporate Housing PAGE — wide photo band (e.g. business apartment, relocation stay)',
        2 => 'Home Staging & Renovation (e.g. styled room, before/after)',
        3 => 'Professional Photography (e.g. photographer at work, camera)',
        4 => 'Consulting & Investments (e.g. meeting, handshake, city view)',
    );
    foreach ($svc_labels as $i => $label) {
        $setting_id = "th_service_img_{$i}";
        $wp_customize->add_setting($setting_id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $setting_id, array(
            'label'    => sprintf(__('Service Photo %d — %s', 'tailorhomes'), $i, $label),
            'section'  => 'th_services_photos_section',
            'settings' => $setting_id,
        )));
    }
}
add_action('customize_register', 'tailorhomes_customizer');

/**
 * Helper: Get apartment gallery images.
 * Returns array of image URLs from Customizer.
 * Priority: per-photo image controls (img_1..img_10) → legacy textarea URLs → provided defaults.
 * Apartments fill 6 slots; villas (06, 07) fill up to 10. Empty slots are skipped automatically.
 */
function th_get_apartment_gallery($apt_num, $defaults = array()) {
    $images = array();
    for ($i = 1; $i <= 10; $i++) {
        $url = get_theme_mod("th_apt_{$apt_num}_img_{$i}", '');
        if (!empty($url)) $images[] = $url;
    }
    if (!empty($images)) return $images;

    // Legacy textarea fallback
    $raw = get_theme_mod("th_apt_{$apt_num}_gallery", '');
    if (!empty(trim($raw))) {
        $urls = array_filter(array_map('trim', explode("\n", $raw)));
        if (!empty($urls)) return $urls;
    }
    return $defaults;
}

/* -----------------------------------------------
   OPENAI / CHATGPT ADS — MEASUREMENT PIXEL
   ----------------------------------------------- */
function th_openai_pixel_id() {
    return 'KLeodmT7QkdGgi1Ss5oMea';
}

// Set to true while testing to log SDK activity in the browser console.
function th_openai_pixel_debug() {
    return false;
}

function th_openai_pixel() {
    $pixel_id = th_openai_pixel_id();
    if (empty($pixel_id)) return;

    // Describe the current page for the page_viewed event.
    if (is_front_page())   { $pid = 'home';    $pname = 'Home'; }
    elseif (is_singular()) { $pid = get_post_field('post_name', get_queried_object_id()); $pname = get_the_title(); }
    elseif (is_home())     { $pid = 'blog';    $pname = 'Blog'; }
    elseif (is_search())   { $pid = 'search';  $pname = 'Search results'; }
    elseif (is_404())      { $pid = '404';     $pname = 'Not found'; }
    else                   { $pid = 'archive'; $pname = wp_get_document_title(); }
    ?>
<script>
(function (w, d, s, u) {
  if (w.oaiq) return;
  var q = function () { q.q.push(arguments); };
  q.q = [];
  w.oaiq = q;
  // Calls below are queued; the SDK itself loads only after the page is idle,
  // so it never competes with the hero image or first paint.
  function load() {
    var js = d.createElement(s);
    js.async = true;
    js.src = u;
    d.head.appendChild(js);
  }
  function idle() { (w.requestIdleCallback || function (cb) { setTimeout(cb, 1500); })(load, { timeout: 4000 }); }
  if (d.readyState === 'complete') idle(); else w.addEventListener('load', idle);
})(window, document, "script", "https://bzrcdn.openai.com/sdk/oaiq.min.js");

oaiq("init", {
  pixelId: "<?php echo esc_js(th_openai_pixel_id()); ?>"<?php echo th_openai_pixel_debug() ? ",\n  debug: true" : ''; ?>

});

/* Every page load reports page_viewed. Without this the pixel initialises
   but never sends anything, and Ads Manager shows no data. */
oaiq("measure", "page_viewed", {
  type: "contents",
  contents: [{
    id: <?php echo wp_json_encode($pid); ?>,
    name: <?php echo wp_json_encode($pname); ?>,
    content_type: "page"
  }]
});
</script>
    <?php
}
add_action('wp_head', 'th_openai_pixel', 1);
/**
 * Site logo <img> with intrinsic width/height (avoids layout shift) — custom logo first,
 * then the bundled SVG.
 */
function th_logo_img($attrs = array()) {
    $attrs = array_merge(array('alt' => get_bloginfo('name') ?: 'Tailor Homes', 'decoding' => 'async'), $attrs);
    $logo_id = has_custom_logo() ? (int) get_theme_mod('custom_logo') : 0;
    // medium_large (768px wide) is plenty for a logo shown ~250px wide; srcset covers 2x/3x screens.
    if ($logo_id && ($html = wp_get_attachment_image($logo_id, 'medium_large', false, array_merge(array('sizes' => '(max-width: 480px) 180px, 250px'), $attrs)))) {
        return $html;
    }
    // Bundled logo.svg has a 3137×1262 viewBox.
    $out = '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/logo.svg') . '" width="249" height="100"';
    foreach ($attrs as $k => $v) {
        $out .= ' ' . $k . '="' . esc_attr($v) . '"';
    }
    return $out . '>';
}

/* -----------------------------------------------
   AI AGENTS — /llms.txt and WebMCP form hints
   ----------------------------------------------- */

/**
 * Serve https://tailorhomes.it/llms.txt (Markdown map of the site for AI agents).
 * Lighthouse "Agentic Browsing" checks it exists, has an H1, real content and Markdown links.
 * A physical llms.txt in the site root takes precedence (the web server serves it directly).
 */
function th_llms_txt_url($slug, $lang) {
    $url = th_url($slug);
    if (function_exists('pll_get_post') && ($page = get_page_by_path($slug))) {
        $tid = pll_get_post($page->ID, $lang);
        if ($tid) $url = get_permalink($tid);
    }
    return $url;
}

function tailorhomes_llms_txt() {
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) return;
    $path = isset($_SERVER['REQUEST_URI']) ? strtok((string) $_SERVER['REQUEST_URI'], '?') : '';
    $home_path = (string) parse_url(home_url('/'), PHP_URL_PATH);
    $home_path = untrailingslashit($home_path);
    if (strtolower(untrailingslashit($path)) !== strtolower($home_path . '/llms.txt')) return;

    $book = th_booking_url();
    $L = function ($slug) { return esc_url_raw(th_llms_txt_url($slug, 'en')); };
    $I = function ($slug) { return esc_url_raw(th_llms_txt_url($slug, 'it')); };
    $home = esc_url_raw(home_url('/'));

    $lines = array(
        '# Tailor Homes',
        '',
        '> Tailor Homes is a property management company based in Padova, Italy. We manage short and medium-term rentals, apartments and prestige villas, offer corporate housing for companies, and a 15% discount for UniPD and Erasmus students. Guests can book directly online.',
        '',
        'Office: Via degli Obizzi 1, Padova, Italy. Email: info@tailorhomes.it. Phone / WhatsApp: +39 371 445 3904. Landline: +39 049 490 6189. The site is available in Italian and English.',
        '',
        '## Book a stay',
        '',
        '- [Book Now — direct booking engine](' . esc_url_raw($book) . '): live availability, prices and secure booking for all our apartments and villas.',
        '- [Gallery](' . $L('apartments') . '): photos of our apartments and villas in and around Padova.',
        '- [Students & Erasmus](' . $L('students') . '): 15% off for UniPD and Erasmus students, verified via WhatsApp.',
        '- [Corporate Housing](' . $L('corporate-housing') . '): furnished stays for business trips, fairs, relocations and projects.',
        '',
        '## Property owners',
        '',
        '- [For Owners](' . $L('owners') . '): full property management, dynamic pricing, guest care 24/7, cleaning and maintenance.',
        '- [Our Services](' . $L('our-services') . '): property management, home staging, photography, consulting and investments.',
        '- [Contact](' . $L('contact') . '): get a free assessment of your property.',
        '',
        '## About',
        '',
        '- [Home](' . $home . '): overview, ratings (Google 5.0, Airbnb 4.89, Booking 9.2) and guest reviews.',
        '- [About Us](' . $L('about-us') . '): our team, approach and the areas we manage.',
        '- [Work With Us](' . $L('work-with-us') . '): jobs and partnerships.',
        '- [Blog](' . $L('blog') . '): guides on student housing and booking a stay in Padova.',
        '',
        '## Italiano',
        '',
        '- [Prenota Ora](' . esc_url_raw($book) . '): prenotazione diretta con disponibilità e prezzi in tempo reale.',
        '- [Per i Proprietari](' . $I('owners') . '): gestione completa del tuo immobile a Padova.',
        '- [Studenti & Erasmus](' . $I('students') . '): 15% di sconto per studenti UniPD ed Erasmus.',
        '- [Contatti](' . $I('contact') . '): scrivici o chiamaci.',
        '',
        '## Optional',
        '',
        '- [Privacy Policy](' . $L('privacy-policy') . ')',
        '- [Terms & Conditions](' . $L('terms-and-conditions') . ')',
    );

    status_header(200);
    header('Content-Type: text/markdown; charset=utf-8');
    header('Cache-Control: public, max-age=86400');
    header('X-Robots-Tag: noindex');
    echo implode("\n", $lines) . "\n";
    exit;
}
// wp_loaded runs before WordPress parses the URL, so no plugin (Polylang, cache, SEO)
// gets the chance to turn /llms.txt into a 404 first.
add_action('wp_loaded', 'tailorhomes_llms_txt', 0);
// Stop WordPress redirecting /llms.txt to /llms.txt/ before we can answer.
add_filter('redirect_canonical', function ($redirect) {
    $path = isset($_SERVER['REQUEST_URI']) ? strtok((string) $_SERVER['REQUEST_URI'], '?') : '';
    return (untrailingslashit($path) === '/llms.txt') ? false : $redirect;
});

/**
 * WebMCP declarative tool hints on Contact Form 7 forms, so AI agents can discover them
 * (Lighthouse flags forms without toolname + tooldescription).
 */
add_filter('wpcf7_form_additional_atts', function ($atts) {
    $it = (function_exists('pll_current_language') && pll_current_language() === 'it');
    $atts['toolname'] = 'contact_tailor_homes';
    $atts['tooldescription'] = $it
        ? 'Invia un messaggio a Tailor Homes (gestione immobiliare a Padova): richieste di soggiorno, gestione immobili, collaborazioni.'
        : 'Send a message to Tailor Homes (property management in Padova): stay enquiries, property management requests, partnerships.';
    return $atts;
});

/**
 * Browser caching for Media Library images (hero, cards, gallery) — Lighthouse
 * "efficient cache lifetimes". Writes wp-content/uploads/.htaccess once, and only when
 * no .htaccess exists there yet (never overwrites one from a security/cache plugin).
 * Apache/LiteSpeed only; nginx ignores it.
 */
function tailorhomes_uploads_cache_rules() {
    if (get_option('th_uploads_htaccess_v1')) return;
    update_option('th_uploads_htaccess_v1', time(), false);

    $dir = wp_upload_dir();
    if (!empty($dir['error'])) return;
    $file = trailingslashit($dir['basedir']) . '.htaccess';
    if (file_exists($file) || !wp_is_writable($dir['basedir'])) return;

    $rules = "# Added by the Tailor Homes theme: long browser cache for uploaded media.\n"
           . "<IfModule mod_expires.c>\n"
           . "  ExpiresActive On\n"
           . "  ExpiresByType image/webp \"access plus 1 year\"\n"
           . "  ExpiresByType image/jpeg \"access plus 1 year\"\n"
           . "  ExpiresByType image/png \"access plus 1 year\"\n"
           . "  ExpiresByType image/svg+xml \"access plus 1 year\"\n"
           . "  ExpiresByType image/avif \"access plus 1 year\"\n"
           . "</IfModule>\n"
           . "<IfModule mod_headers.c>\n"
           . "  <FilesMatch \"\\.(webp|jpe?g|png|svg|avif)$\">\n"
           . "    Header set Cache-Control \"public, max-age=31536000\"\n"
           . "  </FilesMatch>\n"
           . "</IfModule>\n";
    @file_put_contents($file, $rules);
}
add_action('admin_init', 'tailorhomes_uploads_cache_rules');

/* -----------------------------------------------
   THIRD-PARTY / PLUGIN ASSETS
   ----------------------------------------------- */

/**
 * Contact Form 7 only where a form is shown (Contact, Work With Us, or any page whose content
 * contains a CF7 shortcode). Elsewhere this drops 2 CSS/JS files plus WordPress's
 * hooks.min.js and i18n.min.js, which were on the home page's critical path.
 */
function th_page_has_cf7() {
    if (is_page_template('templates/page-contact.php') || is_page_template('templates/page-work-with-us.php')) {
        return true;
    }
    if (is_singular()) {
        $post = get_queried_object();
        if ($post && isset($post->post_content) && has_shortcode($post->post_content, 'contact-form-7')) {
            return true;
        }
    }
    return false;
}
add_filter('wpcf7_load_js', 'th_page_has_cf7');
add_filter('wpcf7_load_css', 'th_page_has_cf7');

/**
 * Complianz cookie-banner stylesheets: load without blocking first paint.
 * The banner is shown by its script after the page loads, so it is styled by then.
 */
add_filter('style_loader_tag', function ($html, $handle, $href) {
    if (strpos((string) $href, 'complianz') === false || strpos($html, 'media="print"') !== false) {
        return $html;
    }
    $async = str_replace(array("media='all'", 'media="all"'), 'media="print" onload="this.media=\'all\'"', $html);
    if ($async === $html) {
        $async = str_replace('<link ', '<link media="print" onload="this.media=\'all\'" ', $html);
    }
    return $async . '<noscript>' . $html . '</noscript>';
}, 20, 3);

/**
 * Google tag (gtag.js from Site Kit, ~175 KB): fetch it after the first user interaction
 * (scroll, tap, key, mouse) or 6 s after the page has loaded, whichever comes first.
 * Calls made before that are queued in dataLayer and sent when it loads, so page views,
 * consent mode and events are kept. Disable with: add_filter('th_delay_gtag', '__return_false');
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    if (strpos((string) $src, 'googletagmanager.com/gtag/js') === false || !apply_filters('th_delay_gtag', true)) {
        return $tag;
    }
    return '<script data-th-delay-src="' . esc_url($src) . '"></script>' . "\n";
}, 20, 3);

add_action('wp_footer', function () {
    if (!apply_filters('th_delay_gtag', true)) return;
    ?>
<script>
(function () {
  var done = false, events = ['scroll', 'pointerdown', 'touchstart', 'keydown', 'mousemove'];
  function go() {
    if (done) return; done = true;
    events.forEach(function (e) { window.removeEventListener(e, go, { passive: true }); });
    document.querySelectorAll('script[data-th-delay-src]').forEach(function (old) {
      var s = document.createElement('script');
      s.async = true;
      s.src = old.getAttribute('data-th-delay-src');
      old.parentNode.replaceChild(s, old);
    });
  }
  events.forEach(function (e) { window.addEventListener(e, go, { passive: true, once: true }); });
  window.addEventListener('load', function () { setTimeout(go, 6000); });
})();
</script>
    <?php
}, 99);

/* -----------------------------------------------
   WEBP FOR EXISTING THEME IMAGES
   ----------------------------------------------- */

/**
 * Images picked in the Customizer (hero, home cards, About, Services, Gallery), the custom logo
 * and blog featured images.
 */
function th_webp_target_ids() {
    $urls = array();
    foreach (array('th_hero_image', 'th_students_card_image', 'th_corporate_card_image', 'th_about_image', 'th_founder_image') as $mod) {
        $urls[] = get_theme_mod($mod, '');
    }
    for ($i = 1; $i <= 4; $i++) {
        $urls[] = get_theme_mod("th_about_area_img_{$i}", '');
        $urls[] = get_theme_mod("th_service_img_{$i}", '');
    }
    foreach (array('01', '02', '03', '04', '05', '06', '07') as $num) {
        for ($i = 1; $i <= 10; $i++) {
            $urls[] = get_theme_mod("th_apt_{$num}_img_{$i}", '');
        }
    }

    $ids = array();
    foreach (array_filter($urls) as $url) {
        $id = th_image_id_from_url($url);
        if ($id) $ids[] = $id;
    }
    if ($logo = (int) get_theme_mod('custom_logo')) $ids[] = $logo;

    $posts = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 60, 'fields' => 'ids'));
    foreach ($posts as $pid) {
        if ($tid = (int) get_post_thumbnail_id($pid)) $ids[] = $tid;
    }
    return array_values(array_unique($ids));
}

/**
 * Rebuilds the resized copies of those images as WebP (via the image_editor_output_format
 * filter above). Runs in small batches while an admin browses wp-admin, once per image,
 * then purges the LiteSpeed page cache so visitors get the new srcset. Originals are untouched.
 */
function tailorhomes_regenerate_webp_batch() {
    if (wp_doing_ajax() || !current_user_can('upload_files') || get_option('th_webp_regen_done_v1')) return;
    if (get_transient('th_webp_regen_lock')) return;
    set_transient('th_webp_regen_lock', 1, 60);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $start = microtime(true);
    $pending = 0;
    $processed = 0;

    foreach (th_webp_target_ids() as $id) {
        if (get_post_meta($id, '_th_webp_regen_v1', true)) continue;
        $mime = get_post_mime_type($id);
        if (!in_array($mime, array('image/jpeg', 'image/png'), true)) {
            update_post_meta($id, '_th_webp_regen_v1', 'skip');
            continue;
        }
        if ($processed >= 3 || (microtime(true) - $start) > 15) { $pending++; continue; }

        $file = get_attached_file($id);
        $old_url = wp_get_attachment_url($id);
        if ($file && file_exists($file)) {
            $meta = wp_generate_attachment_metadata($id, $file);
            if ($meta && !is_wp_error($meta)) {
                wp_update_attachment_metadata($id, $meta);
                // Newer WordPress may also convert the full-size file. Remember the old URL so
                // Customizer settings that still store it keep resolving to this image.
                $new_url = wp_get_attachment_url($id);
                if ($old_url && $new_url && $old_url !== $new_url) {
                    $map = (array) get_option('th_image_url_map', array());
                    $map[$old_url] = $id;
                    update_option('th_image_url_map', $map, false);
                }
            }
        }
        update_post_meta($id, '_th_webp_regen_v1', time());
        $processed++;
    }

    delete_transient('th_webp_regen_lock');
    if ($pending === 0) {
        update_option('th_webp_regen_done_v1', time(), false);
    }
    if ($processed > 0 && $pending === 0) {
        do_action('litespeed_purge_all');
    }
}
add_action('admin_init', 'tailorhomes_regenerate_webp_batch', 20);

/**
 * When an image has WebP sizes, keep the original JPEG/PNG out of srcset so no browser
 * picks the multi-MB original (it remains the plain src fallback).
 */
add_filter('wp_calculate_image_srcset', function ($sources) {
    if (!is_array($sources) || count($sources) < 2) return $sources;
    $has_webp = false;
    foreach ($sources as $src) {
        if (preg_match('/\.webp$/i', $src['url'])) { $has_webp = true; break; }
    }
    if (!$has_webp) return $sources;
    foreach ($sources as $w => $src) {
        if (!preg_match('/\.webp$/i', $src['url'])) unset($sources[$w]);
    }
    return $sources;
});
