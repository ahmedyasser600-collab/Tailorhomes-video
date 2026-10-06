<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>document.documentElement.classList.add('js');</script>
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

  </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="th-skip-link" href="#main"><?php echo (function_exists('pll_current_language') && pll_current_language() === 'it') ? 'Vai al contenuto' : 'Skip to content'; ?></a>

<!-- HEADER -->
<header class="th-header" id="header">
  <div class="th-header__inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="th-header__logo" aria-label="<?php echo esc_attr(get_bloginfo('name') ?: 'Tailor Homes'); ?> — Home">
      <?php echo th_logo_img(); ?>
    </a>

    <nav class="th-nav" aria-label="<?php echo (function_exists('pll_current_language') && pll_current_language() === 'it') ? 'Menu principale' : 'Main menu'; ?>">
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
      <button type="button" class="th-lang-current" aria-haspopup="true" aria-expanded="false" aria-label="<?php echo esc_attr(($current['name'] ?? 'Language') . ' — ' . 'Language / Lingua'); ?>">
        <?php if (!empty($current['flag'])) : ?>
          <img src="<?php echo esc_url($current['flag']); ?>" alt="" width="20" height="14">
        <?php endif; ?>
        <svg class="th-lang-arrow" viewBox="0 0 10 6" width="8" height="8" aria-hidden="true" focusable="false"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
      </button>
      <div class="th-lang-dropdown">
        <?php foreach ($langs as $lang) : ?>
          <a href="<?php echo esc_url($lang['url']); ?>" class="th-lang-option <?php echo $lang['current_lang'] ? 'active' : ''; ?>" hreflang="<?php echo esc_attr($lang['slug']); ?>" lang="<?php echo esc_attr($lang['slug']); ?>" aria-label="<?php echo esc_attr($lang['name']); ?>"<?php echo $lang['current_lang'] ? ' aria-current="true"' : ''; ?>>
            <?php if (!empty($lang['flag'])) : ?>
              <img src="<?php echo esc_url($lang['flag']); ?>" alt="" width="20" height="14">
            <?php endif; ?>
            <span><?php echo esc_html(strtoupper($lang['slug'])); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; endif; ?>

    <button type="button" class="th-burger" id="th-burger" aria-label="<?php echo (function_exists('pll_current_language') && pll_current_language() === 'it') ? 'Apri menu' : 'Open menu'; ?>" aria-expanded="false" aria-controls="th-mobile-menu">
      <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
    </button>
  </div>
</header>

<!-- MOBILE MENU -->
<nav class="th-mobile-menu" id="th-mobile-menu" aria-label="<?php echo (function_exists('pll_current_language') && pll_current_language() === 'it') ? 'Menu mobile' : 'Mobile menu'; ?>" aria-hidden="true" inert>
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
      <a href="<?php echo esc_url($lang['url']); ?>" class="th-lang-option <?php echo $lang['current_lang'] ? 'active' : ''; ?>" hreflang="<?php echo esc_attr($lang['slug']); ?>" lang="<?php echo esc_attr($lang['slug']); ?>" aria-label="<?php echo esc_attr($lang['name']); ?>"<?php echo $lang['current_lang'] ? ' aria-current="true"' : ''; ?>>
        <?php if (!empty($lang['flag'])) : ?>
          <img src="<?php echo esc_url($lang['flag']); ?>" alt="" width="20" height="14">
        <?php endif; ?>
        <span><?php echo esc_html(strtoupper($lang['slug'])); ?></span>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; endif; ?>
</nav>

<main id="main" tabindex="-1">
