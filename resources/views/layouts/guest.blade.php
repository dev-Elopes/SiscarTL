<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F4F5F0]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AutoCare') }} - Acessar Garagem</title>

        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles via Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-['Inter',sans-serif] text-[#1F2937] antialiased bg-[#F1F1EF] bg-no-repeat bg-cover bg-center lg:bg-[right_center] flex items-center justify-center lg:justify-start lg:pl-16 xl:pl-28 p-4 sm:p-6 relative selection:bg-[#E11D48] selection:text-white"
          style="background-image: url('{{ asset('images/auth-blueprint-bg.jpg') }}'); background-color: #F1F1EF;">
        
        <!-- Camada sutil para telas menores (garantindo legibilidade em mobile) -->
        <div class="fixed inset-0 bg-[#F1F1EF]/60 lg:hidden pointer-events-none"></div>

        <!-- Conteúdo do Formulário (Login / Cadastro) -->
        <div class="relative z-10 w-full max-w-[440px]">
            {{ $slot }}
        </div>
    </body>
</html>
