<?php
/**
 * Template Name: Work With Us
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

<style>
  .ww-grid{display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:start;}
  @media(max-width:768px){.ww-grid{grid-template-columns:1fr;gap:48px;}}
  .ww-paths{list-style:none;padding:0;margin:0;}
  .ww-path{display:flex;gap:18px;padding:26px 0;border-top:1px solid rgba(26,25,22,.1);}
  .ww-path:last-child{border-bottom:1px solid rgba(26,25,22,.1);}
  .ww-path__icon{flex-shrink:0;width:44px;height:44px;border-radius:50%;background:var(--th-cream);display:flex;align-items:center;justify-content:center;color:var(--th-red);}
  .ww-path__icon svg{width:22px;height:22px;}
  .ww-path__title{font-size:17px;font-weight:700;color:var(--th-ink);margin:4px 0 6px;letter-spacing:-.01em;}
  .ww-path__text{font-size:14px;font-weight:300;color:var(--th-ink-mid);line-height:1.7;}
  /* Form styling — applies to Contact Form 7 / WPForms output too */
  .ww-form input,.ww-form textarea,.ww-form select,
  .ww-form .wpcf7-form-control{width:100%;padding:14px 18px;border:1px solid rgba(26,25,22,.15);background:var(--th-white);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:300;color:var(--th-ink);margin-bottom:20px;transition:border-color .3s;outline:none;border-radius:0;}
  .ww-form input:focus,.ww-form textarea:focus,.ww-form select:focus{border-color:var(--th-terracotta);}
  .ww-form textarea{min-height:150px;resize:vertical;}
  .ww-form label{font-size:11px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--th-ink-soft);display:block;margin-bottom:8px;}
  .ww-form input[type="submit"],.ww-form .wpcf7-submit,.ww-form button{width:100%;background:var(--th-ink);color:var(--th-white);border:none;padding:16px;font-family:'DM Sans',sans-serif;font-size:12px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;cursor:pointer;transition:background .3s;}
  .ww-form input[type="submit"]:hover,.ww-form .wpcf7-submit:hover,.ww-form button:hover{background:var(--th-red);}
  .ww-form input[type="file"]{padding:10px;background:var(--th-cream);font-size:13px;}
</style>

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo $it ? 'Lavora<br>con Noi.' : 'Work<br>With Us.'; ?></h1>
  <p class="sv-hero__sub"><?php echo $it ? 'Che tu voglia entrare nel nostro team, affidarci il tuo immobile o avviare una collaborazione, parliamone.' : "Whether you want to join our team, entrust us with your property, or start a collaboration — let's talk."; ?></p>
</div></div></section>

<section style="padding:100px 0;"><div class="w"><div class="ww-grid">
  <div class="r">
    <div class="th-section-label"><?php echo $it ? 'Tre modi per iniziare' : 'Three ways to begin'; ?></div>
    <h2 style="font-size:clamp(28px,3.5vw,42px);font-weight:800;letter-spacing:-.025em;line-height:1.1;margin-bottom:32px;"><?php echo $it ? 'Costruiamo qualcosa<br>insieme.' : "Let's build<br>something together."; ?></h2>
    <ul class="ww-paths">
      <li class="ww-path">
        <span class="ww-path__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <div>
          <div class="ww-path__title"><?php echo $it ? 'Unisciti al team' : 'Join the team'; ?></div>
          <div class="ww-path__text"><?php echo $it ? 'Cerchi lavoro nel settore dell\'ospitalità e della gestione immobiliare? Inviaci la tua candidatura.' : 'Looking for a role in hospitality and property management? Send us your application.'; ?></div>
        </div>
      </li>
      <li class="ww-path">
        <span class="ww-path__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V21a1 1 0 0 1-1 1h-5v-7h-6v7H4a1 1 0 0 1-1-1z"/></svg></span>
        <div>
          <div class="ww-path__title"><?php echo $it ? 'Proprietario partner' : 'Property-owner partner'; ?></div>
          <div class="ww-path__text"><?php echo $it ? 'Hai un immobile e vuoi affidarne la gestione? Diventa nostro partner.' : 'Have a property you\'d like managed? Become our partner.'; ?></div>
        </div>
      </li>
      <li class="ww-path">
        <span class="ww-path__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
        <div>
          <div class="ww-path__title"><?php echo $it ? 'Azienda o altro' : 'Business or other'; ?></div>
          <div class="ww-path__text"><?php echo $it ? 'Rappresenti un\'azienda o hai un\'idea di collaborazione? Scrivici.' : 'Represent a business or have a collaboration idea? Get in touch.'; ?></div>
        </div>
      </li>
    </ul>
  </div>
  <div class="rr d1 ww-form">
    <?php
    $th_work_shortcode = get_theme_mod('th_work_form_shortcode', '');
    if (!empty(trim($th_work_shortcode))) {
        echo do_shortcode($th_work_shortcode);
    } else {
    ?>
    <label><?php echo $it ? 'Nome e Cognome' : 'Your Name'; ?></label><input type="text" placeholder="<?php echo $it ? 'Nome completo' : 'Full name'; ?>">
    <label>Email</label><input type="email" placeholder="your@email.com">
    <label><?php echo $it ? 'Numero di Telefono' : 'Phone Number'; ?></label><input type="tel" placeholder="+39 000 000 0000">
    <label><?php echo $it ? 'Come vuoi collaborare?' : 'How would you like to work with us?'; ?></label>
    <select>
      <option><?php echo $it ? 'Come dipendente (candidatura)' : 'As an employee (job application)'; ?></option>
      <option><?php echo $it ? 'Come proprietario partner' : 'As a property-owner partner'; ?></option>
      <option><?php echo $it ? 'Come azienda / altra collaborazione' : 'As a business / other collaboration'; ?></option>
    </select>
    <label><?php echo $it ? 'Messaggio' : 'Message'; ?></label>
    <textarea placeholder="<?php echo $it ? 'Raccontaci di te...' : 'Tell us about yourself...'; ?>"></textarea>
    <button class="th-btn th-btn--solid" type="button" style="width:100%;"><?php echo $it ? 'Invia →' : 'Submit →'; ?></button>
    <p style="font-size:12px;color:var(--th-ink-soft);margin-top:12px;text-align:center;"><?php echo $it ? 'Questo è un modulo di anteprima. Collega il tuo plugin dei moduli dal Customizer.' : 'This is a placeholder form. Connect your form plugin from the Customizer.'; ?></p>
    <?php } ?>
  </div>
</div></div></section>

<?php get_footer(); ?>
