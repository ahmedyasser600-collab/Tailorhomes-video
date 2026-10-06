(function() {
  'use strict';

  /* -----------------------------------------------
     HEADER — Scroll: transparent → solid
     ----------------------------------------------- */
  var header = document.getElementById('header');

  function onScroll() {
    if (!header) return;
    if (window.scrollY > 50) {
      header.classList.add('th-scrolled');
    } else {
      header.classList.remove('th-scrolled');
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();


  /* -----------------------------------------------
     MOBILE MENU
     ----------------------------------------------- */
  var burger = document.getElementById('th-burger');
  var mobileMenu = document.getElementById('th-mobile-menu');

  if (burger && mobileMenu) {
    burger.addEventListener('click', function() {
      burger.classList.toggle('open');
      mobileMenu.classList.toggle('open');
      document.body.classList.toggle('menu-open');
    });

    mobileMenu.querySelectorAll('a').forEach(function(link) {
      link.addEventListener('click', function() {
        burger.classList.remove('open');
        mobileMenu.classList.remove('open');
        document.body.classList.remove('menu-open');
      });
    });
  }


  /* -----------------------------------------------
     SCROLL REVEAL
     ----------------------------------------------- */
  var revealEls = document.querySelectorAll('.r, .rl, .rr');

  if (revealEls.length > 0 && 'IntersectionObserver' in window) {
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
        d.setAttribute('aria-label', 'Slide ' + (i + 1));
        d.addEventListener('click', function() { go(i); });
        dotsEl.appendChild(d);
      });
    }

    function go(n) {
      idx = (n + total) % total;
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
