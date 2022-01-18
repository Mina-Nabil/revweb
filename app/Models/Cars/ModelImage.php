<?php

namespace App\Models\Cars;

use App\Services\FilesHandler;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModelImage extends Model
{
    protected $table = "model_images";
    public $timestamps = false;
    protected $appends = ["image_url"];

    public $fillable = [
        "MOIM_URL", "MOIM_SORT"
    ];

    public function getImageUrlAttribute()
    {
        return isset($this->MOIM_URL) ? Storage::url($this->MOIM_URL) : null;
    }

    public function deleteImage()
    {
        try {
            $filesHandler = new FilesHandler();
            return $filesHandler->deleteFile($this->MOIM_URL) && $this->delete();
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB" => self::class]);
            throw $e;
        }
    }

    public function editInfo($sort, $imageURL)
    {
        try {
            return $this->update([
                "MOIM_SORT" => $sort,
                "MOIM_URL" => $imageURL ?? $this->MOIM_URL,
            ]);
        } catch (Exception $e) {
            Log::alert($e->getMessage(), ["DB", self::class]);
            return false;
        }
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, "MOIM_MODL_ID");
    }

    // public function compress()
    // {
    //     $quality = 40;
    //     $ext = last(explode('.', $this->MOIM_URL));
    //     $fileNoExt = str_replace('.' . $ext, '', $this->MOIM_URL);
    //     $imagePath = public_path('storage/' . $this->MOIM_URL);
    //     $newImagePath =  $fileNoExt . '_' . $quality . '.' . $ext;
    //     echo "Extension: " . $ext . "\n";
    //     echo "FileNoExt: " . $fileNoExt . "\n";
    //     echo "Path: " . $imagePath . "\n";
    //     echo "New Path: " . $newImagePath . "\n";
    //     if ($ext == 'png') {
    //         try {
    //             $image = imagecreatefrompng($imagePath);
    //             imagejpeg($image, public_path('storage/' . $newImagePath), $quality);
    //             $this->MOIM_URL = $newImagePath;
    //             $this->save();
    //             unlink($imagePath);
    //         } catch (Exception $e) {
    //             echo "Something went wrong here \n";
    //             echo $e->getMessage();
    //             echo "\n";
    //         }
    //     } else if ($ext == 'jpg' || $ext == 'jpeg') {
    //         $image = self::imagecreatefromjpegexif($imagePath);
    //         try {
    //             imagejpeg($image, public_path('storage/' . $newImagePath), $quality);
    //             $this->MOIM_URL = $newImagePath;
    //             $this->save();
    //             unlink($imagePath);
    //         } catch (Exception $e) {
    //             echo "Something went wrong here \n";
    //             echo $e->getMessage();
    //             echo "\n";
    //         }
    //     }
    // }

    // private static function imagecreatefromjpegexif($filename)
    // {
    //     $img = imagecreatefromjpeg($filename);
    //     $exif = exif_read_data($filename);
    //     echo "size before: ";
    //     echo $exif['FileSize'] . "\n";

    //     if ($img && $exif && isset($exif['Orientation'])) {
    //         $ort = $exif['Orientation'];

    //         if ($ort == 6 || $ort == 5)
    //             $img = imagerotate($img, 270, null);
    //         if ($ort == 3 || $ort == 4)
    //             $img = imagerotate($img, 180, null);
    //         if ($ort == 8 || $ort == 7)
    //             $img = imagerotate($img, 90, null);

    //         if ($ort == 5 || $ort == 4 || $ort == 7)
    //             imageflip($img, IMG_FLIP_HORIZONTAL);
    //     }
    //     return $img;
    // }


}
