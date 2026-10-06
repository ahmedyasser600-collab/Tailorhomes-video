(function() {
  'use strict';

  /* -----------------------------------------------
     HEADER — Scroll: transparent → solid
     ----------------------------------------------- */
  var header = document.getElementById('header');
  var scrolled = null;
  var ticking = false;

  // Only touch the class when the state actually flips, once per frame.
  function onScroll() {
    if (!header) return;
    var next = window.scrollY > 50;
    if (next !== scrolled) {
      scrolled = next;
      header.classList.toggle('th-scrolled', next);
    }
    ticking = false;
  }

  window.addEventListener('scroll', function() {
    if (!ticking) { ticking = true; window.requestAnimationFrame(onScroll); }
  }, { passive: true });
  // Read scroll position on the next frame, not during script execution (avoids a forced reflow).
  window.requestAnimationFrame(onScroll);


  /* -----------------------------------------------
     MOBILE MENU
     ----------------------------------------------- */
  var burger = document.getElementById('th-burger');
  var mobileMenu = document.getElementById('th-mobile-menu');

  if (burger && mobileMenu) {
    var openLabel = burger.getAttribute('aria-label');
    var closeLabel = document.documentElement.lang.indexOf('it') === 0 ? 'Chiudi menu' : 'Close menu';

    function setMenu(open) {
      burger.classList.toggle('open', open);
      mobileMenu.classList.toggle('open', open);
      document.body.classList.toggle('menu-open', open);
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      burger.setAttribute('aria-label', open ? closeLabel : openLabel);
      mobileMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
      if (open) {
        mobileMenu.removeAttribute('inert');
        var first = mobileMenu.querySelector('a');
        if (first) first.focus();
      } else {
        mobileMenu.setAttribute('inert', '');
      }
    }

    burger.addEventListener('click', function() {
      setMenu(!mobileMenu.classList.contains('open'));
    });

    mobileMenu.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function() { setMenu(false); });
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
        setMenu(false);
        burger.focus();
      }
    });
  }


  /* -----------------------------------------------
     LANGUAGE SWITCHER — tap to open on touch screens
     ----------------------------------------------- */
  var langSwitcher = document.querySelector('.th-lang-switcher:not(.th-lang-switcher--mobile)');
  var langBtn = langSwitcher && langSwitcher.querySelector('.th-lang-current');

  if (langBtn) {
    function setLang(open) {
      langSwitcher.classList.toggle('open', open);
      langBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    langBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      setLang(!langSwitcher.classList.contains('open'));
    });
    document.addEventListener('click', function(e) {
      if (!langSwitcher.contains(e.target)) setLang(false);
    });
    langSwitcher.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') { setLang(false); langBtn.focus(); }
    });
  }


  /* -----------------------------------------------
     SCROLL REVEAL
     ----------------------------------------------- */
  var revealEls = document.querySelectorAll('.r, .rl, .rr');

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (revealEls.length > 0 && (reduceMotion || !('IntersectionObserver' in window))) {
    revealEls.forEach(function(el) { el.classList.add('in'); });
  } else if (revealEls.length > 0) {
    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    revealEls.forEach(function(el) { observer.observe(el); });
  }


  /* -----------------------------------------------
     IMAGE SLIDERS
     ----------------------------------------------- */
  document.querySelectorAll('[data-slider]').forEach(function(slider) {
    var slides = slider.querySelectorAll('.apt-slide');
    var track = slider.querySelector('.apt-slides');
    var prev = slider.querySelector('.apt-arrow--prev');
    var next = slider.querySelector('.apt-arrow--next');
    var dotsEl = slider.querySelector('.apt-dots');
    var cur = slider.querySelector('.cur');
    var tot = slider.querySelector('.tot');
    var idx = 0, total = slides.length;

    if (!track || total === 0) return;
    if (tot) tot.textContent = total;

    if (dotsEl) {
      slides.forEach(function(_, i) {
        var d = document.createElement('button');
        d.className = 'apt-dot' + (i === 0 ? ' active' : '');
        d.type = 'button';
        d.setAttribute('aria-label', 'Slide ' + (i + 1));
        d.addEventListener('click', function() { go(i); });
        dotsEl.appendChild(d);
      });
    }

    function go(n) {
      idx = (n + total) % total;
      // Slides after the first are lazy; load the target before it slides in.
      var img = slides[idx].querySelector('img[loading="lazy"]');
      if (img) img.loading = 'eager';
      track.style.transform = 'translateX(-' + idx * 100 + '%)';
      if (cur) cur.textContent = idx + 1;
      if (dotsEl) dotsEl.querySelectorAll('.apt-dot').forEach(function(d, i) { d.classList.toggle('active', i === idx); });
    }

    if (prev) prev.addEventListener('click', function() { go(idx - 1); });
    if (next) next.addEventListener('click', function() { go(idx + 1); });

    var tx = 0;
    slider.addEventListener('touchstart', function(e) { tx = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', function(e) { var dx = tx - e.changedTouches[0].clientX; if (Math.abs(dx) > 40) dx > 0 ? go(idx + 1) : go(idx - 1); }, { passive: true });
  });


  /* -----------------------------------------------
     COOKIE BANNER (Complianz) — hide empty document links
     An unconfigured policy renders <a class="cmplz-link"> with no text that just points
     to the home page: invisible, but screen readers and AI agents hit an unnamed link.
     ----------------------------------------------- */
  function fixEmptyBannerLinks() {
    document.querySelectorAll('.cmplz-documents a, a.cmplz-link').forEach(function(a) {
      if (a.textContent.trim() === '' && !a.getAttribute('aria-label')) {
        var item = a.closest('li') || a;
        item.hidden = true;
        a.setAttribute('tabindex', '-1');
        a.setAttribute('aria-hidden', 'true');
      }
    });
  }
  fixEmptyBannerLinks();
  // The banner can be re-rendered after load (e.g. on consent change).
  if ('MutationObserver' in window) {
    var cmplzTimer;
    new MutationObserver(function() {
      clearTimeout(cmplzTimer);
      cmplzTimer = setTimeout(fixEmptyBannerLinks, 100);
    }).observe(document.body, { childList: true, subtree: true });
  }


  /* -----------------------------------------------
     FAQ ACCORDION
     ----------------------------------------------- */
  function initFAQ() {
    document.querySelectorAll('.sv-faq-q').forEach(function(q) {
      q.addEventListener('click', function(e) {
        e.stopPropagation();
        this.closest('.sv-faq-item').classList.toggle('open');
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFAQ);
  } else {
    initFAQ();
  }

})();
