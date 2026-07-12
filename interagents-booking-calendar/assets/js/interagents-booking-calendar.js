(function () {
  'use strict';

  function parseConfig(widget) {
    var node = widget.querySelector('.iabc-booking__config');
    if (!node) return null;
    try {
      return JSON.parse(node.textContent || '{}');
    } catch (error) {
      return null;
    }
  }

  function statusElement(widget, area) {
    return widget.querySelector('.iabc-booking__status--' + area);
  }

  function setStatus(widget, area, message, isError) {
    var status = statusElement(widget, area);
    if (!status) return;
    status.textContent = message || '';
    status.classList.toggle('is-error', Boolean(isError));
    status.classList.toggle('is-visible', Boolean(message));
  }

  function focusStatus(widget, area) {
    var status = statusElement(widget, area);
    if (status) status.focus();
  }

  function clearSlots(widget) {
    var slots = widget.querySelector('.iabc-booking__slots');
    if (slots) slots.replaceChildren();
    var start = widget.querySelector('input[name="start"]');
    if (start) start.value = '';
  }

  async function loadSlots(widget, config) {
    var date = widget.querySelector('.iabc-booking__date');
    var slots = widget.querySelector('.iabc-booking__slots');
    if (!date || !slots || !date.value) return;

    var requestedDate = date.value;
    var requestId = (widget._iabcSlotsRequestId || 0) + 1;
    widget._iabcSlotsRequestId = requestId;

    clearSlots(widget);
    slots.setAttribute('aria-busy', 'true');
    setStatus(widget, 'slots', config.strings.loading, false);

    try {
      var url = new URL(config.slotsUrl, window.location.href);
      url.searchParams.set('date', requestedDate);
      url.searchParams.set('lang', config.lang);
      var response = await fetch(url.toString(), { credentials: 'same-origin', cache: 'no-store' });
      var data = await response.json().catch(function () { return {}; });
      if (widget._iabcSlotsRequestId !== requestId || date.value !== requestedDate) return;
      if (!response.ok) throw new Error(data.message || config.strings.loadError);

      if (data.booking_nonce) config.nonce = String(data.booking_nonce);

      var available = Array.isArray(data.slots) ? data.slots : [];
      if (!available.length) {
        setStatus(widget, 'slots', config.strings.noSlots, false);
        return;
      }

      setStatus(widget, 'slots', '', false);
      available.forEach(function (slot) {
        var button = document.createElement('button');
        var fullLabel = String(slot.label || '');
        var startLabel = fullLabel.split('–')[0] || fullLabel;
        var interval = document.createElement('span');
        var start = document.createElement('span');
        button.type = 'button';
        button.className = 'iabc-booking__slot';
        button.dataset.start = String(slot.start || '');
        button.dataset.label = fullLabel;
        button.setAttribute('aria-pressed', 'false');
        button.setAttribute('aria-label', fullLabel);
        interval.className = 'iabc-booking__slot-interval';
        interval.textContent = fullLabel;
        start.className = 'iabc-booking__slot-start';
        start.textContent = startLabel;
        start.setAttribute('aria-hidden', 'true');
        button.appendChild(interval);
        button.appendChild(start);
        button.addEventListener('click', function () {
          slots.querySelectorAll('.iabc-booking__slot').forEach(function (candidate) {
            candidate.classList.remove('is-selected');
            candidate.setAttribute('aria-pressed', 'false');
          });
          button.classList.add('is-selected');
          button.setAttribute('aria-pressed', 'true');
          widget.querySelector('input[name="start"]').value = button.dataset.start;
          setStatus(widget, 'slots', config.strings.slotSelected + ': ' + button.dataset.label, false);
          setStatus(widget, 'form', '', false);
        });
        slots.appendChild(button);
      });
    } catch (error) {
      if (widget._iabcSlotsRequestId !== requestId || date.value !== requestedDate) return;
      setStatus(widget, 'slots', error.message || config.strings.loadError, true);
    } finally {
      if (widget._iabcSlotsRequestId === requestId) slots.removeAttribute('aria-busy');
    }
  }

  async function submitBooking(event, widget, config) {
    event.preventDefault();
    var form = event.currentTarget;
    var start = form.querySelector('input[name="start"]');

    if (!start || !start.value) {
      setStatus(widget, 'slots', config.strings.chooseSlot, true);
      focusStatus(widget, 'slots');
      return;
    }
    if (!form.checkValidity()) {
      form.reportValidity();
      setStatus(widget, 'form', config.strings.invalidFields, true);
      return;
    }

    var submit = form.querySelector('.iabc-booking__submit');
    var formData = new FormData(form);
    var payload = Object.fromEntries(formData.entries());
    payload.privacy_acknowledged = formData.has('privacy_acknowledged');
    payload.nonce = config.nonce;

    submit.disabled = true;
    form.setAttribute('aria-busy', 'true');
    setStatus(widget, 'form', config.strings.submitting, false);

    try {
      var response = await fetch(config.bookUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      var data = await response.json().catch(function () { return {}; });
      if (!response.ok) {
        var requestError = new Error(data.message || config.strings.genericError);
        requestError.status = response.status;
        throw requestError;
      }

      var grid = widget.querySelector('.iabc-booking__grid');
      var success = widget.querySelector('.iabc-booking__success');
      var when = widget.querySelector('.iabc-booking__success-when');
      var ics = widget.querySelector('.iabc-booking__ics');
      if (grid) grid.hidden = true;
      setStatus(widget, 'slots', '', false);
      setStatus(widget, 'form', '', false);
      if (when) when.textContent = data.when || '';
      if (ics) ics.href = data.ics_url || '#';
      if (success) {
        success.hidden = false;
        success.focus();
      }

      document.dispatchEvent(new CustomEvent('interagents:booking-complete', { detail: { source: 'calendar' } }));
    } catch (error) {
      var message = error.message || config.strings.genericError;
      submit.disabled = false;
      if (error.status === 409) {
        await loadSlots(widget, config);
        setStatus(widget, 'slots', message, true);
        focusStatus(widget, 'slots');
      } else {
        setStatus(widget, 'form', message, true);
        focusStatus(widget, 'form');
      }
    } finally {
      form.removeAttribute('aria-busy');
    }
  }

  function init(widget) {
    var config = parseConfig(widget);
    if (!config) return;
    var date = widget.querySelector('.iabc-booking__date');
    var form = widget.querySelector('.iabc-booking__form');
    if (!date || !form) return;

    date.min = config.minDate;
    date.max = config.maxDate;
    date.value = config.suggestedDate || config.minDate;
    date.addEventListener('change', function () { loadSlots(widget, config); });
    form.addEventListener('submit', function (event) { submitBooking(event, widget, config); });
    loadSlots(widget, config);
  }

  document.querySelectorAll('[data-iabc-booking]').forEach(init);
}());
