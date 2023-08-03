@section('header')
<picture>
    <source media="(min-width:1200px)" srcset="{!! $image['sizes']['2048x2048'] !!}">
    <source media="(min-width:1024px)" srcset="{!! $image['sizes']['1536x1536'] !!}">
    <source media="(min-width:768px)" srcset="{!! $image['sizes']['1536x1536'] !!}">
    <source media="(min-width:480px)" srcset="{!! $image['sizes']['large'] !!}">
    <img src="{!! $image['sizes']['medium_large'] !!}" alt="{{ $image['alt'] }}">
</picture>
@endsection
@section('content')
    <h1>{{ $title }}</h1>
    {!! $text !!}
    <form action="{{ route('submitRegistrationForm') }}" method="post">
        @csrf
        <div class="fieldlist">
            <div @error('full_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-full_name">Volledige naam *</label><input type="text" id="form-full_name" name="full_name" size="25" value="{{ old('full_name') }}" placeholder="Je volledige naam"></div>
            <div @error('first_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-first_name">Voornaam *</label><input type="text" id="form-first_name" name="first_name" size="15" value="{{ old('first_name') }}" placeholder="Je voornaam"></div>
            <div @error('last_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-last_name">Achternaam *</label><input type="text" id="form-last_name" name="last_name" size="15" value="{{ old('last_name') }}" placeholder="Je achternaam"></div>
            <div @error('email')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-email">E-mail Adres *</label><input type="text" id="form-email" name="email" value="{{ old('email') }}" placeholder="Je e-mail adres"></div>
            <div @error('birth_date')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-birth_date">Geboortedatum *</label><input type="text" id="form-birth_date" name="birth_date" size="10" value="{{ old('birth_date') }}" placeholder="DD-MM-JJJJ"></div>
            <div @error('company')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-company">Bedrijfsnaam *</label><input type="text" id="form-company" name="company" size="25" value="{{ old('company') }}" placeholder="Bij welk bedrijf werk je?"></div>
            <div>
                <label for="form-partner">Inclusief partner?</label>
                <input type="checkbox" id="form-partner" name="partner" value="1"><span>Ja</span>
            </div>
            <div><label for="form-partner_name">Naam partner</label><input type="text" id="form-partner_name" name="partner_name" value="{{ old('partner_name') }}" placeholder="Naam van je partner"></div>
            <div>
                <label for="form-children_amount">Aantal kinderen</label>
                <select name="children_amount" id="form-children_amount">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>
            <div><label for="form-children_ages">Leeftijd kinderen</label><input type="text" id="form-children_ages" name="children_ages" value="{{ old('children_ages') }}" placeholder="Hoe oud zijn je kinderen?"></div>


            {{-- <div><label for="form-phone">Phone number</label><input type="text" id="form-phone" name="Telefoon" value="{{ old('Telefoon') }}"></div>
            <div @error('E-mail_adres')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-email">E-mail Address *</label><input type="text" id="form-email" name="E-mail_adres" value="{{ old('E-mail_adres') }}"></div>
            <div><label for="form-company">Company</label><input type="text" id="form-company" name="Bedrijfsnaam" value="{{ old('Bedrijfsnaam') }}"></div>     --}}
        </div>
        <input type="hidden" name="original_submit_page" value="{{ basename(parse_url(URL::current(), PHP_URL_PATH)) }}">
        <input type="text" name="valkuil" value="" class="snare">
        <input type="text" name="valstrik" value="" class="snare">
        <div class="fieldlist">
            <div>
                <label>&nbsp;</label>
                <button type="submit">Aanmelden</button>
            </div>
        </div>
    </form>
@endsection