<?php

namespace App\Services;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;  // Importar Storage facade
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use setasign\Fpdi\Fpdi;

class UtilsService
{

    // método para o "Clean PDF"
    public function cleanPdf(string $inputPath): string
    {
        $cleanedPath = uniqid('cleaned_pdf_'. time(), true) . '.pdf';
        $inputAbsolutPath = storage_path('app/public/'.$inputPath);
        $cleanedAbsolutPath = storage_path('app/public/' . $cleanedPath);

        $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($cleanedAbsolutPath) . " " . escapeshellarg($inputAbsolutPath);
        exec($cmd, $output, $return_var);

        if ($return_var !== 0) {
            throw new \Exception('Ghostscript falhou ao limpar o PDF. Certifica-te que está instalado.');
        }

        return $cleanedPath;
    }

    public function generateQrCode($qrData = 'https://www.us.edu.cv', $qrFile = 'qrcode.png')
    {
        // Gerar o QR code
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L, // Nível de correção de erro
            'imageBase64' => false,
        ]);
    
        // Gerar a imagem PNG do QR Code
        $qrcode = (new QRCode($options))->render($qrData);
    
        file_put_contents($qrFile, $qrcode);
    
        return $qrFile;
    }
}