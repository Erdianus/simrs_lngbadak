@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Billing</h3>
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
                                        <h3 class="page-title">Billing</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('billing-verifikasi/list') }}"
                                            class="btn btn-outline-gray me-2 active">
                                            <i class="fa fa-list" aria-hidden="true"></i>
                                        </a>
                                        {{-- <a href="{{ route('student/grid') }}" class="btn btn-outline-gray me-2">
                                            <i class="fa fa-th" aria-hidden="true"></i>
                                        </a> --}}
                                        <a href="#" class="btn btn-outline-primary me-2"><i
                                                class="fas fa-download"></i> Download</a>
                                        {{-- <a href="{{ route('billing/add/page') }}" class="btn btn-primary"><i
                                                class="fas fa-plus"></i></a> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-stripped table table-hover table-center mb-0" id="EselonsList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Keterangan</th>
                                            <th>No Registrasi</th>
                                            <th>Nama Pasien</th>
                                            <th>Eselon</th>
                                            <th>Layanan</th>
                                            <th>Sub Layanan</th>
                                            <th>Biaya</th>
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
                    url: "{{ route('get-billings-verifikasi-data') }}",
                },
                columns: [{
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'no_registrasi',
                        name: 'no_registrasi'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'eslon',
                        name: 'eslon'
                    },
                    {
                        data: 'layanan',
                        name: 'layanan'
                    },
                    {
                        data: 'sub_layanan',
                        name: 'sub_layanan'
                    },
                    {
                        data: 'biaya',
                        name: 'biaya'
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
