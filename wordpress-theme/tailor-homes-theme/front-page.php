<?php get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>

<!-- HERO — Full-width photo with logo + tagline overlay -->
<section class="th-hero">
  <?php
    // 1) Check Customizer hero image first (global, language-independent)
    $hero_img = get_theme_mod('th_hero_image', '');
    // 2) Fall back to Featured Image on current page
    if (empty($hero_img) && has_post_thumbnail()) {
        $hero_img = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }
    // 3) If on Italian page and still no image, get it from the English/default version
    if (empty($hero_img) && function_exists('pll_get_post')) {
        $en_page_id = pll_get_post(get_the_ID(), 'en');
        if ($en_page_id && has_post_thumbnail($en_page_id)) {
            $hero_img = get_the_post_thumbnail_url($en_page_id, 'full');
        }
    }
  ?>
  <?php if (!empty($hero_img)) : ?>
    <div class="th-hero__photo" style="background-image:url('<?php echo esc_url($hero_img); ?>')"></div>
  <?php else : ?>
    <div class="th-hero__photo th-hero__photo--placeholder">
      <p>Go to Appearance → Customize → Hero Image to set your hero photo</p>
    </div>
  <?php endif; ?>

  <!-- Overlay content: logo + tagline -->
  <div class="th-hero__overlay">
    <div class="th-hero__overlay-logo">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/th-symbol-transparent.png" alt="Tailor Homes">
    </div>
    <h1 class="th-hero__tagline">Tailoring Your Experience</h1>
    <p class="th-hero__subtitle"><?php echo $it ? 'Gestione Immobiliare — Appartamenti &amp; Ville di Prestigio' : 'Property Management — Apartments &amp; Prestige Villas'; ?></p>
  </div>
</section>

<style>
.th-hero {
  position: relative;
  width: 100%;
  height: 100vh;
  min-height: 500px;
  overflow: hidden;
}
.th-hero__photo {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
}
/* Stronger cinematic overlay for text readability */
.th-hero__photo::after {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse at center, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.5) 100%),
    linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.45) 100%);
}
.th-hero__photo--placeholder {
  background: var(--th-bg-warm, #E5E1DA);
  display: flex;
  align-items: center;
  justify-content: center;
}
.th-hero__photo--placeholder::after {
  display: none;
}
.th-hero__photo--placeholder p {
  font-size: 14px;
  color: rgba(27,42,74,0.35);
  letter-spacing: 0.05em;
}

/* Hero overlay — centered logo + tagline */
.th-hero__overlay {
  position: absolute;
  inset: 0;
  z-index: 2;
  isolation: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 0 24px;
  pointer-events: none;
}
.th-hero__overlay > * {
  pointer-events: auto;
}

/* Decorative line above logo */
.th-hero__overlay::before {
  content: '';
  width: 40px;
  height: 1px;
  background: rgba(245,240,232,0.4);
  margin-bottom: 28px;
}

.th-hero__overlay-logo {
  margin-bottom: 8px;
}

.th-hero__overlay-logo img {
  height: 150px;
  width: auto;
  filter: drop-shadow(0 4px 24px rgba(0,0,0,0.4));
}

.th-hero__tagline {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(30px, 4.5vw, 56px);
  font-weight: 300;
  font-style: italic;
  color: #F5F0E8;
  letter-spacing: 0.04em;
  line-height: 1.2;
  margin: 0;
  text-shadow: 0 2px 30px rgba(0,0,0,0.4), 0 0 60px rgba(0,0,0,0.2);
}

/* Subtitle beneath — bold red, in the TH logo colour, on a soft cream fade */
.th-hero__subtitle {
  display: inline-block;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px;
  font-weight: 700;
  letter-spacing: 0.28em;
  text-transform: uppercase;
  color: var(--th-red);
  background: linear-gradient(
    to right,
    rgba(245, 240, 232, 0) 0%,
    rgba(245, 240, 232, 0.55) 20%,
    rgba(245, 240, 232, 0.55) 80%,
    rgba(245, 240, 232, 0) 100%
  );
  padding: 12px 56px;
  margin-top: 24px;
  text-shadow: none;
}

/* When placeholder (no photo), darker text */
.th-hero__photo--placeholder ~ .th-hero__overlay::before {
  background: rgba(27,42,74,0.2);
}
.th-hero__photo--placeholder ~ .th-hero__overlay .th-hero__overlay-logo img {
  filter: none;
}
.th-hero__photo--placeholder ~ .th-hero__overlay .th-hero__tagline {
  color: var(--th-navy, #1B2A4A);
  text-shadow: none;
}
.th-hero__photo--placeholder ~ .th-hero__overlay .th-hero__subtitle {
  color: var(--th-red);
  text-shadow: none;
}

/* ── MOBILE HERO FIX ── */
@media (max-width: 768px) {
  .th-hero {
    height: 100svh; /* Use small viewport height for mobile browsers */
    min-height: 450px;
  }
  .th-hero__photo {
    /* Focus on the interesting part of the photo on mobile */
    background-position: center 40%;
  }
  .th-hero__overlay-logo img {
    height: 85px;
    margin-bottom: 10px;
  }
  .th-hero__overlay-logo {
    margin-bottom: 12px;
  }
  .th-hero__overlay::before {
    width: 30px;
    margin-bottom: 12px;
  }
  .th-hero__subtitle {
    font-size: 12px;
    letter-spacing: 0.22em;
    margin-top: 18px;
  }
}

@media (max-width: 480px) {
  .th-hero {
    min-height: 400px;
  }
  .th-hero__overlay-logo img {
    height: 65px;
    margin-bottom: 8px;
  }
  .th-hero__overlay-logo {
    margin-bottom: 8px;
  }
}
</style>


<!-- WHAT WE DO — Bento grid -->
<section class="th-bento">
  <div class="w">
    <div class="th-bento__head">
      <div class="th-section-label r"><?php echo $it ? 'Cosa Facciamo' : 'What We Do'; ?></div>
      <div class="th-bento__head-row r d1">
        <h2 class="th-bento__title"><?php echo $it ? 'Quattro pilastri.<br><em>Un unico obiettivo.</em>' : 'Four pillars.<br><em>One goal.</em>'; ?></h2>
        <p class="th-bento__lede"><?php echo $it ? "Dalla gestione operativa quotidiana alla strategia di investimento — coordiniamo ogni area perché tu possa concentrarti sul risultato." : 'From daily operations to investment strategy — we coordinate every area so you can focus on the result.'; ?></p>
      </div>
    </div>

    <div class="th-bento__grid">

      <!-- 01 — DARK HERO CARD (largest, top-left, spans 2 cols on desktop) -->
      <article class="th-bento__card th-bento__card--hero r d1">
        <div class="th-bento__card-num">01</div>
        <div class="th-bento__card-icon th-bento__card-icon--3d">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-home-01-gestione.svg" alt="" loading="lazy">
        </div>
        <h3 class="th-bento__card-title"><?php echo $it ? 'Gestione Completa Immobili' : 'Complete Property Management'; ?></h3>
        <p class="th-bento__card-lede"><?php echo $it ? "Trasformiamo il tuo immobile in una rendita ottimizzata e completamente gestita." : 'We turn your property into an optimised, fully-managed source of income.'; ?></p>
        <div class="th-bento__card-tags">
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>
            <?php echo $it ? 'Affitti Brevi' : 'Short-Term'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <?php echo $it ? 'Medio Termine' : 'Medium-Term'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <?php echo $it ? 'Pricing Dinamico' : 'Dynamic Pricing'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <?php echo $it ? 'Gestione Ospiti 24/7' : '24/7 Guest Care'; ?>
          </span>
        </div>
      </article>

      <!-- 02 — VALUE / ENHANCEMENT (top-right) -->
      <article class="th-bento__card th-bento__card--accent r d2">
        <div class="th-bento__card-num">02</div>
        <div class="th-bento__card-icon th-bento__card-icon--3d">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-home-02-ottimizzazione.svg" alt="" loading="lazy">
        </div>
        <h3 class="th-bento__card-title"><?php echo $it ? 'Ottimizzazione e Valorizzazione' : 'Optimisation & Value'; ?></h3>
        <p class="th-bento__card-lede"><?php echo $it ? "Aumentiamo il valore percepito del tuo immobile per generare più rendimento." : 'We increase your property\'s perceived value to generate higher returns.'; ?></p>
        <div class="th-bento__card-tags">
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-7h-6v7H4a1 1 0 0 1-1-1z"/></svg>
            <?php echo $it ? 'Home Staging' : 'Home Staging'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/><circle cx="12" cy="5" r="3"/></svg>
            <?php echo $it ? 'Interior Design' : 'Interior Design'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 18v3"/><path d="M7 8h.01"/><path d="M11 8h6"/><path d="M7 12h.01"/><path d="M11 12h6"/></svg>
            <?php echo $it ? 'Tech Solutions' : 'Tech Solutions'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <?php echo $it ? 'Accessi Smart' : 'Smart Access'; ?>
          </span>
        </div>
      </article>

      <!-- 03 — CHANNELS (bottom-left) -->
      <article class="th-bento__card th-bento__card--clean r d3">
        <div class="th-bento__card-num">03</div>
        <div class="th-bento__card-icon th-bento__card-icon--3d">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-home-03-clienti.svg" alt="" loading="lazy">
        </div>
        <h3 class="th-bento__card-title"><?php echo $it ? 'Clienti e Canali Garantiti' : 'Guaranteed Clients & Channels'; ?></h3>
        <p class="th-bento__card-lede"><?php echo $it ? "Flussi costanti di prenotazioni qualificate, oltre le OTA." : 'Steady streams of qualified bookings, beyond OTAs.'; ?></p>
        <div class="th-bento__card-tags">
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
            <?php echo $it ? 'Aziende' : 'Corporate'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <?php echo $it ? 'Università' : 'Universities'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 7v10"/><path d="M7 12h10"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
            <?php echo $it ? 'Ospedali' : 'Hospitals'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 22V8l9-6 9 6v14"/><path d="M9 22V12h6v10"/><path d="M3 22h18"/></svg>
            <?php echo $it ? 'Enti Pubblici' : 'Institutions'; ?>
          </span>
        </div>
      </article>

      <!-- 04 — INVESTMENTS / STRATEGY (bottom-right) -->
      <article class="th-bento__card th-bento__card--strategy r d4">
        <div class="th-bento__card-num">04</div>
        <div class="th-bento__card-icon th-bento__card-icon--3d">
          <img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-home-04-consulenza.svg" alt="" loading="lazy">
        </div>
        <h3 class="th-bento__card-title"><?php echo $it ? 'Consulenza e Investimenti' : 'Consulting & Investments'; ?></h3>
        <p class="th-bento__card-lede"><?php echo $it ? "Ti supportiamo nelle decisioni immobiliari per massimizzare il valore nel tempo." : 'We support your real-estate decisions to maximise value over time.'; ?></p>
        <div class="th-bento__card-tags">
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            <?php echo $it ? 'Consulenza Strategica' : 'Strategic Consulting'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <?php echo $it ? 'Analisi Immobile' : 'Property Analysis'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <?php echo $it ? 'Ristrutturazioni' : 'Renovations'; ?>
          </span>
          <span class="th-bento__tag">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            <?php echo $it ? 'Valorizzazione' : 'Value Enhancement'; ?>
          </span>
        </div>
      </article>

    </div>
  </div>
</section>

<style>
.th-bento {
  padding: 110px 0 100px;
  border-bottom: 1px solid rgba(26,25,22,.08);
  background: var(--th-white);
}
.th-bento__head {
  margin-bottom: 64px;
}
.th-bento__head-row {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 80px;
  align-items: end;
  margin-top: 18px;
}
.th-bento__title {
  font-size: clamp(36px, 5.2vw, 68px);
  font-weight: 800;
  letter-spacing: -.035em;
  line-height: 1.02;
  color: var(--th-ink);
  margin: 0;
}
.th-bento__title em {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-weight: 400;
  color: var(--th-red);
  letter-spacing: -.02em;
}
.th-bento__lede {
  font-size: 16px;
  font-weight: 300;
  color: var(--th-ink-mid);
  line-height: 1.75;
  max-width: 480px;
  padding-bottom: 8px;
}

/* === BENTO GRID === */
.th-bento__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: auto auto;
  gap: 20px;
}
.th-bento__card--hero    { grid-column: 1 / 2; grid-row: 1 / 2; }
.th-bento__card--accent  { grid-column: 2 / 3; grid-row: 1 / 2; }
.th-bento__card--clean   { grid-column: 1 / 2; grid-row: 2 / 3; }
.th-bento__card--strategy{ grid-column: 2 / 3; grid-row: 2 / 3; }

/* === BASE CARD === */
.th-bento__card {
  position: relative;
  padding: 52px 48px 48px;
  border: 1px solid rgba(26,25,22,.08);
  border-left: 4px solid var(--th-red);
  background: var(--th-cream);
  transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s ease, border-color .35s ease;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.th-bento__card:hover {
  transform: translateY(-4px);
  box-shadow: 0 24px 60px rgba(26,25,22,.10);
  border-top-color: rgba(26,25,22,.18);
  border-right-color: rgba(26,25,22,.18);
  border-bottom-color: rgba(26,25,22,.18);
}
.th-bento__card-num {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-weight: 300;
  font-size: 22px;
  color: var(--th-red);
  letter-spacing: .02em;
  margin-bottom: 24px;
}
.th-bento__card-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 56px;
  height: 56px;
  margin-bottom: 28px;
  color: var(--th-red);
}
.th-bento__card-icon svg {
  width: 100%;
  height: 100%;
}
.th-bento__card-icon--3d {
  width: 108px;
  height: 108px;
  margin-bottom: 24px;
}
.th-bento__card-icon--3d img {
  width: 100%;
  height: 100%;
  display: block;
}
.th-bento__card-title {
  font-size: clamp(24px, 2.4vw, 32px);
  font-weight: 700;
  letter-spacing: -.02em;
  line-height: 1.15;
  color: var(--th-ink);
  margin-bottom: 16px;
}
.th-bento__card-lede {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-size: 19px;
  font-weight: 400;
  color: var(--th-ink-mid);
  line-height: 1.6;
  margin-bottom: 28px;
  max-width: 540px;
}

/* === HERO CARD — inherits unified card styling === */
.th-bento__card--hero {
  /* All styling now lives in the base .th-bento__card */
}
.th-bento__card-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: auto;
}
.th-bento__tag {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 9px 14px;
  background: rgba(26,25,22,.04);
  border: 1px solid rgba(26,25,22,.12);
  color: var(--th-ink);
  font-family: 'Jost', sans-serif;
  font-size: 11px;
  font-weight: 400;
  letter-spacing: .08em;
  text-transform: uppercase;
  border-radius: 100px;
  transition: background .25s, border-color .25s;
}
.th-bento__tag:hover {
  background: rgba(26,25,22,.08);
  border-color: rgba(26,25,22,.2);
}
.th-bento__tag svg { width: 14px; height: 14px; flex-shrink: 0; color: var(--th-red); }

/* === ACCENT CARD === */
.th-bento__card--accent {
  /* Inherits unified card styling from base */
}

/* === CLEAN CARD === */
.th-bento__card--clean {
  /* Inherits unified card styling from base */
}

/* === STRATEGY CARD (04) — inherits unified card styling, no special overrides === */
.th-bento__card--strategy {
  /* Visual variation comes from the bento layout, not bg/border */
}

/* === RESPONSIVE === */
@media (max-width: 1023px) {
  .th-bento__grid {
    grid-template-columns: 1fr;
    grid-template-rows: auto;
  }
  .th-bento__card--hero,
  .th-bento__card--accent,
  .th-bento__card--clean,
  .th-bento__card--strategy {
    grid-column: 1 / -1;
    grid-row: auto;
  }
  .th-bento__card { min-height: auto; }
}

@media (max-width: 768px) {
  .th-bento { padding: 72px 0; }
  .th-bento__head-row { grid-template-columns: 1fr; gap: 24px; }
  .th-bento__lede { padding-bottom: 0; max-width: none; }
  .th-bento__card { padding: 44px 32px 36px; }
}
</style>




<!-- STUDENTS & CORPORATE CARDS -->
<?php
$th_students_img = get_theme_mod('th_students_card_image', '');
$th_corporate_img = get_theme_mod('th_corporate_card_image', '');
?>
<section class="th-st-square th-promo-square">
  <div class="w">
    <div class="th-promo-grid">
      <a href="<?php echo esc_url(th_url('students')); ?>" class="th-st-card th-promo-card r d1">
        <div class="th-st-card__media"<?php if (!empty($th_students_img)) echo ' style="background-image:url(\'' . esc_url($th_students_img) . '\');"'; ?>>
          <?php if (empty($th_students_img)) : ?>
            <div class="th-st-card__placeholder">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
              <span><?php echo $it ? 'Aggiungi una foto dal Customizer' : 'Add a photo from the Customizer'; ?></span>
            </div>
          <?php endif; ?>
          <span class="th-st-card__badge">−15%</span>
        </div>
        <div class="th-st-card__content">
          <span class="th-st-card__label"><?php echo $it ? 'Studenti & Erasmus' : 'Students & Erasmus'; ?></span>
          <h2 class="th-st-card__title"><?php echo $it ? '15% di sconto per studenti UniPD ed Erasmus.' : '15% off for UniPD & Erasmus students.'; ?></h2>
          <p class="th-st-card__text"><?php echo $it ? 'Studi a Padova? Verifica la tua tessera via WhatsApp e ottieni il 15% su ogni soggiorno — per te, amici e famiglia.' : 'Studying in Padova? Verify your student card via WhatsApp and get 15% off every stay — for you, friends, and family.'; ?></p>
          <span class="th-st-card__cta"><?php echo $it ? 'Richiedi il tuo sconto' : 'Claim your discount'; ?> →</span>
        </div>
      </a>

      <a href="<?php echo esc_url(th_url('corporate-housing')); ?>" class="th-st-card th-st-card--corporate th-promo-card r d2">
        <div class="th-st-card__media"<?php if (!empty($th_corporate_img)) echo ' style="background-image:url(\'' . esc_url($th_corporate_img) . '\');"'; ?>>
          <?php if (empty($th_corporate_img)) : ?>
            <div class="th-st-card__placeholder th-st-card__placeholder--corp">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/><path d="M9 13v2h6v-2"/></svg>
              <span><?php echo $it ? 'Alloggi aziendali' : 'Corporate housing'; ?></span>
            </div>
          <?php endif; ?>
        </div>
        <div class="th-st-card__content">
          <span class="th-st-card__label"><?php echo $it ? 'Per le Aziende' : 'For Companies'; ?></span>
          <h2 class="th-st-card__title"><?php echo $it ? 'Corporate housing su misura per trasferte, fiere e progetti.' : 'Tailored corporate housing for business trips, fairs, and projects.'; ?></h2>
          <p class="th-st-card__text"><?php echo $it ? 'Scopri le soluzioni dedicate.' : 'Discover our dedicated solutions.'; ?></p>
          <span class="th-st-card__cta"><?php echo $it ? 'Scopri le soluzioni dedicate' : 'Discover our dedicated solutions'; ?> →</span>
        </div>
      </a>
    </div>
  </div>
</section>
<style>
.th-st-square { padding: 72px 0 64px; background: var(--th-white); border-bottom: 1px solid rgba(26,25,22,.08); }
.th-promo-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 32px; align-items: stretch; }
.th-st-card {
  display: grid;
  grid-template-columns: 1fr;
  background: var(--th-navy, #1B2A4A);
  text-decoration: none;
  overflow: hidden;
  transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s ease;
  height: 100%;
}
.th-st-card:hover { transform: translateY(-4px); box-shadow: 0 24px 60px rgba(26,25,22,.16); }
.th-st-card__media {
  position: relative;
  min-height: 280px;
  background-size: cover;
  background-position: center;
  background-color: var(--th-bg-warm);
}
.th-st-card__placeholder {
  position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; color: var(--th-ink-soft); text-align: center; padding: 24px;
}
.th-st-card__placeholder svg { width: 48px; height: 48px; opacity: .5; }
.th-st-card__placeholder span { font-size: 12px; letter-spacing: .08em; text-transform: uppercase; }
.th-st-card__placeholder--corp { background: linear-gradient(135deg, #F5F0E8 0%, #ECEAE4 100%); color: var(--th-red); }
.th-st-card__badge {
  position: absolute; top: 24px; left: 24px; background: var(--th-red); color: #fff;
  font-family: 'DM Sans', sans-serif; font-weight: 700; font-size: 20px; letter-spacing: -.01em;
  padding: 10px 18px; border-radius: 100px; box-shadow: 0 6px 20px rgba(0,0,0,.2);
}
.th-st-card__content { padding: 50px 42px; display: flex; flex-direction: column; justify-content: center; }
.th-st-card__label {
  display: inline-block; font-family: 'Jost', sans-serif; font-size: 11px; font-weight: 500;
  letter-spacing: .3em; text-transform: uppercase; color: #E8A598; margin-bottom: 18px;
}
.th-st-card__title {
  font-family: 'Cormorant Garamond', serif; font-style: italic; font-weight: 300;
  font-size: clamp(28px, 3vw, 42px); line-height: 1.12; letter-spacing: -.02em;
  color: #F5F0E8; margin: 0 0 18px;
}
.th-st-card__text {
  font-size: 16px; font-weight: 300; color: rgba(245,240,232,.72); line-height: 1.7; margin: 0 0 28px;
}
.th-st-card__cta {
  font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 600;
  letter-spacing: .18em; text-transform: uppercase; color: #fff;
}
.th-st-card--corporate { background: var(--th-cream); border: 1px solid rgba(26,25,22,.08); }
.th-st-card--corporate .th-st-card__label { color: var(--th-terracotta); }
.th-st-card--corporate .th-st-card__title { color: var(--th-ink); }
.th-st-card--corporate .th-st-card__text { color: var(--th-ink-mid); }
.th-st-card--corporate .th-st-card__cta { color: var(--th-red); }
@media(max-width:960px){
  .th-st-square { padding-top: 72px; }
  .th-promo-grid { grid-template-columns: 1fr; }
  .th-st-card__media { min-height: 240px; }
  .th-st-card__content { padding: 44px 28px; }
}
</style>


<!-- PARTNERS TICKER -->
<section class="th-partners">
  <div class="w">
    <div class="th-section-label r"><?php echo $it ? 'Le Realtà che Serviamo' : 'Organizations We Serve'; ?></div>
  </div>
  <div class="th-partners__track-wrap">
    <div class="th-partners__track">
      <?php
      // Repeat logos 3 times for seamless infinite scroll
      for ($set = 0; $set < 3; $set++) : ?>
      <div class="th-partners__item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/partner-unipd.png" alt="University of Padova">
      </div>
      <div class="th-partners__item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/partner-ospedale-padova-red.jpg" alt="Ospedale di Padova">
      </div>
      <div class="th-partners__item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/partner-teatro.jpg" alt="Teatro Stabile Veneto">
      </div>
      <div class="th-partners__item">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/partner-urbs.png" alt="Padova Urbs Picta">
      </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<style>
.th-partners {
  padding: 56px 0;
  border-bottom: 1px solid rgba(26,25,22,.08);
  background: var(--th-bg-warm);
  overflow: hidden;
}
.th-partners .th-section-label {
  margin-bottom: 36px;
}
.th-partners__track-wrap {
  overflow: hidden;
  width: 100%;
  mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, black 10%, black 90%, transparent 100%);
}
.th-partners__track {
  display: flex;
  align-items: center;
  gap: 100px;
  width: max-content;
  animation: th-partners-scroll 30s linear infinite;
  will-change: transform;
}
.th-partners__track:hover {
  animation-play-state: paused;
}
.th-partners__item {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 120px;
  flex-shrink: 0;
}
.th-partners__item img {
  height: 100px;
  width: auto;
  max-width: 280px;
  object-fit: contain;
  filter: grayscale(100%) opacity(0.45);
  transition: filter .3s ease;
}
.th-partners__item:hover img {
  filter: grayscale(0%) opacity(1);
}
/* UniPD has black background — use screen blend to knock it out */
.th-partners__item img[alt="University of Padova"] {
  mix-blend-mode: multiply;
  filter: grayscale(100%) opacity(0.5) invert(1);
}
.th-partners__item:hover img[alt="University of Padova"] {
  filter: grayscale(0%) opacity(1) invert(1);
}
@keyframes th-partners-scroll {
  0%   { transform: translateX(0); }
  100% { transform: translateX(calc(-100% / 3)); }
}
</style>


<!-- NUMBERS -->
<section style="padding:80px 0;background:var(--th-bg-warm);border-bottom:1px solid rgba(26,25,22,.08);">
  <div class="w">
    <div class="th-numbers-grid">
      <div class="r d1">
        <div class="th-num-big">63</div>
        <div class="th-num-label"><?php echo $it ? 'Immobili Gestiti' : 'Properties Managed'; ?></div>
      </div>
      <div class="r d2">
        <div class="th-num-big">5.0<span class="th-num-star">★</span></div>
        <div class="th-num-label">Google</div>
      </div>
      <div class="r d3">
        <div class="th-num-big">4.89<span class="th-num-star">★</span></div>
        <div class="th-num-label">Airbnb</div>
      </div>
      <div class="r d4">
        <div class="th-num-big">9.2</div>
        <div class="th-num-label">Booking</div>
      </div>
      <div class="r d5">
        <div class="th-num-big">24/7</div>
        <div class="th-num-label"><?php echo $it ? 'Assistenza Ospiti' : 'Guest Support'; ?></div>
      </div>
      <div class="r d6">
        <div class="th-num-big" style="font-size:clamp(20px,2.5vw,32px);">Padova</div>
        <div class="th-num-label"><?php echo $it ? 'La Nostra Sede' : 'Based In'; ?></div>
      </div>
    </div>
  </div>
</section>

<style>
.th-numbers-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 32px;
  text-align: center;
  align-items: end;
}
.th-num-big {
  font-size: clamp(32px, 3.5vw, 48px);
  font-weight: 800;
  color: var(--th-red);
  letter-spacing: -.03em;
  line-height: 1;
}
.th-num-star {
  font-size: .55em;
  vertical-align: super;
  margin-left: 2px;
  color: var(--th-red);
}
.th-num-label {
  font-size: 12px;
  font-weight: 400;
  color: var(--th-ink-soft);
  margin-top: 10px;
  letter-spacing: .06em;
  text-transform: uppercase;
}
@media(max-width:1024px) {
  .th-numbers-grid { grid-template-columns: repeat(3, 1fr); gap: 40px 32px; }
}
@media(max-width:600px) {
  .th-numbers-grid { grid-template-columns: repeat(2, 1fr); gap: 36px 24px; }
}
</style>


<!-- REVIEWS SECTION -->
<?php get_template_part('template-parts/section-reviews'); ?>



<!-- WORK WITH US SQUARE -->
<section class="th-ww-square">
  <div class="w">
    <a href="<?php echo esc_url(th_url('work-with-us')); ?>" class="th-ww-card r">
      <div class="th-ww-card__content">
        <span class="th-ww-card__label"><?php echo $it ? 'Lavora con Noi' : 'Work With Us'; ?></span>
        <h2 class="th-ww-card__title"><?php echo $it ? 'Unisciti al team, diventa partner o collabora con noi.' : 'Join the team, become a partner, or collaborate with us.'; ?></h2>
        <p class="th-ww-card__text"><?php echo $it ? 'Cerchi lavoro, hai un immobile da affidarci o rappresenti un\'azienda? Scopri come possiamo crescere insieme.' : 'Looking for work, have a property to entrust, or represent a business? Discover how we can grow together.'; ?></p>
        <span class="th-ww-card__cta"><?php echo $it ? 'Scopri di più' : 'Find out more'; ?> →</span>
      </div>
    </a>
  </div>
</section>
<style>
.th-ww-square { padding: 0 0 110px; background: var(--th-white); }
.th-ww-card {
  display: block;
  background: var(--th-cream);
  border-left: 4px solid var(--th-red);
  border-top: 1px solid rgba(26,25,22,.08);
  border-right: 1px solid rgba(26,25,22,.08);
  border-bottom: 1px solid rgba(26,25,22,.08);
  padding: 64px 56px;
  text-decoration: none;
  transition: transform .35s cubic-bezier(.16,1,.3,1), box-shadow .35s ease;
}
.th-ww-card:hover { transform: translateY(-4px); box-shadow: 0 24px 60px rgba(26,25,22,.10); }
.th-ww-card__content { max-width: 760px; }
.th-ww-card__label {
  display: inline-block; font-family: 'Jost', sans-serif; font-size: 11px; font-weight: 500;
  letter-spacing: .3em; text-transform: uppercase; color: var(--th-terracotta); margin-bottom: 20px;
}
.th-ww-card__title {
  font-family: 'Cormorant Garamond', serif; font-style: italic; font-weight: 300;
  font-size: clamp(28px, 3.6vw, 46px); line-height: 1.12; letter-spacing: -.02em;
  color: var(--th-ink); margin: 0 0 18px;
}
.th-ww-card__text {
  font-size: 16px; font-weight: 300; color: var(--th-ink-mid); line-height: 1.7; margin: 0 0 28px; max-width: 600px;
}
.th-ww-card__cta {
  font-family: 'DM Sans', sans-serif; font-size: 12px; font-weight: 600;
  letter-spacing: .18em; text-transform: uppercase; color: var(--th-red);
}
@media(max-width:768px){ .th-ww-square{ padding-bottom: 72px; } .th-ww-card{ padding: 44px 28px; } }
</style>

<!-- BOTTOM CTA -->
<section class="th-dark-cta">
  <div class="w th-dark-cta__inner r">
    <h2 class="th-dark-cta__heading"><?php echo $it ? 'Hai un immobile?<br><em>Parliamone.</em>' : 'Own a property?<br><em>Let\'s talk.</em>'; ?></h2>
    <p class="th-dark-cta__text"><?php echo $it ? "Che si tratti di un singolo immobile o di un intero portafoglio, ti supportiamo nella gestione e valorizzazione per ottenere risultati concreti e duraturi nel tempo." : "Whether it is a single property or an entire portfolio, we support you in management and enhancement to deliver concrete results that last over time."; ?></p>
    <a href="<?php echo esc_url(th_url('contact')); ?>" class="th-btn th-btn--cta"><?php echo $it ? 'Contattaci →' : 'Contact Us →'; ?></a>
  </div>
</section>

<?php get_footer(); ?>
