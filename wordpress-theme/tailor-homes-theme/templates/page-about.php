<?php
/**
 * Template Name: About Us
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');

$s = array(
  'hero_h'    => $it ? 'Chi<br>Siamo.'   : 'About<br>Us.',
  'hero_sub'  => $it ? "Un team professionale di gestione immobiliare fondato sulla fiducia, l'esperienza e una genuina passione per l'ospitalità — a Padova dal 2019." : 'A professional property management team built on trust, experience, and a genuine passion for hospitality — based in Padova since 2019.',
  'intro'     => $it ? "Tailor Homes è stata fondata nel <strong>2019</strong> con una semplice convinzione: ogni immobile merita di essere gestito con la stessa cura e attenzione che riserviamo alla nostra casa. Con sede a <strong>Padova, Italia</strong>, offriamo una gestione completa per affitti brevi e a medio termine — sia per appartamenti che per <strong>ville di prestigio</strong>, con un team di specialisti dedicati al successo del tuo immobile." : "Tailor Homes was founded in <strong>2019</strong> with a simple belief: that every property deserves to be managed with the same care and attention as if it were our own home. Based in <strong>Padova, Italy</strong>, we offer end-to-end property management for short-term and medium-term rentals — covering both apartments and <strong>prestige villas</strong>, with a team of specialists dedicated to the success of your property.",

  'founder_label' => $it ? 'La Fondatrice' : 'The Founder',
  'founder_name'  => 'Alice Baggio',
  'founder_role'  => $it ? 'Fondatrice' : 'Founder',
  'founder_p1'    => $it ? "Tailor Homes nasce da un'idea semplice: gestire ogni immobile con lo stesso livello di attenzione, cura e visione strategica con cui si gestisce un vero asset." : "Tailor Homes was born from a simple idea: to manage every property with the same level of attention, care, and strategic vision as a true asset.",
  'founder_p2'    => $it ? "Alice segue direttamente lo sviluppo del progetto, la relazione con i proprietari e l'impostazione delle strategie, con un approccio pratico e orientato al risultato." : "Alice personally oversees the development of the project, the relationship with property owners, and the setting of strategies — with a practical, results-driven approach.",
  'founder_goal'  => $it ? "L'obiettivo è uno: valorizzare ogni immobile e trasformarlo in una fonte di reddito stabile e ottimizzata nel tempo." : "The goal is one: to enhance every property and turn it into a stable, optimised source of income over time.",

  'approach_label' => $it ? 'Il Nostro Approccio' : 'Our Approach',
  'approach_h' => $it ? 'Su misura per<br>ogni immobile.' : 'Tailored to<br>every property.',
  'approach_p1' => $it ? "Non crediamo nelle soluzioni standard. Ogni immobile nel nostro portafoglio riceve una strategia personalizzata — dal pricing alla distribuzione, dalla comunicazione con gli ospiti alla manutenzione. Selezioniamo gli immobili individualmente." : "We don't believe in one-size-fits-all. Each property in our portfolio receives a custom strategy — from pricing and listing to guest communication and maintenance. We select properties individually.",
  'approach_p2' => $it ? "Il nostro team gestisce tutto: dalla fotografia professionale alla distribuzione multicanale, dall'assistenza ospiti 24/7 alle pulizie, alla biancheria, alla conformità legale e ai report mensili. Tu incassi — noi gestiamo." : 'Our team handles everything from professional photography and multi-channel distribution to 24/7 guest support, cleaning, linen, legal compliance, and monthly reporting. You earn — we manage.',

  'founded'   => $it ? 'Fondazione'        : 'Founded',
  'props'     => $it ? 'Immobili Gestiti'  : 'Properties Managed',
  'rating'    => $it ? 'Valutazione Media' : 'Average Rating',
  'support'   => $it ? 'Assistenza Ospiti' : 'Guest Support',

  'sys_label' => $it ? 'Il Sistema' : 'The System',
  'sys_h'     => $it ? 'Un sistema completo,<br>non una semplice gestione.' : 'A complete system,<br>not just management.',
  'sys_p1'    => $it ? "Ogni immobile viene seguito attraverso un sistema integrato che coordina strategia, operatività e valorizzazione." : 'Every property is overseen through an integrated system that coordinates strategy, operations, and value enhancement.',
  'sys_p2'    => $it ? "Ogni area lavora in modo coordinato per garantire continuità, qualità e performance nel tempo." : 'Each area works in coordination to guarantee continuity, quality, and performance over time.',
  'sys_punch' => $it ? "Non gestiamo singole attività. Gestiamo il risultato." : "We don't manage individual tasks. We manage the result.",
  'sys_p3'    => $it ? "Collaboriamo con professionisti dedicati per ogni area, coordinando ogni fase in modo integrato." : 'We collaborate with dedicated professionals in every area, coordinating each phase in an integrated way.',

  'team_label'=> $it ? 'Le Nostre Aree'    : 'Our Areas',
  'team_h'    => $it ? 'Cinque aree,<br>un unico risultato.' : 'Five areas,<br>one result.',
  'team_sub'  => $it ? "Coordiniamo cinque aree operative attraverso un unico referente. Tu hai un solo punto di contatto — noi orchestriamo tutto il resto." : 'We coordinate five operational areas through a single point of contact. You have one person to talk to — we orchestrate everything else.',

  'b1_h'      => $it ? 'Operatività e gestione quotidiana' : 'Operations & Daily Management',
  'b1_lede'   => $it ? "Gestiamo tutto ciò che serve per mantenere l'immobile sempre efficiente e pronto ad accogliere ospiti:" : 'We manage everything needed to keep the property efficient and guest-ready at all times:',
  'b1_l1'     => $it ? 'pulizie professionali e lavanderia' : 'professional cleaning and laundry',
  'b1_l2'     => $it ? 'manutenzione ordinaria e straordinaria' : 'routine and extraordinary maintenance',
  'b1_l3'     => $it ? 'gestione ospiti e comunicazione' : 'guest management and communication',
  'b1_l4'     => $it ? 'adempimenti e obblighi connessi' : 'compliance and connected obligations',

  'b2_h'      => $it ? 'Valorizzazione e presentazione' : 'Enhancement & Presentation',
  'b2_lede'   => $it ? "Ottimizziamo l'immobile per renderlo competitivo e attrattivo sul mercato:" : 'We optimise the property to make it competitive and attractive on the market:',
  'b2_l1'     => $it ? 'interior design e home staging' : 'interior design and home staging',
  'b2_l2'     => $it ? 'fotografia professionale' : 'professional photography',
  'b2_l3'     => $it ? 'presentazione e posizionamento degli annunci' : 'listing presentation and positioning',

  'b3_h'      => $it ? 'Marketing e performance' : 'Marketing & Performance',
  'b3_lede'   => $it ? "Lavoriamo sulla visibilità e sull'ottimizzazione delle performance:" : 'We work on visibility and performance optimisation:',
  'b3_l1'     => $it ? 'gestione canali e presenza online' : 'channel management and online presence',
  'b3_l2'     => $it ? 'strategie di marketing e distribuzione' : 'marketing and distribution strategies',
  'b3_l3'     => $it ? 'sviluppo di canali diretti' : 'development of direct channels',

  'b4_h'      => $it ? 'Supporto tecnico e consulenziale' : 'Technical & Advisory Support',
  'b4_lede'   => $it ? "Affianchiamo il cliente anche nelle decisioni più strutturate:" : 'We support clients in their most structured decisions:',
  'b4_l1'     => $it ? 'consulenza immobiliare' : 'real-estate consulting',
  'b4_l2'     => $it ? 'supporto architettonico' : 'architectural support',
  'b4_l3'     => $it ? 'gestione aspetti legali e fiscali' : 'legal and tax matters',

  'b5_h'      => $it ? 'Un unico referente, zero complessità' : 'One point of contact, zero complexity',
  'b5_p1'     => $it ? 'Tu hai un solo punto di contatto.' : 'You have one point of contact.',
  'b5_p2'     => $it ? 'Noi coordiniamo ogni figura coinvolta, garantendo continuità, qualità e risultati nel tempo.' : 'We coordinate every person involved, guaranteeing continuity, quality, and results over time.',

  'cta_h'     => $it ? "Hai un immobile?<br><em>Parliamone.</em>" : "Have a property?<br><em>Let's talk.</em>",
  'cta_p'     => $it ? "Che si tratti di un singolo immobile o di un intero portafoglio, ti supportiamo nella gestione e valorizzazione per ottenere risultati concreti e duraturi nel tempo." : "Whether it is a single property or an entire portfolio, we support you in management and enhancement to deliver concrete results that last over time.",
  'cta_btn'   => $it ? 'Contattaci →' : 'Contact Us →',
);

$founder_img = get_theme_mod('th_founder_image', '');
if (empty($founder_img)) {
    $founder_img = get_template_directory_uri() . '/assets/images/team/alice-baggio.jpg';
}
?>
<script>document.body.classList.add('th-solid-header');</script>

<style>
/* Intro standfirst — matches the shared services.css treatment */
.sv-intro { padding: 92px 0; background: var(--th-bg-warm); border-bottom: 1px solid rgba(26,25,22,.08); }
.sv-intro__text { position: relative; max-width: 760px; margin: 0; padding-left: 40px; text-align: left; font-size: clamp(18px, 1.7vw, 22px); font-weight: 300; line-height: 1.82; color: var(--th-ink-mid); }
.sv-intro__text::before { content: ''; position: absolute; left: 0; top: 6px; bottom: 6px; width: 2px; background: var(--th-red); }
.sv-intro__text::first-line { color: var(--th-ink); font-weight: 400; }
.sv-intro__text strong { font-weight: 600; color: var(--th-ink); }
@media(max-width:768px){ .sv-intro { padding: 60px 0; } .sv-intro__text { padding-left: 24px; font-size: 17px; line-height: 1.75; } }
</style>

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo $s['hero_h']; ?></h1>
  <p class="sv-hero__sub"><?php echo $s['hero_sub']; ?></p>
</div></div></section>

<section class="sv-intro"><div class="w">
  <p class="sv-intro__text r"><?php echo $s['intro']; ?></p>
</div></section>

<!-- APPROACH -->
<section style="padding:100px 0;border-bottom:1px solid rgba(26,25,22,.08);">
  <div class="w"><div style="display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center;" class="th-about-grid">
    <div class="r">
      <div class="th-section-label"><?php echo $s['approach_label']; ?></div>
      <h2 style="font-size:clamp(28px,3.5vw,42px);font-weight:800;letter-spacing:-.025em;line-height:1.1;margin-bottom:24px;"><?php echo $s['approach_h']; ?></h2>
      <p style="font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;margin-bottom:20px;"><?php echo $s['approach_p1']; ?></p>
      <p style="font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;"><?php echo $s['approach_p2']; ?></p>
    </div>
    <?php
    // 4 area photos for the mosaic
    $area_photos = array(
        1 => array('icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/><rect x="3" y="2" width="18" height="20" rx="2"/></svg>', 'label' => $it ? 'Pulizie & Biancheria' : 'Cleaning & Linen', 'hint' => 'Customize → Area Photo 1'),
        2 => array('icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 9V6a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v3"/><path d="M3 16a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M5 18v2"/><path d="M19 18v2"/></svg>', 'label' => $it ? 'Staging & Design' : 'Staging & Design', 'hint' => 'Customize → Area Photo 2'),
        3 => array('icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>', 'label' => $it ? 'Manutenzione & Tech' : 'Maintenance & Tech', 'hint' => 'Customize → Area Photo 3'),
        4 => array('icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>', 'label' => $it ? 'Marketing & Social' : 'Marketing & Social', 'hint' => 'Customize → Area Photo 4'),
    );
    ?>
    <div class="th-area-mosaic rr d2">
      <?php foreach ($area_photos as $i => $area) :
        $img = get_theme_mod("th_about_area_img_{$i}", '');
      ?>
      <div class="th-area-mosaic__item">
        <?php if (!empty($img)) : ?>
          <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($area['label']); ?>">
          <div class="th-area-mosaic__overlay">
            <span class="th-area-mosaic__overlay-icon"><?php echo $area['icon']; ?></span>
            <span class="th-area-mosaic__overlay-label"><?php echo $area['label']; ?></span>
          </div>
        <?php else : ?>
          <div class="th-area-mosaic__placeholder">
            <span class="th-area-mosaic__ph-icon"><?php echo $area['icon']; ?></span>
            <span class="th-area-mosaic__ph-label"><?php echo $area['label']; ?></span>
            <span class="th-area-mosaic__ph-hint"><?php echo $area['hint']; ?></span>
          </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div></div>
</section>
<style>
@media(max-width:768px){.th-about-grid{grid-template-columns:1fr!important;gap:40px!important;}}

.th-area-mosaic {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: 1fr 1fr;
  gap: 8px;
  aspect-ratio: 4/3;
  overflow: hidden;
}
.th-area-mosaic__item {
  position: relative;
  overflow: hidden;
  background: var(--th-bg-warm);
}
.th-area-mosaic__item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform .4s ease;
}
.th-area-mosaic__item:hover img {
  transform: scale(1.04);
}
.th-area-mosaic__overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 14px 16px;
  background: linear-gradient(transparent, rgba(26,25,22,.78));
  display: flex;
  align-items: center;
  gap: 10px;
  opacity: 1;
  transition: opacity .3s ease;
}
.th-area-mosaic__item:hover .th-area-mosaic__overlay {
  opacity: 1;
}
.th-area-mosaic__overlay-icon {
  width: 18px;
  height: 18px;
  color: var(--th-white);
  flex-shrink: 0;
  display: inline-flex;
}
.th-area-mosaic__overlay-icon svg { width: 100%; height: 100%; }
.th-area-mosaic__overlay-label {
  font-size: 11px;
  font-weight: 500;
  color: var(--th-white);
  letter-spacing: .08em;
  text-transform: uppercase;
}
.th-area-mosaic__placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  text-align: center;
  padding: 16px;
  gap: 8px;
}
.th-area-mosaic__ph-icon {
  width: 32px;
  height: 32px;
  color: var(--th-red);
  opacity: .5;
  display: inline-flex;
}
.th-area-mosaic__ph-icon svg { width: 100%; height: 100%; }
.th-area-mosaic__ph-label {
  font-size: 11px;
  font-weight: 500;
  color: var(--th-ink-soft);
  letter-spacing: .1em;
  text-transform: uppercase;
}
.th-area-mosaic__ph-hint {
  font-size: 9px;
  font-weight: 300;
  color: var(--th-ink-soft);
  opacity: .5;
  letter-spacing: .05em;
}
</style>

<!-- STATS -->
<section style="padding:80px 0;background:var(--th-bg-warm);border-bottom:1px solid rgba(26,25,22,.08);">
  <div class="w"><div style="display:grid;grid-template-columns:repeat(4,1fr);gap:40px;text-align:center;" class="th-about-numbers">
    <div class="r d1"><div style="font-size:clamp(36px,4vw,52px);font-weight:800;color:var(--th-red);letter-spacing:-.03em;line-height:1;">2019</div><div style="font-size:13px;font-weight:400;color:var(--th-ink-soft);margin-top:8px;letter-spacing:.05em;"><?php echo $s['founded']; ?></div></div>
    <div class="r d2"><div style="font-size:clamp(36px,4vw,52px);font-weight:800;color:var(--th-red);letter-spacing:-.03em;line-height:1;">63</div><div style="font-size:13px;font-weight:400;color:var(--th-ink-soft);margin-top:8px;letter-spacing:.05em;"><?php echo $s['props']; ?></div></div>
    <div class="r d3"><div style="font-size:clamp(36px,4vw,52px);font-weight:800;color:var(--th-red);letter-spacing:-.03em;line-height:1;">4.9★</div><div style="font-size:13px;font-weight:400;color:var(--th-ink-soft);margin-top:8px;letter-spacing:.05em;"><?php echo $s['rating']; ?></div></div>
    <div class="r d4"><div style="font-size:clamp(36px,4vw,52px);font-weight:800;color:var(--th-red);letter-spacing:-.03em;line-height:1;">24/7</div><div style="font-size:13px;font-weight:400;color:var(--th-ink-soft);margin-top:8px;letter-spacing:.05em;"><?php echo $s['support']; ?></div></div>
  </div></div>
</section>
<style>@media(max-width:768px){.th-about-numbers{grid-template-columns:repeat(2,1fr)!important;}}</style>

<!-- FOUNDER — Alice Baggio -->
<section class="th-founder">
  <div class="w">
    <div class="th-founder__inner">
      <div class="th-founder__photo r d1">
        <img src="<?php echo esc_url($founder_img); ?>" alt="<?php echo esc_attr($s['founder_name'] . ' — ' . $s['founder_role']); ?>">
      </div>
      <div class="th-founder__content r d2">
        <h2 class="th-founder__name"><?php echo esc_html($s['founder_name']); ?></h2>
        <div class="th-founder__role"><?php echo esc_html($s['founder_role']); ?></div>
        <p class="th-founder__text"><?php echo $s['founder_p1']; ?></p>
        <p class="th-founder__text"><?php echo $s['founder_p2']; ?></p>
        <div class="th-founder__goal">
          <span class="th-founder__goal-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
          </span>
          <p><?php echo $s['founder_goal']; ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.th-founder {
  padding: 110px 0;
  background: var(--th-cream);
  border-bottom: 1px solid rgba(26,25,22,.08);
}
.th-founder__inner {
  display: grid;
  grid-template-columns: 380px 1fr;
  gap: 80px;
  align-items: center;
}
.th-founder__photo {
  aspect-ratio: 1 / 1;
  overflow: hidden;
  border-radius: 50%;
  box-shadow: 0 30px 80px rgba(26,25,22,.12);
  background: var(--th-bg-warm);
}
.th-founder__photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.th-founder__name {
  font-size: clamp(36px, 4.5vw, 56px);
  font-weight: 800;
  letter-spacing: -.025em;
  line-height: 1;
  color: var(--th-ink);
  margin: 18px 0 6px;
}
.th-founder__role {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-size: 18px;
  color: var(--th-red);
  margin-bottom: 28px;
  letter-spacing: .01em;
}
.th-founder__text {
  font-size: 15px;
  font-weight: 300;
  color: var(--th-ink-mid);
  line-height: 1.85;
  margin-bottom: 18px;
}
.th-founder__goal {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-top: 24px;
  padding: 22px 26px;
  background: var(--th-white);
  border-left: 3px solid var(--th-red);
}
.th-founder__goal-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 22px;
  height: 22px;
  color: var(--th-red);
}
.th-founder__goal-icon svg {
  width: 22px;
  height: 22px;
}
.th-founder__goal p {
  font-size: 14px;
  font-weight: 500;
  color: var(--th-ink);
  line-height: 1.6;
  letter-spacing: -.005em;
  margin: 0;
}
@media(max-width: 900px) {
  .th-founder { padding: 72px 0; }
  .th-founder__inner { grid-template-columns: 1fr; gap: 40px; text-align: center; }
  .th-founder__photo { max-width: 280px; margin: 0 auto; }
  .th-founder__goal { text-align: left; }
}
</style>

<!-- SYSTEM intro -->
<section class="th-system">
  <div class="w">
    <div class="th-system__inner">
      <div class="th-section-label r"><?php echo $s['sys_label']; ?></div>
      <h2 class="th-system__h r d1"><?php echo $s['sys_h']; ?></h2>
      <div class="th-system__body">
        <p class="r d2"><?php echo $s['sys_p1']; ?></p>
        <p class="r d3"><?php echo $s['sys_p2']; ?></p>
        <div class="th-system__punch r d4">
          <span class="th-system__punch-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polyline points="20 6 9 17 4 12"/></svg>
          </span>
          <span><?php echo $s['sys_punch']; ?></span>
        </div>
        <p class="r d5"><?php echo $s['sys_p3']; ?></p>
      </div>
    </div>
  </div>
</section>

<style>
.th-system {
  padding: 110px 0;
  border-bottom: 1px solid rgba(26,25,22,.08);
}
.th-system__inner {
  max-width: 880px;
  margin: 0 auto;
}
.th-system__h {
  font-size: clamp(32px, 4.2vw, 52px);
  font-weight: 800;
  letter-spacing: -.025em;
  line-height: 1.1;
  margin: 12px 0 36px;
  color: var(--th-ink);
}
.th-system__body p {
  font-size: 16px;
  font-weight: 300;
  color: var(--th-ink-mid);
  line-height: 1.85;
  margin-bottom: 22px;
}
.th-system__punch {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 18px 26px;
  background: var(--th-ink);
  color: var(--th-white);
  font-size: 16px;
  font-weight: 500;
  letter-spacing: -.005em;
  margin: 14px 0 24px;
}
.th-system__punch-icon {
  display: inline-flex;
  color: var(--th-red);
}
@media(max-width:768px) {
  .th-system { padding: 72px 0; }
}
</style>


<!-- 5-AREAS GRID -->
<section style="padding:110px 0;border-bottom:1px solid rgba(26,25,22,.08);">
  <div class="w">
    <div class="th-section-label r"><?php echo $s['team_label']; ?></div>
    <h2 style="font-size:clamp(28px,3.5vw,48px);font-weight:800;letter-spacing:-.025em;line-height:1.1;margin-bottom:16px;" class="r d1"><?php echo $s['team_h']; ?></h2>
    <p style="font-size:15px;font-weight:300;color:var(--th-ink-soft);max-width:620px;line-height:1.8;margin-bottom:64px;" class="r d2"><?php echo $s['team_sub']; ?></p>

    <div class="th-areas-grid">

      <div class="th-area r d1">
        <div class="th-area__num">01</div>
        <h3 class="th-area__h"><?php echo $s['b1_h']; ?></h3>
        <p class="th-area__lede"><?php echo $s['b1_lede']; ?></p>
        <ul class="th-area__list">
          <li><?php echo th_area_icon('laundry'); ?><span><?php echo $s['b1_l1']; ?></span></li>
          <li><?php echo th_area_icon('wrench'); ?><span><?php echo $s['b1_l2']; ?></span></li>
          <li><?php echo th_area_icon('chat'); ?><span><?php echo $s['b1_l3']; ?></span></li>
          <li><?php echo th_area_icon('doc'); ?><span><?php echo $s['b1_l4']; ?></span></li>
        </ul>
      </div>

      <div class="th-area r d2">
        <div class="th-area__num">02</div>
        <h3 class="th-area__h"><?php echo $s['b2_h']; ?></h3>
        <p class="th-area__lede"><?php echo $s['b2_lede']; ?></p>
        <ul class="th-area__list">
          <li><?php echo th_area_icon('sofa'); ?><span><?php echo $s['b2_l1']; ?></span></li>
          <li><?php echo th_area_icon('camera'); ?><span><?php echo $s['b2_l2']; ?></span></li>
          <li><?php echo th_area_icon('megaphone'); ?><span><?php echo $s['b2_l3']; ?></span></li>
        </ul>
      </div>

      <div class="th-area r d3">
        <div class="th-area__num">03</div>
        <h3 class="th-area__h"><?php echo $s['b3_h']; ?></h3>
        <p class="th-area__lede"><?php echo $s['b3_lede']; ?></p>
        <ul class="th-area__list">
          <li><?php echo th_area_icon('globe'); ?><span><?php echo $s['b3_l1']; ?></span></li>
          <li><?php echo th_area_icon('trend'); ?><span><?php echo $s['b3_l2']; ?></span></li>
          <li><?php echo th_area_icon('link'); ?><span><?php echo $s['b3_l3']; ?></span></li>
        </ul>
      </div>

      <div class="th-area r d4">
        <div class="th-area__num">04</div>
        <h3 class="th-area__h"><?php echo $s['b4_h']; ?></h3>
        <p class="th-area__lede"><?php echo $s['b4_lede']; ?></p>
        <ul class="th-area__list">
          <li><?php echo th_area_icon('building'); ?><span><?php echo $s['b4_l1']; ?></span></li>
          <li><?php echo th_area_icon('ruler'); ?><span><?php echo $s['b4_l2']; ?></span></li>
          <li><?php echo th_area_icon('scale'); ?><span><?php echo $s['b4_l3']; ?></span></li>
        </ul>
      </div>

      <div class="th-area th-area--wide r d5">
        <div class="th-area__num">05</div>
        <h3 class="th-area__h"><?php echo $s['b5_h']; ?></h3>
        <p class="th-area__lede"><strong><?php echo $s['b5_p1']; ?></strong> <?php echo $s['b5_p2']; ?></p>
      </div>

    </div>
  </div>
</section>

<style>
.th-areas-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2px;
  background: rgba(26,25,22,.08);
}
.th-area {
  background: var(--th-white);
  padding: 44px 40px;
  display: flex;
  flex-direction: column;
}
.th-area:nth-child(2n) { background: var(--th-bg-warm); }
.th-area--wide {
  grid-column: 1 / -1;
  background: var(--th-ink) !important;
  color: var(--th-white);
  padding: 52px 48px;
}
.th-area__num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 28px;
  font-weight: 300;
  font-style: italic;
  color: var(--th-red);
  margin-bottom: 18px;
  line-height: 1;
}
.th-area__h {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -.01em;
  margin-bottom: 14px;
  color: var(--th-ink);
}
.th-area--wide .th-area__h {
  color: var(--th-white);
  font-size: 22px;
}
.th-area__lede {
  font-size: 14px;
  font-weight: 300;
  color: var(--th-ink-mid);
  line-height: 1.75;
  margin-bottom: 16px;
}
.th-area--wide .th-area__lede {
  color: rgba(245,243,239,.85);
  font-size: 15px;
  margin-bottom: 0;
}
.th-area__list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.th-area__list li {
  font-size: 13px;
  font-weight: 400;
  color: var(--th-ink);
  padding: 9px 0;
  display: flex;
  align-items: center;
  gap: 10px;
  border-bottom: 1px solid rgba(26,25,22,.06);
  line-height: 1.5;
}
.th-area__list li:last-child {
  border-bottom: none;
}
.th-area__list li svg {
  width: 17px;
  height: 17px;
  flex-shrink: 0;
  color: var(--th-red);
}
.th-area--wide .th-area__list li svg { color: var(--th-red); }
@media(max-width:768px) {
  .th-areas-grid { grid-template-columns: 1fr; }
  .th-area, .th-area--wide { padding: 36px 28px; }
}
</style>

<!-- CTA -->
<section class="th-dark-cta"><div class="w th-dark-cta__inner r">
  <h2 class="th-dark-cta__heading"><?php echo $s['cta_h']; ?></h2>
  <p class="th-dark-cta__text"><?php echo $s['cta_p']; ?></p>
  <a href="<?php echo esc_url(th_url('contact')); ?>" class="th-btn th-btn--cta"><?php echo $s['cta_btn']; ?></a>
</div></section>

<?php get_footer(); ?>
