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
                                                <td>:
                                                    {{ 'Rp ' . number_format($sp3->jenis_sp3 === 'tagihan keluar' ? $sp3->total_tagihan : $sp3->total_biaya, 0, ',', '.') }}
                                                </td>
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
                                        @if ($sp3->jenis_sp3 === 'deposito')
                                            <a href="{{ route('sp3/add/page/list-deposit', $sp3->slug) }}"
                                                class="btn btn-outline-gray me-2 active">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </a>
                                        @elseif ($sp3->jenis_sp3 === 'mcu')
                                            <a href="{{ route('sp3/add/page/list-mcu', $sp3->slug) }}"
                                                class="btn btn-outline-gray me-2 active">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('sp3/refresh', $sp3->slug) }}"
                                                class="btn btn-outline-gray me-2 active">
                                                <i class="fa fa-retweet" aria-hidden="true"></i>
                                            </a>
                                        @endif
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
                                <table class="table table-stripped table table-hover table-center mb-0" id="BillingList">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>No Registrasi</th>
                                            <th>Nama Pasien</th>
                                            <th>Eselon</th>
                                            <th>Total Biaya Eselon</th>
                                            <th>COB</th>
                                            <th>Deposit</th>
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

    {{-- modal delete --}}
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
                                <input type="hidden" name="slug" class="e_slug" id = 'slug-delete' value="">
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

    {{-- modal add COB --}}
    <div class="modal custom-modal fade" id="cob" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-header">
                        <h3>Input COB Billing</h3>
                    </div>
                    <div class="modal-btn delete-action">
                        <div class="row">
                            <form id="form-cob" action="{{ route('billing/add/save/cob') }}" method="POST">
                                @csrf
                                <input type="hidden" name="slug" id="slug-cob" value="">
                                <div class="col-12">
                                    <div class="form-group local-forms">
                                        <label>Total COB <span class="login-danger">*</span></label>
                                        <input type="text" class="form-control @error('total_cob') is-invalid @enderror"
                                            name="total_cob_display" id="total_cob_display" placeholder="Rp 0"
                                            value="{{ old('total_cob') ? number_format(old('total_cob'), 0, ',', '.') : '' }}"
                                            autocomplete="off">

                                        {{-- Hidden input yang dikirim sebagai integer --}}
                                        <input type="hidden" name="total_cob" id="total_cob"
                                            value="{{ old('total_cob') }}">

                                        @error('total_cob')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary paid-continue-btn"
                                            style="width: 100%;">Add</button>
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
        $('#delete').on('show.bs.modal', function(e) {
            console.log('relatedTarget:', e.relatedTarget);
            console.log('button data:', $(e.relatedTarget).data());
            var button = $(e.relatedTarget); // tombol yang diklik
            var slug = button.data('slug');
            console.log('slug:', slug); // pastikan muncul
            $(this).find('#slug-delete').val(slug);
        });
    </script>

    <script>
        $('#cob').on('show.bs.modal', function(e) {
            console.log('relatedTarget:', e.relatedTarget);
            console.log('button data:', $(e.relatedTarget).data());
            var button = $(e.relatedTarget); // tombol yang diklik
            var slug = button.data('slug');
            console.log('slug:', slug); // pastikan muncul
            $(this).find('#slug-cob').val(slug);
        });
    </script>

    {{-- get user all js --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('#BillingList').DataTable({
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
                        data: 'cob',
                        name: 'cob'
                    },
                    {
                        data: 'deposit',
                        name: 'deposit'
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
        // Approve billing via AJAX
        $(document).on('click', '.btn-approve', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            console.log(url);

            $.ajax({
                url: url,
                method: 'GET',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#BillingList').DataTable().ajax.reload(null, false);
                    toastr.success(response.message ?? 'Billing berhasil diapprove');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                    toastr.error(msg);
                }
            });
        });
    </script>
    <script>
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
                    $('#BillingList').DataTable().ajax.reload(null, false);
                    toastr.success(response.message ?? 'Billing berhasil diunapprove');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
                    toastr.error(msg);
                }
            });
        });

        //add COB pada billing
        // $(document).on('submit', '#form-cob', function(e) {
        //     e.preventDefault();

        //     const form = $(this);
        //     const url = form.attr('action');

        //     $.ajax({
        //         url: url,
        //         method: 'POST',
        //         data: form.serialize(),
        //         success: function(response) {
        //             $('#cob').modal('hide');
        //             form[0].reset();
        //             $('#BillingList').DataTable().ajax.reload(null, false);
        //             toastr.success(response.message ?? 'COB berhasil ditambahkan');
        //         },
        //         error: function(xhr) {
        //             const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan';
        //             toastr.error(msg);
        //         }
        //     });
        // });
    </script>



    <script>
        const display = document.getElementById('total_cob_display');
        const hidden = document.getElementById('total_cob');

        display.addEventListener('input', function() {
            // Hapus semua karakter selain angka
            let raw = this.value.replace(/\D/g, '');

            // Simpan nilai integer ke hidden input
            hidden.value = raw;

            // Format tampilan dengan titik sebagai pemisah ribuan
            this.value = raw ? 'Rp ' + parseInt(raw).toLocaleString('id-ID') : '';
        });
    </script>
@endsection

@endsection
