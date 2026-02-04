<!DOCTYPE html>
<html>
<head>
    <title>Rapor {{ $student->name }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Rapor Siswa</h2>
    <p><strong>Nama:</strong> {{ $student->name }}</p>
    <p><strong>NRE:</strong> {{ $student->nre }}</p>
    <p><strong>Class:</strong> {{ $student->classRoom->level }} {{ $student->classRoom->turma }}</p>

    <table>
        <thead>
            <tr>
                <th>Disiplina</th>
                <th>Professor</th>
                <th>Score</th>
                <th>Observasaun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $grade)
            <tr>
                <td>{{ $grade->subject->name }}</td>
                <td>{{ $grade->teacher->name ?? '-' }}</td>
                <td>{{ $grade->score }}</td>
                <td>{{ $grade->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
