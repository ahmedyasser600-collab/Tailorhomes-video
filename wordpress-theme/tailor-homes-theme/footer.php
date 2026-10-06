</main>

<?php $it = (function_exists('pll_current_language') && pll_current_language() === 'it'); ?>

<footer class="th-footer">
  <div class="w">
    <div class="th-footer__inner">
      <div class="th-footer__logo">
        <a href="<?php echo esc_url(home_url('/')); ?>">
          <?php if (has_custom_logo()) :
            $logo_id = get_theme_mod('custom_logo');
            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
          ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>">
          <?php else : ?>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="Tailor Homes">
          <?php endif; ?>
        </a>
        <div class="th-footer__social">
          <a href="https://www.facebook.com/profile.php?id=61575933561481" target="_blank" rel="noopener" aria-label="Facebook"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
          <a href="https://www.instagram.com/tailor_homes/" target="_blank" rel="noopener" aria-label="Instagram"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
          <a href="https://www.youtube.com/@TailorHomes-Studio" target="_blank" rel="noopener" aria-label="YouTube"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg></a>
          <a href="https://www.linkedin.com/company/tailor-homes/" target="_blank" rel="noopener" aria-label="LinkedIn"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>
        </div>
      </div>
      <div class="th-footer__nav">
        <div class="th-footer__nav-col">
          <h4><?php echo $it ? 'Navigazione' : 'Navigation'; ?></h4>
          <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
          <a href="<?php echo esc_url(th_url('about-us')); ?>"><?php echo $it ? 'Chi Siamo' : 'About Us'; ?></a>
          <a href="<?php echo esc_url(th_url('our-services')); ?>"><?php echo $it ? 'I Nostri Servizi' : 'Our Services'; ?></a>
          <a href="<?php echo esc_url(th_url('owners')); ?>"><?php echo $it ? 'Per i Proprietari' : 'For Owners'; ?></a>
          <a href="<?php echo esc_url(th_url('corporate-housing')); ?>">Corporate Housing</a>
          <a href="<?php echo esc_url(th_url('apartments')); ?>"><?php echo $it ? 'Galleria' : 'Gallery'; ?></a>
        </div>
        <div class="th-footer__nav-col">
          <h4><?php echo $it ? 'Risorse' : 'Resources'; ?></h4>
          <a href="<?php echo esc_url(th_url('contact')); ?>"><?php echo $it ? 'Contatti' : 'Contact'; ?></a>
          <a href="<?php echo esc_url(th_url('work-with-us')); ?>"><?php echo $it ? 'Lavora con Noi' : 'Work With Us'; ?></a>
          <a href="<?php echo esc_url(th_url('students')); ?>"><?php echo $it ? 'Studenti & Erasmus' : 'Students & Erasmus'; ?></a>
          <a href="<?php echo esc_url(home_url('/')); ?>#reviews"><?php echo $it ? 'Recensioni' : 'Reviews'; ?></a>
          <a href="<?php echo esc_url(th_url('blog')); ?>">Blog</a>
          <a href="<?php echo esc_url(th_url('privacy-policy')); ?>">Privacy Policy</a>
          <a href="<?php echo esc_url(th_url('terms-and-conditions')); ?>"><?php echo $it ? 'Termini e Condizioni' : 'Terms &amp; Conditions'; ?></a>
        </div>
      </div>
      <div class="th-footer__contact">
        <h4><?php echo $it ? 'Contattaci' : 'Get in Touch'; ?></h4>
        <div class="th-footer__contact-list">
          <span class="th-footer__contact-line">Via degli Obizzi, 1<br>Padova, Italia</span>
          <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a>
          <a href="tel:+393714453904">+39 371 445 3904</a>
          <a href="tel:+390494906189">+39 049 490 6189</a>
        </div>
      </div>
    </div>
    <div class="th-footer__bottom">
      <p>&copy; <?php echo date('Y'); ?> VIAL SRLS &mdash; P.IVA: 05721710282. <?php echo $it ? 'Tutti i diritti riservati.' : 'All rights reserved.'; ?></p>
      <div class="th-footer__legal">
        <a href="<?php echo esc_url(th_url('privacy-policy')); ?>">Privacy Policy</a>
        <span>·</span>
        <a href="<?php echo esc_url(th_url('terms-and-conditions')); ?>"><?php echo $it ? 'Termini e Condizioni' : 'Terms &amp; Conditions'; ?></a>
      </div>
    </div>
  </div>
</footer>

<!-- WHATSAPP FLOATING BUTTON -->
<a
  href="<?php echo $it ? 'https://wa.me/393714453904?text=Ciao%20Tailor%20Homes%2C%20vorrei%20mettermi%20in%20contatto.' : 'https://wa.me/393714453904?text=Hi%20Tailor%20Homes%2C%20I%27d%20like%20to%20get%20in%20touch.'; ?>"
  class="th-wa-btn"
  target="_blank"
  rel="noopener"
  aria-label="<?php echo $it ? 'Contattaci su WhatsApp' : 'Contact us on WhatsApp'; ?>"
>
  <span class="th-wa-btn__icon">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="26" height="26" fill="currentColor">
      <path d="M16 .5C7.44.5.5 7.44.5 16c0 2.74.72 5.41 2.08 7.77L.5 31.5l7.94-2.08A15.44 15.44 0 0016 31.5c8.56 0 15.5-6.94 15.5-15.5S24.56.5 16 .5zm0 28.3a12.73 12.73 0 01-6.5-1.78l-.46-.28-4.71 1.23 1.26-4.59-.3-.48A12.8 12.8 0 1116 28.8zm7.02-9.57c-.38-.19-2.27-1.12-2.62-1.25-.35-.13-.6-.19-.85.19-.25.38-.97 1.25-1.19 1.5-.22.25-.44.28-.82.09-.38-.19-1.6-.59-3.04-1.88-1.12-1-1.88-2.24-2.1-2.62-.22-.38-.02-.58.17-.77.17-.17.38-.44.57-.66.19-.22.25-.38.38-.63.13-.25.06-.47-.03-.66-.09-.19-.85-2.06-1.17-2.82-.31-.74-.62-.64-.85-.65h-.72c-.25 0-.66.09-1 .47-.35.38-1.32 1.28-1.32 3.13s1.35 3.63 1.54 3.88c.19.25 2.66 4.06 6.44 5.69.9.39 1.6.62 2.15.79.9.28 1.73.24 2.38.15.73-.1 2.27-.93 2.59-1.82.32-.9.32-1.67.22-1.82-.09-.16-.34-.25-.72-.44z"/>
    </svg>
  </span>
  <span class="th-wa-btn__label"><?php echo $it ? 'Contattaci' : 'Contact Us'; ?></span>
</a>

<style>
  .th-wa-btn {
    position: fixed;
    bottom: 32px;
    left: 32px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #25D366;
    color: #fff;
    text-decoration: none;
    padding: 13px 20px 13px 16px;
    border-radius: 50px;
    box-shadow: 0 4px 24px rgba(0,0,0,.18);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: .02em;
    transition: transform .25s cubic-bezier(.16,1,.3,1), box-shadow .25s ease, padding .25s ease;
    overflow: hidden;
    max-width: 180px;
  }

  .th-wa-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(37,211,102,.35);
  }

  .th-wa-btn__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    width: 26px;
    height: 26px;
  }

  .th-wa-btn__label {
    white-space: nowrap;
  }

  /* Mobile — shrink to icon only */
  @media (max-width: 600px) {
    .th-wa-btn {
      padding: 14px;
      max-width: 54px;
      bottom: 24px;
      left: 20px;
      border-radius: 50%;
    }
    .th-wa-btn__label {
      display: none;
    }
  }

  /* Entrance animation */
  @keyframes th-wa-slide-in {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .th-wa-btn {
    animation: th-wa-slide-in .6s 1.2s cubic-bezier(.16,1,.3,1) both;
  }
</style>

<?php wp_footer(); ?>
</body>
</html>
