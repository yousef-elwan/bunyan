<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
// use Intervention\Image\ImageManager as Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait FileManagerTrait
{

    /**
     * @param string $dir
     * @param string $disk
     * @param $oldImage
     * @param string $format
     * @param $image
     * @param string $fileType image/file
     * @return string
     */
    public function updateFile(string $dir, $oldFile, $file, string $imageFormat = IMAGE_FORMATE, string $disk = "public"): string
    {
        if (Storage::disk($disk)->exists($dir . $oldFile)) {
            Storage::disk($disk)->delete($dir . $oldFile);
        }
        return $this->upload(dir: $dir, imageFormat: $imageFormat, file: $file, disk: $disk);
    }

    /**
     * @param string $dir
     * @param string $disk
     * @param $oldImage
     * @param string $format
     * @param $image
     * @param string $fileType image/file
     * @return string
     */
    public function upload(string $dir, $file, string $imageFormat = IMAGE_FORMATE, string $disk = "public"): string
    {
        $path = $dir;
        $fileExtension = $file->extension();
        // $fileExtension = $file->getClientOriginalExtension();
        $fileFormate =  $fileExtension;

        $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $fileFormate;

        if (!Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->makeDirectory($path);
        }

        if (in_array($fileExtension, ['jpg', 'jpeg', 'bmp', 'png'])) {

            $fileFormate = $imageFormat == null ? $fileExtension : $imageFormat;
            try {
                // create image manager with desired driver
                $manager = new ImageManager(new Driver());
                // read image from file system
                $image = $manager->read($file);
                $imageMedium =  $image->encodeByExtension($fileFormate, 30);
                // $imageMedium =  Image::make($file)->encode($fileFormate, 30);
                Storage::disk($disk)->put($path . $fileName, $imageMedium);
                // $imageMedium->destroy();
            } catch (\Throwable $th) {
                info('FileManagerTrait', [
                    'th' => $th->getMessage()
                ]);
                // $fileName = DEFAULT_IMAGE_NAME;
                // throw $th;
                Storage::disk('public')->put($path . $fileName, file_get_contents($file));
            }
        } else {
            Storage::disk($disk)->put($path . $fileName, file_get_contents($file));
        }
        return $fileName;
    }


    /**
     * @param string $filePath
     * @param string $disk
     * @return array
     */
    protected function  deleteFile(string $filePath, string $disk = "public"): array
    {
        if (Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }
        return [
            'success' => 1,
            'message' => trans('Removed_successfully')
        ];
    }
}
