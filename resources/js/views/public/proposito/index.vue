<template>
    <div>
        <!-- Hero section -->
        <div class="relative w-full flex items-center bg-white pt-20 pb-16 overflow-hidden z-0">
            <div class="container mx-auto px-6">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-4">
                    Oportunidades para Todos
                </h1>
                <p class="text-lg text-gray-600 max-w-lg">
                    Creemos en el potencial de todas las personas, sin importar sus capacidades. Conoce nuestro compromiso 
                    con la inclusión laboral de personas con discapacidades.
                </p>
            </div>
        </div>

        <!-- Sección Misión, Visión y Valores -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Misión -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-8 border border-blue-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-[#013C7B] rounded-lg flex items-center justify-center">
                                <i class="pi pi-users text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Misión</h2>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            Conectar el talento de personas con discapacidades con empresas comprometidas 
                            con la inclusión laboral, creando puentes hacia oportunidades significativas 
                            y empleos dignos que reconozcan el valor de cada individuo.
                        </p>
                    </div>

                    <!-- Visión -->
                    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg p-8 border border-pink-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-[#E91E63] rounded-lg flex items-center justify-center">
                                <i class="pi pi-megaphone text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Visión</h2>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            Ser la plataforma líder en inclusión laboral, donde personas con discapacidades 
                            acceden a oportunidades reales, equitativas y accesibles para construir 
                            carreras profesionales plenas y transformar el mercado laboral.
                        </p>
                    </div>

                    <!-- Valores -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-8 border border-green-200">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="pi pi-shield text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Valores</h2>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            Inclusión sin compromiso, accesibilidad total, dignidad laboral, 
                            no discriminación y compromiso real con la diversidad. Creemos que 
                            la discapacidad no define capacidades profesionales.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección Objetivos -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">
                    Nuestro Compromiso
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-[#E91E63]">
                                <i class="pi pi-check text-white text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Sensibilizar empresas</h3>
                            <p class="text-gray-600">
                                Trabajar con empresas para crear espacios inclusivos, accesibles 
                                y libres de prejuicios donde prospera el talento diverso.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-[#013C7B]">
                                <i class="pi pi-check text-white text-lg"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Eliminar barreras laborales</h3>
                            <p class="text-gray-600">
                                Luchar contra la discriminación y crear oportunidades reales, 
                                equitativas y sostenibles para la inserción laboral.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección CTA -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="bg-gradient-to-r from-[#013C7B] to-[#0052a3] rounded-lg p-12 text-center text-white">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">
                        Tu talento merece ser reconocido
                    </h2>
                    <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
                        Si tienes una discapacidad, estamos aquí para darte las mismas oportunidades 
                        que mereces. Explora vacantes accesibles y construye tu carrera profesional sin límites.
                    </p>
                    <div class="flex flex-col md:flex-row gap-4 justify-center">
                        <router-link to="/ofertas">
                            <button class="bg-white text-[#013C7B] hover:bg-blue-50 font-bold px-8 py-3 rounded-lg transition-all duration-200">
                                Ver ofertas accesibles
                            </button>
                        </router-link>
                        <router-link to="/register">
                            <button class="border-2 border-white text-white hover:bg-white hover:text-[#013C7B] font-bold px-8 py-3 rounded-lg transition-all duration-200">
                                Crear mi perfil
                            </button>
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
// No se necesita lógica específica para esta página de contenido estático
</script>

<style scoped>
/* Estilos adicionales si es necesario */
</style>
