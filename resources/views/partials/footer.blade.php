{{-- Footer compartido para vistas auth y principal --}}
<footer role="contentinfo" class="bg-emerald-900 text-white">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-10 sm:py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <img class="h-12 w-12 rounded-xl object-contain bg-white/10 p-1" src="{{ asset('images/logo-antonio-polo.png') }}" alt="Logo de Antonio Polo S.A." width="48" height="48">
                    <div>
                        <div class="font-bold text-base sm:text-lg">Antonio Polo S.A.</div>
                        <div class="text-emerald-400 text-sm">Compañía de Transporte Mixto</div>
                    </div>
                </div>
                <p class="text-emerald-200 text-sm mb-6 leading-relaxed max-w-md">
                    Tu mejor opción en transporte entre Salinas y Guaranda. Servicio seguro y puntual desde 2010.
                </p>
                <a href="https://wa.me/593999999999" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 rounded-xl text-sm font-semibold transition-colors"
                   aria-label="Contactar por WhatsApp">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Contáctanos
                </a>
            </div>

            <div>
                <h3 class="font-bold mb-4 text-white text-base flex items-center gap-2">
                    <span class="w-1 h-6 bg-green-500 rounded-full"></span>
                    Contacto
                </h3>
                <address class="not-italic space-y-3 text-sm">
                    <p class="text-emerald-200 flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Parroquia Salinas, Cantón Guaranda, Provincia Bolívar, Ecuador</span>
                    </p>
                    <p class="text-emerald-200 flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <a href="tel:+593999999999" class="hover:text-white transition-colors">+593 99 999 9999</a>
                    </p>
                    <p class="text-emerald-200 flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:antoniopolo@gmail.com" class="hover:text-white transition-colors">antoniopolo@gmail.com</a>
                    </p>
                </address>
            </div>

            <div>
                <h3 class="font-bold mb-4 text-white text-base flex items-center gap-2">
                    <span class="w-1 h-6 bg-green-500 rounded-full"></span>
                    Enlaces
                </h3>
                <nav class="space-y-2 text-sm" aria-label="Enlaces legales">
                    <a href="#" class="block text-emerald-200 hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Términos y Condiciones
                    </a>
                    <a href="#" class="block text-emerald-200 hover:text-white transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Política de Privacidad
                    </a>
                </nav>
            </div>
        </div>
        
        <div class="py-4 border-t border-emerald-800">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-emerald-400">
                <span>© {{ date('Y') }} Antonio Polo S.A. — Todos los derechos reservados.</span>
                <span class="text-emerald-500">Provincia Bolívar, Ecuador</span>
            </div>
        </div>
    </div>
</footer>
