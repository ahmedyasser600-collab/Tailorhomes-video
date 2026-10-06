<?php $it = (function_exists('pll_current_language') && pll_current_language() === 'it'); ?>
<style>
  .th-reviews-section { padding: 100px 0; background: var(--th-bg-warm); border-bottom: 1px solid rgba(26,25,22,.08); }
  .th-reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 28px;
    margin-top: 60px;
  }
  .th-review {
    background: var(--th-white);
    padding: 36px 32px;
    border: 1px solid rgba(26,25,22,.06);
    transition: transform .3s, box-shadow .3s;
    display: flex;
    flex-direction: column;
  }
  .th-review:hover { transform: translateY(-4px); box-shadow: 0 16px 48px rgba(26,25,22,.08); }
  .th-review__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    gap: 12px;
  }
  .th-review__stars { color: var(--th-red); font-size: 14px; letter-spacing: 2px; }
  .th-review__platform {
    font-family: 'Jost', sans-serif;
    font-size: 10px;
    font-weight: 500;
    letter-spacing: .15em;
    text-transform: uppercase;
    color: var(--th-ink-soft);
  }
  .th-review__text {
    font-size: 14.5px;
    font-weight: 300;
    color: var(--th-ink-mid);
    line-height: 1.85;
    font-style: italic;
    margin-bottom: 24px;
    flex-grow: 1;
  }
  .th-review__footer {
    border-top: 1px solid rgba(26,25,22,.08);
    padding-top: 18px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 16px;
  }
  .th-review__author {
    font-size: 13px;
    font-weight: 600;
    color: var(--th-ink);
    letter-spacing: -.005em;
  }
  .th-review__meta {
    font-size: 11px;
    font-weight: 400;
    color: var(--th-ink-soft);
    letter-spacing: .03em;
    margin-top: 4px;
    line-height: 1.4;
  }
  .th-review__country-flag {
    font-size: 14px;
    line-height: 1;
    margin-right: 4px;
  }
  .th-review__apt {
    font-size: 11px;
    font-weight: 500;
    color: var(--th-red);
    letter-spacing: .03em;
    text-align: right;
    line-height: 1.4;
    flex-shrink: 0;
  }
  .th-review__apt-label {
    display: block;
    font-size: 9px;
    font-weight: 400;
    color: var(--th-ink-soft);
    letter-spacing: .15em;
    text-transform: uppercase;
    margin-bottom: 2px;
  }
</style>

<section class="th-reviews-section" id="reviews">
  <div class="w">
    <div class="th-section-label r"><?php echo $it ? 'Testimonianze' : 'Testimonials'; ?></div>
    <h2 class="th-section-title r d1"><?php echo $it ? 'Cosa Dicono<br>i Nostri Ospiti.' : 'What Our<br>Guests Say.'; ?></h2>

    <div class="th-reviews-grid">

      <!-- Review 1: Caterina (Italy) — La Casa Bianca — Booking — Feb 2025 -->
      <div class="th-review r d1">
        <div class="th-review__top">
          <div class="th-review__stars">★★★★★</div>
          <div class="th-review__platform">Booking.com</div>
        </div>
        <p class="th-review__text"><?php echo $it ? "&ldquo;Appartamento bellissimo, luminoso e completo di ogni comfort. La pulizia era eccezionale e ogni dettaglio è stato curato con attenzione. La posizione è super, comodissima per esplorare la zona. Ma ciò che ha davvero fatto la differenza è stato lo staff, che è stato incredibilmente accogliente e disponibile. Un'esperienza davvero fantastica, consigliatissimo!&rdquo;" : "&ldquo;A beautiful apartment, bright and equipped with every comfort. The cleanliness was exceptional and every detail was carefully attended to. The location is fantastic — super convenient for exploring the area. But what really made the difference was the staff, who were incredibly welcoming and helpful. A truly fantastic experience, highly recommended!&rdquo;"; ?></p>
        <div class="th-review__footer">
          <div>
            <div class="th-review__author">Caterina</div>
            <div class="th-review__meta"><span class="th-review__country-flag">🇮🇹</span><?php echo $it ? 'Italia' : 'Italy'; ?> · <?php echo $it ? 'Febbraio 2025' : 'February 2025'; ?></div>
          </div>
          <div class="th-review__apt">
            <span class="th-review__apt-label"><?php echo $it ? 'Soggiornata a' : 'Stayed at'; ?></span>
            <?php echo $it ? 'Appartamento Monselice' : 'Monselice Apartment'; ?>
          </div>
        </div>
      </div>

      <!-- Review 2: Maria (Italy) — Airbnb — November 2025 -->
      <div class="th-review r d2">
        <div class="th-review__top">
          <div class="th-review__stars">★★★★★</div>
          <div class="th-review__platform">Airbnb</div>
        </div>
        <p class="th-review__text"><?php echo $it ? "&ldquo;Sono tornata in questo appartamento per la seconda volta perché mi ero trovata benissimo la prima volta, e devo dire che anche stavolta mi sono sentita a casa! Gli host sono molto gentili e cordiali, l'appartamento è perfettamente pulito con biancheria di qualità e — da non sottovalutare a Padova — parcheggio a un minuto a piedi. Tornerò sicuramente!&rdquo;" : "&ldquo;I returned to this accommodation for the second time because I had a great time the first time, and I must say that this time I also felt at home! The hosts are very kind and friendly, the house is perfectly clean with quality linen and — not to be underestimated in Padua — parking just one minute's walk from the accommodation. I will definitely be back!&rdquo;"; ?></p>
        <div class="th-review__footer">
          <div>
            <div class="th-review__author">Maria</div>
            <div class="th-review__meta"><span class="th-review__country-flag">🇮🇹</span><?php echo $it ? 'Italia' : 'Italy'; ?> · <?php echo $it ? 'Novembre 2025' : 'November 2025'; ?></div>
          </div>
          <div class="th-review__apt">
            <span class="th-review__apt-label"><?php echo $it ? 'Soggiornata a' : 'Stayed at'; ?></span>
            <?php echo $it ? 'Appartamenti Locatelli' : 'Locatelli Apartments'; ?>
          </div>
        </div>
      </div>

      <!-- Review 3: Alex (Germany) — Via Marcanova — Airbnb — April 2026 -->
      <div class="th-review r d3">
        <div class="th-review__top">
          <div class="th-review__stars">★★★★★</div>
          <div class="th-review__platform">Airbnb</div>
        </div>
        <p class="th-review__text"><?php echo $it ? "&ldquo;Abbiamo trascorso giorni meravigliosamente rilassanti a Padova! L'appartamento era perfetto per le nostre attività in città e offriva tutto ciò di cui avevamo bisogno per la breve vacanza. Volentieri torneremo in qualsiasi momento!&rdquo;" : "&ldquo;We had wonderfully relaxing days in Padua! The apartment was perfect for our activities in the city and offered everything we needed for our short break. Happy to return anytime!&rdquo;"; ?></p>
        <div class="th-review__footer">
          <div>
            <div class="th-review__author">Alex</div>
            <div class="th-review__meta"><span class="th-review__country-flag">🇩🇪</span><?php echo $it ? 'Germania' : 'Germany'; ?> · <?php echo $it ? 'Aprile 2026' : 'April 2026'; ?></div>
          </div>
          <div class="th-review__apt">
            <span class="th-review__apt-label"><?php echo $it ? 'Soggiornato a' : 'Stayed at'; ?></span>
            Marcanova
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
