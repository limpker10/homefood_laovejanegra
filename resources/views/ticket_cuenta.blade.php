
@php
$establishment = $document->sucursal->razon_social_sucursal;
$direccion_fiscal = $document->sucursal->direccion_fiscal;
$ruc_sucursal = $document->sucursal->ruc_sucursal;
$branch = $document->sucursal;
$customer = $document->cliente;
$waiter = $document->usuario;
//$table = $mesa->nro_mesa;
$table = $mesa;
$emision_date = explode(" ", $document->created_at);
$sumaTotal  = 0;
foreach ($detail as $val) {
   $sumaTotal = $sumaTotal+($val->cantidad * $val->precio);
}
$subtotal = round(($sumaTotal/1.18), 2);
$igv = round(($sumaTotal - $subtotal), 2);
$total = number_format((float) (round($sumaTotal, 2)), 2, '.', '');

@endphp
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Ticket de Mesa</title>
    <link href="{{asset('css/style.css')}}" rel="stylesheet" />
    <link href="{{asset('css/ticket.css')}}" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Ticket de Mesa</title>
</head>
<body>
    <div class="ticket">
        
        <p class="centered">
            <br><strong>{{$establishment}}</strong>
            <br><span class="centered address">{{$direccion_fiscal}}</span>
        </p>
        <p class="centered">
            <span class="ruc">RUC {{$ruc_sucursal}}</span>
        </p>
        <p>
            Atendido por: {{$waiter->name}} / {{$table}} {{$zona}}
        </p>	
        <table>
            <thead>
                <tr >
                    <th class="quantity both_border">Cant.</th>
                    <th class="description both_border">Producto</th>
                    <th class="price both_border">P.U</th>
                    <th class="price both_border">P.T</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $item)
                <tr>
                    <td class="quantity">{{$item->cantidad}}</td>
                    <td class="description">{{$item->producto->nombre_producto}}</td>
                    <td class="price">S/. {{$item->precio}}</td>
                    <td class="price">S/. {{number_format((float) ($item->cantidad * $item->precio), 2, '.', '')}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="margin-top: 15px">
                <tr>
                    <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                    <td colspan="2" class="price">S/. {{$subtotal}}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;"><b>IGV (18%): </b></td>
                    <td colspan="2"  class="price">S/. {{$igv}}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                    <td colspan="2"  class="price">S/. {{$total}}</td>
                </tr>
            </tfoot>

        </table>
    </div>
</body>
</html>