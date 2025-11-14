<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Orariu ESG. NOSSEF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h3 { text-align: center; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #0099ff; color: white; }
    </style>
</head>
<body>
    <h3>Orariu ESG. NOSSEF</h3>
    <table>
        <thead>
            <tr>
                <th>Loron</th>
                <th>Oras</th>
                <th>Materia</th>
                <th>Professor</th>
                <th>Klase</th>
                <th>Turma</th>
                <th>Area Estudu</th>
            </tr>
        </thead>
        <tbody>
            @php
                $dayNames = [
                    'Monday' => 'Segunda', 'Tuesday' => 'Tersa', 'Wednesday' => 'Kuarta',
                    'Thursday' => 'Kinta', 'Friday' => 'Sexta', 'Saturday' => 'Sabadu'
                ];
            @endphp
            @foreach($timetables as $tt)
            <tr>
                <td>{{ $dayNames[$tt->day] ?? $tt->day }}</td>
                <td>{{ $tt->start_time }} - {{ $tt->end_time }}</td>
                <td>{{ optional(optional($tt->subjectAssignment)->subject)->name ?? '-' }}</td>
                <td>{{ optional(optional($tt->subjectAssignment)->teacher)->name ?? '-' }}</td>
                <td>{{ optional($tt->classRoom)->level ?? '-' }}</td>
                <td>{{ optional($tt->classRoom)->turma ?? '-' }}</td>
                <td>{{ optional(optional($tt->classRoom)->major)->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
