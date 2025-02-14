<?php

namespace App\Models;

use App\Models\Caja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;
    protected $table= 'prc_comprobante';
    protected $primaryKey= 'id_comprobante';
    protected $fillable = [
        'id_cliente',
        'id_tipo_comprobante',
        'id_usuario',
        'id_sucursal',
        'id_moneda',
        'id_estado_comprobante',
        'id_medio_pago',
        'id_serie',
        'correlativo',
        'nombre_cliente',
        'nro_documento',
        'direccion_cliente',
        'fecha_emision',
        'nro_operacion',
        'tipo_cambio',
        'comentario',
        'porcentaje_igv',
        'igv',
        'subtotal',
        'total',
        'comentario',
        'external_id',
        'formato_impresion',
        'fecha_anulacion',
        'motivacion_anulacion',
        'id_caja',
        'id_mozo',
        'id_mesa',
        'id_reserva',
        'total_efectivo',
        'total_vuelto',
        'total_dscto'
    ];
    public $timestamps = true;

    protected $with = array('usuario', 'sucursal', 'cliente','serie','medio_pago','tipo_comprobante', 'mozo','mesa');

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'id_usuario');
    }

    public function mozo(){
        return $this->belongsTo('App\Models\User', 'id_mozo', 'id');
    }

    public function mesa(){
        return $this->belongsTo('App\Models\Mesas', 'id_mesa');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    public function cliente(){
        return $this->belongsTo('App\Models\Clientes', 'id_cliente');
    }

    public function serie(){
        return $this->belongsTo('App\Models\Serie', 'id_serie');
    }

    public function medio_pago(){
        return $this->belongsTo('App\Models\MedioPago', 'id_medio_pago');
    }

    public function tipo_comprobante(){
        return $this->belongsTo('App\Models\TipoComprobantes', 'id_tipo_comprobante');
    }

    public static function normalizeRequest($request,$rubro){
        $usuario_logeado = auth('sanctum')->user();
        $request['id_usuario']=$usuario_logeado->id;
        $request['id_sucursal']=$usuario_logeado->id_sucursal;
        $request['id_moneda']=1;
        $request['porcentaje_igv']=10;
        $request['formato_impresion']='ticket'; // $request->id_serie

        if(strlen($request['nro_documento'])<3 || strlen($request['nombre_cliente'])<3){
            $request['id_cliente'] = 0;
            $request['nombre_cliente'] = "CLIENTES VARIOS";
            $request['nro_documento'] = "88888888";
            $request['direccion_cliente'] = "-";
        }

        //$last_inv = Comprobante::where("id_serie",$request['id_serie'])->orderByDesc('correlativo')->first();
        //$last_inv = Comprobante::where("id_serie",$request['id_serie'])->orderByRaw('CAST(correlativo AS INT) desc')->first();
        $last_inv = Comprobante::where("id_serie",$request['id_serie'])->orderByRaw('CAST(correlativo AS DECIMAL(11,2)) desc')->first();


        //orderByRaw('CONVERT(section, SIGNED) desc')
        if($last_inv != null){
            $request['correlativo'] = $last_inv->correlativo + 1;

        }else{
            $request['correlativo'] = 1;
        }

        $usuario_logeado = auth('sanctum')->user();
        $data = Caja::where('id_sucursal',$usuario_logeado->id_sucursal)->where('rubro', $rubro)->latest()->first();
        $request['id_caja'] = $data->id_caja;
        return $request;
    }
}
