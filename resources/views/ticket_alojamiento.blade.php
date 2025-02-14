
@php
$establishment = $document->sucursal->razon_social_sucursal;
$direccion_fiscal = $document->sucursal->direccion_fiscal;
$ruc_sucursal = $document->sucursal->ruc_sucursal;
$branch = $document->sucursal;
$customer = $document->cliente;
$document_name = $document->tipo_comprobante->tipo_comprobante;
$document_number = $document->serie->serie.'-'.str_pad($document->correlativo,8,'0',STR_PAD_LEFT);
$emision_date = explode(" ", $document->created_at);
$medio_pago = $document->medio_pago;
@endphp
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>{{$document_number}}</title>
    <link href="{{asset('css/style.css')}}" rel="stylesheet" />
    <link href="{{asset('css/ticket.css')}}" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>{{$document_number}}</title>
</head>
<body>
    <div class="ticket">
        
        <p class="centered">
            <br><strong>{{$establishment}}</strong>
            <br><span class="centered address">{{$direccion_fiscal}}</span>
        </p>
        <p class="centered">
            <span class="ruc">RUC {{$ruc_sucursal}}</span>
            <br>
            <strong>{{$document_name}}</strong>
            <br>
            <strong>{{$document_number}}</strong>
        </p>

        <p>
            Fecha: {{ \Carbon\Carbon::parse($document->created_at)->format('d/m/Y H:i:s') }}<br>
            Cliente: {{$document->nombre_cliente}}<br>
            Dirección: {{$document->direccion_cliente}}
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
                    <td class="description">{{$item->nombre_producto}}</td>
                    <td class="price">S/. {{$item->precio_unitario}}</td>
                    <td class="price">S/. {{$item->precio_total}}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="margin-top: 15px">
                <tr>
                    <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                    <td colspan="2" class="price">S/. {{$document->subtotal}}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;"><b>IGV (18%): </b></td>
                    <td colspan="2"  class="price">S/. {{$document->igv}}</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                    <td colspan="2"  class="price">S/. {{$document->total}}</td>
                </tr>
            </tfoot>

        </table>
        <br>
        <hr>
        <p style="font-size: 12px; margin-top: -5px">
            <strong>Forma de Pago:</strong>
            <br>
            <span>{{$medio_pago->medio_pago}}</span>
        </p>
        <p style="font-size: 12px; margin-top: -5px">
            <strong>Comentario:</strong>
            <br>
            <span>{{$document->comentario}}</span>
        </p>
    </div>
</body>
</html>