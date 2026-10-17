{{-- Gaya bersama untuk halaman buat / edit / lihat surat --}}
<style>
  body { background: linear-gradient(160deg, #eef1f6 0%, #f7f4ec 100%); min-height: 100vh; }
  .sx-wrap { max-width: 760px; margin: 0 auto; animation: sxIn .5s ease both; }
  .sx-wrap.sx-wide { max-width: 1280px; }
  @keyframes sxIn { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
  @media (prefers-reduced-motion: reduce) { .sx-wrap { animation: none; } }

  .sx-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; color: #1e3a8a; text-decoration: none; }
  .sx-brand img { width: 44px; height: 44px; object-fit: contain; }
  .sx-brand strong { display: block; font-size: 15px; letter-spacing: .02em; }
  .sx-brand small { color: #6b7280; font-size: 12px; }

  .sx-card { background: #fff; border: 1px solid #e7e9ec; border-radius: 18px; overflow: hidden;
    box-shadow: 0 10px 30px rgba(30, 41, 59, .08); }
  .sx-card-head { display: flex; align-items: center; gap: 14px; padding: 22px 28px;
    background: linear-gradient(135deg, #1e3a8a 0%, #2c4fa8 100%); color: #fff; border-bottom: 3px solid #d5aa45; }
  .sx-card-icon { width: 46px; height: 46px; border-radius: 12px; display: grid; place-items: center; font-size: 22px;
    background: rgba(255, 255, 255, .15); flex: 0 0 auto; }
  .sx-card-head h1 { margin: 0; font-size: 20px; font-weight: 700; }
  .sx-card-head p { margin: 2px 0 0; font-size: 13px; opacity: .85; }
  .sx-card-body { padding: 28px; }

  .sx-section { margin: 0 0 14px; padding-bottom: 8px; border-bottom: 1px solid #eef0f3; font-size: 12px; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase; color: #987223; }
  .sx-section:not(:first-child) { margin-top: 26px; }

  .sx-card .form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
  .sx-card .form-control, .sx-card .form-select { border-radius: 10px; border: 1px solid #d9dde3; padding: 10px 14px; font-size: 14px;
    transition: border-color .2s ease, box-shadow .2s ease; }
  .sx-card .form-control:focus, .sx-card .form-select:focus { border-color: #2c4fa8; box-shadow: 0 0 0 4px rgba(44, 79, 168, .12); }
  .sx-card .form-control[readonly], .sx-card .form-control:disabled { background: #f4f5f7; color: #6b7280; }
  .sx-card .form-text, .sx-card small.text-muted { font-size: 12px; }
  .sx-card a { overflow-wrap: anywhere; }

  .sx-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 28px; padding-top: 20px; border-top: 1px solid #eef0f3; }
  .sx-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 22px; border-radius: 10px;
    font-weight: 600; font-size: 14px; text-decoration: none; cursor: pointer; transition: transform .15s ease, box-shadow .2s ease, background-color .2s ease; }
  .sx-btn:active { transform: scale(.98); }
  .sx-btn-primary { border: 0; color: #fff; background: linear-gradient(135deg, #1e3a8a, #2c4fa8); box-shadow: 0 6px 16px rgba(30, 58, 138, .28); }
  .sx-btn-primary:hover { color: #fff; box-shadow: 0 8px 20px rgba(30, 58, 138, .36); transform: translateY(-1px); }
  .sx-btn-ghost { border: 1px solid #d1d5db; color: #374151; background: #fff; }
  .sx-btn-ghost:hover { background: #f3f4f6; color: #111827; }

  .sx-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 0; }
  .sx-chip { padding: 12px 14px; border-radius: 12px; background: #f6f7f9; border: 1px solid #eceef1; }
  .sx-chip span { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: #8a929b; margin-bottom: 3px; }
  .sx-chip strong { font-size: 14px; color: #1f2937; word-break: break-word; }

  .sx-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
  .sx-toolbar h1 { margin: 0; font-size: 22px; font-weight: 700; color: #1f2937; }
  .sx-toolbar small { color: #6b7280; }

  .sx-viewer iframe { width: 100%; height: 78vh; border: 0; display: block; background: #f1f3f5; }
  .sx-empty { padding: 40px 24px; text-align: center; color: #6b7280; }
  .sx-empty i { font-size: 40px; color: #c4c9d0; display: block; margin-bottom: 8px; }

  .sx-track { position: relative; margin: 0; padding: 0 0 0 30px; list-style: none; }
  .sx-track::before { content: ""; position: absolute; left: 10px; top: 6px; bottom: 6px; width: 2px; background: #dfe3ea; }
  .sx-step { position: relative; padding-bottom: 22px; }
  .sx-step:last-child { padding-bottom: 0; }
  .sx-dot { position: absolute; left: -30px; top: 0; width: 22px; height: 22px; border-radius: 50%; display: grid; place-items: center;
    font-size: 11px; color: #fff; background: #1e3a8a; box-shadow: 0 0 0 4px #fff; }
  .sx-step.pending .sx-dot { background: #c4c9d0; }
  .sx-step h3 { margin: 0 0 2px; font-size: 15px; font-weight: 700; color: #1f2937; }
  .sx-step time { display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px; }
  .sx-step p { margin: 0; font-size: 13px; color: #4b5563; }

  .sx-badge { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
  .sx-badge-ok { background: #e8f3ed; color: #24734e; }
  .sx-badge-wait { background: #fdf3dc; color: #987223; }

  @media (max-width: 575.98px) {
    .sx-card-head { padding: 18px; }
    .sx-card-body { padding: 18px; }
    .sx-actions { flex-direction: column-reverse; }
    .sx-actions .sx-btn { width: 100%; }
    .sx-viewer iframe { height: 65vh; }
    .sx-toolbar > .d-flex { min-width: 0; }
    .sx-toolbar h1 { overflow-wrap: anywhere; }
  }
</style>
