/**
 * interagents.ai — Main JS
 * Navigation toggle, sticky header, scroll reveal, language toggle
 */

(function () {
  'use strict';

  var LANG = document.body.getAttribute('data-lang') || 'en';

  function t(pl, en) { return LANG === 'pl' ? pl : en; }

  // -- Scroll handling on page load --
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
  // Scroll to hash target if present, otherwise scroll to top
  if (window.location.hash) {
    var target = document.querySelector(window.location.hash);
    if (target) {
      setTimeout(function () {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 100);
    }
  } else {
    window.scrollTo(0, 0);
  }

  // Smooth scroll for hash links, including localized absolute homepage URLs.
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a[href]');
    if (!link) return;

    var url;
    try {
      url = new URL(link.href, window.location.href);
    } catch (err) {
      return;
    }

    if (!url.hash || url.hash.length < 2 || url.origin !== window.location.origin || url.pathname !== window.location.pathname) return;

    var target = document.getElementById(decodeURIComponent(url.hash.substring(1)));
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      history.pushState(null, '', url.pathname + url.search + url.hash);
    }
  });

  // -- Language toggle --
  var langBtn = document.getElementById('lang-toggle');
  if (langBtn) {
    langBtn.addEventListener('click', function () {
      var newLang = LANG === 'pl' ? 'en' : 'pl';
      document.cookie = 'ia_lang=' + newLang + ';path=/;max-age=31536000;SameSite=Lax;Secure';
      // Use full URL reload to bust any page cache
      var url = new URL(window.location.href);
      url.searchParams.set('lang', newLang);
      window.location.href = url.toString();
    });
  }

  // -- Sticky header on scroll --
  var header = document.querySelector('.site-header');
  var ticking = false;

  function onScroll() {
    if (!ticking) {
      requestAnimationFrame(function () {
        header.classList.toggle('is-scrolled', window.scrollY > 40);
        ticking = false;
      });
      ticking = true;
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // -- Mobile nav toggle --
  var nav = document.querySelector('.site-nav');
  var toggle = document.querySelector('.nav-toggle');
  var menu = document.querySelector('.site-nav .menu');

  if (toggle && nav) {
    var toggleLabel = toggle.querySelector('.nav-toggle__label');
    var desktopNavQuery = window.matchMedia('(min-width: 960px)');

    function setMobileNav(open) {
      nav.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', String(open));
      if (toggleLabel) toggleLabel.textContent = open ? t('Zamknij', 'Close') : t('Menu', 'Menu');

      if (menu) {
        if (desktopNavQuery.matches || open) {
          menu.removeAttribute('inert');
          menu.removeAttribute('aria-hidden');
        } else {
          menu.setAttribute('inert', '');
          menu.setAttribute('aria-hidden', 'true');
        }
      }
    }

    toggle.addEventListener('click', function () {
      setMobileNav(!nav.classList.contains('is-open'));
    });

    if (menu) {
      menu.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
          setMobileNav(false);
        });
      });
    }

    document.addEventListener('click', function (e) {
      if (!nav.contains(e.target)) {
        setMobileNav(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        setMobileNav(false);
        toggle.focus();
      }
    });

    if (typeof desktopNavQuery.addEventListener === 'function') {
      desktopNavQuery.addEventListener('change', function () { setMobileNav(false); });
    }
    setMobileNav(false);
  }

  // -- Accessible contact form modal --
  var modal = document.getElementById('contact-modal');
  var mobileBookingBar = document.querySelector('.mobile-booking-bar');
  var lastModalTrigger = null;
  var modalPageRegions = document.querySelectorAll('.site-header, main > section, .site-footer, .mobile-booking-bar');
  var focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

  function setPageInert(isInert) {
    modalPageRegions.forEach(function (region) {
      if (isInert) {
        region.setAttribute('inert', '');
      } else {
        region.removeAttribute('inert');
      }
    });
  }

  function openModal(trigger) {
    ga4('cta_click', { event_category: 'Engagement', event_label: 'Contact Modal Open', language: LANG });
    ga4('form_step', { form_step: 'modal_open', language: LANG });
    if (!modal) return;
    lastModalTrigger = trigger || document.activeElement;
    // Reset form state if previously submitted
    var formWrap = modal.querySelector('.contact-form-wrap');
    var titleEl = modal.querySelector('.modal-title');
    var subEl = modal.querySelector('.modal-subtitle');
    var success = modal.querySelector('.modal-success');
    if (formWrap) formWrap.style.display = '';
    if (titleEl) titleEl.style.display = '';
    if (subEl) subEl.style.display = '';
    if (success) success.classList.remove('is-visible');
    // Reset the form fields
    var form = modal.querySelector('.wpforms-form');
    if (form) form.reset();
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    if (mobileBookingBar) mobileBookingBar.classList.remove('is-visible');
    setPageInert(true);
    document.body.style.overflow = 'hidden';

    window.requestAnimationFrame(function () {
      var firstField = modal.querySelector('input:not([disabled]), textarea:not([disabled]), select:not([disabled])');
      var closeButton = modal.querySelector('.modal-close');
      (closeButton || firstField || modal.querySelector('.modal-content')).focus();
    });
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    setPageInert(false);
    document.body.style.overflow = '';
    updateMobileBookingBar();

    if (lastModalTrigger && typeof lastModalTrigger.focus === 'function') {
      lastModalTrigger.focus();
    }
    lastModalTrigger = null;
  }

  document.querySelectorAll('[data-open-contact-form]').forEach(function (button) {
    button.addEventListener('click', function () {
      openModal(button);
    });
  });

  // Offer-page code can use the same focus-managed dialog behavior.
  window.iaOpenContactModal = openModal;
  window.iaCloseContactModal = closeModal;

  if (modal) {
    var closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    var backdrop = modal.querySelector('.modal-backdrop');
    if (backdrop) backdrop.addEventListener('click', closeModal);

    modal.addEventListener('keydown', function (e) {
      if (!modal.classList.contains('is-open')) return;

      if (e.key === 'Escape') {
        e.preventDefault();
        closeModal();
        return;
      }

      if (e.key !== 'Tab') return;
      var focusable = Array.prototype.slice.call(modal.querySelectorAll(focusableSelector)).filter(function (element) {
        return element.offsetParent !== null;
      });
      if (!focusable.length) {
        e.preventDefault();
        modal.querySelector('.modal-content').focus();
        return;
      }

      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    });
  }

  // Show the mobile CTA after the hero, then get it out of the way while
  // the visitor is using the booking section.
  var bookingSectionVisible = false;
  function updateMobileBookingBar() {
    if (!mobileBookingBar) return;
    var modalOpen = modal && modal.classList.contains('is-open');
    mobileBookingBar.classList.toggle('is-visible', window.scrollY > 160 && !bookingSectionVisible && !modalOpen);
  }

  if (mobileBookingBar) {
    window.addEventListener('scroll', updateMobileBookingBar, { passive: true });
    var bookingSection = document.getElementById('book');
    if (bookingSection && 'IntersectionObserver' in window) {
      var bookingSectionObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          bookingSectionVisible = entry.isIntersecting;
          updateMobileBookingBar();
        });
      }, { threshold: 0.05 });
      bookingSectionObserver.observe(bookingSection);
    }
    updateMobileBookingBar();
  }

  function findFieldByLabel(text) {
    var labels = document.querySelectorAll('.wpforms-form .wpforms-field-label');
    for (var i = 0; i < labels.length; i++) {
      var lbl = labels[i];
      var txt = lbl.textContent.replace(/\s*\*/g, '').trim();
      if (txt === text) return lbl.closest('.wpforms-field');
    }
    return null;
  }

  // Hide ghost/empty fields
  document.querySelectorAll('.wpforms-form .wpforms-field').forEach(function (field) {
    var label = field.querySelector('.wpforms-field-label');
    var text = label ? label.textContent.replace(/\s*\*/g, '').trim() : '';
    if (!text || text === 'Single Line Text' || text === 'Paragraph Text') {
      field.style.display = 'none';
    }
  });

  // Name field: show "Imię" and "Nazwisko" as proper labels above each input
  var nameField = document.querySelector('.wpforms-field-name');
  if (nameField) {
    var mainLabel = nameField.querySelector('.wpforms-field-label');
    if (mainLabel) mainLabel.style.display = 'none';

    var sublabelMap = {
      'First': t('Imię', 'First name'),
      'Last': t('Nazwisko', 'Last name'),
      'Imię': t('Imię', 'First name'),
      'Nazwisko': t('Nazwisko', 'Last name')
    };

    nameField.querySelectorAll('.wpforms-field-row-block').forEach(function (block) {
      var sublabel = block.querySelector('.wpforms-field-sublabel');
      var input = block.querySelector('input');
      if (sublabel && input) {
        var rawText = sublabel.textContent.trim();
        var labelText = sublabelMap[rawText] || rawText;
        var label = document.createElement('label');
        label.className = 'wpforms-field-label wpforms-field-label--sub';
        label.textContent = labelText;
        label.setAttribute('for', input.id);
        if (mainLabel && mainLabel.querySelector('.wpforms-required-label')) {
          var req = document.createElement('span');
          req.className = 'wpforms-required-label';
          req.textContent = ' *';
          label.appendChild(req);
        }
        block.insertBefore(label, input);
      }
    });
  }

  // Translate WPForms labels for English
  if (LANG === 'en') {
    var labelMap = {
      'Imię i nazwisko': 'Full name',
      'Imię': 'First name',
      'Nazwisko': 'Last name',
      'Nazwa firmy': 'Company name',
      'Telefon': 'Phone',
      'E-mail': 'E-mail',
      'Wiadomość': 'Message'
    };
    document.querySelectorAll('.wpforms-form .wpforms-field-label').forEach(function (lbl) {
      var txt = lbl.childNodes[0];
      if (txt && txt.nodeType === 3) {
        var clean = txt.textContent.trim();
        if (labelMap[clean]) txt.textContent = labelMap[clean] + ' ';
      }
    });

    // Translate placeholders
    var placeholderMap = {
      'Wpisz swoją wiadomość...': 'Which workflow do you want to improve?'
    };
    document.querySelectorAll('.wpforms-form input, .wpforms-form textarea').forEach(function (el) {
      if (el.placeholder && placeholderMap[el.placeholder]) {
        el.placeholder = placeholderMap[el.placeholder];
      }
    });

    // Translate submit button
    var submitBtn = document.querySelector('.wpforms-form .wpforms-submit');
    if (submitBtn) {
      submitBtn.textContent = 'Send message';
      submitBtn.setAttribute('data-submit-text', 'Send message');
      submitBtn.setAttribute('data-alt-text', 'Sending...');
    }
  }

  // Keep the email alternative deliberately short. Only hide optional fields;
  // required WPForms fields remain visible so server-side validation still works.
  [t('Nazwa firmy', 'Company name'), t('Telefon', 'Phone')].forEach(function (label) {
    var field = findFieldByLabel(label);
    if (!field || field.querySelector('[required], [aria-required="true"], .wpforms-required-label')) return;

    field.classList.add('ia-field-hidden');
    field.querySelectorAll('input, select, textarea').forEach(function (control) {
      control.disabled = true;
    });
  });

  // -- Character counter on message textarea --
  var MAX_CHARS = 1000;
  var msgTextarea = document.querySelector('.wpforms-field-textarea textarea');
  if (msgTextarea) {
    msgTextarea.setAttribute('maxlength', MAX_CHARS);

    var counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.textContent = '0 / ' + MAX_CHARS;
    msgTextarea.parentElement.appendChild(counter);

    msgTextarea.addEventListener('input', function () {
      var len = msgTextarea.value.length;
      counter.textContent = len + ' / ' + MAX_CHARS;
      counter.classList.toggle('is-near', len > MAX_CHARS * 0.85);
      counter.classList.toggle('is-over', len >= MAX_CHARS);
    });
  }

  // -- Show success state after form submit --
  document.addEventListener('wpformsAjaxSubmitSuccess', function () {
    ga4('generate_lead', { event_category: 'Contact', event_label: 'Contact Form Submit', value: 1, language: LANG });
    ga4('form_step', { form_step: 'submitted', language: LANG });
    if (!modal) return;
    var formWrap = modal.querySelector('.contact-form-wrap');
    var titleEl = modal.querySelector('.modal-title');
    var subEl = modal.querySelector('.modal-subtitle');

    if (formWrap) formWrap.style.display = 'none';
    if (titleEl) titleEl.style.display = 'none';
    if (subEl) subEl.style.display = 'none';

    var success = modal.querySelector('.modal-success');
    if (!success) {
      success = document.createElement('div');
      success.className = 'modal-success';
      success.innerHTML =
        '<h4>' + t('Mamy Twój opis.', 'We have your workflow.') + '</h4>' +
        '<p>' + t(
          'Wrócimy z konkretnym następnym krokiem.',
          'We\'ll reply with a concrete next step.'
        ) + '</p>';
      modal.querySelector('.modal-content').appendChild(success);
    }
    success.classList.add('is-visible');

    setTimeout(function () {
      closeModal();
      setTimeout(function () {
        if (formWrap) formWrap.style.display = '';
        if (titleEl) titleEl.style.display = '';
        if (subEl) subEl.style.display = '';
        success.classList.remove('is-visible');
      }, 500);
    }, 4000);
  });

  // -- GA4 Enhanced Event Tracking --
  function ga4(event, params) {
    if (typeof gtag === 'function') gtag('event', event, params || {});
  }

  // Booking funnel events deliberately contain no names, email addresses,
  // selected dates, free-text answers, or other user-provided values.
  document.addEventListener('click', function (e) {
    var bookingCta = e.target.closest('[data-booking-cta]');
    if (!bookingCta) return;

    ga4('booking_cta_click', {
      placement: bookingCta.getAttribute('data-booking-cta') || 'unknown',
      language: LANG
    });
  });

  var calendar = document.querySelector('[data-booking-calendar]');
  if (calendar && 'IntersectionObserver' in window) {
    var calendarViewed = false;
    var calendarObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting && !calendarViewed) {
          calendarViewed = true;
          ga4('calendar_view', { language: LANG, source: 'homepage' });
          calendarObserver.disconnect();
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });
    calendarObserver.observe(calendar);
  }

  var bookingCompleteTracked = false;
  function trackBookingComplete() {
    if (bookingCompleteTracked) return;
    bookingCompleteTracked = true;
    ga4('booking_complete', { language: LANG, source: 'calendar' });
  }

  ['interagents:booking-complete', 'interagents_booking_complete', 'booking:complete'].forEach(function (eventName) {
    document.addEventListener(eventName, trackBookingComplete);
    window.addEventListener(eventName, trackBookingComplete);
  });

  window.addEventListener('message', function (event) {
    if (event.origin !== window.location.origin || !event.data || typeof event.data !== 'object') return;
    if (event.data.type === 'interagents:booking-complete' || event.data.type === 'booking_complete') {
      trackBookingComplete();
    }
  });

  // Track which sections users actually see
  var sectionNames = {
    'hero': 'Hero',
    'offer': 'Architecture',
    'book': 'Booking',
    'jak-dzialamy': 'How We Work',
    'dlaczego-my': 'Outcomes',
    'footer': 'Footer'
  };

  var trackedSections = {};
  var sectionEls = document.querySelectorAll('section[id], .site-footer');
  if ('IntersectionObserver' in window && sectionEls.length) {
    var sectionObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var id = entry.target.id || 'footer';
          if (!trackedSections[id]) {
            trackedSections[id] = true;
            ga4('section_view', {
              section_name: sectionNames[id] || id,
              language: LANG
            });
          }
        }
      });
    }, { threshold: 0.3 });
    sectionEls.forEach(function (el) { sectionObserver.observe(el); });
  }

  // Track scroll depth milestones (25%, 50%, 75%, 100%)
  var scrollMilestones = {};
  window.addEventListener('scroll', function () {
    var scrollPct = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
    [25, 50, 75, 100].forEach(function (m) {
      if (scrollPct >= m && !scrollMilestones[m]) {
        scrollMilestones[m] = true;
        ga4('scroll_depth', { percent: m, language: LANG });
      }
    });
  }, { passive: true });

  // Track time on page (30s, 60s, 120s, 300s)
  var timeMilestones = [30, 60, 120, 300];
  var timeIdx = 0;
  var pageTimer = setInterval(function () {
    if (timeIdx >= timeMilestones.length) { clearInterval(pageTimer); return; }
    ga4('time_on_page', { seconds: timeMilestones[timeIdx], language: LANG });
    timeIdx++;
  }, (timeMilestones[0]) * 1000);
  // Adjust interval for subsequent milestones
  var pageStart = Date.now();
  clearInterval(pageTimer);
  timeMilestones.forEach(function (sec) {
    setTimeout(function () {
      ga4('time_on_page', { seconds: sec, language: LANG });
    }, sec * 1000);
  });

  // Track outbound link clicks
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a[href]');
    if (link && link.hostname && link.hostname !== location.hostname) {
      ga4('outbound_click', { url: link.href, link_text: (link.textContent || '').trim().substring(0, 50) });
    }
  });

  // Track language toggle
  if (langBtn) {
    langBtn.addEventListener('click', function () {
      ga4('language_switch', { from_lang: LANG, to_lang: LANG === 'pl' ? 'en' : 'pl' });
    });
  }

  // Form funnel tracking
  var formTracked = { focus: false, started: false };
  document.addEventListener('focusin', function (e) {
    if (e.target.closest && e.target.closest('.wpforms-form') && !formTracked.focus) {
      formTracked.focus = true;
      ga4('form_step', { form_step: 'field_focus', language: LANG });
    }
  });
  document.addEventListener('input', function (e) {
    if (e.target.closest && e.target.closest('.wpforms-form') && !formTracked.started) {
      formTracked.started = true;
      ga4('form_step', { form_step: 'started_typing', language: LANG });
    }
  });

  // -- Scroll reveal with Intersection Observer --
  var reveals = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window && reveals.length) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-inview');
            observer.unobserve(entry.target);
          }
        });
      },
      {
        threshold: 0.12,
        rootMargin: '0px 0px -40px 0px',
      }
    );

    reveals.forEach(function (el) { observer.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-inview'); });
  }
})();
