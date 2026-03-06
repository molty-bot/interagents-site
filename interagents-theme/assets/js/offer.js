/**
 * InterAgents.ai — Offer Builder
 * Pure client-side configurator for OpenClaw pricing.
 * InterCore/Complete show "from" pricing with contact CTA.
 */
(function () {
  'use strict';

  var cfg = window.offerConfig;
  if (!cfg) return;

  var pricing = cfg.pricing;
  var currency = cfg.currency;
  var sep = cfg.thousandSep;
  var labels = cfg.labels;

  // State
  var state = {
    product: null,        // 'openclaw' | 'intercore' | 'complete'
    hosting: 'mac',       // 'mac' | 'vps' | 'mini'
    managed: 'managed',   // 'self' | 'managed'
    api: 'included'       // 'own' | 'included'
  };

  // DOM refs
  var productCards = document.querySelectorAll('.offer-product-card');
  var configurator = document.getElementById('offer-configurator');
  var intercoreInfo = document.getElementById('offer-intercore-info');
  var completeInfo = document.getElementById('offer-complete-info');
  var apiGroup = document.getElementById('offer-api-group');
  var quoteSetup = document.getElementById('quote-setup');
  var quoteMonthly = document.getElementById('quote-monthly');
  var quoteTotal = document.getElementById('quote-total');
  var includeApi = document.getElementById('offer-include-api');
  var includeManaged = document.getElementById('offer-include-managed');
  var toast = document.getElementById('offer-toast');

  // Format number with separator
  function fmt(n) {
    var s = String(Math.round(n));
    if (sep === ' ') {
      return s.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
    return s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  // Show toast message
  function showToast(msg) {
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(function () {
      toast.classList.remove('show');
    }, 2800);
  }

  // Read URL params into state
  function readUrlState() {
    var params = new URLSearchParams(window.location.search);
    if (params.has('product')) state.product = params.get('product');
    if (params.has('hosting')) state.hosting = params.get('hosting');
    if (params.has('managed')) state.managed = params.get('managed');
    if (params.has('api')) state.api = params.get('api');
  }

  // Write state to URL (replace, no reload)
  function writeUrlState() {
    if (!state.product) return;
    var params = new URLSearchParams();
    params.set('product', state.product);
    if (state.product === 'openclaw') {
      params.set('hosting', state.hosting);
      params.set('managed', state.managed);
      if (state.managed === 'managed') {
        params.set('api', state.api);
      }
    }
    var url = window.location.pathname + '?' + params.toString();
    window.history.replaceState(null, '', url);
  }

  // Calculate OpenClaw pricing
  function calcOpenClaw() {
    var setup = pricing.openclaw.install;
    var monthly = 0;

    if (state.managed === 'managed') {
      if (state.hosting === 'mac') monthly = pricing.openclaw.managed_mac;
      else if (state.hosting === 'vps') monthly = pricing.openclaw.managed_vps;
      else monthly = pricing.openclaw.managed_mini;

      if (state.api === 'own') {
        monthly -= pricing.openclaw.no_api_discount;
      }
    } else if (state.hosting === 'mini') {
      // Self-managed on Our Mac Mini still has a monthly rental fee
      monthly = pricing.openclaw.self_mini || 0;
    }

    return { setup: setup, monthly: monthly };
  }

  // Update the quote display
  function updateQuote() {
    if (state.product !== 'openclaw') return;

    var calc = calcOpenClaw();

    quoteSetup.textContent = fmt(calc.setup) + ' ' + currency;

    if (calc.monthly > 0) {
      quoteMonthly.textContent = fmt(calc.monthly) + ' ' + currency + labels.perMonth;
    } else {
      quoteMonthly.textContent = '0 ' + currency;
    }

    // Total
    if (calc.monthly > 0) {
      quoteTotal.textContent = fmt(calc.setup) + ' ' + currency + ' + ' + fmt(calc.monthly) + ' ' + currency + labels.perMonth;
    } else {
      quoteTotal.textContent = fmt(calc.setup) + ' ' + currency;
    }

    // Show/hide includes
    if (includeApi) {
      includeApi.style.display = (state.managed === 'managed' && state.api === 'included') ? 'list-item' : 'none';
    }
    if (includeManaged) {
      includeManaged.style.display = (state.managed === 'managed') ? 'list-item' : 'none';
    }
  }

  // Show/hide panels
  function showPanel() {
    // Hide all first
    configurator.style.display = 'none';
    configurator.setAttribute('aria-hidden', 'true');
    intercoreInfo.style.display = 'none';
    intercoreInfo.setAttribute('aria-hidden', 'true');
    completeInfo.style.display = 'none';
    completeInfo.setAttribute('aria-hidden', 'true');

    // Update product cards
    productCards.forEach(function (card) {
      var isActive = card.dataset.product === state.product;
      card.classList.toggle('active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    if (state.product === 'openclaw') {
      configurator.style.display = '';
      configurator.removeAttribute('aria-hidden');
      updateQuote();
    } else if (state.product === 'intercore') {
      intercoreInfo.style.display = '';
      intercoreInfo.removeAttribute('aria-hidden');
    } else if (state.product === 'complete') {
      completeInfo.style.display = '';
      completeInfo.removeAttribute('aria-hidden');
    }
  }

  // Update toggle visuals for a config group
  function updateToggles() {
    document.querySelectorAll('.offer-toggle-group').forEach(function (group) {
      var configKey = group.dataset.config;
      var current = state[configKey];
      group.querySelectorAll('.offer-toggle').forEach(function (btn) {
        btn.classList.toggle('active', btn.dataset.value === current);
      });
    });

    // Show/hide API group based on managed state
    if (apiGroup) {
      apiGroup.style.display = (state.managed === 'managed') ? '' : 'none';
    }
  }

  // Generate shareable URL
  function getShareUrl() {
    var params = new URLSearchParams();
    params.set('product', state.product);
    if (state.product === 'openclaw') {
      params.set('hosting', state.hosting);
      params.set('managed', state.managed);
      if (state.managed === 'managed') {
        params.set('api', state.api);
      }
    }
    return window.location.origin + window.location.pathname + '?' + params.toString();
  }

  // Generate email body
  function getEmailBody() {
    if (state.product !== 'openclaw') return '';
    var calc = calcOpenClaw();

    var hostingLabel = state.hosting === 'mac' ? labels.hostingMac
      : state.hosting === 'vps' ? labels.hostingVps
      : labels.hostingMini;

    var managedLabel = state.managed === 'self' ? labels.selfManaged : labels.managed;
    var apiLabel = state.api === 'included' ? labels.apiIncluded : labels.apiOwn;

    var lines = [
      labels.product + ' — ' + labels.setup + ': ' + fmt(calc.setup) + ' ' + currency,
      '',
      'Hosting: ' + hostingLabel,
      managedLabel
    ];

    if (state.managed === 'managed') {
      lines.push('API: ' + apiLabel);
      lines.push('');
      lines.push(labels.monthly + ': ' + fmt(calc.monthly) + ' ' + currency + labels.perMonth);
    }

    lines.push('');
    lines.push(getShareUrl());

    return lines.join('\n');
  }

  // Generate inquiry text for contact form
  function getInquiryText() {
    if (state.product === 'intercore') {
      return 'InterCore Platform — ' + (cfg.lang === 'pl' ? 'Zapytanie o wycenę' : 'Quote request');
    }
    if (state.product === 'complete') {
      return 'Complete Package — ' + (cfg.lang === 'pl' ? 'Zapytanie o wycenę' : 'Quote request');
    }

    var calc = calcOpenClaw();
    var hostingLabel = state.hosting === 'mac' ? labels.hostingMac
      : state.hosting === 'vps' ? labels.hostingVps
      : labels.hostingMini;

    var parts = ['OpenClaw', hostingLabel];
    if (state.managed === 'managed') {
      parts.push(state.api === 'included' ? 'API included' : 'Own API');
      parts.push(fmt(calc.monthly) + ' ' + currency + labels.perMonth);
    } else {
      parts.push('Self-managed');
    }
    parts.push(labels.setup + ': ' + fmt(calc.setup) + ' ' + currency);

    return parts.join(' | ');
  }

  // Event: Product card click
  productCards.forEach(function (card) {
    card.addEventListener('click', function () {
      state.product = card.dataset.product;
      showPanel();
      writeUrlState();

      // Smooth scroll to panel
      var target = state.product === 'openclaw' ? configurator
        : state.product === 'intercore' ? intercoreInfo
        : completeInfo;
      if (target) {
        setTimeout(function () {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 50);
      }
    });

    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        card.click();
      }
    });
  });

  // Event: Toggle buttons
  document.querySelectorAll('.offer-toggle-group').forEach(function (group) {
    group.querySelectorAll('.offer-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var configKey = group.dataset.config;
        state[configKey] = btn.dataset.value;

        // If switching to self-managed, reset API
        if (configKey === 'managed' && btn.dataset.value === 'self') {
          state.api = 'own';
        }
        // If switching to managed, default API to included
        if (configKey === 'managed' && btn.dataset.value === 'managed') {
          state.api = 'included';
        }

        updateToggles();
        updateQuote();
        writeUrlState();
      });
    });
  });

  // Event: Share link
  var shareBtn = document.getElementById('offer-share');
  if (shareBtn) {
    shareBtn.addEventListener('click', function () {
      var url = getShareUrl();
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function () {
          showToast(labels.copied);
        });
      } else {
        // Fallback
        var input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast(labels.copied);
      }
    });
  }

  // Event: Email quote
  var emailBtn = document.getElementById('offer-email');
  if (emailBtn) {
    emailBtn.addEventListener('click', function () {
      var subject = encodeURIComponent(labels.emailSubject);
      var body = encodeURIComponent(getEmailBody());
      window.location.href = 'mailto:?subject=' + subject + '&body=' + body;
    });
  }

  // Modal open/close logic (self-contained for offer page)
  function openContactModal() {
    var modal = document.getElementById('contact-modal');
    if (!modal) return;
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeContactModal() {
    var modal = document.getElementById('contact-modal');
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // Wire up modal close buttons
  var modalBackdrop = document.querySelector('#contact-modal .modal-backdrop');
  var modalClose = document.querySelector('#contact-modal .modal-close');
  if (modalBackdrop) modalBackdrop.addEventListener('click', closeContactModal);
  if (modalClose) modalClose.addEventListener('click', closeContactModal);
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeContactModal();
  });

  // Event: Inquire (opens contact modal with pre-filled text)
  function handleInquire() {
    var text = getInquiryText();

    // Open modal directly
    openContactModal();

    // Try to fill WPForms message field after modal opens
    setTimeout(function () {
      var textarea = document.querySelector('#contact-modal textarea, .wpforms-field-textarea textarea');
      if (textarea) {
        textarea.value = text + '\n\n' + getShareUrl() + '\n\n';
        textarea.focus();
        // Trigger input event for any char counters
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }, 300);
  }

  var inquireBtn = document.getElementById('offer-inquire');
  if (inquireBtn) inquireBtn.addEventListener('click', handleInquire);

  var icInquireBtn = document.getElementById('intercore-inquire');
  if (icInquireBtn) icInquireBtn.addEventListener('click', handleInquire);

  var cmpInquireBtn = document.getElementById('complete-inquire');
  if (cmpInquireBtn) cmpInquireBtn.addEventListener('click', handleInquire);

  // Init: read URL state and render
  readUrlState();
  // Default to OpenClaw if no product specified (avoids empty space)
  if (!state.product) {
    state.product = 'openclaw';
  }
  updateToggles();
  showPanel();
})();
