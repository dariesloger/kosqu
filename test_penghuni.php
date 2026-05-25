<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$penghunis = \App\Models\Penghuni::with('kamar')->whereIn('nama', ['SAIFUL RIJAL', 'rafif zaki'])->get();

foreach ($penghunis as $p) {
    echo "Nama: " . $p->nama . "\n";
    echo "Jumlah Tagihan (Custom Rate): " . $p->jumlah_tagihan . "\n";
    echo "Harga Sewa (Kamar): " . ($p->kamar ? $p->kamar->harga_sewa : 'N/A') . "\n";
    echo "Tempo Periode: " . $p->tempo_periode . "\n";
    echo "Total Tagihan Periode: " . $p->totalTagihanPeriode() . "\n";
    echo "Total Dibayar: " . $p->totalDibayarPeriode() . "\n";
    echo "Sisa Tagihan: " . $p->sisaTagihan() . "\n";
    echo "--------------------------\n";
}
