{{-- Dialog pesan elegan; menggantikan alert() bawaan browser. Pakai: sxNotice({ title, message, type, button, onClose }) --}}
<style>
  #noticeDialog{position:fixed;inset:0;z-index:3100;display:flex;align-items:center;justify-content:center;padding:16px;
    background:rgba(15,23,42,.55);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);
    opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease}
  #noticeDialog.show{opacity:1;visibility:visible}
  #noticeDialog .nd-card{width:100%;max-width:400px;background:#fff;border-radius:18px;padding:28px 26px 22px;text-align:center;
    box-shadow:0 24px 60px rgba(0,0,0,.3);transform:translateY(14px) scale(.96);transition:transform .25s ease}
  #noticeDialog.show .nd-card{transform:none}
  #noticeDialog .nd-icon{width:64px;height:64px;margin:0 auto 16px;border-radius:50%;display:grid;place-items:center;font-size:28px}
  #noticeDialog[data-type="info"] .nd-icon{background:#e8effd;color:#1e3a8a}
  #noticeDialog[data-type="warning"] .nd-icon{background:#fdf3d8;color:#b7791f}
  #noticeDialog[data-type="success"] .nd-icon{background:#e3f6ea;color:#1e8449}
  #noticeDialog[data-type="error"] .nd-icon{background:#fdecec;color:#c0392b}
  #noticeDialog .nd-title{margin:0 0 6px;font-size:19px;font-weight:700;color:#1f2937}
  #noticeDialog .nd-text{margin:0 0 22px;font-size:14px;color:#6b7280;line-height:1.55;white-space:pre-line}
  #noticeDialog .nd-ok{width:100%;padding:11px 14px;border:0;border-radius:10px;font-weight:600;font-size:14px;color:#fff;
    background:#1e3a8a;cursor:pointer;transition:background-color .2s ease,transform .1s ease}
  #noticeDialog .nd-ok:hover{background:#172e6e}
  #noticeDialog .nd-ok:active{transform:scale(.98)}
  @media (prefers-reduced-motion:reduce){#noticeDialog,#noticeDialog .nd-card{transition:none}}
</style>
<div id="noticeDialog" role="alertdialog" aria-modal="true" aria-labelledby="ndTitle" aria-describedby="ndText" aria-hidden="true" data-type="info">
  <div class="nd-card">
    <div class="nd-icon"><i class="bi bi-info-circle" id="ndIcon" aria-hidden="true"></i></div>
    <h2 class="nd-title" id="ndTitle">Informasi</h2>
    <p class="nd-text" id="ndText"></p>
    <button type="button" class="nd-ok" id="ndOk">OK</button>
  </div>
</div>
<script>
  (function () {
    const dlg = document.getElementById('noticeDialog');
    if (!dlg) return;
    const icons = { info: 'bi-info-circle', warning: 'bi-exclamation-triangle', success: 'bi-check-circle', error: 'bi-x-circle' };
    const titles = { info: 'Informasi', warning: 'Perhatian', success: 'Berhasil', error: 'Terjadi Kesalahan' };
    const iconEl = document.getElementById('ndIcon');
    const titleEl = document.getElementById('ndTitle');
    const textEl = document.getElementById('ndText');
    const okBtn = document.getElementById('ndOk');
    let onClose = null;
    let lastFocus = null;

    function close() {
      if (!dlg.classList.contains('show')) return;
      dlg.classList.remove('show');
      dlg.setAttribute('aria-hidden', 'true');
      const cb = onClose;
      onClose = null;
      if (lastFocus && document.contains(lastFocus)) lastFocus.focus();
      if (typeof cb === 'function') cb();
    }

    window.sxNotice = function (opts) {
      opts = opts || {};
      const type = icons[opts.type] ? opts.type : 'info';
      dlg.dataset.type = type;
      iconEl.className = 'bi ' + icons[type];
      titleEl.textContent = opts.title || titles[type];
      textEl.textContent = opts.message || '';
      okBtn.textContent = opts.button || 'OK';
      onClose = opts.onClose || null;
      lastFocus = document.activeElement;
      dlg.classList.add('show');
      dlg.setAttribute('aria-hidden', 'false');
      okBtn.focus();
    };

    // alert() bawaan browser dialihkan ke dialog ini
    window.alert = function (message) {
      window.sxNotice({ message: String(message == null ? '' : message) });
    };

    okBtn.addEventListener('click', close);
    dlg.addEventListener('click', function (e) { if (e.target === dlg) close(); });
    document.addEventListener('keydown', function (e) {
      if (dlg.classList.contains('show') && (e.key === 'Escape' || e.key === 'Enter')) { e.preventDefault(); close(); }
    });
    window.addEventListener('pageshow', function () { dlg.classList.remove('show'); });
  })();
</script>
