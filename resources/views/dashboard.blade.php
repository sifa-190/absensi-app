<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Kipin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                
                <h2 class="mb-4">Dashboard Monitoring Absensi</h2>

                {{-- NOTIFIKASI --}}
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                {{-- FORM UPLOAD --}}
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        Upload Data Revo (CSV)
                    </div>
                    <div class="card-body">
                        <form action="{{ route('import.presensi') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" class="form-control" name="file_csv" required>
                                <button class="btn btn-success" type="submit">
                                    Upload & Proses
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABEL DATA --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        Data Presensi Mentah
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>PIN</th>
                                    <th>Nama Karyawan</th>
                                    <th>Waktu Absensi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $d)
                                <tr>
                                    <td>{{ $d->karyawan->id_mesin }}</td>
                                    <td>{{ $d->karyawan->nama }}</td>
                                    <td>{{ $d->waktu_absensi }}</td>
                                    <td>
                                        @if(str_contains(strtolower($d->status_mesin), 'masuk'))
                                            <span class="badge bg-success">Absensi Masuk</span>
                                        @else
                                            <span class="badge bg-danger">Absensi Pulang</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Belum ada data
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>