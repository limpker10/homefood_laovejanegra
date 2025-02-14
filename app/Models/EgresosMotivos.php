<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EgresosMotivos extends Model
{
    use HasFactory;
    protected $table= 'mst_egreso_motivos';
    protected $primaryKey= 'id_egreso_motivo';
    protected $fillable = [
        'motivo',
        'estado'
    ];
    public $timestamps = false;

    public function remove()
    {   
        try{
            $this->update(['estado'=>0]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }  
    }
}
