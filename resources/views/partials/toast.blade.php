{{-- Toast elegan untuk flash message (success/error/warning/info + error validasi). Pakai di JS: sxToast({ type, title, message, items, duration }) --}}
@php
    $sxFlash = [];
    foreach (['success', 'error', 'warning', 'info'] as $sxType) {
        if ($sxType === 'success' && session('register_success')) {
            continue;
        }
        if (session()->has($sxType) && is_string(session($sxType)) && session($sxType) !== '') {
            $sxFlash[] = ['type' => $sxType, 'message' => session($sxType)];
        }
    }
    if (isset($errors) && $errors->any()) {
        $sxFlash[] = ['type' => 'error', 'title' => 'Periksa kembali input Anda', 'items' => $errors->all()];
    }
@endphp
<style>
  #sxToastStack{position:fixed;top:16px;right:16px;z-index:3200;display:flex;flex-direction:column;gap:10px;
    width:calc(100vw - 32px);max-width:380px;pointer-events:none}
  .sx-toast{--c:#1e3a8a;--bg:#e8effd;position:relative;display:flex;gap:12px;align-items:flex-start;overflow:hidden;
    padding:14px 14px 16px 16px;background:#fff;border-radius:14px;box-shadow:0 12px 32px rgba(15,23,42,.22);
    pointer-events:auto;opacity:0;transform:translateX(24px);transition:opacity .3s ease,transform .3s ease}
  .sx-toast.show{opacity:1;transform:none}
  .sx-toast.hide{opacity:0;transform:translateX(24px)}
  .sx-toast::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--c)}
  .sx-toast[data-type="success"]{--c:#1e8449;--bg:#e3f6ea}
  .sx-toast[data-type="error"]{--c:#c0392b;--bg:#fdecec}
  .sx-toast[data-type="warning"]{--c:#b7791f;--bg:#fdf3d8}
  .sx-toast .st-icon{flex:0 0 auto;width:36px;height:36px;border-radius:50%;display:grid;place-items:center;
    background:var(--bg);color:var(--c);font-size:18px}
  .sx-toast .st-body{flex:1;min-width:0}
  .sx-toast .st-title{margin:0;font-size:14px;font-weight:700;color:#1f2937;line-height:1.35}
  .sx-toast .st-text{margin:2px 0 0;font-size:13px;color:#6b7280;line-height:1.5;word-break:break-word}
  .sx-toast .st-list{margin:4px 0 0;padding-left:18px;font-size:13px;color:#6b7280;line-height:1.5}
  .sx-toast .st-close{flex:0 0 auto;border:0;background:transparent;color:#9ca3af;font-size:20px;line-height:1;cursor:pointer;
    padding:0 2px;transition:color .2s ease}
  .sx-toast .st-close:hover{color:#374151}
  .sx-toast .st-bar{position:absolute;left:0;right:0;bottom:0;height:3px;background:var(--c);opacity:.35;transform-origin:left;
    animation:stBar var(--d,4s) linear forwards}
  .sx-toast:hover .st-bar{animation-play-state:paused}
  @keyframes stBar{from{transform:scaleX(1)}to{transform:scaleX(0)}}
  @media (max-width:575.98px){#sxToastStack{top:10px;right:10px;width:calc(100vw - 20px)}}
  @media (prefers-reduced-motion:reduce){.sx-toast{transition:none}.sx-toast .st-bar{animation:none;display:none}}
</style>
<div id="sxToastStack" aria-live="polite" aria-atomic="false"></div>
<script>
  (function () {
    const stack = document.getElementById('sxToastStack');
    if (!stack) return;
    const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
    const titles = { success: 'Berhasil', error: 'Terjadi Kesalahan', warning: 'Perhatian', info: 'Informasi' };

    window.sxToast = function (opts) {
      opts = opts || {};
      const type = icons[opts.type] ? opts.type : 'info';
      const duration = opts.duration || (opts.items || type === 'error' ? 6000 : 4000);

      const el = document.createElement('div');
      el.className = 'sx-toast';
      el.dataset.type = type;
      el.setAttribute('role', type === 'error' ? 'alert' : 'status');
      el.style.setProperty('--d', duration + 'ms');

      const icon = document.createElement('div');
      icon.className = 'st-icon';
      icon.innerHTML = '<i class="bi ' + icons[type] + '" aria-hidden="true"></i>';

      const body = document.createElement('div');
      body.className = 'st-body';
      const title = document.createElement('p');
      title.className = 'st-title';
      title.textContent = opts.title || titles[type];
      body.appendChild(title);
      if (opts.message) {
        const text = document.createElement('p');
        text.className = 'st-text';
        text.textContent = opts.message;
        body.appendChild(text);
      }
      if (opts.items && opts.items.length) {
        const ul = document.createElement('ul');
        ul.className = 'st-list';
        opts.items.forEach(function (it) { const li = document.createElement('li'); li.textContent = it; ul.appendChild(li); });
        body.appendChild(ul);
      }

      const close = document.createElement('button');
      close.type = 'button';
      close.className = 'st-close';
      close.setAttribute('aria-label', 'Tutup');
      close.innerHTML = '&times;';

      const bar = document.createElement('div');
      bar.className = 'st-bar';

      el.append(icon, body, close, bar);
      stack.appendChild(el);
      requestAnimationFrame(function () { requestAnimationFrame(function () { el.classList.add('show'); }); });

      let timer;
      let remaining = duration;
      let started = Date.now();
      function dismiss() {
        clearTimeout(timer);
        el.classList.remove('show');
        el.classList.add('hide');
        setTimeout(function () { el.remove(); }, 350);
      }
      function start() { started = Date.now(); timer = setTimeout(dismiss, remaining); }
      el.addEventListener('mouseenter', function () { clearTimeout(timer); remaining -= Date.now() - started; });
      el.addEventListener('mouseleave', start);
      close.addEventListener('click', dismiss);
      start();
    };

    const flash = @json($sxFlash);
    function fire() { flash.forEach(function (f, i) { setTimeout(function () { window.sxToast(f); }, i * 150); }); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fire); else fire();
  })();
</script>
