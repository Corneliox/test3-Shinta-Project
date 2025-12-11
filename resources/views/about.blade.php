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
                    <h2 class="text-white">{{ __('messages.about_title') }}</h2>
                </div>
            </div>
        </div>
    </header>

    {{-- 2. DESCRIPTION SECTION --}}
    <section class="section-padding pb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-12 mx-auto">
                    <div class="custom-block bg-white shadow-sm p-5 text-center">
                        <p class="lead text-muted" style="font-weight: 400; line-height: 1.8;">
                            {!! nl2br(e(__('messages.about_description'))) !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. VISI & MISI SECTION --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                
                {{-- VISI (Full Width Card) --}}
                <div class="col-12 mb-4">
                    <div class="custom-block bg-white shadow-lg p-5 text-center">
                        <h3 class="mb-4 text-uppercase text-primary">{{ __('messages.visi_title') }}</h3>
                        <p class="lead fst-italic text-dark">
                            "{{ __('messages.visi_text') }}"
                        </p>
                    </div>
                </div>

                {{-- MISI (3 Pillars) --}}
                <div class="col-12">
                    <div class="custom-block bg-light shadow-sm p-5">
                        <h3 class="mb-5 text-center text-uppercase">{{ __('messages.misi_title') }}</h3>
                        <div class="row text-center">
                            
                            {{-- Painting --}}
                            <div class="col-md-4 mb-4">
                                <div class="p-4 bg-white rounded shadow-sm h-100">
                                    <div class="mb-3 text-primary"><i class="bi-palette fs-1"></i></div>
                                    <h4 class="mb-3">{{ __('messages.misi_painting') }}</h4>
                                    <p class="text-muted">{{ __('messages.misi_painting_desc') }}</p>
                                </div>
                            </div>

                            {{-- Sharing --}}
                            <div class="col-md-4 mb-4">
                                <div class="p-4 bg-white rounded shadow-sm h-100">
                                    <div class="mb-3 text-success"><i class="bi-share fs-1"></i></div>
                                    <h4 class="mb-3">{{ __('messages.misi_sharing') }}</h4>
                                    <p class="text-muted">{{ __('messages.misi_sharing_desc') }}</p>
                                </div>
                            </div>

                            {{-- Empowering --}}
                            <div class="col-md-4 mb-4">
                                <div class="p-4 bg-white rounded shadow-sm h-100">
                                    <div class="mb-3 text-danger"><i class="bi-heart fs-1"></i></div>
                                    <h4 class="mb-3">{{ __('messages.misi_empowering') }}</h4>
                                    <p class="text-muted">{{ __('messages.misi_empowering_desc') }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 4. HISTORY & TIMELINE SECTION --}}
    <section class="section-padding section-bg">
        <div class="container-fluid">
            <div class="row justify-content-center">
                
                {{-- History Text --}}
                <div class="col-lg-10 col-12 text-center mb-5">
                    <h2 class="mb-3">{{ __('messages.history_title') }}</h2>
                    <p class="text-muted mb-5">{{ __('messages.history_subtitle') }}</p>
                    
                    <div class="bg-white p-4 rounded shadow-sm text-start mx-auto" style="max-width: 900px;">
                        <p class="text-muted" style="line-height: 1.8;">
                            {!! nl2br(e(__('messages.history_desc'))) !!}
                        </p>
                    </div>
                </div>
            </div>

            {{-- TIMELINE (Exhibitions) --}}
            <div class="timeline mt-5">
                
                {{-- 2018 --}}
                <div class="timeline__event animated fadeInUp delay-3s timeline__event--type1">
                    <div class="timeline__event__icon"><i class="bi-brush"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2018_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2018_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2018_desc') }}</p></div>
                    </div>
                </div>

                {{-- 2019 --}}
                <div class="timeline__event animated fadeInUp delay-2s timeline__event--type2">
                    <div class="timeline__event__icon"><i class="bi-easel"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2019_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2019_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2019_desc') }}</p></div>
                    </div>
                </div>

                {{-- 2020 --}}
                <div class="timeline__event animated fadeInUp delay-1s timeline__event--type3">
                    <div class="timeline__event__icon"><i class="bi-mask"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2020_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2020_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2020_desc') }}</p></div>
                    </div>
                </div>

                {{-- 2022 --}}
                <div class="timeline__event animated fadeInUp timeline__event--type1">
                    <div class="timeline__event__icon"><i class="bi-flower1"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2022_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2022_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2022_desc') }}</p></div>
                    </div>
                </div>

                {{-- 2023 --}}
                <div class="timeline__event animated fadeInUp timeline__event--type2">
                    <div class="timeline__event__icon"><i class="bi-building"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2023_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2023_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2023_desc') }}</p></div>
                    </div>
                </div>

                {{-- 2024 --}}
                <div class="timeline__event animated fadeInUp timeline__event--type3">
                    <div class="timeline__event__icon"><i class="bi-award"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2024_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2024_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2024_desc') }}</p></div>
                    </div>
                </div>

                {{-- 2025 --}}
                <div class="timeline__event animated fadeInUp timeline__event--type1">
                    <div class="timeline__event__icon"><i class="bi-stars"></i></div>
                    <div class="timeline__event__date">{{ __('messages.timeline_2025_date') }}</div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">{{ __('messages.timeline_2025_title') }}</div>
                        <div class="timeline__event__description"><p>{{ __('messages.timeline_2025_desc') }}</p></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 5. OTHER ACTIVITIES LIST --}}
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-5">
                    <h2 class="text-center">{{ __('messages.activities_title') }}</h2>
                </div>
                <div class="col-12">
                    <div class="custom-block bg-white shadow-sm p-5" style="border-left: 5px solid #9251ac; text-align: left;">
                        <ul class="list-unstyled text-muted" style="line-height: 2.2;">
                            @foreach(explode("\n", __('messages.activities_list')) as $activity)
                                @if(!empty(trim($activity)))
                                    <li class="d-flex align-items-start mb-2">
                                        <i class="bi-check-circle-fill text-success me-3 mt-1"></i>
                                        <span>{{ $activity }}</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. ORGANIZATION STRUCTURE --}}
    <section class="section-padding section-bg">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2>{{ __('messages.structure_title') }}</h2>
                </div>
                
                <div class="col-12">
                    <div class="org-tree">
                        
                        {{-- LEVEL 1: PENASEHAT --}}
                        <ul>
                            <li>
                                <div class="org-card advisor">
                                    <div class="role">{{ __('messages.org_advisor') }}</div>
                                    <div class="name">Ratri Cipto Hening</div>
                                    <div class="name">Irene Indriasari</div>
                                </div>

                                {{-- LEVEL 2: KETUA --}}
                                <ul>
                                    <li>
                                        <div class="org-card chair">
                                            <div class="role">{{ __('messages.org_chair') }}</div>
                                            <div class="name">Widji Pangastuti</div>
                                        </div>

                                        {{-- LEVEL 3: WAKIL KETUA --}}
                                        <ul>
                                            <li>
                                                <div class="org-card">
                                                    <div class="role">{{ __('messages.org_vice_chair') }}</div>
                                                    <div class="name">Noer Aida</div>
                                                </div>

                                                {{-- LEVEL 4: SEKRETARIS & BENDAHARA --}}
                                                <ul>
                                                    {{-- Sekretaris --}}
                                                    <li>
                                                        <div class="org-card">
                                                            <div class="role">{{ __('messages.org_secretary') }}</div>
                                                            <div class="name">Nora Betty Kurnia</div>
                                                            <div class="name">Lu’lu’ Fajriyatus Sa’adah</div>
                                                        </div>
                                                    </li>

                                                    {{-- Bendahara --}}
                                                    <li>
                                                        <div class="org-card">
                                                            <div class="role">{{ __('messages.org_treasurer') }}</div>
                                                            <div class="name">Erny Dhany</div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    {{-- LEVEL 5: ANGGOTA (Grid Layout) --}}
                    <div class="mt-5 pt-4 border-top">
                        <h4 class="text-center mb-4">{{ __('messages.org_member') }}</h4>
                        <div class="row justify-content-center text-center">
                            @php
                                $members = [
                                    'Anastasia Pera', 'Dina Sartika', 'Erni Louw', 
                                    'Hayyun Nihayah', 'Lidya Indriati', 'Maria Arkananta', 
                                    'Marlita Sari', 'Tilamsari', 'Yuniarti'
                                ];
                            @endphp
                            
                            @foreach($members as $member)
                                <div class="col-lg-3 col-md-4 col-6 mb-3">
                                    <div class="bg-white p-3 rounded shadow-sm h-100 member-card">
                                        <p class="mb-0 fw-bold">{{ $member }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

{{-- TIMELINE & ORG CHART CSS --}}
    <style>
        /* --- TIMELINE VARS --- */
        :root {
            --content-width: calc(40vw - 84px);
            --color1: #9251ac;
            --color2: #f6a4ec;
            --color3: #87bbfe;
            --color4: #555ac0;
            --color5: #24b47e;
            --color6: #aff1b6;
        }

        /* --- ORGANIZATION TREE CSS (FIXED) --- */
        .org-tree * {
            box-sizing: border-box;
        }

        .org-tree ul {
            padding-top: 20px; 
            padding-left: 0; /* <--- CRITICAL FIX: Removes browser default indentation */
            position: relative;
            transition: all 0.5s;
            display: flex;
            justify-content: center;
        }

        .org-tree li {
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 10px 0 10px; /* Added spacing between siblings */
            transition: all 0.5s;
        }

        /* --- CONNECTING LINES --- */
        
        /* 1. Horizontal connectors for siblings */
        .org-tree li::before, .org-tree li::after {
            content: '';
            position: absolute; top: 0; right: 50%;
            border-top: 2px solid #ccc;
            width: 50%; height: 20px;
        }
        .org-tree li::after {
            right: auto; left: 50%;
            border-left: 2px solid #ccc;
        }

        /* 2. Remove connectors for single children (Vertical Stacks) */
        .org-tree li:only-child::after, .org-tree li:only-child::before {
            display: none;
        }
        .org-tree li:only-child { 
            padding-top: 0;
        }

        /* 3. Remove outer connectors for first/last elements in a row */
        .org-tree li:first-child::before, .org-tree li:last-child::after {
            border: 0 none;
        }
        .org-tree li:last-child::before{
            border-right: 2px solid #ccc;
            border-radius: 0 5px 0 0;
        }
        .org-tree li:first-child::after{
            border-radius: 5px 0 0 0;
        }

        /* 4. Vertical Line Down from Parent to Child UL */
        .org-tree ul ul::before{
            content: '';
            position: absolute; top: 0; left: 50%;
            border-left: 2px solid #ccc;
            width: 0; height: 20px;
            transform: translateX(-50%); /* Ensures perfect centering */
        }

        /* --- THE CARDS --- */
        .org-card {
            border: 1px solid #ddd;
            padding: 15px 20px;
            text-decoration: none;
            color: #666;
            font-family: 'Open Sans', sans-serif;
            display: inline-block;
            border-radius: 5px;
            background: white;
            transition: all 0.3s;
            min-width: 180px; /* Made slightly wider for better text fit */
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative; 
            z-index: 2; /* Keeps card above lines */
        }

        .org-card:hover, .member-card:hover {
            background: #fdfdfd;
            border-color: #9251ac;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(146, 81, 172, 0.2);
        }

        /* Role Styles */
        .org-card .role {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9251ac;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .org-card .name {
            font-weight: 600;
            color: #333;
            font-size: 1rem;
        }

        /* Special Styles */
        .org-card.advisor {
            background: #9251ac;
            color: white;
            border: none;
        }
        .org-card.advisor .role { color: #f6a4ec; }
        .org-card.advisor .name { color: white; }

        .org-card.chair {
            border-top: 4px solid #9251ac;
        }

        /* --- TIMELINE CSS (Restored) --- */
        .timeline { display: flex; flex-direction: column; margin: 20px auto; position: relative; }
        .timeline__event { margin-bottom: 20px; position: relative; display: flex; margin: 20px 0; border-radius: 6px; align-self: center; width: 50vw; }
        .timeline__event:nth-child(2n + 1) { flex-direction: row-reverse; }
        .timeline__event:nth-child(2n + 1) .timeline__event__date { border-radius: 0 6px 6px 0; }
        .timeline__event:nth-child(2n + 1) .timeline__event__content { border-radius: 6px 0 0 6px; }
        .timeline__event__content { padding: 20px; box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25), 0 18px 36px -18px rgba(0, 0, 0, 0.3), 0 -12px 36px -8px rgba(0, 0, 0, 0.025); background: #fff; width: calc(40vw - 84px); border-radius: 0 6px 6px 0; }
        .timeline__event__date { color: var(--color2); font-size: 1.5rem; font-weight: 600; background: var(--color1); display: flex; align-items: center; justify-content: center; white-space: nowrap; padding: 0 20px; border-radius: 6px 0 0 6px; }
        .timeline__event__icon { display: flex; align-items: center; justify-content: center; color: var(--color1); padding: 20px; align-self: center; margin: 0 20px; background: var(--color2); border-radius: 100%; width: 40px; box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25), 0 18px 36px -18px rgba(0, 0, 0, 0.3), 0 -12px 36px -8px rgba(0, 0, 0, 0.025); padding: 40px; height: 40px; position: relative; }
        .timeline__event__icon i { font-size: 32px; }
        .timeline__event__icon:before { content: ""; width: 2px; height: 100%; background: var(--color2); position: absolute; top: 0%; left: 50%; right: auto; z-index: -1; transform: translateX(-50%); animation: fillTop 2s forwards 4s ease-in-out; }
        .timeline__event__icon:after { content: ""; width: 100%; height: 2px; background: var(--color2); position: absolute; right: 0; z-index: -1; top: 50%; left: auto; transform: translateY(-50%); animation: fillLeft 2s forwards 4s ease-in-out; }
        .timeline__event__description { flex-basis: 100%; }
        .timeline__event:nth-child(2n + 1) .timeline__event__icon:before { left: 50%; right: auto; transform: translateX(-50%); }
        .timeline__event:nth-child(2n + 1) .timeline__event__icon:after { right: 0; left: auto; transform: translateY(-50%); animation: fillLeft 2s forwards 4s ease-in-out; }
        .timeline__event--type2 .timeline__event__date { color: var(--color3); background: var(--color4); }
        .timeline__event--type2 .timeline__event__icon { background: var(--color3); color: var(--color4); }
        .timeline__event--type2 .timeline__event__icon:before, .timeline__event--type2 .timeline__event__icon:after { background: var(--color3); }
        .timeline__event--type2 .timeline__event__title { color: var(--color4); }
        .timeline__event--type3 .timeline__event__date { color: var(--color6); background-color: var(--color5); }
        .timeline__event--type3 .timeline__event__icon { background: var(--color6); color: var(--color5); }
        .timeline__event--type3 .timeline__event__icon:before, .timeline__event--type3 .timeline__event__icon:after { background: var(--color6); }
        .timeline__event--type3 .timeline__event__title { color: var(--color5); }
        .timeline__event__title { font-size: 1.2rem; text-transform: uppercase; font-weight: 600; color: var(--color1); letter-spacing: 1.5px; margin-bottom: 5px; }
        .timeline__event:last-child .timeline__event__icon:before { content: none; }

        @keyframes fillLeft { 100% { right: 100%; } }
        @keyframes fillTop { 100% { top: 100%; } }
        @keyframes fillLeftOdd { 100% { left: 100%; } }

        @media (max-width: 786px) {
            .timeline__event { flex-direction: column; width: 100%; }
            .timeline__event__content { width: 100%; border-radius: 6px; }
            .timeline__event__icon { border-radius: 6px 6px 0 0; width: 100%; margin: 0; box-shadow: none; }
            .timeline__event__icon:before, .timeline__event__icon:after { display: none; }
            .timeline__event__date { border-radius: 0; padding: 20px; }
            .timeline__event:nth-child(2n + 1) { flex-direction: column; }
            .timeline__event:nth-child(2n + 1) .timeline__event__date { border-radius: 0; padding: 20px; }
            .timeline__event:nth-child(2n + 1) .timeline__event__icon { border-radius: 6px 6px 0 0; margin: 0; }
            
            /* Mobile Tree */
            .org-tree ul { flex-direction: column; padding-left: 0; }
            .org-tree li { padding: 10px 0; }
            .org-tree li::before, .org-tree li::after, .org-tree ul ul::before { display: none; }
            .org-card { width: 100%; display: block; }
        }
    </style>
@endsection