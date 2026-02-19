<template>
    <!-- Hero Section -->
    <div class="relative w-full min-h-[400px] flex items-start bg-white pt-24 overflow-hidden shadow-md z-0">
        
        <!-- Background Image -->
        <div class="absolute inset-0 z-0 flex justify-end items-end mt-6">
             <img 
                src="/images/header_background.svg" 
                alt="Background" 
                class="h-[110%] w-auto object-contain object-right-bottom"
            />
        </div>

        <!-- Content -->
        <div class="container mx-auto px-6 relative z-10">
            <div class="max-w-2xl">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 leading-tight mb-8">
                    Siempre a mejor
                </h1>

                <!-- Search Box -->
                <div class="flex flex-col md:flex-row items-end gap-4">
                    <!-- Keyword Input -->
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Busco ofertas de...</label>
                        <InputText 
                            placeholder="Puesto, empresa o palabra clave" 
                            class="w-full p-3 rounded-lg border-gray-300 shadow-sm"
                        />
                    </div>

                    <!-- Location Input -->
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-gray-600 mb-2">en...</label>
                        <div class="relative">
                            <InputText 
                                placeholder="Toda España" 
                                class="w-full p-3 rounded-lg border-gray-300 shadow-sm"
                            />
                            <i class="pi pi-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <Button 
                        label="BUSCAR" 
                        class="w-full md:w-auto border-none font-bold px-8 py-3 rounded-lg shadow-md transition-colors"
                        style="background-color: #013C7B !important; color: white;"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { authStore } from "@/store/auth";
</script>
