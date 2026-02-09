/**
 * InterAgents.ai — Main JS
 * Navigation toggle, sticky header, scroll reveal, language toggle
 */

(function () {
  'use strict';

  var LANG = document.body.getAttribute('data-lang') || 'en';

  function t(pl, en) { return LANG === 'pl' ? pl : en; }

  // -- Scroll to top on page load (prevent browser restoring scroll position) --
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
  window.scrollTo(0, 0);

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
    toggle.addEventListener('click', function () {
      var isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
    });

    menu.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });

    document.addEventListener('click', function (e) {
      if (!nav.contains(e.target)) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // -- Contact form modal --
  var modal = document.getElementById('contact-modal');
  var openBtn = document.getElementById('open-contact-form');
  var heroCTA = document.querySelector('a.btn--primary[href="#kontakt"]');

  function openModal() {
    if (!modal) return;
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
    document.body.style.overflow = 'hidden';
  }

  function closeModal() {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (openBtn) openBtn.addEventListener('click', openModal);
  if (heroCTA) {
    heroCTA.addEventListener('click', function (e) {
      e.preventDefault();
      openModal();
    });
  }

  if (modal) {
    var closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    var backdrop = modal.querySelector('.modal-backdrop');
    if (backdrop) backdrop.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
  }

  // -- Fix WPForms layout: pair fields side by side --
  function pairFieldEls(f1, f2) {
    if (!f1 || !f2) return;
    var wrapper = document.createElement('div');
    wrapper.className = 'form-row-pair';
    f1.parentElement.insertBefore(wrapper, f1);
    wrapper.appendChild(f1);
    wrapper.appendChild(f2);
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
      'Wpisz swoją wiadomość...': 'Type your message...'
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

  // Telefon + E-mail side by side
  pairFieldEls(findFieldByLabel(t('Telefon', 'Phone')), findFieldByLabel('E-mail'));

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
        '<div class="success-icon">✓</div>' +
        '<h4>' + t('Dziękujemy za wiadomość!', 'Thank you for reaching out!') + '</h4>' +
        '<p>' + t(
          'Otrzymaliśmy Twoją wiadomość i odezwiemy się najszybciej jak to możliwe.',
          'We\'ve received your message and will get back to you shortly.'
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
