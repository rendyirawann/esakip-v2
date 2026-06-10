<?php
$mapping = [
    'SakipBidangSearch.php' => ['urusan'],
    'SakipKegiatanSearch.php' => ['urusan', 'bidang', 'program'],
    'SakipLkesubkomponenSearch.php' => ['refLkekomponen'],
    'SakipLkesubkriteriaSearch.php' => ['refLkekomponen', 'refLkesubkomponen'],
    'SakipMisiSearch.php' => ['periode', 'visi'],
    'SakipMisiPSearch.php' => ['periode', 'visi'],
    'SakipPegawaibappedaSearch.php' => ['refEselon', 'refTitle', 'refBidangbappeda'],
    'SakipPeriodeSearch.php' => ['periode5Tahun'],
    'SakipPimpinanSearch.php' => ['refPeriode'],
    'SakipProgramSearch.php' => ['urusan', 'bidang'],
    'SakipSasaranSearch.php' => ['visi', 'misi', 'tujuan', 'periode'],
    'SakipSasaranPSearch.php' => ['visi', 'misi', 'tujuan', 'periode'],
    'SakipSkpdSearch.php' => ['urusan', 'bidang'],
    'SakipSubkegiatanSearch.php' => ['urusan', 'bidang', 'program', 'kegiatan'],
    'SakipTujuanSearch.php' => ['visi', 'misi', 'periode'],
    'SakipTujuanPSearch.php' => ['visi', 'misi', 'periode'],
    'SakipVisiSearch.php' => ['periode'],
    'SakipVisiPSearch.php' => ['periode'],
    'UserSearch.php' => ['skpd', 'group'],
];

$dir = __DIR__ . '/../backend/models/search';

foreach ($mapping as $file => $relations) {
    $path = $dir . '/' . $file;
    if (!file_exists($path)) {
        echo "File not found: $path\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    // Check if it's already optimized
    if (strpos($content, '$query->with(') !== false) {
        echo "Already optimized: $file\n";
        continue;
    }
    
    $target = "// add conditions that should always apply here";
    $relString = implode("', '", $relations);
    $replacement = "// add conditions that should always apply here\n        \$query->with(['$relString']);";
    
    if (strpos($content, $target) !== false) {
        $content = str_replace($target, $replacement, $content);
        file_put_contents($path, $content);
        echo "Optimized: $file\n";
    } else {
        echo "Target comment not found in: $file\n";
    }
}
