@extends('layouts.master')
@section('content')
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
                                                <td><b>Total Tagihan</b></td>
                                                <td>: {{ 'Rp ' . number_format($sp3->total_tagihan, 0, ',', '.') }}</td>
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
                    <div class="card card-table comman-shadow">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Billing</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('sp3/refresh', $sp3->slug) }}"
                                            class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-retweet" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a>
                                        <a href="{{ route('billing/add/page') }}" class="btn btn-primary"><i
                                                class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="EselonsList">
                                    <thead class="student-thread">
                                        <tr>

                                            <th>SP3</th>
                                            <th>No Registrasi</th>
                                            <th>Nama Pasien</th>
                                            <th>Eselon</th>
                                            <th>Tindakan</th>
                                            <th>BMHP</th>
                                            <th>Resep</th>
                                            <th>KIP</th>
                                            <th>Sewa Kamar</th>
                                            <th>PPN</th>
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
        </div>
    </div>

    {{-- model student delete --}}
    <div class="modal custom-modal fade" id="delete" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Delete Billing</h3>
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
@section('script')
    {{-- delete js --}}
    <script>
        $(document).on('click', '.delete', function() {
            var _this = $(this).parents('tr');
            $('.e_slug').val(_this.find('.slug').data('slug'));
        });
    </script>

    {{-- get user all js --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#EselonsList').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                searching: true,
                ajax: {
                    url: "{{ route('get-billings-sp3-data', $sp3->slug) }}",
                },
                columns: [{
                        data: 'sp3',
                        name: 'sp3'
                    },
                    {
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
                        data: 'total_tindakan',
                        name: 'total_tindakan'
                    },
                    {
                        data: 'total_BMHP',
                        name: 'total_BMHP'
                    },
                    {
                        data: 'total_resep',
                        name: 'total_resep'
                    },
                    {
                        data: 'total_KIP',
                        name: 'total_KIP'
                    },
                    {
                        data: 'total_sewa_kamar',
                        name: 'total_sewa_kamar'
                    },
                    {
                        data: 'total_PPN',
                        name: 'total_PPN'
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
@endsection

@endsection
