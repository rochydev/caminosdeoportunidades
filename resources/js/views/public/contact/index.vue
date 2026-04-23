<template>
    <div>
        <!-- Hero section -->
        <div class="relative w-full flex items-center bg-white pt-20 pb-16 overflow-hidden z-0">
            <div class="container mx-auto px-6">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-4">
                    Ponte en contacto
                </h1>
                <p class="text-lg text-gray-600 max-w-lg">
                    ¿Tienes preguntas? Estamos aquí para ayudarte. Contacta con nosotros a través de los medios
                    disponibles.
                </p>
            </div>
        </div>

        <!-- Seccion contacto -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="max-w-5xl mx-auto">
                    <div class="bg-gray-50 space-y-8">
                        <!-- Dos columnas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                            <!-- Informacion de contacto -->
                            <div>
                                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="pi pi-info-circle text-[#013C7B] text-lg"></i>
                                    Información de contacto
                                </h3>
                                <div class="space-y-5">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600 mb-1">Email</p>
                                        <a href="mailto:contacto@oportunidades.com" class="text-[#013C7B] text-sm">
                                            contacto@oportunidades.com
                                        </a>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600 mb-1">Teléfono</p>
                                        <a href="tel:+34123456789" class="text-[#013C7B] text-sm">
                                            +34 123 45 67 89
                                        </a>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600 mb-1">Dirección</p>
                                        <p class="text-sm text-gray-700">
                                            Carrer Sra. de Lourdes, 34<br>
                                            08750, Barcelona
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Horario de atencion -->
                            <div>
                                <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                                    <i class="pi pi-clock text-[#013C7B] text-lg"></i>
                                    Horario de atención
                                </h3>
                                <div class="space-y-3 text-sm text-gray-700">
                                    <div class="flex justify-between">
                                        <span>Lunes - Viernes:</span>
                                        <span class="font-medium">09:00 - 18:00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Sábado:</span>
                                        <span class="font-medium">10:00 - 13:00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Domingo:</span>
                                        <span class="font-medium">Cerrado</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- divisor -->
                        <div class="border-t border-gray-200"></div>

                        <!-- Redes sociales -->
                        <div>
                            <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <i class="pi pi-share-alt text-[#013C7B] text-lg"></i>
                                Síguenos
                            </h3>
                            <div class="flex gap-3">
                                <a href="#"
                                    class="w-10 h-10 bg-[#013C7B] rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <i class="pi pi-facebook text-white text-sm"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-[#013C7B] rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <i class="pi pi-twitter text-white text-sm"></i>
                                </a>
                                <a href="#"
                                    class="w-10 h-10 bg-[#013C7B] rounded-full flex items-center justify-center hover:opacity-80 transition-opacity">
                                    <i class="pi pi-youtube text-white text-sm"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
