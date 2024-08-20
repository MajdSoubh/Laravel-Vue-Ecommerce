<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait ImageHelper
{
    public function saveImages(Model $model, $images)
    {
        foreach ($images as $image)
        {

            $path = Storage::putFile('images', $image);
            $model->images()->save(new Image(['path' => $path, 'name' => $image->getClientOriginalName()]));
        }
    }
    public function deleteImages(Model $model)
    {
        $images = $model->images;
        foreach ($images as $image)
        {
            Storage::delete($image->path);
            $image->delete();
        }
    }
}
