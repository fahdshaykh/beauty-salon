<!DOCTYPE html>



<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">



<head>



    <meta charset="utf-8">



    <meta name="viewport" content="width=device-width, initial-scale=1">






    <!-- CSRF Token -->

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">







    <title>Optout | DiaryMaster</title>







    <!-- Scripts -->



    <script src="{{ asset('js/app.js') }}" defer></script>







    <!-- Fonts -->



    <link rel="dns-prefetch" href="//fonts.gstatic.com">



    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">







    <!-- Styles -->



</head>



<body>



    <div id="app">



        <main>



            @yield('content')



        </main>



        



    </div>



</body>



</html>



