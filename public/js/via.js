/* VIA BELLA — интерактив: шапка, появления, избранное, консультант, бронирование, карта, конструктор */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var VB = window.VB || {};
  var fmt = function (n) { return '€' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' '); };
  var toastEl = $('#toast'), toastT;
  function toast(msg) { if (!toastEl) return; toastEl.textContent = msg; toastEl.classList.add('on'); clearTimeout(toastT); toastT = setTimeout(function () { toastEl.classList.remove('on'); }, 2600); }
  window.vbToast = toast;

  /* ---------- Шапка: прозрачная на hero, плотная при прокрутке ---------- */
  var hdr = $('#hdr');
  var overHero = !!$('[data-hero]');
  function onScroll() { if (!hdr) return; hdr.classList.toggle('solid', !overHero || window.scrollY > 40); }
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true });

  /* ---------- Мобильное меню и поиск ---------- */
  var burger = $('#burger'), drawer = $('#drawer');
  function setDrawer(on) {
    drawer.classList.toggle('on', on); hdr.classList.toggle('open', on);
    burger.setAttribute('aria-expanded', on); drawer.setAttribute('aria-hidden', !on);
    document.body.style.overflow = on ? 'hidden' : '';
  }
  if (burger) burger.addEventListener('click', function () { setDrawer(!drawer.classList.contains('on')); });
  var sOv = $('#searchOv');
  function setSearch(on) { sOv.classList.toggle('on', on); sOv.setAttribute('aria-hidden', !on); if (on) setTimeout(function () { $('#searchInput').focus(); }, 200); }
  var so = $('#searchOpen'); if (so) so.addEventListener('click', function () { setSearch(true); });
  var sc = $('#searchClose'); if (sc) sc.addEventListener('click', function () { setSearch(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setSearch(false); if (drawer) setDrawer(false); setChat(false); } });

  /* ---------- Появление секций ---------- */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } }); }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });
    $$('[data-reveal]').forEach(function (el) { io.observe(el); });
  } else { $$('[data-reveal]').forEach(function (el) { el.classList.add('is-in'); }); }

  /* ---------- Мягкие переходы между страницами ---------- */
  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[href]');
    if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || a.target || a.hasAttribute('download')) return;
    var u; try { u = new URL(a.href, location.href); } catch (_) { return; }
    if (u.origin !== location.origin || (u.pathname === location.pathname && u.hash)) return;
    e.preventDefault(); document.body.classList.add('leaving');
    setTimeout(function () { location.href = a.href; }, 240);
  });
  window.addEventListener('pageshow', function (e) { if (e.persisted) document.body.classList.remove('leaving'); });

  /* ---------- Избранное: localStorage + синхронизация с БД для вошедших ---------- */
  var KEY = 'vb:favs';
  function load() { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (_) { return []; } }
  function save(a) { try { localStorage.setItem(KEY, JSON.stringify(a)); } catch (_) {} paint(); }
  function paint() {
    var f = load(), n = $('#favCount');
    if (n) { n.textContent = f.length || ''; n.dataset.n = f.length; }
    $$('[data-fav]').forEach(function (b) { var on = f.indexOf(b.dataset.fav) > -1; b.classList.toggle('on', on); b.setAttribute('aria-pressed', on); });
  }
  function post(url, body) {
    return fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': VB.csrf, 'Accept': 'application/json' }, body: JSON.stringify(body) }).then(function (r) { return r.json(); });
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-fav]'); if (!b) return;
    e.preventDefault(); e.stopPropagation();
    var k = b.dataset.fav, f = load(), i = f.indexOf(k);
    if (i > -1) f.splice(i, 1); else f.push(k);
    save(f); toast(i > -1 ? 'Убрано из избранного' : 'Добавлено в избранное');
    if (VB.auth) { var p = k.split(':'); post(VB.routes.toggle, { kind: p[0], ref: p.slice(1).join(':') }).catch(function () {}); }
  });
  if (VB.auth) {
    post(VB.routes.merge, { keys: load() }).then(function (r) { if (r.keys) { try { localStorage.setItem(KEY, JSON.stringify(r.keys)); } catch (_) {} paint(); document.dispatchEvent(new Event('favs:synced')); } }).catch(function () {});
  }
  paint();
  document.addEventListener('favs:changed', paint);

  /* страница «Избранное» */
  var favBox = $('#favBox');
  if (favBox) {
    var render = function () {
      var f = load();
      if (!f.length) { favBox.innerHTML = '<div class="empty"><h3>Пока пусто</h3><p class="muted">Нажмите на сердце на любой карточке — и тур, гид или отель появится здесь.</p><a class="btn" href="' + favBox.dataset.catalog + '">Открыть каталог</a></div>'; return; }
      fetch(VB.routes.cards + '?keys=' + encodeURIComponent(f.join(',')), { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(function (r) { return r.text(); }).then(function (h) { favBox.innerHTML = h; paint(); $$('[data-reveal]', favBox).forEach(function (el) { el.classList.add('is-in'); }); });
    };
    render(); document.addEventListener('favs:synced', render);
    document.addEventListener('click', function (e) { if (e.target.closest('#favBox [data-fav]')) setTimeout(render, 350); });
  }

  /* ---------- Счётчик-степпер (поиск) ---------- */
  $$('[data-stepper]').forEach(function (s) {
    var out = $('output', s), inp = $('input', s), min = +s.dataset.min || 1, max = +s.dataset.max || 20;
    function set(v) { v = Math.max(min, Math.min(max, v)); out.textContent = v; inp.value = v; s.dispatchEvent(new CustomEvent('change', { bubbles: true, detail: v })); }
    $$('button', s).forEach(function (b) { b.addEventListener('click', function () { set(+inp.value + (b.dataset.d === '+' ? 1 : -1)); }); });
  });

  /* ---------- Консультант (демо-сценарий) ---------- */
  var chat = $('#chat'), chatBtn = $('#chatOpen');
  function setChat(on) { if (!chat) return; chat.classList.toggle('on', on); chat.setAttribute('aria-hidden', !on); chatBtn.setAttribute('aria-expanded', on); if (on) $('#chatInput').focus(); }
  if (chatBtn) {
    chatBtn.addEventListener('click', function () { setChat(!chat.classList.contains('on')); });
    $('#chatClose').addEventListener('click', function () { setChat(false); });
    var log = $('#chatLog');
    var add = function (t, cls, tours) {
      var m = document.createElement('div'); m.className = 'msg ' + cls; m.textContent = t;
      (tours || []).forEach(function (x) { var a = document.createElement('a'); a.className = 'mini'; a.href = x.url; a.innerHTML = '<img alt="" loading="lazy"><div><b></b><small></small></div>'; a.querySelector('img').src = x.image; a.querySelector('b').textContent = x.title; a.querySelector('small').textContent = x.meta; m.appendChild(a); });
      log.appendChild(m); log.scrollTop = log.scrollHeight;
    };
    var ask = function (t) {
      if (!t.trim()) return; add(t, 'me');
      fetch(VB.routes.assistant, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': VB.csrf, 'Accept': 'application/json' }, body: JSON.stringify({ message: t }) })
        .then(function (r) { return r.json(); }).then(function (d) { setTimeout(function () { add(d.text || 'Не поняла, попробуйте иначе.', 'bot', d.tours); }, 450); })
        .catch(function () { add('Сейчас не получается ответить. Откройте каталог — там всё на месте.', 'bot'); });
    };
    $('#chatForm').addEventListener('submit', function (e) { e.preventDefault(); var i = $('#chatInput'); ask(i.value); i.value = ''; });
    $$('.chat__sug button').forEach(function (b) { b.addEventListener('click', function () { ask(b.textContent); }); });
  }

  /* ---------- Бронирование: пошаговый мастер ---------- */
  var bk = $('#bkForm');
  if (bk) {
    var steps = $$('.step', bk), prog = $$('.progress li'), cur = 0;
    var price = +bk.dataset.price, sched = $$('input[name=schedule_id]', bk), cnt = $('#pcount'), out = $('#pcountOut');
    var firstErr = bk.dataset.errstep ? +bk.dataset.errstep : 0;
    function view() {
      steps.forEach(function (s, i) { s.classList.toggle('on', i === cur); });
      prog.forEach(function (p, i) { p.classList.toggle('cur', i === cur); p.classList.toggle('done', i < cur); });
      if (cur === 4) fillReview();
      window.scrollTo({ top: Math.max(0, bk.getBoundingClientRect().top + window.scrollY - 120), behavior: 'smooth' });
    }
    function chosen() { return sched.filter(function (s) { return s.checked; })[0]; }
    function free() { var c = chosen(); return c ? +c.dataset.free : 0; }
    function recalc() {
      var n = +cnt.value, c = chosen();
      out.textContent = n;
      $$('[data-sum-count]').forEach(function (e) { e.textContent = n; });
      $$('[data-sum-total]').forEach(function (e) { e.textContent = fmt(n * price); });
      $$('[data-sum-date]').forEach(function (e) { e.textContent = c ? c.dataset.label : 'Дата не выбрана'; });
      var hint = $('#freeHint'); if (hint) hint.textContent = c ? ('Свободно мест на выбранную дату: ' + c.dataset.free) : 'Сначала выберите дату';
    }
    sched.forEach(function (s) { s.addEventListener('change', function () { $$('.date-opt', bk).forEach(function (l) { l.classList.toggle('on', l.contains(s) && s.checked); }); var f = +s.dataset.free; if (+cnt.value > f) cnt.value = Math.max(1, f); recalc(); }); });
    $$('[data-cd]', bk).forEach(function (b) { b.addEventListener('click', function () { var v = +cnt.value + (b.dataset.cd === '+' ? 1 : -1); v = Math.max(1, Math.min(Math.max(1, free() || 20), v)); cnt.value = v; recalc(); }); });
    function msg(el, t) { var p = el.parentNode.querySelector('.err.js'); if (!p) { p = document.createElement('p'); p.className = 'err js'; el.parentNode.appendChild(p); } p.textContent = t; el.classList && el.classList.add('bad'); }
    function clear(scope) { $$('.err.js', scope).forEach(function (e) { e.remove(); }); $$('.bad', scope).forEach(function (e) { e.classList.remove('bad'); }); }
    function validate(i) {
      var s = steps[i]; clear(s); var ok = true;
      if (i === 0 && !chosen()) { var h = $('#dateErr'); h.textContent = 'Выберите дату поездки.'; ok = false; } else if (i === 0) { $('#dateErr').textContent = ''; }
      if (i === 1 && +cnt.value > free()) { $('#cntErr').textContent = 'На выбранную дату свободно мест: ' + free() + '.'; ok = false; } else if (i === 1) { $('#cntErr').textContent = ''; }
      if (i === 2) {
        $$('[required]', s).forEach(function (f) { if (!f.value.trim()) { msg(f, 'Заполните это поле.'); ok = false; } });
        var em = $('[name=contact_email]', s); if (em.value && !/^\S+@\S+\.\S+$/.test(em.value)) { msg(em, 'Проверьте адрес почты.'); ok = false; }
        var ph = $('[name=contact_phone]', s); if (ph.value && ph.value.replace(/\D/g, '').length < 6) { msg(ph, 'Слишком короткий номер.'); ok = false; }
      }
      return ok;
    }
    function fillReview() {
      var c = chosen(), set = function (k, v) { var e = $('[data-r="' + k + '"]'); if (e) e.textContent = v; };
      set('date', c ? c.dataset.label : '—'); set('count', cnt.value); set('name', $('[name=contact_name]').value);
      set('email', $('[name=contact_email]').value); set('phone', $('[name=contact_phone]').value); set('total', fmt(+cnt.value * price));
    }
    $$('[data-next]', bk).forEach(function (b) { b.addEventListener('click', function () { if (validate(cur)) { cur = Math.min(steps.length - 1, cur + 1); view(); } }); });
    $$('[data-prev]', bk).forEach(function (b) { b.addEventListener('click', function () { cur = Math.max(0, cur - 1); view(); }); });
    bk.addEventListener('submit', function (e) { for (var i = 0; i < 4; i++) { if (!validate(i)) { e.preventDefault(); cur = i; view(); return; } } if (!$('#agree').checked) { e.preventDefault(); $('#agreeErr').textContent = 'Подтвердите согласие с условиями.'; return; } $('#submitBtn').disabled = true; });
    var pre = sched.filter(function (s) { return s.checked; })[0]; if (pre) pre.dispatchEvent(new Event('change'));
    cur = firstErr; recalc(); view();
    window.scrollTo(0, 0);
  }

  /* ---------- Страница тура: выбор даты → кнопка бронирования ---------- */
  var tourBox = $('#tourAside');
  if (tourBox) {
    var go = $('#goBook'), base = go.dataset.href;
    $$('input[name=sched]', tourBox).forEach(function (r) { r.addEventListener('change', function () { $$('.date-opt', tourBox).forEach(function (l) { l.classList.toggle('on', l.contains(r) && r.checked); }); go.href = base + '?schedule=' + r.value; go.classList.remove('btn--disabled'); }); });
  }

  /* ---------- Карта Европы ---------- */
  var map = $('#euMap');
  if (map) {
    var pts = JSON.parse($('#mapData').textContent), card = $('#mapCard');
    function show(p) {
      $$('.pin', map).forEach(function (b) { b.classList.toggle('on', b.dataset.k === p.key); });
      $('img', card).src = p.img; $('[data-m=name]', card).textContent = p.name; $('[data-m=country]', card).textContent = p.country_name; $('[data-m=text]', card).textContent = p.text;
      var ul = $('[data-m=tours]', card); ul.innerHTML = '';
      if (!p.tours.length) ul.innerHTML = '<li class="muted" style="padding:10px 0;border-top:1px solid var(--line)">Туры в этом месте скоро появятся.</li>';
      p.tours.forEach(function (t) { var li = document.createElement('li'); li.innerHTML = '<a><span></span><em></em></a>'; var a = $('a', li); a.href = t.url; $('span', a).textContent = t.title; $('em', a).textContent = 'от ' + fmt(t.price); ul.appendChild(li); });
      card.classList.remove('flash'); void card.offsetWidth; card.classList.add('flash');
    }
    $$('.pin', map).forEach(function (b) { b.addEventListener('click', function () { show(pts.filter(function (p) { return p.key === b.dataset.k; })[0]); }); });
    show(pts[0]);
  }

  /* ---------- Конструктор путешествия ---------- */
  var bld = $('#builder');
  if (bld) {
    var state = { country: '', flight: null, hotel: null, nights: 5, tours: [] }, data = null;
    var el = function (id) { return document.getElementById(id); };
    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function load2() {
      var c = el('bCountry').value; if (!c) return;
      state = { country: c, flight: null, hotel: null, nights: +el('bNights').value, tours: [] };
      el('bBody').hidden = false; el('bFlights').innerHTML = 'Загружаем…';
      fetch(bld.dataset.url + '?country=' + c + '&from=' + encodeURIComponent(el('bFrom').value) + '&date=' + el('bDate').value).then(function (r) { return r.json(); }).then(function (d) { data = d; draw(); });
    }
    function draw() {
      el('bFlights').innerHTML = data.flights.map(function (f, i) { return '<button type="button" class="choice flight-opt" data-f="' + i + '"><b>' + esc(f.from) + ' → ' + esc(f.to) + '</b><small>' + esc(f.airline) + ' · ' + f.depart + '–' + f.arrive + ' · ' + (f.stops ? '1 пересадка' : 'прямой') + '</small><b style="margin-top:8px">' + fmt(f.price) + '</b></button>'; }).join('');
      el('bHotels').innerHTML = data.hotels.length ? data.hotels.map(function (h, i) { return '<button type="button" class="choice" data-h="' + i + '"><div class="ph"><img src="' + h.image + '" alt="" loading="lazy"></div><div class="b"><b>' + esc(h.name) + '</b><small>' + esc(h.city) + ' · ' + fmt(h.price) + ' / ночь</small></div></button>'; }).join('') : '<p class="muted">Для этого направления демо-отелей пока нет.</p>';
      el('bTours').innerHTML = data.tours.map(function (t, i) { return '<button type="button" class="choice" data-t="' + i + '"><div class="ph"><img src="' + t.image + '" alt="" loading="lazy"></div><div class="b"><b>' + esc(t.title) + '</b><small>' + esc(t.duration) + ' · ' + fmt(t.price) + '</small></div></button>'; }).join('');
      sum();
    }
    function sum() {
      var rows = [], total = 0, n = +el('bGuests').value;
      if (state.flight) { var fp = state.flight.price * n; total += fp; rows.push(['Перелёт · ' + state.flight.to, fp]); }
      if (state.hotel) { var hp = state.hotel.price * state.nights * Math.ceil(n / 2); total += hp; rows.push([state.hotel.name + ' · ' + state.nights + ' н.', hp]); }
      state.tours.forEach(function (t) { var tp = t.price * n; total += tp; rows.push([t.title, tp]); });
      el('bRoute').innerHTML = rows.length ? rows.map(function (r) { return '<li><span>' + esc(r[0]) + '</span><b>' + fmt(r[1]) + '</b></li>'; }).join('') : '<li class="empty-li">Выберите перелёт, проживание и впечатления</li>';
      el('bTotal').textContent = fmt(total);
      var links = el('bLinks'); links.innerHTML = state.tours.map(function (t) { return '<a class="btn btn--sm" href="' + t.url + '">Забронировать: ' + esc(t.title.slice(0, 26)) + '…</a>'; }).join('');
    }
    el('bGo').addEventListener('click', load2);
    el('bNights').addEventListener('input', function () { state.nights = +this.value; el('bNightsOut').textContent = this.value; sum(); });
    el('bGuests').closest('[data-stepper]').addEventListener('change', sum);
    bld.addEventListener('click', function (e) {
      var b = e.target.closest('.choice'); if (!b) return;
      if (b.dataset.f !== undefined) { state.flight = data.flights[+b.dataset.f]; $$('[data-f]', bld).forEach(function (x) { x.classList.toggle('on', x === b); }); }
      if (b.dataset.h !== undefined) { state.hotel = data.hotels[+b.dataset.h]; $$('[data-h]', bld).forEach(function (x) { x.classList.toggle('on', x === b); }); }
      if (b.dataset.t !== undefined) { var t = data.tours[+b.dataset.t], i = state.tours.indexOf(t); if (i > -1) state.tours.splice(i, 1); else state.tours.push(t); b.classList.toggle('on', i === -1); }
      sum();
    });
  }
})();
