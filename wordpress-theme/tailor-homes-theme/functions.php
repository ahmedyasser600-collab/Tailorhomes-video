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

// Enqueue
function tailorhomes_scripts() {
    wp_enqueue_style('tailorhomes-fonts', 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,700;0,9..40,800;1,9..40,300;1,9..40,400&family=Jost:wght@300;400;500&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&display=swap', array(), null);
    wp_enqueue_style('tailorhomes-style', get_stylesheet_uri(), array(), '4.2');

    if (is_page_template('templates/page-apartments.php')) {
        wp_enqueue_style('tailorhomes-apartments', get_template_directory_uri() . '/assets/css/apartments.css', array(), '3.0');
    }
    if (
        is_page_template('templates/page-services.php') ||
        is_page_template('templates/page-owners.php') ||
        is_page_template('templates/page-students.php') ||
        is_page_template('templates/page-students-unipd.php') ||
        is_page_template('templates/page-students-erasmus.php') ||
        is_page_template('templates/page-corporate.php')
    ) {
        wp_enqueue_style('tailorhomes-services', get_template_directory_uri() . '/assets/css/services.css', array(), '3.3');
    }

    wp_enqueue_script('tailorhomes-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true);
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
    return true;
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
  var js = d.createElement(s);
  js.async = true;
  js.src = u;
  var f = d.getElementsByTagName(s)[0];
  f.parentNode.insertBefore(js, f);
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