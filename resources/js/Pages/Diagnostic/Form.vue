<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import {
    ChevronRight, ChevronLeft, Check, Wallet,
    Send, Mail, CheckCircle, ShieldCheck, Loader2, Play
} from 'lucide-vue-next';

const props = defineProps({
    questions: Array,
    type: String // 'pf' ou 'pj' vindo do controller
});

// Estados de Fluxo
const step = ref(-1); // -1: Landing, 0 a N: Perguntas, N+1: Form Lead
const submitted = ref(false);
const lockNavigation = ref(false);

const form = useForm({
    lead: {
        name: '',
        email: '',
        phone: '',
        type: props.type,
        consent: false,
        captcha_token: ''
    },
    answers: {}
});

// --- LÓGICA DE VALIDAÇÃO E MÁSCARA ---

const isEmailValid = computed(() => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(form.lead.email);
});

const formatPhone = (value) => {
    if (!value) return "";
    value = value.replace(/\D/g, "");
    value = value.replace(/(\d{2})(\d)/, "($1) $2");
    value = value.replace(/(\d{5})(\d)/, "$1-$2");
    return value.length > 15 ? value.substring(0, 15) : value;
};

watch(() => form.lead.phone, (newVal) => {
    form.lead.phone = formatPhone(newVal);
});

const canSubmit = computed(() => {
    return form.lead.name.length > 3 &&
           isEmailValid.value &&
           form.lead.phone.length >= 14 &&
           form.lead.consent &&
           !form.processing;
});

// --- NAVEGAÇÃO ---

const next = () => step.value++;
const back = () => { if (!lockNavigation.value) step.value--; };

const selectOption = (questionId, optionId) => {
    form.answers[questionId] = optionId;
    setTimeout(() => next(), 300);
};

const submit = () => {
    lockNavigation.value = true;
    form.post(route('diagnostic.store'), {
        preserveScroll: true,
        onSuccess: () => { submitted.value = true; }
    });
};
</script>

<template>
    <Head title="Diagnóstico Financeiro Profissional" />

    <div class="main-container">
        <div class="dark-overlay"></div>

        <div class="logo-wrapper animate-in fade-in duration-1000">
            <div class="flex items-center gap-3">
                <div class="logo-icon"><img src="/assets/images/plannfinn.png" alt=""></div>
                <div class="flex flex-col">
                    <span class="logo-text">Plannfinn Family Office</span>
                    <span class="logo-sub">Negócios e Finanças</span>
                </div>
            </div>
        </div>

        <div class="glass-card" :class="{ 'scale-95 opacity-50': form.processing }">

            <form @submit.prevent>

                <div v-if="step === -1" class="step-content text-center">
                    <h1 v-if="type=='pf'" class="text-4xl font-black text-slate-900 mb-6">
                        Descubra o potencial da sua saúde <span class="text-indigo-600">financeira.</span>
                    </h1>
                    <h1 v-else class="text-4xl font-black text-slate-900 mb-6">
                        Descubra o potencial da saúde <span class="text-indigo-600">financeira</span> da sua empresa.
                    </h1>
                    <p class="text-slate-500 text-lg mb-10">
                        Preparamos um questionário estratégico para identificar gargalos e oportunidades no seu perfil de {{ type === 'pf' ? 'Pessoa Física' : 'Empresa' }}.
                    </p>
                    <button @click="next" class="start-btn group">
                        Iniciar Questionário <Play class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" />
                    </button>
                </div>

                <div v-else-if="step >= 0 && step < questions.length" class="step-content">
                    <div class="progress-bar-container">
                        <div class="progress-fill" :style="`width: ${((step + 1) / questions.length) * 100}%`"></div>
                    </div>

                    <span class="category-tag">{{ questions[step].category?.name }}</span>
                    <h2 class="question-text">{{ questions[step].text }}</h2>

                    <div class="options-grid">
                        <button v-for="opt in questions[step].options" :key="opt.id" type="button"
                            @click="selectOption(questions[step].id, opt.id)"
                            :class="form.answers[questions[step].id] === opt.id ? 'opt-active cursor-pointer' : 'opt-inactive cursor-pointer'">
                            {{ opt.text }}
                            <Check v-if="form.answers[questions[step].id] === opt.id" class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <div v-else-if="step === questions.length && !submitted" class="step-content">
                    <h2 class="text-3xl font-black text-slate-900 mb-2">Quase lá!</h2>
                    <p class="text-slate-500 mb-8">Para seguir nos informe seus dados para envio do relatório completo para seu melhor email</p>

                    <div class="space-y-4">
                        <input v-model="form.lead.name" type="text" placeholder="Seu nome completo" class="form-input" />

                        <div class="relative">
                            <input v-model="form.lead.email" type="email" placeholder="Seu e-mail principal"
                                :class="['form-input', form.lead.email && !isEmailValid ? 'border-red-500 bg-red-50' : '']" />
                            <p v-if="form.lead.email && !isEmailValid" class="text-red-500 text-xs mt-1 ml-2 font-bold">Por favor, insira um e-mail válido.</p>
                        </div>

                        <input v-model="form.lead.phone" type="text" placeholder="(00) 00000-0000" class="form-input" />

                        <label class="flex items-start gap-3 p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-slate-100 transition-colors">
                            <input v-model="form.lead.consent" type="checkbox" class="mt-1 w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-sm text-slate-600 leading-tight font-medium">
                                Aceito os termos de uso e autorizo o envio do diagnóstico para o meu e-mail.
                            </span>
                        </label>

                        <div class="captcha-placeholder">
                            <ShieldCheck class="w-5 h-5 text-indigo-400" />
                            <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">Proteção Anti-Bot Ativa</span>
                        </div>
                    </div>

                    <div class="flex flex-col mt-8 gap-4">
                        <button @click="submit" :disabled="!canSubmit" class="submit-btn">
                            <span v-if="form.processing" class="flex items-center gap-2"><Loader2 class="animate-spin" /> Processando...</span>
                            <span v-else class="flex items-center gap-2">Gerar e Enviar Diagnóstico <Send class="w-5 h-5" /></span>
                        </button>
                        <button v-if="!lockNavigation" @click="back" class="text-slate-400 font-bold text-sm">Voltar e revisar respostas</button>
                    </div>
                </div>

                <div v-else class="step-content text-center py-10">
                    <div class="success-icon"><CheckCircle class="w-20 h-20" /></div>
                    <h1 class="text-3xl font-black text-slate-900 mb-4">Relatório Enviado!</h1>
                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Verifique sua caixa de entrada (e a pasta de spam) em instantes.
                        O diagnóstico detalhado já está a caminho.
                    </p>
                </div>

            </form>
        </div>
    </div>
</template>

<style scoped>
@reference "../../../css/app.css";

/* Layout e Fundo */
.main-container {
    @apply min-h-screen w-full flex flex-col items-center justify-center p-4 relative overflow-hidden bg-[#0f1115];
}

.dark-overlay {
    @apply absolute inset-0 bg-gradient-to-br from-blue-950 via-gray-900 to-blue-950 backdrop-blur-3xl pointer-events-none;
}

/* Caixa Principal - Foco em Visibilidade */
.glass-card {
    @apply w-full max-w-2xl bg-white rounded-[2rem] shadow-[0_30px_100px_rgba(0,0,0,0.5)]
    z-10 relative border border-slate-200 transition-all duration-500;
}

.step-content { @apply p-8 md:p-14 animate-in fade-in slide-in-from-bottom-4 duration-500; }

/* Logo */
.logo-wrapper { @apply mb-10 z-10; }
.logo-icon { @apply object-cover w-14 rounded-full overflow-hidden; }
.logo-text { @apply text-white font-black text-2xl tracking-tighter leading-none; }
.logo-sub { @apply text-indigo-400 text-[10px] font-bold uppercase tracking-[0.3em]; }

/* Inputs e UI */
.form-input {
    @apply w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none
    focus:border-indigo-500 focus:bg-white transition-all text-slate-700 font-medium;
}

.start-btn {
    @apply bg-indigo-600 hover:bg-indigo-700 text-white px-12 py-5 rounded-[1.5rem]
    font-black text-xl shadow-2xl shadow-indigo-500/40 flex items-center mx-auto transition-all cursor-pointer;
}

.submit-btn {
    @apply w-full py-5 rounded-2xl font-black text-xl flex items-center justify-center transition-all
    disabled:bg-slate-100 disabled:text-slate-300 bg-emerald-500 hover:bg-emerald-600 text-white shadow-xl shadow-emerald-500/20 cursor-pointer;
}

.category-tag { @apply px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest mb-4 inline-block; }
.question-text { @apply text-2xl md:text-3xl font-bold text-slate-800 mb-8 leading-tight; }

.options-grid { @apply grid gap-3; }
.opt-inactive { @apply p-5 bg-white border-2 border-slate-100 rounded-2xl text-left font-bold text-slate-600 hover:border-indigo-200 transition-all flex justify-between items-center; }
.opt-active { @apply p-5 bg-indigo-600 border-2 border-indigo-600 rounded-2xl text-left font-black text-white shadow-lg shadow-indigo-100 flex justify-between items-center scale-[1.02]; }

.progress-bar-container { @apply w-full h-1.5 bg-slate-100 rounded-full mb-10 overflow-hidden; }
.progress-fill { @apply h-full bg-indigo-600 transition-all duration-500; }

.captcha-placeholder { @apply flex items-center justify-center gap-2 p-3 border-2 border-dashed border-slate-200 rounded-2xl; }

.success-icon { @apply inline-flex p-6 bg-emerald-50 text-emerald-500 rounded-full mb-6; }

@media (max-width: 640px) {
    .step-content { @apply p-6; }
    .question-text { @apply text-xl; }
}
</style>
