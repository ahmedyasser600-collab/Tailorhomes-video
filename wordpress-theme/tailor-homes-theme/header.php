<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
  <style>
    /* 
       CRITICAL HEADER CSS 
       Ensures the header is transparent and floats over the content.
    */
    .th-header { 
      position: absolute; /* Floating over content */
      top: 0; 
      left: 0; 
      right: 0; 
      width: 100%; 
      z-index: 9999; 
      background: transparent !important; 
      height: 160px; 
      transition: all 0.4s ease;
      display: flex;
      align-items: center;
    }
    
    .th-header.th-scrolled {
      position: fixed;
      height: 115px;
      background: rgba(245, 240, 232, 0.95) !important;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      box-shadow: 0 2px 20px rgba(0,0,0,0.05);
    }

    .th-header__inner {
      width: 100%;
      max-width: 1440px;
      margin: 0 auto;
      padding: 0 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .th-header__logo img {
      height: 100px;
      width: auto;
      transition: all 0.4s ease;
    }
    
    .th-header.th-scrolled .th-header__logo img {
      height: 78px;
    }

    /* Invert symbol for dark hero backgrounds */
    .th-header:not(.th-scrolled) .th-header__logo img {
      filter: brightness(0) invert(1);
    }

    /* LOADER CSS */
    .th-loader { position: fixed; inset: 0; z-index: 999999; background: #F5F0E8; display: flex; align-items: center; justify-content: center; flex-direction: column; transition: opacity 0.5s, visibility 0.5s; animation: th-auto-hide 0.5s 4s forwards; }
    @keyframes th-auto-hide { to { opacity: 0; visibility: hidden; pointer-events: none; } }
    .th-loader.loaded { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
    .th-loader__logo { height: 100px; width: auto; }
  </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- LOADING SCREEN -->
<div class="th-loader" id="th-loader">
  <div style="text-align: center;">
    <?php if (has_custom_logo()) :
      $logo_id = get_theme_mod('custom_logo');
      $logo_url = wp_get_attachment_image_url($logo_id, 'full');
    ?>
      <img class="th-loader__logo" src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>">
    <?php else : ?>
      <img class="th-loader__logo" src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="Tailor Homes">
    <?php endif; ?>
  </div>
</div>

<script>
  (function() {
    function hide() { var l = document.getElementById('th-loader'); if (l) l.classList.add('loaded'); }
    window.addEventListener('load', hide);
    setTimeout(hide, 2500);
  })();
</script>

<!-- HEADER -->
<header class="th-header" id="header">
  <div class="th-header__inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="th-header__logo">
      <?php if (has_custom_logo()) :
        $logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
      ?>
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>">
      <?php else : ?>
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="Tailor Homes">
      <?php endif; ?>
    </a>

    <nav class="th-nav">
      <?php wp_nav_menu(array(
        'theme_location' => 'primary',
        'container' => false,
        'items_wrap' => '%3$s',
        'walker' => new TH_Simple_Walker(),
        'fallback_cb' => 'tailorhomes_fallback_menu',
      )); ?>
    </nav>

    <!-- LANGUAGE SWITCHER -->
    <?php if (function_exists('pll_the_languages')) :
      $langs = pll_the_languages(array('raw' => 1));
      if (!empty($langs)) :
        $current = array_filter($langs, fn($l) => $l['current_lang']);
        $current = reset($current);
    ?>
    <div class="th-lang-switcher">
      <div class="th-lang-current">
        <?php if (!empty($current['flag'])) : ?>
          <img src="<?php echo esc_url($current['flag']); ?>" alt="<?php echo esc_attr($current['name']); ?>">
        <?php endif; ?>
        <svg class="th-lang-arrow" viewBox="0 0 10 6" width="8" height="8"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
      </div>
      <div class="th-lang-dropdown">
        <?php foreach ($langs as $lang) : ?>
          <a href="<?php echo esc_url($lang['url']); ?>" class="th-lang-option <?php echo $lang['current_lang'] ? 'active' : ''; ?>">
            <?php if (!empty($lang['flag'])) : ?>
              <img src="<?php echo esc_url($lang['flag']); ?>" alt="<?php echo esc_attr($lang['name']); ?>">
            <?php endif; ?>
            <span><?php echo esc_html(strtoupper($lang['slug'])); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; endif; ?>

    <button class="th-burger" id="th-burger" aria-label="Toggle Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<!-- MOBILE MENU -->
<div class="th-mobile-menu" id="th-mobile-menu">
  <?php wp_nav_menu(array(
    'theme_location' => 'primary',
    'container' => false,
    'items_wrap' => '%3$s',
    'walker' => new TH_Simple_Walker(),
    'fallback_cb' => 'tailorhomes_fallback_menu',
  )); ?>
  <?php if (function_exists('pll_the_languages')) :
    $langs = pll_the_languages(array('raw' => 1));
    if (!empty($langs)) : ?>
  <div class="th-lang-switcher th-lang-switcher--mobile">
    <?php foreach ($langs as $lang) : ?>
      <a href="<?php echo esc_url($lang['url']); ?>" class="th-lang-option <?php echo $lang['current_lang'] ? 'active' : ''; ?>">
        <?php if (!empty($lang['flag'])) : ?>
          <img src="<?php echo esc_url($lang['flag']); ?>" alt="<?php echo esc_attr($lang['name']); ?>">
        <?php endif; ?>
        <span><?php echo esc_html(strtoupper($lang['slug'])); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; endif; ?>
</div>

<main>
