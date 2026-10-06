<?php
/**
 * ════════════════════════════════════════════════════════════════════════
 *  Tailor Homes — Single Blog Post Template
 *  File: single-post.php
 *  Place in: /wp-content/themes/tailor-homes-theme/single-post.php
 * ════════════════════════════════════════════════════════════════════════
 *
 *  This template renders ALL the chrome around a blog post:
 *    • Hero (category, date, read time, title, intro)
 *    • Featured image (drag-and-drop in WP Featured Image panel)
 *    • The post body content (whatever is in the post editor)
 *    • Author block
 *    • Related posts (auto-pulled from same category)
 *    • JSON-LD schema (auto-generated from WP fields)
 *
 *  HOW TO USE A BLOG POST:
 *    1. WP admin → Posts → Add New (or edit existing)
 *    2. Fill in the TITLE field at the top
 *    3. Fill in the EXCERPT field in the sidebar (this becomes the intro)
 *    4. Set the FEATURED IMAGE via drag-and-drop in the sidebar
 *    5. Choose a CATEGORY (Local Guide / Rental Advice / etc.)
 *    6. Paste the body-only HTML in a Custom HTML block in the editor
 *    7. Insert native Image blocks between sections if you want drag-and-drop
 *    8. Publish.
 * ════════════════════════════════════════════════════════════════════════
 */

get_header();

$it = (function_exists('pll_current_language') && pll_current_language() === 'it');

while (have_posts()) :
    the_post();

    // ---- Pull post data from WordPress ----
    $cats          = get_the_category();
    $primary_cat   = !empty($cats) ? $cats[0]->name : '';
    $featured_id   = get_post_thumbnail_id();
    $featured_url  = $featured_id ? wp_get_attachment_image_url($featured_id, 'full') : '';
    $featured_alt  = $featured_id ? get_post_meta($featured_id, '_wp_attachment_image_alt', true) : '';
    if (!$featured_alt) { $featured_alt = get_the_title(); }
    $excerpt       = get_the_excerpt();

    // Read time = words / 200, rounded up to nearest minute
    $word_count    = str_word_count(strip_tags(get_the_content()));
    $read_time     = max(1, (int) round($word_count / 200));
    $read_label    = $it ? 'min di lettura' : 'min read';

    // Italian month names for date formatting
    $date_iso      = get_the_date('c');
    if ($it) {
        $months_it = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
                      'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
        $date_display = get_the_date('j') . ' ' . $months_it[(int) get_the_date('n') - 1] . ' ' . get_the_date('Y');
    } else {
        $date_display = get_the_date('j F Y');
    }
?>

<!-- ───────── JSON-LD SCHEMA (auto-generated from WP) ───────── -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": <?php echo wp_json_encode(get_the_title()); ?>,
  "description": <?php echo wp_json_encode($excerpt); ?>,
  "image": <?php echo wp_json_encode($featured_url ?: ''); ?>,
  "datePublished": <?php echo wp_json_encode(get_the_date('c')); ?>,
  "dateModified": <?php echo wp_json_encode(get_the_modified_date('c')); ?>,
  "author": { "@type": "Organization", "name": "Tailor Homes", "url": "https://www.tailorhomes.it" },
  "publisher": { "@type": "Organization", "name": "Tailor Homes" },
  "mainEntityOfPage": { "@type": "WebPage", "@id": <?php echo wp_json_encode(get_permalink()); ?> },
  "articleSection": <?php echo wp_json_encode($primary_cat); ?>,
  "inLanguage": "<?php echo $it ? 'it-IT' : 'en-GB'; ?>"
}
</script>

<!-- ───────── ARTICLE STYLES ───────── -->
<style>
:root {
  --bg: #ECEAE4; --ink: #1A1916; --ink-mid: #3A3830; --ink-soft: #8A8780;
  --red: #B8312F; --white: #F5F3EF; --bg-warm: #E5E1DA; --line: #D9D5CC;
}

.th-post { background: var(--bg); color: var(--ink); font-family: 'DM Sans', system-ui, -apple-system, sans-serif; }

/* Hero — accounts for 160px fixed header + breathing room (200px total top padding) */
.th-post__hero { padding: 200px 24px 56px; max-width: 800px; margin: 0 auto; }
.th-post__meta { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 28px; }
.th-post__tag { font-size: 10px; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; color: var(--red); padding: 5px 12px; border: 1px solid var(--red); }
.th-post__date, .th-post__read { font-size: 12px; color: var(--ink-soft); letter-spacing: .04em; text-transform: uppercase; }
.th-post__title { font-family: 'Cormorant Garamond', Georgia, serif; font-weight: 500; font-size: clamp(36px, 5vw, 56px); line-height: 1.1; letter-spacing: -0.01em; margin: 0 0 24px; color: var(--ink); }
.th-post__intro { font-size: 19px; line-height: 1.6; color: var(--ink-mid); margin: 0; }

/* Featured image */
.th-post__featured { width: 100%; max-width: 1200px; margin: 0 auto 56px; aspect-ratio: 16/9; overflow: hidden; background: var(--bg-warm); }
.th-post__featured img { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Post body container */
.th-post__body { max-width: 760px; margin: 0 auto; padding: 0 24px; }
.th-post__body p { font-size: 17px; line-height: 1.75; color: var(--ink-mid); margin: 0 0 24px; }
.th-post__body h2 { font-family: 'Jost', sans-serif; font-weight: 500; font-size: 28px; line-height: 1.25; color: var(--ink); margin: 56px 0 16px; letter-spacing: -0.005em; }
.th-post__body h3 { font-family: 'Jost', sans-serif; font-weight: 500; font-size: 20px; color: var(--ink); margin: 32px 0 8px; }
.th-post__body strong { font-weight: 700; color: var(--ink); }
.th-post__body em { font-style: italic; }
.th-post__body a { color: var(--red); text-decoration: underline; text-underline-offset: 3px; }
.th-post__body a:hover { text-decoration: none; }
.th-post__body img { max-width: 100%; height: auto; display: block; margin: 32px auto; }
.th-post__body figcaption { font-size: 12px; color: var(--ink-soft); text-align: center; font-style: italic; margin-top: 8px; }

/* Booking CTA */
.th-post__book { max-width: 760px; margin: 56px auto 0; background: #1A1916; color: #F5F3EF; padding: 48px 36px; text-align: center; border-radius: 4px; }
.th-post__book-eyebrow { font-family: 'Jost', sans-serif; font-size: 11px; font-weight: 500; letter-spacing: .22em; text-transform: uppercase; color: var(--red); margin-bottom: 14px; }
.th-post__book-title { font-size: clamp(21px, 3vw, 27px); font-weight: 800; letter-spacing: -.02em; color: #F5F3EF; margin: 0 0 12px; }
.th-post__book-text { font-size: 15px; font-weight: 300; color: rgba(245,243,239,.72); line-height: 1.75; max-width: 520px; margin: 0 auto 26px; }
.th-post__book-btn { display: inline-block; background: var(--red); color: #F5F3EF; padding: 14px 30px; border-radius: 2px; font-family: 'Jost', sans-serif; font-weight: 500; font-size: 15px; text-decoration: none; transition: transform .2s, opacity .2s; }
.th-post__book-btn:hover { transform: translateY(-2px); opacity: .92; }
@media(max-width:640px){ .th-post__book { padding: 38px 24px; } }

/* Footer (author + related) */
.th-post__footer { max-width: 760px; margin: 80px auto 0; padding: 0 24px 80px; }
.th-post__author { display: flex; align-items: center; gap: 16px; padding: 32px 0; border-top: 1px solid var(--line); }
.th-post__author-av { width: 56px; height: 56px; border-radius: 50%; background: var(--red); color: var(--white); display: flex; align-items: center; justify-content: center; font-family: 'Jost', sans-serif; font-weight: 500; letter-spacing: 0.05em; }
.th-post__author-name { font-family: 'Jost', sans-serif; font-weight: 500; font-size: 16px; color: var(--ink); }
.th-post__author-role { font-size: 14px; color: var(--ink-soft); }

.th-post__related { margin-top: 48px; }
.th-post__related-head { font-family: 'Jost', sans-serif; font-weight: 500; font-size: 13px; color: var(--ink-soft); letter-spacing: .18em; text-transform: uppercase; margin-bottom: 20px; }
.th-post__related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.th-post__related-card { display: block; padding: 20px; background: var(--white); border: 1px solid var(--line); text-decoration: none; transition: transform .2s, border-color .2s; }
.th-post__related-card:hover { transform: translateY(-2px); border-color: var(--red); }
.th-post__related-tag { font-size: 11px; color: var(--red); letter-spacing: .18em; text-transform: uppercase; margin-bottom: 8px; }
.th-post__related-title { font-family: 'Jost', sans-serif; font-weight: 500; font-size: 15px; color: var(--ink); line-height: 1.4; }

@media (max-width: 720px) {
  .th-post__hero { padding-top: 220px; }
  .th-post__related-grid { grid-template-columns: 1fr; }
  .th-post__title { font-size: 32px; }
}
</style>

<article class="th-post" itemscope itemtype="https://schema.org/Article">

  <!-- ───────── HERO ───────── -->
  <header class="th-post__hero">
    <div class="th-post__meta">
      <?php if ($primary_cat) : ?>
        <span class="th-post__tag"><?php echo esc_html($primary_cat); ?></span>
      <?php endif; ?>
      <time class="th-post__date" datetime="<?php echo esc_attr($date_iso); ?>"><?php echo esc_html($date_display); ?></time>
      <span class="th-post__read"><?php echo esc_html($read_time . ' ' . $read_label); ?></span>
    </div>
    <h1 class="th-post__title" itemprop="headline"><?php the_title(); ?></h1>
    <?php if ($excerpt) : ?>
      <p class="th-post__intro"><?php echo esc_html($excerpt); ?></p>
    <?php endif; ?>
  </header>

  <!-- ───────── FEATURED IMAGE ───────── -->
  <?php if ($featured_url) : ?>
    <div class="th-post__featured">
      <?php echo th_img($featured_url, array('alt' => $featured_alt, 'itemprop' => 'image', 'fetchpriority' => 'high', 'sizes' => '(max-width: 1100px) 100vw, 1100px')); ?>
    </div>
  <?php endif; ?>

  <!-- ───────── BODY (post content from editor) ───────── -->
  <div class="th-post__body" itemprop="articleBody">
    <?php the_content(); ?>
  </div>

  <?php
  // FAQ rich-result schema, when the post stores one in _th_faq_schema
  $th_faq = get_post_meta(get_the_ID(), '_th_faq_schema', true);
  if (!empty($th_faq)) {
      $th_faq = is_string($th_faq) ? json_decode($th_faq, true) : $th_faq;
  }
  if (!empty($th_faq) && is_array($th_faq)) :
      $th_faq_entities = array();
      foreach ($th_faq as $qa) {
          if (empty($qa['q']) || empty($qa['a'])) { continue; }
          $th_faq_entities[] = array(
              '@type'          => 'Question',
              'name'           => wp_strip_all_tags($qa['q']),
              'acceptedAnswer' => array(
                  '@type' => 'Answer',
                  'text'  => wp_strip_all_tags($qa['a']),
              ),
          );
      }
      if (!empty($th_faq_entities)) : ?>
  <script type="application/ld+json"><?php echo wp_json_encode(array(
      '@context'   => 'https://schema.org',
      '@type'      => 'FAQPage',
      'mainEntity' => $th_faq_entities,
  ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
  <?php endif; endif; ?>

  <!-- ───────── BOOKING CTA ───────── -->
  <div class="th-post__book">
    <div class="th-post__book-eyebrow"><?php echo $it ? 'Prenotazione diretta' : 'Book direct'; ?></div>
    <h3 class="th-post__book-title">
      <?php echo $it
        ? 'Stai organizzando un soggiorno a Padova?'
        : 'Planning a stay in Padova?'; ?>
    </h3>
    <p class="th-post__book-text">
      <?php echo $it
        ? 'Disponibilità reale su tutti i nostri appartamenti, conferma immediata e nessuna commissione di intermediazione.'
        : 'Live availability across all our apartments, instant confirmation, and no platform fees in between.'; ?>
    </p>
    <a href="<?php echo esc_url(th_booking_url()); ?>" class="th-post__book-btn" target="_blank" rel="noopener">
      <?php echo $it ? 'Verifica la disponibilità →' : 'Check Availability →'; ?>
    </a>
  </div>

  <!-- ───────── AUTHOR + RELATED ───────── -->
  <footer class="th-post__footer">

    <div class="th-post__author">
      <div class="th-post__author-av">TH</div>
      <div>
        <div class="th-post__author-name">Tailor Homes</div>
        <div class="th-post__author-role">
          <?php echo $it
            ? 'Specialisti in affitti brevi e medio termine a Padova'
            : "Padova's local short &amp; medium-term rental specialists"; ?>
        </div>
      </div>
    </div>

    <?php
    // Related posts: same category, excluding this one, max 3
    $related_args = [
      'posts_per_page' => 3,
      'post__not_in'   => [get_the_ID()],
      'orderby'        => 'date',
      'order'          => 'DESC',
    ];
    if (!empty($cats)) {
      $related_args['category__in'] = wp_list_pluck($cats, 'term_id');
    }
    // Polylang: restrict to current language if available
    if (function_exists('pll_current_language')) {
      $related_args['lang'] = pll_current_language();
    }
    $related = get_posts($related_args);

    if (!empty($related)) : ?>
      <div class="th-post__related">
        <div class="th-post__related-head">
          <?php echo $it ? 'Continua a leggere' : 'Continue Reading'; ?>
        </div>
        <div class="th-post__related-grid">
          <?php foreach ($related as $r) :
            $r_cats = get_the_category($r->ID);
            $r_cat  = !empty($r_cats) ? $r_cats[0]->name : '';
          ?>
            <a href="<?php echo esc_url(get_permalink($r)); ?>" class="th-post__related-card">
              <?php if ($r_cat) : ?>
                <div class="th-post__related-tag"><?php echo esc_html($r_cat); ?></div>
              <?php endif; ?>
              <div class="th-post__related-title"><?php echo esc_html(get_the_title($r)); ?></div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </footer>

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
