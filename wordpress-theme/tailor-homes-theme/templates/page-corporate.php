<?php
/**
 * Template Name: Corporate Housing
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
include(get_template_directory() . '/templates/page-guests-langs.php');
?>
<script>document.body.classList.add('th-solid-header');</script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/services.css">

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo th_gt($it,'co_hero_title'); ?></h1>
  <p class="sv-hero__sub"><?php echo th_gt($it,'co_hero_sub'); ?></p>
</div></div></section>

<section class="sv-intro"><div class="w">
  <p class="sv-intro__text r"><?php echo th_gt($it,'co_intro'); ?></p>
</div></section>

<!-- PHOTO BAND -->
<?php $co_img = get_theme_mod('th_service_img_1',''); if (!empty($co_img)): ?>
<section style="padding:0 0 20px;"><div class="w">
  <div class="r" style="aspect-ratio:21/9;overflow:hidden;"><?php echo th_img($co_img, array('alt' => 'Corporate Housing — Tailor Homes', 'style' => 'width:100%;height:100%;object-fit:cover;display:block;', 'sizes' => '(max-width: 1440px) 100vw, 1320px')); ?></div>
</div></section>
<?php endif; ?>

<section class="sv-services"><div class="w">
  <div class="sv-services__label r"><?php echo th_gt($it,'co_why_label'); ?></div>
  <h2 class="sv-services__title r d1"><?php echo th_gt($it,'co_why_title'); ?></h2>
  <div class="sv-grid sv-grid--two">
    <?php foreach (array(1,2,3,4) as $i): ?>
    <div class="sv-card r d<?php echo $i; ?>"><span class="sv-card__num">0<?php echo $i; ?></span>
      <h3 class="sv-card__heading"><?php echo th_gt($it,'co_b'.$i.'_title'); ?></h3>
      <p style="font-size:14px;font-weight:300;color:var(--th-ink-mid);line-height:1.7;"><?php echo th_gt($it,'co_b'.$i.'_desc'); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</div></section>

<section class="sv-extended"><div class="w">
  <div class="sv-extended__label r"><?php echo th_gt($it,'co_use_label'); ?></div>
  <h2 class="sv-extended__title r d1"><?php echo th_gt($it,'co_use_title'); ?></h2>
  <ul class="gst-tags gst-tags--grid r d2">
    <?php foreach (array(1,2,3,4,5,6) as $i): ?>
    <li><?php echo th_gt($it,'co_u'.$i); ?></li>
    <?php endforeach; ?>
  </ul>
</div></section>

<section class="sv-cta"><div class="w sv-cta__inner r">
  <h2 class="sv-cta__heading"><?php echo th_gt($it,'co_cta_heading'); ?></h2>
  <p class="sv-cta__text"><?php echo th_gt($it,'co_cta_text'); ?></p>
  <a href="<?php echo esc_url(th_url('contact')); ?>" class="sv-cta__btn"><?php echo th_gt($it,'co_cta_btn'); ?></a>
</div></section>

<?php get_footer(); ?>
