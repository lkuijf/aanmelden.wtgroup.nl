<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function renderImage(Request $request, $year, $month, $file) {
        $urlToWpImage = config('app_wt.cmsPath') . '/wp-content/uploads/' . $year . '/' . $month . '/' . $file;
        $placeholder = 'https://placehold.jp/24/ccc/000/250x250.png?text=Geen+afbeelding+gevonden';

        $imgSize = getimagesize($urlToWpImage);
        $fp = fopen($urlToWpImage, "rb");
        // readfile($urlToWpImage); // Also possible solution
        if($imgSize && $fp) {
            header("Content-type: {$imgSize['mime']}");
            fpassthru($fp);
            exit;
        } else {
            $imgSize = getimagesize($placeholder);
            $fp = fopen($urlToWpImage, "rb");
            header("Content-type: {$imgSize['mime']}");
            fpassthru($fp);
            exit;
        }
        exit;
    }
}
