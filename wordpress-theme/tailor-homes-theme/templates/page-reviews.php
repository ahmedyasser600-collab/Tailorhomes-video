<?php
/**
 * Template Name: Reviews
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

<style>
  .th-review{background:var(--th-white);padding:40px;border:1px solid rgba(26,25,22,.06);transition:transform .3s,box-shadow .3s;}
  .th-review:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(26,25,22,.08);}
  .th-review__stars{color:var(--th-red);font-size:16px;letter-spacing:2px;margin-bottom:16px;}
  .th-review__text{font-size:15px;font-weight:300;color:var(--th-ink-mid);line-height:1.85;font-style:italic;margin-bottom:20px;}
  .th-review__author{font-size:13px;font-weight:500;color:var(--th-ink);}
  .th-review__source{font-size:11px;font-weight:400;color:var(--th-ink-soft);letter-spacing:.05em;}
</style>

<section class="sv-hero"><div class="w"><div class="sv-hero__inner">
  <h1 class="sv-hero__heading"><?php echo $it ? 'Recensioni.' : 'Reviews.'; ?></h1>
  <p class="sv-hero__sub"><?php echo $it ? 'Cosa dicono i nostri ospiti e proprietari dell\'esperienza con Tailor Homes.' : 'What our guests and property owners have to say about working with Tailor Homes.'; ?></p>
</div></div></section>

<section style="padding:80px 0 20px;background:var(--th-bg-warm);">
  <div class="w" style="text-align:center;">
    <div style="font-size:clamp(48px,5vw,72px);font-weight:800;color:var(--th-red);letter-spacing:-.03em;" class="r">4.9 ★</div>
    <p style="font-size:14px;color:var(--th-ink-soft);margin-top:8px;" class="r d1"><?php echo $it ? 'Valutazione media su tutte le piattaforme' : 'Average rating across all platforms'; ?></p>
  </div>
</section>

<section style="padding:60px 0 100px;background:var(--th-bg-warm);">
  <div class="w"><div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:32px;">
    <div class="th-review r d1"><div class="th-review__stars">★★★★★</div><p class="th-review__text"><?php echo $it ? '"Un soggiorno assolutamente meraviglioso. L\'appartamento era impeccabile, dal design curato, e il check-in è stato facilissimo. Ci siamo sentiti a casa nel cuore di Padova."' : '"An absolutely wonderful stay. The apartment was spotless, beautifully designed, and the check-in process was seamless. We felt right at home in the heart of Padova."'; ?></p><div class="th-review__author">Marco & Elena</div><div class="th-review__source">Airbnb — Colore & Design</div></div>
    <div class="th-review r d2"><div class="th-review__stars">★★★★★</div><p class="th-review__text"><?php echo $it ? '"Perfetto per il nostro soggiorno di 3 mesi come ricercatori in visita. Il team ha gestito tutto con professionalità e l\'appartamento vicino a Prato della Valle era ideale — fermata del tram proprio fuori."' : '"Perfect for our 3-month stay as visiting researchers. The team handled everything professionally and the apartment near Prato della Valle was ideal — tram stop right outside."'; ?></p><div class="th-review__author">Dr. Sarah L.</div><div class="th-review__source">Booking.com — Locatelli Apartments</div></div>
    <div class="th-review r d3"><div class="th-review__stars">★★★★★</div><p class="th-review__text"><?php echo $it ? '"Come proprietario, non potrei essere più soddisfatto. Il mio appartamento rende più di un affitto tradizionale e non devo fare nulla. I report mensili sono chiari e i pagamenti sempre puntuali."' : '"As a property owner, I couldn\'t be happier. My apartment earns more than it did with a traditional lease, and I don\'t have to lift a finger. Monthly reports are clear and payments are always on time."'; ?></p><div class="th-review__author">Giovanni R.</div><div class="th-review__source"><?php echo $it ? 'Proprietario — Padova' : 'Property Owner — Padova'; ?></div></div>
    <div class="th-review r d4"><div class="th-review__stars">★★★★★</div><p class="th-review__text"><?php echo $it ? '"L\'appartamento Fitness & Charme è splendido — il soppalco è un tocco bellissimo. Ottima comunicazione durante tutto il soggiorno."' : '"The Fitness & Charme apartment is gorgeous — the mezzanine loft was a beautiful touch. Great communication throughout our stay."'; ?></p><div class="th-review__author">Anna K.</div><div class="th-review__source">Airbnb — Fitness & Charme</div></div>
    <div class="th-review r d5"><div class="th-review__stars">★★★★★</div><p class="th-review__text"><?php echo $it ? '"Pulito, moderno, ottima posizione. La terrazza sul tetto è stata una bella sorpresa. Comunicazione eccellente — team molto professionale."' : '"Clean, modern, great location. The rooftop terrace was a lovely bonus. Communication was excellent throughout — very professional team."'; ?></p><div class="th-review__author">Thomas & Julia</div><div class="th-review__source">Airbnb — Marcanova</div></div>
    <div class="th-review r d6"><div class="th-review__stars">★★★★★</div><p class="th-review__text"><?php echo $it ? '"Mi sono trasferita per lavoro e avevo bisogno di un appartamento arredato per 6 mesi. Tailor Homes ha reso l\'intero processo semplice — dalla visita al trasloco. Consigliatissimo."' : '"I relocated for work and needed a furnished apartment for 6 months. Tailor Homes made the entire process easy — from viewing to move-in. Highly recommended."'; ?></p><div class="th-review__author">Luisa M.</div><div class="th-review__source"><?php echo $it ? 'Affitto Medio Termine — Appartamento Monselice' : 'Medium-Term Stay — Monselice Apartment'; ?></div></div>
  </div></div>
</section>

<section class="th-dark-cta">
  <div class="w th-dark-cta__inner r">
    <h2 class="th-dark-cta__heading"><?php echo $it ? 'Pronto a<br><em>provare?</em>' : 'Ready to<br><em>experience it?</em>'; ?></h2>
    <p class="th-dark-cta__text"><?php echo $it ? 'Scopri i nostri appartamenti o contattaci — saremo felici di ospitarti.' : 'Browse our apartments or get in touch — we\'d love to host you.'; ?></p>
    <a href="<?php echo esc_url(th_url('book-now')); ?>" class="th-btn th-btn--cta"><?php echo $it ? 'Vedi gli Appartamenti →' : 'View Apartments →'; ?></a>
  </div>
</section>

<?php get_footer(); ?>
