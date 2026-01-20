<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { Lock, Mail, Loader2 } from 'lucide-vue-next';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        // Limpa a senha se o login falhar
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Acesso Admin" />
    <div class="min-h-screen bg-slate-100 flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-[2.5rem] shadow-2xl p-10 border border-slate-200">
            <div class="text-center mb-8">
                <div class="inline-flex p-4 bg-indigo-600 rounded-2xl text-white mb-4">
                    <Lock class="w-8 h-8" />
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Área Restrita</h1>
                <p class="text-slate-500">Entre com suas credenciais de acesso</p>
            </div>

            <div v-if="form.errors.email" class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-xl">  
                <p class="text-sm text-rose-700 font-bold">
                    {{ form.errors.email }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <div class="relative">
                        <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <input v-model="form.email" type="email" placeholder="E-mail" class="w-full p-4 pl-12 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-500 transition-all" :class="{ 'border-rose-500': form.errors.email }" required />
                    </div>
                </div>
                <div>
                    <div class="relative">
                        <Lock class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                        <input v-model="form.password" type="password" placeholder="Senha" class="w-full p-4 pl-12 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-500 transition-all" :class="{ 'border-rose-500': form.errors.password }" required />
                    </div>
                </div>

                <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <Loader2 v-if="form.processing" class="w-5 h-5 animate-spin" />
                    <span>Acessar Painel</span>
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>
    @reference "../../../css/app.css";
.admin-input { @apply w-full p-4 pl-12 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-500 transition-all; }
</style>
