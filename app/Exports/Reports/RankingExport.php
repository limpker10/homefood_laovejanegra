<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RankingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $data_export;
    protected $data_total;
    protected $id_sucursal;

    function __construct($data,$user) {
        $this->data_export = $data;
        $this->id_sucursal = $user;
    }

    public function headings(): array
    {
        return [
            'Código Producto',
            'Producto',
            'Categoría',
            'Cantidad Vendida',
            'Monto Total'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $usuario_logeado = auth('sanctum')->user();
        $datos = DB::table('prc_comprobante')
        ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
        ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
        ->where("prc_comprobante.id_estado_comprobante",[1,4,5])
        ->join('prc_comprobante_detalle', 'prc_comprobante.id_comprobante', '=', 'prc_comprobante_detalle.id_comprobante')
        ->join('mst_producto', 'prc_comprobante_detalle.id_producto', '=', 'mst_producto.id_producto')
        ->join('mst_categorias', 'mst_producto.id_categoria', '=', 'mst_categorias.id_categoria')

        ->select(
        DB::raw('mst_producto.codigo_producto as codigo_producto'),
        DB::raw('prc_comprobante_detalle.nombre_producto AS nombre_producto'),
        DB::raw('mst_categorias.nombre as nombre_categoria'), 
        DB::raw('SUM(prc_comprobante_detalle.cantidad) as cantidad'),
        DB::raw('SUM(prc_comprobante_detalle.precio_total) as precio_total'))
        ->groupBy('prc_comprobante_detalle.nombre_producto')
        ->orderBy('cantidad', 'desc')
        ->get();

        return $datos;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $bold_tittle=['font' => ['bold' => true]];
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->applyFromArray($bold_tittle);

                $event->sheet->getStyle('D')->getNumberFormat()->setFormatCode('0.00'); 
                $event->sheet->getStyle('E')->getNumberFormat()->setFormatCode('0.00');
            },
        ];
    }
}
