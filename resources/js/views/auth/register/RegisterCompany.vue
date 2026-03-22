<template>
    <div class="min-h-screen flex flex-col items-center justify-start pt-10 sm:pt-16 bg-[#FAFAFA] px-4 sm:px-6 lg:px-8 font-sans pb-10">
        
        <div class="w-full max-w-2xl bg-white p-8 sm:p-12 rounded-xl shadow-sm border border-gray-100">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">
                    Crea tu cuenta de Empresa
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Encuentra el mejor talento en <span class="text-[#013C7B] font-bold">Caminos de Oportunidades</span>
                </p>
            </div>

            <form @submit.prevent="submitCompanyRegister" class="space-y-5">
                
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Datos de la Empresa</h3>
                    <p class="text-xs text-gray-500">Información sobre la organización.</p>
                </div>

                <!-- Company Name -->
                <div class="flex flex-col gap-2">
                    <label for="company_name" class="font-medium text-gray-700">Razón Social o Nombre Público</label>
                    <InputText
                        id="company_name"
                        v-model="registerCompanyForm.company_name"
                        class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                        :class="{ 'p-invalid': validationErrors?.company_name }"
                        placeholder="Mi Empresa S.L."
                    />
                    <small v-if="validationErrors?.company_name" class="text-red-500">
                        {{ validationErrors.company_name[0] }}
                    </small>
                </div>
                
                <div class="border-b border-gray-100 pb-4 mb-4 pt-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Datos de Contacto (Responsable)</h3>
                    <p class="text-xs text-gray-500">Información de la persona que gestionará la cuenta.</p>
                </div>

                <!-- Contact Name & Surname -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="font-medium text-gray-700">Nombre</label>
                        <InputText
                            id="name"
                            v-model="registerCompanyForm.name"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                            :class="{ 'p-invalid': validationErrors?.name }"
                            placeholder="Tu nombre"
                        />
                        <small v-if="validationErrors?.name" class="text-red-500">
                            {{ validationErrors.name[0] }}
                        </small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="surname1" class="font-medium text-gray-700">Apellidos</label>
                        <InputText
                            id="surname1"
                            v-model="registerCompanyForm.surname1"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                            :class="{ 'p-invalid': validationErrors?.surname1 }"
                            placeholder="Tus apellidos"
                        />
                        <small v-if="validationErrors?.surname1" class="text-red-500">
                            {{ validationErrors.surname1[0] }}
                        </small>
                    </div>
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-2">
                    <label for="email" class="font-medium text-gray-700">Email Corporativo</label>
                    <InputText
                        id="email"
                        type="email"
                        v-model="registerCompanyForm.email"
                        class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                        :class="{ 'p-invalid': validationErrors?.email }"
                        placeholder="tu@empresa.com"
                    />
                    <small v-if="validationErrors?.email" class="text-red-500">
                        {{ validationErrors.email[0] }}
                    </small>
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-2">
                    <label for="password" class="font-medium text-gray-700">Contraseña</label>
                    <Password
                        id="password"
                        v-model="registerCompanyForm.password"
                        toggleMask
                        :feedback="false"
                        inputClass="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                        class="w-full"
                        :class="{ 'p-invalid': validationErrors?.password }"
                        placeholder="••••••••"
                    />
                    <small v-if="validationErrors?.password" class="text-red-500">
                        {{ validationErrors.password[0] }}
                    </small>
                </div>

                <!-- Confirm Password -->
                <div class="flex flex-col gap-2">
                    <label for="password_confirmation" class="font-medium text-gray-700">Confirmar contraseña</label>
                    <Password
                        id="password_confirmation"
                        v-model="registerCompanyForm.password_confirmation"
                        toggleMask
                        :feedback="false"
                        inputClass="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                        class="w-full"
                        :class="{ 'p-invalid': validationErrors?.password_confirmation }"
                        placeholder="••••••••"
                    />
                    <small v-if="validationErrors?.password_confirmation" class="text-red-500">
                        {{ validationErrors.password_confirmation[0] }}
                    </small>
                </div>

                <!-- Submit Button -->
                <Button
                    type="submit"
                    label="Solicitar Alta de Empresa"
                    :loading="processing"
                    :disabled="processing"
                    class="w-full font-bold py-3 rounded-full transition-colors register-submit-button mt-4"
                />

                <div class="border-t border-gray-200 my-6"></div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        ¿Ya tienes una cuenta de empresa?
                        <router-link
                            :to="{ name: 'auth.login.company' }"
                            class="text-[#013C7B] font-bold hover:underline"
                        >
                            Inicia sesión aquí
                        </router-link>
                    </p>
                    <p class="mt-4 text-sm text-gray-600">
                        ¿Eres un candidato/a?
                        <router-link :to="{ name: 'auth.register' }" class="text-[#013C7B] font-bold hover:underline">
                            Regístrate aquí
                        </router-link>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import useAuth from '@/composables/auth';

const { registerCompanyForm, validationErrors, processing, submitCompanyRegister } = useAuth();
</script>

<style scoped>
:deep(.register-submit-button) {
    background-color: #013C7B !important;
    border: 1px solid #013C7B !important;
    color: #ffffff !important;
}
:deep(.register-submit-button:hover) {
    background-color: #012d5e !important;
    border-color: #012d5e !important;
}

:deep(.p-inputtext) {
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    width: 100%;
}

:deep(.p-inputtext:focus) {
    border-color: #013C7B !important;
    box-shadow: 0 0 0 2px #dbeafe !important;
}

:deep(.p-password-input) {
    border-radius: 0.5rem;
    padding: 0.75rem 1rem;
    width: 100%;
}

:deep(.p-password) {
    width: 100%;
}

:deep(.p-button) {
    border-radius: 9999px;
    justify-content: center;
    width: 100%;
}

:deep(.pi) {
    font-family: 'primeicons' !important;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    display: inline-block;
}
</style>
