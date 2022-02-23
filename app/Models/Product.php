<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'description',
        'time',
        'station',
        'user_id',
    ];
    
    public function getImagesAttribute()
    {
        $files = $this->getMedia('photos');
        $photos = []; 
        foreach ($files as $file) {
            $temp['url'] = $file->getUrl();
            $temp['name'] = $file->file_name;
            $photos[] = $temp;
        }
        return $photos;
    }

    public function getPhotosAttribute()
    {
        $files = $this->getMedia('photos');
        $files->each(function ($item) {
            $item->url = $item->getUrl();
        });

        return $files;
    }

    public function getThumbnailAttribute()
    {
        return $this->getFirstMediaUrl('photos');
    }

    public function owner()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
