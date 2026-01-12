<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $fileName }}</title>

    <style>
        * {
            font-family: DejaVu Sans, sans-serif !important;
        }

        body {
            font-size: 13px;
            color: #333;
        }

        /* ===== HEADER ===== */
        .header {
            width: 100%;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo {
            width: 80px;
        }

        .school-info {
            text-align: left;
            padding-left: 10px;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .school-detail {
            font-size: 12px;
        }

        .divider {
            border-bottom: 2px solid #2c5aa0;
            margin: 10px 0;
        }

        .report-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 10px 0 15px;
        }

        /* ===== TABLE ===== */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #dcdcdc;
            padding: 8px;
            font-size: 12px;
        }

        table.data-table th {
            background-color: #f0f4fa;
            font-weight: bold;
            text-align: center;
        }

        table.data-table tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>

    <!-- Ambil data Mestre dan Diretor dari Filament Shield / Spatie Roles -->
    @php
        $mestre = \App\Models\User::role('mestre')->first();
        $diretor = \App\Models\User::role('diretor')->first();
    @endphp

    <!-- ===== HEADER ===== -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="90">
                    <img src="{{ asset('assets/img/nossef-logo.png')  }}" class="logo">
                </td>
                <td class="school-info">
                    <div class="school-name">ESG. NOSSEF</div>
                    <div class="school-detail">
                        Alamat Sekolah, Kota, Ermera<br>
                        Telf / WA: +670 1234 5678
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <!-- ===== JUDUL LAPORAN ===== -->
    <div class="report-title">
        {{ $fileName }}
    </div>

    <!-- ===== TABLE DATA ===== -->
    <table class="data-table">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column->getLabel() }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($columns as $column)
                        <td>{{ $row[$column->getName()] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ===== TANDA TANGAN ===== -->
    <div style="width: 100%; margin-top: 150px;"> <!-- Jarak jauh dari tabel -->
        <table style="width: 100%; border: none;">
            <tr>
                <!-- Kiri untuk Mestre -->
                <td style="width: 50%; text-align: left; vertical-align: top;">
                    <div class="font-bold">Mestre</div>
                    <div style="margin-top: 80px; border-top: 1px solid #000; width: 200px;">
                        {{ $mestre->name ?? '________________' }}
                    </div>
                </td>

                <!-- Kanan untuk Diretor -->
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <div class="font-bold">Diretor</div>
                    <div style="margin-top: 80px; border-top: 1px solid #000; width: 200px; display: inline-block;">
                        {{ $diretor->name ?? '________________' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
