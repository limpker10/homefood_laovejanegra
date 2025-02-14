<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{

    public function dashboardSales()
    {
        try {
            $topSales = DB::query()
                ->select('codigo_producto', 'nombre_producto', DB::raw('SUM(cantidad) as total_vendido'))
                ->from('prc_comprobante_detalle')
//                ->whereIn('id_estado_comprobante', [1, 4, 5]) falta , establecer la relacion de la tabla de comprobante detalle con comprobante para saber el estado del mismo
                ->groupBy('codigo_producto', 'nombre_producto')
                ->orderByDesc('total_vendido')
                ->limit(5)
                ->get();
            if ($topSales->isEmpty()) {
                return response()->json(['message' => 'No se encontraron ventas registradas'], 200);
            }

            return response()->json($topSales, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    public function dashboardCards(){
        try{
            $cards = DB::select("SELECT COUNT(IF(estado = 0, 1, NULL)) 'disponible' ,
            COUNT(IF(estado = 1, 1, NULL)) 'ocupado',
            COUNT(IF(estado = 2, 1, NULL)) 'limpieza',
            COUNT(IF(estado = 3, 1, NULL)) 'reserva'
            FROM mst_habitaciones");

            return response()->json($cards[0], 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    public function dashboardGraph(){
        try{
            $graph = DB::select("SELECT t1.month, ifnull(sum(t3.amount),0) AS expenses, ifnull(sum(t2.amount),0) AS incomes
            FROM
            (
                SELECT fechas.fecha AS month
                FROM (
                SELECT
                (DATE(NOW()) - INTERVAL 1 DAY) fecha UNION
                SELECT
                (DATE(NOW()) - INTERVAL 2 DAY) fecha UNION
                SELECT
                (DATE(NOW()) - INTERVAL 3 DAY) fecha UNION
                SELECT
                (DATE(NOW()) - INTERVAL 4 DAY) fecha UNION
                SELECT
                (DATE(NOW()) - INTERVAL 5 DAY) fecha UNION
                SELECT
                (DATE(NOW()) - INTERVAL 6 DAY) fecha UNION
                SELECT
                (DATE(NOW()) - INTERVAL 7 DAY) fecha UNION
                SELECT
                (DATE(NOW())) fecha) as fechas
            ) t1

            LEFT JOIN
            (
            SELECT DATE(c.`fecha_emision`) AS month, ifnull(sum(total),0) AS amount
            FROM `prc_comprobante` as c
            WHERE c.id_estado_comprobante IN (1,4,5)
            GROUP BY DATE(c.`fecha_emision`)
            ) t2
                ON t1.month = t2.month
            LEFT JOIN
            (
            SELECT DATE(e.`fecha_egreso`) AS month, ifnull(sum(monto),0)  AS amount
            FROM `prc_egresos` as e
            WHERE estado = 1
            GROUP BY DATE(e.`fecha_egreso`)
            ) t3
                ON t1.month = t3.month
            GROUP BY t1.month
            ORDER BY
                t1.month"
            );

            return response()->json($graph, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
