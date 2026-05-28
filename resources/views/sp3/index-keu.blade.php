@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">SP3</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('sp3-keuangan/list') }}">SP3</a></li>
                                <li class="breadcrumb-item active">All SP3</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            {{-- <div class="student-group-form mb-3">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Dari Tanggal (tgl_masuk)</label>
                            <input type="text" class="form-control datetimepicker" id="filter_dari_tgl"
                                placeholder="Dari Tanggal"
                                value="{{ \Carbon\Carbon::now()->subDays(30)->format('d-M-Y') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Sampai Tanggal (tgl_keluar)</label>
                            <input type="text" class="form-control datetimepicker" id="filter_sampai_tgl"
                                placeholder="Sampai Tanggal" value="{{ \Carbon\Carbon::now()->format('d-M-Y') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 d-flex align-items-end">
                        <div class="form-group">
                            <button type="button" id="btn_filter" class="btn btn-primary me-2">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <button type="button" id="btn_reset" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">List SP3</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('sp3-keuangan/list') }}" class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        {{-- <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a> --}}
                                        {{-- <div class="btn btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('sp3/add/page') }}" class="dropdown-item"
                                                        href="#">Sp3 Billing</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('sp3/add/page/deposit') }}" class="dropdown-item"
                                                        href="#">Sp3 Deposit</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('sp3/add/page/mcu') }}" class="dropdown-item"
                                                        href="#">Sp3 MCU</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('sp3/add/page/tagihan-keluar') }}"
                                                        class="dropdown-item" href="#">Sp3 Pembayaran Tagihan Luar</a>
                                                </li>

                                            </ul>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="Sp3List">
                                    <thead class="student-thread">
                                        <tr>

                                            <th>SP3</th>
                                            <th>Tanggal SP3</th>
                                            <th>Nomor Tagihan</th>
                                            <th>Tanggal Terima Keu</th>
                                            <th>Tagihan Kode</th>
                                            <th>Ketrangan INV Pasien</th>
                                            <th>Ketrangan INV RS</th>
                                            <th>Eselon</th>
                                            <th>Jumlah Pasien</th>
                                            <th>Jumlah Kunjungan</th>
                                            <th>Keterangan Pembayaran</th>
                                            <th>Layanan</th>
                                            <th>Tanggal Berobat</th>
                                            <th>Total Biaya</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal sp3 delete --}}
    <div class="modal custom-modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Sp3</h3>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form action="{{ route('sp3/delete') }}" method="POST">
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

    {{-- Modal Revisi --}}
    <div class="modal custom-modal fade" id="revisi" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Revisi SP3</h3>
                    </div>

                    <div class="modal-btn delete-action">
                        <form id="form-revisi" action="{{ route('sp3/revisi', ['slug' => ':slug']) }}" method="POST">
                            @csrf
                            <input type="hidden" name="slug" id="slug-revisi" value="">

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group local-forms">
                                        <label>
                                            Penjelasan Revisi
                                            <span class="login-danger">*</span>
                                        </label>
                                        <input type="text" name="alasan_rev" id="alasan_rev"
                                            class="form-control @error('alasan_rev') is-invalid @enderror"
                                            placeholder="Penjelasan Revisi" value="{{ old('alasan_rev') ?? '' }}"
                                            autocomplete="off">
                                        @error('alasan_rev')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary paid-continue-btn w-100">
                                        Simpan
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a data-bs-dismiss="modal" class="btn btn-primary paid-cancel-btn w-100">
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@section('script')
    {{-- delete js --}}
    <script>
        $(document).on('click', '.delete', function() {
            var _this = $(this).parents('tr');
            $('.e_slug').val(_this.find('.slug').data('slug'));
        });

        $('#revisi').on('show.bs.modal', function(e) {
            var $button = $(e.relatedTarget);
            var slug = $button.data('slug');
            var baseUrl = "{{ route('sp3/revisi', ['slug' => ':slug']) }}";
            var actionUrl = baseUrl.replace(':slug', slug);

            $(this).find('#slug-revisi').val(slug);
            $(this).find('#form-revisi').attr('action', actionUrl);
        });
    </script>

    {{-- get user all js --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#Sp3List').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                ajax: {
                    url: "{{ route('get-sp3-keuangan-data') }}",
                    data: function(d) {
                        d.dari_tgl = $('#filter_dari_tgl').val();
                        d.sampai_tgl = $('#filter_sampai_tgl').val();
                    }
                },
                columns: [{
                        data: 'no_sp3',
                        name: 'no_sp3',
                    },
                    {
                        data: 'tgl_sp3',
                        name: 'tgl_sp3'
                    },
                    {
                        data: 'nomor_tagihan',
                        name: 'nomor_tagihan'
                    },
                    {
                        data: 'tgl_terima_keu',
                        name: 'tgl_terima_keu'
                    },
                    {
                        data: 'perihal_tagihan',
                        name: 'perihal_tagihan'
                    },
                    {
                        data: 'ket_inv_pasien',
                        name: 'ket_inv_pasien'
                    },
                    {
                        data: 'ket_inv_rs',
                        name: 'ket_inv_rs'
                    },
                    {
                        data: 'eselon',
                        name: 'eselon'
                    },
                    {
                        data: 'jumlah_pasien',
                        name: 'jumlah_pasien'
                    },
                    {
                        data: 'jumlah_kunjungan',
                        name: 'jumlah_kunjungan'
                    },
                    {
                        data: 'ket_pembayaran',
                        name: 'ket_pembayaran'
                    },
                    {
                        data: 'layanan',
                        name: 'layanan'
                    },
                    {
                        data: 'tgl_berobat',
                        name: 'tgl_berobat'
                    },
                    {
                        data: 'total_tagihan',
                        name: 'total_tagihan'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
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
    <script>
        // Tombol Filter: reload datatable dengan nilai filter yang dipilih
        $('#btn_filter').on('click', function() {
            $('#Sp3List').DataTable().ajax.reload(null, false);
            // null = tidak ada callback, false = jangan kembali ke halaman 1
        });

        // Tombol Reset: kembalikan nilai filter ke default, lalu reload
        $('#btn_reset').on('click', function() {
            $('#filter_dari_tgl').val('{{ \Carbon\Carbon::now()->subDays(30)->format('d-M-Y') }}');
            $('#filter_sampai_tgl').val('{{ \Carbon\Carbon::now()->format('d-M-Y') }}');
            $('#Sp3List').DataTable().ajax.reload(null, false);
        });

        // Approve SP3 via AJAX
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
                    $('#Sp3List').DataTable().ajax.reload(null, false);
                    toastr.success(response.message ?? 'SP3 berhasil disetujui');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                    toastr.error(msg);
                }
            });
        });
    </script>
@endsection

@endsection
