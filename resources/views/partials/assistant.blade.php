<button class="fab" id="chatOpen" aria-expanded="false" aria-controls="chat"><svg><use href="#i-chat"/></svg><span>Консультант</span></button>
<section class="chat" id="chat" aria-label="Онлайн-консультант" aria-hidden="true">
  <div class="chat__h"><div><b>Консультант VIA BELLA</b><small>Демо-сценарий · не ИИ</small></div><button class="icon-btn" id="chatClose" aria-label="Закрыть"><svg><use href="#i-close"/></svg></button></div>
  <div class="chat__log" id="chatLog" aria-live="polite">
    <div class="msg bot">Здравствуйте! Я помогу выбрать поездку. Расскажите, какое настроение вам нужно — или нажмите подсказку ниже.</div>
  </div>
  <div>
    <div class="chat__sug">
      <button type="button">Хочу море и закаты</button><button type="button">Горы и тишина</button><button type="button">Вино и еда в Италии</button><button type="button">Города и музеи до 2000</button>
    </div>
    <p class="chat__demo">Это демонстрационный сценарий на правилах, без внешнего искусственного интеллекта. Подбор идёт по каталогу сайта.</p>
    <form id="chatForm"><input type="text" id="chatInput" placeholder="Например: море и вино до 3000" maxlength="300" aria-label="Сообщение"><button aria-label="Отправить"><svg width="22" height="22"><use href="#i-arrow"/></svg></button></form>
  </div>
</section>
