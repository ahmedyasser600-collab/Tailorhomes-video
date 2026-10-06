## v2.10.0 - PageSpeed: performance, accessibility, SEO
Targets the mobile PageSpeed report (Performance 77, LCP 15.6 s).

**Performance**
- Removed the full-screen loading screen: it hid the page for up to 2.5-4 s on every visit.
- Home hero is now a real `<img>` with `srcset`/`sizes` (phones download a small version instead of the full-size original), `fetchpriority="high"` and a `<link rel="preload">` in the head. Previously it was a CSS background, found late and always full-size.
- Google Fonts: one variable-font file per family instead of one per weight, preconnect hints, and the stylesheet no longer blocks first paint.
- `th_img()` helper: Customizer image URLs get responsive `srcset`, intrinsic width/height and `loading="lazy"` (home cards, About, Owners, Gallery sliders, Corporate band, blog featured image).
- `logo.svg` minified from 148 KB to 30 KB (visually identical). Service icons minified.
- Partner logos, hero TH symbol and founder photo converted to resized WebP (about 380 KB down to about 60 KB); the unused PNG/JPG originals were removed.
- Bundled blog images converted from about 2 MB PNGs to 1600px WebP (60-150 KB each); the seeder now imports the WebP files. Theme size: 17 MB down to 1.6 MB.
- `main.js` loads with `defer`; the scroll handler runs once per frame and only touches the DOM when the state flips.
- Block-library CSS skipped on the theme's hand-built templates; WP emoji script removed.
- Added `.htaccess` in the theme folder with long browser-cache lifetimes for theme CSS/JS/images (Apache/LiteSpeed).
- OpenAI pixel `debug` switched off for production.

**Accessibility**
- Contrast: `--th-ink-soft` and `--th-terracotta` darkened, footer/CTA text brightened, hero subtitle band made opaque, WhatsApp button green darkened. All now pass WCAG AA.
- Footer column titles are `h2` (no more h2 to h4 jump); hero decorative logo uses empty alt.
- Logo links have accessible names; the language switcher is a real `<button>` with `aria-expanded` and opens on tap; the burger has `aria-expanded`/`aria-controls`; the closed mobile menu is `inert`; Escape closes menus.
- Skip-to-content link, visible keyboard focus rings, `prefers-reduced-motion` support. Reveal animations no longer hide content if JS fails.
- Duplicate partner-ticker logos hidden from screen readers.

**SEO**
- Fallback `<meta name="description">` (EN/IT, uses the post excerpt when present). Skipped automatically if Yoast, Rank Math, AIOSEO, SEOPress or The SEO Framework is active.

**UX**
- Hero now has two clear actions: Book Now (Krossbooking) and Own a property? (Owners page).


## v2.9.2 - Blog removed from header menu
- Blog no longer appears in the header or mobile menu; it stays in the footer only, to reduce header crowding.
- Added a `wp_nav_menu_objects` filter on the primary location that drops the Blog item (matched by title, blog page ID, or URL path) along with any children, so no change is needed in the WP menu editor.
- Removed Blog from the fallback menu.


## v2.9.1 - Bilingual booking guide + post CTA
- Added a new bilingual blog post, "The Best Way to Book Your Stay in Padova, Italy" / "Il Modo Migliore per Prenotare il Tuo Soggiorno a Padova", seeded via `inc/seed-booking-blogs.php` and linked EN<->IT through Polylang.
- Post bodies in `assets/blog-content/book_stay_padova_en.html` and `_it.html`, following the existing th-blog9 markup.
- Added FAQPage JSON-LD output in `single-post.php`, driven by a `_th_faq_schema` post meta array, for FAQ rich results.
- Added a booking CTA block at the end of every blog post, linking to the Krossbooking engine in the reader's language.
- Added `[th_book_url]` and `[th_page slug='...']` shortcodes so blog content never hardcodes a language-specific URL.


## v2.9.0 — Direct booking CTA
- The header CTA button (previously "Gallery" / "Galleria") is now "Book Now" / "Prenota Ora" and points to the Krossbooking direct booking engine (https://tailorhomes-new.kross.travel/), opening in a new tab.
- Added `th_booking_url()`, `th_booking_label()` and `th_booking_cta_link()` helpers so the booking URL lives in one place.
- The nav walker now turns any menu item flagged as CTA (custom class `th-cta`, or a title containing "book"/"prenota") into the booking button, in the desktop header and the mobile menu.
- Added a safety filter that keeps a plain Gallery / Galleria link in the primary menu and appends the booking button if the saved menu has no CTA item.
- Bumped theme version and stylesheet cache version.


## v2.8.9
- Removed the oversized faded TH watermark from the Services hero.
- Rebalanced the Services hero to match Chi Siamo more closely: title on the left, intro copy on the right, no empty visual mark area.
- Bumped CSS cache version.


## v2.8.8 - Services typography alignment
- Aligned the Services hero typography with the Chi Siamo hero by switching the large Services heading back to the same bold DM Sans treatment.
- Kept the refined services layout and icon-card structure, while changing the section/card headings to the same sans-serif brand style used across Chi Siamo.
- Bumped stylesheet cache versions for deployment.

# 2.8.5 — Header, corporate card routing, and home order

- Added **Lavora con Noi / Work With Us** to the primary header menu, including existing saved menus.
- Updated the homepage Corporate Housing card to link directly to the Contact page.
- Renamed Via Locatelli labels to **Locatelli Apartments / Appartamenti Locatelli** in gallery/admin labels and review displays.
- Moved the Students & Erasmus and Corporate Housing cards directly under the Four Pillars section on the homepage.

# 2.8.4 — Option A content refinements

- Updated Gallery caption in English and Italian to the understated invitation copy.
- Updated Services hero caption to the Option A corporate housing copy.
- Updated Corporate Housing extended service card heading and description to Option A.
- Updated the homepage Corporate Housing card to Option A in English and Italian.

# 2.8.3 — Gallery, corporate housing, and home service card

- Renamed the collection page/menu/footer label to **Gallery** / **Galleria** while keeping existing `/apartments` and `/appartamenti` URLs intact.
- Updated the Gallery page caption to: “This is part of our portfolio. Contact us to get our full portfolio.”
- Added Corporate Housing as a visible service for short and medium-term company stays.
- Added Corporate Housing wording to the Services page hero/intro copy.
- Added a new Corporate Housing card next to the Students & Erasmus card on the home page.

# Tailor Homes Theme — Changelog

## v2.8.1 (June 2026)

**Home page — Students & Erasmus card**
- New card added before the Work With Us square, linking to the Students page.
- Distinct style from the Work With Us card: navy split layout with a photo panel (left) and content (right), a red "−15%" badge, cream/serif headline.
- Photo is uploadable via Customizer → "Home — Students Card". A labelled placeholder shows until a photo is set.

## v2.8 (June 2026)

**New page — Lavora con Noi / Work With Us**
- New template `templates/page-work-with-us.php`, bilingual, routed via th_url('work-with-us' / 'lavora-con-noi').
- Hero + three "ways to work with us" (employee / property-owner partner / business-other) + a form.
- Form is plugin-driven (Option A): renders the shortcode set in Customizer → Form Shortcodes → "Work With Us page". Falls back to a styled placeholder form if no shortcode set. CSS styles Contact Form 7 / WPForms output to match the site.

**New page — Studenti & Erasmus / Students & Erasmus**
- New template `templates/page-students.php`, bilingual, routed via th_url('students' / 'studenti').
- Explains the 15% student discount. Form collects name, student type (Erasmus / UniPD), and university, then opens WhatsApp (wa.me/393714453904) with a pre-filled message; the student attaches their card photo directly in the chat.

**Contact page — form now functional (Option A)**
- Renders the Customizer "Contact page" form shortcode if set; otherwise shows the existing static placeholder form. (The old form was a non-submitting placeholder.)

**Customizer**
- New "Form Shortcodes" section with two textarea fields: Contact page form, Work With Us page form. Paste a Contact Form 7 / WPForms shortcode into each.

**Home page**
- New "Work With Us" CTA square added before the bottom "Hai un immobile?" section — cream card, red left border, Cormorant italic headline, links to the new page.

**Footer**
- Added "Lavora con Noi / Work With Us" and "Studenti & Erasmus / Students & Erasmus" nav links.
- Re-applied the real social URLs (Facebook 61575933561481, Instagram @tailor_homes, YouTube @TailorHomes-Studio, LinkedIn /company/tailor-homes) — the base had reverted to placeholders.

## v2.7.2 (June 2026)

**Favicon**
- Black background removed — favicon is now a clean red "TH" on a transparent background (both favicon.png and favicon.svg). Sits naturally on light browser tabs and bookmarks.

## v2.7.1 (June 2026)

**Hero subtitle**
- Restored the soft cream fade band behind the subtitle, now sitting under the bold red text. Combines the v2.7 red/bold/bigger styling with the earlier horizontal fade backdrop.

## v2.7 (June 2026)

**Favicon**
- Replaced favicon with the red serif "TH" mark on black (favicon.png 512×512 + favicon.svg embedding the same artwork). Shows in browser tabs, bookmarks, and mobile home screens.

**Bento cards — all four uniform**
- Red 4px left border + cream background now applied to ALL four cards via the base `.th-bento__card` class (previously only card 01). Hover no longer wipes the red left border (only the other three sides shift).
- Base card icon (108px), title (clamp 24–32px), and Cormorant italic 19px lede unified across all cards.

**Hero subtitle**
- "Gestione Immobiliare — Appartamenti & Ville di Prestigio" restyled: bolder (700), bigger (15px, was 11px), and in the brand red (var(--th-red)) to match the TH logo. Fade band removed; soft cream text-shadow retained for legibility.

**Reviews (home page)**
- Caterina (Feb 2025): La Casa Bianca → Monselice.
- Maria (Nov 2025): La Casa Bianca → Locatelli Apartments / Appartamenti Locatelli (label corrected to feminine "Soggiornata a").
- Alex (Apr 2026): Via Marcanova → Marcanova (consistency).

**Our Collection page — property names**
- Apt 03: La Casa Bianca → Locatelli Apartments / Appartamenti Locatelli.
- Apt 04: Via Marcanova → Marcanova.
- Apt 05: Via Monselice → Monselice.
- Note: apt 05 description prose still references the street "Via Monselice" (the actual address) to avoid confusion with the nearby town of Monselice.

**Consistency cleanup**
- Standalone Reviews page: "La Casa Bianca" → "Marcanova"; stale "SubitoSanto" → "Monselice".
- Customizer admin photo-slot labels updated to Locatelli Apartments / Marcanova / Monselice (admin-only; slot keys unchanged, so all photos preserved).

## v2.6.3 (May 2026)

**Hero subtitle — soft fade instead of pill**
- Replaced the rounded pill backdrop with a soft horizontal cream gradient that fades to transparent on both sides. No border, no rounded corners, no glass effect — just a halo of light behind the navy text, dissolving naturally into the hero photo.

## v2.6.2 (May 2026)

**Hero subtitle — frosted-glass refinement**
- Refined the subtitle pill from a solid cream to a frosted-glass treatment: diagonal cream gradient (55%→38% opacity), 14px backdrop blur with saturation boost, 1px hairline border, and layered inner-highlight + outer-shadow box-shadows. More elegant and luxurious; reads as an etched glass plaque rather than a UI badge.

## v2.6.1 (May 2026)

**Hero subtitle readability**
- Added a soft cream pill backdrop behind the navy hero subtitle for legibility on any hero photo. Background: rgba(245, 240, 232, 0.92) with 6px backdrop blur, rounded full pill shape, subtle drop shadow. Text-shadow removed (pill handles readability now).

## v2.6 (May 2026)

**Hero subtitle — terminology + colour**
- "Ville di Lusso" → **"Ville di Prestigio"** (IT).
- "Luxury Villas" → **"Prestige Villas"** (EN).
- Subtitle colour changed from faded cream (rgba(245,240,232,0.5)) to **navy (var(--th-navy), #1B2A4A)** at full opacity, with a soft cream text-shadow for readability on dark hero photos. Weight bumped from 400 to 500.

**Bento cards — uniform tag-chip style across all four**
- Cards 02 (Ottimizzazione), 03 (Clienti), 04 (Consulenza) previously had either dashed `<li>` bullet lists or a complex split icon-paragraph layout. All three are now refactored to use the same **tag-chip pattern as card 01 (Gestione Completa)** — pill-shaped chips with small line icons and uppercase labels.
- Card 02 tags: Home Staging · Interior Design · Tech Solutions · Accessi Smart
- Card 03 tags: Aziende · Università · Ospedali · Enti Pubblici
- Card 04 tags: Consulenza Strategica · Analisi Immobile · Ristrutturazioni · Valorizzazione
- Each tag uses the same Tabler-style outline icon convention as card 01 — calendar, clock, currency, chat, etc.
- Removed dead CSS for `.th-bento__card-list` and `.th-bento__card-strategy-*` / `.th-bento__strategy-*` — the split-layout strategy card variant is no longer in use.

## v2.5 (May 2026)

**Editorial Watermark section headings on the Collection page**
- "Ville di Prestigio" and "Appartamenti" section headings redesigned from plain bold DM Sans to an editorial luxury treatment: Cormorant Garamond italic at light weight (300), clamp(56px, 8vw, 108px), no period.
- Faint large italic numeral ("01" behind villas, "02" behind apartments) sits as a watermark in the background — a magazine-spread editorial cue used to elevate the luxury feel.
- Thin red horizontal rule with a small diamond accent in the centre, placed beneath each heading.
- Villas section has a subtly warmer background tone (`--th-bg-warm`) to visually separate the two collections.
- Section vertical padding increased for better breathing room around the new typography.

## v2.4 (May 2026)

**Apartments page restructure — "La Nostra Collezione"**
- Page H1 changed from "I Nostri Appartamenti" / "Our Apartments" to **"La Nostra Collezione"** / **"Our Collection"**.
- Page now organised into two clearly labelled subsections:
  - **Ville di Prestigio / Prestige Villas** — 06 Casa Almendro (Cartagena), 07 Villa Graziosa (Sardinia)
  - **Appartamenti / Apartments** — 01 Colore & Design, 02 Fitness & Charme, 03 La Casa Bianca, 04 Via Marcanova, 05 Via Monselice
- Each subsection has a small terracotta-uppercase label + large H2 heading before its property cards.
- Hero subtitle broadened to: "Curated apartments and prestige villas — each one individually selected, designed, and maintained to the same standard."

**Photo slots expanded for villas only**
- Villas 06 and 07 now have **10 photo slots each** (was 6). New slots 7–10 are empty by default — fill via WP Customizer.
- Apartments 01–05 remain at 6 slots. **No existing slot keys were renamed** — all currently uploaded photos remain mapped and will load automatically after deployment.
- `th_get_apartment_gallery()` helper now iterates 1–10 (empty slots are skipped automatically).

**Home page hero badge — broader brand framing**
- IT: "Gestione Immobiliare — Padova" → **"Gestione Immobiliare — Appartamenti & Ville di Lusso"**
- EN: "Property Management — Padova" → **"Property Management — Apartments & Luxury Villas"**

**About page intro — broader positioning**
- Added brief mention that Tailor Homes manages both apartments and prestige villas. No specific service list — general framing only.

**Services page intro — broader positioning**
- Same brief mention added. The "across Padova" phrasing was removed since the company now operates beyond Padova (Cartagena, Sardinia).

**Footer**
- "Appartamenti" / "Our Apartments" footer nav link → **"La Nostra Collezione"** / **"Our Collection"**.

**Important deployment note**
The theme folder name remains exactly `tailor-homes-theme`. Deploy via Hostinger File Manager replace-contents only — never via WP Appearance → Themes → Add New. All Customizer photo assignments are preserved because the slot keys (`th_apt_NN_img_N`) are unchanged.


## v286 — Monselice location correction
- Updated apartment 05 naming and location copy so Monselice is not presented as a Padova apartment.
- Reworded related review labels/copy to avoid implying the stay was inside Padova.

## v2.9 — Services restructure: Owners vs Guests

**New page architecture**
- **I Nostri Servizi / Our Services** — rewritten as a hub page with two sections: *Per i Proprietari* (teaser + 4 key points + button to the new Owners page) and *Per gli Ospiti* (two cards: Students, Corporate Housing).
- **NEW: Per i Proprietari / For Owners** (`templates/page-owners.php`) — the full former services content (7-card management grid, extended services incl. home staging / photography / consulting, success-fee highlight, 4 steps, FAQ, CTA) moved here with a dedicated hero.
- **Studenti / Students** (`templates/page-students.php`) — rebuilt as an SEO umbrella page: all-inclusive package (one fixed monthly payment incl. utilities), registered-contract / Questura-residency support content, furnished + local support blocks, then a "UniPD or Erasmus?" split with two cards, then the existing 15% WhatsApp discount form (unchanged logic, anchored at `#student-discount`).
- **NEW: Studenti UniPD** (`templates/page-students-unipd.php`) — academic-year terms, fixed payment, registered contract for residency, study-friendly homes, 15% discount note linking to the form.
- **NEW: Studenti Erasmus** (`templates/page-students-erasmus.php`) — semester flexibility, all-in payment, Questura/permit paperwork support (carefully worded: contract "typically required", we "support" — no guarantees), English-speaking assistance, discount note.
- **NEW: Corporate Housing** (`templates/page-corporate.php`) — premium tone: short & medium-term stays for companies, move-in-ready premium homes, one contact / one invoice, dedicated rates, use-case tag cloud, proposal CTA.

**Translations**
- `page-services-langs.php`: `th_t()` now wrapped in `function_exists` guard; added hub + owners-hero strings. Shared by services hub and owners page.
- NEW `templates/page-guests-langs.php`: `th_gt()` array with all student/UniPD/Erasmus/corporate strings (array pattern — apostrophe-safe).

**Routing & navigation**
- `th_url()` map extended: `owners→proprietari`, `students-unipd→studenti-unipd`, `students-erasmus→studenti-erasmus`, `corporate-housing→alloggi-aziendali`.
- Home page corporate card now links to the Corporate Housing page (was: contact).
- Footer: added "Per i Proprietari / For Owners" and "Corporate Housing" links.
- `services.css` (v unchanged, appended): shared `gst-*` styles for guest pages; enqueued for all new templates.

**Deployment — pages to create in WP admin (both languages, assign templates):**
| Template | IT slug | EN slug |
|---|---|---|
| Owners / Proprietari | `proprietari` | `owners` |
| Students — UniPD | `studenti-unipd` | `students-unipd` |
| Students — Erasmus | `studenti-erasmus` | `students-erasmus` |
| Corporate Housing | `alloggi-aziendali` | `corporate-housing` |

Existing `studenti`/`students` pages keep the "Students" template (content comes from the template, no page edits needed). Theme folder name remains exactly `tailor-homes-theme`; deploy via Hostinger File Manager replace-contents only. No Customizer slot keys were renamed — all photos preserved.

## v2.9.1 — Editorial premium redesign (hub + guest pages + owners hero)
- Replaced the bold-sans hero ("big headline + small side paragraph") on Services hub, Owners, Students, UniPD, Erasmus, and Corporate with the **editorial treatment from La Nostra Collezione**: Jost letterspaced eyebrow, huge Cormorant Garamond italic title, red ornament line + diamond, and a full-width elegant lede paragraph beneath. Each hero has a faint italic-serif watermark word (Servizi / Proprietari / Studenti / UniPD / Erasmus / Corporate).
- Section headers now use giant watermark numerals (01, 02, 03) + serif italic titles.
- Benefit blocks redesigned as hairline-top editorial entries with terracotta serif numerals (hover: red hairline).
- Path cards: serif italic titles, roman-numeral markers (I. / II.), ink top border turning red on hover, arrow-gap CTA animation.
- Corporate use cases: editorial serif list with terracotta diamonds instead of pill tags.
- Discount/contract notes: serif italic pull-quote panels with red left rule.
- Students discount section reframed as section 03 "Il Vantaggio Studenti"; form card restyled (white, red top border, soft shadow). WhatsApp logic unchanged.
- New `ed-*` style family appended to services.css; older `hub-*`/`gst-*` grid styles retained for safety, `gst-back` reused.

## v2.9.2 — Corporate housing is guest-side only; owners "We Also Offer"
- **Owners page**: removed Corporate Housing entirely — both grid card 07 (Alloggi Aziendali) and the first extended card. Corporate housing is a guest offer, not an owner service; it lives on its own page under "Per gli Ospiti".
- Extended services now show 3 cards (Home Staging & Renovation, Professional Photography, Consulting & Investments) renumbered 07/08/09 in a 3-column row (`.sv-ext-grid--three`).
- Section copy: "Oltre la Gestione / Altri Modi per Valorizzare..." → **"Servizi Aggiuntivi / Offriamo Anche."** with a direct we-also-offer intro listing the three services.
- Customizer slot `th_service_img_1` (formerly the corporate card photo) now feeds a wide photo band on the **Corporate Housing page** (hidden if empty). No slot keys renamed; slots 2–4 unchanged.

## v2.9.3 — Reverted to site-wide standard style (bold DM Sans, no serif editorial treatment)
Removed the one-off "editorial premium" (`ed-*`) look introduced in v2.9.1 — italic Cormorant Garamond headlines, giant watermark numerals, red ornament dividers — from Services hub, Owners hero, Students, UniPD, Erasmus, and Corporate. These looked inconsistent against the rest of the site (About, Work With Us, Apartments), which all use the bold DM Sans / Jost system.

All six pages now reuse the theme's existing shared components:
- **Hero**: `sv-hero` (bold 800-weight headline + short sub) followed by `sv-intro` (centered statement paragraph) — same pattern as About Us / Work With Us / the original Services page.
- **Section headers**: `sv-services__label` / `sv-services__title` (light bg) and `sv-extended__label` / `sv-extended__title` (alt bg) — identical typography to the Owners management grid.
- **Benefit blocks**: `sv-card` (white card, numbered top-right, bold heading, grey paragraph) in a new `.sv-grid--two` 2-column variant for 4-item sets.
- **Path/link cards**: `gst-card` — tightened to 18px/700 headings and 14px/300 body text to match `sv-card__heading` exactly (was 21px, slightly off-system).
- **Buttons**: `th-btn th-btn--solid` (the same navy button used on About/Apartments/Reviews) replacing the custom editorial button.
- **Notes/callouts**: `gst-note` (bold sans, red left rule) — dropped its leftover Cormorant Garamond numeral, now DM Sans throughout.
- **Corporate use-case tags**: kept the pill style already used site-wide, now arranged in a fixed 3-column grid (`gst-tags--grid`, per the earlier 3-up/3-down request) instead of the serif diamond list.
- Deleted ~240 lines of now-unused `ed-*` CSS from `services.css`.

No functional changes: WhatsApp discount form, Customizer photo slots, routing (`th_url()` map), and page/template assignments are all unchanged from v2.9.2.

## v2.9.4 — Icons replace dash bullets in About Us "5 Areas" grid
- The "Chi Siamo / About Us" page's five-area checklist (Operatività, Valorizzazione, Marketing, Supporto tecnico) previously used plain red dash bullets ("—") before each list item.
- Replaced with small red line-icons (17px, thin stroke, matching the site's terracotta red) paired with each item, in the same icon+text row style as the apartment amenities reference image: laundry/washing machine, wrench, chat bubble, document for area 01; sofa, camera, megaphone for area 02; globe, trend arrow, link for area 03; building, ruler, scale for area 04.
- New `th_area_icon($name)` helper in `page-about.php` returns an inline 24×24 stroke SVG per icon name — no new image assets needed, fully theme-colour-aware (`currentColor` via `--th-red`).
- `.th-area__list li` switched from `position:relative` + `::before` dash to a flex row (`display:flex;align-items:center;gap:10px`) housing the icon + text.
- No content or translation-string changes — only the visual marker before each item.

## v2.9.5 — Intro paragraph redesigned as an editorial standfirst
Replaced the centered oversized "manifesto" intro (`sv-intro`) used under every service/guest-page hero. Same DM Sans font and cream/crimson palette — only the layout of the intro paragraph changed:
- Was: centered, 800px, up to 24px, long centered lines (hard to scan, felt generic).
- Now: left-aligned standfirst on a 760px reading measure, indented from a single 2px crimson vertical rule that sits on the same left grid line as the section labels below it. Opening line auto-darkens via `::first-line` so it reads as a lead; the rest stays in mid-grey; `<strong>` key phrases in ink-dark for rhythm. One accent only (the crimson rule), everything else quiet.
- Applied globally through `services.css` (Services hub, Owners, Students, UniPD, Erasmus, Corporate). The About page uses the same class names but doesn't load services.css, so it got an identical self-contained `<style>` block for consistency.
- Responsive: rule padding and type step down on mobile.
No content, string, routing, or Customizer changes.

## v2.9.6 — Services hub "Per i Proprietari" fixed + icons on its points
- The hub's owner section was rendering stacked (button above a plain dashed list, right column empty). Root cause: the old `.sv-split` grid (`1.05fr 1fr`) could collapse. Rebuilt as a dedicated `.hub-owners` grid using `minmax(0, 1fr)` columns so it reliably shows two columns: left = description + navy button, right = the four key points.
- The four owner points now use the same small red line-icons as the Chi Siamo "5 Areas" section (icon + label, hairline dividers) instead of dash bullets: camera (annuncio/fotografia/home staging), trend (pricing dinamico), sparkle (accoglienza/pulizie/cura), scale (conformità legale).
- Moved the `th_area_icon()` line-icon helper out of `page-about.php` into `functions.php` (guarded with `function_exists`) so it's shared by both the About page and the Services hub; added three icons (sparkle, tag, key). About’s inline definition removed — no duplication, no redeclare risk.
- Removed the now-unused `.sv-split` CSS.
