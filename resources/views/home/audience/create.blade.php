@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')

<div class="wrapper">
    <div class="content-wrapper" style="min-height: 80vh; margin-left: 0;">
        <section class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Tambah Audience Baru</h1>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Formulir Data Audience</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('audience.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="conference_id" class="form-label">Konferensi <span class="text-danger">*</span></label>
                                <select class="form-select @error('conference_id') is-invalid @enderror" id="conference_id" name="conference_id" required>
                                    <option value="">-- Pilih Konferensi --</option>
                                    @foreach($conferences as $conf)
                                    <option
                                        value="{{ $conf->id }}"
                                        data-online-fee="{{ $conf->online_fee }}"
                                        data-onsite-fee="{{ $conf->onsite_fee }}"
                                        data-participant-fee="{{ $conf->participant_fee }}"
                                        {{ old('conference_id') == $conf->id ? 'selected' : '' }}>
                                        {{ $conf->name }} ({{ $conf->year }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('conference_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">Nama Depan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Nama Belakang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Nomor Telepon/WhatsApp</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                                @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label fw-bold">Country <span class="text-danger">*</span></label>
                                <select class="form-select rounded-0 mt-2" id="country" name="country">
                                    <option value="" selected="">Choose Country</option>
                                    <option value="1">
                                        Afghanistan
                                        (93)
                                    </option>
                                    <option value="2">
                                        Albania
                                        (355)
                                    </option>
                                    <option value="3">
                                        Algeria
                                        (213)
                                    </option>
                                    <option value="4">
                                        American Samoa
                                        (+1-684*)
                                    </option>
                                    <option value="5">
                                        Andorra
                                        (376)
                                    </option>
                                    <option value="6">
                                        Angola
                                        (244)
                                    </option>
                                    <option value="7">
                                        Anguilla
                                        (+1-264*)
                                    </option>
                                    <option value="8">
                                        Antigua
                                        (+1-268*)
                                    </option>
                                    <option value="9">
                                        Argentina
                                        (54)
                                    </option>
                                    <option value="10">
                                        Armenia
                                        (374)
                                    </option>
                                    <option value="11">
                                        Aruba
                                        (297)
                                    </option>
                                    <option value="12">
                                        Ascension
                                        (247)
                                    </option>
                                    <option value="13">
                                        Australia
                                        (61)
                                    </option>
                                    <option value="14">
                                        Australian External Territories
                                        (672)
                                    </option>
                                    <option value="15">
                                        Austria
                                        (43)
                                    </option>
                                    <option value="16">
                                        Azerbaijan
                                        (994)
                                    </option>
                                    <option value="17">
                                        Bahamas
                                        (+1-242*)
                                    </option>
                                    <option value="18">
                                        Bahrain
                                        (973)
                                    </option>
                                    <option value="19">
                                        Bangladesh
                                        (880)
                                    </option>
                                    <option value="20">
                                        Barbados
                                        (+1-246*)
                                    </option>
                                    <option value="21">
                                        Barbuda
                                        (+1-268*)
                                    </option>
                                    <option value="22">
                                        Belarus
                                        (375)
                                    </option>
                                    <option value="23">
                                        Belgium
                                        (32)
                                    </option>
                                    <option value="24">
                                        Belize
                                        (501)
                                    </option>
                                    <option value="25">
                                        Benin
                                        (229)
                                    </option>
                                    <option value="26">
                                        Bermuda
                                        (+1-441*)
                                    </option>
                                    <option value="27">
                                        Bhutan
                                        (975)
                                    </option>
                                    <option value="28">
                                        Bolivia
                                        (591)
                                    </option>
                                    <option value="29">
                                        Bosnia &amp; Herzegovina
                                        (387)
                                    </option>
                                    <option value="30">
                                        Botswana
                                        (267)
                                    </option>
                                    <option value="31">
                                        Brazil
                                        (55)
                                    </option>
                                    <option value="32">
                                        British Virgin Islands
                                        (+1-284*)
                                    </option>
                                    <option value="33">
                                        Brunei Darussalam
                                        (673)
                                    </option>
                                    <option value="34">
                                        Bulgaria
                                        (359)
                                    </option>
                                    <option value="35">
                                        Burkina Faso
                                        (226)
                                    </option>
                                    <option value="36">
                                        Burundi
                                        (257)
                                    </option>
                                    <option value="37">
                                        Cambodia
                                        (855)
                                    </option>
                                    <option value="38">
                                        Cameroon
                                        (237)
                                    </option>
                                    <option value="39">
                                        Canada
                                        (1)
                                    </option>
                                    <option value="40">
                                        Cape Verde Islands
                                        (238)
                                    </option>
                                    <option value="41">
                                        Cayman Islands
                                        (+1-345*)
                                    </option>
                                    <option value="42">
                                        Central African Republic
                                        (236)
                                    </option>
                                    <option value="43">
                                        Chad
                                        (235)
                                    </option>
                                    <option value="44">
                                        Chatham Island (New Zealand)
                                        (64)
                                    </option>
                                    <option value="45">
                                        Chile
                                        (56)
                                    </option>
                                    <option value="46">
                                        China (PRC)
                                        (86)
                                    </option>
                                    <option value="47">
                                        Christmas Island
                                        (61)
                                    </option>
                                    <option value="48">
                                        Cocos-Keeling Islands
                                        (61)
                                    </option>
                                    <option value="49">
                                        Colombia
                                        (57)
                                    </option>
                                    <option value="50">
                                        Comoros
                                        (269)
                                    </option>
                                    <option value="51">
                                        Congo
                                        (242)
                                    </option>
                                    <option value="52">
                                        Congo, Dem. Rep. of (Zaire)
                                        (243)
                                    </option>
                                    <option value="53">
                                        Cook Islands
                                        (682)
                                    </option>
                                    <option value="54">
                                        Costa Rica
                                        (506)
                                    </option>
                                    <option value="55">
                                        Côte d’Ivoire (Ivory Coast)
                                        (225)
                                    </option>
                                    <option value="56">
                                        Croatia
                                        (385)
                                    </option>
                                    <option value="57">
                                        Cuba
                                        (53)
                                    </option>
                                    <option value="58">
                                        Cuba (Guantanamo Bay)
                                        (5399)
                                    </option>
                                    <option value="59">
                                        Curaçao
                                        (599)
                                    </option>
                                    <option value="60">
                                        Cyprus
                                        (357)
                                    </option>
                                    <option value="61">
                                        Czech Republic
                                        (420)
                                    </option>
                                    <option value="62">
                                        Denmark
                                        (45)
                                    </option>
                                    <option value="63">
                                        Diego Garcia
                                        (246)
                                    </option>
                                    <option value="64">
                                        Djibouti
                                        (253)
                                    </option>
                                    <option value="65">
                                        Dominica
                                        (+1-767*)
                                    </option>
                                    <option value="66">
                                        Dominican Republic
                                        (+1-809)
                                    </option>
                                    <option value="67">
                                        East Timor
                                        (670)
                                    </option>
                                    <option value="68">
                                        Easter Island
                                        (56)
                                    </option>
                                    <option value="69">
                                        Ecuador
                                        (593)
                                    </option>
                                    <option value="70">
                                        Egypt
                                        (20)
                                    </option>
                                    <option value="71">
                                        El Salvador
                                        (503)
                                    </option>
                                    <option value="72">
                                        Ellipso (Mobile Satellite service)
                                        (17625)
                                    </option>
                                    <option value="73">
                                        EMSAT (Mobile Satellite service)
                                        (88213)
                                    </option>
                                    <option value="74">
                                        Equatorial Guinea
                                        (240)
                                    </option>
                                    <option value="75">
                                        Eritrea
                                        (291)
                                    </option>
                                    <option value="76">
                                        Estonia
                                        (372)
                                    </option>
                                    <option value="77">
                                        Ethiopia
                                        (251)
                                    </option>
                                    <option value="78">
                                        Falkland Islands (Malvinas)
                                        (500)
                                    </option>
                                    <option value="79">
                                        Faroe Islands
                                        (298)
                                    </option>
                                    <option value="80">
                                        Fiji Islands
                                        (679)
                                    </option>
                                    <option value="81">
                                        Finland
                                        (358)
                                    </option>
                                    <option value="82">
                                        France
                                        (33)
                                    </option>
                                    <option value="83">
                                        French Antilles
                                        (596)
                                    </option>
                                    <option value="84">
                                        French Guiana
                                        (594)
                                    </option>
                                    <option value="85">
                                        French Polynesia
                                        (689)
                                    </option>
                                    <option value="86">
                                        Gabonese Republic
                                        (241)
                                    </option>
                                    <option value="87">
                                        Gambia
                                        (220)
                                    </option>
                                    <option value="88">
                                        Georgia
                                        (995)
                                    </option>
                                    <option value="89">
                                        Germany
                                        (995)
                                    </option>
                                    <option value="90">
                                        Ghana
                                        (49)
                                    </option>
                                    <option value="91">
                                        Gibraltar
                                        (350)
                                    </option>
                                    <option value="92">
                                        Global Mobile Satellite System (GMSS)
                                        (881)
                                    </option>
                                    <option value="93">
                                        Globalstar
                                        (8818)
                                    </option>
                                    <option value="94">
                                        Globalstar (Mobile Satellite Service)
                                        (17637)
                                    </option>
                                    <option value="95">
                                        Greece
                                        (30)
                                    </option>
                                    <option value="96">
                                        Greenland
                                        (299)
                                    </option>
                                    <option value="97">
                                        Grenada
                                        (+1-473*)
                                    </option>
                                    <option value="98">
                                        Guadeloupe
                                        (590)
                                    </option>
                                    <option value="99">
                                        Guam
                                        (+1-671*)
                                    </option>
                                    <option value="100">
                                        Guantanamo Bay
                                        (5399)
                                    </option>
                                    <option value="101">
                                        Guatemala
                                        (502)
                                    </option>
                                    <option value="102">
                                        Guinea-Bissau
                                        (245)
                                    </option>
                                    <option value="103">
                                        Guinea
                                        (224)
                                    </option>
                                    <option value="104">
                                        Guyana
                                        (224)
                                    </option>
                                    <option value="105">
                                        Haiti
                                        (509)
                                    </option>
                                    <option value="106">
                                        Honduras
                                        (504)
                                    </option>
                                    <option value="107">
                                        Hong Kong
                                        (852)
                                    </option>
                                    <option value="108">
                                        Hungary
                                        (36)
                                    </option>
                                    <option value="109">
                                        ICO Global (Mobile Satellite Service)
                                        (17621)
                                    </option>
                                    <option value="110">
                                        Iceland
                                        (354)
                                    </option>
                                    <option value="111">
                                        India
                                        (91)
                                    </option>
                                    <option value="112">
                                        Indonesia
                                        (62)
                                    </option>
                                    <option value="113">
                                        Inmarsat (Atlantic Ocean – East)
                                        (871)
                                    </option>
                                    <option value="114">
                                        Inmarsat (Atlantic Ocean – West)
                                        (874)
                                    </option>
                                    <option value="115">
                                        Inmarsat (Indian Ocean)
                                        (873)
                                    </option>
                                    <option value="116">
                                        Inmarsat (Pacific Ocean)
                                        (872)
                                    </option>
                                    <option value="117">
                                        International Freephone Service
                                        (800)
                                    </option>
                                    <option value="118">
                                        International Shared Cost Service (ISCS)
                                        (808)
                                    </option>
                                    <option value="119">
                                        Iran
                                        (98)
                                    </option>
                                    <option value="120">
                                        Iraq
                                        (964)
                                    </option>
                                    <option value="121">
                                        Ireland
                                        (353)
                                    </option>
                                    <option value="122">
                                        Iridium (Mobile Satellite service)
                                        (8816)
                                    </option>
                                    <option value="123">
                                        Iridium (Mobile Satellite service)
                                        (8817)
                                    </option>
                                    <option value="124">
                                        Israel
                                        (972)
                                    </option>
                                    <option value="125">
                                        Italy
                                        (39)
                                    </option>
                                    <option value="126">
                                        Jamaica
                                        (+1-876*)
                                    </option>
                                    <option value="127">
                                        Japan
                                        (81)
                                    </option>
                                    <option value="128">
                                        Jordan
                                        (962)
                                    </option>
                                    <option value="129">
                                        Kazakhstan
                                        (7)
                                    </option>
                                    <option value="130">
                                        Kenya
                                        (254)
                                    </option>
                                    <option value="131">
                                        Kiribati
                                        (686)
                                    </option>
                                    <option value="132">
                                        Korea (North)
                                        (850)
                                    </option>
                                    <option value="133">
                                        Korea (South)
                                        (82)
                                    </option>
                                    <option value="134">
                                        Kuwait
                                        (965)
                                    </option>
                                    <option value="135">
                                        Kyrgyz Republic
                                        (996)
                                    </option>
                                    <option value="136">
                                        Laos
                                        (856)
                                    </option>
                                    <option value="137">
                                        Latvia
                                        (371)
                                    </option>
                                    <option value="138">
                                        Lebanon
                                        (961)
                                    </option>
                                    <option value="139">
                                        Lesotho
                                        (266)
                                    </option>
                                    <option value="140">
                                        Liberia
                                        (231)
                                    </option>
                                    <option value="141">
                                        Libya
                                        (218)
                                    </option>
                                    <option value="142">
                                        Liechtenstein
                                        (423)
                                    </option>
                                    <option value="143">
                                        Lithuania
                                        (370)
                                    </option>
                                    <option value="144">
                                        Luxembourg
                                        (352)
                                    </option>
                                    <option value="145">
                                        Macao
                                        (853)
                                    </option>
                                    <option value="146">
                                        Macedonia (Former Yugoslav Rep of.)
                                        (389)
                                    </option>
                                    <option value="147">
                                        Madagascar
                                        (261)
                                    </option>
                                    <option value="148">
                                        Malawi
                                        (265)
                                    </option>
                                    <option value="149">
                                        Malaysia
                                        (60)
                                    </option>
                                    <option value="150">
                                        Maldives
                                        (960)
                                    </option>
                                    <option value="151">
                                        Mali Republic
                                        (223)
                                    </option>
                                    <option value="152">
                                        Malta
                                        (356)
                                    </option>
                                    <option value="153">
                                        Marshall Islands
                                        (692)
                                    </option>
                                    <option value="154">
                                        Martinique
                                        (596)
                                    </option>
                                    <option value="155">
                                        Mauritania
                                        (222)
                                    </option>
                                    <option value="156">
                                        Mauritius
                                        (230)
                                    </option>
                                    <option value="157">
                                        Mayotte Island
                                        (262)
                                    </option>
                                    <option value="158">
                                        Mexico
                                        (52)
                                    </option>
                                    <option value="159">
                                        Micronesia, (Federal States of)
                                        (691)
                                    </option>
                                    <option value="160">
                                        Midway Island
                                        (+1-808*)
                                    </option>
                                    <option value="161">
                                        Moldova
                                        (373)
                                    </option>
                                    <option value="162">
                                        Monaco
                                        (377)
                                    </option>
                                    <option value="163">
                                        Mongolia
                                        (976)
                                    </option>
                                    <option value="164">
                                        Montenegro
                                        (382)
                                    </option>
                                    <option value="165">
                                        Montserrat
                                        (+1-664*)
                                    </option>
                                    <option value="166">
                                        Morocco
                                        (212)
                                    </option>
                                    <option value="167">
                                        Mozambique
                                        (258)
                                    </option>
                                    <option value="168">
                                        Myanmar
                                        (95)
                                    </option>
                                    <option value="169">
                                        Namibia
                                        (264)
                                    </option>
                                    <option value="170">
                                        Nauru
                                        (674)
                                    </option>
                                    <option value="171">
                                        Nepal
                                        (977)
                                    </option>
                                    <option value="172">
                                        Netherlands
                                        (31)
                                    </option>
                                    <option value="173">
                                        Netherlands Antilles
                                        (599)
                                    </option>
                                    <option value="174">
                                        Nevis
                                        (+1-869*)
                                    </option>
                                    <option value="175">
                                        New Caledonia
                                        (687)
                                    </option>
                                    <option value="176">
                                        New Zealand
                                        (64)
                                    </option>
                                    <option value="177">
                                        Nicaragua
                                        (505)
                                    </option>
                                    <option value="178">
                                        Niger
                                        (227)
                                    </option>
                                    <option value="179">
                                        Nigeria
                                        (234)
                                    </option>
                                    <option value="180">
                                        Niue
                                        (683)
                                    </option>
                                    <option value="181">
                                        Norfolk Island
                                        (672)
                                    </option>
                                    <option value="182">
                                        Northern Marianas Islands
                                        (+1-670*)
                                    </option>
                                    <option value="183">
                                        Norway
                                        (47)
                                    </option>
                                    <option value="184">
                                        Oman
                                        (968)
                                    </option>
                                    <option value="185">
                                        Pakistan
                                        (92)
                                    </option>
                                    <option value="186">
                                        Palau
                                        (680)
                                    </option>
                                    <option value="187">
                                        Palestine
                                        (970)
                                    </option>
                                    <option value="188">
                                        Panama
                                        (507)
                                    </option>
                                    <option value="189">
                                        Papua New Guinea
                                        (675)
                                    </option>
                                    <option value="190">
                                        Paraguay
                                        (595)
                                    </option>
                                    <option value="191">
                                        Peru
                                        (51)
                                    </option>
                                    <option value="192">
                                        Philippines
                                        (63)
                                    </option>
                                    <option value="193">
                                        Poland
                                        (48)
                                    </option>
                                    <option value="194">
                                        Portugal
                                        (351)
                                    </option>
                                    <option value="195">
                                        Réunion Island
                                        (262)
                                    </option>
                                    <option value="196">
                                        Qatar
                                        (974)
                                    </option>
                                    <option value="197">
                                        Puerto Rico
                                        (+1-787* or +1-939*)
                                    </option>
                                    <option value="198">
                                        Romania
                                        (40)
                                    </option>
                                    <option value="199">
                                        Russia
                                        (7)
                                    </option>
                                    <option value="200">
                                        Rwandese Republic
                                        (250)
                                    </option>
                                    <option value="201">
                                        St. Helena
                                        (290)
                                    </option>
                                    <option value="202">
                                        St. Kitts/Nevis
                                        (+1-869*)
                                    </option>
                                    <option value="203">
                                        St. Lucia
                                        (+1-758*)
                                    </option>
                                    <option value="204">
                                        St. Pierre &amp; Miquelon
                                        (508)
                                    </option>
                                    <option value="205">
                                        St. Vincent &amp; Grenadines
                                        (+1-784*)
                                    </option>
                                    <option value="206">
                                        Samoa
                                        (685)
                                    </option>
                                    <option value="207">
                                        San Marino
                                        (378)
                                    </option>
                                    <option value="208">
                                        São Tomé and Principe
                                        (239)
                                    </option>
                                    <option value="209">
                                        Saudi Arabia
                                        (966)
                                    </option>
                                    <option value="210">
                                        Senegal
                                        (221)
                                    </option>
                                    <option value="211">
                                        Serbia
                                        (381)
                                    </option>
                                    <option value="212">
                                        Seychelles Republic
                                        (248)
                                    </option>
                                    <option value="213">
                                        Sierra Leone
                                        (232)
                                    </option>
                                    <option value="214">
                                        Singapore
                                        (65)
                                    </option>
                                    <option value="215">
                                        Slovak Republic
                                        (421)
                                    </option>
                                    <option value="216">
                                        Slovenia
                                        (386)
                                    </option>
                                    <option value="217">
                                        Solomon Islands
                                        (677)
                                    </option>
                                    <option value="218">
                                        Somali Democratic Republic
                                        (252)
                                    </option>
                                    <option value="219">
                                        South Africa
                                        (27)
                                    </option>
                                    <option value="220">
                                        Spain
                                        (34)
                                    </option>
                                    <option value="221">
                                        Sri Lanka
                                        (94)
                                    </option>
                                    <option value="222">
                                        Sudan
                                        (249)
                                    </option>
                                    <option value="223">
                                        Suriname
                                        (597)
                                    </option>
                                    <option value="224">
                                        Swaziland
                                        (268)
                                    </option>
                                    <option value="225">
                                        Sweden
                                        (46)
                                    </option>
                                    <option value="226">
                                        Switzerland
                                        (41)
                                    </option>
                                    <option value="227">
                                        Syria
                                        (963)
                                    </option>
                                    <option value="228">
                                        Taiwan
                                        (886)
                                    </option>
                                    <option value="229">
                                        Tajikistan
                                        (992)
                                    </option>
                                    <option value="230">
                                        Tanzania
                                        (255)
                                    </option>
                                    <option value="231">
                                        Thailand
                                        (66)
                                    </option>
                                    <option value="232">
                                        Thuraya (Mobile Satellite service)
                                        (88216)
                                    </option>
                                    <option value="233">
                                        Timor Leste
                                        (670)
                                    </option>
                                    <option value="234">
                                        Togolese Republic
                                        (228)
                                    </option>
                                    <option value="235">
                                        Tokelau
                                        (690)
                                    </option>
                                    <option value="236">
                                        Tonga Islands
                                        (676)
                                    </option>
                                    <option value="237">
                                        Trinidad &amp; Tobago
                                        (+1-868*)
                                    </option>
                                    <option value="238">
                                        Tunisia
                                        (216)
                                    </option>
                                    <option value="239">
                                        Turkey
                                        (90)
                                    </option>
                                    <option value="240">
                                        Turkmenistan
                                        (993)
                                    </option>
                                    <option value="241">
                                        Turks and Caicos Island
                                        (+1-649*)
                                    </option>
                                    <option value="242">
                                        Tuvalu
                                        (688)
                                    </option>
                                    <option value="243">
                                        Uganda
                                        (256)
                                    </option>
                                    <option value="244">
                                        Ukraine
                                        (380)
                                    </option>
                                    <option value="245">
                                        United Arab Emirates
                                        (971)
                                    </option>
                                    <option value="246">
                                        United Kingdom
                                        (44)
                                    </option>
                                    <option value="247">
                                        United States of America
                                        (1)
                                    </option>
                                    <option value="248">
                                        US Virgin Islands
                                        (+1-340*)
                                    </option>
                                    <option value="249">
                                        Universal Personal Telecommunications (UPT)
                                        (878)
                                    </option>
                                    <option value="250">
                                        Uruguay
                                        (598)
                                    </option>
                                    <option value="251">
                                        Uzbekistan
                                        (998)
                                    </option>
                                    <option value="252">
                                        Vanuatu
                                        (678)
                                    </option>
                                    <option value="253">
                                        Vatican City
                                        (39; 379)
                                    </option>
                                    <option value="254">
                                        Venezuela
                                        (58)
                                    </option>
                                    <option value="255">
                                        Vietnam
                                        (84)
                                    </option>
                                    <option value="256">
                                        Wake Island
                                        (808)
                                    </option>
                                    <option value="257">
                                        Wallis and Futuna Islands
                                        (681)
                                    </option>
                                    <option value="258">
                                        Yemen
                                        (967)
                                    </option>
                                    <option value="259">
                                        Zambia
                                        (260)
                                    </option>
                                    <option value="260">
                                        Zanzibar
                                        (255)
                                    </option>
                                    <option value="261">
                                        Zimbabwe
                                        (263)
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="paper_title" class="form-label">Judul Paper</label>
                                <input type="text" class="form-control @error('paper_title') is-invalid @enderror" id="paper_title" name="paper_title" value="{{ old('paper_title') }}">
                                @error('paper_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="institution" class="form-label">Institusi/Afiliasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('institution') is-invalid @enderror" id="institution" name="institution" value="{{ old('institution') }}" required>
                                @error('institution')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Presentasi <span class="text-danger">*</span></label>
                                <div id="presentation-types-wrapper">
                                    <div class="form-check">
                                        <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type" id="online_author" value="online_author" {{ old('presentation_type') == 'online_author' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="online_author">
                                            Online (author/presenter)
                                            <span class="text-muted small-info" id="online-fee-display"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type" id="onsite" value="onsite" {{ old('presentation_type') == 'onsite' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="onsite">
                                            Onsite
                                            <span class="text-muted small-info" id="onsite-fee-display"></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input presentation-type-radio" type="radio" name="presentation_type" id="participant_only" value="participant_only" {{ old('presentation_type') == 'participant_only' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="participant_only">
                                            Participant Only
                                            <span class="text-muted small-info" id="participant-fee-display"></span>
                                        </label>
                                    </div>
                                </div>
                                @error('presentation_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="paid_fee" class="form-label">Biaya Dibayar</label>
                                <input type="number" step="0.01" class="form-control @error('paid_fee') is-invalid @enderror" id="paid_fee" name="paid_fee" value="{{ old('paid_fee', 0) }}" readonly>
                                <div class="form-text">Biaya akan otomatis terisi berdasarkan pilihan di atas.</div>
                                @error('paid_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="payment_status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                                    <option value="pending_payment" {{ old('payment_status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                    <option value="cancelled" {{ old('payment_status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    <option value="refunded" {{ old('payment_status') == 'refunded' ? 'selected' : '' }}>Dikembalikan</option>
                                </select>
                                @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="full_paper" class="form-label">Upload Full Paper (Doc/Docx)</label>
                                <input type="file" class="form-control @error('full_paper') is-invalid @enderror" id="full_paper" name="full_paper" accept=".doc,.docx">
                                <div class="form-text">Maksimal 5MB.</div>
                                @error('full_paper')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save mr-1"></i> Simpan Audience</button>
                            <a href="{{ route('audience.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@stop

@section('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    console.log("Hi, I'm using the Laravel-AdminLTE package!");
</script>
<script>
    $(document).ready(function() {
        // Fungsi untuk mengupdate biaya dibayar
        function updatePaidFee() {
            var selectedConferenceOption = $('#conference_id option:selected');
            var selectedPresentationType = $('input[name="presentation_type"]:checked').val();
            var paidFeeField = $('#paid_fee');
            var fee = 0;

            if (selectedConferenceOption.val() && selectedPresentationType) {
                if (selectedPresentationType === 'online_author') {
                    fee = parseFloat(selectedConferenceOption.data('online-fee'));
                } else if (selectedPresentationType === 'onsite') {
                    fee = parseFloat(selectedConferenceOption.data('onsite-fee'));
                } else if (selectedPresentationType === 'participant_only') {
                    fee = parseFloat(selectedConferenceOption.data('participant-fee'));
                }
            }

            paidFeeField.val(fee.toFixed(2)); // Mengisi field dengan 2 angka di belakang koma
        }

        // Fungsi untuk mengupdate tampilan biaya di samping radio button
        function updateFeeDisplays() {
            var selectedConferenceOption = $('#conference_id option:selected');

            // Set default jika tidak ada konferensi terpilih
            var onlineFee = selectedConferenceOption.data('online-fee') !== undefined ? selectedConferenceOption.data('online-fee') : 0;
            var onsiteFee = selectedConferenceOption.data('onsite-fee') !== undefined ? selectedConferenceOption.data('onsite-fee') : 0;
            var participantFee = selectedConferenceOption.data('participant-fee') !== undefined ? selectedConferenceOption.data('participant-fee') : 0;

            $('#online-fee-display').text(' (Rp ' + parseFloat(onlineFee).toLocaleString('id-ID') + ')');
            $('#onsite-fee-display').text(' (Rp ' + parseFloat(onsiteFee).toLocaleString('id-ID') + ')');
            $('#participant-fee-display').text(' (Rp ' + parseFloat(participantFee).toLocaleString('id-ID') + ')');

            // Jika belum ada konferensi terpilih, kosongkan atau sembunyikan info biaya
            if (!selectedConferenceOption.val()) {
                $('.small-info').text('');
            }
        }


        // Panggil fungsi ketika:
        // 1. Dropdown Konferensi berubah
        $('#conference_id').change(function() {
            updateFeeDisplays(); // Update tampilan biaya di radio button
            updatePaidFee(); // Update field biaya dibayar
        });

        // 2. Radio button Tipe Presentasi berubah
        $('.presentation-type-radio').change(function() {
            updatePaidFee();
        });

        // Panggil saat halaman dimuat (untuk mengisi nilai awal jika ada old() value)
        updateFeeDisplays(); // Panggil dulu untuk menampilkan biaya di samping radio button
        updatePaidFee(); // Lalu panggil untuk mengisi field biaya dibayar

        // Jika ada old('paid_fee') yang disimpan dari validasi gagal, pertahankan nilainya
        // Ini untuk memastikan nilai yang dikirim dari server tetap ada jika validasi gagal
        var oldPaidFee = "{{ old('paid_fee') }}";
        if (oldPaidFee) {
            $('#paid_fee').val(oldPaidFee);
        }
    });
</script>

@stop