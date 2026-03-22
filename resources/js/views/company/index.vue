<template>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
                <div class="text-[#013C7B] bg-blue-50 p-3 rounded-full mb-4">
                    <i class="pi pi-briefcase text-2xl"></i>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Ofertas Activas</h3>
                <p class="text-3xl font-bold text-gray-900 mt-1">2</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
                <div class="text-[#013C7B] bg-emerald-50 p-3 rounded-full mb-4">
                    <i class="pi pi-users text-2xl"></i>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Candidaturas Nuevas</h3>
                <p class="text-3xl font-bold text-gray-900 mt-1">15</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center">
                <div class="text-[#013C7B] bg-purple-50 p-3 rounded-full mb-4">
                    <i class="pi pi-eye text-2xl"></i>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Visitas al Perfil</h3>
                <p class="text-3xl font-bold text-gray-900 mt-1">84</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center cursor-pointer hover:shadow-md transition-shadow">
                <div class="text-white bg-[#013C7B] p-3 rounded-full mb-4">
                    <i class="pi pi-plus text-2xl"></i>
                </div>
                <h3 class="text-gray-900 text-sm font-bold mt-1">Publicar nueva oferta</h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">Candidaturas Recientes</h2>
                <Button label="Ver todas" class="p-button-text p-button-sm text-[#013C7B]" />
            </div>
            
            <div class="p-6 text-center text-gray-500 py-12">
                <i class="pi pi-inbox text-4xl mb-3 text-gray-300"></i>
                <p>Las últimas candidaturas de talento aparecerán aquí.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>

<style scoped>
</style>
