<?php

namespace App\Exports\Reports;

use App\Comprobante;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ComprobantesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
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
            'Tipo Comprobante',
            'Serie',
            'Correlativo',
            'Fecha emisión',
            'Cliente',
            'Documento',
            'Moneda',
            'Estado',
            'Total Gravado',
            'Total IGV',
            'Total',
        ];
    }

    public function collection()
    {
        /*$datos = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal);
            if(!empty($this->data_export["id_comprobante"])){
                $datos->whereIn("prc_comprobante.id_tipo_comprobante", $this->data_export["id_comprobante"]);
            }
            //if(!empty($this->data_export["id_estado"])){
                //->whereIn("prc_comprobante.id_estado_comprobante", $this->data_export["id_estado"])
            //}
            $datos->join('mst_tipos_comprobante', 'prc_comprobante.id_tipo_comprobante', '=', 'mst_tipos_comprobante.id_tipo_comprobante')
            ->join('mst_series', 'prc_comprobante.id_serie', '=', 'mst_series.id_serie')
            ->join('mst_estado_comprobante', 'prc_comprobante.id_estado_comprobante', '=', 'mst_estado_comprobante.id_estado_comprobante')
            ->join('mst_monedas', 'prc_comprobante.id_moneda', '=', 'mst_monedas.id_moneda')
            ->select('mst_tipos_comprobante.tipo_comprobante','mst_series.serie', 'prc_comprobante.correlativo','prc_comprobante.created_at','prc_comprobante.nombre_cliente', 'prc_comprobante.nro_documento', 'mst_monedas.nombre', 'mst_estado_comprobante.id_estado_comprobante', 'prc_comprobante.subtotal', 'prc_comprobante.igv', 'prc_comprobante.total');
            $datos->get();

            $this->data_total = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            //if(!empty($this->data_export["id_comprobante"])){
            ->whereIn("prc_comprobante.id_tipo_comprobante", $this->data_export["id_comprobante"])
            //}
            //if(!empty($this->data_export["id_estado"])){
            ->whereIn("prc_comprobante.id_estado_comprobante", $this->data_export["id_estado"])
            //}
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->select(DB::raw('SUM(igv) as igv'),DB::raw('SUM(subtotal) as subtotal'),DB::raw('SUM(total) as total'))
            ->first();

            return $datos;*/

        if(($this->data_export["fecha_inicio"]!="") && ($this->data_export["fecha_fin"]!="") && (empty($this->data_export["id_comprobante"])) && (empty($this->data_export["id_estado"]))){
            $datos = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->join('mst_tipos_comprobante', 'prc_comprobante.id_tipo_comprobante', '=', 'mst_tipos_comprobante.id_tipo_comprobante')
            ->join('mst_series', 'prc_comprobante.id_serie', '=', 'mst_series.id_serie')
            ->join('mst_estado_comprobante', 'prc_comprobante.id_estado_comprobante', '=', 'mst_estado_comprobante.id_estado_comprobante')
            ->join('mst_monedas', 'prc_comprobante.id_moneda', '=', 'mst_monedas.id_moneda')
            ->select('mst_tipos_comprobante.tipo_comprobante','mst_series.serie', 'prc_comprobante.correlativo','prc_comprobante.created_at','prc_comprobante.nombre_cliente', 'prc_comprobante.nro_documento', 'mst_monedas.nombre', 'mst_estado_comprobante.id_estado_comprobante', 'prc_comprobante.subtotal', 'prc_comprobante.igv', 'prc_comprobante.total')
            ->get();

            $this->data_total = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->select(DB::raw('SUM(igv) as igv'),DB::raw('SUM(subtotal) as subtotal'),DB::raw('SUM(total) as total'))
            ->first();

            return $datos;

        }
        if(($this->data_export["fecha_inicio"]!="") && ($this->data_export["fecha_fin"]!="") && (!empty($this->data_export["id_comprobante"])) && (empty($this->data_export["id_estado"]))){
            $datos = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->whereIn("prc_comprobante.id_tipo_comprobante", $this->data_export["id_comprobante"])
            ->join('mst_tipos_comprobante', 'prc_comprobante.id_tipo_comprobante', '=', 'mst_tipos_comprobante.id_tipo_comprobante')
            ->join('mst_series', 'prc_comprobante.id_serie', '=', 'mst_series.id_serie')
            ->join('mst_estado_comprobante', 'prc_comprobante.id_estado_comprobante', '=', 'mst_estado_comprobante.id_estado_comprobante')
            ->join('mst_monedas', 'prc_comprobante.id_moneda', '=', 'mst_monedas.id_moneda')
            ->select('mst_tipos_comprobante.tipo_comprobante','mst_series.serie', 'prc_comprobante.correlativo','prc_comprobante.created_at','prc_comprobante.nombre_cliente', 'prc_comprobante.nro_documento', 'mst_monedas.nombre', 'mst_estado_comprobante.id_estado_comprobante', 'prc_comprobante.subtotal', 'prc_comprobante.igv', 'prc_comprobante.total')
            ->get();

            $this->data_total = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->whereIn("prc_comprobante.id_tipo_comprobante", $this->data_export["id_comprobante"])
            ->select(DB::raw('SUM(igv) as igv'),DB::raw('SUM(subtotal) as subtotal'),DB::raw('SUM(total) as total'))
            ->first();

            return $datos;
        }
        if(($this->data_export["fecha_inicio"]!="") && ($this->data_export["fecha_fin"]!="") && (!empty($this->data_export["id_comprobante"])) && (!empty($this->data_export["id_estado"]))){
            $datos = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->whereIn("prc_comprobante.id_tipo_comprobante", $this->data_export["id_comprobante"])
            ->whereIn("prc_comprobante.id_estado_comprobante", $this->data_export["id_estado"])
            ->join('mst_tipos_comprobante', 'prc_comprobante.id_tipo_comprobante', '=', 'mst_tipos_comprobante.id_tipo_comprobante')
            ->join('mst_series', 'prc_comprobante.id_serie', '=', 'mst_series.id_serie')
            ->join('mst_estado_comprobante', 'prc_comprobante.id_estado_comprobante', '=', 'mst_estado_comprobante.id_estado_comprobante')
            ->join('mst_monedas', 'prc_comprobante.id_moneda', '=', 'mst_monedas.id_moneda')
            ->select('mst_tipos_comprobante.tipo_comprobante','mst_series.serie', 'prc_comprobante.correlativo','prc_comprobante.created_at','prc_comprobante.nombre_cliente', 'prc_comprobante.nro_documento', 'mst_monedas.nombre', 'mst_estado_comprobante.id_estado_comprobante', 'prc_comprobante.subtotal', 'prc_comprobante.igv', 'prc_comprobante.total')
            ->get();

            $this->data_total = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->whereIn("prc_comprobante.id_tipo_comprobante", $this->data_export["id_comprobante"])
            ->whereIn("prc_comprobante.id_estado_comprobante", $this->data_export["id_estado"])
            ->select(DB::raw('SUM(igv) as igv'),DB::raw('SUM(subtotal) as subtotal'),DB::raw('SUM(total) as total'))
            ->first();

            return $datos;
        }
        if(($this->data_export["fecha_inicio"]!="") && ($this->data_export["fecha_fin"]!="") && (!empty($this->data_export["id_comprobante"])) && (empty($this->data_export["id_estado"])) ){
            $datos = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->whereIn("prc_comprobante.id_estado_comprobante", $this->data_export["id_estado"])
            ->join('mst_tipos_comprobante', 'prc_comprobante.id_tipo_comprobante', '=', 'mst_tipos_comprobante.id_tipo_comprobante')
            ->join('mst_series', 'prc_comprobante.id_serie', '=', 'mst_series.id_serie')
            ->join('mst_estado_comprobante', 'prc_comprobante.id_estado_comprobante', '=', 'mst_estado_comprobante.id_estado_comprobante')
            ->join('mst_monedas', 'prc_comprobante.id_moneda', '=', 'mst_monedas.id_moneda')
            ->select('mst_tipos_comprobante.tipo_comprobante','mst_series.serie', 'prc_comprobante.correlativo','prc_comprobante.created_at','prc_comprobante.nombre_cliente', 'prc_comprobante.nro_documento', 'mst_monedas.nombre', 'mst_estado_comprobante.id_estado_comprobante', 'prc_comprobante.subtotal', 'prc_comprobante.igv', 'prc_comprobante.total')
            ->get();
            
            $this->data_total = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$this->data_export["fecha_inicio"]." 00:00:00", $this->data_export["fecha_fin"]." 23:59:59"])
            ->where("prc_comprobante.id_sucursal",$this->id_sucursal)
            ->whereIn("prc_comprobante.id_estado_comprobante", $this->data_export["id_estado"])
            ->select(DB::raw('SUM(igv) as igv'),DB::raw('SUM(subtotal) as subtotal'),DB::raw('SUM(total) as total'))
            ->first();

            return $datos;
        }
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $bold_tittle=['font' => ['bold' => true]];
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->applyFromArray($bold_tittle);
                $event->sheet->getStyle($cellRange)->applyFromArray($bold_tittle);
                $event->sheet->mergeCells('Q4:T4');
                $event->sheet->mergeCells('Q5:T6');
                $event->sheet->mergeCells('Q7:R7');
                $event->sheet->mergeCells('Q8:R8');
                $event->sheet->mergeCells('Q9:R9');
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '#000000'],
                        ],
                    ],
                ];
                $event->sheet->getStyle('Q4:T9')->applyFromArray($styleArray);
                $event->sheet->setCellValueExplicit(
                    'Q4','Cálculo de Subtotal e IGV',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                $event->sheet->getStyle('Q4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->setCellValueExplicit(
                    'Q7','Subtotal (Gravado)',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                
                $event->sheet->setCellValueExplicit('Q8','IGV',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $event->sheet->getStyle('Q7')->applyFromArray($bold_tittle);
                $event->sheet->getStyle('Q8')->applyFromArray($bold_tittle);
                $event->sheet->getStyle('Q9')->applyFromArray($bold_tittle);
                $event->sheet->getStyle('T7')->getNumberFormat()->setFormatCode('0.00'); 
                $event->sheet->getStyle('T8')->getNumberFormat()->setFormatCode('0.00'); 
                $event->sheet->getStyle('T9')->getNumberFormat()->setFormatCode('0.00');
                $event->sheet->getStyle('I')->getNumberFormat()->setFormatCode('0.00'); 
                $event->sheet->getStyle('J')->getNumberFormat()->setFormatCode('0.00'); 
                $event->sheet->getStyle('K')->getNumberFormat()->setFormatCode('0.00');
                
                $event->sheet->setCellValueExplicit('Q9','Total Comprobante',\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $event->sheet->setCellValueExplicit('T9',$this->data_total->total, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $event->sheet->setCellValueExplicit('T8',$this->data_total->igv, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $event->sheet->setCellValueExplicit('T7',$this->data_total->subtotal, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                
            },
        ];
    }
}
