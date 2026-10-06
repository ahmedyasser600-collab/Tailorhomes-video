<?php
/**
 * Tailor Homes — seeded bilingual "how to book your stay in Padova" blog posts.
 *
 * Same pattern as seed-student-housing-blogs.php: idempotent, matched by slug,
 * EN/IT linked through Polylang. Featured images are NOT bundled here — set them
 * from the WordPress media library (Posts → Edit → Featured Image), and use the
 * "Blog Image Slots" meta box to fill the [th_image n="1"] / n="2" placeholders.
 */
if (!defined('ABSPATH')) { exit; }

function th_seed_booking_blog_definitions() {
    return array(
        'book_stay_padova_en' => array(
            'title'     => 'The Best Way to Book Your Stay in Padova, Italy',
            'slug'      => 'best-way-to-book-your-stay-in-padova',
            'lang'      => 'en',
            'cat'       => 'Padova Guide',
            'read'      => '8 min read',
            'excerpt'   => 'Hotel, serviced apartment or furnished monthly rental? Where to stay in Padova, what each option really costs once the platform fees are added, and why booking direct with the people who manage the apartment is almost always the better deal.',
            'meta_desc' => 'How to book accommodation in Padova, Italy: the best areas to stay, which type of apartment suits your trip, and why booking direct avoids platform commissions and guest service fees. Live availability and instant confirmation.',
            'faq'       => array(
                array(
                    'q' => 'What is the best way to book accommodation in Padova?',
                    'a' => 'Decide first on the type of stay — a hotel for a night, a serviced apartment for a few days to a few weeks, a furnished medium-term rental for a month or more — then book with the property directly wherever you can. Direct booking avoids both the commission the property pays a platform and the service fee added to your side at checkout, which is why the same apartment on the same dates is often cheaper booked direct.',
                ),
                array(
                    'q' => 'Is it cheaper to book direct than through a large booking platform?',
                    'a' => 'Usually, yes. Platforms charge the property a commission on every reservation and most add a guest service fee at checkout, and both are ultimately reflected in the price you see. Booking through a property\'s own site removes those layers, and the terms and any special request are agreed with the people who actually manage the apartment.',
                ),
                array(
                    'q' => 'Where is the best area to stay in Padova?',
                    'a' => 'It depends on why you are coming. The historic centre around Piazza delle Erbe suits sightseeing and dining; Prato della Valle and the Basilica area is quieter and beautiful; near the station is best for commuting to Venice, Vicenza or Verona; the university quarter and Portello suit anyone with business at UniPD; and staying close to the hospital or the fair district makes sense when your visit is built around either of those.',
                ),
                array(
                    'q' => 'Can I book an apartment in Padova for a month or longer?',
                    'a' => 'Yes. Furnished medium-term rentals are designed for exactly this — students, relocating professionals, visiting academics, and people staying for medical treatment. They come furnished with bills included and a proper contract, at a monthly rate rather than a nightly one, and without committing to a multi-year Italian lease.',
                ),
                array(
                    'q' => 'Do I have to pay a city tax in Padova?',
                    'a' => 'Padova applies a tourist tax per person per night on stays in tourist accommodation, as most Italian cities do, with exemptions that typically include young children and certain other categories. When you book with Tailor Homes we confirm the exact amount for your stay and how to pay it before you arrive.',
                ),
                array(
                    'q' => 'How far in advance should I book a stay in Padova?',
                    'a' => 'For ordinary dates a few weeks is comfortable. Book considerably earlier for graduation periods, the Saint Anthony feast in June, major trade fair dates and the start of the university term, when availability across the entire city tightens at once.',
                ),
            ),
        ),
        'book_stay_padova_it' => array(
            'title'     => 'Il Modo Migliore per Prenotare il Tuo Soggiorno a Padova',
            'slug'      => 'come-prenotare-soggiorno-padova',
            'lang'      => 'it',
            'cat'       => 'Guida Padova',
            'read'      => '8 min di lettura',
            'excerpt'   => 'Hotel, appartamento con servizi o affitto mensile arredato? Dove dormire a Padova, quanto costa davvero ogni opzione una volta aggiunte le commissioni dei portali, e perché prenotare in diretta con chi gestisce l\'appartamento conviene quasi sempre.',
            'meta_desc' => 'Come prenotare un alloggio a Padova: le zone migliori dove dormire, quale tipo di appartamento scegliere e perché la prenotazione diretta evita commissioni di intermediazione e costi di servizio. Disponibilità reale e conferma immediata.',
            'faq'       => array(
                array(
                    'q' => 'Qual è il modo migliore per prenotare un alloggio a Padova?',
                    'a' => 'Prima scegli il tipo di soggiorno — hotel per una notte, appartamento con servizi da qualche giorno a qualche settimana, affitto arredato a medio termine da un mese in su — poi prenota direttamente con la struttura ogni volta che puoi. La prenotazione diretta evita sia la commissione che la struttura paga al portale sia il costo di servizio addebitato a te al pagamento.',
                ),
                array(
                    'q' => 'Conviene prenotare direttamente invece che tramite i grandi portali?',
                    'a' => 'In genere sì. I portali applicano alla struttura una commissione su ogni prenotazione e quasi tutti aggiungono un costo di servizio a carico dell\'ospite, e alla fine entrambi si riflettono sul prezzo che vedi. Prenotare dal sito della struttura elimina questi passaggi e permette di concordare condizioni e richieste particolari con chi gestisce davvero l\'appartamento.',
                ),
                array(
                    'q' => 'Qual è la zona migliore dove dormire a Padova?',
                    'a' => 'Dipende dal motivo del viaggio. Il centro storico attorno a Piazza delle Erbe è ideale per visitare la città e per i ristoranti; Prato della Valle e la zona del Santo sono più tranquille; vicino alla stazione conviene se ti sposti su Venezia, Vicenza o Verona; la zona universitaria e il Portello sono comode per chi ha impegni all\'Università; stare vicino all\'ospedale o alla Fiera ha senso quando il soggiorno ruota attorno a uno dei due.',
                ),
                array(
                    'q' => 'Posso prenotare un appartamento a Padova per un mese o più?',
                    'a' => 'Sì. Gli affitti a medio termine arredati nascono esattamente per questo: studenti, professionisti in trasferimento, docenti in visita e persone in città per cure mediche. Sono arredati, con utenze incluse e contratto regolare, a canone mensile invece che a notte, senza vincolarsi a una locazione pluriennale.',
                ),
                array(
                    'q' => 'A Padova si paga la tassa di soggiorno?',
                    'a' => 'Sì, come nella maggior parte delle città italiane Padova applica un\'imposta di soggiorno per persona e per notte nelle strutture ricettive, con esenzioni che riguardano di norma i bambini e alcune altre categorie. Quando prenoti con Tailor Homes ti confermiamo l\'importo esatto e le modalità di pagamento prima dell\'arrivo.',
                ),
                array(
                    'q' => 'Con quanto anticipo conviene prenotare a Padova?',
                    'a' => 'Per date ordinarie qualche settimana è sufficiente. Serve molto più anticipo per le sessioni di laurea, la festa di Sant\'Antonio a giugno, le principali date di fiera e l\'inizio dell\'anno accademico, quando la disponibilità dell\'intera città si riduce tutta insieme.',
                ),
            ),
        ),
    );
}

function th_seed_booking_read_content($key) {
    $file = get_template_directory() . '/assets/blog-content/' . $key . '.html';
    return file_exists($file) ? file_get_contents($file) : '';
}

function th_seed_booking_blog_posts() {
    if (!function_exists('wp_insert_post')) { return; }

    $posts   = th_seed_booking_blog_definitions();
    $created = array();

    foreach ($posts as $key => $def) {
        $content = th_seed_booking_read_content($key);
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

        if (!empty($def['faq'])) {
            // Stored as an array (WordPress serializes it) — a JSON string would lose its escaping to wp_unslash().
            update_post_meta($post_id, '_th_faq_schema', $def['faq']);
        }

        if (function_exists('pll_set_post_language') && !empty($def['lang'])) {
            pll_set_post_language($post_id, $def['lang']);
        }
    }

    if (function_exists('pll_save_post_translations')) {
        if (!empty($created['book_stay_padova_en']) && !empty($created['book_stay_padova_it'])) {
            pll_save_post_translations(array(
                'en' => $created['book_stay_padova_en'],
                'it' => $created['book_stay_padova_it'],
            ));
        }
    }

    update_option('th_seed_booking_blogs_v1', time(), false);
}

add_action('after_switch_theme', 'th_seed_booking_blog_posts');
add_action('admin_init', function () {
    // Runs once after upload/update as well as after activation. Idempotent: existing posts are matched by slug.
    if (!get_option('th_seed_booking_blogs_v1')) {
        th_seed_booking_blog_posts();
    }
});
