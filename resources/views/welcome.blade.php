<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antonio Polo S.A. - Sistema de Gestión de Transporte con Geolocalización</title>
    <meta name="description" content="Compañía de Transporte Mixto Antonio Polo S.A. — Servicio entre Salinas y Guaranda con gestión digital y geolocalización en tiempo real.">
    <meta name="author" content="Universidad Estatal de Bolívar">
    <link rel="icon" href="{{ asset('images/logo-antonio-polo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" integrity="sha512-/r8fwbL9BnJ+NIWVyJ7NJQpZmSsk4Lq5LWP3I+7Qn/SpJCUDPiS0pLHaNEK9jBuLiTPLJw==" crossorigin="anonymous">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        html { scroll-behavior: smooth; }
        body { margin: 0; padding: 0; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #059669; border-radius: 4px; }
        [x-cloak] { display: none !important; }
        .map-container { position: relative; z-index: 1; }
        .map-container .leaflet-container { position: relative; z-index: 1; }
        .leaflet-control-zoom { z-index: 1 !important; }
        .leaflet-control-attribution { z-index: 1 !important; }
        .leaflet-popup { z-index: 2 !important; }
        .leaflet-container { font-family: 'Poppins', sans-serif !important; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-white">

    {{-- NAVBAR --}}
    <header role="banner">
        <nav role="navigation" aria-label="Navegación principal"
            x-data="{ scrolled: false, mobileMenu: false }"
            @scroll.window="scrolled = window.scrollY > 20"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-emerald-800 shadow-lg"
            :class="scrolled ? 'py-2' : 'py-2'"
        >
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <a href="#inicio" class="flex items-center gap-2 sm:gap-3" aria-label="Ir al inicio">
                        <img class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl object-contain" 
                             src="{{ asset('images/logo-antonio-polo.png') }}" 
                             alt="Logo de Antonio Polo S.A."
                             width="48" height="48">
                        <span class="hidden sm:block">
                            <span class="block font-bold text-white text-sm leading-tight">Antonio Polo S.A.</span>
                            <span class="block text-emerald-300 text-xs">Transporte Mixto</span>
                        </span>
                    </a>

                    {{-- Desktop Menu --}}
                    <div class="hidden lg:flex items-center gap-2">
                        <a href="#inicio" class="px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg text-sm font-medium transition-colors">Inicio</a>
                        <a href="#nosotros" class="px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg text-sm font-medium transition-colors">Nosotros</a>
                        <a href="#mision-vision" class="px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg text-sm font-medium transition-colors">Misión y Visión</a>
                        <a href="#funciones" class="px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg text-sm font-medium transition-colors">Funciones</a>
                        <a href="#ruta" class="px-3 py-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg text-sm font-medium transition-colors">Ruta</a>
                        
                        <a href="https://wa.me/593999999999" target="_blank" rel="noopener noreferrer"
                            class="ml-4 flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-semibold transition-colors"
                            aria-label="Contactar por WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" 
                                class="ml-2 px-5 py-2.5 bg-white text-emerald-800 font-bold rounded-xl hover:bg-emerald-50 transition-colors text-sm flex items-center gap-2"
                                aria-label="Ingresar al sistema">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Ingresar
                            </a>
                        @endif
                    </div>

                    {{-- Mobile Menu Button --}}
                    <button @click="mobileMenu = !mobileMenu" 
                            class="lg:hidden p-2 text-white rounded-xl hover:bg-white/10 transition-colors"
                            :aria-expanded="mobileMenu"
                            aria-controls="mobile-menu"
                            aria-label="Abrir menú de navegación">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Mobile Menu --}}
                <div id="mobile-menu" x-show="mobileMenu" x-cloak x-transition
                    class="lg:hidden absolute top-full left-0 right-0 bg-emerald-800 pb-4 shadow-lg"
                    @click.away="mobileMenu = false">
                    <nav class="flex flex-col gap-1 px-2 py-2" role="menu">
                        <a href="#inicio" role="menuitem" class="px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-colors flex items-center gap-3" @click="mobileMenu = false">Inicio</a>
                        <a href="#nosotros" role="menuitem" class="px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-colors flex items-center gap-3" @click="mobileMenu = false">Nosotros</a>
                        <a href="#mision-vision" role="menuitem" class="px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-colors flex items-center gap-3" @click="mobileMenu = false">Misión y Visión</a>
                        <a href="#funciones" role="menuitem" class="px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-colors flex items-center gap-3" @click="mobileMenu = false">Funciones</a>
                        <a href="#ruta" role="menuitem" class="px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-xl transition-colors flex items-center gap-3" @click="mobileMenu = false">Ruta</a>
                        
                        <hr class="border-white/10 my-2">
                        
                        <a href="https://wa.me/593999999999" target="_blank" rel="noopener noreferrer"
                            class="mx-2 px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl text-center font-semibold transition-colors"
                            @click="mobileMenu = false">
                            Contactar por WhatsApp
                        </a>
                        
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" 
                                class="mx-2 mt-2 px-4 py-3 bg-white text-emerald-800 font-bold rounded-xl text-center transition-colors"
                                @click="mobileMenu = false">
                                Ingresar al Sistema
                            </a>
                        @endif
                    </nav>
                </div>
            </div>
        </nav>
    </header>

    <main role="main">
        {{-- HERO SECTION --}}
        <section id="inicio" aria-labelledby="hero-title" class="relative min-h-screen bg-gradient-to-br from-emerald-800 via-emerald-700 to-emerald-800 flex items-center pt-16 pb-12">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/50 to-transparent"></div>
            <div class="absolute top-0 right-0 w-64 h-64 sm:w-96 sm:h-96 bg-emerald-500/10 rounded-full blur-[80px] sm:blur-[100px]"></div>

            <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    <div class="text-center lg:text-left order-2 lg:order-1">
                        <h1 id="hero-title" class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight mb-4 sm:mb-6">
                            Transporte Mixto<br>
                            <span class="text-emerald-300">Antonio Polo S.A.</span>
                        </h1>
                        
                        <p class="text-base sm:text-lg text-emerald-100/90 mb-6 sm:mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                            Sistema web para la <strong>gestión operativa</strong> y 
                            <strong>control automatizado</strong> del arribo y despacho de unidades 
                            mediante geolocalización entre <strong>Salinas</strong> y 
                            <strong>Guaranda</strong>.
                        </p>

                        <div class="flex justify-center lg:justify-start">
                            <a href="#ruta" class="inline-flex items-center justify-center gap-2 px-6 sm:px-8 py-3 sm:py-3.5 bg-white text-emerald-700 font-semibold rounded-xl hover:bg-emerald-50 transition-colors shadow-lg text-sm sm:text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                Ver Ruta
                            </a>
                        </div>
                    </div>

                    <div class="flex items-center justify-center lg:justify-end order-1 lg:order-2">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-400/30 to-emerald-600/30 rounded-full blur-2xl sm:blur-3xl scale-90 sm:scale-100"></div>
                            <img class="relative w-40 h-40 sm:w-56 sm:h-56 md:w-64 md:h-64 lg:w-72 lg:h-72 object-contain drop-shadow-2xl" 
                                 src="{{ asset('images/logo-antonio-polo.png') }}" 
                                 alt="Logo de la Compañía de Transporte Mixto Antonio Polo S.A."
                                 width="288" height="288"
                                 loading="eager">
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0" aria-hidden="true">
                <svg viewBox="0 0 1440 120" fill="none" class="w-full">
                    <path d="M0 120L48 110C96 100 192 80 288 70C384 60 480 60 576 65C672 70 768 80 864 85C960 90 1056 90 1152 82.5C1248 75 1344 60 1392 52.5L1440 45V120H0Z" fill="white"/>
                </svg>
            </div>
        </section>

        {{-- STATS --}}
        <section aria-labelledby="stats-title" class="bg-white py-8 sm:py-12">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 id="stats-title" class="sr-only">Estadísticas de la compañía</h2>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                    <article class="text-center p-4 sm:p-6 bg-emerald-50 rounded-xl">
                        <div class="text-2xl sm:text-3xl font-bold text-emerald-700" aria-label="24 socios activos">24+</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Socios activos</div>
                    </article>
                    <article class="text-center p-4 sm:p-6 bg-emerald-50 rounded-xl">
                        <div class="text-2xl sm:text-3xl font-bold text-emerald-700">15</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Min. intervalo</div>
                    </article>
                    <article class="text-center p-4 sm:p-6 bg-emerald-50 rounded-xl">
                        <div class="text-2xl sm:text-3xl font-bold text-emerald-700">60</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Min. ruta máx.</div>
                    </article>
                    <article class="text-center p-4 sm:p-6 bg-emerald-50 rounded-xl col-span-2 lg:col-span-1">
                        <div class="text-2xl sm:text-3xl font-bold text-emerald-700">2</div>
                        <div class="text-xs sm:text-sm text-gray-600 mt-1">Puntos de servicio</div>
                    </article>
                </div>
            </div>
        </section>

        {{-- NOSOTROS --}}
        <section id="nosotros" aria-labelledby="nosotros-title" class="py-12 sm:py-16 lg:py-20 bg-gray-50 scroll-mt-16">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="text-center mb-8 sm:mb-12">
                    <span class="inline-block px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4">Sobre Nosotros</span>
                    <h2 id="nosotros-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Quiénes somos</h2>
                    <div class="w-12 h-1 bg-emerald-500 rounded-full mx-auto"></div>
                </header>

                <div class="grid md:grid-cols-2 gap-6 sm:gap-8 items-center">
                    <figure class="flex justify-center">
                        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg w-full max-w-sm aspect-[4/3] flex items-center justify-center">
                            <img class="w-full h-full object-contain" 
                                 src="{{ asset('images/logo-antonio-polo.png') }}" 
                                 alt="Logo de Antonio Polo S.A."
                                 loading="lazy"
                                 width="300" height="225">
                            <figcaption class="sr-only">Logo de la empresa Antonio Polo S.A.</figcaption>
                        </div>
                    </figure>

                    <figure>
                        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-lg w-full max-w-sm aspect-[4/3] flex items-center justify-center">
                            <img src="{{ asset('images/flota.png') }}" 
                                 alt="Flota vehicular de la compañía" 
                                 class="w-full h-full object-contain rounded-xl"
                                 loading="lazy"
                                 width="300" height="225">
                            <figcaption class="sr-only">Imagen de la flota de vehículos de la compañía</figcaption>
                        </div>
                    </figure>
                </div>

                <div class="mt-8 sm:mt-12 max-w-4xl mx-auto text-center">
                    <p class="text-sm sm:text-base text-gray-600 mb-4 leading-relaxed">
                        La <strong class="text-emerald-700">Compañía de Transporte Mixto Antonio Polo S.A.</strong>, 
                        ubicada en la parroquia Salinas del cantón Guaranda, opera entre Salinas y Guaranda con un 
                        <strong class="text-emerald-700">intervalo de 15 minutos</strong> por unidad y un 
                        <strong class="text-emerald-700">tiempo máximo de 60 minutos</strong> por recorrido.
                    </p>
                    <p class="text-sm sm:text-base text-gray-600 mb-6 leading-relaxed">
                        Este sistema digitaliza y automatiza los procesos de arribo, despacho y control de turnos, 
                        eliminando inconsistencias y proporcionando información confiable en tiempo real.
                    </p>
                    
                    <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-medium">Transporte Mixto</span>
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-medium">Unidades 4x4</span>
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-medium">Gestión Digital</span>
                        <span class="px-3 py-1.5 sm:px-4 sm:py-2 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-medium">Geolocalización</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- MISIÓN Y VISIÓN --}}
        <section id="mision-vision" aria-labelledby="mision-vision-title" class="py-12 sm:py-16 lg:py-20 bg-white scroll-mt-16">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="text-center mb-8 sm:mb-12">
                    <span class="inline-block px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4">Nuestros Principios</span>
                    <h2 id="mision-vision-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Misión y Visión</h2>
                    <div class="w-12 h-1 bg-emerald-500 rounded-full mx-auto"></div>
                </header>

                <div class="grid md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                    <article class="bg-emerald-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 lg:p-8 border border-emerald-100">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6" aria-hidden="true">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Misión</h3>
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                            Proporcionar un servicio de transporte mixto confiable, seguro y puntual entre Salinas y Guaranda, 
                            velando por el bienestar de nuestros pasajeros y la integridad de sus bienes, 
                            contribuyendo al desarrollo económico y social de la provincia Bolívar.
                        </p>
                    </article>

                    <article class="bg-emerald-50 rounded-xl sm:rounded-2xl p-5 sm:p-6 lg:p-8 border border-emerald-100">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-500 rounded-xl flex items-center justify-center mb-4 sm:mb-6" aria-hidden="true">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-3 sm:mb-4">Visión</h3>
                        <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                            Consolidarnos como la empresa de transporte de referencia en la zona centro de Bolívar, 
                            destacando por la excelencia en el servicio, la innovación tecnológica en nuestros procesos 
                            y el compromiso con el desarrollo sostenible de las comunidades.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        {{-- HISTORIA --}}
        <section aria-labelledby="historia-title" class="py-12 sm:py-16 lg:py-20 bg-gray-50 scroll-mt-16">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="text-center mb-8 sm:mb-12">
                    <span class="inline-block px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4">Trayectoria</span>
                    <h2 id="historia-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Nuestra Historia</h2>
                    <div class="w-12 h-1 bg-emerald-500 rounded-full mx-auto"></div>
                </header>
                
                <div class="grid sm:grid-cols-3 gap-4 sm:gap-6">
                    <article class="bg-white rounded-xl sm:rounded-2xl p-5 sm:p-6 lg:p-8 shadow-lg text-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-emerald-600 mb-2 sm:mb-4">2010</div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Fundación</h3>
                        <p class="text-sm text-gray-600">Se constituye la Compañía de Transporte Mixto Antonio Polo S.A. en la parroquia Salinas.</p>
                    </article>
                    
                    <article class="bg-white rounded-xl sm:rounded-2xl p-5 sm:p-6 lg:p-8 shadow-lg text-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-emerald-600 mb-2 sm:mb-4">2015</div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-2 sm:mb-3">Consolidación</h3>
                        <p class="text-sm text-gray-600">Se amplía la flota vehicular y se establecen puntos de parada fijos en Guaranda y Salinas.</p>
                    </article>
                    
                    <article class="bg-emerald-600 rounded-xl sm:rounded-2xl p-5 sm:p-6 lg:p-8 shadow-lg text-center text-white sm:col-span-3 md:col-span-1">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white/80 mb-2 sm:mb-4">2024</div>
                        <h3 class="text-base sm:text-lg lg:text-xl font-bold mb-2 sm:mb-3">Innovación</h3>
                        <p class="text-sm text-emerald-100">Implementación del sistema web de gestión con geolocalización.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- FUNCIONES --}}
        <section id="funciones" aria-labelledby="funciones-title" class="py-12 sm:py-16 lg:py-20 bg-white scroll-mt-16">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="text-center mb-8 sm:mb-12">
                    <span class="inline-block px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-semibold mb-4">Características</span>
                    <h2 id="funciones-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Funciones del Sistema</h2>
                    <div class="w-16 h-1 bg-emerald-500 rounded-full mx-auto mb-4"></div>
                    <p class="text-sm sm:text-base text-gray-600 max-w-2xl mx-auto">Automatización completa para la gestión operativa de la compañía de transporte</p>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @php 
                    $features = [
                        ['title' => 'Sorteo y Turnos', 'desc' => 'Sorteo diario automatizado, asignación de punteros y control del orden de salida por punto.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['title' => 'Geolocalización GPS', 'desc' => 'Monitoreo en tiempo real de las unidades mediante mapas interactivos con actualización cada 5 segundos.', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['title' => 'Detección por Geofence', 'desc' => 'Registro automático de llegada cuando la unidad ingresa al área configurada del punto de destino.', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                        ['title' => 'Multas Automáticas', 'desc' => 'Cálculo automático de multas por retraso (60+ min) y gestión de sanciones por no presentación.', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['title' => 'Validación Documental', 'desc' => 'Control de licencias y matrículas con bloqueo automático de conductores con documentos vencidos.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['title' => 'Reportes en PDF', 'desc' => 'Generación de informes operativos exportables: turnos, multas, punteros, llegadas y despachos.', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['title' => 'Justificativos', 'desc' => 'Registro de inasistencias, retrasos y sustituciones de conductores con documentación del reemplazante.', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ['title' => 'Auditoría', 'desc' => 'Historial inmutable de todos los cambios operativos y configuraciones del sistema.', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                        ['title' => 'Sincronización Offline', 'desc' => 'Almacenamiento local de datos cuando se pierde conexión, sincronización automática al recuperar señal.', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                    ]; 
                    @endphp
                    
                    @foreach($features as $i => $feature)
                    <article class="group bg-white rounded-xl p-5 sm:p-6 border border-gray-200 hover:border-emerald-400 hover:shadow-xl transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-emerald-600 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-emerald-500 transition-colors duration-300" aria-hidden="true">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $feature['desc'] }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- RUTA --}}
        <section id="ruta" aria-labelledby="ruta-title" class="py-12 sm:py-16 lg:py-20 bg-gray-50 scroll-mt-16">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <header class="text-center mb-8 sm:mb-12">
                    <span class="inline-block px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs sm:text-sm font-semibold mb-3 sm:mb-4">Cobertura</span>
                    <h2 id="ruta-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3">Rutas y Puntos de Servicio</h2>
                    <div class="w-12 h-1 bg-emerald-500 rounded-full mx-auto mb-4 sm:mb-6"></div>
                    <p class="text-sm sm:text-base text-gray-600 max-w-2xl mx-auto">Monitoreo en tiempo real de las unidades en los puntos establecidos</p>
                </header>

                <div class="grid lg:grid-cols-2 gap-6 sm:gap-8">
                    <article>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden relative">
                            <div id="map-guaranda-salinas" class="map-container h-56 sm:h-72 w-full" role="application" aria-label="Mapa de la ruta Guaranda a Salinas"></div>
                        </div>
                        <div class="mt-4 bg-white rounded-xl p-4 sm:p-6 shadow-lg">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4">
                                <span class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 rounded-full" aria-hidden="true"></span>
                                    <span class="text-gray-600">Partida: Guaranda</span>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                <span class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="w-3 h-3 sm:w-4 sm:h-4 bg-red-500 rounded-full" aria-hidden="true"></span>
                                    <span class="text-gray-600">Llegada: Salinas</span>
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1 sm:mb-2">Guaranda → Salinas</h3>
                            <p class="text-emerald-600 text-xs sm:text-sm font-semibold mb-1 sm:mb-2">Ruta principal</p>
                            <p class="text-xs sm:text-sm text-gray-600">Salidas cada 15 minutos desde la Cooperativa en Guaranda (Salinas 214). Tiempo máximo: 60 minutos.</p>
                        </div>
                    </article>

                    <article>
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden relative">
                            <div id="map-salinas-guaranda" class="map-container h-56 sm:h-72 w-full" role="application" aria-label="Mapa de la ruta Salinas a Guaranda"></div>
                        </div>
                        <div class="mt-4 bg-white rounded-xl p-4 sm:p-6 shadow-lg">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 mb-4">
                                <span class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 rounded-full" aria-hidden="true"></span>
                                    <span class="text-gray-600">Partida: Salinas</span>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                <span class="flex items-center gap-2 text-xs sm:text-sm">
                                    <span class="w-3 h-3 sm:w-4 sm:h-4 bg-red-500 rounded-full" aria-hidden="true"></span>
                                    <span class="text-gray-600">Llegada: Guaranda</span>
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1 sm:mb-2">Salinas → Guaranda</h3>
                            <p class="text-blue-600 text-xs sm:text-sm font-semibold mb-1 sm:mb-2">Ruta de retorno</p>
                            <p class="text-xs sm:text-sm text-gray-600">Salidas cada 15 minutos desde Confites El Salinerito en Salinas. Tiempo máximo: 60 minutos.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section aria-labelledby="cta-title" class="py-12 sm:py-16 lg:py-20 bg-gradient-to-br from-emerald-700 to-emerald-800">
            <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 id="cta-title" class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4">
                    Acceso al Sistema
                </h2>
                <p class="text-base sm:text-lg text-emerald-100 mb-6 sm:mb-8 max-w-2xl mx-auto">
                    Control total de la operación: turnos, seguimiento GPS, multas, reportes y más para todo el equipo.
                </p>
                
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 sm:py-3.5 bg-white text-emerald-700 font-semibold rounded-xl hover:bg-emerald-50 transition-colors shadow-lg text-sm sm:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Ingresar
                </a>
            </div>
        </section>
    </main>

    @include('partials.footer')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" media="print" onload="this.media='all'">
    
    <script>
        window.addEventListener('load', function() {
            AOS.init({ duration: 600, easing: 'ease-out', once: true });
            
            const blueIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background:#3b82f6;width:24px;height:24px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><svg viewBox="0 0 24 24" width="14" height="14" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const redIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background:#ef4444;width:24px;height:24px;border-radius:50%;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);display:flex;align-items:center;justify-content:center;"><svg viewBox="0 0 24 24" width="14" height="14" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const guarandaCoords = [-1.6033, -79.0018];
            const salinasCoords = [-1.4000, -79.0167];

            let map1Created = false;
            let map2Created = false;
            let map1, map2;

            function initMaps() {
                if (!map1Created && document.getElementById('map-guaranda-salinas')) {
                    map1 = L.map('map-guaranda-salinas', { center: [-1.50, -79.01], zoom: 10, scrollWheelZoom: false });
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map1);
                    L.marker(guarandaCoords, { icon: blueIcon }).addTo(map1).bindPopup('<b style="color:#3b82f6">● Guaranda</b><br><small>Cooperativa Salinas 214</small><br><small style="color:#6b7280">Punto de partida</small><br><small style="color:#6b7280">Salidas cada 15 min</small>');
                    L.marker(salinasCoords, { icon: redIcon }).addTo(map1).bindPopup('<b style="color:#ef4444">● Salinas</b><br><small>Confites El Salinerito</small><br><small style="color:#6b7280">Punto de llegada</small><br><small style="color:#6b7280">Tiempo máx. 60 min</small>');
                    L.polyline([guarandaCoords, salinasCoords], { color: '#10b981', weight: 3, opacity: 0.8, dashArray: '8, 4' }).addTo(map1);
                    map1.invalidateSize();
                    map1Created = true;
                }
                
                if (!map2Created && document.getElementById('map-salinas-guaranda')) {
                    map2 = L.map('map-salinas-guaranda', { center: [-1.50, -79.01], zoom: 10, scrollWheelZoom: false });
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map2);
                    L.marker(salinasCoords, { icon: blueIcon }).addTo(map2).bindPopup('<b style="color:#3b82f6">● Salinas</b><br><small>Confites El Salinerito</small><br><small style="color:#6b7280">Punto de partida</small><br><small style="color:#6b7280">Salidas cada 15 min</small>');
                    L.marker(guarandaCoords, { icon: redIcon }).addTo(map2).bindPopup('<b style="color:#ef4444">● Guaranda</b><br><small>Cooperativa Salinas 214</small><br><small style="color:#6b7280">Punto de llegada</small><br><small style="color:#6b7280">Tiempo máx. 60 min</small>');
                    L.polyline([salinasCoords, guarandaCoords], { color: '#3b82f6', weight: 3, opacity: 0.8, dashArray: '8, 4' }).addTo(map2);
                    map2.invalidateSize();
                    map2Created = true;
                }
            }

            initMaps();

            const mapSection = document.getElementById('ruta');
            if (mapSection) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            setTimeout(() => {
                                if (map1) { map1.invalidateSize(); map1.setView([-1.50, -79.01], 10); }
                                if (map2) { map2.invalidateSize(); map2.setView([-1.50, -79.01], 10); }
                            }, 300);
                        }
                    });
                }, { threshold: 0.3 });
                observer.observe(mapSection);
            }

            let scrollTimeout;
            window.addEventListener('scroll', function() {
                if (scrollTimeout) clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    if (map1) map1.invalidateSize();
                    if (map2) map2.invalidateSize();
                }, 200);
            });

            window.addEventListener('resize', function() {
                if (map1) map1.invalidateSize();
                if (map2) map2.invalidateSize();
            });
        });
    </script>

</body>
</html>
