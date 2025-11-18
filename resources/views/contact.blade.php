@extends('layouts.main')

@section('content')

    <header class="site-header d-flex flex-column justify-content-center align-items-center">
        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-5 col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Homepage</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Contact Form</li>
                        </ol>
                    </nav>
                    <h2 class="text-white">Contact Form</h2>
                </div>

            </div>
        </div>
    </header>


    <section class="section-padding section-bg">
        <div class="container">
            <div class="row">

                <div class="col-lg-12 col-12">
                    <h3 class="mb-4 pb-2">Kami mengharapkan Feedback dari anda 😁</h3>
                </div>

                <div class="col-lg-6 col-12">

                    {{-- === 1. ADD THE SUCCESS MESSAGE HERE === --}}
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    {{-- === 2. UPDATE THE FORM TAG AND ADD CSRF === --}}
                    <form action="{{ route('contact.store') }}" method="POST" class="custom-form contact-form" role="form">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="form-floating">
                                    {{-- Pre-fill name and email if user is logged in --}}
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" required="" value="{{ auth()->user()->name ?? old('name') }}">
                                    <label for="floatingInput">Nama</label>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-12"> 
                                <div class="form-floating">
                                    <input type="email" name="email" id="email" pattern="[^ @]*@[^ @]*" class="form-control" placeholder="Email address" required="" value="{{ auth()->user()->email ?? old('email') }}">
                                    <label for="floatingInput">Alamat email</label>
                                </div>
                            </div>

                            <div class="col-lg-12 col-12">
                                <div class="form-floating">
                                    {{-- 3. Fixed duplicate ID --}}
                                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Name" required="">
                                    <label for="floatingInput">Subject</label>
                                </div>

                                <div class="form-floating">
                                    {{-- 4. Your field name "message" is correct --}}
                                    <textarea class="form-control" id="message" name="message" placeholder="Tell me about the project"></textarea>
                                    <label for="floatingTextarea">Berikan feedback anda!</label>
                                </div>
                            </div>

                            <div class="col-lg-4 col-12 ms-auto">
                                <button type="submit" class="form-control">Submit</button>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="col-lg-5 col-12 mx-auto mt-5 mt-lg-0">
                    <iframe class="google-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2595.065641062665!2d-122.4230416990949!3d37.80335401520422!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80858127459fabad%3A0x808ba520e5e9edb7!2sFrancisco%20Park!5e1!3m2!1sen!2sth!4v1684340239744!5m2!1sen!2sth" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> <h5 class="mt-4 mb-2">Sekretariat Wopanco</h5>
                    <p>Bay St &, Ganti Alamat ini</p>
                </div>

            </div>
        </div>
    </section>
@endsection