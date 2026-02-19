<template>
    <div class="min-h-screen flex flex-col items-center justify-start pt-16 sm:pt-24 bg-[#FAFAFA] px-4 sm:px-6 lg:px-8 font-sans">
        <div class="w-full max-w-[1000px] grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    Accede como candidato/a
                </h2>

                <form @submit.prevent="submitLogin" class="space-y-5">
                    <div class="flex flex-col gap-2">
                        <label for="email" class="font-medium text-gray-700">Email</label>
                        <InputText
                            id="email"
                            type="email"
                            v-model="loginForm.email"
                            class="w-full p-3 border-gray-300 rounded-lg focus:ring-blue-600 focus:border-blue-600"
                            :class="{ 'p-invalid': validationErrors?.email }"
                        />
                        <small v-if="validationErrors?.email" class="text-red-500">
                            <div v-for="message in validationErrors.email" :key="message">
                                {{ message }}
                            </div>
                        </small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="password" class="font-medium text-gray-700">Contraseña</label>
                        <Password
                            id="password"
                            v-model="loginForm.password"
                            :toggleMask="true"
                            :feedback="false"
                            inputClass="w-full p-3 border-gray-300 rounded-lg focus:ring-blue-600 focus:border-blue-600"
                            class="w-full"
                            :class="{ 'p-invalid': validationErrors?.password }"
                        />
                        <small v-if="validationErrors?.password" class="text-red-500">
                            <div v-for="message in validationErrors.password" :key="message">
                                {{ message }}
                            </div>
                        </small>
                        
                        <div class="mt-1">
                            <router-link
                                :to="{ name: 'auth.forgot-password' }"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                            >
                                ¿Has olvidado tu contraseña?
                            </router-link>
                        </div>
                    </div>

                    <Button
                        type="submit"
                        label="Iniciar sesión"
                        :loading="processing"
                        :disabled="processing"
                        class="w-full font-bold py-3 rounded-full transition-colors login-button"
                    />

                    <div class="flex items-center gap-2 mt-4">
                        <Checkbox
                            v-model="loginForm.remember"
                            inputId="remember"
                            binary
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-600"
                        />
                        <label for="remember" class="text-sm text-gray-700 cursor-pointer">
                            Mantener mi sesión abierta. <span class="text-blue-600 hover:underline">Más info</span>
                        </label>
                    </div>

                    <div class="border-t border-gray-200 my-6"></div>

                    <div class="text-center">
                        <a href="#" class="text-sm text-gray-600">
                            ¿Eres una empresa? <span class="text-blue-600 font-bold hover:underline">Accede aquí</span>
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        ¿Eres nuevo/a?
                    </h2>

                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <div class="mt-1">
                                <i class="pi pi-file text-xl text-gray-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Crea tu CV</h3>
                                <p class="text-sm text-gray-600">Muestra a las empresas todo tu talento</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="mt-1">
                                <i class="pi pi-check-circle text-xl text-gray-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Inscríbete en las ofertas que te gustan</h3>
                                <p class="text-sm text-gray-600">Y sigue tus candidaturas</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="mt-1">
                                <i class="pi pi-clock text-xl text-gray-400"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Mantén actualizado tu CV</h3>
                                <p class="text-sm text-gray-600">Y dobla las posibilidades de ser contactado por la empresa que quieres</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <router-link :to="{ name: 'auth.register' }">
                        <Button
                            label="Regístrate"
                            class="w-full font-bold py-3 rounded-full transition-colors register-button"
                        />
                    </router-link>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import useAuth from '@/composables/auth';

const { loginForm, validationErrors, processing, submitLogin } = useAuth();
</script>

<style scoped>
:deep(.login-button) {
    background-color: #013C7B !important;
    border: 1px solid #013C7B !important;
    color: #ffffff !important;
}
:deep(.login-button:hover) {
    background-color: #012d5e !important;
    border-color: #012d5e !important;
}

:deep(.register-button) {
    background-color: #ffffff !important;
    border: 1px solid #013C7B !important;
    color: #013C7B !important;
}
:deep(.register-button:hover) {
    background-color: #f0f9ff !important;
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
    border-radius: 9999px; /* Full rounded pill shape */
    justify-content: center;
    width: 100%;
}

:deep(.p-checkbox .p-checkbox-box.p-highlight) {
    background: #013C7B !important;
    border-color: #013C7B !important;
}

:deep(.p-checkbox:not(.p-checkbox-disabled) .p-checkbox-box.p-focus) {
    box-shadow: 0 0 0 2px #dbeafe !important;
    border-color: #013C7B !important;
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
