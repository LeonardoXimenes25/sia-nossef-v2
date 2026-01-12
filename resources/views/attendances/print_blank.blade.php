<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absensi Kosong</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Absensi Kosong</h2>
    <p><strong>Classe / Turma:</strong> {{ $classRoom->level }} {{ $classRoom->turma }}</p>
    <p><strong>Disciplina:</strong> {{ $subjectAssignment->subjects->pluck('name')->join(', ') }}</p>
    <p><strong>Data:</strong> {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NRE</th>
                <th>Nama Estudante</th>
                <th>P</th>
                <th>M</th>
                <th>L</th>
                <th>F</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $i => $student)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $student->nre }}</td>
                    <td>{{ $student->name }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
