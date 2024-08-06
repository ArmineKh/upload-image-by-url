<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{

    public function index()
    {
        $images = Image::all()->sortByDesc('id')->toArray();
        return view('index', compact('images'));
    }
    public function uploadImage(Request $request)
    {
        $validator = Validator::make( $request->all(), [
            'url' => 'required|string|max:255',
            'resolution' => 'required|string|max:255',
            'watermark_text' => 'required|string|max:255',
        ]);

        if ( $validator->fails() ) {
            return response()->json( [ 'errors' => $validator->errors() ] );
        }

        $url = $request->url;

        $imageWidth = $imageHeight = explode("x",$request->resolution)[0];
        list($orig_width, $orig_height) = getimagesize($url);

        if ($orig_width < $imageWidth && $orig_height < $imageHeight){
            return response()->json( [ 'errors' => ['Image size incorrect!'] ] );
        }


        $dest = imagecreatetruecolor($imageWidth, $imageHeight);
        $fileType = pathinfo($url, PATHINFO_EXTENSION);

        $filename = time() . basename($url);

        $watermark_text = $request->watermark_text;
        $fontsize = "24";
        $font = public_path("font\Roboto-Regular.ttf");


        switch ($fileType) {
            case "jpg":
            case "jpeg":
                $src = imagecreatefromjpeg($url);
                $white = imagecolorallocate($src, 255, 255, 255);
                imagettftext($src, $fontsize, 0, 10, 25, $white, $font, $watermark_text);
                imagecopyresampled($dest, $src, 0, 0, 0, 0, $imageHeight, $imageHeight, $orig_width, $orig_height);
                imagejpeg($dest, storage_path('app/public/images/' . $filename), 100);
                imagedestroy($dest);
                imagedestroy($src);
                break;
            case "png":
                $src = imagecreatefrompng($url);
                $white = imagecolorallocate($src, 255, 255, 255);
                imagettftext($src, $fontsize, 0, 10, 25, $white, $font, $watermark_text);
                imagecopyresampled($dest, $src, 0, 0, 0, 0, $imageHeight, $imageHeight, $orig_width, $orig_height);
                imagepng($dest, storage_path('app/public/images/' . $filename));
                imagedestroy($dest);
                imagedestroy($src);
                break;
            case "gif":
                $src = imagecreatefromgif($url);
                $white = imagecolorallocate($src, 255, 255, 255);
                imagettftext($src, $fontsize, 0, 10, 25, $white, $font, $watermark_text);
                imagecopyresampled($dest, $src, 0, 0, 0, 0, $imageHeight, $imageHeight, $orig_width, $orig_height);
                imagegif($dest, storage_path('app/public/images/' . $filename));
                imagedestroy($dest);
                imagedestroy($src);
                break;
        }

        Image::create([
            'path' => '/images/' . $filename,
            'resolution' => $request->resolution,
            'watermark' => $request->watermark_text,
        ]);

        return response()
                ->json([
                    'success' => 'Image Uploaded Successfully',
                    'images' => Image::all()->sortByDesc('id')->toArray()
                ], 201);


    }
}
