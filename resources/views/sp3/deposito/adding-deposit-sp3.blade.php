@extends('layouts.master')
@section('content')
    {{-- {!! Toastr::message() !!} --}}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Detail Billing SP3 / {{ $sp3->no_surat_sp3 }}</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('billing-verifikasi/list') }}">Billing</a></li>
                                <li class="breadcrumb-item active">All Billing</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="row align-items-start mb-3">
                <div class="col">
                    <a href="{{ route('sp3-verifikasi/list') }}" type="button" class="btn btn-primary"><i
                            class="fa fa-arrow-left" aria-hidden="true"></i> Kembali</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <table class = "page-title">
                                            <tr class="mx-3">
                                                <td><b>No SP3</b></td>
                                                <td>: {{ $sp3->no_surat_sp3 }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Tanggal SP3</b></td>
                                                <td>: {{ \Carbon\Carbon::parse($sp3->tgl_sp3)->translatedFormat('d F Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Eselon</b></td>
                                                <td>: {{ $sp3->eselon->deskripsi }}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Tanggal Berobat</b></td>
                                                <td>:
                                                    {{ $sp3->tgl_masuk && $sp3->tgl_keluar
                                                        ? \Carbon\Carbon::parse($sp3->tgl_masuk)->translatedFormat('d F Y') .
                                                            ' - ' .
                                                            \Carbon\Carbon::parse($sp3->tgl_keluar)->translatedFormat('d F Y')
                                                        : null }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Jenis Layanan</b></td>
                                                <td>: {{ $sp3->layanan->nama }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card comman-shadow">
                        <div class="card-body">
                            <h4 class="card-title">List Deposit Bill Sp3</h4>
                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="BillingSp3List">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>No Registrasi</th>
                                            <th>Nama Pasien</th>
                                            <th>Eselon</th>
                                            <th>Total Biaya Eselon</th>
                                            <th>Total Biaya Kas</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card comman-shadow">
                        <div class="card-body">
                            <h4 class="card-title">List Deposit</h4>
                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="DepositList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>No Reg</th>
                                            <th>Nama Pasien</th>
                                            <th>Updated Date</th>
                                            <th>Keterangan</th>
                                            <th>Jumlah Deposit</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('sp3-verifikasi/list') }}" class="btn btn-primary me-2"><i class="fas fa-check"></i>
                        Selesai</a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal custom-modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Deposit Sp3</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form action="{{ route('billing/delete') }}" method="POST">
                                @csrf
                                <input type="hidden" name="slug" class="e_slug" value="">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary paid-continue-btn"
                                            style="width: 100%;">Delete</button>
                                    </div>
                                    <div class="col-6">
                                        <a data-bs-dismiss="modal" class="btn btn-primary paid-cancel-btn">Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
@section('script')
    <script>
        $(document).on('click', '.delete', function() {
            var _this = $(this).parents('tr');
            $('.e_slug').val(_this.find('.slug').data('slug'));
        });
    </script>


    {{-- get all deposit js --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#DepositList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                ajax: {
                    url: "{{ route('get-deposit-data') }}",
                    data: function(d) {
                        d.sp3_slug = "{{ $sp3->slug }}"; // kirim slug via request
                    }
                },
                columns: [{
                        data: 'no_reg',
                        name: 'no_reg',
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                    },
                    {
                        data: 'update_date',
                        name: 'update_date'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'jumlah_deposit',
                        name: 'jumlah_deposit'
                    },
                    {
                        data: 'modify',
                        name: 'modify',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>

    {{-- get user all js --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#BillingSp3List').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                ajax: {
                    url: "{{ route('get-billings-sp3-data', $sp3->slug) }}",
                },
                columns: [{
                        data: 'no_registrasi',
                        name: 'no_registrasi'
                    },
                    {
                        data: 'nama_pasien',
                        name: 'nama_pasien'
                    },
                    {
                        data: 'eslon',
                        name: 'eslon'
                    },
                    {
                        data: 'total_biaya_eselon',
                        name: 'total_biaya_eselon'
                    },
                    {
                        data: 'total_biaya_kas',
                        name: 'total_biaya_kas'
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                    },
                    {
                        data: 'modify',
                        name: 'modify',
                    },
                ]
            });
        });
    </script>

    <script>
        // Handle tombol tambah deposit
        $(document).on('click', '.btn-add-deposit', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const btn = $(this);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message ?? 'Deposit berhasil ditambahkan.');
                        // Reload kedua DataTable tanpa full page reload
                        $('#BillingSp3List').DataTable().ajax.reload(null, false);
                        $('#DepositList').DataTable().ajax.reload(null, false);
                    } else {
                        toastr.error(response.message ?? 'Gagal menambahkan deposit.');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                    toastr.error(msg);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-plus"></i>');
                }
            });
        });

        // Approve billing via AJAX
        $(document).on('click', '.btn-approve', function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#BillingSp3List').DataTable().ajax.reload(null, false);
                    toastr.success(response.message ?? 'Billing berhasil diapprove');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                    toastr.error(msg);
                }
            });
        });

        // Unapprove billing via AJAX
        $(document).on('click', '.btn-unapprove', function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#BillingSp3List').DataTable().ajax.reload(null, false);
                    toastr.success(response.message ?? 'Billing berhasil diunapprove');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                    toastr.error(msg);
                }
            });
        });
    </script>
@endsection
