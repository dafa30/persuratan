{{-- Dialog konfirmasi hapus di tengah layar; menggantikan confirm() bawaan browser --}}
<style>
  #confirmDialog{position:fixed;inset:0;z-index:3000;display:flex;align-items:center;justify-content:center;padding:16px;
    background:rgba(15,23,42,.55);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);
    opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease}
  #confirmDialog.show{opacity:1;visibility:visible}
  #confirmDialog .cd-card{width:100%;max-width:400px;background:#fff;border-radius:18px;padding:28px 26px 22px;text-align:center;
    box-shadow:0 24px 60px rgba(0,0,0,.3);transform:translateY(14px) scale(.96);transition:transform .25s ease}
  #confirmDialog.show .cd-card{transform:none}
  #confirmDialog .cd-icon{width:64px;height:64px;margin:0 auto 16px;border-radius:50%;display:grid;place-items:center;
    background:#fdecec;color:#c0392b;font-size:28px}
  #confirmDialog .cd-title{margin:0 0 6px;font-size:19px;font-weight:700;color:#1f2937}
  #confirmDialog .cd-text{margin:0 0 22px;font-size:14px;color:#6b7280;line-height:1.55}
  #confirmDialog .cd-actions{display:flex;gap:10px}
  #confirmDialog .cd-actions button{flex:1;padding:11px 14px;border-radius:10px;font-weight:600;font-size:14px;cursor:pointer;
    transition:background-color .2s ease,transform .1s ease}
  #confirmDialog .cd-actions button:active{transform:scale(.98)}
  #confirmDialog .cd-cancel{border:1px solid #d1d5db;background:#fff;color:#374151}
  #confirmDialog .cd-cancel:hover{background:#f3f4f6}
  #confirmDialog .cd-ok{border:0;background:#c0392b;color:#fff}
  #confirmDialog .cd-ok:hover{background:#a93226}
  @media (prefers-reduced-motion:reduce){#confirmDialog,#confirmDialog .cd-card{transition:none}}
</style>
<div id="confirmDialog" role="alertdialog" aria-modal="true" aria-labelledby="cdTitle" aria-describedby="cdText" aria-hidden="true">
  <div class="cd-card">
    <div class="cd-icon"><i class="bi bi-trash3" aria-hidden="true"></i></div>
    <h2 class="cd-title" id="cdTitle">Hapus Surat?</h2>
    <p class="cd-text" id="cdText">Yakin ingin menghapus surat ini? Tindakan ini tidak dapat dibatalkan.</p>
    <div class="cd-actions">
      <button type="button" class="cd-cancel">Batal</button>
      <button type="button" class="cd-ok">Ya, Hapus</button>
    </div>
  </div>
</div>
<script>
  (function () {
    const dlg = document.getElementById('confirmDialog');
    if (!dlg) return;
    const okBtn = dlg.querySelector('.cd-ok');
    const cancelBtn = dlg.querySelector('.cd-cancel');
    let pending = null;
    let lastFocus = null;

    function close() {
      dlg.classList.remove('show');
      dlg.setAttribute('aria-hidden', 'true');
      pending = null;
      if (lastFocus) lastFocus.focus();
    }

    function open(form) {
      pending = form;
      lastFocus = document.activeElement;
      dlg.classList.add('show');
      dlg.setAttribute('aria-hidden', 'false');
      cancelBtn.focus();
    }

    // Form lama memakai onsubmit="return confirm('...')": diganti dialog ini tanpa mengubah tiap halaman.
    function upgrade(form) {
      if (form.dataset.confirmDelete) return;
      form.dataset.confirmDelete = '1';
      form.removeAttribute('onsubmit');
      form.addEventListener('submit', function (e) {
        if (form.dataset.confirmed === '1') return;
        e.preventDefault();
        open(form);
      });
    }

    function scan() {
      document.querySelectorAll('form[onsubmit*="confirm("]').forEach(upgrade);
    }
    scan();

    okBtn.addEventListener('click', function () {
      const form = pending;
      if (!form) return;
      form.dataset.confirmed = '1';
      dlg.classList.remove('show');
      form.submit();
    });
    cancelBtn.addEventListener('click', close);
    dlg.addEventListener('click', e => { if (e.target === dlg) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && dlg.classList.contains('show')) close(); });
    window.addEventListener('pageshow', () => dlg.classList.remove('show'));
  })();
</script>
