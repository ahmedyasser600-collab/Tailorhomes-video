<?php
/**
 * Template Name: Our Services
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
include(get_template_directory() . '/templates/page-services-langs.php');

$service_areas = array(
  array(
    'num' => '01',
    'main_icon' => 'building',
    'title' => $it ? 'Operatività e gestione quotidiana' : 'Operations & Daily Management',
    'lede' => $it ? "Gestiamo tutto ciò che serve per mantenere l'immobile sempre efficiente e pronto ad accogliere ospiti:" : 'We manage everything needed to keep the property efficient and guest-ready at all times:',
    'items' => array(
      array('laundry', $it ? 'pulizie professionali e lavanderia' : 'professional cleaning and laundry'),
      array('wrench', $it ? 'manutenzione ordinaria e straordinaria' : 'routine and extraordinary maintenance'),
      array('chat', $it ? 'gestione ospiti e comunicazione' : 'guest management and communication'),
      array('doc', $it ? 'adempimenti e obblighi connessi' : 'compliance and connected obligations'),
    ),
  ),
  array(
    'num' => '02',
    'main_icon' => 'sofa',
    'title' => $it ? 'Valorizzazione e presentazione' : 'Enhancement & Presentation',
    'lede' => $it ? "Ottimizziamo l'immobile per renderlo competitivo e attrattivo sul mercato:" : 'We optimise the property to make it competitive and attractive on the market:',
    'items' => array(
      array('sofa', $it ? 'interior design e home staging' : 'interior design and home staging'),
      array('camera', $it ? 'fotografia professionale' : 'professional photography'),
      array('megaphone', $it ? 'presentazione e posizionamento degli annunci' : 'listing presentation and positioning'),
    ),
  ),
  array(
    'num' => '03',
    'main_icon' => 'globe',
    'title' => $it ? 'Marketing e performance' : 'Marketing & Performance',
    'lede' => $it ? "Lavoriamo sulla visibilità e sull'ottimizzazione delle performance:" : 'We work on visibility and performance optimisation:',
    'items' => array(
      array('globe', $it ? 'gestione canali e presenza online' : 'channel management and online presence'),
      array('trend', $it ? 'strategie di marketing e distribuzione' : 'marketing and distribution strategies'),
      array('link', $it ? 'sviluppo di canali diretti' : 'development of direct channels'),
    ),
  ),
  array(
    'num' => '04',
    'main_icon' => 'scale',
    'title' => $it ? 'Supporto tecnico e consulenziale' : 'Technical & Advisory Support',
    'lede' => $it ? 'Affianchiamo il cliente anche nelle decisioni più strutturate:' : 'We support clients in their most structured decisions:',
    'items' => array(
      array('building', $it ? 'consulenza immobiliare' : 'real-estate consulting'),
      array('ruler', $it ? 'supporto architettonico' : 'architectural support'),
      array('scale', $it ? 'gestione aspetti legali e fiscali' : 'legal and tax matters'),
    ),
  ),
);
?>
<script>document.body.classList.add('th-solid-header');</script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/services.css">

<section class="sv-hero sv-hero--editorial"><div class="w"><div class="sv-hero__inner sv-hero__inner--editorial">
  <div class="sv-hero__copy">
    <div class="th-section-label r"><?php echo $it ? 'Cosa facciamo' : 'What we do'; ?></div>
    <h1 class="sv-hero__heading sv-hero__heading--editorial r d1"><?php echo th_t($it,'hero_title'); ?></h1>
  </div>
  <p class="sv-hero__sub sv-hero__sub--editorial r d2"><?php echo th_t($it,'hero_sub'); ?></p>
</div></div></section>

<section class="sv-intro"><div class="w">
  <p class="sv-intro__text r"><?php echo th_t($it,'hub_intro'); ?></p>
</div></section>

<section class="sv-service-system"><div class="w">
  <div class="th-section-label r"><?php echo $it ? 'Il Sistema' : 'The System'; ?></div>
  <h2 class="sv-system-title r d1"><?php echo $it ? 'Quattro aree.<br>Un unico obiettivo.' : 'Four areas.<br>One goal.'; ?></h2>
  <p class="sv-system-sub r d2"><?php echo $it ? 'Coordiniamo ogni fase della gestione immobiliare con un metodo chiaro, integrato e orientato alla performance.' : 'We coordinate every phase of property management through a clear, integrated, performance-driven method.'; ?></p>

  <div class="sv-area-grid">
    <?php foreach ($service_areas as $i => $area): ?>
      <article class="sv-area-card r d<?php echo min($i + 1, 4); ?>">
        <div class="sv-area-card__top">
          <span class="sv-area-card__num"><?php echo esc_html($area['num']); ?></span>
          <span class="sv-area-card__main-icon"><?php echo th_area_icon($area['main_icon']); ?></span>
        </div>
        <div class="sv-area-card__body">
          <h3 class="sv-area-card__title"><?php echo esc_html($area['title']); ?></h3>
          <p class="sv-area-card__lede"><?php echo esc_html($area['lede']); ?></p>
          <ul class="sv-area-card__list">
            <?php foreach ($area['items'] as $item): ?>
              <li><?php echo th_area_icon($item[0]); ?><span><?php echo esc_html($item[1]); ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</div></section>

<!-- PER GLI OSPITI -->
<section class="sv-extended"><div class="w">
  <div class="sv-extended__label r"><?php echo th_t($it,'hub_guests_label'); ?></div>
  <h2 class="sv-extended__title r d1"><?php echo th_t($it,'hub_guests_title'); ?></h2>
  <p class="sv-extended__intro r d2"><?php echo th_t($it,'hub_guests_desc'); ?></p>
  <div class="gst-cards">
    <a href="<?php echo esc_url(th_url('students')); ?>" class="gst-card r d1">
      <h3 class="gst-card__title"><?php echo th_t($it,'hub_students_title'); ?></h3>
      <p class="gst-card__desc"><?php echo th_t($it,'hub_students_desc'); ?></p>
      <span class="gst-card__cta"><?php echo th_t($it,'hub_card_btn'); ?></span>
    </a>
    <a href="<?php echo esc_url(th_url('corporate-housing')); ?>" class="gst-card r d2">
      <h3 class="gst-card__title"><?php echo th_t($it,'hub_corporate_title'); ?></h3>
      <p class="gst-card__desc"><?php echo th_t($it,'hub_corporate_desc'); ?></p>
      <span class="gst-card__cta"><?php echo th_t($it,'hub_card_btn'); ?></span>
    </a>
  </div>
</div></section>

<section class="sv-cta"><div class="w sv-cta__inner r">
  <h2 class="sv-cta__heading"><?php echo th_t($it,'cta_heading'); ?></h2>
  <p class="sv-cta__text"><?php echo th_t($it,'cta_text'); ?></p>
  <a href="<?php echo esc_url(th_url('contact')); ?>" class="sv-cta__btn"><?php echo th_t($it,'cta_btn'); ?></a>
</div></section>

<?php get_footer(); ?>
