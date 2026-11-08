<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaBarang = isset($_POST['nama_barang']) ? trim($_POST['nama_barang']) : 'Barang';
    $harga = isset($_POST['harga']) ? (float)$_POST['harga'] : 0;
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;
    $diskonPersen = isset($_POST['diskon']) ? (float)$_POST['diskon'] : 0;
    $ppnPersen = isset($_POST['ppn']) ? (float)$_POST['ppn'] : 0;
    $jumlahOrang = isset($_POST['jumlah_orang']) ? (int)$_POST['jumlah_orang'] : 1;
    $uangDibayar = isset($_POST['uang_dibayar']) ? (float)$_POST['uang_dibayar'] : 0;

    // Perhitungan transaksi
    $subtotalBruto = $harga * $qty;
    $nominalDiskon = $subtotalBruto * ($diskonPersen / 100);
    $subtotalNetto = $subtotalBruto - $nominalDiskon;
    $nominalPPN = $subtotalNetto * ($ppnPersen / 100);
    $totalBayar = $subtotalNetto + $nominalPPN;

    $pembagi = $jumlahOrang > 0 ? $jumlahOrang : 1;
    $bayarPerOrang = $totalBayar / $pembagi;

    // Inovasi 1: kembalian otomatis
    $kembalian = $uangDibayar - $totalBayar;
    $uangKurang = $kembalian < 0 ? abs($kembalian) : 0;
    $statusPembayaran = $kembalian >= 0 ? 'LUNAS' : 'UANG KURANG';
} else {
    header('Location: index.html');
    exit;
}

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - KasirKu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container receipt-page">
        <header class="header">
            <div>
                <span class="badge">KASIRKU</span>
                <h1>Struk Transaksi</h1>
                <p>Rincian pembayaran pelanggan</p>
            </div>
            <div class="header-icon">🧾</div>
        </header>

        <div class="receipt">
            <div class="receipt-heading">
                <span class="receipt-icon">🛒</span>
                <div>
                    <h2>Detail Pembelian</h2>
                    <p><?php echo htmlspecialchars($namaBarang); ?> × <?php echo $qty; ?></p>
                </div>
            </div>

            <div class="result-row">
                <span>Harga Satuan</span>
                <strong><?php echo formatRupiah($harga); ?></strong>
            </div>

            <div class="result-row">
                <span>Jumlah</span>
                <strong><?php echo $qty; ?> item</strong>
            </div>

            <div class="result-row">
                <span>Subtotal Bruto</span>
                <strong><?php echo formatRupiah($subtotalBruto); ?></strong>
            </div>

            <div class="result-row text-danger">
                <span>Potongan Diskon (<?php echo $diskonPersen; ?>%)</span>
                <strong>- <?php echo formatRupiah($nominalDiskon); ?></strong>
            </div>

            <div class="result-row">
                <span>Subtotal Netto</span>
                <strong><?php echo formatRupiah($subtotalNetto); ?></strong>
            </div>

            <div class="result-row text-success">
                <span>PPN (<?php echo $ppnPersen; ?>%)</span>
                <strong>+ <?php echo formatRupiah($nominalPPN); ?></strong>
            </div>

            <div class="result-row total">
                <span>Total Bayar</span>
                <strong><?php echo formatRupiah($totalBayar); ?></strong>
            </div>

            <div class="result-row">
                <span>Uang Dibayar</span>
                <strong><?php echo formatRupiah($uangDibayar); ?></strong>
            </div>

            <?php if ($kembalian >= 0): ?>
                <div class="result-row change">
                    <span>💰 Kembalian</span>
                    <strong><?php echo formatRupiah($kembalian); ?></strong>
                </div>
            <?php else: ?>
                <div class="result-row text-danger change">
                    <span>⚠️ Uang Kurang</span>
                    <strong><?php echo formatRupiah($uangKurang); ?></strong>
                </div>
            <?php endif; ?>

            <div class="payment-status <?php echo $kembalian >= 0 ? 'paid' : 'unpaid'; ?>">
                <?php echo $statusPembayaran; ?>
            </div>

            <div class="result-row split">
                <span>Bayar per Orang (<?php echo $jumlahOrang; ?> orang)</span>
                <strong><?php echo formatRupiah($bayarPerOrang); ?></strong>
            </div>
        </div>

        <div class="receipt-actions">
            <button onclick="window.print()" class="btn-print">🖨️ Cetak Struk</button>
            <a href="index.html" class="btn-back">← Transaksi Baru</a>
        </div>

        <div class="thank-you">
            <strong>Terima kasih telah menggunakan KasirKu!</strong>
            <span>Transaksi cepat, sederhana, dan lebih rapi.</span>
        </div>
    </div>
</body>
</html>
