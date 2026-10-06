<?php
/**
 * Template Name: Owners / Proprietari
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
include(get_template_directory() . '/templates/page-services-langs.php');
?>
<script>document.body.classList.add('th-solid-header');</script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/services.css">

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo th_t($it,'own_hero_title'); ?></h1>
  <p class="sv-hero__sub"><?php echo th_t($it,'own_hero_sub'); ?></p>
</div></div></section>

<section class="sv-intro"><div class="w">
  <p class="sv-intro__text r"><?php echo th_t($it,'intro'); ?></p>
</div></section>

<section class="sv-services"><div class="w">
  <div class="sv-services__label r"><?php echo th_t($it,'what_we_do'); ?></div>
  <h2 class="sv-services__title r d1"><?php echo th_t($it,'complete_mgmt'); ?></h2>
  <div class="sv-grid">

    <div class="sv-card r d1"><span class="sv-card__num">01</span>
      <div class="sv-card__icon sv-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-01-analisi.svg" alt="" loading="lazy"></div>
      <h3 class="sv-card__heading"><?php echo th_t($it,'c1_title'); ?></h3>
      <ul class="sv-card__list"><li><?php echo th_t($it,'c1_l1'); ?></li><li><?php echo th_t($it,'c1_l2'); ?></li><li><?php echo th_t($it,'c1_l3'); ?></li><li><?php echo th_t($it,'c1_l4'); ?></li></ul>
    </div>

    <div class="sv-card r d2"><span class="sv-card__num">02</span>
      <div class="sv-card__icon sv-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-02-calendario.svg" alt="" loading="lazy"></div>
      <h3 class="sv-card__heading"><?php echo th_t($it,'c2_title'); ?></h3>
      <ul class="sv-card__list"><li><?php echo th_t($it,'c2_l1'); ?></li><li><?php echo th_t($it,'c2_l2'); ?></li><li><?php echo th_t($it,'c2_l3'); ?></li><li><?php echo th_t($it,'c2_l4'); ?></li></ul>
    </div>

    <div class="sv-card r d3"><span class="sv-card__num">03</span>
      <div class="sv-card__icon sv-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-03-proprieta.svg" alt="" loading="lazy"></div>
      <h3 class="sv-card__heading"><?php echo th_t($it,'c3_title'); ?></h3>
      <ul class="sv-card__list"><li><?php echo th_t($it,'c3_l1'); ?></li><li><?php echo th_t($it,'c3_l2'); ?></li><li><?php echo th_t($it,'c3_l3'); ?></li><li><?php echo th_t($it,'c3_l4'); ?></li></ul>
    </div>

    <div class="sv-card r d4"><span class="sv-card__num">04</span>
      <div class="sv-card__icon sv-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-04-documenti.svg" alt="" loading="lazy"></div>
      <h3 class="sv-card__heading"><?php echo th_t($it,'c4_title'); ?></h3>
      <ul class="sv-card__list"><li><?php echo th_t($it,'c4_l1'); ?></li><li><?php echo th_t($it,'c4_l2'); ?></li><li><?php echo th_t($it,'c4_l3'); ?></li><li><?php echo th_t($it,'c4_l4'); ?></li></ul>
    </div>

    <div class="sv-card r d5"><span class="sv-card__num">05</span>
      <div class="sv-card__icon sv-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-05-accesso.svg" alt="" loading="lazy"></div>
      <h3 class="sv-card__heading"><?php echo th_t($it,'c5_title'); ?></h3>
      <ul class="sv-card__list"><li><?php echo th_t($it,'c5_l1'); ?></li><li><?php echo th_t($it,'c5_l2'); ?></li><li><?php echo th_t($it,'c5_l3'); ?></li><li><?php echo th_t($it,'c5_l4'); ?></li></ul>
    </div>

    <div class="sv-card r d6"><span class="sv-card__num">06</span>
      <div class="sv-card__icon sv-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-06-protezione.svg" alt="" loading="lazy"></div>
      <h3 class="sv-card__heading"><?php echo th_t($it,'c6_title'); ?></h3>
      <ul class="sv-card__list"><li><?php echo th_t($it,'c6_l1'); ?></li><li><?php echo th_t($it,'c6_l2'); ?></li><li><?php echo th_t($it,'c6_l3'); ?></li><li><?php echo th_t($it,'c6_l4'); ?></li></ul>
    </div>

  </div>
</div></section>

<section class="sv-extended"><div class="w">
  <div class="sv-extended__label r"><?php echo th_t($it,'beyond'); ?></div>
  <h2 class="sv-extended__title r d1"><?php echo th_t($it,'more_ways'); ?></h2>
  <p class="sv-extended__intro r d2"><?php echo th_t($it,'beyond_intro'); ?></p>
  <div class="sv-ext-grid sv-ext-grid--three">

    <?php
    $svc_hints = array(
        2 => $it ? 'Foto: stanza allestita, interior design, prima/dopo' : 'Photo: styled room, interior design, before/after',
        3 => $it ? 'Foto: fotografo al lavoro, macchina fotografica, set' : 'Photo: photographer at work, camera, property shoot',
        4 => $it ? 'Foto: riunione, stretta di mano, vista città' : 'Photo: meeting, handshake, city view',
    );
    ?>

    <div class="sv-ext-card r d1">
      <?php $simg = get_theme_mod('th_service_img_2',''); ?>
      <div class="sv-ext-card__photo">
        <?php if(!empty($simg)): ?>
          <img src="<?php echo esc_url($simg); ?>" alt="">
        <?php else: ?>
          <div class="sv-ext-card__ph"><span><?php echo $svc_hints[2]; ?></span><span class="sv-ext-card__ph-hint">Customize → Services Photos → 2</span></div>
        <?php endif; ?>
      </div>
      <div class="sv-ext-card__content">
        <span class="sv-card__num">07</span>
        <div class="sv-ext-card__icon sv-ext-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-08-staging.svg" alt="" loading="lazy"></div>
        <h3 class="sv-ext-card__heading"><?php echo th_t($it,'e2_title'); ?></h3>
        <p class="sv-ext-card__desc"><?php echo th_t($it,'e2_desc'); ?></p>
        <ul class="sv-ext-card__tags"><li><?php echo th_t($it,'e2_t1'); ?></li><li><?php echo th_t($it,'e2_t2'); ?></li><li><?php echo th_t($it,'e2_t3'); ?></li><li><?php echo th_t($it,'e2_t4'); ?></li></ul>
      </div>
    </div>

    <div class="sv-ext-card r d2">
      <?php $simg = get_theme_mod('th_service_img_3',''); ?>
      <div class="sv-ext-card__photo">
        <?php if(!empty($simg)): ?>
          <img src="<?php echo esc_url($simg); ?>" alt="">
        <?php else: ?>
          <div class="sv-ext-card__ph"><span><?php echo $svc_hints[3]; ?></span><span class="sv-ext-card__ph-hint">Customize → Services Photos → 3</span></div>
        <?php endif; ?>
      </div>
      <div class="sv-ext-card__content">
        <span class="sv-card__num">08</span>
        <div class="sv-ext-card__icon sv-ext-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-09-photography.svg" alt="" loading="lazy"></div>
        <h3 class="sv-ext-card__heading"><?php echo th_t($it,'e3_title'); ?></h3>
        <p class="sv-ext-card__desc"><?php echo th_t($it,'e3_desc'); ?></p>
        <ul class="sv-ext-card__tags"><li><?php echo th_t($it,'e3_t1'); ?></li><li><?php echo th_t($it,'e3_t2'); ?></li><li><?php echo th_t($it,'e3_t3'); ?></li><li><?php echo th_t($it,'e3_t4'); ?></li></ul>
      </div>
    </div>

    <div class="sv-ext-card r d3">
      <?php $simg = get_theme_mod('th_service_img_4',''); ?>
      <div class="sv-ext-card__photo">
        <?php if(!empty($simg)): ?>
          <img src="<?php echo esc_url($simg); ?>" alt="">
        <?php else: ?>
          <div class="sv-ext-card__ph"><span><?php echo $svc_hints[4]; ?></span><span class="sv-ext-card__ph-hint">Customize → Services Photos → 4</span></div>
        <?php endif; ?>
      </div>
      <div class="sv-ext-card__content">
        <span class="sv-card__num">09</span>
        <div class="sv-ext-card__icon sv-ext-card__icon--3d"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/icons/icon-sv-10-consulenza.svg" alt="" loading="lazy"></div>
        <h3 class="sv-ext-card__heading"><?php echo th_t($it,'e4_title'); ?></h3>
        <p class="sv-ext-card__desc"><?php echo th_t($it,'e4_desc'); ?></p>
        <ul class="sv-ext-card__tags"><li><?php echo th_t($it,'e4_t1'); ?></li><li><?php echo th_t($it,'e4_t2'); ?></li><li><?php echo th_t($it,'e4_t3'); ?></li><li><?php echo th_t($it,'e4_t4'); ?></li></ul>
      </div>
    </div>

  </div>
</div></section>

<section class="sv-highlight">
  <div class="sv-highlight__bg">TAILOR</div>
  <div class="w"><div class="sv-highlight__inner r"><div class="sv-highlight__text">
    <h3 class="sv-highlight__heading"><?php echo th_t($it,'no_fees'); ?></h3>
    <p class="sv-highlight__desc"><?php echo th_t($it,'no_fees_desc'); ?></p>
  </div></div></div>
</section>

<section class="sv-steps"><div class="w">
  <div class="sv-steps__label r"><?php echo th_t($it,'how_it_works'); ?></div>
  <h2 class="sv-steps__title r d1"><?php echo th_t($it,'four_steps'); ?></h2>
  <div class="sv-timeline">
    <div class="sv-step r d1"><div class="sv-step__num">1</div><h4 class="sv-step__heading"><?php echo th_t($it,'s1_title'); ?></h4><p class="sv-step__desc"><?php echo th_t($it,'s1_desc'); ?></p></div>
    <div class="sv-step r d2"><div class="sv-step__num">2</div><h4 class="sv-step__heading"><?php echo th_t($it,'s2_title'); ?></h4><p class="sv-step__desc"><?php echo th_t($it,'s2_desc'); ?></p></div>
    <div class="sv-step r d3"><div class="sv-step__num">3</div><h4 class="sv-step__heading"><?php echo th_t($it,'s3_title'); ?></h4><p class="sv-step__desc"><?php echo th_t($it,'s3_desc'); ?></p></div>
    <div class="sv-step r d4"><div class="sv-step__num">4</div><h4 class="sv-step__heading"><?php echo th_t($it,'s4_title'); ?></h4><p class="sv-step__desc"><?php echo th_t($it,'s4_desc'); ?></p></div>
  </div>
</div></section>

<section class="sv-faq"><div class="w">
  <div class="sv-faq__label r"><?php echo th_t($it,'faq_label'); ?></div>
  <h2 class="sv-faq__title r d1"><?php echo th_t($it,'faq_title'); ?></h2>
  <?php
  // faq2 (upfront costs) and faq4 (insurance) intentionally removed per content review
  $faqs = array('faq1','faq3','faq5','faq6');
  foreach($faqs as $i => $faq): $d = 'd'.($i+1); ?>
  <div class="sv-faq-item r <?php echo $d; ?>">
    <div class="sv-faq-q">
      <span class="sv-faq-q__text"><?php echo th_t($it,$faq.'_q'); ?></span>
      <span class="sv-faq-q__icon"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
    </div>
    <div class="sv-faq-a"><p class="sv-faq-a__text"><?php echo th_t($it,$faq.'_a'); ?></p></div>
  </div>
  <?php endforeach; ?>

  <!-- Ask-your-own-question CTA -->
  <div class="sv-ask r d5">
    <div class="sv-ask__inner">
      <div class="sv-ask__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <div class="sv-ask__text">
        <h3 class="sv-ask__title"><?php echo th_t($it,'ask_q_title'); ?></h3>
        <p class="sv-ask__desc"><?php echo th_t($it,'ask_q_text'); ?></p>
      </div>
      <a href="<?php echo esc_url(th_url('contact')); ?>" class="sv-ask__btn"><?php echo th_t($it,'ask_q_btn'); ?></a>
    </div>
  </div>
</div></section>

<style>
.sv-ask {
  margin-top: 60px;
  padding: 36px 40px;
  background: var(--th-cream);
  border-left: 3px solid var(--th-red);
}
.sv-ask__inner {
  display: grid;
  grid-template-columns: 48px 1fr auto;
  align-items: center;
  gap: 24px;
}
.sv-ask__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--th-white);
  color: var(--th-red);
  flex-shrink: 0;
}
.sv-ask__title {
  font-size: 18px;
  font-weight: 700;
  color: var(--th-ink);
  letter-spacing: -.01em;
  margin-bottom: 4px;
}
.sv-ask__desc {
  font-size: 13px;
  font-weight: 300;
  color: var(--th-ink-mid);
  line-height: 1.6;
}
.sv-ask__btn {
  display: inline-flex;
  align-items: center;
  padding: 14px 28px;
  background: var(--th-ink);
  color: var(--th-white);
  font-family: 'DM Sans', sans-serif;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: .18em;
  text-transform: uppercase;
  text-decoration: none;
  transition: background .25s, transform .25s;
  white-space: nowrap;
}
.sv-ask__btn:hover {
  background: var(--th-red);
  transform: translateY(-2px);
}
@media(max-width: 768px) {
  .sv-ask { padding: 28px 24px; }
  .sv-ask__inner { grid-template-columns: 1fr; gap: 16px; text-align: left; }
  .sv-ask__btn { justify-self: start; }
}
</style>

<section class="sv-cta"><div class="w sv-cta__inner r">
  <h2 class="sv-cta__heading"><?php echo th_t($it,'cta_heading'); ?></h2>
  <p class="sv-cta__text"><?php echo th_t($it,'cta_text'); ?></p>
  <a href="<?php echo esc_url(th_url('contact')); ?>" class="sv-cta__btn"><?php echo th_t($it,'cta_btn'); ?></a>
</div></section>

<?php get_footer(); ?>
