<?php
/**
 * Template Name: Privacy Policy
 */
get_header();
$it = (function_exists('pll_current_language') && pll_current_language() === 'it');
?>
<script>document.body.classList.add('th-solid-header');</script>

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

  /* Sticky sidebar nav */
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

  /* Content */
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
      <h1 class="th-legal-hero__heading"><?php echo $it ? 'Informativa<br>sulla Privacy.' : 'Privacy<br>Policy.'; ?></h1>
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
        <a href="#scope"><?php echo $it ? 'Ambito' : 'Scope'; ?></a>
        <a href="#who-we-are"><?php echo $it ? 'Chi Siamo' : 'Who We Are'; ?></a>
        <a href="#data-we-collect"><?php echo $it ? 'Dati Raccolti' : 'Data We Collect'; ?></a>
        <a href="#how-we-use"><?php echo $it ? 'Utilizzo dei Dati' : 'How We Use It'; ?></a>
        <a href="#legal-basis"><?php echo $it ? 'Base Giuridica' : 'Legal Basis'; ?></a>
        <a href="#cookies">Cookies</a>
        <a href="#data-sharing"><?php echo $it ? 'Condivisione Dati' : 'Data Sharing & Transfers'; ?></a>
        <a href="#retention"><?php echo $it ? 'Conservazione Dati' : 'Data Retention'; ?></a>
        <a href="#your-rights"><?php echo $it ? 'I Tuoi Diritti' : 'Your Rights'; ?></a>
        <a href="#security"><?php echo $it ? 'Sicurezza' : 'Security'; ?></a>
        <a href="#contact"><?php echo $it ? 'Contattaci' : 'Contact Us'; ?></a>
      </nav>

      <!-- Main Content -->
      <div class="th-legal-content">

        <div class="th-legal-highlight">
          <?php echo $it
            ? 'La presente Informativa sulla Privacy spiega come Tailor Homes raccoglie, utilizza e protegge i tuoi dati personali quando visiti il nostro sito web o ci contatti. Ci impegniamo a trattare i tuoi dati in modo trasparente e nel pieno rispetto del Regolamento Generale sulla Protezione dei Dati (GDPR) dell\'UE e della normativa italiana vigente in materia di privacy.'
            : 'This Privacy Policy explains how Tailor Homes collects, uses, and protects your personal data when you visit our website or contact us. We are committed to handling your data transparently and in full compliance with the EU General Data Protection Regulation (GDPR) and applicable Italian privacy law.'; ?>
        </div>

        <h2 id="scope"><?php echo $it ? '1. Ambito di Applicazione' : '1. Scope of This Policy'; ?></h2>
        <p><?php echo $it
          ? 'La presente Informativa sulla Privacy si applica esclusivamente al sito web gestito da Tailor Homes al proprio dominio registrato. Non si applica a siti web di terze parti accessibili tramite link esterni presenti sul nostro sito. Si consiglia di consultare le informative sulla privacy di eventuali siti esterni visitati.'
          : 'This Privacy Policy applies exclusively to the website operated by Tailor Homes at its registered domain. It does not apply to any third-party websites accessible via external links on our site. We recommend reviewing the privacy policies of any external sites you visit independently.'; ?></p>
        <p><?php echo $it
          ? 'Il trattamento dei dati personali si basa sui principi di liceità, correttezza, trasparenza, limitazione delle finalità, minimizzazione dei dati, esattezza, integrità e riservatezza, nel pieno rispetto del Regolamento UE 2016/679 (GDPR) e della legislazione italiana applicabile.'
          : 'The processing of your personal data is based on the principles of lawfulness, fairness, transparency, purpose limitation, data minimisation, accuracy, integrity, and confidentiality, in full compliance with EU Regulation 2016/679 (GDPR) and applicable Italian legislation.'; ?></p>

        <h2 id="who-we-are"><?php echo $it ? '2. Chi Siamo' : '2. Who We Are'; ?></h2>
        <p><strong><?php echo $it ? 'Titolare del Trattamento:' : 'Data Controller:'; ?></strong> Tailor Homes<br>
        <strong><?php echo $it ? 'Indirizzo:' : 'Address:'; ?></strong> Via degli Obizzi, 1 — 35122 Padova, Italy<br>
        <strong>Email:</strong> <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a><br>
        <strong><?php echo $it ? 'Telefono:' : 'Phone:'; ?></strong> <a href="tel:+393714453904">+39 371 445 3904</a> / <a href="tel:+390494906189">+39 049 490 6189</a></p>
        <p><?php echo $it
          ? 'Tailor Homes è una società di gestione immobiliare specializzata in affitti brevi e a medio termine. Siamo il titolare del trattamento dei dati personali raccolti attraverso questo sito web.'
          : 'Tailor Homes is a property management company specialising in short-term and medium-term rentals. We are the data controller responsible for personal data collected through this website.'; ?></p>

        <h2 id="data-we-collect"><?php echo $it ? '3. Dati che Raccogliamo' : '3. Data We Collect'; ?></h2>
        <p><?php echo $it ? 'Raccogliamo dati personali nelle seguenti situazioni:' : 'We collect personal data in the following situations:'; ?></p>
        <p><strong><?php echo $it ? 'Modulo di Contatto' : 'Contact Form'; ?></strong></p>
        <ul>
          <li><?php echo $it ? 'Nome completo' : 'Full name'; ?></li>
          <li><?php echo $it ? 'Indirizzo email' : 'Email address'; ?></li>
          <li><?php echo $it ? 'Contenuto del messaggio' : 'Message content'; ?></li>
          <li><?php echo $it ? 'Oggetto / tipo di richiesta' : 'Subject / enquiry type'; ?></li>
        </ul>
        <p><strong><?php echo $it ? 'Iscrizione alla Newsletter' : 'Newsletter Signup'; ?></strong></p>
        <ul>
          <li><?php echo $it ? 'Indirizzo email' : 'Email address'; ?></li>
          <li><?php echo $it ? 'Nome (se fornito)' : 'Name (if provided)'; ?></li>
          <li><?php echo $it ? 'Data e ora dell\'iscrizione' : 'Date and time of subscription'; ?></li>
          <li><?php echo $it ? 'Indirizzo IP (registrato automaticamente per conformità)' : 'IP address (recorded automatically for compliance)'; ?></li>
        </ul>
        <p><strong><?php echo $it ? 'Dati raccolti automaticamente' : 'Automatically collected data'; ?></strong></p>
        <ul>
          <li><?php echo $it ? 'Indirizzo IP e posizione approssimativa' : 'IP address and approximate location'; ?></li>
          <li><?php echo $it ? 'Tipo di browser e informazioni sul dispositivo' : 'Browser type and device information'; ?></li>
          <li><?php echo $it ? 'Pagine visitate e tempo trascorso sul sito' : 'Pages visited and time spent on site'; ?></li>
          <li><?php echo $it ? 'Fonte di provenienza (come hai trovato il nostro sito)' : 'Referral source (how you found our website)'; ?></li>
        </ul>

        <h2 id="how-we-use"><?php echo $it ? '4. Come Utilizziamo i Tuoi Dati' : '4. How We Use Your Data'; ?></h2>
        <p><?php echo $it ? 'Utilizziamo i dati raccolti esclusivamente per le seguenti finalità:' : 'We use the data we collect solely for the following purposes:'; ?></p>
        <ul>
          <li><?php echo $it ? 'Per rispondere alle richieste inviate tramite il modulo di contatto' : 'To respond to enquiries submitted through the contact form'; ?></li>
          <li><?php echo $it ? 'Per inviare newsletter e aggiornamenti sugli immobili agli iscritti' : 'To send newsletters and property updates to subscribers'; ?></li>
          <li><?php echo $it ? 'Per analizzare il traffico del sito e migliorare il nostro servizio (analytics)' : 'To analyse website traffic and improve our service (analytics)'; ?></li>
          <li><?php echo $it ? 'Per adempiere ai nostri obblighi legali e normativi' : 'To comply with our legal and regulatory obligations'; ?></li>
        </ul>
        <p><?php echo $it ? 'Non utilizziamo i tuoi dati per decisioni automatizzate o profilazione. Non vendiamo i tuoi dati a terze parti.' : 'We do not use your data for automated decision-making or profiling. We do not sell your data to third parties.'; ?></p>

        <h2 id="legal-basis"><?php echo $it ? '5. Base Giuridica del Trattamento' : '5. Legal Basis for Processing'; ?></h2>
        <p><?php echo $it ? 'Ai sensi del GDPR, trattiamo i tuoi dati personali sulla base delle seguenti basi giuridiche:' : 'Under the GDPR, we process your personal data on the following legal bases:'; ?></p>
        <ul>
          <li><strong><?php echo $it ? 'Consenso' : 'Consent'; ?></strong> — <?php echo $it ? 'per le iscrizioni alla newsletter e i cookie non essenziali. Puoi revocare il consenso in qualsiasi momento.' : 'for newsletter subscriptions and non-essential cookies. You may withdraw consent at any time.'; ?></li>
          <li><strong><?php echo $it ? 'Interesse legittimo' : 'Legitimate interest'; ?></strong> — <?php echo $it ? 'per rispondere alle richieste del modulo di contatto e per le analisi di base del sito web.' : 'for responding to contact form enquiries and for basic website analytics.'; ?></li>
          <li><strong><?php echo $it ? 'Obbligo legale' : 'Legal obligation'; ?></strong> — <?php echo $it ? 'dove richiesto dalla legge italiana o dell\'UE.' : 'where required by Italian or EU law.'; ?></li>
        </ul>

        <h2 id="cookies">6. Cookies</h2>
        <p><?php echo $it
          ? 'Il nostro sito utilizza i cookie — piccoli file di testo memorizzati sul tuo dispositivo quando visiti una pagina. Aiutano il sito a funzionare correttamente, ricordano le tue preferenze e analizzano come i visitatori utilizzano il sito. Di seguito una descrizione completa dei tipi di cookie che utilizziamo.'
          : 'Our website uses cookies — small text files stored on your device when you visit a page. They help the site function correctly, remember your preferences, and analyse how visitors use the site. Below is a full breakdown of the types of cookies we use.'; ?></p>

        <p><strong><?php echo $it ? 'Cookie tecnici (sempre attivi)' : 'Technical cookies (always active)'; ?></strong><br>
        <?php echo $it
          ? 'Sono strettamente necessari per il funzionamento del sito e non possono essere disattivati. Includono identificatori di sessione che consentono una navigazione sicura e le funzionalità principali. Non raccolgono dati personali a fini di marketing e non richiedono il tuo consenso.'
          : 'These are strictly necessary for the website to operate and cannot be disabled. They include session identifiers that allow safe navigation and core functionality. They do not collect personal data for marketing purposes and do not require your consent.'; ?></p>

        <p><strong><?php echo $it ? 'Cookie analitici' : 'Analytics cookies'; ?></strong><br>
        <?php echo $it
          ? 'Utilizziamo <strong>Google Analytics</strong> per capire come i visitatori interagiscono con il nostro sito — ad esempio quali pagine sono più visitate e come gli utenti navigano. Questi dati sono raccolti in forma anonima e aggregata e non possono essere utilizzati per identificare singoli utenti. I cookie analitici appartengono alla categoria tecnica e sono utilizzati esclusivamente per migliorare le prestazioni e l\'usabilità del sito. Puoi disattivarli in qualsiasi momento installando il <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">componente aggiuntivo per la disattivazione di Google Analytics</a>.'
          : 'We use <strong>Google Analytics</strong> to understand how visitors interact with our site — such as which pages are most visited and how users navigate. This data is collected in anonymised and aggregated form and cannot be used to identify individual users. Analytics cookies belong to the technical category and are used solely to improve the site\'s performance and usability. You can opt out at any time by installing the <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics Opt-out Browser Add-on</a>.'; ?></p>

        <p><strong><?php echo $it ? 'Cookie di preferenza' : 'Preference cookies'; ?></strong><br>
        <?php echo $it
          ? 'Impostati da <strong>Weglot</strong>, il nostro servizio di traduzione, questi cookie ricordano la tua scelta linguistica in modo che non debba riselezionarla ad ogni visita. Non tracciano il comportamento di navigazione.'
          : 'Set by <strong>Weglot</strong>, our translation service, these cookies remember your language choice so you don\'t have to re-select it on every visit. They do not track browsing behaviour.'; ?></p>

        <p><strong><?php echo $it ? 'Cookie di profilazione e di terze parti' : 'Profiling and third-party cookies'; ?></strong><br>
        <?php echo $it
          ? 'Il nostro sito può includere elementi (come pulsanti social o contenuti incorporati) che caricano cookie da piattaforme di terze parti tra cui Facebook, Instagram e YouTube. Questi cookie di terze parti possono essere utilizzati per tracciare la tua attività su altri siti web a fini pubblicitari o di personalizzazione. Non controlliamo direttamente questi cookie — ti invitiamo a consultare le informative sulla privacy di ciascuna piattaforma:'
          : 'Our site may include elements (such as social media buttons or embedded content) that load cookies from third-party platforms including Facebook, Instagram, and YouTube. These third-party cookies may be used to track your activity across other websites for advertising or personalisation purposes. We do not control these cookies directly — you are encouraged to review the privacy policies of each platform:'; ?></p>
        <ul>
          <li><a href="https://www.facebook.com/about/privacy/" target="_blank" rel="noopener"><?php echo $it ? 'Informativa Privacy di Facebook / Meta' : 'Facebook / Meta Privacy Policy'; ?></a></li>
          <li><a href="https://policies.google.com/privacy" target="_blank" rel="noopener"><?php echo $it ? 'Informativa Privacy di Google / YouTube' : 'Google / YouTube Privacy Policy'; ?></a></li>
        </ul>
        <p><?php echo $it
          ? 'L\'utilizzo dei cookie di profilazione richiede il tuo consenso preventivo e liberamente prestato. Puoi gestire o revocare le tue preferenze sui cookie in qualsiasi momento tramite il nostro banner cookie o le impostazioni del browser. Per la gestione individuale dei cookie, puoi anche visitare <a href="https://www.youronlinechoices.com" target="_blank" rel="noopener">youronlinechoices.com</a>.'
          : 'The use of profiling cookies requires your prior, freely given consent. You can manage or withdraw your cookie preferences at any time via our cookie banner or through your browser settings. For individual cookie management, you may also visit <a href="https://www.youronlinechoices.com" target="_blank" rel="noopener">youronlinechoices.com</a>.'; ?></p>

        <h2 id="data-sharing"><?php echo $it ? '7. Condivisione dei Dati e Trasferimenti Internazionali' : '7. Data Sharing &amp; International Transfers'; ?></h2>
        <p><?php echo $it
          ? 'Condividiamo i dati solo con fornitori di servizi terzi fidati che ci aiutano a gestire il nostro sito web e i nostri servizi. Tutti i responsabili del trattamento sono vincolati da accordi sul trattamento dei dati e sono conformi al GDPR:'
          : 'We share data only with trusted third-party service providers who help us operate our website and services. All processors are bound by data processing agreements and comply with GDPR:'; ?></p>
        <ul>
          <li><strong>Google Analytics</strong> — <?php echo $it ? 'analisi del traffico web. I dati possono essere archiviati ed elaborati su server situati negli Stati Uniti. Google fornisce garanzie adeguate tramite le Clausole Contrattuali Standard approvate dalla Commissione Europea.' : 'website traffic analysis. Data may be stored and processed on servers located in the United States. Google provides adequate safeguards through Standard Contractual Clauses approved by the European Commission.'; ?></li>
          <li><strong>Weglot</strong> — <?php echo $it ? 'servizio di traduzione del sito. Weglot può elaborare contenuti su server al di fuori del SEE; i trasferimenti sono regolati da garanzie contrattuali appropriate.' : 'website translation service. Weglot may process content on servers outside the EEA; transfers are governed by appropriate contractual safeguards.'; ?></li>
          <li><strong><?php echo $it ? 'Fornitore servizio email / newsletter' : 'Email / newsletter service provider'; ?></strong> — <?php echo $it ? 'per l\'invio di newsletter e risposte al modulo di contatto. La posizione dei server dipende dal fornitore utilizzato ed è coperta dai meccanismi di trasferimento applicabili.' : 'for sending newsletters and contact form responses. Server location depends on the provider used and is covered by applicable transfer mechanisms.'; ?></li>
          <li><strong><?php echo $it ? 'Fornitore di hosting' : 'Hosting provider'; ?></strong> — <?php echo $it ? 'per l\'infrastruttura e l\'archiviazione del sito web.' : 'for website infrastructure and storage.'; ?></li>
        </ul>
        <p><?php echo $it
          ? 'Qualora i dati personali siano trasferiti al di fuori dello Spazio Economico Europeo (SEE), Tailor Homes garantisce l\'adozione di garanzie appropriate — incluse le Clausole Contrattuali Standard (SCC) riconosciute dalla Commissione Europea — per mantenere un livello equivalente di protezione dei dati. Non trasferiamo i tuoi dati al di fuori del SEE senza tali garanzie.'
          : 'Where personal data is transferred outside the European Economic Area (EEA), Tailor Homes ensures that appropriate safeguards are in place — including Standard Contractual Clauses (SCCs) as recognised by the European Commission — to maintain an equivalent level of data protection. We do not transfer your data outside the EEA without such safeguards.'; ?></p>

        <h2 id="retention"><?php echo $it ? '8. Conservazione dei Dati' : '8. Data Retention'; ?></h2>
        <p><?php echo $it ? 'Conserviamo i tuoi dati personali solo per il tempo necessario:' : 'We retain your personal data only for as long as necessary:'; ?></p>
        <ul>
          <li><strong><?php echo $it ? 'Richieste dal modulo di contatto' : 'Contact form enquiries'; ?></strong> — <?php echo $it ? 'fino a 2 anni, o fino alla risoluzione della questione' : 'up to 2 years, or until the matter is resolved'; ?></li>
          <li><strong><?php echo $it ? 'Iscrizioni alla newsletter' : 'Newsletter subscriptions'; ?></strong> — <?php echo $it ? 'fino alla cancellazione dell\'iscrizione' : 'until you unsubscribe'; ?></li>
          <li><strong><?php echo $it ? 'Dati analitici' : 'Analytics data'; ?></strong> — <?php echo $it ? 'fino a 26 mesi (impostazione predefinita di Google Analytics)' : 'up to 26 months (Google Analytics default)'; ?></li>
        </ul>
        <p><?php echo $it ? 'Quando i dati non sono più necessari, vengono cancellati in modo sicuro o anonimizzati.' : 'When data is no longer needed, it is securely deleted or anonymised.'; ?></p>

        <h2 id="your-rights"><?php echo $it ? '9. I Tuoi Diritti' : '9. Your Rights'; ?></h2>
        <p><?php echo $it ? 'Ai sensi del GDPR, hai i seguenti diritti riguardo ai tuoi dati personali:' : 'Under the GDPR, you have the following rights regarding your personal data:'; ?></p>
        <ul>
          <li><strong><?php echo $it ? 'Diritto di accesso' : 'Right of access'; ?></strong> — <?php echo $it ? 'richiedere una copia dei dati che conserviamo su di te' : 'request a copy of the data we hold about you'; ?></li>
          <li><strong><?php echo $it ? 'Diritto di rettifica' : 'Right of rectification'; ?></strong> — <?php echo $it ? 'chiedere la correzione di dati inesatti' : 'ask us to correct inaccurate data'; ?></li>
          <li><strong><?php echo $it ? 'Diritto alla cancellazione' : 'Right of erasure'; ?></strong> — <?php echo $it ? 'richiedere la cancellazione dei tuoi dati ("diritto all\'oblio")' : 'request deletion of your data ("right to be forgotten")'; ?></li>
          <li><strong><?php echo $it ? 'Diritto di limitazione del trattamento' : 'Right to restrict processing'; ?></strong> — <?php echo $it ? 'chiedere la sospensione del trattamento dei tuoi dati' : 'ask us to pause processing of your data'; ?></li>
          <li><strong><?php echo $it ? 'Diritto alla portabilità dei dati' : 'Right to data portability'; ?></strong> — <?php echo $it ? 'ricevere i tuoi dati in un formato leggibile da dispositivo automatico' : 'receive your data in a machine-readable format'; ?></li>
          <li><strong><?php echo $it ? 'Diritto di opposizione' : 'Right to object'; ?></strong> — <?php echo $it ? 'opporsi al trattamento basato su interesse legittimo' : 'object to processing based on legitimate interest'; ?></li>
          <li><strong><?php echo $it ? 'Diritto di revocare il consenso' : 'Right to withdraw consent'; ?></strong> — <?php echo $it ? 'in qualsiasi momento, dove il trattamento si basa sul consenso' : 'at any time, where processing is consent-based'; ?></li>
        </ul>
        <p><?php echo $it
          ? 'Per esercitare uno di questi diritti, contattaci all\'indirizzo <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a>. Risponderemo entro 30 giorni. Hai inoltre il diritto di presentare un reclamo all\'autorità italiana per la protezione dei dati: <strong>Garante per la protezione dei dati personali</strong> — <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener">garanteprivacy.it</a>.'
          : 'To exercise any of these rights, contact us at <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a>. We will respond within 30 days. You also have the right to lodge a complaint with the Italian data protection authority: <strong>Garante per la protezione dei dati personali</strong> — <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener">garanteprivacy.it</a>.'; ?></p>

        <h2 id="security"><?php echo $it ? '10. Sicurezza' : '10. Security'; ?></h2>
        <p><?php echo $it
          ? 'Adottiamo misure tecniche e organizzative ragionevoli per proteggere i tuoi dati personali da accessi non autorizzati, perdita o divulgazione. Il nostro sito è servito tramite HTTPS. L\'accesso ai dati personali è limitato al solo personale autorizzato.'
          : 'We take reasonable technical and organisational measures to protect your personal data against unauthorised access, loss, or disclosure. Our website is served over HTTPS. Access to personal data is restricted to authorised personnel only.'; ?></p>
        <p><?php echo $it
          ? 'Pur prendendo la sicurezza molto seriamente, nessun metodo di trasmissione su Internet è sicuro al 100%. Se hai dubbi sulla sicurezza dei dati, contattaci direttamente.'
          : 'While we take security seriously, no method of transmission over the internet is 100% secure. If you have concerns about data security, please contact us directly.'; ?></p>

        <h2 id="contact"><?php echo $it ? '11. Contattaci' : '11. Contact Us'; ?></h2>
        <p><?php echo $it
          ? 'Per qualsiasi domanda su questa Informativa sulla Privacy o su come gestiamo i tuoi dati, contattaci:'
          : 'If you have any questions about this Privacy Policy or how we handle your data, please contact us:'; ?></p>
        <p>
          <strong>Tailor Homes</strong><br>
          Via degli Obizzi, 1 — 35122 Padova, Italy<br>
          <a href="mailto:info@tailorhomes.it">info@tailorhomes.it</a><br>
          <a href="tel:+393714453904">+39 371 445 3904</a> / <a href="tel:+390494906189">+39 049 490 6189</a>
        </p>
        <p><?php echo $it
          ? 'Questa informativa può essere aggiornata di tanto in tanto. La data in cima a questa pagina indica la revisione più recente. L\'utilizzo continuato del sito web dopo qualsiasi aggiornamento costituisce accettazione dell\'informativa aggiornata.'
          : 'This policy may be updated from time to time. The date at the top of this page reflects the most recent revision. Continued use of the website after any update constitutes acceptance of the revised policy.'; ?></p>

      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
