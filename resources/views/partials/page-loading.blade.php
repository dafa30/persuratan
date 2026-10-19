{{-- Overlay loading global: tampil saat halaman dimuat, pindah halaman, dan submit form --}}
<style>
  #suratLoading{position:fixed;inset:0;z-index:2000;display:flex;align-items:center;justify-content:center;
    background:rgba(15,30,60,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
    opacity:0;visibility:hidden;transition:opacity .35s ease,visibility .35s ease}
  #suratLoading.show{opacity:1;visibility:visible}
  #suratLoading .sl-card{background:#fff;border-radius:16px;padding:2rem 2.5rem;text-align:center;min-width:280px;max-width:calc(100vw - 32px);
    box-shadow:0 20px 50px rgba(0,0,0,.3);transform:translateY(12px) scale(.97);transition:transform .35s ease}
  #suratLoading.show .sl-card{transform:none}
  #suratLoading .sl-ring{position:relative;width:64px;height:64px;margin:0 auto 1.25rem}
  #suratLoading .sl-ring::before,#suratLoading .sl-ring::after{content:"";position:absolute;inset:0;border-radius:50%;border:3px solid transparent}
  #suratLoading .sl-ring::before{border-top-color:#1e3a8a;border-right-color:#1e3a8a;animation:slSpin 1.1s cubic-bezier(.5,.1,.5,.9) infinite}
  #suratLoading .sl-ring::after{inset:10px;border-bottom-color:#c9a227;border-left-color:#c9a227;animation:slSpin 1.6s linear infinite reverse}
  #suratLoading .sl-title{font-weight:600;color:#1e3a8a;margin-bottom:.25rem}
  #suratLoading .sl-sub{font-size:.85rem;color:#6b7280}
  #suratLoading .sl-dots span{display:inline-block;animation:slFade 1.4s infinite both}
  #suratLoading .sl-dots span:nth-child(2){animation-delay:.2s}
  #suratLoading .sl-dots span:nth-child(3){animation-delay:.4s}
  @keyframes slSpin{to{transform:rotate(360deg)}}
  @keyframes slFade{0%,80%,100%{opacity:.2}40%{opacity:1}}
  @media (prefers-reduced-motion:reduce){#suratLoading .sl-ring::before,#suratLoading .sl-ring::after{animation-duration:3s}}
</style>
<noscript><style>#suratLoading{display:none}</style></noscript>
<div id="suratLoading" class="show" role="status" aria-live="polite" aria-hidden="false">
  <div class="sl-card">
    <div class="sl-ring"></div>
    <div class="sl-title"><span id="slTitle">Memuat Halaman</span><span class="sl-dots"><span>.</span><span>.</span><span>.</span></span></div>
    <div class="sl-sub" id="slSub">Mohon tunggu sebentar.</div>
  </div>
</div>
<script>
  (function () {
    const ov = document.getElementById('suratLoading');
    const title = document.getElementById('slTitle');
    const sub = document.getElementById('slSub');
    let timer;

    function show(t, s) {
      title.textContent = t || 'Memuat Halaman';
      sub.textContent = s || 'Mohon tunggu sebentar.';
      ov.classList.add('show');
      ov.setAttribute('aria-hidden', 'false');
      clearTimeout(timer);
      // Pengaman bila navigasi tidak jadi terjadi (mis. unduhan file)
      timer = setTimeout(hide, 20000);
    }
    function hide() {
      clearTimeout(timer);
      ov.classList.remove('show');
      ov.setAttribute('aria-hidden', 'true');
    }

    // Muat awal: tutup setelah semua resource selesai, dengan batas waktu
    if (document.readyState === 'complete') hide();
    else window.addEventListener('load', hide);
    timer = setTimeout(hide, 10000);
    window.addEventListener('pageshow', hide);

    document.addEventListener('click', function (e) {
      if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      const a = e.target.closest && e.target.closest('a[href]');
      if (!a || a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-bs-toggle') || a.hasAttribute('data-no-loading')) return;
      let url;
      try { url = new URL(a.href, location.href); } catch (_) { return; }
      if (!/^https?:$/.test(url.protocol) || url.origin !== location.origin) return;
      if (url.pathname === location.pathname && url.search === location.search) return;
      if (/\/file(\/|$)/.test(url.pathname)) return;
      setTimeout(function () { if (!e.defaultPrevented) show(); }, 0);
    });

    document.addEventListener('submit', function (e) {
      const f = e.target;
      if (!f || f.target === '_blank' || f.hasAttribute('data-no-loading')) return;
      setTimeout(function () {
        if (e.defaultPrevented) return;
        if (f.id === 'formSurat') {
          show('Memproses Surat', 'Mohon tunggu, jangan menutup halaman ini.');
          f.querySelectorAll('button[type=submit]').forEach(function (b) { b.disabled = true; });
        } else {
          show();
        }
      }, 0);
    });
  })();
</script>
