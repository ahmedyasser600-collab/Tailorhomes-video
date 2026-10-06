<?php
/**
 * Template Name: Terms & Conditions
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/legal.css">

<style>
  .th-legal-hero {
    padding: 200px 0 72px;
    border-bottom: 1px solid rgba(26,25,22,.1);
  }
  .th-legal-hero__inner {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 48px;
  }
  .th-legal-hero__heading {
    font-size: clamp(48px, 6vw, 88px);
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: 1;
    color: var(--th-ink);
  }
  .th-legal-hero__meta {
    font-size: 13px;
    font-weight: 300;
    color: var(--th-ink-soft);
    text-align: right;
    line-height: 1.8;
  }
  @media(max-width:768px) {
    .th-legal-hero { padding: 180px 0 56px; }
    .th-legal-hero__inner { flex-direction: column; align-items: flex-start; gap: 20px; }
    .th-legal-hero__meta { text-align: left; }
  }

  .th-legal-body {
    padding: 80px 0 120px;
  }
  .th-legal-layout {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 80px;
    align-items: start;
  }
  @media(max-width:900px) {
    .th-legal-layout { grid-template-columns: 1fr; gap: 40px; }
    .th-legal-nav { display: none; }
  }

  .th-legal-nav {
    position: sticky;
    top: 100px;
  }
  .th-legal-nav__title {
    font-size: 10px;
    font-weight: 500;
    letter-spacing: .22em;
    text-transform: uppercase;
    color: var(--th-ink-soft);
    margin-bottom: 20px;
  }
  .th-legal-nav a {
    display: block;
    font-size: 13px;
    font-weight: 300;
    color: var(--th-ink-mid);
    text-decoration: none;
    padding: 7px 0;
    border-left: 2px solid transparent;
    padding-left: 14px;
    transition: color .2s, border-color .2s;
    line-height: 1.4;
  }
  .th-legal-nav a:hover {
    color: var(--th-ink);
    border-left-color: var(--th-terracotta);
  }

  .th-legal-content h2 {
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -.015em;
    color: var(--th-ink);
    margin: 56px 0 16px;
    padding-top: 8px;
    border-top: 1px solid rgba(26,25,22,.08);
    scroll-margin-top: 100px;
  }
  .th-legal-content h2:first-child {
    margin-top: 0;
    border-top: none;
  }
  .th-legal-content p {
    font-size: 15px;
    font-weight: 300;
    color: var(--th-ink-mid);
    line-height: 1.85;
    margin-bottom: 16px;
  }
  .th-legal-content ul {
    margin: 0 0 20px 0;
    padding: 0;
    list-style: none;
  }
  .th-legal-content ul li {
    font-size: 15px;
    font-weight: 300;
    color: var(--th-ink-mid);
    line-height: 1.85;
    padding: 4px 0 4px 20px;
    position: relative;
  }
  .th-legal-content ul li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 14px;
    width: 7px;
    height: 1px;
    background: var(--th-terracotta);
  }
  .th-legal-content strong {
    font-weight: 600;
    color: var(--th-ink);
  }
  .th-legal-content a {
    color: var(--th-terracotta);
    text-decoration: none;
  }
  .th-legal-content a:hover {
    text-decoration: underline;
  }
  .th-legal-highlight {
    background: rgba(184,49,47,.04);
    border-left: 3px solid var(--th-terracotta);
    padding: 20px 24px;
    margin: 24px 0;
    font-size: 14px;
    font-weight: 300;
    color: var(--th-ink-mid);
    line-height: 1.8;
  }
</style>

<!-- HERO -->
<section class="th-legal-hero">
  <div class="w">
    <div class="th-legal-hero__inner">
      <h1 class="th-legal-hero__heading"><?php echo $it ? 'Termini e<br>Condizioni.' : 'Terms &amp;<br>Conditions.'; ?></h1>
      <div class="th-legal-hero__meta">
        <?php echo $it ? 'Ultimo aggiornamento: Giugno 2025' : 'Last updated: June 2025'; ?><br>
        Tailor Homes · Padova, Italy
      </div>
    </div>
  </div>
</section>

<!-- BODY -->
<section class="th-legal-body">
  <div class="w">
    <div class="th-legal-layout">

      <!-- Sidebar Nav -->
      <nav class="th-legal-nav">
        <div class="th-legal-nav__title"><?php echo $it ? 'Indice' : 'Contents'; ?></div>
        <a href="#acceptance"><?php echo $it ? 'Accettazione dei Termini' : 'Acceptance of Terms'; ?></a>
        <a href="#services"><?php echo $it ? 'I Nostri Servizi' : 'Our Services'; ?></a>
        <a href="#website-use"><?php echo $it ? 'Utilizzo del Sito' : 'Website Use'; ?></a>
        <a href="#enquiries"><?php echo $it ? 'Richieste e Contatti' : 'Enquiries & Contact'; ?></a>
        <a href="#newsletter">Newsletter</a>
        <a href="#intellectual-property"><?php echo $it ? 'Proprietà Intellettuale' : 'Intellectual Property'; ?></a>
        <a href="#disclaimers"><?php echo $it ? 'Esclusioni di Responsabilità' : 'Disclaimers'; ?></a>
        <a href="#liability"><?php echo $it ? 'Limitazione di Responsabilità' : 'Limitation of Liability'; ?></a>
        <a href="#third-party"><?php echo $it ? 'Link di Terze Parti' : 'Third-Party Links'; ?></a>
        <a href="#governing-law"><?php echo $it ? 'Legge Applicabile' : 'Governing Law'; ?></a>
        <a href="#changes"><?php echo $it ? 'Modifiche ai Termini' : 'Changes to Terms'; ?></a>
        <a href="#contact"><?php echo $it ? 'Contatti' : 'Contact'; ?></a>
      </nav>

      <!-- Main Content -->
      <div class="th-legal-content">

        <div class="th-legal-highlight">
          <?php echo $it
            ? 'Si prega di leggere attentamente i presenti Termini e Condizioni prima di utilizzare il sito web di Tailor Homes. Accedendo o utilizzando questo sito, accetti di essere vincolato da questi termini. Se non sei d\'accordo, ti preghiamo di non utilizzare questo sito.'
            : 'Please read these Terms and Conditions carefully before using the Tailor Homes website. By accessing or using this website, you agree to be bound by these terms. If you do not agree, please do not use this website.'; ?>
        </div>

        <h2 id="acceptance"><?php echo $it ? '1. Accettazione dei Termini' : '1. Acceptance of Terms'; ?></h2>
        <p><?php echo $it
          ? 'I presenti Termini e Condizioni regolano l\'utilizzo del sito web gestito da <strong>Tailor Homes</strong>, con sede in Via degli Obizzi, 1 — 35122 Padova, Italia. Navigando questo sito, inviando un modulo di contatto o iscrivendoti alla nostra newsletter, confermi di aver letto, compreso e accettato questi termini.'
          : 'These Terms and Conditions govern your use of the website operated by <strong>Tailor Homes</strong>, located at Via degli Obizzi, 1 — 35122 Padova, Italy. By browsing this website, submitting a contact form, or signing up for our newsletter, you confirm that you have read, understood, and agree to these terms.'; ?></p>

        <h2 id="services"><?php echo $it ? '2. I Nostri Servizi' : '2. Our Services'; ?></h2>
        <p><?php echo $it ? 'Tailor Homes fornisce servizi di gestione immobiliare per affitti brevi e a medio termine, inclusi ma non limitati a:' : 'Tailor Homes provides property management services for short-term and medium-term rentals, including but not limited to:'; ?></p>
        <ul>
          <li><?php echo $it ? 'Inserzione e promozione di immobili sulle piattaforme di affitto' : 'Listing and promoting properties on rental platforms'; ?></li>
          <li><?php echo $it ? 'Comunicazione con gli ospiti, gestione check-in e check-out' : 'Guest communication, check-in and check-out management'; ?></li>
          <li><?php echo $it ? 'Coordinamento pulizie, biancheria e manutenzione' : 'Cleaning, linen, and property maintenance coordination'; ?></li>
          <li><?php echo $it ? 'Conformità legale e normativa per affitti brevi' : 'Legal and regulatory compliance for short-term rentals'; ?></li>
          <li><?php echo $it ? 'Soluzioni abitative aziendali e a medio termine' : 'Corporate and medium-term accommodation solutions'; ?></li>
          <li><?php echo $it ? 'Home staging, consulenza ristrutturazione e fotografia professionale' : 'Home staging, renovation guidance, and professional photography'; ?></li>
        </ul>
        <p><?php echo $it
          ? 'Le informazioni presentate su questo sito sono a scopo puramente informativo. Non costituiscono un\'offerta contrattuale. Qualsiasi accordo di servizio tra Tailor Homes e un proprietario o cliente aziendale sarà regolato da un contratto scritto separato.'
          : 'Information presented on this website is for general informational purposes only. It does not constitute a contractual offer. Any service agreement between Tailor Homes and a property owner or corporate client will be governed by a separate written contract.'; ?></p>

        <h2 id="website-use"><?php echo $it ? '3. Utilizzo del Sito Web' : '3. Website Use'; ?></h2>
        <p><?php echo $it ? 'Accetti di utilizzare questo sito solo per scopi leciti e in modo da non violare i diritti altrui. Non devi:' : 'You agree to use this website only for lawful purposes and in a manner that does not infringe the rights of others. You must not:'; ?></p>
        <ul>
          <li><?php echo $it ? 'Tentare di ottenere accesso non autorizzato a qualsiasi parte del sito o della sua infrastruttura' : 'Attempt to gain unauthorised access to any part of the website or its infrastructure'; ?></li>
          <li><?php echo $it ? 'Utilizzare il sito per trasmettere contenuti dannosi, offensivi o illeciti' : 'Use the website to transmit any harmful, offensive, or unlawful content'; ?></li>
          <li><?php echo $it ? 'Riprodurre, distribuire o sfruttare qualsiasi contenuto di questo sito senza previa autorizzazione scritta' : 'Reproduce, distribute, or exploit any content from this website without prior written permission'; ?></li>
          <li><?php echo $it ? 'Utilizzare strumenti automatizzati (bot, scraper) per accedere o raccogliere dati da questo sito' : 'Use automated tools (bots, scrapers) to access or collect data from this website'; ?></li>
        </ul>
        <p><?php echo $it
          ? 'Ci riserviamo il diritto di limitare o terminare l\'accesso a questo sito in qualsiasi momento, senza preavviso, per comportamenti che riteniamo violino questi termini o siano dannosi per altri utenti, per noi o per terzi.'
          : 'We reserve the right to restrict or terminate access to this website at any time, without notice, for conduct that we believe violates these terms or is harmful to other users, us, or third parties.'; ?></p>

        <h2 id="enquiries"><?php echo $it ? '4. Richieste e Modulo di Contatto' : '4. Enquiries & Contact Form'; ?></h2>
        <p><?php echo $it ? 'Quando invii una richiesta tramite il nostro modulo di contatto, accetti che:' : 'When you submit an enquiry through our contact form, you agree that:'; ?></p>
        <ul>
          <li><?php echo $it ? 'Le informazioni fornite siano accurate e veritiere' : 'The information you provide is accurate and truthful'; ?></li>
          <li><?php echo $it ? 'Sei autorizzato a richiedere informazioni per conto di qualsiasi parte che rappresenti' : 'You are authorised to enquire on behalf of any party you represent'; ?></li>
          <li><?php echo $it ? 'Tailor Homes può contattarti in risposta alla tua richiesta tramite l\'email o il numero di telefono forniti' : 'Tailor Homes may contact you in response to your enquiry via the email or phone number provided'; ?></li>
        </ul>
        <p><?php echo $it
          ? 'L\'invio di una richiesta non crea alcun rapporto contrattuale né obbligo da parte di Tailor Homes. Ci proponiamo di rispondere a tutte le richieste entro 24 ore, ma non possiamo garantire i tempi di risposta.'
          : 'Submitting an enquiry does not create any contractual relationship or obligation on the part of Tailor Homes. We aim to respond to all enquiries within 24 hours but cannot guarantee response times.'; ?></p>

        <h2 id="newsletter">5. Newsletter</h2>
        <p><?php echo $it
          ? 'Iscrivendoti alla nostra newsletter, acconsenti a ricevere comunicazioni periodiche via email da Tailor Homes, incluse notizie sugli immobili, consigli e aggiornamenti. Puoi annullare l\'iscrizione in qualsiasi momento cliccando il link di cancellazione presente in ogni email, o contattandoci direttamente all\'indirizzo <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a>.'
          : 'By subscribing to our newsletter, you consent to receiving periodic email communications from Tailor Homes, including property news, tips, and updates. You may unsubscribe at any time by clicking the unsubscribe link in any email, or by contacting us directly at <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a>.'; ?></p>
        <p><?php echo $it
          ? 'Non utilizzeremo il tuo indirizzo email per scopi diversi dall\'invio dei contenuti della newsletter a cui ti sei iscritto.'
          : 'We will not use your email address for any purpose other than sending the newsletter content you subscribed to.'; ?></p>

        <h2 id="intellectual-property"><?php echo $it ? '6. Proprietà Intellettuale' : '6. Intellectual Property'; ?></h2>
        <p><?php echo $it
          ? 'Tutti i contenuti di questo sito — inclusi testi, immagini, grafiche, loghi, icone e codice — sono di proprietà di Tailor Homes o dei suoi fornitori di contenuti e sono protetti dalle leggi applicabili sul diritto d\'autore e sulla proprietà intellettuale.'
          : 'All content on this website — including text, images, graphics, logos, icons, and code — is the property of Tailor Homes or its content suppliers and is protected by applicable copyright and intellectual property laws.'; ?></p>
        <p><?php echo $it
          ? 'È consentito visualizzare e stampare pagine di questo sito solo per uso personale e non commerciale. Qualsiasi altro utilizzo, inclusa la riproduzione, modifica, distribuzione o ripubblicazione, richiede il nostro consenso scritto preventivo.'
          : 'You may view and print pages from this website for personal, non-commercial use only. Any other use, including reproduction, modification, distribution, or republication, requires our prior written consent.'; ?></p>

        <h2 id="disclaimers"><?php echo $it ? '7. Esclusioni di Responsabilità' : '7. Disclaimers'; ?></h2>
        <p><?php echo $it
          ? 'Questo sito è fornito "così com\'è" e "come disponibile". Pur facendo ogni sforzo per garantire l\'accuratezza e la completezza delle informazioni presentate, Tailor Homes non fornisce garanzie o dichiarazioni di alcun tipo, esplicite o implicite, riguardo:'
          : 'This website is provided on an "as is" and "as available" basis. While we make every effort to ensure the accuracy and completeness of the information presented, Tailor Homes makes no warranties or representations of any kind, express or implied, regarding:'; ?></p>
        <ul>
          <li><?php echo $it ? 'L\'accuratezza, l\'affidabilità o la completezza dei contenuti' : 'The accuracy, reliability, or completeness of any content'; ?></li>
          <li><?php echo $it ? 'La disponibilità o il funzionamento ininterrotto del sito' : 'The availability or uninterrupted operation of the website'; ?></li>
          <li><?php echo $it ? 'Le stime di reddito da locazione, che sono solo indicative e non garantite' : 'Estimated rental income figures, which are illustrative only and not guaranteed'; ?></li>
        </ul>
        <p><?php echo $it
          ? 'Le stime di reddito da locazione presentate su questo sito sono basate su analisi di mercato e dati storici. I risultati effettivi possono variare significativamente in base alle condizioni dell\'immobile, alla posizione, alla stagionalità e ai cambiamenti del mercato.'
          : 'Rental income estimates presented on this website are based on market analysis and historical data. Actual results may vary significantly depending on property condition, location, seasonality, and market changes.'; ?></p>

        <h2 id="liability"><?php echo $it ? '8. Limitazione di Responsabilità' : '8. Limitation of Liability'; ?></h2>
        <p><?php echo $it
          ? 'Nella misura massima consentita dalla legge applicabile, Tailor Homes non sarà responsabile per danni diretti, indiretti, incidentali, consequenziali o punitivi derivanti dall\'utilizzo — o dall\'impossibilità di utilizzo — di questo sito o di qualsiasi contenuto ivi presente.'
          : 'To the fullest extent permitted by applicable law, Tailor Homes shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising from your use of — or inability to use — this website or any content contained herein.'; ?></p>
        <p><?php echo $it
          ? 'Questa limitazione si applica indipendentemente dal fatto che i danni derivino da contratto, illecito, negligenza o qualsiasi altra teoria giuridica, anche qualora Tailor Homes sia stata informata della possibilità di tali danni.'
          : 'This limitation applies regardless of whether the damages arise in contract, tort, negligence, or any other legal theory, even if Tailor Homes has been advised of the possibility of such damages.'; ?></p>

        <h2 id="third-party"><?php echo $it ? '9. Link di Terze Parti' : '9. Third-Party Links'; ?></h2>
        <p><?php echo $it
          ? 'Questo sito può contenere link a siti web di terze parti come Airbnb, Booking.com o altre piattaforme. Questi link sono forniti solo per comodità. Tailor Homes non ha alcun controllo sui contenuti, le pratiche sulla privacy o i termini di questi siti esterni e non se ne assume alcuna responsabilità.'
          : 'This website may contain links to third-party websites such as Airbnb, Booking.com, or other platforms. These links are provided for convenience only. Tailor Homes has no control over the content, privacy practices, or terms of these external sites and accepts no responsibility for them.'; ?></p>
        <p><?php echo $it ? 'Il collegamento a qualsiasi sito di terze parti non implica endorsement o affiliazione.' : 'Linking to any third-party site does not imply endorsement or affiliation.'; ?></p>

        <h2 id="governing-law"><?php echo $it ? '10. Legge Applicabile' : '10. Governing Law'; ?></h2>
        <p><?php echo $it
          ? 'I presenti Termini e Condizioni sono regolati e interpretati in conformità con le leggi della <strong>Repubblica Italiana</strong>, in particolare il Codice Civile italiano e i regolamenti UE applicabili. Qualsiasi controversia derivante da o relativa a questi termini sarà soggetta alla giurisdizione esclusiva dei tribunali di <strong>Padova, Italia</strong>.'
          : 'These Terms and Conditions are governed by and construed in accordance with the laws of <strong>Italy</strong>, in particular the Italian Civil Code and applicable EU regulations. Any disputes arising from or related to these terms shall be subject to the exclusive jurisdiction of the courts of <strong>Padova, Italy</strong>.'; ?></p>
        <p><?php echo $it
          ? 'Per i consumatori residenti nell\'UE, si applicano anche le disposizioni obbligatorie a tutela dei consumatori del paese di residenza.'
          : 'For consumers resident in the EU, mandatory consumer protection provisions of your country of residence also apply.'; ?></p>

        <h2 id="changes"><?php echo $it ? '11. Modifiche ai Presenti Termini' : '11. Changes to These Terms'; ?></h2>
        <p><?php echo $it
          ? 'Tailor Homes si riserva il diritto di aggiornare o modificare i presenti Termini e Condizioni in qualsiasi momento. Le modifiche saranno efficaci immediatamente dopo la pubblicazione su questa pagina. La data in cima a questa pagina indicherà l\'aggiornamento più recente.'
          : 'Tailor Homes reserves the right to update or modify these Terms and Conditions at any time. Changes will be effective immediately upon publication on this page. The date at the top of this page will reflect the most recent update.'; ?></p>
        <p><?php echo $it
          ? 'L\'utilizzo continuato di questo sito dopo qualsiasi modifica costituisce accettazione dei termini aggiornati. Si consiglia di consultare periodicamente questa pagina.'
          : 'Continued use of this website after any changes constitutes your acceptance of the updated terms. We recommend reviewing this page periodically.'; ?></p>

        <h2 id="contact"><?php echo $it ? '12. Contatti' : '12. Contact'; ?></h2>
        <p><?php echo $it ? 'Per qualsiasi domanda sui presenti Termini e Condizioni, contattaci:' : 'If you have any questions about these Terms and Conditions, please contact us:'; ?></p>
        <p>
          <strong>Tailor Homes</strong><br>
          Via degli Obizzi, 1 — 35122 Padova, Italy<br>
          <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a><br>
          <a href="tel:+393714453904">+39 371 445 3904</a> / <a href="tel:+390494906189">+39 049 490 6189</a>
        </p>

      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
