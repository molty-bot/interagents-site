/**
 * InterAgents.ai — Main JS
 * Navigation toggle, sticky header, scroll reveal
 */

(function () {
  'use strict';

  // -- Sticky header on scroll --
  const header = document.querySelector('.site-header');
  let ticking = false;

  function onScroll() {
    if (!ticking) {
      requestAnimationFrame(() => {
        header.classList.toggle('is-scrolled', window.scrollY > 40);
        ticking = false;
      });
      ticking = true;
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // -- Mobile nav toggle --
  const nav = document.querySelector('.site-nav');
  const toggle = document.querySelector('.nav-toggle');
  const menu = document.querySelector('.site-nav .menu');

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
    });

    // Close on link click
    menu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target)) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // -- Contact form modal --
  const modal = document.getElementById('contact-modal');
  const openBtn = document.getElementById('open-contact-form');
  // Also catch hero CTA that links to #kontakt
  const heroCTA = document.querySelector('a.btn--primary[href="#kontakt"]');

  function openModal() {
    if (!modal) return;
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
    heroCTA.addEventListener('click', (e) => {
      e.preventDefault();
      openModal();
    });
  }

  if (modal) {
    // Close button
    const closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    // Backdrop click
    const backdrop = modal.querySelector('.modal-backdrop');
    if (backdrop) backdrop.addEventListener('click', closeModal);

    // Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
  }

  // -- Fix WPForms layout: pair fields side by side --
  function pairFieldEls(f1, f2) {
    if (!f1 || !f2) return;
    const wrapper = document.createElement('div');
    wrapper.className = 'form-row-pair';
    f1.parentElement.insertBefore(wrapper, f1);
    wrapper.appendChild(f1);
    wrapper.appendChild(f2);
  }

  // Find fields by label text
  function findFieldByLabel(text) {
    const labels = document.querySelectorAll('.wpforms-form .wpforms-field-label');
    for (const label of labels) {
      const t = label.textContent.replace(/\s*\*/g, '').trim();
      if (t === text) return label.closest('.wpforms-field');
    }
    return null;
  }

  // Hide ghost/empty fields (fields with no visible label)
  document.querySelectorAll('.wpforms-form .wpforms-field').forEach((field) => {
    const label = field.querySelector('.wpforms-field-label');
    const text = label ? label.textContent.replace(/\s*\*/g, '').trim() : '';
    if (!text || text === 'Single Line Text' || text === 'Paragraph Text') {
      field.style.display = 'none';
    }
  });

  // Imię + Nazwisko side by side
  pairFieldEls(findFieldByLabel('Imię'), findFieldByLabel('Nazwisko'));
  // Telefon + E-mail side by side
  pairFieldEls(findFieldByLabel('Telefon'), findFieldByLabel('E-mail'));

  // -- Character counter on message textarea --
  const MAX_CHARS = 1000;
  const msgTextarea = document.querySelector('.wpforms-field-textarea textarea');
  if (msgTextarea) {
    msgTextarea.setAttribute('maxlength', MAX_CHARS);

    const counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.textContent = '0 / ' + MAX_CHARS;
    msgTextarea.parentElement.appendChild(counter);

    msgTextarea.addEventListener('input', () => {
      const len = msgTextarea.value.length;
      counter.textContent = len + ' / ' + MAX_CHARS;
      counter.classList.toggle('is-near', len > MAX_CHARS * 0.85);
      counter.classList.toggle('is-over', len >= MAX_CHARS);
    });
  }

  // -- Show success state after form submit --
  // WPForms fires 'wpformsAjaxSubmitSuccess' on the document
  document.addEventListener('wpformsAjaxSubmitSuccess', () => {
    if (!modal) return;
    const formWrap = modal.querySelector('.contact-form-wrap');
    const titleEl = modal.querySelector('.modal-title');
    const subEl = modal.querySelector('.modal-subtitle');

    if (formWrap) formWrap.style.display = 'none';
    if (titleEl) titleEl.style.display = 'none';
    if (subEl) subEl.style.display = 'none';

    // Create success message
    let success = modal.querySelector('.modal-success');
    if (!success) {
      success = document.createElement('div');
      success.className = 'modal-success';
      success.innerHTML =
        '<div class="success-icon">✓</div>' +
        '<h4>Dziękujemy!</h4>' +
        '<p>Twoja wiadomość została wysłana.<br>Skontaktujemy się z Tobą najszybciej jak to możliwe.</p>';
      modal.querySelector('.modal-content').appendChild(success);
    }
    success.classList.add('is-visible');

    // Auto-close after 4 seconds
    setTimeout(() => {
      closeModal();
      // Reset after close animation
      setTimeout(() => {
        if (formWrap) formWrap.style.display = '';
        if (titleEl) titleEl.style.display = '';
        if (subEl) subEl.style.display = '';
        success.classList.remove('is-visible');
      }, 500);
    }, 4000);
  });

  // -- Scroll reveal with Intersection Observer --
  const reveals = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window && reveals.length) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
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

    reveals.forEach((el) => observer.observe(el));
  } else {
    // Fallback: show everything
    reveals.forEach((el) => el.classList.add('is-inview'));
  }
})();
