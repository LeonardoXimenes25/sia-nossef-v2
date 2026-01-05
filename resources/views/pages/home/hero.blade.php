@extends('layouts.app')

@section('title', 'ESG. Nossef | Baranda')

@section('content')
<section id="hero" class="hero section mt-5">
  <div class="hero-content">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <div class="content">
            <h1>Ensino Secundaria Geral Nossef</h1>
            <p>Paz em Cristo</p>
            <div class="cta-group">
              <a href="/admin/login" class="btn-primary">Get Started</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="hero-image">
            <img src="{{asset('assets/img/nossef-picture.webp')}}" alt="#" class="img-fluid shadow">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="container mt-4">
  <h2 class="text-center mb-3">Mapa Lokalizasaun ESG. NOSSEF, Timor-Leste</h2>
  <div id="map" style="height: 500px; border-radius: 10px;"></div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi peta
    const map = L.map('map').setView([-8.8742, 125.7275], 8); // tengah Timor-Leste

    // Base map OSM
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>'
    }).addTo(map);

    L.marker([-8.6720327, 125.4156324]).addTo(map)
  .bindPopup(`
    <b>ESG. NOSSEF</b><br>
    Dili, Timor-Leste<br>
    <a href="https://maps.google.com?q=-8.6720327,125.7275" target="_blank">Haree iha Google Maps</a>
  `);

      // Titik awal dan akhir (contoh rute kendaraan dari Dili ke sekolah)
    const routePoints = [
        [-8.5594, 125.5736],    // Pusat kota Dili
        [-8.6000, 125.5200],    // Melewati tengah jalan
        [-8.6500, 125.4600],    // Mendekati daerah Hera
        [-8.6720327, 125.4156324] // Lokasi ESG. NOSSEF
    ];

    // Gambar rute kendaraan
    const routeLine = L.polyline(routePoints, {
        color: "blue",        // warna garis rute
        weight: 4,            // ketebalan garis
        opacity: 0.8,         // transparansi
        smoothFactor: 1
    }).addTo(map);

    // Zoom otomatis agar rute terlihat utuh
    map.fitBounds(routeLine.getBounds());

    let carIcon = L.icon({
  iconUrl: 'https://cdn-icons-png.flaticon.com/512/744/744465.png',
  iconSize: [32, 32]
});

let carMarker = L.marker(routePoints[0], { icon: carIcon }).addTo(map);

// Animasi sederhana
let i = 0;
function moveCar() {
    if (i < routePoints.length) {
        carMarker.setLatLng(routePoints[i]);
        i++;
        setTimeout(moveCar, 1000);
    }
}
moveCar();

    // Load GeoJSON
    fetch("{{ asset('geo/timorleste.json') }}")
      .then(res => res.json())
      .then(data => {
          data.features.forEach(feature => {
              // Cek tipe geometry
              let polygons = [];
              if (feature.geometry.type === "Polygon") {
                  polygons = [feature.geometry.coordinates];
              } else if (feature.geometry.type === "MultiPolygon") {
                  polygons = feature.geometry.coordinates;
              }

              polygons.forEach(polygon => {
                  L.polygon(polygon, {
                      fillColor: "#90EE90",   // warna area hijau
                      fillOpacity: 0.6,       // transparansi
                      color: "#90EE90",       // samakan stroke dengan fill
                      weight: 0,               // tebal garis 0
                      stroke: false            // hapus garis
                  }).addTo(map);
              });
          });
      })
      .catch(err => console.error("Gagal load GeoJSON:", err));
});
</script>

<!-- Statistik Sekolah -->
@include('pages.home.stats')
@endsection
