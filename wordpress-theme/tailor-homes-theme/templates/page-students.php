<?php
/**
 * Template Name: Students
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
include(get_template_directory() . '/templates/page-guests-langs.php');
$wa_number = '393714453904'; // WhatsApp business number (no +, no spaces)
?>
<script>document.body.classList.add('th-solid-header');</script>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/services.css">

<style>
  .st-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:start;}
  @media(max-width:768px){.st-grid{grid-template-columns:1fr;gap:44px;}}
  .st-badge{display:inline-flex;align-items:center;gap:10px;background:var(--th-white);border:1px solid rgba(184,49,47,.25);color:var(--th-red);padding:12px 22px;border-radius:100px;font-family:'Jost',sans-serif;font-weight:500;letter-spacing:.05em;font-size:14px;margin-bottom:24px;}
  .st-badge strong{font-size:20px;font-weight:700;}
  .st-perks{list-style:none;padding:0;margin:28px 0 0;}
  .st-perk{display:flex;gap:14px;align-items:flex-start;padding:12px 0;font-size:14px;font-weight:300;color:var(--th-ink-mid);line-height:1.7;}
  .st-perk svg{flex-shrink:0;width:18px;height:18px;color:var(--th-red);margin-top:2px;}
  .st-form input,.st-form select{width:100%;padding:14px 18px;border:1px solid rgba(26,25,22,.15);background:var(--th-cream);font-family:'DM Sans',sans-serif;font-size:14px;font-weight:300;color:var(--th-ink);margin-bottom:20px;transition:border-color .3s;outline:none;}
  .st-form input:focus,.st-form select:focus{border-color:var(--th-terracotta);}
  .st-form label{font-size:11px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--th-ink-soft);display:block;margin-bottom:8px;}
  .st-wa-btn{display:flex;align-items:center;justify-content:center;gap:12px;width:100%;background:#25D366;color:#fff;border:none;padding:17px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:filter .3s;text-decoration:none;}
  .st-wa-btn:hover{filter:brightness(.94);}
  .st-wa-btn svg{width:20px;height:20px;}
  .st-note{font-size:12px;color:var(--th-ink-soft);margin-top:14px;text-align:center;line-height:1.6;}
  .st-form-card{background:var(--th-white);padding:40px 36px;border:1px solid rgba(26,25,22,.06);box-shadow:0 16px 48px rgba(26,25,22,.08);}
  @media(max-width:768px){.st-form-card{padding:32px 24px;}}
</style>

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo th_gt($it,'st_hero_title'); ?></h1>
  <p class="sv-hero__sub"><?php echo th_gt($it,'st_hero_sub'); ?></p>
</div></div></section>

<section class="sv-intro"><div class="w">
  <p class="sv-intro__text r"><?php echo th_gt($it,'st_intro'); ?></p>
</div></section>

<!-- THE PACKAGE -->
<section class="sv-services"><div class="w">
  <div class="sv-services__label r"><?php echo th_gt($it,'st_pkg_label'); ?></div>
  <h2 class="sv-services__title r d1"><?php echo th_gt($it,'st_pkg_title'); ?></h2>
  <div class="sv-grid sv-grid--two">
    <?php foreach (array(1,2,3,4) as $i): ?>
    <div class="sv-card r d<?php echo $i; ?>"><span class="sv-card__num">0<?php echo $i; ?></span>
      <h3 class="sv-card__heading"><?php echo th_gt($it,'st_b'.$i.'_title'); ?></h3>
      <p style="font-size:14px;font-weight:300;color:var(--th-ink-mid);line-height:1.7;"><?php echo th_gt($it,'st_b'.$i.'_desc'); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</div></section>

<!-- CHOOSE YOUR PATH -->
<section class="sv-extended"><div class="w">
  <div class="sv-extended__label r"><?php echo th_gt($it,'st_choose_label'); ?></div>
  <h2 class="sv-extended__title r d1"><?php echo th_gt($it,'st_choose_title'); ?></h2>
  <p class="sv-extended__intro r d2"><?php echo th_gt($it,'st_choose_desc'); ?></p>
  <div class="gst-cards">
    <a href="<?php echo esc_url(th_url('students-unipd')); ?>" class="gst-card r d1">
      <h3 class="gst-card__title"><?php echo th_gt($it,'st_card_up_title'); ?></h3>
      <p class="gst-card__desc"><?php echo th_gt($it,'st_card_up_desc'); ?></p>
      <span class="gst-card__cta"><?php echo th_gt($it,'st_card_btn'); ?></span>
    </a>
    <a href="<?php echo esc_url(th_url('students-erasmus')); ?>" class="gst-card r d2">
      <h3 class="gst-card__title"><?php echo th_gt($it,'st_card_er_title'); ?></h3>
      <p class="gst-card__desc"><?php echo th_gt($it,'st_card_er_desc'); ?></p>
      <span class="gst-card__cta"><?php echo th_gt($it,'st_card_btn'); ?></span>
    </a>
  </div>
</div></section>

<!-- 15% DISCOUNT + WHATSAPP FORM -->
<section class="sv-services" id="student-discount"><div class="w">
  <div class="sv-services__label r"><?php echo $it ? 'Il Vantaggio Studenti' : 'The Student Advantage'; ?></div>
  <div class="st-grid" style="margin-top:8px;">
  <div class="r">
    <div class="st-badge"><strong>−15%</strong> <?php echo $it ? 'su ogni prenotazione' : 'on every booking'; ?></div>
    <h2 class="sv-services__title" style="font-size:clamp(28px,3.5vw,44px);margin-bottom:20px;"><?php echo $it ? 'Uno sconto pensato<br>per la vita universitaria.' : 'A discount made<br>for student life.'; ?></h2>
    <p style="font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.8;"><?php echo $it ? 'Mandaci una foto della tua tessera universitaria o del documento Erasmus su WhatsApp. Una volta verificata, ti inviamo il tuo codice sconto del 15%, valido per i tuoi soggiorni e quelli di amici e familiari.' : 'Send us a photo of your university card or Erasmus document on WhatsApp. Once verified, we send you your 15% discount code — valid for your stays and those of your friends and family.'; ?></p>
    <ul class="st-perks">
      <li class="st-perk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?php echo $it ? 'Valido per appartamenti e soggiorni a medio termine' : 'Valid on apartments and medium-term stays'; ?></li>
      <li class="st-perk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?php echo $it ? 'Condivisibile con amici e familiari' : 'Shareable with friends and family'; ?></li>
      <li class="st-perk"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?php echo $it ? 'Verifica rapida via WhatsApp' : 'Quick verification via WhatsApp'; ?></li>
    </ul>
  </div>
  <div class="rr d1">
    <div class="st-form-card st-form">
      <label><?php echo $it ? 'Il tuo nome' : 'Your name'; ?></label>
      <input type="text" id="st-name" placeholder="<?php echo $it ? 'Nome completo' : 'Full name'; ?>">
      <label><?php echo $it ? 'Sei uno studente...' : 'You are a...'; ?></label>
      <select id="st-type">
        <option value="<?php echo $it ? 'Studente Erasmus' : 'Erasmus student'; ?>"><?php echo $it ? 'Studente Erasmus' : 'Erasmus student'; ?></option>
        <option value="<?php echo $it ? 'Studente UniPD' : 'UniPD student'; ?>"><?php echo $it ? 'Studente UniPD' : 'UniPD student'; ?></option>
      </select>
      <label><?php echo $it ? 'Università' : 'University'; ?></label>
      <input type="text" id="st-uni" placeholder="<?php echo $it ? 'Es. Università di Padova' : 'e.g. University of Padova'; ?>">
      <a href="#" id="st-wa" class="st-wa-btn" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.15-1.77-.87-2.04-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51-.17-.01-.37-.01-.57-.01-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.46 1.07 2.88 1.22 3.08.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.62.71.23 1.36.2 1.87.12.57-.08 1.77-.72 2.02-1.42.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35z M12 2a10 10 0 0 0-8.6 15.06L2 22l5.06-1.33A10 10 0 1 0 12 2z"/></svg>
        <?php echo $it ? 'Invia su WhatsApp' : 'Send via WhatsApp'; ?>
      </a>
      <p class="st-note"><?php echo $it ? 'Si aprirà WhatsApp con un messaggio già pronto. Allega una foto della tua tessera e inviaci il messaggio.' : 'WhatsApp will open with a ready message. Attach a photo of your card and send it to us.'; ?></p>
    </div>
  </div>
  </div>
</div></section>

<script>
(function(){
  var btn = document.getElementById('st-wa');
  if(!btn) return;
  btn.addEventListener('click', function(e){
    e.preventDefault();
    var name = (document.getElementById('st-name').value || '').trim();
    var type = document.getElementById('st-type').value || '';
    var uni  = (document.getElementById('st-uni').value || '').trim();
    var intro = <?php echo $it ? "'Ciao Tailor Homes! Vorrei richiedere lo sconto studenti del 15%.'" : "'Hi Tailor Homes! I would like to request the 15% student discount.'"; ?>;
    var lName = <?php echo $it ? "'Nome'" : "'Name'"; ?>;
    var lType = <?php echo $it ? "'Tipo'" : "'Type'"; ?>;
    var lUni  = <?php echo $it ? "'Università'" : "'University'"; ?>;
    var lCard = <?php echo $it ? "'(Allego qui la foto della mia tessera)'" : "'(Attaching a photo of my student card here)'"; ?>;
    var msg = intro + '\n\n' + lName + ': ' + name + '\n' + lType + ': ' + type + '\n' + lUni + ': ' + uni + '\n\n' + lCard;
    var url = 'https://wa.me/<?php echo $wa_number; ?>?text=' + encodeURIComponent(msg);
    window.open(url, '_blank');
  });
})();
</script>

<?php get_footer(); ?>
