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

  function parseIsoDate(value) {
    var match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || ''));
    if (!match) return null;
    var year = Number(match[1]);
    var month = Number(match[2]) - 1;
    var day = Number(match[3]);
    var date = new Date(year, month, day, 12, 0, 0, 0);
    if (date.getFullYear() !== year || date.getMonth() !== month || date.getDate() !== day) return null;
    return date;
  }

  function isoDate(date) {
    return [
      String(date.getFullYear()).padStart(4, '0'),
      String(date.getMonth() + 1).padStart(2, '0'),
      String(date.getDate()).padStart(2, '0')
    ].join('-');
  }

  function addDays(date, amount) {
    var next = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 12, 0, 0, 0);
    next.setDate(next.getDate() + amount);
    return next;
  }

  function addMonths(date, amount) {
    var targetMonth = new Date(date.getFullYear(), date.getMonth() + amount, 1, 12, 0, 0, 0);
    var lastDay = new Date(targetMonth.getFullYear(), targetMonth.getMonth() + 1, 0, 12, 0, 0, 0).getDate();
    targetMonth.setDate(Math.min(date.getDate(), lastDay));
    return targetMonth;
  }

  function startOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1, 12, 0, 0, 0);
  }

  function monthKey(date) {
    return (date.getFullYear() * 12) + date.getMonth();
  }

  function workingDays(config) {
    var configured = Array.isArray(config.weekdays) ? config.weekdays.map(Number) : [];
    configured = configured.filter(function (day) { return day >= 1 && day <= 7; });
    return configured.length ? configured : [1, 2, 3, 4, 5];
  }

  function isSelectableDate(date, config) {
    var value = isoDate(date);
    var isoWeekday = date.getDay() === 0 ? 7 : date.getDay();
    return value >= config.minDate && value <= config.maxDate && workingDays(config).indexOf(isoWeekday) !== -1;
  }

  function closestSelectableDate(date, direction, config) {
    var candidate = date;
    var step = direction < 0 ? -1 : 1;
    for (var index = 0; index < 370; index += 1) {
      var value = isoDate(candidate);
      if ((value < config.minDate && step < 0) || (value > config.maxDate && step > 0)) return null;
      if (isSelectableDate(candidate, config)) return candidate;
      candidate = addDays(candidate, step);
    }
    return null;
  }

  function calendarLocale(config) {
    return config.lang === 'pl' ? 'pl-PL' : 'en-GB';
  }

  function focusCalendarDate(widget, date) {
    var calendar = widget._iabcCalendar;
    if (!calendar) return;
    calendar.activeMonth = startOfMonth(date);
    renderCalendar(widget, calendar.config);
    var button = calendar.body.querySelector('[data-iabc-date="' + isoDate(date) + '"]');
    if (button) {
      calendar.body.querySelectorAll('.iabc-booking__calendar-day').forEach(function (candidate) {
        candidate.tabIndex = -1;
      });
      button.tabIndex = 0;
      button.focus();
    }
  }

  function handleCalendarKeydown(event, widget, config, currentDate) {
    var target = null;
    var direction = 1;
    var mondayIndex = (currentDate.getDay() + 6) % 7;

    if (event.key === 'ArrowLeft') {
      target = addDays(currentDate, -1);
      direction = -1;
    } else if (event.key === 'ArrowRight') {
      target = addDays(currentDate, 1);
    } else if (event.key === 'ArrowUp') {
      target = addDays(currentDate, -7);
      direction = -1;
    } else if (event.key === 'ArrowDown') {
      target = addDays(currentDate, 7);
    } else if (event.key === 'Home') {
      target = addDays(currentDate, -mondayIndex);
    } else if (event.key === 'End') {
      target = addDays(currentDate, 6 - mondayIndex);
      direction = -1;
    } else if (event.key === 'PageUp') {
      target = addMonths(currentDate, -1);
      direction = -1;
    } else if (event.key === 'PageDown') {
      target = addMonths(currentDate, 1);
    } else {
      return;
    }

    event.preventDefault();
    target = closestSelectableDate(target, direction, config);
    if (target) focusCalendarDate(widget, target);
  }

  function renderCalendar(widget, config) {
    var calendar = widget._iabcCalendar;
    var selected = parseIsoDate(calendar.date.value) || parseIsoDate(config.minDate);
    var today = new Date();
    var locale = calendarLocale(config);
    var monthLabel = new Intl.DateTimeFormat(locale, { month: 'long', year: 'numeric' }).format(calendar.activeMonth);
    monthLabel = monthLabel.charAt(0).toLocaleUpperCase(locale) + monthLabel.slice(1);
    calendar.title.textContent = monthLabel;

    var minimumMonth = startOfMonth(parseIsoDate(config.minDate));
    var maximumMonth = startOfMonth(parseIsoDate(config.maxDate));
    calendar.previous.disabled = monthKey(calendar.activeMonth) <= monthKey(minimumMonth);
    calendar.next.disabled = monthKey(calendar.activeMonth) >= monthKey(maximumMonth);

    calendar.body.replaceChildren();
    var firstDisplayed = addDays(calendar.activeMonth, -((calendar.activeMonth.getDay() + 6) % 7));
    var firstFocusable = null;
    var selectedButton = null;

    for (var week = 0; week < 6; week += 1) {
      var row = document.createElement('tr');
      for (var weekday = 0; weekday < 7; weekday += 1) {
        var current = addDays(firstDisplayed, (week * 7) + weekday);
        var value = isoDate(current);
        var cell = document.createElement('td');
        var button = document.createElement('button');
        var isSelected = selected && value === isoDate(selected);
        var isToday = value === isoDate(today);
        var isOutsideMonth = current.getMonth() !== calendar.activeMonth.getMonth();
        var selectable = isSelectableDate(current, config);

        button.type = 'button';
        button.className = 'iabc-booking__calendar-day';
        button.textContent = String(current.getDate());
        button.dataset.iabcDate = value;
        button.disabled = !selectable;
        button.tabIndex = -1;
        button.setAttribute('aria-label', new Intl.DateTimeFormat(locale, {
          weekday: 'long',
          day: 'numeric',
          month: 'long',
          year: 'numeric'
        }).format(current));
        button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
        button.classList.toggle('is-selected', Boolean(isSelected));
        button.classList.toggle('is-today', isToday);
        button.classList.toggle('is-outside-month', isOutsideMonth);
        if (isToday) button.setAttribute('aria-current', 'date');

        if (selectable && !firstFocusable && !isOutsideMonth) firstFocusable = button;
        if (selectable && isSelected) selectedButton = button;

        button.addEventListener('click', function (clickEvent) {
          calendar.date.value = clickEvent.currentTarget.dataset.iabcDate;
          calendar.date.dispatchEvent(new Event('change', { bubbles: true }));
        });
        button.addEventListener('keydown', function (keyEvent) {
          var focusedDate = parseIsoDate(keyEvent.currentTarget.dataset.iabcDate);
          if (focusedDate) handleCalendarKeydown(keyEvent, widget, config, focusedDate);
        });

        cell.appendChild(button);
        row.appendChild(cell);
      }
      calendar.body.appendChild(row);
    }

    var tabStop = selectedButton || firstFocusable || calendar.body.querySelector('.iabc-booking__calendar-day:not(:disabled)');
    if (tabStop) tabStop.tabIndex = 0;
  }

  function buildCalendar(widget, config, date) {
    var mount = widget.querySelector('[data-iabc-calendar]');
    var fallback = widget.querySelector('[data-iabc-date-fallback]');
    if (!mount || !fallback || !window.Intl) return;

    var locale = calendarLocale(config);
    var titleId = widget.id + '-calendar-month';
    var navigation = document.createElement('div');
    var previous = document.createElement('button');
    var title = document.createElement('h4');
    var next = document.createElement('button');
    var table = document.createElement('table');
    var head = document.createElement('thead');
    var headingRow = document.createElement('tr');
    var body = document.createElement('tbody');

    navigation.className = 'iabc-booking__calendar-nav';
    previous.type = 'button';
    previous.className = 'iabc-booking__calendar-nav-button';
    previous.textContent = config.strings.previousShort;
    previous.setAttribute('aria-label', config.strings.previousMonth);
    title.className = 'iabc-booking__calendar-title';
    title.id = titleId;
    title.setAttribute('aria-live', 'polite');
    next.type = 'button';
    next.className = 'iabc-booking__calendar-nav-button';
    next.textContent = config.strings.nextShort;
    next.setAttribute('aria-label', config.strings.nextMonth);
    navigation.appendChild(previous);
    navigation.appendChild(title);
    navigation.appendChild(next);

    table.className = 'iabc-booking__calendar-table';
    table.setAttribute('aria-labelledby', titleId);
    var monday = new Date(2024, 0, 1, 12, 0, 0, 0);
    for (var weekday = 0; weekday < 7; weekday += 1) {
      var weekdayDate = addDays(monday, weekday);
      var heading = document.createElement('th');
      heading.scope = 'col';
      heading.setAttribute('abbr', new Intl.DateTimeFormat(locale, { weekday: 'long' }).format(weekdayDate));
      heading.textContent = new Intl.DateTimeFormat(locale, { weekday: 'short' }).format(weekdayDate).replace('.', '');
      headingRow.appendChild(heading);
    }
    head.appendChild(headingRow);
    table.appendChild(head);
    table.appendChild(body);
    mount.setAttribute('role', 'group');
    mount.setAttribute('aria-label', config.strings.calendarLabel);
    mount.replaceChildren(navigation, table);

    widget._iabcCalendar = {
      activeMonth: startOfMonth(parseIsoDate(date.value) || parseIsoDate(config.minDate)),
      body: body,
      config: config,
      date: date,
      next: next,
      previous: previous,
      title: title
    };

    previous.addEventListener('click', function () {
      widget._iabcCalendar.activeMonth = startOfMonth(addMonths(widget._iabcCalendar.activeMonth, -1));
      renderCalendar(widget, config);
    });
    next.addEventListener('click', function () {
      widget._iabcCalendar.activeMonth = startOfMonth(addMonths(widget._iabcCalendar.activeMonth, 1));
      renderCalendar(widget, config);
    });

    renderCalendar(widget, config);
    mount.hidden = false;
    fallback.hidden = true;
    widget.classList.add('iabc-booking--calendar-enhanced');
  }

  function syncCalendar(widget, config) {
    var calendar = widget._iabcCalendar;
    if (!calendar) return;
    var restoreFocus = calendar.body.contains(document.activeElement);
    var selected = parseIsoDate(calendar.date.value);
    if (selected) calendar.activeMonth = startOfMonth(selected);
    renderCalendar(widget, config);
    if (restoreFocus && selected) {
      var selectedButton = calendar.body.querySelector('[data-iabc-date="' + isoDate(selected) + '"]');
      if (selectedButton) selectedButton.focus();
    }
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
    date.addEventListener('change', function () {
      syncCalendar(widget, config);
      loadSlots(widget, config);
    });
    form.addEventListener('submit', function (event) { submitBooking(event, widget, config); });
    try {
      buildCalendar(widget, config, date);
    } catch (error) {
      var calendar = widget.querySelector('[data-iabc-calendar]');
      var fallback = widget.querySelector('[data-iabc-date-fallback]');
      if (calendar) calendar.hidden = true;
      if (fallback) fallback.hidden = false;
    }
    loadSlots(widget, config);
  }

  document.querySelectorAll('[data-iabc-booking]').forEach(init);
}());
