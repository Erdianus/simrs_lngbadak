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
            PENAGIHAN BIAYA RAWAT JALAN
        </div>

        <br>

        <table class="border medium">
            <tr>
                <td class="border">KODE REKENING</td>
                <td class="border">KODE BAGIAN</td>
                <td class="border">JENIS BIAYA</td>
            </tr>
            <tr>
                <td class="border">-</td>
                <td class="border">-</td>
                <td class="border">-</td>
            </tr>
        </table>

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
        </table>

        <div class="line"></div>

        <table class="medium">
            <tr>
                <td><b>JUMLAH PEMBAYARAN</b></td>
                <td class="text-right">
                    <b>Rp {{ number_format($data['tagihan'], 0, ',', '.') }}</b>
                </td>
            </tr>
        </table>

        <br><br>

        <table class="medium">
            <tr>
                <td>DISETUJUI OLEH,</td>
                <td class="text-right">DIBUAT OLEH,</td>
            </tr>

            <tr>
                <td>
                    <br><br>
                    @if ($data['ttd_path'])
                        <img src="{{ public_path($data['ttd_path']) }}" height="40">
                    @endif
                    <br>
                    <b>{{ $data['disetujui_oleh'] }}</b>
                </td>

                <td class="text-right">
                    <br><br>
                    {{-- <img src="data:image/png;base64,{{ $qr }}"> --}}
                    <br>
                    <b>{{ $data['dibuat_oleh'] }}</b>
                </td>
            </tr>
        </table>

        <br><br>

        <div class="box">
            SURAT JAMINAN ADA / TIDAK ADA
        </div>

    </div>
</body>

</html>
