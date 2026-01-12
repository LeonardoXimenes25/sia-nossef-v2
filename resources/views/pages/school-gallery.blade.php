{{-- css --}}
<style>
    .swiper {
        width: 600px;
        height: 300px;
}
</style>

<div class="container">
    <h2 class="text-center mb-4">Galeria Eskola NOSSEF</h2>

<div class="swiper">
  <!-- Additional required wrapper -->
  <div class="swiper-wrapper">
    <!-- Slides -->
    <div class="swiper-slide">
        <img src="{{asset('assets/img/nossef/1.jpeg')}}" alt="">
    </div>
    <div class="swiper-slide">
        <img src="{{asset('assets/img/nossef/2.jpeg')}}" alt="">
    </div>
    <div class="swiper-slide">
        <img src="{{asset('assets/img/nossef/3.jpeg')}}" alt="">
    </div>
    ...
  </div>
  <!-- If we need pagination -->
  <div class="swiper-pagination"></div>

  <!-- If we need navigation buttons -->
  <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div>

  <!-- If we need scrollbar -->
  <div class="swiper-scrollbar"></div>
</div>

</div>
<
<script>
    const swiper = new Swiper('.swiper', {
  // Optional parameters
  direction: 'vertical',
  loop: true,

  // If we need pagination
  pagination: {
    el: '.swiper-pagination',
  },

  // Navigation arrows
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },

  // And if we need scrollbar
  scrollbar: {
    el: '.swiper-scrollbar',
  },
});
</script>
