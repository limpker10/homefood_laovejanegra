<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumentos extends Model
{
    use HasFactory;
    protected $table = 'mst_tipos_documento';
    protected $primaryKey = 'id_tipo_doc';
    protected $fillable = [
        'tipo_documento',
        'codigo_sunat',
        'caracteres'
    ];

    /*OBJECT FUNCTIONS*/

    public function remove()
    {   
        try{
            $this->update(['estado'=>0]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }  
    }

}
