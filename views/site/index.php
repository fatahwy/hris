<?php
use app\models\master\Paket;
use yii\helpers\Url;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pranata HR – Absensi, Cuti, dan Gaji UMKM dalam Satu Aplikasi</title>
    <link rel="icon" type="image/webp" href="data:image/webp;base64,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">
    <meta name="description" content="HRIS untuk UMKM: absensi, izin & cuti, payroll dengan PPh 21 dan BPJS otomatis. Coba gratis 14 hari.">
    <style>
        :root {
            --ink: #0A345D;
            --bg: #F6F9FC;
            --white: #fff;
            --mint: #C2570F;
            --mintbg: #FDEBDD;
            --sun: #F47A27;
            --muted: #455B73;
            --line: #D7E0EA;
            --red: #B42318;
            --redbg: #FDECEA
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html {
            scroll-behavior: smooth
        }

        body {
            font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            font-size: 17px
        }

        a {
            color: inherit
        }

        .wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 22px
        }

        h1,
        h2,
        h3 {
            line-height: 1.15;
            letter-spacing: -.02em
        }

        h1 {
            font-size: clamp(2.1rem, 5.2vw, 3.5rem);
            font-weight: 850
        }

        h2 {
            font-size: clamp(1.7rem, 3.6vw, 2.4rem);
            font-weight: 800;
            margin-bottom: 12px
        }

        h3 {
            font-size: 1.2rem;
            font-weight: 750
        }

        section {
            padding: 76px 0
        }

        .lead {
            color: var(--muted);
            font-size: 1.12rem;
            max-width: 56ch
        }

        .btn {
            display: inline-block;
            padding: 14px 26px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            border: 2px solid var(--sun);
            background: var(--sun);
            color: var(--ink);
            transition: transform .15s, box-shadow .15s
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(244, 122, 39, .35)
        }

        .btn.alt {
            background: transparent;
            color: var(--ink);
            border-color: var(--ink)
        }

        .btn:focus-visible,
        a:focus-visible,
        summary:focus-visible,
        button:focus-visible {
            outline: 3px solid var(--sun);
            outline-offset: 3px
        }

        header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(246, 249, 252, .96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--line);
            transition: all 0.2s ease;
        }

        header .wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: relative;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 850;
            font-size: 1.3rem;
            text-decoration: none;
            letter-spacing: -.03em;
            color: var(--ink);
            z-index: 101;
        }

        .logo img {
            height: 38px;
            width: auto;
        }

        .desktop-nav {
            display: flex;
            gap: 24px;
            align-items: center;
            font-size: .95rem;
            font-weight: 600;
        }

        .desktop-nav a {
            text-decoration: none;
            color: var(--muted);
            transition: color .15s;
        }

        .desktop-nav a:hover {
            color: var(--ink);
        }

        .desktop-nav .btn {
            padding: 9px 18px;
            color: var(--ink);
            font-size: .95rem;
        }

        .mobile-nav-triggers {
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 101;
        }

        .mobile-login-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 8px;
            border: 1.5px solid var(--line);
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--ink);
            background: #fff;
            transition: all 0.15s;
        }

        .mobile-login-pill:hover {
            border-color: var(--sun);
            color: var(--sun);
        }

        /* Hamburger button */
        .menu-toggle {
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 42px;
            height: 42px;
            padding: 0;
            background: #fff;
            border: 1.5px solid var(--line);
            border-radius: 10px;
            cursor: pointer;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .menu-toggle .bar {
            width: 20px;
            height: 2px;
            background-color: var(--ink);
            border-radius: 2px;
            transition: transform 0.25s ease, opacity 0.2s ease;
            transform-origin: center;
        }

        /* Animated hamburger -> X */
        .menu-toggle.active {
            background-color: #f0f4f8;
            border-color: var(--sun);
        }

        .menu-toggle.active .bar:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .menu-toggle.active .bar:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }

        .menu-toggle.active .bar:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* Mobile Dropdown Menu */
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            box-shadow: 0 16px 32px rgba(10, 52, 93, 0.15);
            border-bottom: 3px solid var(--sun);
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 99;
        }

        .mobile-menu.open {
            display: block;
            max-height: 520px;
        }

        .mobile-menu-inner {
            padding: 16px 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .mobile-menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--ink);
            text-decoration: none;
            transition: background-color 0.15s, color 0.15s;
        }

        .mobile-menu-link svg {
            color: var(--sun);
            flex-shrink: 0;
        }

        .mobile-menu-link:hover,
        .mobile-menu-link:active {
            background-color: #f6f9fc;
            color: var(--sun);
        }

        .mobile-menu-divider {
            height: 1px;
            background-color: var(--line);
            margin: 10px 0;
        }

        .mobile-menu-cta {
            width: 100%;
            text-align: center;
            padding: 13px 20px;
            font-size: 1rem;
            margin-top: 4px;
        }

        .mobile-menu-backdrop {
            display: none;
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 52, 93, 0.3);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            z-index: 98;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .mobile-menu-backdrop.open {
            display: block;
            opacity: 1;
        }

        @media(max-width:768px) {
            .desktop-nav {
                display: none;
            }

            .mobile-nav-triggers {
                display: flex;
            }

            .logo {
                font-size: 1.15rem;
            }

            .logo img {
                height: 32px;
            }

            .wrap {
                padding: 0 16px;
            }

            .hero {
                padding: 40px 0 50px;
            }

            .cta-row {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .cta-row .btn {
                text-align: center;
                width: 100%;
            }
        }

        @media(max-width:576px) {
            .toggle {
                width: 100%;
                display: flex;
            }

            .toggle button {
                flex: 1;
                padding: 8px 10px;
                font-size: 0.85rem;
            }

            .plan.rec {
                transform: none;
                margin-top: 14px;
            }
        }

        /* hero */
        .hero {
            padding: 64px 0 80px
        }

        .hero .wrap {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 48px;
            align-items: center
        }

        .hero p.lead {
            margin: 20px 0 28px
        }

        .cta-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap
        }

        .note {
            margin-top: 16px;
            color: var(--muted);
            font-size: .95rem
        }

        .slip {
            background: var(--white);
            border: 2px solid var(--ink);
            border-radius: 16px;
            padding: 22px 24px;
            box-shadow: 10px 10px 0 var(--sun);
            max-width: 420px;
            justify-self: end;
            width: 100%
        }

        .slip h4 {
            font-size: 1rem;
            display: flex;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 2px dashed var(--line);
            margin-bottom: 10px
        }

        .slip h4 span {
            color: var(--muted);
            font-weight: 500;
            font-size: .9rem
        }

        .row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: .97rem;
            font-variant-numeric: tabular-nums
        }

        .row b {
            font-weight: 600
        }

        .row.auto {
            background: var(--mintbg);
            margin: 0 -10px;
            padding: 7px 10px;
            border-radius: 6px
        }

        .row.auto em {
            font-style: normal;
            font-size: .72rem;
            color: var(--mint);
            font-weight: 700;
            margin-left: 6px
        }

        .total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 2px solid var(--ink);
            margin-top: 10px;
            padding-top: 12px;
            font-weight: 800
        }

        .total strong {
            font-size: 1.6rem;
            letter-spacing: -.02em
        }

        .slip small {
            display: block;
            margin-top: 10px;
            color: var(--muted);
            font-size: .78rem
        }

        .row.auto {
            opacity: 0;
            animation: pop .45s ease forwards
        }

        .row.auto:nth-of-type(4) {
            animation-delay: .5s
        }

        .row.auto:nth-of-type(5) {
            animation-delay: .9s
        }

        .row.auto:nth-of-type(6) {
            animation-delay: 1.3s
        }

        .row.auto:nth-of-type(7) {
            animation-delay: 1.7s
        }

        @keyframes pop {
            from {
                opacity: 0;
                transform: translateX(-10px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        @media(prefers-reduced-motion:reduce) {
            .row.auto {
                animation: none;
                opacity: 1
            }

            html {
                scroll-behavior: auto
            }

            .btn {
                transition: none
            }
        }

        @media(max-width:860px) {
            .hero .wrap {
                grid-template-columns: 1fr
            }

            .slip {
                justify-self: start
            }
        }

        /* sebelum sesudah */
        .versus {
            background: var(--white);
            border-block: 1px solid var(--line)
        }

        .vs {
            margin-top: 34px;
            border: 2px solid var(--ink);
            border-radius: 14px;
            overflow: hidden
        }

        .vs div.r {
            display: grid;
            grid-template-columns: 1fr 1fr
        }

        .vs div.r+div.r {
            border-top: 1px solid var(--line)
        }

        .vs .h {
            font-weight: 800;
            padding: 14px 22px
        }

        .vs .h.b {
            background: var(--redbg);
            color: var(--red)
        }

        .vs .h.a {
            background: var(--ink);
            color: #fff
        }

        .vs p {
            padding: 16px 22px;
            font-size: 1rem
        }

        .vs p:first-child {
            border-right: 1px solid var(--line);
            color: var(--muted)
        }

        @media(max-width:640px) {
            .vs div.r {
                grid-template-columns: 1fr
            }

            .vs p:first-child {
                border-right: 0;
                border-bottom: 1px dashed var(--line)
            }
        }

        /* fitur */
        .feat {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 56px;
            align-items: center;
            margin-top: 56px
        }

        .feat:nth-of-type(even) .txt {
            order: 2
        }

        .feat ul {
            list-style: none;
            margin-top: 14px
        }

        .feat li {
            padding: 8px 0 8px 30px;
            position: relative;
            color: var(--muted)
        }

        .feat li:before {
            content: "";
            position: absolute;
            left: 0;
            top: 15px;
            width: 16px;
            height: 9px;
            border-left: 3px solid var(--mint);
            border-bottom: 3px solid var(--mint);
            transform: rotate(-45deg) scale(.8)
        }

        .panel {
            background: var(--ink);
            color: #fff;
            border-radius: 16px;
            padding: 30px;
            min-height: 190px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px
        }

        .panel .big {
            font-size: 2.6rem;
            font-weight: 850;
            letter-spacing: -.03em;
            color: var(--sun);
            line-height: 1
        }

        .panel span {
            color: #C9D6E5;
            font-size: .98rem
        }

        @media(max-width:820px) {
            .feat {
                grid-template-columns: 1fr;
                gap: 22px
            }

            .feat:nth-of-type(even) .txt {
                order: 0
            }
        }

        /* langkah */
        .steps {
            background: var(--ink);
            color: #fff
        }

        .steps .lead {
            color: #C9D6E5
        }

        .st {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-top: 38px;
            counter-reset: s
        }

        .st div {
            border-top: 4px solid var(--sun);
            padding-top: 16px;
            counter-increment: s
        }

        .st h3:before {
            content: counter(s) ". "
        }

        .st p {
            color: #C9D6E5;
            margin-top: 8px;
            font-size: 1rem
        }

        @media(max-width:760px) {
            .st {
                grid-template-columns: 1fr
            }
        }

        /* harga */
        .toggle {
            display: inline-flex;
            background: var(--white);
            border: 2px solid var(--ink);
            border-radius: 999px;
            padding: 4px;
            margin: 20px 0 34px
        }

        .toggle button {
            border: 0;
            background: transparent;
            font: inherit;
            font-weight: 700;
            padding: 9px 22px;
            border-radius: 999px;
            cursor: pointer;
            color: var(--ink)
        }

        .toggle button[aria-pressed=true] {
            background: var(--ink);
            color: #fff
        }

        .toggle small {
            background: var(--sun);
            color: var(--ink);
            border-radius: 6px;
            padding: 1px 7px;
            margin-left: 6px;
            font-size: .78rem
        }

        .plans {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            align-items: stretch
        }

        .plan {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 26px 22px;
            display: flex;
            flex-direction: column
        }

        .plan.rec {
            border: 3px solid var(--sun);
            position: relative;
            transform: translateY(-8px)
        }

        .plan.rec:before {
            content: "Rekomendasi";
            position: absolute;
            top: -15px;
            left: 20px;
            background: var(--sun);
            color: var(--ink);
            font-size: .8rem;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 999px
        }

        .plan .who {
            color: var(--muted);
            font-size: .95rem;
            margin: 2px 0 16px
        }

        .price {
            font-size: 2rem;
            font-weight: 850;
            letter-spacing: -.03em;
            line-height: 1.1
        }

        .price small {
            font-size: .95rem;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0
        }

        .sub {
            color: var(--mint);
            font-weight: 600;
            font-size: .9rem;
            min-height: 1.5em;
            margin: 4px 0 18px
        }

        .plan ul {
            list-style: none;
            font-size: .95rem;
            color: var(--muted);
            margin-bottom: 22px
        }

        .plan li {
            padding: 3px 0
        }

        .plan .btn {
            margin-top: auto;
            text-align: center;
            padding: 12px
        }

        .big-co {
            margin-top: 26px;
            color: var(--muted);
            text-align: center
        }

        .inc {
            margin-top: 30px;
            background: var(--mintbg);
            border-radius: 12px;
            padding: 20px 24px;
            color: var(--ink)
        }

        .inc b {
            display: block;
            margin-bottom: 4px
        }

        /* faq */
        details {
            border-bottom: 1px solid var(--line);
            padding: 18px 0
        }

        summary {
            font-weight: 700;
            cursor: pointer;
            font-size: 1.05rem;
            list-style: none;
            display: flex;
            justify-content: space-between;
            gap: 16px
        }

        summary::-webkit-details-marker {
            display: none
        }

        summary:after {
            content: "+";
            font-size: 1.4rem;
            line-height: 1;
            color: var(--mint)
        }

        details[open] summary:after {
            content: "–"
        }

        details p {
            color: var(--muted);
            margin-top: 10px;
            max-width: 68ch
        }

        .final {
            background: var(--ink);
            color: #fff;
            text-align: center;
            padding: 72px 0
        }

        .final .lead {
            color: #C9D6E5;
            margin: 12px auto 28px
        }

        .final .btn {
            background: var(--sun);
            border-color: var(--sun);
            color: var(--ink)
        }

        .final .btn.alt {
            background: transparent;
            color: #fff;
            border-color: #fff
        }

        footer {
            padding: 34px 0;
            background: #fff;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: .9rem;
            text-align: center
        }

        footer img {
            display: block;
            margin: 0 auto 10px;
            width: 150px;
            height: 150px
        }
    </style>
</head>

<body>

    <header>
        <div class="wrap">
            <a class="logo" href="#"><img src="data:image/webp;base64,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" alt="" width="41" height="38"><span>Pranata HR</span></a>

            <div class="header-right">
                <nav class="desktop-nav">
                    <a href="#fitur">Fitur</a>
                    <a href="#harga">Harga</a>
                    <a href="#faq">Tanya jawab</a>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="/login" class="nav-login">Masuk</a>
                        <a class="btn" href="/register">Coba gratis</a>
                    <?php else: ?>
                        <a class="btn" href="/dashboard">Dashboard</a>
                    <?php endif; ?>
                </nav>

                <div class="mobile-nav-triggers">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="/login" class="mobile-login-pill">Masuk</a>
                    <?php endif; ?>
                    <button type="button" class="menu-toggle" id="menuToggle" aria-label="Menu Navigasi" aria-expanded="false">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-inner">
                <a href="#fitur" class="mobile-menu-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                    <span>Fitur Unggulan</span>
                </a>
                <a href="#harga" class="mobile-menu-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    <span>Paket &amp; Harga</span>
                </a>
                <a href="#faq" class="mobile-menu-link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>Tanya Jawab</span>
                </a>

                <div class="mobile-menu-divider"></div>

                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="/login" class="mobile-menu-link mobile-menu-login">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <span>Masuk ke Akun</span>
                    </a>
                    <a class="btn mobile-menu-cta" href="/register">Coba Gratis 14 Hari</a>
                <?php else: ?>
                    <a class="btn mobile-menu-cta" href="/dashboard">Buka Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="mobile-menu-backdrop" id="menuBackdrop"></div>
    </header>

    <main>
        <section class="hero">
            <div class="wrap">
                <div>
                    <h1>Gajian beres tanpa hitung PPh 21 dan BPJS manual.</h1>
                    <p class="lead">Absensi, izin &amp; cuti, sampai slip gaji ada dalam satu aplikasi. Dibuat untuk UMKM yang belum punya tim HR dan tidak mau lembur di tanggal gajian.</p>
                    <div class="cta-row">
                        <a class="btn wa" href="/register">Coba gratis 14 hari</a>
                        <a class="btn alt" href="#harga">Lihat harga</a>
                    </div>
                    <!-- <p class="note">Tanpa kartu kredit. Kami bantu impor data karyawan Anda.</p> -->
                </div>
                <div class="slip" aria-label="Contoh slip gaji">
                    <h4>Slip gaji Oktober <span>Contoh ilustrasi</span></h4>
                    <div class="row"><b>Gaji pokok</b><span>Rp 5.000.000</span></div>
                    <div class="row"><b>Tunjangan</b><span>Rp 1.000.000</span></div>
                    <div class="row"><b>Kehadiran</b><span>22 dari 22 hari</span></div>
                    <div class="row auto"><b>BPJS Kesehatan<em>otomatis</em></b><span>– Rp 60.000</span></div>
                    <div class="row auto"><b>BPJS Ketenagakerjaan<em>otomatis</em></b><span>– Rp 180.000</span></div>
                    <div class="row auto"><b>PPh 21<em>otomatis</em></b><span>– Rp 45.000</span></div>
                    <div class="total"><span>Gaji diterima</span><strong>Rp 5.715.000</strong></div>
                    <small>Angka hanya contoh untuk menggambarkan tampilan slip.</small>
                </div>
            </div>
        </section>

        <section class="versus">
            <div class="wrap">
                <h2>Gajian di tanggal 25: kebanyakan UMKM masih begini.</h2>
                <p class="lead">Satu salah hitung bisa jadi komplain karyawan, atau denda saat lapor pajak dan BPJS.</p>
                <div class="vs">
                    <div class="r">
                        <div class="h b" style="padding:14px 22px">Sebelum</div>
                        <div class="h a">Dengan Pranata HR</div>
                    </div>
                    <div class="r">
                        <p>Absen lewat grup WhatsApp, buku tulis, atau tanda tangan yang gampang lupa.</p>
                        <p>Karyawan absen sendiri. Rekap kehadiran terkumpul otomatis.</p>
                    </div>
                    <div class="r">
                        <p>Izin dan cuti diminta lewat chat, lalu sisa cuti dihitung dari ingatan.</p>
                        <p>Karyawan mengajukan, Anda setujui, sisa cuti terhitung sendiri.</p>
                    </div>
                    <div class="r">
                        <p>Tiap bulan menyusun Excel gaji dan mengecek rumus satu per satu.</p>
                        <p>Gaji, potongan, dan slip jadi dalam beberapa klik.</p>
                    </div>
                    <div class="r">
                        <p>PPh 21 dan BPJS dihitung manual, takut salah dan kena denda.</p>
                        <p>PPh 21 dan BPJS terhitung otomatis dan ikut diperbarui saat aturan berubah.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur">
            <div class="wrap">
                <h2>Tiga hal yang bikin HR makan waktu, sudah kami rapikan.</h2>
                <p class="lead">Tidak perlu belajar sistem rumit. Cukup yang dipakai UMKM setiap bulan.</p>

                <div class="feat">
                    <div class="txt">
                        <h3>Absensi yang tidak bisa diakali "lupa catat"</h3>
                        <ul>
                            <li>Karyawan absen masuk dan pulang sendiri</li>
                            <li>Rekap kehadiran per orang dan per bulan</li>
                            <li>Data kehadiran langsung terhubung ke perhitungan gaji</li>
                        </ul>
                    </div>
                    <div class="panel">
                        <div class="big">0 buku</div><span>Tidak ada lagi buku absen, tanda tangan, atau rekap manual di akhir bulan.</span>
                    </div>
                </div>

                <div class="feat">
                    <div class="txt">
                        <h3>Izin dan cuti selesai tanpa chat berlapis</h3>
                        <ul>
                            <li>Karyawan mengajukan izin atau cuti sendiri</li>
                            <li>Anda setujui atau tolak dalam satu klik</li>
                            <li>Sisa cuti tiap karyawan terhitung otomatis</li>
                        </ul>
                    </div>
                    <div class="panel">
                        <div class="big">1 klik</div><span>Dari pengajuan sampai persetujuan, semuanya tercatat rapi dan bisa dilacak.</span>
                    </div>
                </div>

                <div class="feat">
                    <div class="txt">
                        <h3>Payroll lengkap dengan PPh 21 dan BPJS</h3>
                        <ul>
                            <li>Hitung PPh 21 otomatis untuk tiap karyawan</li>
                            <li>Hitung iuran BPJS Kesehatan dan Ketenagakerjaan otomatis</li>
                            <li>Slip gaji siap dibagikan ke karyawan</li>
                        </ul>
                    </div>
                    <div class="panel">
                        <div class="big">Otomatis</div><span>Bagian yang paling bikin pusing dan paling berisiko salah, kini dikerjakan sistem.</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="steps">
            <div class="wrap">
                <h2>Mulai pakai tanpa repot.</h2>
                <p class="lead">Anda cukup menyiapkan daftar karyawan. Sisanya kami bantu.</p>
                <div class="st">
                    <div>
                        <h3>Daftar dan coba gratis</h3>
                        <p>Hubungi kami lewat WhatsApp. Trial 14 hari, tanpa kartu kredit.</p>
                    </div>
                    <div>
                        <h3>Kirim data karyawan</h3>
                        <p>Kirim daftar karyawan dalam Excel. Kami bantu impor dan atur komponen gajinya.</p>
                    </div>
                    <div>
                        <h3>Gajian jalan sendiri</h3>
                        <p>Absensi dan cuti terkumpul, payroll terhitung, slip gaji siap dibagikan.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="harga">
            <div class="wrap" style="text-align:center">
                <h2>Harga tetap per bulan. Tidak ada hitungan per kepala yang membingungkan.</h2>
                <p class="lead" style="margin:0 auto">Pilih paket sesuai jumlah karyawan. Semua fitur sudah termasuk di semua paket.</p>
                <div class="toggle" role="group" aria-label="Periode bayar">
                    <button id="bM" aria-pressed="true">Bulanan</button>
                    <button id="bY" aria-pressed="false">Tahunan<small>gratis 2 bulan</small></button>
                </div>
                <?php

                $pakets = $pakets ?? Paket::find()->where(['status' => 1])->orderBy(['max_user' => SORT_ASC, 'durasi_hari' => SORT_ASC])->all();

                // Kelompokkan berdasarkan tier (selain Trial)
                $tiers = [];
                $prevMaxUser = 0;
                foreach ($pakets as $p) {
                    if (strtolower($p->tier) === 'trial') {
                        continue;
                    }
                    $tierName = $p->tier;
                    if (!isset($tiers[$tierName])) {
                        $tiers[$tierName] = [
                            'name' => $tierName,
                            'max_user' => $p->max_user,
                            'range' => ($prevMaxUser + 1) . '–' . $p->max_user . ' karyawan',
                            'm_price' => 0,
                            'y_price' => 0,
                            'm_id' => null,
                            'y_id' => null,
                        ];
                        $prevMaxUser = $p->max_user;
                    }
                    if ($p->durasi_satuan === 'bulan' || $p->durasi_hari <= 31) {
                        $tiers[$tierName]['m_price'] = (float)$p->price;
                        $tiers[$tierName]['m_id'] = $p->id_paket;
                    } elseif ($p->durasi_satuan === 'tahun' || $p->durasi_hari > 31) {
                        $tiers[$tierName]['y_price'] = (float)$p->price;
                        $tiers[$tierName]['y_id'] = $p->id_paket;
                    }
                }
                ?>
                <div class="plans" style="text-align:left">
                    <?php foreach ($tiers as $tierKey => $item):
                        $isRec = (strtolower($tierKey) === 'kecil');
                        $mPriceFormatted = 'Rp ' . number_format($item['m_price'], 0, ',', '.');
                        $yPriceFormatted = 'Rp ' . number_format($item['y_price'], 0, ',', '.');
                        $perMonthIfYear = $item['y_price'] > 0 ? 'Setara Rp ' . number_format(round($item['y_price'] / 12), 0, ',', '.') . ' per bulan' : '';
                        $mUrl = Url::to(['/site/register', 'id_paket' => $item['m_id']]);
                        $yUrl = Url::to(['/site/register', 'id_paket' => $item['y_id'] ?: $item['m_id']]);
                    ?>
                        <div class="plan<?= $isRec ? ' rec' : '' ?>">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="who"><?= htmlspecialchars($item['range']) ?></p>
                            <div class="price"><span class="p" data-m="<?= $mPriceFormatted ?>" data-y="<?= $yPriceFormatted ?>"></span><small class="u"></small></div>
                            <p class="sub" data-y="<?= htmlspecialchars($perMonthIfYear) ?>"></p>
                            <ul>
                                <li>Absensi</li>
                                <li>Izin &amp; cuti</li>
                                <li>Payroll, PPh 21, BPJS</li>
                            </ul>
                            <a class="btn btn-pilih-paket" href="<?= htmlspecialchars($mUrl) ?>" data-m-href="<?= htmlspecialchars($mUrl) ?>" data-y-href="<?= htmlspecialchars($yUrl) ?>">Pilih <?= htmlspecialchars($item['name']) ?></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- <p class="big-co">Lebih dari 100 karyawan? Rp 8.500 per karyawan per bulan. <a class="wa" href="#" data-msg="Halo, saya punya lebih dari 100 karyawan dan ingin tanya harga Pranata HR.">Hubungi kami</a>.</p> -->
                <div class="inc" style="text-align:left"><b>Coba dulu 14 hari, tanpa kartu kredit.</b>Biaya setup untuk bantu impor data mulai Rp 500.000, dan gratis untuk paket tahunan.</div>
            </div>
        </section>

        <section id="faq" style="background:var(--white);border-top:1px solid var(--line)">
            <div class="wrap" style="max-width:800px">
                <h2>Pertanyaan yang sering muncul</h2>
                <details>
                    <summary>Apakah perhitungan PPh 21 dan BPJS mengikuti aturan terbaru?</summary>
                    <p>Perhitungan mengikuti ketentuan yang berlaku, dan kami memperbarui sistem saat aturan berubah. Anda tidak perlu mengubah rumus sendiri.</p>
                </details>
                <details>
                    <summary>Saya hanya punya beberapa karyawan. Apakah cocok?</summary>
                    <p>Cocok. Paket Mikro untuk 1–10 karyawan dibuat untuk usaha kecil yang belum punya staf HR.</p>
                </details>
                <details>
                    <summary>Bagaimana dengan data karyawan yang sudah ada di Excel?</summary>
                    <p>Kirimkan file Excel Anda, kami bantu impor ke sistem sehingga Anda tidak perlu mengetik ulang.</p>
                </details>
                <details>
                    <summary>Boleh berhenti kapan saja?</summary>
                    <p>Paket bulanan bisa dihentikan di akhir periode. Paket tahunan dibayar di muka dan lebih hemat, karena Anda hanya membayar 10 bulan.</p>
                </details>
                <details>
                    <summary>Kalau jumlah karyawan bertambah?</summary>
                    <p>Anda tinggal naik ke paket berikutnya. Harga tetap sama sampai batas jumlah karyawan di paket Anda.</p>
                </details>
            </div>
        </section>

        <section class="final">
            <div class="wrap">
                <h2>Bulan depan, gajian tidak perlu lembur.</h2>
                <p class="lead">Coba 14 hari. Kalau tidak cocok, Anda tidak rugi apa-apa.</p>
                <div class="cta-row" style="justify-content:center">
                    <a class="btn wa" href="/register">Coba gratis</a>
                    <a class="btn alt" href="#harga">Lihat harga lagi</a>
                </div>
            </div>
        </section>
    </main>

    <footer><img src="data:image/webp;base64,UklGRj47AABXRUJQVlA4IDI7AABQwgCdASpAAUABPkkejUQioaGWCS50KASEsglwESz4CqPoa/k/13sBNzeD/sf7w/4/3ga1/Yv7D/jv9b/d/dd3g9Z+cz5D+nf8z+8/kz8/f956k/47/k/2I+AH9XP+x/gPyU7hvmB/cb92/eD/3P7g+9H+vfcZ8iP9K/zX/29d32WP3b9g/93vTl/eD///LT/Uv+v+23s9//v97e714HX+9/3f9cfBz+/f3X/Cf4j8f/Q/9T/cv7D+xf7wf5fqY9Sean8c+0n4b+2fsv+af3v/sv+H+RX4q+0fzN1Bfxr+U/4f8hP7v+ynIkw+ewF7kfTf8d+ZP9e/dH6ovn/8l+Unuj84PuAf0T+mf7b8vP8B///r3/oeDf6d7AX83/uH/D/wX7pf8P6aP6z/xf5v/ZfuB7cf0j/Jf+L/K/AT/Mv6j/s/7j+8P+V/////+9r//+4L93v//7qn7a//9NiV3AZMG9EK11IIDO89TBxvfNVGtgDyOJXcBkwif3WCB9u/k9TCTpaez++om8oQlRgT1NJ5dAmu4DJhE/usYru13lX9e3Vg7PyouXfsCX2brBpLbDig9tjTVshyNx2C/iJkdGxK7gMl16z/t7MD0u4qL38iua492u8q+UZSMQ5JMCiJECKVEQdxSHmR8r+ZMIn3GFnsjL304FxX2K/VYfYjWvX1fx+NgGfK01pvmNPLYTy+fNwep7xsTt5kLNxbDld9vNoHxxGkHCm9FO1+uR10aPPw2dVE6N+feX6dKuA3HphQH3Rujix0qwyP5j/Ag1CrTXsP3xGBNr7RZ/OZvg1VrUCh/uj9o4yeOPWIs6dvI4/ekm7hn1QLaifkwXTR6qLCLNar84ZcWkEA3PSvGxH0Kiwev2utIuDLFVsRgH9B900Pl/VbAHAdUwaMgVUYRqfucgSKxrndcczP/r6sj4bJzCmJrNc5fLA5SPpc2wO3sd1cgJi3esalY/6LcJrtqE3hLSVIPxI6Y1I8e6Cn3VNua+flz5ONNZIN+BwHSqwyvQcvcAvfBomuJLnpXT8xBLT1jNBs59rxqocqybnelyNtUrIiz+P+5tXCCxm7sYmUCYZi1+5CHRneKhzOn81Xj4Blj3KG6+zIaux/fMpeLVR+YVh4jmEDj6aJP9ruIiwPp7BCgw/nV1DIj2mIjI8fG34e9yt4mGpNONXCtoRM0MbpCF7p7Urf/L6Vu2OtWXqL3/eJe+zGkueEGtc6zuByHDp6rlv4+9Pcci2u+hgR7upWTkxl/R+Q8vos714Wk094IzxwlJ/+PjGw8BCnb0F9kPkH7e6/ut1ERzUtYQnXBcqHzsIGNjd8OVXBDhuJAgWeksfznMqjytmw+NgTms+NkInJu+hCf5N0fG/XtUaaW0kVf4OYGVA8m/AviGN3zzVH0bng1oRncj6j/YrMQGITHlT++n2aM5S8kYJaqXey7Ser2AaLUAlr77JkMnTEQ5seDPoRKnz0jx7QTxc5acEjbiuyAyGW2+OXbrD/PLTshPl9BokLCXQskgkp6cPcx4w3IG8paQvOhkOP7jWdSyOXy1iUp/2sGtHQDVaHry+75/4Jpj/HotzlhMBGBC36Mx8ltCHwUpaX/odn9OGWvj6FwIk9A2XHCfRRthO6y/czuI1+38mbQAby4RGszWKRRruSrGBB6IHKRU4XsIim1zjjg4K1SgVJA0EsNBJLtU7KqMmhzMO+F4YhRNR/WbZSWG5+W3uQ7CSE76JvBQCmK4M3vYiPxe8I2hky8h1Rotml1DbNxyZgiCn+PAW1BxTGRicCUdKdkCBnLqDgL4GVjXa9B9Lubg/GhGSixkAz+F57/+G0r+HqPcxCL5rezfFNSLTqoryW/Js6derEgMrd9PSut0T5rh6UqlG48TwBfRr3LI+D1Pq1KqpPmS1gYBpEmEkROF026+7bDsWZ9IrqJNFG8ijr0fx+QxoVcqgMBSULfbnWE1stR8TBBLdYhcfVCkzbcs3BMCIoni7SyOkbfUXGl596c/l4lNltC2hqatKsy+Bje2SOrNokaT4THHOThhz6K1f8ck1lKGcO/L5wxOIb8P84nui6afwKTElkxtEHIwAA/v9GUAAZnQVHbjhIsVc2hENgsk71uP+joOz02/DAlr5RJr5hT0DACFw1bUmEpeIZU+WbGGGWCqVIuowj0Ulpv24zJLNDXAcO0GRoziZGFT/NTl5t6pUf+qbwh7KJ7ccafS6A+hAFg90ZRpMpdoHsaqi2tDPNHk7bjfDAjEcb9WZAVXdmFXPyewmXfHrMgAAAGwT6QZpNbAaB14w/wElqnFq7cNWiPQowBKeKBA6hkR/uhoEJTZmkSIrxwtdCVuIESB8KpbWDQqs43T3CA+h6xZvMTlVR/1TqWQaNhFiFHkb4/Jo5elG6qXfVUXAJZhCoKSkkm2mgmlRJBjbpM0FUZBVLOZh9pXzmfY4+UkUIS2JtpY+32pRxLsmgYc7Y/jcq1kVvgimbTtAm5UWuvE3o90+GV0OXatSLGLcDsURQT5A1U0r8VOyUis0gQOah5SyKR5hPlOHGmWQ5G88DOlG3gSEANXOKKeMP6lbmnuds4G6Z2bVIi01va/3Z8gv5fpRMS3PoP5pEgE47RRivQ8hrsDQB3eEEnUTF/8ow9HDkeK9UGKuXcEKiHKXSq0lVpB9X+b05jUuC+tA/jSr5QhCk+7hVgAADwDhWE58paSPBAza3Dp7wuFZuBh5b4cnFJ6E6N8Pytk11TwRdbJ8n2oaJmN3IRWf45pHVZZUqbRNr5l78/HM93RNDBy208Q/CsQNj5cywokMdbWoA6PXFoS1RebWZpXVe9QPs1mFWKuUY89b3akkTyZKBMkuv6FLzCzXdMehV8xn9YQT1mcxF72cBKgzk/81ebvFtKV8kJRkIEEYweB1GyGrvtpZFYeE/03jawP+EVwJQ4YunlAgo4BMe6ud2QOXvvvQAb17SPH4ZobSKXC5pi0UBlNwSs+cKwGvAWYFmTFPrbaZ49v2o32OFBD/SkdT1lSgTQxVJfZQ+IWY5OI3OMfZdYzL3KBuGwoMQkfdY27UWzbZ4DDqOrkUisMYIYCATlQnhgvXdCadH7g2CuRep7JVjwMvoaLF+NFtanaO9iCscBhA6XMjOkPbI/0KLrsBJiffGchv4FGSIf+GBIuKV3H34ZfXLoKbPr/0qAT67C3mDQmWE9sXuAVBRFgZgnlGVq3J+lsOcCyutealTENrO+AJxkQIMuo18qofWr7aIEinlwOsxjIot3w25gtXHTk0Sn9y1C+L4KQKaOyN76B6J+cJDPCJ/4yryGFmhz/IoCisxFc1h+C9USfIZuVXh8JShyB7nc8AA5+tw1p09eFDLam74z9PoCdHtgB1vszSV2RmtwEm9B08mVCSvh7cACnArRFZ3+GVXqwdPTuYMKJBTjcVW8D1COxel7OFxT0RKzS6EMoXpe/0X9O2IrEU6HhfFc/zqt0xZ8T8aX1Zb0f6aANqdRCgeUy1fClQyQemnEmRrTlEzGKoQ1QVTQAQnHF0MMO2TlVLhfFQc2lIKB9ouwZXgU4NPMqfRE9MyqsqFExICswvCwAJxe7oYxWTLzxHDXC9lMyBdQYEHXndIFyJ9PLRTqKeccCBopT9phsvHSdQhLDDI+U7EPJhMxCe6bA0QoexPvlNo3IzZqtNUofdNLuFo+OzrIYm+Y5XPEuuv9wNt6bf+0hRRC+bfQhLkJh36hy23AzJ8pUI2j40aYRM/XSJFyvzL6jXEVL7/Qc67KhE0mPJYACQ4/nBV95uzWh7qtmblf1GLdw5lFH1ibMF0YRQnMkaTModZic918g3NocQfXOo1d4rwiZhTljfwE/9pYA2EZCHkWIP3dF5GrAo5oilAUvuN4FgB4aURPtRYHFT9PJK4uBLdEw5NpfD9Hqo5aN66dJxzwBfTkfSy2dkjdDYAb/t4gR+AtxPYiY612Drza1Opu9UXM1mpRA0ls8lpVLn97cUrvQ5kXJQQMCsrq8o54LfAkyAvnVpDZ8FzUPxBYFCeztaoPI7K+h9WHOAv/T96lWoepkqru6ReVO/Z15mq9B5Gw3ODiU4K7f15D0JIGMmUdGTyIqEymCjfqv/k8UaLtqw55XtH9WuXGwatWNiqbkGL6zoMafRTw1DP79vfyoIAF3K5b946bmBFYUMERyz9IugWgMslBNIfZvgQlGApI5J8SlUjtm4sjcT8vV622fdWllW20VroZYQzxnF0PUg7eNcNJc984eiS+awoi7dFN2EYTWzdAVDTlzMKQfQdQhaathDVPHXkA4PAeo4Q/CkoXWXP84/LZjTFeQ34FulULeM6z7jnhF+Ga4Cb4SyYdoclgCBjWPkpe3RegWWwwon1OkxUpRKycrVOpcaTQ0qpGjEPAFl4D/VbBYSttnBfQYqbokOVwFCwERJ3QgXLn0zoq5DSZaUPGt4hodOHvh+ZrkI3hZPCNLNOhsfqKMH8Raq7zuvvUlupccjSWxicZNPR4fn5NUe/+EBcRZdHMpo0F7eP6r/R43gVdKs3TxuGGn0RvQuogvPoDhe1BJRXr1n82PnVKJUDpPVdMOTekcin6KHlMW0k90kAE1Xmdj3rX8KfUIEQ1HbOd9xm1sND8NQW/I8NSyFFIBRWkmLx7dUm8sI7RG3M6pvdpwKZtGh2JZuOLOiHN+AJ8B8JkaLG/ySuh9y2e4H9UsSJXk+4k5Nnm24xPrHxVwBO6zByFTmp53xEI26FeNtcgRPuoHbBWNlEeDSWiqneJCqJqpvX9YPAZWKNR/J2EamaMxY6WGDHqhP5TGm/BaAV87tG/ZbGq+ACced6ky8/LNzSGxxsYiSisCKhWT4sZ5wl81Q/6OBC+a3Zvq4JHazWw7nmPSpPoHTT0+kNH7IyB6gSggx8OG33en9PUovlYgdlaPQB5UkAK8lVk7PO+Op59MJUs/CJWUvbcJo4CvBhhusIEOWzUFtdUnD5dmV43O88HgJLeDHu0DJnDQvPqyG6Vixx4XXdViwze6iLuhggox7uNbwam7Cc1t3FUIA2BWh2z6rHGQy/fEupMkZsQaYxyX8Vkl/ksOCpnKqE8XoIcreukF0bDfLjA5pIo7MclIYnrTXcgC7da/eboBDN22wirNQwZeuvxoL7EsNQxLkznas36H3kNJnlvYm0/aBZv4W+GIwIrJGetvx26Z3wm1EkmAZ+5v7R5NkzpslNL8Kf38N2d4/83jxiJVyAMTzOdVPz+Nvp/m9DDwySnmG0yi2FtsyDofFf+S+fAmSfxNtBNo9bYmiTWRHv5kJE9s3NsvqkHHIXAxTEQ6FbLqbT9EdWJk122PdcstB96CtVqdBp2caEFseCJUIS2xQXA6uPf453AiBJq4ruftjtlNO3zUvzPRb58Y6IGZ2sU033JdTieiu0qW04VrXqD4fLaWijPjgM6Puo8npcxYuasTicXHqa9RQSvz6wxJiWcz1/nvxHMI81WVVfWPxmp9DSY8DaMoEKblCVPmv1L6G2npwwmh86rDkD1B3Elsy/Xifb5lFEqr/msMztdtZsdtM9dWzCeywT4H7/N7V2wTFywpwwET2m4RO4sOFV/zBKGRgRWgqIEIlHwBK+Lj41QBbvO6nq9RcpmcXRoHI/M5teqSLTrPBOZR2OyVx726icx6yRX5ELKp0DMKgLuvB8z9n7ft+89Vu97Tgc6XZN0SCO4YvjNR92mEQDb4kjLLYdeWZXqxGY+EoZcum8itvoQdMEj7jlMAvr8+MItpCnC7idGUvNKLiZ0FWXH//PBQHwGBEcW/hZ4IigkwUzWxADNvSrEYuU8Jvdr3HzVsVb76xdS8v9EuIvDW1euBCoMKbzlevnFGSmaT+E6te6y3BvDo3yV99ZVBeGkjsXnjTtypuJPJmKoLeshEaTJfJKrwJJOtV7Ky+u+CU85ty8aJky6c6V1umRe8pZq5oIyBC1/3x+Y8pIsRyBK63KvzpXfiOvVk2vFgJNmwyL5GG1ByS0rWFgScw6jxekhuRNv67nAe+o/PNkJryaPvkBKJjGQ9w3Y/f9PO94G0YDpMGkDWFsKp3gCRa4+n2Q6nZX3PbTcBj+uKT7yh7n7dixvCIIrVVdXrxIQmooT5d8+t7S/jXRomt76ZJQTKTM1QmkOXeJIcUnP/Hq6xNwtYN0ZOxKRXoioRNtWWm877QdSAEtgkjad/jAZN20unzsM41wmR30lf6/q1E0oK7b0OOlg4nL5Nl0AsIhN1oQosdCov58axz3cP1PbvTX1ie5ZBPdf11lFCCdO2sTne1VYt8bDrwtiYSWMAOGVz3g74A/StDehnuq8zHiCNKwZa6wNYtHkCrOWWN1WZ+UfAcHBS3sDreULvSXTj8CPhSllB6EpJrsNtN6/Mb3vTVXNAp4v8Trh+IYHureLpNFmH7KoKqZRYETxNlqP6jCklGxWCg6pkjH72qDsO+qspQnil5/A52g0nAUklfWQxb9nq6ORbphUJFRZ5dyCxfO3Q7UGL7pSMPlRU41sBoGZWoZ43yhHBR+D4PyZTVQzAEBf7pn/63Ae86LcSwWT/6LVsW9deWdLt5JtzQVnh2S7t7V2AH1cUsTovhxwZ2oPHhPj/7ihdpafoEY8GYF887eQeIbV8lMtWCl7BfatJueLT6JADKmkM7znbT1VSdgeAVkUJ91KcwxUxq+9zsaq3PzlQ7JLhDddq6KDep+NfAwv86CkOEs9rAhKTmSxdG6jXov7z9husXyV1a18hxDSZi3H0QZMwu3VDyWPyMN0gKH3qzkNnRuuBTX0I12WJ5zjwwmAneekjOgPnuXvyCXvUXp3rJM3ATTrNqz05btihglORYSUdmwBcbLTF4x6K4WkIfz2o3suDlHf3NZ63Z2tbhjzZXdxSbo0qZzcb903mD1TDA+8UMvYKTdsjCPsl9GtuetPnxsVCeB58ptXIUCNHM7UPAZNlug23nbIwg6k3RBb/kKXMEPS6Z7+QpkhPQ8hqJwnQFxV06lSHDULKMnIAp1qTfaMd3fxCUAhF1vtky/0U7PIfsf8wnCpbkTwgAnYU399RlzBDzdzzMPG51EfikWXkKU7Hise7N9R/8PDXo7sAblzy7MZD/ubSnzcSQxgpjByY2MOOeKJSpKLmJPnalvmJt15inI9J/xMf3EcWrXgl3eAYtn3aOva8llihsdr6FdXFsxEdhbzC35aOjW+Buf7qNS2TCzPnuYA83tcidn/HdCIO/AEFWuNE2oOsg5ShYvfx3lLKwz833zsn6wFSnC7BrYXShjdH8HeraEC2hpTFGhmORTp8N0rWr32cq2KYO98K83Cm+JYARSmPKJWWiAzi3JX7LXCM/dyTXP7xp/z8sFPSo2hIVtE8W0epcwlfNrPmBMMxCG846cE86Zd/2unXPNg7E/+c2RpMTRQnnA+t0UKbiP0c+HoaKSoSC5Pi4nWCepujYE3sAFtOaWMS4GKXdLYohdbANRCi5Xv7nJ0c1L50xp/coITOIEhoEzbv7PIaINVqcEJEgibCHw5M8mHbQH8evFV+6geXVEzV7gmjG8WvjkfOASa8P4F9ZCtOfCOlCNR0iBf5DHnZwhIk+tggwqrb/aVurD+Z3oXtzplYDdHF3MWNLCkqFxmyzlj6FACiiIPXbJ1XXWhlBvgLSPtVBtFanQ1Y+Lo8oWs1KfRJ/GE4m62/1+twodmZU/vo+Msh6oBIo7kkJKckHq3IbU/8YL8VK9wpKVh2iEIEA/PiWbpKoVqwh5On94eYzuP/CywTHBtRA0YtyI7I438DxIakYaoT9TcqJ7BoSRi2R7Wj0ERdcIqfDu5dE4r7+/QcKCQ9sgsdeYuuxkBfdfzbZfi/VGTGcqkELOVyEwvKKB3FLX3JXBsBljysvl2gH45s5uKvmde4xVezVpzSCcaYnliRYFYmmoaVJX02lkZLJCBAP4eMh6JRQC5DXxX6PovLfwYNTprXGLuYR3i0INc1N7KGAqbtj9AHg6LIfKoDcvf7XcGdTFSjCKFyIjdHnAp4bSFpEdwpQN3nJVxIuIUXX0S6njJyRRuSKUDCWHq7B/c0nYTZS6hD0uEbQaS7PZzOq7lPfVv82U3/e3T+rkGWXfNR1OyjeLkrugwNRdsSuww6NVmeAR6lM6qW7V7nH6JhMVqG6/O8BU3zuWpIgHTGHiyT2bHZM/urNcwccK6FJkeeuLAGFnf/LLQbcSAfA+WBXUiSDuzjRQaL5cxPCfMljsZx/5b4DQ6Ibhh80VAsd6ne90ins74biDZJG8A32utVxAT8sXkfPis4QWH6F5kU6TC7sZMSf67SNIkyMbQ1+ypGt8Ac1Bgx9e7atqYcqzlBjHKInIXvf9xop2FEcvnVS50QkDGpfqMa984yEm7SYTel5p8mURGvRISOveO6YKrXo15gYwRJbd77iLq19zrk2DFozWpSdtIFX+MrKhI02JkDm5MhJObWLbVNiDczlSKeYg/VrOVjTEm7QF/TqZz71CitLitiCBxC5phUnxdGWfminSqSvumYQvoMyH3inaI5fC2DLj5wKSvAB7Nm5Vcy9hkGdgjnv9Z/RQ4Ok1mDqhfSsXq7J7YtagI8cBN5dGlcMey+5/3yHiRgvZ/8mE397fXwzRIVvm7nj1Qttg6kZkwshcLThQUmaefG3RabJYgp5rPXtaGenrTlTnBcwDSTkFHoMsnp1fbfMPHLAxknA8bEJdHDDR6XPAyDf3+xW/yyiQ7OFJ00742MrfOpYy6ygSKjk5k3aQ3efEEkX9wZSvjRHQd1xE2106YBO0C9vHBMt6FcTs+mjHsfyfz2yCiuMsO7H0PJEI+1MSZcRplYTe0Ig4BRrvAWzEPnmwpsv6sdRN+d3rR1HENBh7OHvKfboMNfiNaazYObV6crmqEETAUJD34Z/q9UYwN4tRt+vKbd5IKDApaLCrCkicVtm8XqXIvvjYc8BZ0nVkiW+R/S1D9j4tpAcqPIRxibdugsP75XZR0gxvuuTUxE5G0kdcNAXnlb9AwQwar7C2MxLKHgVgrcW7ojBCGuErsROHSdFJj3OSFA0ZzQBSSd3yc9PHWNvHz6GEZKLCU8X1EmsJkzMMLnkrkycUU0deYImBWX7hm6lM77wIkwTfR2215Wo93WiymedPCY37FOuIIz61rDGsnXPBVnrszj3rfWn20N/WBCU8w99ig7Ii92ZdHZNANtMfFy6IKSVds3i+YJl0czio/A3Qo0EYArTtq78awza/EpqXzvSUsQfcuosHbpdPVD6+9tLcA4d5Aol4L+ZDpbnF9UWnEEXqukazRnedfleaIg/8YwCB1Er5gg5L+rqkuZbvJz0NSilrj/Ak9inOnzIkOlr9CnGqBpJar6aULr+2OU0/DQptaH8cLCx06Joee2B2RGoCUSgKBVpgyx8Yx6mjUZO6qJbYxMgXlLItLmTzP84QBR5y5EgBOCHDdTcyClBlfCoj3+qnw5KkgxuwDgw1FaBh81pnlBMwW57rgw2vzh6kODvqMnu1lRrnwY5NQ2DUaDIzjyHu7XKKkpyT2ckpgeWT8pfGcByKD8fkVUddX0B28RJZ/1X+G8MyTfywh9N2CkVkWF/LdrFUQ9+vewLE1YsETQJr/EWhk5PxLRIZiHBDEUKTZlHPJ5QvxKPiVmBSGBj3O4k3KkjWHrPMsAs3NF+b6poHIrYpn9md4zYpQj5h1xqhfm0PdiHgzKCPeXSw1mPz8eiGUDML7lGWg98ozySMU3DRPtWn7g+y0DT5OhvVWHDttFOBlXLftbBuzsfynr/ANXOBVp1G5lJXRwXUZcd7VbGNsmmouZVhlxoKzmadD57YHz6YFSSFAh2bDLD7fZY1wOJHJ50iRmngPdK55Eyo7NmO1KsV64C8qzv4Z2frZaorWIuXjWm3buFw3CUU2FKRZ4+DTovcKHDj9PyDqYrwfQe6de48cq3Wy92DaudyGXYOhHcwhGczo1rcj2mbXTLnPTK5SkNKnwsIbIiNiso+4NhLXfA1DLN9XfMkREvKXZhUXFf0Cr3ED1UQBqmGqZFR3dRi8zILwpnMdFb6viShbDBM82fpM5inUFE7vlt6XRa4mZzEpO+a9cTPOyCrjLrGfimsHhwQUbAOj4O4njwBW3dA2xKAThW6+kFC/2CP8WNUdKHtsMBNI5pCvJ1BoHmeg3np2ACaZRqog2ldXhBhvJUDqWehnXSY4QP+ZG6A9esBKWM5G5v5/9LBDNF3TzgfzxVuNpQ+3dSsC3sjkubvjM5QMPV9FCRJZqNMb9QUwm/PdFz48oEgpSXIF0Vu0M3DuT3C2N8htkKv+FKCGiJCrHXok3VXYIBrTBh0A1MW5vAprYx5G++4JE31Xyr3UUfYpIrfahH7jAKAEFc5x0FVxYLvuHey02VeuJzlD4QLOmvZA/yzKXvW+BahQFjaQDot82ncaeWZ6Ue3/R7PVoHMCJUSlyeaPu0/iWRh9s/Jpp1S0Oo9m4sQv+KvHiDQd+tweEsYV3BpVH2GCXGNtRdo6jWH8q3E+GKsX2sfMnIjlBu9EPFZUIUQIezVGvuZKKj7AvelOIX8VI8Y5lR5RLF2w3oHZjk5wO/Zrf/DYX1AstLlkhOEKDo2tVXrd+8jl6w4uGuOu0wwAq9d+Qyrc88PU4qyevEabUMUL4w0vClQsNbXVSKHnl0qEsPy/SL8JK1b7VahjIW5YjH4Ony63+FiCKMfHqe7cj6+z5vSrgiRgDbPKKnmxlfdVPfX75r73prior4sXaeHU/5Orm/ARQwgbjySbSqHVdAwXKUyBIevm646Iif9f0Nb5j452qsBJPwCNjwoo5e69RkRHxJ6qOiXdJfP+eI/Nr7GrLOCCf66CsR1doRrrRd5g/HvX5YjGcEeWo4QdcJ+A4YMZxus5uZeizW9d8L/Cg73sWx5CLmj2/0zBByQdzR/vCkWmTNjPwNgs6OpWjDdkRyOw6wwLSVdHA8G90I0dSqeIINZLdvnsm0hQqTwWBHw6m8om4kWoDvdM73EfR+FttbNvrj2mF2iH4IkhNQ72kIRLNe8/vI72/vmuoebgu+wBHDGFrbmSAKAtOFfem1vbc7lL8+uCrXsmoBACOvTpNAFacadu6sik5H7cYilFNcqJ3ewkWCHCHSEr2/fz//NkXYi4OhOPLuaH/khexVyRBvI2x52oiZOGT1qWcneBpC8udBk8LAsgxrgWQNwwELJH0IMkwBqrs7ZHIt2zaNs3X/ijQAl7jbx2j0FREsiJGChyr7UkApJ0vve5DS9Ye+CTBTleRk7wGURmwiQHYwZFlunh2sT5IHlJTMwkcMz3+fehCoPL7k0AWU+vMgjZIaz2xWtz26O4PFk7hKGu7gmIwCduHykU3iV2nFDRjRfnOilYpxlOUEZuIHSsC+H6sTw+4qOcBFC7ISOLaj759V9tVUiSG5qPVbjizavW3K4GRMeVanb41F+yqiWg/5jCfS6Zh5Y/EzJENPw5xdm6vOY9TECWYBACW6ZZ95iQzZbVA7PwrJlhaHZOQSpMO5oUQxjpG0p9V2MXkJYiHlefX8DaJr0CkONF2UVJGgYxqgfEN3w+ZOsyHNmxZnjxRIHJRCYEzuDKwSnMpt9HmU5ILtqBOesrmNADBJ7EoARowsleiECfhcj1z7CSZcdrUngyGzVFtZap097XHyuA8RyQyhYkFOg/UR5HqBh++D8BnNsoaUxbVUy85ssGKh7ut0UGkOKag6AiSgqBqjj2LmrFhijm58R5HNuGFMmo86j9GbiQTbEwA6iOyvZ0c8KH4NllewCjrEmUE5ohNDyaK6AtOHIibblnZ9ypG1uDW9ViJkoX6hgnO5Hul6IYn2ZkN49TOf8GH8OV5kzX69x+L/ATWV6c5fe+beLYRUR4lE7cEi5PBifoEvKJNXz0w4cN8jmpk50bnM38Rs46VRdADK67x79dQBetOcYRLiBQT5cv2JYHco6sfUMMvvgd6wvbqE4jRlNQv369XPTHNwBESnSTqc2BuMqFS8bvNQUURas3drJD6a2pNovA/+UIVe6SM/JA8ZyTcRorKoGkvLXMx26nCRjbshpTPdTBsBVV4cBcTzzHXCGNQ+ulRb3Av2fhFc6Fz2/hmV9lTZpZEVV6spvBArYrwud+sK2u59Sjhf68BzgbxBaBsVFq97NpOFg3mM+fqR1Qm1sFLLBpH1q1MTZmHlihDbk/TQPF6vbdwlIGfBKbgRX9MYZpySmniaAzDY/hRbXae48T0J9pd7lOh4+mApuZTPkyvkIEp/DVX1/i02uxvWd2DizuzXfVcbfkZXJMnSeHgTUqGWI3sQYtql4P+9rnlI7TE0iZtPkleFlXiUytfvpGtMF1FnEHyFBQHpHDOmkVAHIk/JqZUx18E/5QkBxl1DJZy4Gbjsn6TnYagtp0CPzyL/xy06OGh189ImQtw51VUcOv4SxwH4ewqOU3zu3IZYurTJ+M1q2Q3SvnCWhoBa8AMGSengdpLFuFr2X/gyqh3UO6yVKYbmGP1GvoYnKewyTwUoYaVsli5SKCXgpqvsn7gpwsjeEcEgvB512BixhZe2cxCq6CZeRQUa7dfz36g7ioEkCZ/+2KyCF6YwGcbOPAHnbKfsXGrVp+jxyIZFmsIO8EOALOiMDXKBVclsvB7LQ19VxD9thDU/8BySvqrMbU53bQzbckKbMtH3KGVuciVpjdLmXgGN6xnotFOgkx39m54nFlJvvIq0JPPazf1XHEJ8wjpYChg6+DbaLsllillNiUyzRbqT0ERhWWhu8oQNjmdFWmKa+QsWwpXretP3zMiQOB+Fyz4aWxPN2+yo+izTCO+RXdMX2k/Brbvs6BLlvKD1t91miw6NwCWYdiiq1wHPxgb8M3rl/Y4vx2lNf2cdtA7wPa4RfjZaov7TN9hTASWAdux9gx8Sban7zcDVroMFYh6pf4f6svmIsaK3yF95JvDslEVURgK0JFJcjapujigPyxi6YnTxkVk7Dqit2BkTgjiIEdeYMq5S6aA7hfsgkROIpMX9jE756MC6qXb8/PQgmX7LRNRf/8U/jurWuNjyX304ygs2gU/HPSZW4VpnbrGwMJ2ZG9Wtls1ZZ9jEkuVonAoltycUXnUT+FiCUv7mnQfgLmeRqBtyIuxoQPvYZFSBJAlADlLDC9i1XMMj3zuxbGqva3JZx01WTMkgz1pgsD7kJ9XOLol5xDcc+A0h4mN1bgOHSFw0GuzQQHLtpBV0kifEhRkiBurUq6o7Q7skxY/RGDcFCIasYmOFtmTMGp7sWSijn6bntWxbMMH+hoLUIr+RMTJ/j7rce+xlseLR+2BjQLFbN/4IkwJEUZQsgvpydL/ro0TSUuXtG9EuU3MPQvTVqRw6709I7jE907wrzEw5OmRFY51b9zOprDjcBPfAcxXizXvuU7YZ+DEkorJzgEmXl0INGUz3RzxGoveO/Ip5EYqfzA02o3L5Cdj/TBr0TCgANZvtIT5OcFlgG9VY9c5UaWhh+j+SBuOT7AOl4dCcR9POTsPWQfhepkQqwbU/+xiJWfRj3778ZRraesoP3Mqmm7iMjNY2W+Gt3NFltfIaiVW8jJAz/OD4DT1lHrA11Bog6jkupGSHDknYkUhsT2MHWTSHwYl3XaO4nlrc/NXk6QdnPB9kqnJuO/X0Da2UQO5GjnZ7u1PWm7GbwJwMpq4kTHQ5gYxFIy4ZEABSpxn3ojCtSvc00hrm9tvzT6+gnksh5PNmLYzlesMAZw00VDH+ELskPqJx2QWxf9KYrNwtDaz1nuO9SaEuW4lze7ZC0N+QJN8ZKA6v+6m9yKqJwXVTPXe+FdHLJXQiznhE1IjGkRzoG2hSPeUohapowO3wVsM6e9DOWjrmHiJ72aTiFjFmB5v7/yF7KMtYo3gYgAuHss+0pPjogkW37XHFWEAsuQphk4iZbwqsMSSgHEPJww4kcHtUe5yTvxW0krikFg2jeJnfaE5BnA8S0wqmuJDCMFe4qPPOjgUJNY3kfc+NCl/cSvrtb86gqVVbDWi3CpEqXl1jD0GugpT0UFhgDUBwsCEwb6wcwM84tCzycVtkctky+ukJONeI9k5P3caL85SEF6Zd6XlNS4hzfNS80wkdX0ffhBwAtvX7GdapQ7lA2k7CBVFCPBIA3Mb1/lJz5OCSFAIDFvKJE2WAIiwKhZkx7amBsgCTOErvJVxDS5DkgTn2HWEenlZcFkHU2jWMC7hzibh1uTmsKpkbdnMiRMHnZ3mOsnvM2uW2ibT8UxIs+YDV/4h5KavJ8FutNns7FmA4r+EhrEx48oY3UvjzmwyJqWEvxVzOo8QbAGRmZcF0CcbLXQh+9Pvb7GB/5FtyI2xw5iewB+PIDuNE95nc3puTvAw8OCEgVuMx7FKSRQZd+AuSzxeRbxRitTPYQGsvR6RP7ft2WXXG6Dx1JUxhexuNqUPcnuQJ4HdN9wjRqClBp3Q+Dy2tq2JxLRdbjx/JmgYXufb/WevZ3bJ8FDKz0p/400WO7D0Y78dmN7tBNZLTYuJQFXig5cH4qSNnfSzeD2sBfMeumXeTqSYhqvFGBZ55/P+83kEeYLMav9VRsOB693Xbyt+aZPYOklmfPxTCiIsNv18X1utoCqKGuDc0p5GiBhtoAD7aEqYPoGoknnvGMIl+9kxufy933rxhLVwZZabL0rZZLKNfoikk0Bw90bv9U2KFeUelTlo3VCwz98ooByqHAthdAHwB9x1NWxG7mqS225YfYLfNn4eMzI2AB8GWwlCDjZ6l+ecPG0+MexwvqaeJzJGksB58/f8DKse7NMB3wOgmCDtZFtK6/tpLSPDAlQsWRI+7lypCV21Jgv0HgWEAz8N3bQG2xaKez4Kc9y141FkDovYuelgYqwsNZFwn5krORmDG8yS+kPteR6Z55Cahi6RuP2dsYy2rtspCqckXuGIev3WLAypjWiYufQcj3BPsjr6WQSbp60VXe4DLfO+VddCdy9k4uA5gOgM8WueDFgteY14FbEzFQBFK+zY62KS/GTB8neXJwmTIX11ia0mEH4duaV6vX5G4DRftGOta/7y1U0iTGwauocVvvt9CwbWSvu8yO41fYXv6TLPpEEJOm/39z0GWANodSlkwMP587cPi73gGM5C3okQJFCNlIvNgGyN0ttqRu1awtOxAlxrXoPt6UpzyWiAII0CMLwa1bR1KBvTJ3v5q6BCCIGCaCUb9q2MZ6Lg9LVYMci51Huya2bb1yDQBNl/vbVqiUKXYeoJLOWhG5U+4BtbeJra7984WaOyZ6I8ctZjJVASbhBJgZpAHLMUY4eQuEc6NO10x+1ESwuIJG1LeAO7PG6+m1IlVxhGV3fm3HEIIU6bb9xf5JkkDCyANyWjFEs1iIgabiQUMx4AMOX3zPiSgN9tX/ipnVpNGGI8/qFO6nMhs93DnMeN2aefn/FchV6dsbW09xgRvsosj97e6omuesp3PT+U1RQLm3qDmjRzkuIAVdMkjmQ9nqqhdqzu87IQ4ooZcAOUxfZdY29KWQ13ZW3gX934aDHj46l7UjkfmZGG7XecCj/Z5dOx+funV1RCORAQxIhsom9E/QPq5DNa9QUrPDyx1Rk/4N/oBXFooZK2CkF3VBXGRrYFnjoOaSzFoS1NWAr5LecYH8JC/tcxecrBZDzKmt+mEwwigRpnp+7cfFoAXOb4eJtaDiHRAK+kRLAXuYOouGX06qvYyg9PM//ebxQ9HDAS6tShAyiofc+JT0S2Xar84x9vLnxhdHiXuNnslnalAyrdhaSoR1/+/gk33Nd7GRzI4CLV/vGKlzliBLrAZcXy1CoLJQV6O3v+rDTHzGQIAKu7A7N05IGB8JYsodjcQdVA0lzT0ILRyewdobkfJOH5odnPW9r2J5j9nLaeT/OLzJTnozX8zSouiEIufnViyksc7gj+qVWfvQ6qMYRiNSq3fNEgLG2UiXm29HrBesPHWE0BM6LNo42S3i9ZtFAitmHaYXL87quH6P5ZCd1HazMCKOgs0lPyDzTVrqdcivXdL7pAdqyvx6k2BlaXjLivFKQN9P2LeFBqZbShnWN8jnCeafaFHZJTHeUVyTPqW1PBvRPhh7wCz7O9XZP42y/BvAncDnbTcpkYnXntMtYpQNfaV32Mi9Fdy0Mp6+p4V7sfuo4ogb1jdREphypjqjVkG44cjhScYN+yWUKs99WeBSK9LEXwFjcmx6XxN/z6g3gbpSPKhsI567P9m5FSiPX8xElvXR4Octslb2YPrEFBFXbYzIQXgs7QCg5CVJlMULxvhr150MzLUSkstfrO32eXWtgY+5I3/0RNGRZ7GdC5al71wJZz/z0SDXZD85mQ4N5tbRlwnbYbu+TamuK8T12P/OA8mj2cJmDtzzFHfd/tmEkfmz9MwGc9DzNnz9ADt3K6f+8P/XDDhzJ3moRdUiEsgZC5KS3vjqZi4N9u/nsxAcJPFK8VKmcAawaP20r0QmbQPoYqY91QIgnPgI9cW0ZAT5CrsfmD1bGQ8eyKjAZJwm5r84/xjHg2HVfH06ugk4ZTwLUd+5t91B+kYLq4CO81trRFdAjel72LYUetafqHJwja7pb60x6gjZbtstw8ebcbXbhRURjQNtK1tATux0zCnj6mpi28AOnG5PEZp9sBFyB+Vf5s59v0tR3eCB8l6M3RtgLxJYVTeGblhYkR7544Fe4hRqgLu3K3awCBXy+AHdf0DhhVRj2ejm8xoWjsP5R5NZtcbqRLOW9R6Zh6Squyxb589WGKZgrwsLA5r3z9rJ8XN5Mqqh5YfiqpX+IpqluW8lsNe7Wm7CBFFOYxluGgIr3HWZjxawKgrxpi7Xc9Vq5aIBi+ZwAMFRAdDg/IH6DUnJe0Y6R9ffQcFX/56iIdSR8oYrq5TzMnsjt2iVyMROz9F3vhSik/623Uke9zcMvOfqZWTtWEvTYk5/wkjUA1/ELXVwJja5rL27JOmzHfPBBsHJBJtXUa91RRbinIhkdBb+FG5NKgxNHsmfu9SBUDl/JOVUh7uubuI/Euyx9RQmVe5tZDbz81pYkRuJcJSCQWEr9+DGZUrghvVV48Cd5u2+93dZNV1gmO7b3DXZpRCuN6/irKmosG5KVdOvgvrElk322xN0xlg5mJDGw817oBHORKT+bF5c/z7/CcjPZ9WLo06R5bfLXI3qG685Hs3EaDiGwOKlRIqcxZQkWv0CAZrz/4QTmajjZG7bB4Wd4mFB4rxi3XolNYZsPS67nkjX3/Tg3/EmG27qCuuy/DhkKBRnutjB7Mifo8V0vDTKbfCi8igTUse3rv+MC+zcK9DUx05cqWuQspwyWMNaxTtjPCYbVgP3eX9ONDbh4gPhHt73cie/aUd1K8a7glnSr7aQq5m3KS5sSmDMk6a8OgkZrPmU+iQi5cqddY1VYxIKBUrPkbQR3Zt0l+wrSAjd0y8xDJxtWNZb5kG6OlR0ze0dgtSyK6T3uIHK30WJKDx/a6p3S3/EYpyEzQtyAJPmwllcCGg+ikx4MgPBBk/ZowjeCCWfp0jFc3v2ceQlVrWLNFNclucKIcNQ1G7I36CFodM9RkRyOxiFCN6xcqA+z2rs0LE4QNSt4iUMMPKrcm8o5rDcfp+V/toC78+etwc1ShQNaYfy/soGcHKWlEL3nRIZ77wjrL8OFaDVtWeiBO51yS/phREcx40HPvGcGc56efY3QpznoQjWvE21O27Y2QzFCv/1MrToYoxosEO1mt4xtuhYBwax9em2khDF8kWaUfXHs0I93tEODE5oOf8oiJVD2TwU/SmcD/Ldtx539lOR/zFt0jWZ9w+wgyMBirg9DjcOY56MKTyvoL8cbJ35Upluddle5MtM1tMxW/G+886Cd4NAJW7fNrI74YmkDJHHoJzsBs2TcriY3Zv37ztQPdBQ9mC2QNoiWDmS+q5He8gberbmyZF6a7f/BSqSvkAaIRRz9iP/w1ax4JmaEgGq0FsSwv7gl/2U0bTzBUOgiJsfjmP99+x1mm8fLbwkL0cSnPToLLn/5dU32l/lEg8K5BvK2gHbsI0Mn/OcfDOmISHcp/6g4EUB6B6AN8ud7Q8CXl9nrbCxOzvmzYoVfrpjo/dr1bMdnucZ6wbGWgj87dRhw3R4dY3hcEUQ+LsDV64ycfkXpb7o7tx9KySaOZa21S2sXDVdfPPchXAgYocpnCq0aWz0FVjlEoN4oBPzcF6G7pJLj+0XdCUod48V4yrEJ7tJFgjy/iQWH/qUGi7oRd0Edok+k7Xs6KRfgcjXJwwzpPnGcM+DVObBTQ+PtwFbz49GJ3rh6kJIDdf0t8QBSBn4j/hhjYrluYSh3hKorCZ1yMkGul9RbYS4pDBPHXdv2LBeqG/8snhAjG/PIWO4KazjhEz6KPi+E40aBVVmRtddlfq5DploaS2Z7pg2DDM0t6tEvqlcN+WhkG0eUBY9W9NdGtp0S/A7Awj5kntP8Q+uTCdCyCMcxwT7z1hLgt1qtjcIJ1OlB3jTD6zqspFUU4ERzNYC58f3DHfZaEs6zf+a22Q4QYn4iYcC3Dh6cjXWHgy/5HEa2AxQylCPbub+v/adl0COzm2rDqxfpmyIb4bVTtH2mKUrk38pm39HjZKdy2e8Ys0D4XQv/S91gJ2QB4ySvleBjr/kCJzzQ8NT6s6+VaRFH8SwJO5c75vpNrtVe+d95hvgDa57L3jtrvHzScoa6Wo05lQz/GdxCZTfleLb8MGXLFUPqTfBRg8uTYDRtw8M1bsoNgVYm68dHXFY9KvL2E+t4lTn4BCBAVrvAWwUxfcpx80IeVH3fSB+TU72yiHKmRhYs+h8gHGrAKQSpB6vgMymWK3UcBkezoz3Dxzbd4QLIPO14969+Uey2oCC69YHJocFEU5ZUgNjAJNdUfLl7OwlTaqSQGB2YaYu4riy+Rn6K/4kKfTgqePkRJ8/l2L2NaP97VBHXYIoHsv90QK1l8PBNjEaGZWEOZq0XFi9R0qHNa6nndYb8uMD/vcMD9xMeA3QZhvtBqpCoD0QQTRrpWgEklYxd/zBSEbD3M/ct2+bDe6r1lVX0uEe3NqU7/CMtdCKnwBQoaVPcvfOV5l0NGd/CCe06FPKpY0FRRHDA/Iyhc8JWBIjf4GeNhWbN5CVpQon6PPi5pblTLVhLpF47NGVW+h35Jh1oOD+EA6AD6pW/s3T6fnrmiWBYst44Rj03u95JgOycPAEFv5ZBC00Gv7uQXmtkM9WbBMKrSMZntFWim7qavOMzOLZeJ02v9UbQvkmAbwiMuTzYrzO3BZNch7JPqB973TzisI93J8NVqob+N+nsshFzOV2pKREumWTNjydMmM9kAg5zM2lwyZ1LRIyC2e57n/gZQu/fsLMVns8Qa8U0scH7HDByKuLMlXrsT4FgfZCnFnyeLDNygj2mprsQteCLpNNhGuSHIbbfWG1741nddlWpGT8bnUdynE3Am6rLCz//Cbbu5qozjAnE4MotydxviGwqGGRXic74LIRWWR4JwlN9xcF2y/4WAuurnNMzoKCpWcIe1o55O5mAwwlKGwcfv0MMWE6mcMrVTCbr6fbLDrxgorlPNYP2Fivk6BOpUEfYKH8irUdm9+ZT77zJaSnkBqNLKE9ZYbX0/rZfQzdsoiieEKIeWrFWge3el0JA7gIymflIcCsZzAgQKzP9S+H7vpR294uHMPD1ijFBGmhziBWfSVfpXcBO6URKnaEQMAINC20ihxKbGc/Qr8UkGR+o+jBeEOoIphB4EkIq6A/e3l8si60Z0Gzf38dPVTGoCw3Nkj7ki7IVHBYysvcd+xufKG9g/vNLG8Ty8pjg0BPGiFTAApP928kor/Cim7+Rtyc6ZP0t7TGfvz598vjk2LSwh/FZJUVulDG6qC11QrjRPPCj3pW9HY/Wb3ot9WINYx2G4fQM+pjUXEQ95cTCkFF2CLlHSjjktyrmAf39fdqBSlcQWXjy8aazZKiVv5DlvJCXNwm0ouGOdYmr0xDIod2npsB4uOQPll1zrb1VIkZERV7rXqjbNYsehoX0jw6AyJiZbQQSae3eMhyXR7JjwWnNNc1ypKzP9HZT5H5/jHgj8MnVjUSuYDZgS5xjw/+0oe9k8n/2+75Z5YUqGW71Vyx+r3gVvU1bg3M6MAfh+bobeJdV1E5zJb4xMgs8juWtSZwfJEDr3bBQtfFIpuhUGFm/gaGBqbfgwfFkDcEMN1s0twj+zOgtLWpEIhNDduYpve7Cma0IbEm2pOO+e/+sf/NPEHbTK9F1cRea4FF1p9BMLHvdHuX0hPX66bDall0vIZ3Tn8wAAAA" alt="Pranata HR – Mitra Transformasi SDM &amp; Organisasi" width="150" height="150">© <span id="yr"></span> Pranata HR. Semua hak dilindungi.</footer>

    <script>
        // Ganti nomor WhatsApp (format 62, tanpa + atau 0 di depan)
        var WA_NUMBER = "6281234567890";
        document.querySelectorAll("a.wa").forEach(function(a) {
            a.href = "https://wa.me/" + WA_NUMBER + "?text=" + encodeURIComponent(a.dataset.msg || "Halo, saya tertarik dengan Pranata HR.");
            a.target = "_blank";
            a.rel = "noopener";
        });
        document.getElementById("yr").textContent = new Date().getFullYear();
        var bM = document.getElementById("bM"),
            bY = document.getElementById("bY");

        function setMode(y) {
            bM.setAttribute("aria-pressed", !y);
            bY.setAttribute("aria-pressed", y);
            document.querySelectorAll(".p").forEach(function(e) {
                e.textContent = y ? e.dataset.y : e.dataset.m;
            });
            document.querySelectorAll(".u").forEach(function(e) {
                e.textContent = y ? " / tahun" : " / bulan";
            });
            document.querySelectorAll(".sub").forEach(function(e) {
                e.textContent = y ? e.dataset.y : "";
            });
            document.querySelectorAll(".btn-pilih-paket").forEach(function(e) {
                e.href = y ? e.dataset.yHref : e.dataset.mHref;
            });
        }
        bM.onclick = function() {
            setMode(false);
        };
        bY.onclick = function() {
            setMode(true);
        };
        setMode(false);

        // Mobile Header Menu Toggle
        var menuToggle = document.getElementById("menuToggle");
        var mobileMenu = document.getElementById("mobileMenu");
        var menuBackdrop = document.getElementById("menuBackdrop");

        function toggleMenu(open) {
            var shouldOpen = typeof open === 'boolean' ? open : !mobileMenu.classList.contains("open");
            if (shouldOpen) {
                mobileMenu.classList.add("open");
                menuToggle.classList.add("active");
                menuToggle.setAttribute("aria-expanded", "true");
                menuBackdrop.classList.add("open");
            } else {
                mobileMenu.classList.remove("open");
                menuToggle.classList.remove("active");
                menuToggle.setAttribute("aria-expanded", "false");
                menuBackdrop.classList.remove("open");
            }
        }

        if (menuToggle) {
            menuToggle.addEventListener("click", function(e) {
                e.stopPropagation();
                toggleMenu();
            });
        }

        if (menuBackdrop) {
            menuBackdrop.addEventListener("click", function() {
                toggleMenu(false);
            });
        }

        // Close menu when clicking any link inside mobile menu
        document.querySelectorAll(".mobile-menu a").forEach(function(link) {
            link.addEventListener("click", function() {
                toggleMenu(false);
            });
        });

        // Close menu on ESC key
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape" && mobileMenu && mobileMenu.classList.contains("open")) {
                toggleMenu(false);
            }
        });
    </script>
</body>

</html>