<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Administrador</title>
        <link rel="icon" href="assets/images/favicon.ico">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <script src="{{ mix('js/app.js') }}" defer></script>
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body class="antialiased">
        <div id="app">
            <my-app></my-app>
        </div>
    </body>
</html>


<style>
    ::-webkit-scrollbar {
      width: 10px !important;
    }
    
    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1 !important; 
   }
     
    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: rgb(167, 167, 167) !important;
        border-radius: 10px;
    }
    
    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: rgb(129, 129, 129) !important; 
    }
    </style>