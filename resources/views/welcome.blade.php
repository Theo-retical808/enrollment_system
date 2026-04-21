<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Universidad de Dagupan — Enrollment System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:     #0c1660;
      --navy-mid: #152080;
      --navy-lit: #1c2ea0;
      --blue:     #1a56db;
      --blue-lit: #3b82f6;
      --white:    #ffffff;
      --gray-50:  #f8f9fc;
      --gray-100: #eef0f6;
      --gray-300: #c4cade;
      --gray-500: #7480a0;
      --gray-700: #3d4a6b;
      --text:     #111827;
    }

    html, body {
      height: 100%;
      font-family: 'Figtree', sans-serif;
      background: var(--gray-50);
      color: var(--text);
    }

    .layout {
      display: flex;
      min-height: 100vh;
    }

    /* ── LEFT PANEL ── */
    .left {
      width: 550px;
      flex-shrink: 0;
      background: var(--navy);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 64px 60px;
      overflow: hidden;
    }

    .left::before {
      content: '';
      position: absolute;
      bottom: -140px; right: -140px;
      width: 400px; height: 400px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(26,86,219,.3) 0%, transparent 70%);
      pointer-events: none;
    }

    .left::after {
      content: '';
      position: absolute;
      top: -80px; left: -80px;
      width: 260px; height: 260px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(28,46,160,.5) 0%, transparent 70%);
      pointer-events: none;
    }

    .left-body {
      position: relative;
      z-index: 1;
      animation: fadeLeft .55s ease both;
    }

    .seal-wrap {
      display: flex;
      align-items: center;
      gap: 13px;
      margin-bottom: 56px;
    }

    .seal-circle {
      width: 72px; height: 72px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      background: rgba(255, 255, 255, 0.05);
      border: 1.5px solid rgba(255, 255, 255, 0.15);
      border-radius: 50%;
      padding: 12px;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
    }

    .seal-logo {
      width: 100%;
      height: 100%;
      object-fit: contain;
      filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.1));
    }

    .seal-name {
      font-size: 13px;
      font-weight: 700;
      color: var(--white);
      line-height: 1.25;
    }

    .seal-sub {
      font-size: 10px;
      font-weight: 400;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: rgba(255,255,255,.4);
      margin-top: 3px;
    }

    .left-label {
      font-size: 10.5px;
      font-weight: 600;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: rgba(255,255,255,.4);
      margin-bottom: 14px;
    }

    .left-heading {
      font-size: 48px;
      font-weight: 800;
      color: var(--white);
      line-height: 1.1;
      letter-spacing: -.02em;
      margin-bottom: 24px;
    }

    .left-heading .accent { color: #60a5fa; }

    .left-desc {
      font-size: 15px;
      font-weight: 400;
      color: rgba(255,255,255,.5);
      line-height: 1.7;
      max-width: 380px;
    }

    .iso-badge {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
      gap: 9px;
      margin-top: 44px;
      padding: 10px 16px;
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 8px;
      background: rgba(255,255,255,.04);
    }

    .iso-dot {
      width: 7px; height: 7px;
      border-radius: 50%;
      background: #34d399;
      box-shadow: 0 0 7px #34d399;
      flex-shrink: 0;
    }

    .iso-text {
      font-size: 11.5px;
      font-weight: 600;
      color: rgba(255,255,255,.6);
      letter-spacing: .03em;
    }

    .left-footer {
      position: relative;
      z-index: 1;
      font-size: 11px;
      color: rgba(255,255,255,.22);
      letter-spacing: .05em;
      animation: fadeLeft .55s ease .1s both;
    }

    /* ── RIGHT PANEL ── */
    .right {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 48px 64px;
      background: var(--white);
      animation: fadeRight .55s ease both;
    }

    .right-inner {
      width: 100%;
      max-width: 380px;
    }

    .right-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: var(--blue);
      margin-bottom: 14px;
    }

    .right-eyebrow::before {
      content: '';
      width: 16px; height: 2px;
      background: var(--blue);
      border-radius: 2px;
    }

    .right-heading {
      font-size: 26px;
      font-weight: 800;
      color: var(--navy);
      letter-spacing: -.02em;
      margin-bottom: 7px;
      line-height: 1.1;
    }

    .right-sub {
      font-size: 14px;
      color: var(--gray-500);
      font-weight: 400;
      margin-bottom: 36px;
      line-height: 1.5;
    }

    /* ── Portal Buttons ── */
    .portal-btn {
      display: flex;
      align-items: center;
      width: 100%;
      padding: 17px 20px;
      border-radius: 12px;
      border: 1.5px solid var(--gray-100);
      background: var(--gray-50);
      cursor: pointer;
      text-decoration: none;
      color: var(--text);
      transition: border-color .2s, background .2s, transform .2s, box-shadow .2s;
      margin-bottom: 12px;
      gap: 16px;
    }

    .portal-btn:last-of-type { margin-bottom: 0; }

    .portal-btn:hover {
      border-color: var(--blue);
      background: #eff6ff;
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(26,86,219,.1);
    }

    .portal-btn--primary {
      background: var(--navy);
      border-color: var(--navy);
      color: var(--white);
    }

    .portal-btn--primary:hover {
      background: var(--navy-lit);
      border-color: var(--navy-lit);
      box-shadow: 0 8px 28px rgba(12,22,96,.25);
    }

    .btn-icon {
      width: 42px; height: 42px;
      border-radius: 9px;
      background: rgba(26,86,219,.1);
      display: flex; align-items: center; justify-content: center;
      font-size: 19px;
      flex-shrink: 0;
    }

    .portal-btn--primary .btn-icon { background: rgba(255,255,255,.14); }

    .btn-content { flex: 1; text-align: left; }

    .btn-title {
      font-size: 14.5px;
      font-weight: 700;
      display: block;
      margin-bottom: 2px;
      letter-spacing: -.01em;
    }

    .btn-desc {
      font-size: 12px;
      font-weight: 400;
      color: var(--gray-500);
      display: block;
    }

    .portal-btn--primary .btn-desc { color: rgba(255,255,255,.5); }

    .btn-arrow {
      font-size: 16px;
      color: var(--gray-300);
      transition: transform .2s, color .2s;
      flex-shrink: 0;
    }

    .portal-btn:hover .btn-arrow { transform: translateX(4px); color: var(--blue); }
    .portal-btn--primary .btn-arrow { color: rgba(255,255,255,.35); }
    .portal-btn--primary:hover .btn-arrow { color: rgba(255,255,255,.75); transform: translateX(4px); }

    .separator {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 20px 0;
      font-size: 11.5px;
      font-weight: 500;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--gray-300);
    }

    .separator::before,
    .separator::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--gray-100);
    }

    .right-footer {
      margin-top: 40px;
      font-size: 11.5px;
      color: var(--gray-500);
      text-align: center;
      line-height: 1.75;
    }

    .right-footer a {
      color: var(--blue);
      text-decoration: none;
      font-weight: 500;
    }

    .right-footer a:hover { text-decoration: underline; }

    /* ── Animations ── */
    @keyframes fadeLeft {
      from { opacity: 0; transform: translateX(-18px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeRight {
      from { opacity: 0; transform: translateX(18px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    /* ── Mobile ── */
    @media (max-width: 720px) {
      .layout { flex-direction: column; }
      .left { width: 100%; padding: 36px 28px; }
      .right { padding: 40px 28px; }
    }
  </style>
</head>
<body>

<div class="layout">

  <!-- LEFT -->
  <aside class="left">
    <div class="left-body">
      <div class="seal-wrap">
        <div class="seal-circle">
          <img src="{{ asset('images/udd_logo.PNG') }}" alt="UDD Logo" class="seal-logo">
        </div>
        <div>
          <div class="seal-name">Universidad de Dagupan</div>
          <div class="seal-sub">Formerly Colegio de Dagupan</div>
        </div>
      </div>

      <p class="left-label">Enrollment System</p>
      <h1 class="left-heading">
        Official<br/>
        <span class="accent">Enrollment</span><br/>
        System
      </h1>
      <p class="left-desc">
        Empowering the UDD community with a modernized, efficient, and secure digital enrollment experience.
      </p>

      <div class="iso-badge">
        <div class="iso-dot"></div>
        <div class="iso-text">ISO 21001:2018 Certified</div>
      </div>
    </div>

    <div class="left-footer">
      © {{ date('Y') }} Universidad de Dagupan &nbsp;·&nbsp; All rights reserved
    </div>
  </aside>

  <!-- RIGHT -->
  <main class="right">
    <div class="right-inner">

      <p class="right-eyebrow">System Access</p>
      <h2 class="right-heading">Begin Enrollment</h2>
      <p class="right-sub">Select your account type to proceed with the enrollment process.</p>

      <a href="{{ route('student.login') }}" class="portal-btn portal-btn--primary">
        <div class="btn-icon">🎓</div>
        <div class="btn-content">
          <span class="btn-title">Student Enrollment</span>
          <span class="btn-desc">Register for classes, schedules &amp; subjects</span>
        </div>
        <span class="btn-arrow">→</span>
      </a>

      <div class="separator">or</div>

      <a href="{{ route('professor.login') }}" class="portal-btn">
        <div class="btn-icon">🛡️</div>
        <div class="btn-content">
          <span class="btn-title">Faculty Access</span>
          <span class="btn-desc">Manage advising, class capacities &amp; student records</span>
        </div>
        <span class="btn-arrow">→</span>
      </a>

      <div class="right-footer">
        Need help? <a href="#">Contact the Registrar</a><br/>
        <a href="https://udd.edu.ph" target="_blank">udd.edu.ph</a> &nbsp;·&nbsp; <a href="#">Privacy Policy</a>
      </div>

    </div>
  </main>

</div>

</body>
</html>