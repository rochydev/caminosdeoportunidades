<template>
    <div>
        <!-- Hero section -->
        <div class="relative w-full flex items-center bg-white pt-20 pb-16 overflow-hidden z-0">
            <div class="container mx-auto px-6">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-4">
                    Ponte en contacto
                </h1>
                <p class="text-lg text-gray-600 max-w-lg">
                    ¿Tienes preguntas sobre oportunidades laborales o necesitas más información? 
                    Estamos aquí para ayudarte. Contacta con nosotros a través de los siguientes medios.
                </p>
            </div>
        </div>

        <!-- Sección Información de Contacto -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Email -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-8 border border-blue-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-[#013C7B] rounded-lg flex items-center justify-center">
                                <i class="pi pi-envelope text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Email</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Envía tu consulta y te responderemos en el menor tiempo posible.
                        </p>
                        <a href="mailto:contacto@oportunidades.com" 
                           class="inline-block text-[#013C7B] font-semibold hover:text-[#0052a3] transition-colors">
                            contacto@oportunidades.com
                        </a>
                    </div>

                    <!-- Teléfono -->
                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-8 border border-pink-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-[#E91E63] rounded-lg flex items-center justify-center">
                                <i class="pi pi-phone text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Teléfono</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Llámanos durante nuestro horario de atención.
                        </p>
                        <a href="tel:+34123456789" 
                           class="inline-block text-[#E91E63] font-semibold hover:text-[#C2185B] transition-colors">
                            +34 123 45 67 89
                        </a>
                    </div>

                    <!-- Dirección -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-8 border border-green-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="pi pi-map-marker text-white text-xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Dirección</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            Visítanos en nuestras oficinas.
                        </p>
                        <p class="text-gray-700 font-semibold text-sm">
                            Carrer Sra. de Lourdes, 34<br>
                            08750, Barcelona
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Horario de Atención -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-lg p-12 border border-gray-200 shadow-sm">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-14 h-14 bg-[#013C7B] rounded-lg flex items-center justify-center">
                                <i class="pi pi-calendar text-white text-2xl"></i>
                            </div>
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Horario de Atención</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <span class="font-semibold text-gray-900">Lunes - Viernes</span>
                                        <span class="text-[#013C7B] font-bold">09:00 - 18:00</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-pink-50 rounded-lg border border-pink-200">
                                        <span class="font-semibold text-gray-900">Sábado</span>
                                        <span class="text-[#E91E63] font-bold">10:00 - 13:00</span>
                                    </div>
                                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
                                        <span class="font-semibold text-gray-900">Domingo</span>
                                        <span class="text-red-600 font-bold">Cerrado</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center">
                                <div class="text-center">
                                    <i class="pi pi-clock text-[#013C7B] text-5xl mb-3"></i>
                                    <p class="text-gray-600 text-sm">
                                        Estamos disponibles para atenderte durante estos horarios. 
                                        Fuera de estos horarios, tu mensaje será atendido al regresar.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección CTA -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="bg-gradient-to-r from-[#013C7B] to-[#0052a3] rounded-lg p-12 text-center text-white max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">
                        ¿Tienes dudas sobre el proceso de selección?
                    </h2>
                    <p class="text-lg text-blue-100 mb-8">
                        Nuestro equipo está comprometido en ayudarte. No dudes en comunicarte con nosotros 
                        para resolver cualquier pregunta sobre accesibilidad, oportunidades o cualquier otro aspecto.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="mailto:contacto@oportunidades.com">
                            <button class="bg-white text-[#013C7B] hover:bg-blue-50 font-bold px-8 py-3 rounded-lg transition-all duration-200 w-full sm:w-auto">
                                Enviar Email
                            </button>
                        </a>
                        <router-link to="/proposito">
                            <button class="border-2 border-white text-white hover:bg-white hover:text-[#013C7B] font-bold px-8 py-3 rounded-lg transition-all duration-200 w-full sm:w-auto">
                                Conocer más sobre nosotros
                            </button>
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
