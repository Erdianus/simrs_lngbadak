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
                                <li class="breadcrumb-item"><a href="{{ route('sp3-verifikasi/list') }}">SP3</a></li>
                                <li class="breadcrumb-item active">All SP3</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            <div class="student-group-form">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search by ID ...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search by Name ...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search by Phone ...">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="search-student-btn">
                            <button type="btn" class="btn btn-primary">Search</button>
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
                                        <h3 class="page-title">List SP3</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('sp3-verifikasi/list') }}"
                                            class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a>
                                        <a href="{{ route('sp3/add/page') }}" class="btn btn-primary"><i
                                                class="fas fa-plus"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="EselonsList">
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
                    url: "{{ route('get-sp3-verifikasi-data') }}",
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
                        data: 'total_biaya',
                        name: 'total_biaya',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status'
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
@endsection

@endsection
