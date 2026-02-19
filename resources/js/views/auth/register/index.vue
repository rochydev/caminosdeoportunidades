<template>
    <div class="min-h-screen flex flex-col items-center justify-start pt-10 sm:pt-16 bg-[#FAFAFA] px-4 sm:px-6 lg:px-8 font-sans">
        
        <div class="w-full max-w-2xl bg-white p-8 sm:p-12 rounded-xl shadow-sm border border-gray-100">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">
                    Crea tu cuenta
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Únete a <span class="text-[#013C7B] font-bold">Caminos de Oportunidades</span>
                </p>
            </div>

            <form @submit.prevent="submitRegister" class="space-y-5">
                <!-- Name -->
                <div class="flex flex-col gap-2">
                    <label for="name" class="font-medium text-gray-700">Nombre</label>
                    <InputText
                        id="name"
                        v-model="registerForm.name"
                        class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                        :class="{ 'p-invalid': validationErrors?.name }"
                        placeholder="Tu nombre"
                    />
                    <small v-if="validationErrors?.name" class="text-red-500">
                        {{ validationErrors.name[0] }}
                    </small>
                </div>

                <!-- Surnames -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="surname1" class="font-medium text-gray-700">Primer apellido</label>
                        <InputText
                            id="surname1"
                            v-model="registerForm.surname1"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                            :class="{ 'p-invalid': validationErrors?.surname1 }"
                            placeholder="Apellido 1"
                        />
                        <small v-if="validationErrors?.surname1" class="text-red-500">
                            {{ validationErrors.surname1[0] }}
                        </small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="surname2" class="font-medium text-gray-700">Segundo apellido</label>
                        <InputText
                            id="surname2"
                            v-model="registerForm.surname2"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                            :class="{ 'p-invalid': validationErrors?.surname2 }"
                            placeholder="Apellido 2"
                        />
                        <small v-if="validationErrors?.surname2" class="text-red-500">
                            {{ validationErrors.surname2[0] }}
                        </small>
                    </div>
                </div>

                <!-- Email -->
                <div class="flex flex-col gap-2">
                    <label for="email" class="font-medium text-gray-700">Email</label>
                    <InputText
                        id="email"
                        type="email"
                        v-model="registerForm.email"
                        class="w-full p-3 border-gray-300 rounded-lg focus:ring-[#013C7B] focus:border-[#013C7B]"
                        :class="{ 'p-invalid': validationErrors?.email }"
                        placeholder="tu@email.com"
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
                        v-model="registerForm.password"
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
                        v-model="registerForm.password_confirmation"
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
                    label="Registrarme"
                    :loading="processing"
                    :disabled="processing"
                    class="w-full font-bold py-3 rounded-full transition-colors register-submit-button"
                />

                <div class="border-t border-gray-200 my-6"></div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        ¿Ya tienes una cuenta?
                        <router-link
                            :to="{ name: 'auth.login' }"
                            class="text-[#013C7B] font-bold hover:underline"
                        >
                            Inicia sesión aquí
                        </router-link>
                    </p>
                    <p class="mt-4 text-sm text-gray-600">
                        ¿Eres una empresa?
                        <a href="#" class="text-[#013C7B] font-bold hover:underline">
                            Regístrate aquí
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import useAuth from '@/composables/auth';

const { registerForm, validationErrors, processing, submitRegister } = useAuth();
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
