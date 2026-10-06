<?php
/**
 * Tailor Homes — seeded bilingual student housing blog posts.
 * Creates/updates the 4 supplied posts and imports the bundled SEO images.
 */
if (!defined('ABSPATH')) { exit; }

function th_seed_blog_image_definitions() {
    return array(
        'cozy_study_with_view' => array(
            'file' => 'cozy-study-with-unipd-erasmus-desk-padova.webp',
            'title' => 'Erasmus Student Housing in Padova',
            'alt' => 'Erasmus student accommodation in Padova with UniPD and Erasmus documents on a study desk',
            'caption' => 'A ready-to-live student room in Padova with UniPD and Erasmus essentials, ideal for international students arriving for a semester abroad.',
            'description' => 'Warm student accommodation setup in Padova featuring UniPD materials, Erasmus documents, a city map, laptop, and study desk. Supports content about Erasmus housing, medium-term furnished rentals, and stress-free student arrivals in Padova.',
        ),
        'student_apartment_city_view' => array(
            'file' => 'student-apartment-padova-erasmus-unipd-city-view.webp',
            'title' => 'Furnished Student Apartment in Padova',
            'alt' => 'Furnished student apartment in Padova with Erasmus folder, UniPD bag, suitcase and city view',
            'caption' => 'A furnished apartment prepared for an Erasmus or UniPD student arriving in Padova.',
            'description' => 'Bright furnished student apartment in Padova with Erasmus and UniPD elements, suitcase, desk, sofa, and local city materials. Suitable for student housing, short and medium-term rentals, and international arrivals in Padova.',
        ),
        'university_seal_wall' => array(
            'file' => 'university-of-padova-seal-stone-wall.webp',
            'title' => 'University of Padova Student Housing Guide',
            'alt' => 'Università degli Studi di Padova seal on a historic stone wall',
            'caption' => 'The University of Padova is at the center of student life in the city — and one of the reasons housing demand is so high.',
            'description' => 'Close-up of the Università degli Studi di Padova seal on a historic building wall. Ideal for articles about UniPD students, Erasmus arrivals, student accommodation demand, and finding housing in Padova.',
        ),
        'erasmus_travel_moments' => array(
            'file' => 'erasmus-booklet-travel-map-padova.webp',
            'title' => 'Erasmus Arrival in Padova',
            'alt' => 'Erasmus booklet and travel map held in front of a historic Padova university building',
            'caption' => 'For Erasmus students, finding housing before arriving in Padova is one of the most important steps.',
            'description' => 'An Erasmus booklet and travel map shown in front of a historic university-style building in Padova. Fits content about Erasmus preparation, moving to Padova, housing from abroad, and avoiding accommodation stress.',
        ),
        'cozy_tote_bottle' => array(
            'file' => 'unipd-tote-student-room-padova.webp',
            'title' => 'UniPD Student Room Essentials',
            'alt' => 'UniPD tote bag and student essentials in a furnished room in Padova',
            'caption' => 'A comfortable student setup in Padova, ready for study, daily life, and a new semester.',
            'description' => 'Cozy student room scene with a UniPD tote bag, water bottle, desk, and warm interior details. Best used for student accommodation content, furnished rentals, and practical housing guides for UniPD and Erasmus students.',
        ),
        'student_id_backdrop' => array(
            'file' => 'unipd-student-card-padova-campus.webp',
            'title' => 'UniPD Student Card in Padova',
            'alt' => 'UniPD student card held in front of a historic university building in Padova',
            'caption' => 'A UniPD student card can open the door to student services, discounts, and a smoother arrival in Padova.',
            'description' => 'Close-up of a UniPD student card held outdoors with a historic university building in the background. Useful for student discounts, Erasmus verification, university life, and student accommodation in Padova.',
        ),
        'unipd_banner' => array(
            'file' => 'universita-degli-studi-di-padova-banner.webp',
            'title' => 'Università di Padova Student Life',
            'alt' => 'Red Università degli Studi di Padova banner outside a historic university building',
            'caption' => 'Padova’s university heritage attracts thousands of students every year, increasing demand for quality housing.',
            'description' => 'A red University of Padova banner displayed outside a historic building. Suitable for articles about student life in Padova, UniPD accommodation demand, Erasmus mobility, and the challenge of finding student housing.',
        ),
        'planning_notebook' => array(
            'file' => 'erasmus-planning-notebook-padova.webp',
            'title' => 'Erasmus Planning for Padova',
            'alt' => 'Erasmus planning notebook with laptop, city map and notes about new opportunities',
            'caption' => 'Planning ahead makes the Erasmus housing search in Padova much easier and safer.',
            'description' => 'Clean desk scene with an Erasmus notebook, laptop, map, and handwritten planning notes. Ideal for preparing for Erasmus, finding student housing before arrival, and organizing a smooth move to Padova.',
        ),
    );
}

function th_seed_blog_post_definitions() {
    return array(
        'student_accommodation_en' => array(
            'title' => 'Student Accommodation in Padova: Why It\'s So Hard to Find — and How to Fix It',
            'slug' => 'student-accommodation-padova',
            'lang' => 'en',
            'cat' => 'Student Guide',
            'read' => '8 min read',
            'excerpt' => 'Every September, thousands of students arrive in Padova and discover the same thing: finding a decent, affordable room is genuinely difficult. If you\'re searching for student accommodation in Padova and feeling overwhelmed, you\'re not doing anything wrong — the market really is that tight.',
            'meta_desc' => 'Struggling to find student accommodation in Padova? Here\'s why rooms are so hard to find, what they really cost, the best neighbourhoods, and how furnished short- and medium-term rentals solve the problem — with a 15% student discount.',
            'featured' => 'university_seal_wall',
            'slots' => array(
                'unipd_banner',
                'cozy_tote_bottle',
            ),
        ),
        'student_accommodation_it' => array(
            'title' => 'Alloggi per Studenti a Padova: Perché È Così Difficile Trovarne — e Come Risolvere',
            'slug' => 'alloggi-studenti-padova',
            'lang' => 'it',
            'cat' => 'Guida Studenti',
            'read' => '8 min di lettura',
            'excerpt' => 'Ogni settembre migliaia di studenti arrivano a Padova e scoprono la stessa cosa: trovare una stanza decente e a un prezzo onesto è davvero difficile. Se stai cercando un alloggio per studenti a Padova e ti senti sopraffatto, non stai sbagliando nulla — il mercato è proprio così saturo.',
            'meta_desc' => 'Difficile trovare un alloggio per studenti a Padova? Ecco perché le stanze sono così difficili da trovare, quanto costano davvero, le zone migliori e come gli affitti brevi e a medio termine arredati risolvono il problema — con uno sconto studenti del 15%.',
            'featured' => 'unipd_banner',
            'slots' => array(
                'university_seal_wall',
                'cozy_study_with_view',
            ),
        ),
        'erasmus_housing_en' => array(
            'title' => 'How Erasmus Students Can Find Housing in Padova Without the Stress',
            'slug' => 'erasmus-housing-padova',
            'lang' => 'en',
            'cat' => 'Erasmus Guide',
            'read' => '7 min read',
            'excerpt' => 'Arranging accommodation from another country, in a language you may not speak, for a stay that\'s too short for most landlords — Erasmus housing in Padova is one of the most stressful parts of the whole experience. It doesn\'t have to be.',
            'meta_desc' => 'A practical guide for Erasmus and international students on finding housing in Padova: timelines, temporary contracts, what to avoid, neighbourhoods, costs, and a 15% student discount on furnished medium-term rentals.',
            'featured' => 'cozy_study_with_view',
            'slots' => array(
                'erasmus_travel_moments',
                'student_apartment_city_view',
            ),
        ),
        'erasmus_housing_it' => array(
            'title' => 'Come gli Studenti Erasmus Possono Trovare Casa a Padova Senza Stress',
            'slug' => 'alloggio-erasmus-padova',
            'lang' => 'it',
            'cat' => 'Guida Erasmus',
            'read' => '7 min di lettura',
            'excerpt' => 'Organizzare un alloggio da un altro Paese, in una lingua che magari non parli, per un soggiorno troppo breve per la maggior parte dei proprietari — trovare casa in Erasmus a Padova è una delle parti più stressanti di tutta l\'esperienza. Ma non deve esserlo.',
            'meta_desc' => 'Guida pratica per studenti Erasmus e internazionali per trovare alloggio a Padova: tempistiche, contratti transitori, cosa evitare, quartieri, costi e uno sconto studenti del 15% sugli affitti arredati a medio termine.',
            'featured' => 'student_apartment_city_view',
            'slots' => array(
                'student_id_backdrop',
                'planning_notebook',
            ),
        ),
    );
}

function th_seed_read_blog_content($key) {
    $file = get_template_directory() . '/assets/blog-content/' . $key . '.html';
    return file_exists($file) ? file_get_contents($file) : '';
}

function th_seed_import_blog_image($key) {
    $images = th_seed_blog_image_definitions();
    if (empty($images[$key])) { return 0; }
    $def = $images[$key];

    $existing = get_posts(array(
        'post_type'      => 'attachment',
        'posts_per_page' => 1,
        'post_status'    => 'inherit',
        'meta_key'       => '_th_seed_image_key',
        'meta_value'     => $key,
        'fields'         => 'ids',
    ));
    if (!empty($existing)) {
        $id = (int) $existing[0];
        update_post_meta($id, '_wp_attachment_image_alt', $def['alt']);
        wp_update_post(array(
            'ID'           => $id,
            'post_title'   => $def['title'],
            'post_excerpt' => $def['caption'],
            'post_content' => $def['description'],
        ));
        return $id;
    }

    $source = get_template_directory() . '/assets/images/blog/' . $def['file'];
    if (!file_exists($source)) { return 0; }

    $upload = wp_upload_dir();
    if (!empty($upload['error'])) { return 0; }

    $dest_dir = trailingslashit($upload['path']);
    wp_mkdir_p($dest_dir);
    $dest_file = wp_unique_filename($dest_dir, basename($source));
    $dest_path = $dest_dir . $dest_file;
    copy($source, $dest_path);

    $filetype = wp_check_filetype($dest_file, null);
    $attachment = array(
        'guid'           => trailingslashit($upload['url']) . $dest_file,
        'post_mime_type' => $filetype['type'],
        'post_title'     => $def['title'],
        'post_excerpt'   => $def['caption'],
        'post_content'   => $def['description'],
        'post_status'    => 'inherit',
    );

    $attach_id = wp_insert_attachment($attachment, $dest_path);
    if (is_wp_error($attach_id) || !$attach_id) { return 0; }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    $metadata = wp_generate_attachment_metadata($attach_id, $dest_path);
    wp_update_attachment_metadata($attach_id, $metadata);
    update_post_meta($attach_id, '_wp_attachment_image_alt', $def['alt']);
    update_post_meta($attach_id, '_th_seed_image_key', $key);

    return (int) $attach_id;
}

function th_seed_blog_posts() {
    if (!function_exists('wp_insert_post')) { return; }

    $posts = th_seed_blog_post_definitions();
    $created = array();

    foreach ($posts as $key => $def) {
        $content = th_seed_read_blog_content($key);
        if (!$content) { continue; }

        $cat_id = 0;
        if (!empty($def['cat'])) {
            $cat_id = wp_create_category($def['cat']);
        }

        $existing = get_posts(array(
            'post_type'      => 'post',
            'name'           => $def['slug'],
            'post_status'    => array('publish', 'draft', 'pending', 'future', 'private'),
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ));

        $postarr = array(
            'post_title'   => $def['title'],
            'post_name'    => $def['slug'],
            'post_excerpt' => $def['excerpt'],
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'post',
        );
        if ($cat_id) { $postarr['post_category'] = array($cat_id); }

        if (!empty($existing)) {
            $postarr['ID'] = (int) $existing[0];
            $post_id = wp_update_post($postarr, true);
        } else {
            $post_id = wp_insert_post($postarr, true);
        }
        if (is_wp_error($post_id) || !$post_id) { continue; }
        $post_id = (int) $post_id;
        $created[$key] = $post_id;

        update_post_meta($post_id, '_th_seed_blog_key', $key);
        update_post_meta($post_id, '_th_meta_description', $def['meta_desc']);
        update_post_meta($post_id, '_yoast_wpseo_metadesc', $def['meta_desc']);
        update_post_meta($post_id, '_rank_math_description', $def['meta_desc']);
        update_post_meta($post_id, '_th_read_time', $def['read']);

        $featured_id = th_seed_import_blog_image($def['featured']);
        if ($featured_id) { set_post_thumbnail($post_id, $featured_id); }

        if (!empty($def['slots'])) {
            foreach ($def['slots'] as $i => $image_key) {
                $slot_num = $i + 1;
                $image_id = th_seed_import_blog_image($image_key);
                if ($image_id) {
                    update_post_meta($post_id, '_th_blog_image_' . $slot_num, $image_id);
                    $image_defs = th_seed_blog_image_definitions();
                    if (!empty($image_defs[$image_key]['caption'])) {
                        update_post_meta($post_id, '_th_blog_image_' . $slot_num . '_caption', $image_defs[$image_key]['caption']);
                    }
                }
            }
        }

        if (function_exists('pll_set_post_language') && !empty($def['lang'])) {
            pll_set_post_language($post_id, $def['lang']);
        }
    }

    if (function_exists('pll_save_post_translations')) {
        if (!empty($created['student_accommodation_en']) && !empty($created['student_accommodation_it'])) {
            pll_save_post_translations(array(
                'en' => $created['student_accommodation_en'],
                'it' => $created['student_accommodation_it'],
            ));
        }
        if (!empty($created['erasmus_housing_en']) && !empty($created['erasmus_housing_it'])) {
            pll_save_post_translations(array(
                'en' => $created['erasmus_housing_en'],
                'it' => $created['erasmus_housing_it'],
            ));
        }
    }

    update_option('th_seed_student_housing_blogs_v1', time(), false);
}

add_action('after_switch_theme', 'th_seed_blog_posts');
add_action('admin_init', function () {
    // Runs once after upload/update as well as after activation. Idempotent: existing seeded posts are updated by slug.
    if (!get_option('th_seed_student_housing_blogs_v1')) {
        th_seed_blog_posts();
    }
});
