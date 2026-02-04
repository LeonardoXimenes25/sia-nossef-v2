<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Grade;
use App\Models\Student;
use Filament\Pages\Page;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;

class ExportRapor extends Page
{
    protected static string $resource = GradeResource::class;
    protected static string $view = 'filament.resources.grade-resource.pages.export-rapor';

    public Student $student;

    public function mount(Student $student)
    {
        $this->student = $student;
    }

    public function render(): View
    {
        $grades = Grade::where('student_id', $this->student->id)
            ->with('subject','teacher','classRoom','academicYear','period')
            ->get();

        return view('filament.resources.grade-resource.pages.export-rapor', [
            'student' => $this->student,
            'grades' => $grades,
        ]);
    }

    public function downloadPdf()
    {
        $grades = Grade::where('student_id', $this->student->id)
            ->with('subject','teacher','classRoom','academicYear','period')
            ->get();

        $pdf = Pdf::loadView('filament.resources.grade-resource.pages.export-rapor-pdf', [
            'student' => $this->student,
            'grades' => $grades,
        ]);

        return response()->streamDownload(fn() => print($pdf->output()), 'Rapor_'.$this->student->name.'.pdf');
    }
}
