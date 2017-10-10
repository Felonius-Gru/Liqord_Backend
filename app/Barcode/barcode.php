<?php

include(app_path().'/Barcode/src/BarcodeGenerator.php');
include(app_path().'/Barcode/src/BarcodeGeneratorPNG.php');
include(app_path().'/Barcode/phpqrcode/qrlib.php');

if (!is_dir("uploads/barcode"))
            mkdir("uploads/barcode", 0777);

$barcode_image = "uploads/barcode/{$sku}.png";


$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
$src = base64_encode($generatorPNG->getBarcode($sku, $generatorPNG::TYPE_CODE_128_B, 2, 80));
file_put_contents($barcode_image, base64_decode($src));



    
	