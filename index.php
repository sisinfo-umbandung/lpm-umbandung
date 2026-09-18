<?php
// Jumlah program studi dihitung otomatis dari database ct-rex (tabel master kelas)
// Persentase prodi terakreditasi Baik Sekali / Unggul juga dihitung otomatis dari ak_prodi
$jumlahProdi = 14; // fallback bila database tidak tersedia
$persenBS_U = 92;  // fallback bila database tidak tersedia
try {
    require_once __DIR__ . '/koneksi.php';
    $jumlahProdi = (int) $pdo->query("SELECT COUNT(DISTINCT nama) FROM ak_prodi")->fetchColumn();
    $persenBS_U  = (float) $pdo->query("SELECT (COUNT(CASE WHEN akreditasi IN ('Baik Sekali','Unggul') THEN 1 END) * 100.0 / COUNT(*)) FROM ak_prodi")->fetchColumn();
    $persenBS_U  = (int) round($persenBS_U);
    // var_dump($jumlahProdi);
    // die;
} catch (Throwable $e) {
    // biarkan fallback
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Lembaga Penjaminan Mutu — Universitas Muhammadiyah Bandung</title>
<link rel="icon" type="image/png" href="asset/foto/logo.png" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800&family=Lora:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet" />
<style>
  :root{
    --ink:#0c1f1a;
    --pine:#0e5544;
    --pine-deep:#093a2e;
    --verified:#1a9366;
    --gold:#c98f1c;
    --gold-soft:#f4e6c4;
    --paper:#f2f6f3;
    --surface:#ffffff;
    --line:#d8e3dd;
    --line-soft:#e7efea;
    --muted:#556760;
    --amber:#b5791b;
    --amber-soft:#faedd4;
    --green-soft:#e0f0e7;
    --shadow:0 1px 2px rgba(9,58,46,.05), 0 12px 32px -18px rgba(9,58,46,.28);
    --wrap:1140px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  html{scroll-behavior:smooth}
  body{
    font-family:"Plus Jakarta Sans",system-ui,sans-serif;
    color:var(--ink);
    background:var(--paper);
    line-height:1.55;
    -webkit-font-smoothing:antialiased;
    font-feature-settings:"tnum" 0;
  }
  a{color:inherit;text-decoration:none}
  .wrap{max-width:var(--wrap);margin:0 auto;padding:0 24px}
  .tnum{font-variant-numeric:tabular-nums}

  /* ---------- Top bar ---------- */
  header.bar{
    position:sticky;top:0;z-index:50;
    background:rgba(242,246,243,.86);
    backdrop-filter:saturate(1.4) blur(10px);
    border-bottom:1px solid var(--line);
  }
  .bar-inner{display:flex;align-items:center;gap:20px;height:66px}
  .brand{display:flex;align-items:center;gap:12px;margin-right:auto}
  .mark{width:40px;height:40px;object-fit:contain;flex:none}
  .brand-txt{display:flex;flex-direction:column;line-height:1.15}
  .brand-txt b{font-weight:800;font-size:15px;letter-spacing:-.01em}
  .brand-txt span{font-size:11.5px;color:var(--muted);font-weight:500}
  nav.main{display:flex;gap:26px}
  nav.main a{font-size:14.5px;font-weight:500;color:var(--muted);transition:color .15s}
  nav.main a:hover{color:var(--pine)}
  .statuspill{
    display:inline-flex;align-items:center;gap:8px;
    background:var(--green-soft);color:var(--pine-deep);
    border:1px solid #bfe0cf;border-radius:100px;
    padding:7px 13px 7px 11px;font-size:12.5px;font-weight:600;
  }
  .dot{width:8px;height:8px;border-radius:50%;background:var(--verified);position:relative;flex:none}
  .dot::after{content:"";position:absolute;inset:-4px;border-radius:50%;border:1px solid var(--verified);opacity:.5;animation:pulse 2.4s ease-out infinite}
  @keyframes pulse{0%{transform:scale(.6);opacity:.6}100%{transform:scale(1.9);opacity:0}}

  /* ---------- Hero ---------- */
  .hero{padding:76px 0 60px;position:relative;overflow:hidden}
  .hero::before{
    content:"";position:absolute;top:-160px;right:-120px;width:520px;height:520px;
    background:radial-gradient(circle,rgba(201,143,28,.10),transparent 62%);pointer-events:none;
  }
  .eyebrow{
    display:inline-flex;align-items:center;gap:9px;
    font-size:13px;font-weight:600;color:var(--pine);
    background:var(--surface);border:1px solid var(--line);
    padding:6px 13px;border-radius:100px;margin-bottom:22px;
  }
  .eyebrow svg{width:15px;height:15px}
  h1{
    font-family:"Lora",Georgia,serif;
    font-weight:600;font-size:clamp(34px,4.6vw,52px);
    line-height:1.08;letter-spacing:-.015em;color:var(--pine-deep);
    margin-bottom:20px;max-width:15ch;
  }
  h1 em{font-style:italic;color:var(--gold)}
  .lede{font-size:17.5px;color:var(--muted);max-width:46ch;margin-bottom:30px}
  .cta-row{display:flex;gap:14px;flex-wrap:wrap;align-items:center}
  .btn{
    display:inline-flex;align-items:center;gap:9px;
    font-weight:600;font-size:15px;padding:13px 22px;border-radius:11px;
    border:1px solid transparent;cursor:pointer;transition:transform .12s, background .15s, box-shadow .15s;
  }
  .btn-primary{background:var(--pine);color:#fff;box-shadow:var(--shadow)}
  .btn-primary:hover{background:var(--pine-deep);transform:translateY(-1px)}
  .btn-ghost{background:transparent;color:var(--pine-deep);border-color:var(--line)}
  .btn-ghost:hover{background:var(--surface);border-color:#c5d5cd}
  .btn svg{width:17px;height:17px}

  /* ---------- Hero slider ---------- */
  .hero-slider{
    position:relative;min-height:min(660px,88vh);
    background:var(--pine-deep);color:#fff;overflow:hidden;
  }
  .hero-slider .slides{position:absolute;inset:0;z-index:1}
  .hero-slider .slide{
    position:absolute;inset:0;opacity:0;visibility:hidden;transition:opacity .9s ease,visibility .9s;
  }
  .hero-slider .slide.on{opacity:1;visibility:visible;z-index:2}
  .hero-slider .slide img{width:100%;height:100%;object-fit:cover;display:block;transform:scale(1.06);transition:transform 7s linear}
  .hero-slider .slide.on img{transform:scale(1)}
  .hero-slider .veil{
    position:absolute;inset:0;
    background:
      linear-gradient(90deg,rgba(7,35,28,.88) 0%,rgba(7,35,28,.62) 42%,rgba(7,35,28,.18) 78%,rgba(7,35,28,.05) 100%),
      linear-gradient(180deg,rgba(7,35,28,.25),transparent 35%,rgba(7,35,28,.45) 88%);
  }
  .hero-slider .cap{
    position:relative;z-index:3;min-height:inherit;display:flex;align-items:center;
    padding:92px 24px 110px;max-width:var(--wrap);margin:0 auto;
  }
  .hero-slider .slide .cap{
    position:absolute;inset:0;display:flex;align-items:center;
    max-width:var(--wrap);margin:0 auto;padding:92px 24px 110px;
    opacity:0;transform:translateY(18px);
  }
  .hero-slider .slide.on .cap{animation:rise .8s cubic-bezier(.2,.7,.3,1) forwards}
  .hero-slider .cap-inner{max-width:590px}
  .hero-slider .eyebrow{
    display:inline-flex;align-items:center;gap:9px;
    font-size:13px;font-weight:600;color:#dff0e8;
    background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.2);
    padding:6px 13px;border-radius:100px;margin-bottom:22px;backdrop-filter:blur(4px);
  }
  .hero-slider h1{
    font-family:"Lora",Georgia,serif;font-weight:600;font-size:clamp(30px,4.6vw,52px);
    line-height:1.08;letter-spacing:-.015em;color:#fff;margin-bottom:20px;max-width:15ch;
  }
  .hero-slider h1 em{font-style:italic;color:var(--gold)}
  .hero-slider .lede{font-size:17.5px;color:#cfe3d8;max-width:48ch;margin-bottom:30px}
  .hero-slider .cta-row{display:flex;gap:14px;flex-wrap:wrap;align-items:center}
  .hero-slider .btn{background:var(--pine);color:#fff;box-shadow:var(--shadow)}
  .hero-slider .btn:hover{background:var(--pine-deep)}
  .hero-slider .btn-ghost{background:rgba(255,255,255,.06);color:#fff;border-color:rgba(255,255,255,.32)}
  .hero-slider .btn-ghost:hover{background:rgba(255,255,255,.14);border-color:rgba(255,255,255,.5)}

  /* slider chrome */
  .slide-nav,.slide-dots{position:absolute;z-index:5;display:flex;align-items:center}
  .slide-nav{top:50%;transform:translateY(-50%);width:46px;height:46px;border-radius:50%;
    background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.28);color:#fff;
    cursor:pointer;justify-content:center;transition:background .15s,transform .12s}
  .slide-nav svg{width:20px;height:20px}
  .slide-nav:hover{background:rgba(255,255,255,.28)}
  .slide-nav:active{transform:translateY(-50%) scale(.94)}
  .slide-nav.prev{left:20px}.slide-nav.next{right:20px}
  .slide-dots{left:50%;bottom:26px;transform:translateX(-50%);display:flex;gap:10px}
  .slide-dots button{width:28px;height:6px;border-radius:100px;border:0;cursor:pointer;
    background:rgba(255,255,255,.32);padding:0;transition:width .25s,background .25s}
  .slide-dots button.on{width:42px;background:var(--gold)}
  .hero-slider .browseinfo{position:absolute;z-index:5;right:24px;bottom:26px;font-size:12px;
    font-weight:600;letter-spacing:.08em;color:rgba(255,255,255,.66)}

  /* verification card */
  .verify{
    background:var(--surface);border:1px solid var(--line);border-radius:20px;
    box-shadow:var(--shadow);overflow:hidden;
  }
  .verify-top{
    display:flex;align-items:center;gap:11px;
    padding:15px 20px;border-bottom:1px solid var(--line-soft);
    font-size:12.5px;font-weight:600;color:var(--muted);
  }
  .verify-top .stamp{margin-left:auto;display:inline-flex;align-items:center;gap:6px;color:var(--verified)}
  .verify-top .stamp svg{width:16px;height:16px}
  .verify-body{padding:24px 22px 22px}
  .verify-body .label{font-size:12.5px;font-weight:600;color:var(--muted);letter-spacing:.01em}
  .grade{
    font-family:"Lora",serif;font-weight:600;font-size:38px;line-height:1.1;
    color:var(--pine-deep);margin:4px 0 2px;
  }
  .verify-inst{font-size:14px;color:var(--muted);margin-bottom:20px}
  .verify-meta{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--line-soft);border:1px solid var(--line-soft);border-radius:12px;overflow:hidden}
  .verify-meta div{background:var(--surface);padding:12px 14px}
  .verify-meta dt{font-size:11.5px;color:var(--muted);font-weight:500;margin-bottom:3px}
  .verify-meta dd{font-size:13.5px;font-weight:700;letter-spacing:.02em}

  /* ---------- Stat band ---------- */
  .band{background:var(--pine-deep);color:#dff0e8}
  .band-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0}
  .stat{padding:34px 26px;border-left:1px solid rgba(255,255,255,.09)}
  .stat:first-child{border-left:none}
  .stat .n{font-size:40px;font-weight:800;letter-spacing:-.02em;color:#fff;line-height:1}
  .stat .n .u{font-size:19px;font-weight:700;color:var(--gold);margin-left:2px}
  .stat .k{font-size:13.5px;color:#a9cdbd;margin-top:9px;font-weight:500}

  /* ---------- Section scaffolding ---------- */
  section.blk{padding:82px 0}
  .head{display:flex;align-items:flex-end;justify-content:space-between;gap:30px;margin-bottom:38px}
  .head h2{font-size:clamp(25px,3vw,33px);font-weight:800;letter-spacing:-.02em;color:var(--pine-deep);line-height:1.15}
  .head p{color:var(--muted);font-size:15.5px;max-width:44ch;margin-top:10px}
  .head .side{color:var(--muted);font-size:14px;flex:none;text-align:right}

  /* ---------- Accreditation table ---------- */
  .acc-shell{background:var(--surface);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);overflow:hidden}
  .acc-controls{display:flex;gap:10px;flex-wrap:wrap;padding:16px 20px;border-bottom:1px solid var(--line-soft);align-items:center}
  .chip{font-size:13px;font-weight:600;color:var(--muted);background:var(--paper);border:1px solid var(--line);border-radius:100px;padding:7px 14px;cursor:pointer;transition:.15s}
  .chip.on,.chip:hover{background:var(--pine);color:#fff;border-color:var(--pine)}
  .acc-controls .find{margin-left:auto;font-size:13px;color:var(--muted)}
  table{width:100%;border-collapse:collapse}
  thead th{
    text-align:left;font-size:12px;font-weight:700;color:var(--muted);
    padding:14px 20px;border-bottom:1px solid var(--line);background:#fbfdfc;letter-spacing:.02em;
    user-select:none;white-space:nowrap;
  }
  thead th.sort-asc::after,
  thead th.sort-desc::after{
    content:'';
    display:inline-block;width:0;height:0;margin-left:6px;vertical-align:middle;
    border-left:5px solid transparent;border-right:5px solid transparent;
  }
  thead th.sort-asc::after{
    border-bottom:6px solid var(--pine);
  }
  thead th.sort-desc::after{
    border-top:6px solid var(--pine);
  }
  tbody td{padding:16px 20px;border-bottom:1px solid var(--line-soft);font-size:14.5px;vertical-align:middle}
  tbody tr:last-child td{border-bottom:none}
  tbody tr{transition:background .12s}
  tbody tr:hover{background:#fafdfb}
  .prodi{font-weight:700;color:var(--ink)}
  .prodi small{display:block;font-weight:500;color:var(--muted);font-size:12.5px;margin-top:2px}
  .peringkat{display:inline-flex;align-items:center;gap:7px;font-weight:700;font-size:13px;border-radius:100px;padding:6px 13px;white-space:nowrap}
  .p-unggul{background:var(--gold-soft);color:#8a6410;border:1px solid #e7cf94}
  .p-baiksekali{background:var(--green-soft);color:var(--pine-deep);border:1px solid #bfe0cf}
  .p-baik{background:#eaf0ee;color:#3c534b;border:1px solid #d3e0d9}
  .p-proses{background:var(--amber-soft);color:var(--amber);border:1px solid #ecd6a6}
  .peringkat svg{width:14px;height:14px}
  .lam{font-size:12.5px;color:var(--muted)}
  td.berlaku{font-weight:600;letter-spacing:.02em;color:var(--ink)}

  /* ---------- PPEPP cycle ---------- */
  .cycle{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;counter-reset:step}
  .step{
    background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:24px 20px;position:relative;
  }
  .step::before{
    counter-increment:step;content:counter(step);
    font-family:"Lora",serif;font-weight:600;font-size:15px;
    width:34px;height:34px;border-radius:9px;background:var(--pine);color:#fff;
    display:grid;place-items:center;margin-bottom:16px;
  }
  .step:nth-child(5)::before{background:var(--gold)}
  .step h3{font-size:16px;font-weight:800;color:var(--pine-deep);margin-bottom:7px;letter-spacing:-.01em}
  .step p{font-size:13.5px;color:var(--muted);line-height:1.5}
  .step .arrow{position:absolute;right:-11px;top:34px;color:var(--line);z-index:2}
  .step:last-child .arrow{display:none}
  .step .arrow svg{width:20px;height:20px}

  /* ---------- Activity feed ---------- */
  .feed{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .act{
    display:flex;gap:16px;background:var(--surface);border:1px solid var(--line);
    border-radius:15px;padding:20px;transition:border-color .15s, transform .12s;
  }
  .act:hover{border-color:#c5d5cd;transform:translateY(-1px)}
  .act .date{
    flex:none;width:58px;text-align:center;border-right:1px solid var(--line-soft);padding-right:14px;
  }
  .act .date .d{font-size:24px;font-weight:800;color:var(--pine-deep);line-height:1}
  .act .date .m{font-size:11.5px;font-weight:600;color:var(--muted);margin-top:3px}
  .act .body{flex:1}
  .tagrow{display:flex;gap:8px;align-items:center;margin-bottom:7px;flex-wrap:wrap}
  .tag{font-size:11px;font-weight:700;letter-spacing:.03em;padding:3px 9px;border-radius:6px}
  .t-ami{background:var(--green-soft);color:var(--pine-deep)}
  .t-monev{background:#e6eefc;color:#22467e}
  .t-rtm{background:var(--gold-soft);color:#8a6410}
  .t-survei{background:#f3e9f6;color:#6b3480}
  .status-lbl{font-size:11.5px;font-weight:600;color:var(--verified);display:inline-flex;align-items:center;gap:5px;margin-left:auto}
  .status-lbl.run{color:var(--amber)}
  .status-lbl svg{width:13px;height:13px}
  .act h3{font-size:15.5px;font-weight:700;color:var(--ink);line-height:1.35;margin-bottom:5px}
  .act p{font-size:13px;color:var(--muted)}

  /* ---------- Documents ---------- */
  .docs-wrap{display:grid;grid-template-columns:1.4fr 1fr;gap:40px;align-items:start}
  .doclist{background:var(--surface);border:1px solid var(--line);border-radius:16px;overflow:hidden;box-shadow:var(--shadow)}
  .doc{display:flex;align-items:center;gap:16px;padding:18px 20px;border-bottom:1px solid var(--line-soft);transition:background .12s}
  .doc:last-child{border-bottom:none}
  .doc:hover{background:#fafdfb}
  .doc-ico{width:40px;height:40px;border-radius:10px;background:var(--green-soft);color:var(--pine);display:grid;place-items:center;flex:none}
  .doc-ico svg{width:20px;height:20px}
  .doc .t{flex:1}
  .doc .t b{font-size:14.5px;font-weight:700;display:block}
  .doc .t span{font-size:12.5px;color:var(--muted)}
  .doc .dl{color:var(--muted);flex:none;transition:color .15s}
  .doc:hover .dl{color:var(--pine)}
  .doc .dl svg{width:19px;height:19px}
  .callout{
    background:linear-gradient(160deg,var(--pine),var(--pine-deep));color:#dff0e8;
    border-radius:16px;padding:30px 28px;position:relative;overflow:hidden;
  }
  .callout::after{content:"";position:absolute;right:-40px;bottom:-40px;width:160px;height:160px;border-radius:50%;background:rgba(201,143,28,.16)}
  .callout .q{font-family:"Lora",serif;font-size:20px;line-height:1.4;color:#fff;margin-bottom:18px;position:relative}
  .callout p{font-size:13.5px;color:#a9cdbd;margin-bottom:22px;position:relative}
  .callout a{display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:#231a05;font-weight:700;font-size:14px;padding:11px 18px;border-radius:10px;position:relative}
  .callout a svg{width:16px;height:16px}

  /* ---------- Footer ---------- */
  footer{background:var(--ink);color:#b9ccc3;padding:60px 0 30px;margin-top:20px}
  .foot-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr;gap:40px;padding-bottom:40px;border-bottom:1px solid rgba(255,255,255,.08)}
  .foot-brand b{color:#fff;font-size:16px;font-weight:800;display:block;margin:14px 0 8px}
  .foot-brand p{font-size:13.5px;max-width:38ch;line-height:1.6}
  .foot-col h4{color:#fff;font-size:13px;font-weight:700;margin-bottom:14px;letter-spacing:.02em}
  .foot-col a{display:block;font-size:13.5px;margin-bottom:9px;color:#b9ccc3;transition:color .15s}
  .foot-col a:hover{color:var(--gold)}
  .foot-bottom{display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;padding-top:24px;font-size:12.5px;color:#7f978c}

  a:focus-visible,button:focus-visible,.chip:focus-visible{outline:2px solid var(--gold);outline-offset:3px;border-radius:8px}

  /* ---------- Responsive ---------- */
  @media(max-width:900px){
    nav.main,.statuspill{display:none}
    .band-grid{grid-template-columns:1fr 1fr}
    .stat{border-left:none;border-top:1px solid rgba(255,255,255,.09)}
    .stat:first-child,.stat:nth-child(2){border-top:none}
    .cycle{grid-template-columns:1fr 1fr}
    .step .arrow{display:none}
    .feed,.docs-wrap{grid-template-columns:1fr}
    .foot-grid{grid-template-columns:1fr}
  }
  @media(max-width:560px){
    .hero-slider{min-height:min(560px,92vh)}
    .hero-slider .cap{padding:88px 20px 100px}
    .slide-nav{width:38px;height:38px}
    .slide-nav.prev{left:8px}.slide-nav.next{right:8px}
    .band-grid{grid-template-columns:1fr 1fr}
    .cycle{grid-template-columns:1fr}
    .acc-shell{overflow-x:auto}
    table{min-width:640px}
  }
  @media(prefers-reduced-motion:reduce){
    *{animation:none!important;transition:none!important;scroll-behavior:auto}
  }

  /* one orchestrated load reveal on hero */
  .reveal{opacity:0;transform:translateY(14px);animation:rise .7s cubic-bezier(.2,.7,.3,1) forwards}
  .reveal.d1{animation-delay:.05s}
  .reveal.d2{animation-delay:.15s}
  .reveal.d3{animation-delay:.25s}
  @keyframes rise{to{opacity:1;transform:none}}
</style>
</head>
<body>

<!-- ===== Top bar ===== -->
<header class="bar">
  <div class="wrap bar-inner">
    <a class="brand" href="#">
      <img class="mark" src="asset/foto/logo.png" alt="Logo UM Bandung" />
      <span class="brand-txt">
        <b>Lembaga Penjaminan Mutu</b>
        <span>Universitas Muhammadiyah Bandung</span>
      </span>
    </a>
    <nav class="main">
      <a href="#status">Status Akreditasi</a>
      <a href="#siklus">Siklus Mutu</a>
      <a href="#kegiatan">Kegiatan</a>
      <a href="#dokumen">Dokumen</a>
    </nav>
    <span class="statuspill"><span class="dot"></span>Status institusi: Baik Sekali</span>
  </div>
</header>

<!-- ===== Hero slider ===== -->
<section class="hero-slider" id="hero" aria-label="Sorotan">
  <div class="slides">

    <div class="slide on">
      <img src="asset/slide/slide1.jpg" alt="Kampus Universitas Muhammadiyah Bandung" />
      <div class="veil"></div>
      <div class="cap">
        <div class="cap-inner">
          <span class="eyebrow reveal d1">Transparansi mutu untuk publik</span>
          <h1 class="reveal d1">Mutu yang bisa Anda <em>periksa</em>, bukan sekadar diklaim.</h1>
          <p class="lede reveal d2">Portal resmi status dan kegiatan penjaminan mutu Universitas Muhammadiyah Bandung. Ditujukan bagi calon mahasiswa, orang tua, mitra kerja sama, dan pemangku kepentingan untuk memantau capaian mutu secara terbuka dan berkelanjutan.</p>
          <div class="cta-row reveal d3">
            <a class="btn btn-primary" href="#status">Lihat status akreditasi
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a class="btn btn-ghost" href="#dokumen">Unduh laporan mutu
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="slide">
      <img src="asset/slide/slide2.jpg" alt="Mahasiswa berdiskusi dalam kelompok belajar" />
      <div class="veil"></div>
      <div class="cap">
        <div class="cap-inner">
          <span class="eyebrow">Siklus Penjaminan Mutu</span>
          <h1>PPEPP: Perencanaan, Pelaksanaan, Evaluasi, <em>Pengendalian</em>, Peningkatan</h1>
          <p class="lede">LPM mengawal seluruh siklus mutu di setiap prodi — dari target hingga tindak lanjut — agar peningkatan berjalan berkelanjutan, bukan hanya seremonial.</p>
          <div class="cta-row">
            <a class="btn btn-primary" href="#siklus">Pahami siklus mutu
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="slide">
      <img src="asset/slide/slide3.jpg" alt="Perpustakaan dan ruang belajar kampus" />
      <div class="veil"></div>
      <div class="cap">
        <div class="cap-inner">
          <span class="eyebrow">Sumber Daya</span>
          <h1><?php echo (int)$jumlahProdi; ?> prodi, 300+ dosen, satu standar <em>mutu</em>.</h1>
          <p class="lede">Seluruh program studi dan tenaga pendidik berada dalam satu sistem pemantauan mutu yang konsisten, independen, dan terdokumentasi.</p>
          <div class="cta-row">
            <a class="btn btn-primary" href="#status">Lihat status prodi
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a class="btn btn-ghost" href="#dokumen">Standar &amp; borang
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="slide">
      <img src="asset/slide/slide4.jpg" alt="Suasana kegiatan akademik di kampus" />
      <div class="veil"></div>
      <div class="cap">
        <div class="cap-inner">
          <span class="eyebrow">Kegiatan Mutu</span>
          <h1>Audit, survei, dan <em>tindak lanjut</em> yang terbuka.</h1>
          <p class="lede">Pantau agenda audit internal, monev, dan survei kepuasan — lengkap dengan statusnya, diperbarui secara berkala oleh LPM.</p>
          <div class="cta-row">
            <a class="btn btn-primary" href="#kegiatan">Jelajahi kegiatan
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="slide">
      <img src="asset/slide/slide5.jpg" alt="Wisuda dan pencapaian lulusan Universitas Muhammadiyah Bandung" />
      <div class="veil"></div>
      <div class="cap">
        <div class="cap-inner">
          <span class="eyebrow">Akreditasi &amp; Pengakuan</span>
          <h1>Terakreditasi <em>Baik Sekali</em> dan terus naik kelas.</h1>
          <p class="lede">Status akreditasi institusi dan setiap prodi dapat diverifikasi langsung melalui BAN-PT dan PDDikti. Data ditampilkan apa adanya.</p>
          <div class="cta-row">
            <a class="btn btn-primary" href="#status">Periksa akreditasi
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            <a class="btn btn-ghost" href="#dokumen">Dokumen pendukung
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>

  <button class="slide-nav prev" aria-label="Slide sebelumnya">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 6l-6 6 6 6"/></svg>
  </button>
  <button class="slide-nav next" aria-label="Slide berikutnya">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 6l6 6-6 6"/></svg>
  </button>
  <div class="slide-dots" role="tablist" aria-label="Pilih slide"></div>
  <span class="browseinfo">01 — 05</span>
</section>

<!-- ===== Stat band ===== -->
<section class="band">
  <div class="wrap band-grid">
    <div class="stat"><div class="n tnum"><?php echo (int)$jumlahProdi; ?><span class="u">prodi</span></div><div class="k">Program studi terpantau</div></div>
    <div class="stat"><div class="n tnum"><?php echo $persenBS_U; ?><span class="u">%</span></div><div class="k">Prodi terakreditasi Baik Sekali / Unggul</div></div>
    <div class="stat"><div class="n tnum">300<span class="u">+</span></div><div class="k">Dosen dalam pemantauan mutu</div></div>
    <div class="stat"><div class="n tnum">86<span class="u">%</span></div><div class="k">Indeks kepuasan pemangku kepentingan</div></div>
  </div>
</section>

<!-- ===== Accreditation status ===== -->
<section class="blk" id="status">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Status akreditasi program studi</h2>
        <p>Peringkat resmi setiap program studi beserta lembaga penilai dan masa berlaku. Diperbarui mengikuti keputusan BAN-PT dan LAM.</p>
      </div>
      
    </div>

    <div class="acc-shell">
      <div class="acc-controls">
        <span class="chip on">Semua</span>
        <span class="chip">Unggul</span>
        <span class="chip">Baik Sekali</span>
        <span class="chip">Baik</span>
        <span class="chip">Dalam proses</span>
        <span class="find"><?= $jumlahProdi ?> program studi ditampilkan</span>
      </div>
      <table>
        <thead>
          <tr><th>Program studi</th><th>Lembaga</th><th>Peringkat</th><th class="berlaku">Berlaku s.d.</th></tr>
        </thead>
        <tbody>
        <?php
        try {
            require_once __DIR__ . '/koneksi.php';
            $stmt = $pdo->query("SELECT nama, jenjang, lembaga, akreditasi, berlaku_sampai FROM ak_prodi ORDER BY nama");
            $prodiList = $stmt->fetchAll();
            foreach ($prodiList as $row) {
                $akreditasi = $row['akreditasi'];
                $kelas = match ($akreditasi) {
                    'Unggul' => 'p-unggul',
                    'Baik Sekali' => 'p-baiksekali',
                    'Baik' => 'p-baik',
                    default => 'p-proses',
                };
                $icon = '';
                if ($akreditasi === 'Unggul') {
                    $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 3l2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5z"/></svg>';
                } elseif ($akreditasi === 'Dalam proses') {
                    $icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';
                }
                $lembaga = $row['lembaga'] ?: '—';
                $berlaku = $row['berlaku_sampai'] ?: '—';
                $jenjangLabel = match ($row['jenjang']) {
                    'S1' => 'Sarjana (S1)',
                    'S2' => 'Magister (S2)',
                    'S3' => 'Doktor (S3)',
                    'Profesi' => 'Profesi',
                    default => $row['jenjang'],
                };
                echo '<tr>';
                echo '<td class="prodi">' . htmlspecialchars($row['nama']) . ' <small>' . htmlspecialchars($jenjangLabel) . '</small></td>';
                echo '<td class="lam">' . htmlspecialchars($lembaga) . '</td>';
                echo '<td><span class="peringkat ' . $kelas . '">' . $icon . htmlspecialchars($akreditasi) . '</span></td>';
                echo '<td class="berlaku">' . htmlspecialchars($berlaku) . '</td>';
                echo '</tr>';
            }
        } catch (Throwable $e) {
            // Fallback row jika DB error
            echo '<tr><td colspan="4" style="text-align:center;color:var(--muted)">Gagal memuat data dari database</td></tr>';
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ===== PPEPP cycle ===== -->
<section class="blk" id="siklus" style="background:var(--surface);border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Bagaimana mutu kami dijaga</h2>
        <p>Sistem Penjaminan Mutu Internal (SPMI) berjalan dalam satu siklus berkelanjutan — dikenal sebagai PPEPP — yang berulang setiap tahun akademik.</p>
      </div>
    </div>
    <div class="cycle">
      <div class="step">
        <h3>Penetapan</h3>
        <p>Menetapkan standar mutu mengacu SN-Dikti, standar tambahan, dan nilai Al-Islam Kemuhammadiyahan.</p>
        <span class="arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </div>
      <div class="step">
        <h3>Pelaksanaan</h3>
        <p>Standar diterapkan pada seluruh proses akademik dan layanan di tiap program studi.</p>
        <span class="arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </div>
      <div class="step">
        <h3>Evaluasi</h3>
        <p>Audit Mutu Internal (AMI) dan monitoring mengukur keterlaksanaan standar secara objektif.</p>
        <span class="arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </div>
      <div class="step">
        <h3>Pengendalian</h3>
        <p>Temuan ditindaklanjuti melalui Rapat Tinjauan Manajemen dan rencana perbaikan.</p>
        <span class="arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
      </div>
      <div class="step">
        <h3>Peningkatan</h3>
        <p>Standar dinaikkan pada siklus berikutnya sehingga mutu terus tumbuh dari tahun ke tahun.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== Activity feed ===== -->
<section class="blk" id="kegiatan">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Kegiatan mutu terkini</h2>
        <p>Rekam jejak audit, monitoring, dan evaluasi yang berlangsung — terbuka untuk ditinjau publik.</p>
      </div>
      <div class="side"><a href="#" style="color:var(--pine);font-weight:600">Lihat arsip kegiatan →</a></div>
    </div>
    <div class="feed">
      <article class="act">
        <div class="date"><div class="d tnum">28</div><div class="m">Agu 2026</div></div>
        <div class="body">
          <div class="tagrow"><span class="tag t-ami">AMI</span><span class="status-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l5 5 9-11"/></svg>Selesai</span></div>
          <h3>Audit Mutu Internal siklus 2025/2026</h3>
          <p>Audit terhadap 14 program studi pada 24 standar Dikti dan standar tambahan AIK.</p>
        </div>
      </article>
      <article class="act">
        <div class="date"><div class="d tnum">15</div><div class="m">Agu 2026</div></div>
        <div class="body">
          <div class="tagrow"><span class="tag t-survei">SURVEI</span><span class="status-lbl run"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>Berjalan</span></div>
          <h3>Survei kepuasan pemangku kepentingan</h3>
          <p>Pengukuran kepuasan mahasiswa, alumni, dan mitra terhadap layanan dan lulusan.</p>
        </div>
      </article>
      <article class="act">
        <div class="date"><div class="d tnum">30</div><div class="m">Jul 2026</div></div>
        <div class="body">
          <div class="tagrow"><span class="tag t-rtm">RTM</span><span class="status-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l5 5 9-11"/></svg>Selesai</span></div>
          <h3>Rapat Tinjauan Manajemen semester genap</h3>
          <p>Tindak lanjut temuan audit dan penetapan rencana perbaikan mutu tingkat universitas.</p>
        </div>
      </article>
      <article class="act">
        <div class="date"><div class="d tnum">12</div><div class="m">Jul 2026</div></div>
        <div class="body">
          <div class="tagrow"><span class="tag t-monev">MONEV</span><span class="status-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12l5 5 9-11"/></svg>Selesai</span></div>
          <h3>Monitoring & evaluasi pembelajaran</h3>
          <p>Peninjauan keterlaksanaan RPS dan capaian pembelajaran lulusan tiap prodi.</p>
        </div>
      </article>
    </div>
  </div>
</section>

<!-- ===== Documents ===== -->
<section class="blk" id="dokumen" style="background:var(--surface);border-top:1px solid var(--line)">
  <div class="wrap">
    <div class="head">
      <div>
        <h2>Dokumen &amp; standar mutu</h2>
        <p>Dokumen resmi SPMI yang menjadi acuan seluruh kegiatan penjaminan mutu, terbuka untuk diunduh.</p>
      </div>
    </div>
    <div class="docs-wrap">
      <div class="doclist">
        <a class="doc" href="#">
          <span class="doc-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg></span>
          <span class="t"><b>Kebijakan SPMI</b><span>PDF · 1,2 MB · Revisi 2025</span></span>
          <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg></span>
        </a>
        <a class="doc" href="#">
          <span class="doc-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg></span>
          <span class="t"><b>Manual Mutu</b><span>PDF · 2,4 MB · Revisi 2025</span></span>
          <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg></span>
        </a>
        <a class="doc" href="#">
          <span class="doc-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg></span>
          <span class="t"><b>Standar Mutu (SN-Dikti + Tambahan)</b><span>PDF · 3,1 MB · Revisi 2025</span></span>
          <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg></span>
        </a>
        <a class="doc" href="#">
          <span class="doc-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg></span>
          <span class="t"><b>Laporan Audit Mutu Internal 2025/2026</b><span>PDF · 4,8 MB · Publik</span></span>
          <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg></span>
        </a>
        <a class="doc" href="#">
          <span class="doc-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/></svg></span>
          <span class="t"><b>Formulir &amp; instrumen mutu</b><span>ZIP · 0,9 MB · Diperbarui</span></span>
          <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4v11M8 11l4 4 4-4"/><path d="M5 19h14"/></svg></span>
        </a>
      </div>
      <aside class="callout">
        <p class="q">“Penjaminan mutu bukan tujuan akhir, melainkan cara kami menjaga kepercayaan yang Anda berikan.”</p>
        <p>Punya pertanyaan tentang status mutu, kerja sama, atau data akreditasi tertentu? Tim LPM siap membantu pemangku kepentingan.</p>
        <a href="#kontak">Hubungi LPM
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </aside>
    </div>
  </div>
</section>

<!-- ===== Footer ===== -->
<footer id="kontak">
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <img class="mark" src="asset/foto/logo.png" alt="Logo UM Bandung" style="width:48px;height:48px;object-fit:contain;margin-bottom:8px" />
        <b>Lembaga Penjaminan Mutu</b>
        <p>Universitas Muhammadiyah Bandung<br>Jl. Soekarno-Hatta No. 752, Bandung, Jawa Barat<br>lpm@umbandung.ac.id · (022) xxx-xxxx</p>
      </div>
      <div class="foot-col">
        <h4>Jelajahi</h4>
        <a href="#status">Status akreditasi</a>
        <a href="#siklus">Siklus penjaminan mutu</a>
        <a href="#kegiatan">Kegiatan mutu</a>
        <a href="#dokumen">Dokumen &amp; standar</a>
      </div>
      <div class="foot-col">
        <h4>Tautan resmi</h4>
        <a href="#">BAN-PT</a>
        <a href="#">PDDikti</a>
        <a href="#">Universitas Muhammadiyah Bandung</a>
        <a href="#">Layanan pengaduan mutu</a>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© 2026 Lembaga Penjaminan Mutu UM Bandung. Seluruh data mutu diperbarui berkala.</span>
      <span>Halaman contoh (mockup) — angka dan status akreditasi bersifat ilustratif.</span>
    </div>
  </div>
</footer>

<script>
  // ===== Hero slider =====
  (function(){
    var wrap=document.querySelector('.hero-slider');
    if(!wrap)return;
    var slides=Array.prototype.slice.call(wrap.querySelectorAll('.slide'));
    var dotsBox=wrap.querySelector('.slide-dots');
    var info=wrap.querySelector('.browseinfo');
    var t=0, auto;

    slides.forEach(function(s,i){
      var b=document.createElement('button');
      b.setAttribute('role','tab');
      b.setAttribute('aria-label','Slide '+(i+1));
      b.addEventListener('click',function(){go(i);reset();});
      dotsBox.appendChild(b);
    });
    var dots=Array.prototype.slice.call(dotsBox.children);

    function go(i){
      t=(i+slides.length)%slides.length;
      slides.forEach(function(s,k){s.classList.toggle('on',k===t);});
      dots.forEach(function(d,k){d.classList.toggle('on',k===t);d.setAttribute('aria-selected',k===t);});
      var n=String(t+1).padStart(2,'0');
      info.textContent=n+' — 0'+slides.length;
    }
    function reset(){clearInterval(auto);auto=setInterval(function(){go(t+1);},6000);}

    wrap.querySelector('.prev').addEventListener('click',function(){go(t-1);reset();});
    wrap.querySelector('.next').addEventListener('click',function(){go(t+1);reset();});

    document.addEventListener('keydown',function(e){
      if(!wrap.contains(document.activeElement))return;
      if(e.key==='ArrowLeft'){go(t-1);reset();}
      if(e.key==='ArrowRight'){go(t+1);reset();}
    });

    /* pause on hover/focus/touch */
    ['mouseenter','focusin','touchstart'].forEach(function(ev){
      wrap.addEventListener(ev,function(){clearInterval(auto);});
    });
    ['mouseleave','focusout','touchend'].forEach(function(ev){
      wrap.addEventListener(ev,function(){auto=setInterval(function(){go(t+1);},6000);});
    });

    go(0);reset();
  })();

  // Filter chips (visual demo only)
  document.querySelectorAll('.chip').forEach(c=>{
    c.addEventListener('click',()=>{
      document.querySelectorAll('.chip').forEach(x=>x.classList.remove('on'));
      c.classList.add('on');
    });
  });

  // Table sorting
  (function(){
    const table = document.querySelector('#status table');
    if (!table) return;
    const headers = table.querySelectorAll('thead th');
    let sortDir = 1;
    let lastCol = -1;

    headers.forEach((th, colIdx) => {
      th.style.cursor = 'pointer';
      th.title = 'Klik untuk mengurutkan';
      th.addEventListener('click', () => {
        if (colIdx === lastCol) {
          sortDir *= -1;
        } else {
          sortDir = 1;
          lastCol = colIdx;
        }
        headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
        th.classList.add(sortDir === 1 ? 'sort-asc' : 'sort-desc');

        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
          const aText = a.cells[colIdx].textContent.trim();
          const bText = b.cells[colIdx].textContent.trim();
          // numeric check
          const aNum = parseFloat(aText.replace(/[^\d.-]/g, ''));
          const bNum = parseFloat(bText.replace(/[^\d.-]/g, ''));
          if (!isNaN(aNum) && !isNaN(bNum)) {
            return (aNum - bNum) * sortDir;
          }
          return aText.localeCompare(bText, 'id', {numeric: true}) * sortDir;
        });
        rows.forEach(r => tbody.appendChild(r));
      });
    });
  })();
</script>
</body>
</html>
