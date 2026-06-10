<?php

$searchDir = __DIR__ . '/../frontend/models/search';

// Define the mappings of search models to the relations they should eager load.
$mappings = [
    'SakipCascadingkegiatanSearch' => ['refProgram', 'refKegiatan', 'refPeriode'],
    'SakipCascadingprogramSearch' => ['refProgram', 'refPeriode'],
    'SakipCascadingsubkegiatanSearch' => ['refSubkegiatan', 'refKegiatan', 'refProgram', 'refPeriode'],
    
    'SakipIndikatorcascadingkegiatanSearch' => ['refPeriode', 'refCascadingKegiatan'],
    'SakipIndikatorcascadingkegiatanTriwulanSearch' => ['refPeriode', 'refIndikatorCascadingKegiatan'],
    
    'SakipIndikatorcascadingprogramSearch' => ['refPeriode', 'refCascadingProgram'],
    'SakipIndikatorcascadingprogramTriwulanSearch' => ['refPeriode', 'refIndikatorCascadingProgram'],
    
    'SakipIndikatorcascadingsubkegiatanSearch' => ['refPeriode', 'refCascadingSubkegiatan'],
    'SakipIndikatorcascadingsubkegiatanTriwulanSearch' => ['refPeriode', 'refIndikatorCascadingSubkegiatan'],
    
    'SakipIndikatorsasaranrenstraSearch' => ['refPeriode', 'refSasaranrenstra'],
    'SakipIndikatorsasaranrenstraPSearch' => ['refPeriode', 'refSasaranrenstra'],
    
    'SakipIndikatorsasaranrenstraTriwulanSearch' => ['refPeriode', 'refIndikatorsasaranrenstra'],
    'SakipIndikatorsasaranrenstraPTriwulanSearch' => ['refPeriode', 'refIndikatorsasaranrenstra'],
    
    'SakipIndikatortujuanrenstraSearch' => ['refPeriode', 'tujuanRenstra'],
    'SakipKebijakanSearch' => ['refPeriode', 'strategiRenstra', 'sasaranRenstra'],
    'SakipRenstratujuanSearch' => ['refPeriode', 'tujuan', 'misi', 'visi'],
    
    'SakipSasaranrenstraSearch' => ['refPeriode', 'refTujuan', 'refSasaran', 'refVisi', 'refMisi'],
    'SakipSasaranrenstraPSearch' => ['refPeriode', 'refTujuan', 'refSasaran', 'refVisi', 'refMisi'],
    
    'SakipStrategiSearch' => ['refPeriode', 'sasaranRenstra'],
    'SakipTujuanrenstraSearch' => ['refPeriode', 'misi', 'visi'],
    'SakipTujuanrenstraPSearch' => ['refPeriode', 'misi'],
    
    'SakipVisiSearch' => ['periode5Tahun'],
    'SakipVisiPSearch' => ['periode5Tahun'],
    
    'UserSearch' => ['skpd', 'group'],
    
    // Penjabat skpd cascading
    'SakipPenjabatskpdCascadingkegiatanSearch' => ['refProgram', 'refKegiatan', 'refPeriode'],
    'SakipPenjabatskpdCascadingprogramSearch' => ['refProgram', 'refPeriode'],
    'SakipPenjabatskpdCascadingsubkegiatanSearch' => ['refSubkegiatan', 'refKegiatan', 'refProgram', 'refPeriode'],
    
    // Simona cascading
    'SimonaCascadingkegiatanSearch' => ['refProgram', 'refKegiatan', 'refPeriode'],
    'SimonaCascadingsubkegiatanSearch' => ['refSubkegiatan', 'refKegiatan', 'refProgram', 'refPeriode'],
    'SimonaKeluaranmediacascadingkegiatanSearch' => ['refProgram', 'refKegiatan'],
    'SimonaKeluaranmediacascadingsubkegiatanSearch' => ['refProgram', 'refKegiatan', 'refSubkegiatan'],
    
    'SimonaMediacascadingkegiatanOpdSearch' => ['refPeriode'],
    'SimonaMediacascadingkegiatanSearch' => ['refPeriode'],
    'SimonaMediacascadingsubkegiatanOpdSearch' => ['refPeriode'],
    'SimonaMediacascadingsubkegiatanSearch' => ['refPeriode'],
    
    'SimonaRincianbelanjacascadingkegiatanSearch' => ['refProgram', 'refKegiatan'],
    'SimonaRincianbelanjacascadingsubkegiatanSearch' => ['refProgram', 'refKegiatan', 'refSubkegiatan'],
    
    'SakipLkeSearch' => ['refPeriode'],
];

foreach ($mappings as $className => $relations) {
    $filePath = $searchDir . '/' . $className . '.php';
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    
    // Find $query = ModelName::find();
    // Regex matches: $query = ClassName::find(); or similar
    $pattern = '/\$query\s*=\s*([a-zA-Z0-9_]+)::find\(\);/';
    if (preg_match($pattern, $content, $matches)) {
        $findString = $matches[0];
        $modelClass = $matches[1];
        
        // Prepare relations list format
        $relString = "['" . implode("', '", $relations) . "']";
        $withStatement = "\n\n        // Eager load relations for performance\n        \$query->with($relString);";
        
        // Check if $query->with( is already in the file to avoid duplicate injection
        if (strpos($content, '$query->with(') === false) {
            $newContent = str_replace($findString, $findString . $withStatement, $content);
            file_put_contents($filePath, $newContent);
            echo "Successfully updated $className with eager loading of: " . implode(', ', $relations) . "\n";
        } else {
            echo "$className already has eager loading config. Skipping.\n";
        }
    } else {
        echo "Could not find find() pattern in $className\n";
    }
}
