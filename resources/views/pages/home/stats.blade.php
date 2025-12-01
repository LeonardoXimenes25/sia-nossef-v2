<section id="stats" class="section mt-5 mb-5">
  <div class="container text-center">
    <h2 class="mb-4" data-aos="fade-up">Estatísika Escola</h2>
    <p class="mb-5 text-muted" data-aos="fade-up" data-aos-delay="100">
      Dados gerais do sistema de informação da ESG Nossef.
    </p>

    {{-- 4 Statistik Card --}}
    <div class="row justify-content-center">
      <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="150">
        <div class="p-4 bg-light rounded-4 shadow-sm">
          <i class="bi bi-people fs-1 text-primary mb-2"></i>
          <h3 class="fw-bold mb-1">{{ $totalTeachers }}</h3>
          <p class="text-muted mb-0"><a href="{{route('teachers.index')}}">Total Professores</a></p>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="p-4 bg-light rounded-4 shadow-sm">
          <i class="bi bi-person-video3 fs-1 text-success mb-2"></i>
          <h3 class="fw-bold mb-1">{{ $totalStudents }}</h3>
          <p class="text-muted mb-0"><a href="{{route('students.index')}}">Total Alunos</a></p>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="250">
        <div class="p-4 bg-light rounded-4 shadow-sm">
          <i class="bi bi-book fs-1 text-warning mb-2"></i>
          <h3 class="fw-bold mb-1">{{ $totalSubjects }}</h3>
          <p class="text-muted mb-0">Total Materia</p>
        </div>
      </div>
      <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="300">
        <div class="p-4 bg-light rounded-4 shadow-sm">
          <i class="bi bi-building fs-1 text-danger mb-2"></i>
          <h3 class="fw-bold mb-1">{{ $totalClasses }}</h3>
          <p class="text-muted mb-0">Total Klasse</p>
        </div>
      </div>
    </div>

    {{-- ApexCharts Grafik --}}
    <div class="mt-5" data-aos="fade-up" data-aos-delay="350">
      <h4 class="mb-4">📊 Distribuisaun Alunos Por Turma</h4>
      <div id="chart"></div>
    </div>
  </div>
</section>



<script>
document.addEventListener("DOMContentLoaded", function () {
  const classLabels = @json($classNames);       // ["X-A", "X-B", ...]
  const studentsData = @json($studentsPerClass); // [45, 38, ...]

  var options = {
    chart: {
      type: 'bar',
      height: 350,
      toolbar: { show: true }
    },
    series: [{
      name: 'Total Alunos',
      data: studentsData
    }],
    xaxis: {
      categories: classLabels,
      title: { text: 'Turma (Kelas)' }
    },
    yaxis: {
      title: { text: 'Número de Alunos' }
    },
    colors: ['#00BFFF'],
    dataLabels: { enabled: true },
    plotOptions: {
      bar: {
        borderRadius: 6,
        columnWidth: '50%',
      }
    },
    title: {
      text: 'Distribuisaun Alunos Por Turma',
      align: 'center',
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return val + " alunos";
        }
      }
    },
    grid: {
      show: false,
    }
  };

  var chart = new ApexCharts(document.querySelector("#chart"), options);
  chart.render();
});
</script>


<style>
#stats {
  background: #f8f9fa;
  padding-top: 50px;
  padding-bottom: 50px;
}
#chart {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
