@extends('layouts.app')

@section('title', 'ESG. Nossef | Konaba-Ami')

@section('content')
<section id="about" class="about section mt-5">

    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Konaba-Ami</h2>
    </div>
    <!-- End Section Title -->

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row">
            <div class="col-lg-6" data-aos="fade-right" data-aos-delay="200">
            <div class="content">
            <p>NOSSEF (Nossa Senhora da Consolacao) School in Railaco, Timor-Leste, started small and grew into a thriving secondary school, supported by partners like St Canice's parish for over 20 years, though the exact first building year isn't specified, it's a long-standing project growing into a significant school for the region. 
              To find the specific year the first building started:
              Check the Jesuit Mission Australia website or their partners.
              Search specifically for "NOSSEF School Railaco history" or "St Canice Railaco first building year".</p>
              {{-- vizaun --}}
              <h2>Misaun</h2>

              <div class="description">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item"><b>Cura personalis – </b>haree estudante ida-idak hanesan ema tomak (mente, espiritu, moral, no sosial).</li>
                  <li class="list-group-item"><b>Excelénsia (Magis) –</b> buka sempre buat ne’ebé di’ak liu tan, la’ós de’it satisfasaun iha nivel ki’ik.</li>
                  <li class="list-group-item"><b>Justisa sosial –</b> forma estudante atu sensível ba situasaun ema ki’ik sira no hakarak halo mudansa di’ak iha sosiedade.</li>
                  <li class="list-group-item"><b>Servisu ba ema seluk –</b> edukasaun la’ós de’it ba an-rasik, maibé ba benefísiu komunidade. misaun Forma lider servisu (leaders for others)
                      Visaun Jesuita mak atu forma estudante sira ne’ebé la busca de’it susesu rasik, maibé prontu atu serve komunidade, nasaun, no ema ne’ebé ki’ik liu.</li>
                </ul>
              </div>

              {{-- Misaun --}}
              <h2>Vizaun</h2>

              <div class="description">
                <h5>Justisa sosial no solidariedade</h5>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">Sensível ba sofrimentu ema seluk</li>
                  <li class="list-group-item">Halo asaun ba hadia desigualdade</li>
                  <li class="list-group-item">Defende direitu ema</li>
                  <li class="list-group-item">Kontribui ba dame no harmonía sosial</li>
                </ul>

                <h5>Excelénsia (Magis)</h5>
                <p>Jesuita iha visaun atu buka buat di’ak liu tan iha prosesu aprendizajen, la’ós atu konkurrénsia de’it, maibé atu halo servisu ne’ebé di’ak liu ba benefísiu ema barak.</p>

                <h5>Fé no kultura hakmatek</h5>
                <p>Edukasaun Jesuita buka halo ligasaun entre:</p>
                <ul>
                  <li>Fé no razáun</li>
                  <li>Kultura lokal no valor universal</li>
                  <li>Tradisaun no inovasaun</li>
                  <li>Transformasaun sosiedade</li>
                  <li>Muda estudante sira sai ema ne’ebé responsável</li>
                  <li>Forma sidadán ne’ebé onestu</li>
                  <li>Fó kontribuisaun real ba dezenvolvimentu nasaun visaun</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
            <div class="image-container">
              <img src="{{asset('assets/img/nossef-picture.webp')}}" alt="About Us" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
</section><!-- /About Section -->

{{-- gallery Section --}}
<section>
  @include('pages.school-gallery')
</section>

<section>
    @include('pages.school-structure', ['teachers' => $teachers])
</section>
@endsection