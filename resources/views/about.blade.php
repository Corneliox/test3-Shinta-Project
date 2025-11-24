@extends('layouts.main')

@section('content')

    {{-- 1. HERO SECTION --}}
    <header class="site-header d-flex flex-column justify-content-center align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 col-12 mx-auto text-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="/">Homepage</a></li>
                            <li class="breadcrumb-item active" aria-current="page">About Us</li>
                        </ol>
                    </nav>
                    <h2 class="text-white">Tentang WOPANCO</h2>
                </div>
            </div>
        </div>
    </header>

    {{-- 2. VISI & MISI SECTION --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12 mb-4 mb-lg-0">
                    <div class="custom-block bg-white shadow-lg p-5">
                        <h3 class="mb-4 text-center">Visi</h3>
                        <p class="text-center lead text-muted">
                            "Menjadi komunitas pelukis wanita terdepan di Semarang yang menginspirasi, memberdayakan, dan membawa seni lukis lokal ke panggung global."
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="custom-block bg-white shadow-lg p-5">
                        <h3 class="mb-4 text-center">Misi</h3>
                        <ul class="text-muted">
                            <li class="mb-2">Membangun jejaring yang solid antar seniman wanita.</li>
                            <li class="mb-2">Menyelenggarakan pameran seni berkualitas secara berkala.</li>
                            <li class="mb-2">Memberikan edukasi dan workshop seni kepada masyarakat.</li>
                            <li class="mb-2">Meningkatkan apresiasi masyarakat terhadap karya seni lokal.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. TIMELINE HISTORY SECTION --}}
    <section class="section-padding section-bg">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Sejarah WOPANCO</h2>
                    <p>Perjalanan kami dari masa ke masa</p>
                </div>
            </div>

            <div class="timeline">
                
                {{-- Event 1 --}}
                <div class="timeline__event animated fadeInUp delay-3s timeline__event--type1">
                    <div class="timeline__event__icon">
                        <i class="bi-brush"></i>
                    </div>
                    <div class="timeline__event__date">
                        September 2015
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            Pendirian Komunitas
                        </div>
                        <div class="timeline__event__description">
                            <p>Wopanco didirikan oleh sekolompok seniman wanita yang memiliki visi yang sama untuk memajukan seni di Semarang.</p>
                        </div>
                    </div>
                </div>

                {{-- Event 2 --}}
                <div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
                    <div class="timeline__event__icon">
                        <i class="bi-easel"></i>
                    </div>
                    <div class="timeline__event__date">
                        Juni 2016
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            Pameran Perdana
                        </div>
                        <div class="timeline__event__description">
                            <p>Menyelenggarakan pameran perdana "Wanita & Warna" yang dihadiri oleh lebih dari 500 pengunjung.</p>
                        </div>
                    </div>
                </div>

                {{-- Event 3 --}}
                <div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
                    <div class="timeline__event__icon">
                        <i class="bi-people"></i>
                    </div>
                    <div class="timeline__event__date">
                        Oktober 2019
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            Kolaborasi Nasional
                        </div>
                        <div class="timeline__event__description">
                            <p>Bekerjasama dengan galeri nasional untuk membawa karya anggota Wopanco ke panggung yang lebih luas.</p>
                        </div>
                    </div>
                </div>

                {{-- Event 4 --}}
                <div class="timeline__event animated fadeInUp timeline__event--type1">
                    <div class="timeline__event__icon">
                        <i class="bi-globe"></i>
                    </div>
                    <div class="timeline__event__date">
                        2024 - Sekarang
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            Era Digital
                        </div>
                        <div class="timeline__event__description">
                            <p>Meluncurkan platform digital untuk memudahkan akses masyarakat terhadap karya seni dan profil seniman.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 4. ORGANIZATION STRUCTURE --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>Struktur Organisasi</h2>
                </div>
                
                {{-- Placeholder for Org Chart --}}
                <div class="col-12 text-center">
                    <div class="custom-block bg-white shadow-lg p-5">
                        <img src="{{ asset('images/businesswoman-using-tablet-analysis.jpg') }}" alt="Organization Structure" class="img-fluid rounded" style="max-height: 500px; object-fit: contain;">
                        <p class="mt-3 text-muted">Bagan Struktur Organisasi WOPANCO</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TIMELINE CSS (Converted from SCSS) --}}
    <style>
        /* Variables Converted to CSS */
        :root {
            --content-width: calc(40vw - 84px);
            --margin: 20px;
            --spacing: 20px;
            --bdrs: 6px;
            --circle-size: 40px;
            --icon-size: 32px;
            --bdrs-icon: 100%;
            --color1: #9251ac;
            --color2: #f6a4ec;
            --color3: #87bbfe;
            --color4: #555ac0;
            --color5: #24b47e;
            --color6: #aff1b6;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            margin: 20px auto;
            position: relative;
        }

        .timeline__event {
            margin-bottom: 20px;
            position: relative;
            display: flex;
            margin: 20px 0;
            border-radius: 6px;
            align-self: center;
            width: 50vw;
        }

        /* Reverse layout for odd items */
        .timeline__event:nth-child(2n + 1) {
            flex-direction: row-reverse;
        }

        .timeline__event:nth-child(2n + 1) .timeline__event__date {
            border-radius: 0 6px 6px 0;
        }

        .timeline__event:nth-child(2n + 1) .timeline__event__content {
            border-radius: 6px 0 0 6px;
        }

        .timeline__event:nth-child(2n + 1) .timeline__event__icon:before {
            content: "";
            width: 2px;
            height: 100%;
            background: var(--color2);
            position: absolute;
            top: 0%;
            left: 50%;
            right: auto;
            z-index: -1;
            transform: translateX(-50%);
            animation: fillTop 2s forwards 4s ease-in-out;
        }

        .timeline__event:nth-child(2n + 1) .timeline__event__icon:after {
            content: "";
            width: 100%;
            height: 2px;
            background: var(--color2);
            position: absolute;
            right: 0;
            z-index: -1;
            top: 50%;
            left: auto;
            transform: translateY(-50%);
            animation: fillLeft 2s forwards 4s ease-in-out;
        }

        .timeline__event__title {
            font-size: 1.2rem;
            line-height: 1.4;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--color1);
            letter-spacing: 1.5px;
        }

        .timeline__event__content {
            padding: 20px;
            box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25), 0 18px 36px -18px rgba(0, 0, 0, 0.3), 0 -12px 36px -8px rgba(0, 0, 0, 0.025);
            background: #fff;
            width: calc(40vw - 84px);
            border-radius: 0 6px 6px 0;
        }

        .timeline__event__date {
            color: var(--color2);
            font-size: 1.5rem;
            font-weight: 600;
            background: var(--color1);
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            padding: 0 20px;
            border-radius: 6px 0 0 6px;
        }

        .timeline__event__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color1);
            padding: 20px;
            align-self: center;
            margin: 0 20px;
            background: var(--color2);
            border-radius: 100%;
            width: 40px;
            box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25), 0 18px 36px -18px rgba(0, 0, 0, 0.3), 0 -12px 36px -8px rgba(0, 0, 0, 0.025);
            padding: 40px;
            height: 40px;
            position: relative;
        }

        .timeline__event__icon i {
            font-size: 32px;
        }

        .timeline__event__icon:before {
            content: "";
            width: 2px;
            height: 100%;
            background: var(--color2);
            position: absolute;
            top: 0%;
            z-index: -1;
            left: 50%;
            transform: translateX(-50%);
            animation: fillTop 2s forwards 4s ease-in-out;
        }

        .timeline__event__icon:after {
            content: "";
            width: 100%;
            height: 2px;
            background: var(--color2);
            position: absolute;
            left: 0%;
            z-index: -1;
            top: 50%;
            transform: translateY(-50%);
            animation: fillLeftOdd 2s forwards 4s ease-in-out;
        }

        .timeline__event__description {
            flex-basis: 100%;
        }

        /* TYPE 2 COLORS */
        .timeline__event--type2 .timeline__event__date {
            color: var(--color3);
            background: var(--color4);
        }

        .timeline__event--type2 .timeline__event__icon {
            background: var(--color3);
            color: var(--color4);
        }

        .timeline__event--type2 .timeline__event__icon:before,
        .timeline__event--type2 .timeline__event__icon:after {
            background: var(--color3);
        }

        .timeline__event--type2 .timeline__event__title {
            color: var(--color4);
        }

        /* TYPE 3 COLORS */
        .timeline__event--type3 .timeline__event__date {
            color: var(--color6);
            background-color: var(--color5);
        }

        .timeline__event--type3 .timeline__event__icon {
            background: var(--color6);
            color: var(--color5);
        }

        .timeline__event--type3 .timeline__event__icon:before,
        .timeline__event--type3 .timeline__event__icon:after {
            background: var(--color6);
        }

        .timeline__event--type3 .timeline__event__title {
            color: var(--color5);
        }

        .timeline__event:last-child .timeline__event__icon:before {
            content: none;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 786px) {
            .timeline__event {
                flex-direction: column;
                align-self: center;
                width: 100%;
            }

            .timeline__event__content {
                width: 100%;
                border-radius: 6px;
            }

            .timeline__event__icon {
                border-radius: 6px 6px 0 0;
                width: 100%;
                margin: 0;
                box-shadow: none;
            }

            .timeline__event__icon:before,
            .timeline__event__icon:after {
                display: none;
            }

            .timeline__event__date {
                border-radius: 0;
                padding: 20px;
            }

            .timeline__event:nth-child(2n + 1) {
                flex-direction: column;
                align-self: center;
            }

            .timeline__event:nth-child(2n + 1) .timeline__event__date {
                border-radius: 0;
                padding: 20px;
            }

            .timeline__event:nth-child(2n + 1) .timeline__event__icon {
                border-radius: 6px 6px 0 0;
                margin: 0;
            }
        }

        /* ANIMATIONS */
        @keyframes fillLeft {
            100% { right: 100%; }
        }

        @keyframes fillTop {
            100% { top: 100%; }
        }

        @keyframes fillLeftOdd {
            100% { left: 100%; }
        }
    </style>
@endsection