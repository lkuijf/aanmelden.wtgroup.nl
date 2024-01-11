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
    @if($close_form)
        <h2>Registratie gesloten!</h2>
        <p>Je kan niet meer aanmelden voor dit evenement.</p>
    @else
    <form action="{{ route('submitRegistrationForm') }}" method="post">
        @csrf
        <div class="fieldlist">
            @if($show_participate)<div>
                <label for="form-participate">Ik ben aanwezig</label>
                <div class="participateRadioGroup">
                    <div><input type="radio" id="form-participate-yes" name="participate" value="1" @if((old('participate') !== null && old('participate') == '1') || !old('participate')){{ 'checked ' }}@endif/><label for="form-participate-yes">Ja</label></div>
                    <div><input type="radio" id="form-participate-no" name="participate" value="0" @if(old('participate') !== null && old('participate') == '0'){{ 'checked ' }}@endif/><label for="form-participate-no">Nee</label></div>
                </div>
            </div>@endif
            @if($show_full_name)<div @error('full_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-full_name">Volledige naam *</label><input type="text" id="form-full_name" name="full_name" size="20" value="{{ old('full_name') }}" placeholder="Je volledige naam"></div>@endif
            @if($show_first_name)<div @error('first_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-first_name">Voornaam *</label><input type="text" id="form-first_name" name="first_name" size="12" value="{{ old('first_name') }}" placeholder="Je voornaam"></div>@endif
            @if($show_last_name)<div @error('last_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-last_name">Achternaam *</label><input type="text" id="form-last_name" name="last_name" size="12" value="{{ old('last_name') }}" placeholder="Je achternaam"></div>@endif
            <div @error('email')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-email">E-mail Adres *</label><input type="text" id="form-email" name="email" size="20" value="{{ old('email') }}" placeholder="Je e-mail adres"></div>
            @if($show_birth_date)<div @error('birth_date')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-birth_date">Geboortedatum *</label><input type="text" id="form-birth_date" name="birth_date" size="10" value="{{ old('birth_date') }}" placeholder="DD-MM-JJJJ"></div>@endif
            @if($show_company)<div @error('company')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-company">Bedrijfsnaam *</label><input type="text" id="form-company" name="company" size="20" value="{{ old('company') }}" placeholder="Bij welk bedrijf werk je?"></div>@endif
            @if($show_partner)<div>
                <label for="form-partner">Inclusief partner?</label>
                <div>
                    <input type="checkbox" id="form-partner" name="partner" value="1"@if(old('partner') && old('partner') == 1){{ ' checked' }}@endif>
                    <span>Ja</span>
                </div>
            </div>@endif
            @if($show_partner_name)<div @error('partner_name')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-partner_name">Naam partner</label><input type="text" id="form-partner_name" name="partner_name" size="20" value="{{ old('partner_name') }}" placeholder="Naam van je partner"></div>@endif
            @if($show_children_amount)<div>
                <label for="form-children_amount">Aantal kinderen</label>
                <select name="children_amount" id="form-children_amount">
                    <option value="0">0</option>
                    <option value="1"@if(old('children_amount') && old('children_amount') == 1){{ ' selected' }}@endif>1</option>
                    <option value="2"@if(old('children_amount') && old('children_amount') == 2){{ ' selected' }}@endif>2</option>
                    <option value="3"@if(old('children_amount') && old('children_amount') == 3){{ ' selected' }}@endif>3</option>
                    <option value="4"@if(old('children_amount') && old('children_amount') == 4){{ ' selected' }}@endif>4</option>
                    <option value="5"@if(old('children_amount') && old('children_amount') == 5){{ ' selected' }}@endif>5</option>
                    <option value="6"@if(old('children_amount') && old('children_amount') == 6){{ ' selected' }}@endif>6</option>
                </select>
            </div>@endif
            @if($show_children_ages)<div @error('children_ages')class="error" data-err-msg="{{ $message }}"@enderror><label for="form-children_ages">Leeftijd kinderen</label><input type="text" id="form-children_ages" name="children_ages" size="20" value="{{ old('children_ages') }}" placeholder="Hoe oud zijn je kinderen?"></div>@endif

            @if($show_diet_wishes)<div class="dieetWensen">
                <label>Dieetwensen</label>
                {{-- <input type="text" id="form-diet_wishes" name="diet_wishes" size="20" value="{{ old('diet_wishes') }}" placeholder="Heb je dieetwensen?"> --}}
                <div class="dietRadioGroup">
                    {{-- <div><input type="radio" id="form-diet_nvt" name="diet_wishes" value="n.v.t." @if((old('diet_wishes') && old('diet_wishes') == 'n.v.t.') || !old('diet_wishes')){{ 'checked ' }}@endif/><label for="form-diet_nvt">Niet van toepassing</label></div> --}}
                    <div><input type="radio" id="form-diet_vlees" name="diet_wishes" value="Vlees" @if((old('diet_wishes') && old('diet_wishes') == 'Vlees') || !old('diet_wishes')){{ 'checked ' }}@endif/><label for="form-diet_vlees">Vlees</label></div>
                    <div><input type="radio" id="form-diet_vis" name="diet_wishes" value="Vis" @if((old('diet_wishes') && old('diet_wishes') == 'Vis') || !old('diet_wishes')){{ 'checked ' }}@endif/><label for="form-diet_vis">Vis</label></div>
                    <div><input type="radio" id="form-diet_vegetarisch" name="diet_wishes" value="Vegetarisch" @if(old('diet_wishes') && old('diet_wishes') == 'Vegetarisch'){{ 'checked ' }}@endif/><label for="form-diet_vegetarisch">Vegetarisch</label></div>
                    <div><input type="radio" id="form-diet_halal" name="diet_wishes" value="Halal" @if(old('diet_wishes') && old('diet_wishes') == 'Halal'){{ 'checked ' }}@endif/><label for="form-diet_halal">Halal</label></div>
                    <div><input type="radio" id="form-diet_anders" name="diet_wishes" value="Anders" @if(old('diet_wishes') && old('diet_wishes') == 'Anders'){{ 'checked ' }}@endif/></label>
                        {{-- <input type="text" name="diet_anders" size="35" value="{{ old('diet_anders') }}" placeholder="Anders"> --}}
                        <div @error('diet_anders')class="error" data-err-msg="{{ $message }}"@enderror><textarea name="diet_anders" cols="30" rows="3" placeholder="Anders">{{ old('diet_anders') }}</textarea></div>
                    </div>
                </div>
            </div>@endif


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
                <button type="submit">Verzenden</button>
            </div>
        </div>
    </form>
    @endif
@endsection