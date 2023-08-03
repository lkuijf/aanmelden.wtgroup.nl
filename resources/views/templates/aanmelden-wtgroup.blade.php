<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title')</title>
    <meta name="description" content="@yield('meta_description')">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    {{-- @yield('extra_head') --}}
</head>
<body>
    {{-- @yield('after_body_tag') --}}
    @if(session('success'))
        <div class="alert alert-success">
            <div><p class="thumbsUpIcon"></p></div>
            {{-- @if(session('success') == 'contact')<div><p>{{ $data['website_options']->form_success }}</p></div>@endif --}}
            @if(session('success') == 'subscription')
                {{-- <div><p>{{ $data['website_options']->form_subscription_success }}</p></div> --}}
                <div><p>Gelukt</p></div>
            @else
                <div><p>{{ session('success') }}</p></div>
            @endif
            {{-- <div><p>Gelukt</p></div> --}}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <div><p class="exclamationTriangleIcon"></p></div>
            {{-- <div><p>{{ $data['website_options']->form_error }}</p></div> --}}
            <div><p>Aanmelden is helaas mislukt, check het formulier op fouten.</p></div>
        </div>
    @endif
    <header>
        <img src="{{ asset('statics/wt-group-logo.png') }}" alt="W.T. Group B.V.">
        <span class="triangle-top-left"></span>
        @yield('header')
    </header>

    <div class="contentWrapper">
        @yield('content')
    </div>
    
    <footer>
        <p>Erat velit scelerisque in dictum non consectetur a erat. Felis eget velit aliquet sagittis id consectetur purus ut faucibus. Turpis nunc eget lorem dolor sed viverra ipsum nunc.</p>
    </footer>

    <a href="" id="toTop"></a>
    <script src="{{ asset('js/script.js') }}"></script>
    @if($errors->any())
    <script>
        const errors = document.querySelectorAll('.error');
        errors.forEach((el) => {
            const err = document.createElement('span');
            err.classList.add('errMsg');
            err.innerHTML = el.dataset.errMsg;
            el.appendChild(err);
        });
    </script>
    @endif
    {{-- @yield('before_closing_body_tag') --}}
</body>
</html>