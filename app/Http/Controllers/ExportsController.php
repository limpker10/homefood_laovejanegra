<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Exports\Reports\ComprobantesExport;
use App\Exports\Reports\RankingExport;

class ExportsController extends Controller
{
    public function exportarReporteComp($dat_export){
        $usuario_logeado = auth('sanctum')->user();
        $data = $dat_export;
        $data_json = json_decode($data,true);
        return Excel::download(new ComprobantesExport($data_json,$usuario_logeado->id_sucursal), 'ReporteComprobantes.xlsx');
    }

    public function exportarReporteRanking($dat_export){
        $usuario_logeado = auth('sanctum')->user();
        $data = $dat_export;
        $data_json = json_decode($data,true);
        return Excel::download(new RankingExport($data_json,$usuario_logeado->id_sucursal), 'ReporteRankingPlatos.xlsx');
    }

    public function exportarReporteStockInsumosProducto($dat_export){
        $usuario_logeado = auth('sanctum')->user();
        $data = $dat_export;
        $data_json = json_decode($data,true);
        return Excel::download(new ConsildadoExport($data_json,$usuario_logeado->id_sucursal), 'Reporte_Consolidado.xlsx');
    }

    public function exportarReporteConsolidado($dat_export){
        $usuario_logeado = auth('sanctum')->user();
        $data = $dat_export;
        $data_json = json_decode($data,true);
        return Excel::download(new ConsildadoExport($data_json,$usuario_logeado->id_sucursal), 'Reporte_Consolidado.xlsx');
    }

    public function exportarStockInsumos($dat_export){
        $usuario_logeado = auth('sanctum')->user();
        $data = $dat_export;
        $data_json = json_decode($data,true);
        return Excel::download(new StockInsumos($data_json,$usuario_logeado->id_sucursal), 'Stock_Insumos.xlsx');
    }

    public function exportarStockProductos($dat_export){
        $usuario_logeado = auth('sanctum')->user();
        $data = $dat_export;
        $data_json = json_decode($data,true);
        return Excel::download(new StockProductos($data_json,$usuario_logeado->id_sucursal), 'Stock_Productos.xlsx');
    }
}
