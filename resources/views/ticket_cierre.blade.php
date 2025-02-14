
@php
    $fecha_apertura = explode(' ',$caja->fecha_apertura)[0];
    $fecha_cierre = $caja->fecha_apertura;
@endphp
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Cierre de Caja</title>
    <link href="{{asset('css/style.css')}}" rel="stylesheet" />
    <link href="{{asset('css/ticket.css')}}" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>Cierre de Caja</title>
</head>
<body>
    <div class="ticket">
        
        <p class="centered">
            <br><strong>{{$caja->sucursal->nombre_sucursal}}</strong>
            <br><span class="centered">Caja {{ \Carbon\Carbon::parse($fecha_apertura)->format('d/m/Y') }}</span>
            <br>
        </p>
        <p>
            <span>Usuario: {{$caja->usuario["name"] }}</span>
            <br>
            <span>Fecha Apertura: {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y h:m A') }}</span>
            <br>
            <span>Fecha Cierre: {{ \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y h:m A') }}</span>
        </p>
        	
        <table>
            <thead>
                <tr >
                    <th class="both_border" style="width: 50mm;">Tipo.</th>
                    <th class="both_border" style="width: 20mm;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($document as $item)
                <tr>
                    <td style="width: 50mm; font-size: 12px;">{{$item->medio_pago}} {{$item->codigo_sunat}}</td>
                    @if ($item->medio_pago == 'EGRESOS')
                    <td style="width: 20mm; font-size: 12px; text-align:left;">S/. -{{$item->total_comprobante}}</td>
                    @else
                    <td style="width: 20mm; font-size: 12px; text-align:left;">S/. {{$item->total_comprobante}}</td>
                    @endif
                </tr>
                @endforeach
                <tr>
                    <td style="width: 50mm; font-size: 12px;"><strong>TOTAL EFECTIVO</strong></td>
                    <td style="width: 20mm; font-size: 12px; text-align:left;"><strong>S/. {{number_format($total_efectivo,2)}}</strong></td>
                </tr>
            </tbody>
        </table>
        
    </div>
    <br>
    <div class="ticket">
        <table>
            <thead>
                <tr >
                    <th class="both_border" style="width: 50mm;">Cliente.</th>
                    <th class="both_border" style="width: 20mm;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detail as $item)
                <tr>
                    <td style="width: 50mm; font-size: 12px;">{{$item->nombre_cliente}}</td>
                    <td style="width: 20mm; font-size: 12px; text-align:left;">S/. {{$item->total}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br>
    <div class="ticket">
        <table>
            <thead>
                <tr >
                    <th class="both_border" style="width: 50mm;">Egreso.</th>
                    <th class="both_border" style="width: 20mm;">Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($egresos as $item)
                <tr>
                    <td style="width: 50mm; font-size: 12px;">{{$item->detalle}}</td>
                    <td style="width: 20mm; font-size: 12px; text-align:left;">S/. {{$item->monto}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <br>
    <div class="ticket">
        <table>
            <thead>
                <tr >
                    <th class="both_border" style="width: 50mm;">Productos Vendidos.</th>
                    <th class="both_border" style="width: 20mm;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $item)
                <tr>
                    <td style="width: 50mm; font-size: 12px;">{{$item->nombre_producto}}</td>
                    <td style="width: 20mm; font-size: 12px; text-align:left;">{{$item->cantidad}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>