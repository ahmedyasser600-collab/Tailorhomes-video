<?php
/**
 * Template Name: Apartments
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/apartments.css">

<?php
// Default images per apartment (used until Customizer galleries are set)
$apt_defaults = array(
  '01' => array(
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/137c862a-e9e6-4abd-9b32-5c77d977a3d9/Screenshot+2026-03-02+032017.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/283b9b20-55e0-4a2d-9c6e-4f1a733821ee/Screenshot+2026-03-02+031801.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/15b5d807-a0d4-4d29-a538-b75d5109751e/Screenshot+2026-03-02+032028.png?content-type=image%2Fpng',
  ),
  '02' => array(
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/f485e493-aadb-41fb-a47e-c69495ea558d/Screenshot+2026-03-02+032039.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/ae8b10d0-af3c-4527-981b-97e8e501975c/Screenshot+2026-03-02+031813.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/137c862a-e9e6-4abd-9b32-5c77d977a3d9/Screenshot+2026-03-02+032017.png?content-type=image%2Fpng',
  ),
  '03' => array(
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/80c1e619-e770-4530-84c7-8e79a850fa63/Screenshot+2026-03-02+032237.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/b2eafc56-99cf-4f24-a806-91dd6e051bd5/Screenshot+2026-03-02+032219.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/881ac643-e1da-4ede-b27f-79f9dc906c6c/Screenshot+2026-03-02+032328.png?content-type=image%2Fpng',
  ),
  '04' => array(
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/df2e9e5f-e1e1-4ed7-bf51-8f2b1e6bc39b/Screenshot+2026-03-02+032511.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/cbed0fca-0e31-4ef2-b2b9-5e5f27e9aadd/Screenshot+2026-03-02+032521.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/52b3e2b2-ec82-455d-b67a-b41e430e2b1d/Screenshot+2026-03-02+032455.png?content-type=image%2Fpng',
  ),
  '05' => array(
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/349cbbb6-5e43-4b6a-b8e4-41bb73010b7e/Screenshot+2026-03-02+032701.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/f4c2795b-8b7f-471b-9fcc-f39337378853/Screenshot+2026-03-02+032631.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/3a1a446c-fdeb-4ae6-8e5d-204be4ef0289/Screenshot+2026-03-02+032640.png?content-type=image%2Fpng',
  ),
  '06' => array(
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/1598913f-d4f7-4449-93d0-21f3896a77ce/Screenshot+2026-03-02+032904.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/679b38a4-a356-4805-aed9-c56605fe5260/Screenshot+2026-03-02+032922.png?content-type=image%2Fpng',
    'https://images.squarespace-cdn.com/content/69942297b0e650257e21ef7a/35adcb22-29a5-495b-b788-f8cfabce9379/Screenshot+2026-03-02+032853.png?content-type=image%2Fpng',
  ),
  '07' => array(
    // Villa Graziosa — upload photos via Customizer
    // Placeholders use existing Cartagena villa shots as temporary fallback; replace in admin
    '',
    '',
    '',
  ),
);

// Apartment info
$apartments = array(
  '01' => array(
    'tag'      => $it ? 'Appartamento 01' : 'Apartment 01',
    'name'     => 'Colore<br>&amp; Design',
    'location' => $it ? 'Centro Storico, Padova — a pochi passi da Piazza dei Signori' : 'Centro Storico, Padova — steps from Piazza dei Signori',
    'desc'     => $it ? "Appartamento al primo piano completamente ristrutturato nel cuore di Padova, a pochi passi da Piazza dei Signori. Arredato con colori vivaci e design moderno, dispone di camera matrimoniale e seconda camera con letto a castello (uno e mezzo + singolo), cucina open-space ampia e completamente attrezzata e bagno con doccia e lavatrice. Al terzo piano, una incantevole terrazza condominiale con vista sui tetti della città. Wi-Fi ad alta velocità, aria condizionata, riscaldamento e finestre insonorizzate garantiscono un soggiorno tranquillo nonostante la posizione centrale. Ideale per esplorare la città." : "Newly renovated first-floor apartment in the heart of Padua, just steps from Piazza dei Signori. Furnished with bright colours and modern design, it features a master bedroom and a second bedroom with bunk beds (1.5 + single), a spacious open-plan kitchen with all appliances, and a bathroom with shower and washing machine. On the third floor, a charming communal terrace offers views over the city rooftops. High-speed Wi-Fi, air conditioning, heating, and soundproof windows ensure a peaceful stay despite the central location. Ideal base for exploring the city.",
    'feats'    => $it ? array('2 Camere', 'Fino a 5 ospiti', 'Terrazza Condominiale', 'Cucina Completa', 'Smart TV', 'Wi-Fi &amp; A/C') : array('2 Bedrooms', 'Sleeps 5', 'Communal Terrace', 'Full Kitchen', 'Smart TVs', 'Wi-Fi &amp; A/C'),
  ),
  '02' => array(
    'tag'      => $it ? 'Appartamento 02' : 'Apartment 02',
    'name'     => 'Fitness<br>&amp; Charme',
    'location' => $it ? 'Centro Storico, Padova' : 'Centro Storico, Padova',
    'desc'     => $it ? "Elegante appartamento al primo piano nel cuore di Padova, completamente rifinito in legno con grande attenzione al dettaglio. Una raffinata scala in legno conduce al soggiorno con divano letto e Smart TV, mentre la cucina di design — con French window sul terrazzo privato — è dotata di lavastoviglie, lavatrice, piano induzione, Nespresso e tutti gli elettrodomestici. La camera matrimoniale offre due finestre e un'area lavoro dedicata. Un suggestivo soppalco completa lo spazio: ideale per yoga, pilates, un allenamento più sportivo o semplicemente per rilassarsi. A pochi passi dalle principali attrazioni, ristoranti e trasporti pubblici." : "Elegant first-floor apartment in the heart of Padua, finished entirely in wood with attention to every detail. A refined wooden staircase leads to a spacious living room with sofa bed and Smart TV, while the designer kitchen — with French window opening onto a private terrace — features dishwasher, washing machine, induction hob, Nespresso, and full appliances. The double bedroom offers two windows and a dedicated workspace. A charming mezzanine completes the space — perfect for yoga, pilates, a sportier workout, or simply unwinding. Steps from all main attractions, restaurants, and public transport.",
    'feats'    => $it ? array('Soppalco', 'Terrazzo Privato', 'Cucina di Design', 'Area Lavoro', 'Smart TV', 'Wi-Fi') : array('Mezzanine Loft', 'Private Terrace', 'Designer Kitchen', 'Workspace', 'Smart TV', 'Wi-Fi'),
  ),
  '03' => array(
    'tag'      => $it ? 'Appartamento 03' : 'Apartment 03',
    'name'     => $it ? 'Appartamenti<br>Locatelli' : 'Locatelli<br>Apartments',
    'location' => $it ? 'Padova — vicino al centro storico' : 'Padova — close to the historic centre',
    'desc'     => $it ? "Spazioso appartamento con due camere da letto e due bagni, perfetto per coppie, famiglie e gruppi. Il soggiorno presenta divano e zona pranzo, offrendo ampio spazio per relax e convivialità. La cucina è completamente attrezzata con macchina del caffè, microonde e lavastoviglie. Tra i comfort: Wi-Fi gratuito, aria condizionata, servizi di streaming, lavatrice, scrivania da lavoro e insonorizzazione. Posizione comoda: a 19 minuti dal Gran Teatro Geox, 1.4 km da Palazzo della Ragione, 1.7 km dalla Cappella degli Scrovegni e 2.9 km dalla stazione di Padova. Aeroporto Marco Polo di Venezia a 47 km. Check-in in presenza con benvenuto personalizzato." : "Spacious apartment with two bedrooms and two bathrooms, perfect for couples, families, and groups. The living room features a sofa and dining area, providing ample space for relaxation and meals. The kitchen is fully equipped with coffee machine, microwave, and dishwasher. Amenities include free Wi-Fi, air conditioning, streaming services, washing machine, work desk, and soundproofing. Convenient location: 19 minutes from Gran Teatro Geox, 0.9 mi from Palazzo della Ragione, 1.1 mi from Scrovegni Chapel, and 1.8 mi from Padova Railway Station. Venice Marco Polo Airport is 29 mi away. In-person check-in with a personal welcome.",
    'feats'    => $it ? array('2 Camere', '2 Bagni', 'Coppie &amp; Famiglie', 'Cucina Completa', 'Wi-Fi &amp; A/C', 'Check-in in Presenza') : array('2 Bedrooms', '2 Bathrooms', 'Couples &amp; Families', 'Full Kitchen', 'Wi-Fi &amp; A/C', 'In-Person Check-in'),
  ),
  '04' => array(
    'tag'      => $it ? 'Appartamento 04' : 'Apartment 04',
    'name'     => 'Marcanova',
    'location' => $it ? 'Padova — vicino a PadovaFiere e Gran Teatro Geox' : 'Padova — near PadovaFiere &amp; Gran Teatro Geox',
    'desc'     => $it ? "Appartamento recentemente ristrutturato nel centro di Padova, con tre camere da letto, due bagni e terrazza. A 1.8 km da PadovaFiere e 19 minuti a piedi dal Gran Teatro Geox. La struttura dispone di ascensore e sicurezza tutto il giorno, con Wi-Fi gratuito in tutto l'edificio. Lo staff in loco può organizzare un servizio navetta. L'ampio appartamento offre vista sul cortile interno, soggiorno con TV a schermo piatto, cucina completamente attrezzata e bagni con bidet e doccia. Asciugamani e biancheria da letto inclusi. Anallergico e non fumatori. A pochi passi dalla Cappella degli Scrovegni, Palazzo della Ragione e dalla stazione centrale. Aeroporto Marco Polo di Venezia a 42 km, con servizio navetta a pagamento disponibile." : "Recently renovated apartment in the centre of Padua with three bedrooms, two bathrooms, and a terrace. 1.1 mi from PadovaFiere and a 19-minute walk from Gran Teatro Geox. The property features an elevator, full-day security, and free Wi-Fi throughout. Staff on site can arrange a shuttle service. The spacious apartment offers inner-courtyard views, a living room with flat-screen TV, a fully equipped kitchen, and two bathrooms with bidet and shower. Towels and bed linen included. Allergy-free and non-smoking. Close to Scrovegni Chapel, Palazzo della Ragione, and Padua Central Station. Venice Marco Polo Airport is 26 mi away, with paid airport shuttle service available.",
    'feats'    => $it ? array('3 Camere', '2 Bagni', 'Terrazza', 'Ascensore', 'Sicurezza 24h', 'Servizio Navetta') : array('3 Bedrooms', '2 Bathrooms', 'Terrace', 'Elevator', '24h Security', 'Shuttle Available'),
  ),
  '05' => array(
    'tag'      => $it ? 'Appartamento 05' : 'Apartment 05',
    'name'     => $it ? 'Appartamento<br>Monselice' : 'Monselice<br>Apartment',
    'location' => $it ? 'Monselice — vicino ai Colli Euganei' : 'Monselice — near the Euganean Hills',
    'desc'     => $it ? "Accogliente appartamento con due camere da letto a Monselice, ideale per chi cerca tranquillità, comfort e una posizione comoda vicino ai Colli Euganei. La vista panoramica sui colli è il suo tratto distintivo: perfetta per godersi paesaggi rilassanti e tramonti memorabili. Spazi luminosi, arredamento curato e tutti i comfort moderni per un soggiorno piacevole — sia per brevi vacanze che per permanenze più lunghe." : "Welcoming two-bedroom apartment in Monselice, ideal for guests seeking tranquillity, comfort, and a convenient position near the Euganean Hills. Its panoramic hills view is the standout feature — perfect for enjoying peaceful landscapes and memorable sunsets. Bright spaces, curated furnishings, and all modern comforts make for a pleasant stay — whether for a short break or a longer visit.",
    'feats'    => $it ? array('2 Camere', 'Vista Colli Euganei', 'Spazi Luminosi', 'Arredo Curato', 'Wi-Fi', 'Comfort Moderni') : array('2 Bedrooms', 'Hills View', 'Bright Spaces', 'Curated Furnishings', 'Wi-Fi', 'Modern Comforts'),
  ),
  '06' => array(
    'tag'      => 'Villa — Cartagena',
    'name'     => 'Casa<br>Almendro',
    'location' => 'Getsemaní, Cartagena de Indias, Colombia',
    'desc'     => $it ? "Una villa coloniale splendidamente restaurata nel quartiere storico di Getsemaní a Cartagena, a pochi passi da Plaza de la Trinidad e dall'iconica Torre dell'Orologio. La proprietà ruota attorno a un magico patio tropicale con piscina privata trasparente e un caratteristico albero di mandorlo, coronata da una terrazza sul tetto con jacuzzi e vista panoramica sulla Città Vecchia. Progettata per il lusso e il comfort, la villa include camere climatizzate con bagno privato, una sala da pranzo per 12 persone e personale di cucina dedicato pronto a preparare ricette tradizionali locali." : 'A stunningly restored colonial villa in the historic Getsemaní neighbourhood of Cartagena, steps from Plaza de la Trinidad and the iconic Clock Tower. The property centres around a magical tropical patio with a private transparent pool and a signature almond tree, crowned by a rooftop terrace with a jacuzzi and panoramic views of the Old City. Designed for luxury and comfort, the villa includes air-conditioned en-suite bedrooms, a dining room for 12, and dedicated kitchen staff ready to prepare traditional local recipes.',
    'feats'    => $it ? array('Piscina Privata', 'Jacuzzi sul Tetto', 'Personale di Cucina', 'Sala da Pranzo (12)', 'Camere con Bagno', 'Vista Città Vecchia') : array('Private Pool', 'Rooftop Jacuzzi', 'Kitchen Staff', 'Dining Room (12)', 'En-Suite Bedrooms', 'Old City Views'),
  ),
  '07' => array(
    'tag'      => $it ? 'Villa — Natura &amp; Mare' : 'Villa — Nature &amp; Sea',
    'name'     => 'Villa<br>Graziosa',
    'location' => $it ? 'Immersa nella natura — a pochi minuti dal mare' : 'Immersed in nature — minutes from the sea',
    'desc'     => $it ? "Una villa immersa nella natura, dove il blu del mare si fonde con il cielo e il tempo rallenta davvero. Interni luminosi e minimalisti in stile mediterraneo moderno, con ampio soggiorno open-space con vista mare, cucina completamente attrezzata e tre camere accoglienti (due matrimoniali e una con letto a castello) servite da due bagni moderni con doccia. Il cuore della villa è all'esterno: piscina a sfioro con vista aperta, area pranzo coperta sotto la pergola, giardino mediterraneo e zone relax circondate dal verde. Posizione tranquilla e riservata, a pochi minuti dalle spiagge. Ideale per coppie, famiglie o piccoli gruppi in cerca di silenzio e privacy totale." : "A villa immersed in nature, where the blue of the sea merges with the sky and time truly slows down. Bright, minimalist interiors with a modern Mediterranean style, featuring a spacious open-plan living area with sea view, a fully equipped kitchen, and three cosy bedrooms (two doubles and one with bunk beds) served by two modern bathrooms with shower. The heart of the villa is outdoors: infinity pool with open view, covered dining area under the pergola, Mediterranean garden, and relaxation zones surrounded by greenery. Quiet, secluded location, just minutes from the beach. Perfect for couples, families, or small groups seeking silence and total privacy.",
    'feats'    => $it ? array('Piscina a Sfioro', 'Vista Mare', '3 Camere', '2 Bagni', 'Pergola &amp; Giardino', 'Check-in in Presenza') : array('Infinity Pool', 'Sea View', '3 Bedrooms', '2 Bathrooms', 'Pergola &amp; Garden', 'In-Person Check-in'),
  ),
);
?>

<div class="th-apts">

  <!-- HERO -->
  <section class="aph">
    <div class="w">
      <div class="aph__inner">
        <h1 class="aph__heading"><?php echo $it ? 'Galleria.' : 'Gallery.'; ?></h1>
        <p class="aph__sub"><?php echo $it ? 'Un estratto dal nostro portfolio. Ogni spazio racconta una storia — scrivici per scoprirle tutte.' : 'A glimpse of our portfolio. Every space tells a story — write to us to discover them all.'; ?></p>
      </div>
    </div>
  </section>

  <!-- ============================================
       PRESTIGE VILLAS SECTION
       ============================================ -->
  <section class="collection-section collection-section--villas">
    <span class="collection-section__numeral" aria-hidden="true">01</span>
    <div class="w">
      <div class="collection-section__head">
        <span class="collection-section__label"><?php echo $it ? 'Ville di Prestigio' : 'Prestige Villas'; ?></span>
        <h2 class="collection-section__title"><?php echo $it ? 'Ville di Prestigio' : 'Prestige Villas'; ?></h2>
        <div class="collection-section__ornament" aria-hidden="true">
          <span class="ln"></span>
          <span class="dm"></span>
          <span class="ln"></span>
        </div>
      </div>
    </div>
  </section>

  <?php
  // Split apartments into villas (06, 07) and apartments (01–05)
  $villa_nums = array('06', '07');
  $apt_nums   = array('01', '02', '03', '04', '05');

  // Render villas first
  foreach ($villa_nums as $num) :
    if (!isset($apartments[$num])) continue;
    $apt = $apartments[$num];
    $images = th_get_apartment_gallery($num, $apt_defaults[$num]);
  ?>
  <section class="apt-section" id="apt-<?php echo $num; ?>">
    <div class="w"><div class="apt-grid">
      <div class="apt-slider rl" data-slider>
        <div class="apt-slides">
          <?php foreach ($images as $i => $url) : ?>
            <div class="apt-slide"><?php echo th_img($url, array('alt' => wp_strip_all_tags($apt['name']) . ' — Photo ' . ($i + 1), 'sizes' => '(max-width: 960px) 100vw, 60vw', 'loading' => $i === 0 ? 'eager' : 'lazy')); ?></div>
          <?php endforeach; ?>
        </div>
        <button class="apt-arrow apt-arrow--prev" aria-label="Previous"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="apt-arrow apt-arrow--next" aria-label="Next"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        <div class="apt-counter"><span class="cur">1</span> / <span class="tot"></span></div>
        <div class="apt-dots"></div>
      </div>
      <div class="apt-info rr d1">
        <div class="apt-tag"><?php echo esc_html($apt['tag']); ?></div>
        <h2 class="apt-name"><?php echo $apt['name']; ?></h2>
        <div class="apt-location"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?php echo $apt['location']; ?></div>
        <p class="apt-desc"><?php echo esc_html($apt['desc']); ?></p>
        <div class="apt-feats"><?php foreach ($apt['feats'] as $f) : ?><span class="apt-feat"><?php echo esc_html($f); ?></span><?php endforeach; ?></div>
        <div class="apt-divider"></div>
        <div class="apt-footer"><a href="<?php echo esc_url(th_url('contact')); ?>" class="apt-btn"><?php echo $it ? 'Contattaci →' : 'Contact Us →'; ?></a></div>
      </div>
    </div></div>
  </section>
  <?php endforeach; ?>

  <!-- ============================================
       APARTMENTS SECTION
       ============================================ -->
  <section class="collection-section collection-section--apartments">
    <span class="collection-section__numeral" aria-hidden="true">02</span>
    <div class="w">
      <div class="collection-section__head">
        <span class="collection-section__label"><?php echo $it ? 'Appartamenti' : 'Apartments'; ?></span>
        <h2 class="collection-section__title"><?php echo $it ? 'Appartamenti' : 'Apartments'; ?></h2>
        <div class="collection-section__ornament" aria-hidden="true">
          <span class="ln"></span>
          <span class="dm"></span>
          <span class="ln"></span>
        </div>
      </div>
    </div>
  </section>

  <?php
  // Render apartments
  foreach ($apt_nums as $num) :
    if (!isset($apartments[$num])) continue;
    $apt = $apartments[$num];
    $images = th_get_apartment_gallery($num, $apt_defaults[$num]);
  ?>
  <section class="apt-section" id="apt-<?php echo $num; ?>">
    <div class="w"><div class="apt-grid">
      <div class="apt-slider rl" data-slider>
        <div class="apt-slides">
          <?php foreach ($images as $i => $url) : ?>
            <div class="apt-slide"><?php echo th_img($url, array('alt' => wp_strip_all_tags($apt['name']) . ' — Photo ' . ($i + 1), 'sizes' => '(max-width: 960px) 100vw, 60vw', 'loading' => $i === 0 ? 'eager' : 'lazy')); ?></div>
          <?php endforeach; ?>
        </div>
        <button class="apt-arrow apt-arrow--prev" aria-label="Previous"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
        <button class="apt-arrow apt-arrow--next" aria-label="Next"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></button>
        <div class="apt-counter"><span class="cur">1</span> / <span class="tot"></span></div>
        <div class="apt-dots"></div>
      </div>
      <div class="apt-info rr d1">
        <div class="apt-tag"><?php echo esc_html($apt['tag']); ?></div>
        <h2 class="apt-name"><?php echo $apt['name']; ?></h2>
        <div class="apt-location"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><?php echo $apt['location']; ?></div>
        <p class="apt-desc"><?php echo esc_html($apt['desc']); ?></p>
        <div class="apt-feats"><?php foreach ($apt['feats'] as $f) : ?><span class="apt-feat"><?php echo esc_html($f); ?></span><?php endforeach; ?></div>
        <div class="apt-divider"></div>
        <div class="apt-footer"><a href="<?php echo esc_url(th_url('contact')); ?>" class="apt-btn"><?php echo $it ? 'Contattaci →' : 'Contact Us →'; ?></a></div>
      </div>
    </div></div>
  </section>
  <?php endforeach; ?>

</div>

<!-- BOTTOM CTA (outside .th-apts so padding isn't reset) -->
<section class="th-dark-cta">
  <div class="w th-dark-cta__inner r">
    <h2 class="th-dark-cta__heading"><?php echo $it ? 'Non sai<br><em>quale scegliere?</em>' : 'Not sure<br><em>which one?</em>'; ?></h2>
    <p class="th-dark-cta__text"><?php echo $it ? 'Raccontaci del tuo soggiorno — date, dimensione del gruppo, cosa conta per te — e troveremo insieme l\'appartamento giusto.' : 'Tell us about your stay — dates, group size, what matters to you — and we\'ll find the right apartment together.'; ?></p>
    <a href="<?php echo esc_url(th_url('contact')); ?>" class="th-btn th-btn--cta"><?php echo $it ? 'Contattaci →' : 'Get in Touch →'; ?></a>
  </div>
</section>

<?php get_footer(); ?>
