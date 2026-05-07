<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .container {
            padding: 2px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .line {
            border-bottom: 1px solid black;
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 3px;
            vertical-align: top;
        }

        .border {
            border: 1px solid black;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .small {
            font-size: 6px;
        }

        .medium {
            font-size: 8px;
        }

        .box {
            border: 1px solid black;
            padding: 5px;
            display: inline-block;
        }

        .sub-header {
            font-size: 6px;
            margin-top: 10px;
        }

        .underline {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="title">
            SURAT PERMINTAAN PROSES PENAGIHAN
        </div>

        <table class = 'medium'>
            <tr>
                <td>KEPADA</td>
                <td>:</td>
                <td>WADIR KEUANGAN</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-left">NOMOR</td>
                <td>:</td>
                <td class="text-left" style="margin-left: 2px">{{ $data['nomor'] }}</td>
            </tr>
            <tr>
                <td>DARI</td>
                <td>:</td>
                <td>UNIT VERIFIKASI</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-left" style="margin-left: 2px ">TANGGAL</td>
                <td>:</td>
                <td class="text-left">{{ $data['tanggal'] }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <div class = "medium">
            PERIHAL :<b> {{ $data['hal'] }}</b>
        </div>

        <br><br>

        <div class="medium">
            KETERANGAN PEMBAYARAN:<br>
            {{ $data['ket_pembayaran'] . ' ' . $data['layanan'] . ' ' . $data['nama_rs'] . ' ' . $data['range_tgl'] . ' atas nama ' . $data['eselon'] }}
        </div>

        <br>

        <table class='medium'>
            <tr>
                <td>PASIEN</td>
                <td>: {{ $data['pasien'] }}</td>
            </tr>
            <tr>
                <td>TAGIHAN PASIEN</td>
                <td>: Rp {{ number_format($data['tagihan'], 0, ',', '.') }}</td>
                <td class="text-right">{{ $data['kunjungan'] }} PASIEN, {{ $data['kunjungan'] }} Kunjungan</td>
            </tr>
            <tr>
                <td>BEBAN PIHAK KE 3</td>
                <td>: Rp 0</td>
            </tr>
            <tr style="background: #bfbcbc">
                <td><b>JUMLAH PEMBAYARAN</b></td>
                <td class="text-left">
                    : <b>Rp {{ number_format($data['tagihan'], 0, ',', '.') }}</b>
                </td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="medium">
            DEMIKIAN UNTUK DAPAT DITERIMA DENGAN BAIK DAN MENJADI ACUAN UNTUK PROSES PENAGIHAN PEMBAYARAN OLEH UNIT YANG
            BAPAK/IBU PIMPIN.
        </div>
        <br>
        <br>

        <table class="medium">
            <tr>
                <td class="text-center">DISETUJUI OLEH,<br>WADIR YANMED</td>
                <td class="text-center">DIKETAHUI OLEH,<br>CASE MANAGER</td>
                <td class="text-center">DIBUAT OLEH,<br>PENATA VERIFKASI</td>
            </tr>

            <tr>
                <td class="text-center">
                    <br><br>
                    @if ($data['ttd_path'])
                        <img src="{{ public_path($data['ttd_path']) }}" height="40">
                    @endif
                    <br>
                    <b class="underline">{{ $data['disetujui_oleh'] }}</b>
                </td>
                <td class="text-center">
                    <br><br>
                    {{-- <img src="data:image/png;base64,{{ $qr }}"> --}}
                    <br>
                    <b class="underline">{{ $data['diketahui_oleh'] }}</b>
                </td>
                <td class="text-center">
                    <br><br>
                    {{-- <img src="data:image/png;base64,{{ $qr }}"> --}}
                    <br>
                    <b class="underline">{{ $data['dibuat_oleh'] }}</b>
                </td>
            </tr>
        </table>
        <br><br>
        <table class="medium">
            <tr>
                <td class="underline">DISPOSISI</td>
                <td>Selisih Biaya</td>
                <td>:</td>
                <td>Ada</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Tagihan Semula (Rp.)</td>
                <td>:</td>
                <td>{{ number_format($data['tagihan'], 0, ',', '.') }},-</td>
                <td></td>
                <td>Dibayarkan (Rp):</td>
                <td class="text-right">0,00,-</td>
            </tr>
            <tr>
                <td></td>
                <td>Tidak Ditanggung (Rp.)</td>
                <td>:</td>
                <td>0,00,-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Item Tidak Ditanggung</td>
                <td>:</td>
                <td>0,-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Karena</td>
                <td>:</td>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Mohon Ditagihkan ke</td>
                <td>:</td>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Sebesar (Rp)</td>
                <td>:</td>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Karena</td>
                <td>:</td>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>Keterangan</td>
                <td>:</td>
                <td>-</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <br><br>
        <table>
            <tr>
                <td class="text-left">
                    <div class="box" style="width: 60px; height: 12px;">
                    </div>
                </td>
                <td class="text-right">
                    <div class="box">
                        SURAT JAMINAN ADA / TIDAK ADA
                    </div>
                </td>
            </tr>
        </table>
        {{-- <div class="box text-right">
            SURAT JAMINAN ADA / TIDAK ADA
        </div> --}}

    </div>
</body>

</html>
