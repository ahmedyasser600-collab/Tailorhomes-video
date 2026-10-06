<?php
/**
 * Template Name: Contact
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

<style>
  .th-contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:72px;}
  @media(max-width:768px){.th-contact-grid{grid-template-columns:1fr;gap:48px;}}
  .th-contact-form input,.th-contact-form textarea,.th-contact-form select{width:100%;padding:14px 18px;border:1px solid rgba(26,25,22,.15);background:var(--th-white);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:300;color:var(--th-ink);margin-bottom:20px;transition:border-color .3s;outline:none;}
  .th-contact-form input:focus,.th-contact-form textarea:focus{border-color:var(--th-terracotta);}
  .th-contact-form textarea{min-height:160px;resize:vertical;}
  .th-contact-form label{font-size:11px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--th-ink-soft);display:block;margin-bottom:8px;}
</style>

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo $it ? 'Contattaci.' : 'Contact<br>Us.'; ?></h1>
  <p class="sv-hero__sub"><?php echo $it ? 'Hai una domanda, un immobile o vuoi semplicemente salutarci? Siamo felici di sentirti.' : "Have a question, a property, or just want to say hello? We'd love to hear from you."; ?></p>
</div></div></section>

<section style="padding:100px 0;"><div class="w"><div class="th-contact-grid">
  <div class="r">
    <div class="th-section-label"><?php echo $it ? 'Scrivici' : 'Get in Touch'; ?></div>
    <h2 style="font-size:clamp(28px,3.5vw,42px);font-weight:800;letter-spacing:-.025em;line-height:1.1;margin-bottom:32px;"><?php echo $it ? 'Ti rispondiamo<br>in tempi rapidi.' : "We'll get back<br>to you quickly."; ?></h2>
    <div style="margin-bottom:36px;"><p style="font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;"><strong style="font-weight:500;color:var(--th-ink);">Email</strong><br><a href="mailto:info@tailorhomes.it" style="color:var(--th-terracotta);">info@tailorhomes.it</a></p></div>
    <div style="margin-bottom:36px;"><p style="font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;"><strong style="font-weight:500;color:var(--th-ink);"><?php echo $it ? 'Telefono' : 'Phone'; ?></strong><br><a href="tel:+393714453904" style="color:var(--th-terracotta);">+39 371 445 3904</a><br><a href="tel:+390494906189" style="color:var(--th-terracotta);">+39 049 490 6189</a></p></div>
    <div><p style="font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;"><strong style="font-weight:500;color:var(--th-ink);"><?php echo $it ? 'Indirizzo' : 'Address'; ?></strong><br>Via degli Obizzi, 1<br>35122 Padova, Italia</p></div>
  </div>
  <div class="rr d1 th-contact-form">
    <?php
    $th_contact_shortcode = get_theme_mod('th_contact_form_shortcode', '');
    if (!empty(trim($th_contact_shortcode))) {
        echo do_shortcode($th_contact_shortcode);
    } else {
    ?>
    <label><?php echo $it ? 'Nome e Cognome' : 'Your Name'; ?></label><input type="text" placeholder="<?php echo $it ? 'Nome completo' : 'Full name'; ?>">
    <label>Email</label><input type="email" placeholder="your@email.com">
    <label><?php echo $it ? 'Numero di Telefono' : 'Phone Number'; ?></label><input type="tel" placeholder="+39 000 000 0000">
    <label><?php echo $it ? 'Oggetto' : 'Subject'; ?></label>
    <select>
      <option><?php echo $it ? 'Sono un ospite e voglio prenotare' : "I'm a guest looking to book"; ?></option>
      <option><?php echo $it ? 'Sono un proprietario e cerco gestione' : "I'm an owner looking for management"; ?></option>
      <option><?php echo $it ? 'Informazioni generali' : 'General enquiry'; ?></option>
      <option><?php echo $it ? 'Partnership o collaborazione' : 'Partnership or collaboration'; ?></option>
    </select>
    <label><?php echo $it ? 'Messaggio' : 'Message'; ?></label>
    <textarea placeholder="<?php echo $it ? 'Dicci come possiamo aiutarti...' : 'Tell us how we can help...'; ?>"></textarea>
    <button class="th-btn th-btn--solid" type="button" style="width:100%;"><?php echo $it ? 'Invia Messaggio →' : 'Send Message →'; ?></button>
    <p style="font-size:12px;color:var(--th-ink-soft);margin-top:12px;text-align:center;"><?php echo $it ? 'Rispondiamo solitamente entro 24 ore.' : 'We typically respond within 24 hours.'; ?></p>
    <?php } ?>
  </div>
</div></div></section>

<?php get_footer(); ?>
