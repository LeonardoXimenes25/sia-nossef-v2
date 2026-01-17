<?php

namespace Database\Seeders;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ct  = Major::where('code', 'CT')->first();
        $csh = Major::where('code', 'CSH')->first();

        // Bahasa Inggris (CT & CSH)
        $english = Subject::create([
            'name' => 'Lingua Inglesh',
        ]);
        $english->majors()->attach([$ct->id, $csh->id]);

        $portuguese = Subject::create([
            'name' => 'Lingua Portuguesa',
        ]);
        $portuguese->majors()->attach([$ct->id, $csh->id]);

        $dalentetun = Subject::create([
            'name' => 'Dalen Tetun',
        ]);
        $dalentetun->majors()->attach([$ct->id, $csh->id]);

        $dalenindonesia = Subject::create([
            'name' => 'Lingua Indonesia',
        ]);
        $dalenindonesia->majors()->attach([$ct->id, $csh->id]);

        $cidadania = Subject::create([
            'name' => 'Cidadania Desenvolvimentu Social',
        ]);
        $cidadania->majors()->attach([$ct->id, $csh->id]);

        $multimedia = Subject::create([
            'name' => 'Tecnologia Multimedia',
        ]);
        $multimedia->majors()->attach([$ct->id, $csh->id]);

        $desportu = Subject::create([
            'name' => 'Edukasaun Fisica e Desportu',
        ]);
        $desportu->majors()->attach([$ct->id, $csh->id]);

        $religiao = Subject::create([
            'name' => 'Religiao no Moral',
        ]);
        $religiao->majors()->attach([$ct->id, $csh->id]);

        // Materia CT
        $math = Subject::create([
            'name' => 'Matematika',
        ]);
        $math->majors()->attach($ct->id);

        $chemistry = Subject::create([
            'name' => 'Quimica',
        ]);
        $chemistry->majors()->attach($ct->id);

        $physics = Subject::create([
            'name' => 'Fisika',
        ]);
        $physics->majors()->attach($ct->id);

        $biology = Subject::create([
            'name' => 'Biologia',
        ]);
        $biology->majors()->attach($ct->id);

        $geology = Subject::create([
            'name' => 'Geologia',
        ]);
        $geology->majors()->attach($ct->id);

        // materia CSH
        $history = Subject::create([
            'name' => 'Historia',
        ]);
        $history->majors()->attach($csh->id);

        $geography = Subject::create([
            'name' => 'Geografia',
        ]);
        $geography->majors()->attach($csh->id);

        $sociology = Subject::create([
            'name' => 'Sosiologia',
        ]);
        $sociology->majors()->attach($csh->id);

        $economics = Subject::create([
            'name' => 'Ekonomia e Metodos Quantitativas',
        ]);
        $economics->majors()->attach($csh->id); 

        $temas = Subject::create([
            'name' => 'Temas Literaturas e Cultura',    
        ]);
        $temas->majors()->attach($csh->id); 
    }
}
