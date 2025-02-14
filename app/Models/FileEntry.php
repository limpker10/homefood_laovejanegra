<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileEntry extends Model
{
    use HasFactory;
    protected $table= 'mst_images_productos';
    protected $primaryKey= 'id_image';
    protected $fillable = [
        'id_producto',
        'section',
        'filename',
        'mime', 
        'path', 
        'size', 
    ];
    public $timestamps = true;
}
