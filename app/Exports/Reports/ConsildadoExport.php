<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ConsildadoExport implements FromCollection, ShouldAutoSize, WithEvents
{
    protected $data_export;
    protected $data_total;
    protected $id_sucursal;

    function __construct($data,$user) {
        $this->data_export = $data;
        $this->id_sucursal = $user;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = DB::select("
        SELECT 'CÓDIGO','PRODUCTO','CATEGORÍA','CANTIDAD','MONTO TOTAL'
        
        UNION ALL
        
        SELECT 
        mst_producto.codigo_producto as codigo_producto,
        comprobante_detalle.nombre_producto AS nombre_producto,
        mst_categorias.nombre as nombre_categoria, 
        SUM(comprobante_detalle.cantidad) as cantidad,
        SUM(comprobante_detalle.precio_unitario * comprobante_detalle.cantidad) as precio_total
        FROM comprobante
        INNER JOIN comprobante_detalle ON comprobante.id_comprobante = comprobante_detalle.id_comprobante
        INNER JOIN mst_producto ON comprobante_detalle.id_producto = mst_producto.id_producto
        INNER JOIN mst_categorias ON mst_producto.id_categoria = mst_categorias.id_categoria
        WHERE DATE(comprobante.created_at) BETWEEN '".$this->data_export["fecha_inicio"]."' AND '".$this->data_export["fecha_fin"]."' AND
        comprobante.id_estado_comprobante = 1
        GROUP BY comprobante_detalle.nombre_producto
        UNION ALL
        
        SELECT '','','','',''

        UNION ALL
        
        SELECT 'EGRESOS','','','',''
        
        UNION ALL
        
        SELECT egresos.detalle,'','','',egresos.monto
        FROM egresos
        WHERE DATE(egresos.fecha_egreso) BETWEEN '".$this->data_export["fecha_inicio"]."' AND '".$this->data_export["fecha_fin"]."'
        
        UNION ALL
        
        SELECT '','','','',''

        UNION ALL
        
        SELECT 'COMPRAS','','','',''
        
        UNION ALL
        
        SELECT '','','','',''

        UNION ALL
        
        SELECT 'CÓDIGO','PRODUCTO/INSUMO','CATEGORÍA','CANTIDAD','MONTO TOTAL'
        
        UNION ALL
        
        SELECT 
        IFNULL(mst_producto.codigo_producto, mst_insumos.codigo_insumo) as codigo_producto,
        compras_detalle.nombre_producto_insumo AS nombre_producto_insumo, 
        IFNULL(mcp.nombre, mci.nombre) as nombre_categoria,
        SUM(compras_detalle.cantidad) as cantidad,
        SUM(compras_detalle.precio_compra_unitario) as precio_total
        FROM compras
        INNER JOIN compras_detalle ON compras.id_compra = compras_detalle.id_compra
        LEFT JOIN mst_producto ON compras_detalle.id_producto = mst_producto.id_producto
        LEFT JOIN mst_insumos ON compras_detalle.id_insumo = mst_insumos.id_insumo
        LEFT JOIN mst_categorias AS mcp ON mst_producto.id_categoria = mcp.id_categoria
        LEFT JOIN mst_categorias AS mci ON mst_insumos.id_categoria = mci.id_categoria
        WHERE DATE(compras.created_at) BETWEEN '".$this->data_export["fecha_inicio"]."' AND '".$this->data_export["fecha_fin"]."'
        GROUP BY compras_detalle.nombre_producto_insumo");
        return collect($data);
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
