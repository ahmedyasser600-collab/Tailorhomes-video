<?php
/**
 * Template Name: Students — Erasmus
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
include(get_template_directory() . '/templates/page-guests-langs.php');
?>
<script>document.body.classList.add('th-solid-header');</script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/services.css">

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <div>
    <a href="<?php echo esc_url(th_url('students')); ?>" class="gst-back"><?php echo th_gt($it,'gs_back_students'); ?></a>
    <h1 class="sv-hero__heading"><?php echo th_gt($it,'er_hero_title'); ?></h1>
  </div>
  <p class="sv-hero__sub"><?php echo th_gt($it,'er_hero_sub'); ?></p>
</div></div></section>

<section class="sv-intro"><div class="w">
  <p class="sv-intro__text r"><?php echo th_gt($it,'er_intro'); ?></p>
</div></section>

<section class="sv-services"><div class="w">
  <div class="sv-services__label r"><?php echo th_gt($it,'er_why_label'); ?></div>
  <h2 class="sv-services__title r d1"><?php echo th_gt($it,'er_why_title'); ?></h2>
  <div class="sv-grid sv-grid--two">
    <?php foreach (array(1,2,3,4) as $i): ?>
    <div class="sv-card r d<?php echo $i; ?>"><span class="sv-card__num">0<?php echo $i; ?></span>
      <h3 class="sv-card__heading"><?php echo th_gt($it,'er_b'.$i.'_title'); ?></h3>
      <p style="font-size:14px;font-weight:300;color:var(--th-ink-mid);line-height:1.7;"><?php echo th_gt($it,'er_b'.$i.'_desc'); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="gst-note r d5">
    <?php echo th_gt($it,'er_disc'); ?>
    <div style="margin-top:20px;"><a href="<?php echo esc_url(th_url('students')); ?>#student-discount" class="th-btn th-btn--solid" style="display:inline-block;"><?php echo th_gt($it,'gs_disc_btn'); ?></a></div>
  </div>
</div></section>

<section class="sv-cta"><div class="w sv-cta__inner r">
  <h2 class="sv-cta__heading"><?php echo th_gt($it,'er_cta_heading'); ?></h2>
  <p class="sv-cta__text"><?php echo th_gt($it,'er_cta_text'); ?></p>
  <a href="<?php echo esc_url(th_url('contact')); ?>" class="sv-cta__btn"><?php echo th_gt($it,'gs_cta_btn'); ?></a>
</div></section>

<?php get_footer(); ?>
