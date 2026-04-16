<template>
    <footer class="bg-white pt-10 pb-6">
        <div class="max-w-6xl mx-auto px-8">
            <!-- Footer Columns -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-y-8 gap-x-12 mb-10">
                <!-- Misión y valores -->
                <div>
                    <h4 class="font-bold text-sm text-gray-900 mb-3">Misión y valores</h4>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Ofertas</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Quienes somos</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Valores</a></li>
                    </ul>
                </div>

                <!-- Ofertas -->
                <div>
                    <h4 class="font-bold text-sm text-gray-900 mb-3">Ofertas</h4>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Media Jornada</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Hostelería</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Barcelona</a></li>
                    </ul>
                </div>

                <!-- Contacta -->
                <div>
                    <h4 class="font-bold text-sm text-gray-900 mb-3">Contacta</h4>
                    <ul class="space-y-1.5">
                        <li><a href="mailto:contacto@oportunidades.com" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">contacto@oportunidades.com</a></li>
                        <li><a href="tel:+34660770075" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">+34 660 77 00 75</a></li>
                        <li class="text-sm text-gray-500">Carrer Sra. de Lourdes, 34, 08750</li>
                    </ul>
                </div>

                <!-- Información legal -->
                <div>
                    <h4 class="font-bold text-sm text-gray-900 mb-3">Información legal</h4>
                    <ul class="space-y-1.5">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Política de Privacidad</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Uso del servicio</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-[#013C7B] transition-colors">Política de cookies</a></li>
                    </ul>
                </div>
            </div>

            <!-- Social Icons -->
            <div class="flex items-center gap-3 mb-6">
                <span class="font-bold text-sm text-gray-900">¡Síguenos!</span>
                <a href="#" class="w-7 h-7 bg-gray-900 rounded-full flex items-center justify-center hover:bg-[#013C7B] transition-colors">
                    <i class="pi pi-facebook text-white text-xs"></i>
                </a>
                <a href="#" class="w-7 h-7 bg-gray-900 rounded-full flex items-center justify-center hover:bg-[#013C7B] transition-colors">
                    <i class="pi pi-twitter text-white text-xs"></i>
                </a>
                <a href="#" class="w-7 h-7 bg-gray-900 rounded-full flex items-center justify-center hover:bg-[#013C7B] transition-colors">
                    <i class="pi pi-youtube text-white text-xs"></i>
                </a>
            </div>

            <!-- Divider + Copyright -->
            <div class="border-t border-gray-200 pt-5">
                <p class="text-center text-xs text-gray-400">
                    © Camino de Oportunidades S.L. {{ currentYear }}
                </p>
            </div>
        </div>
    </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear();
</script>
