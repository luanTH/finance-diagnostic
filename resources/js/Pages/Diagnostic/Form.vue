<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ChevronRight, ChevronLeft, Check, Wallet, Building, User, Target } from 'lucide-vue-next';

// Agora recebemos as perguntas com o relacionamento 'options' carregado
const props = defineProps({
    questions: Array
});

// Estado do formulário
const step = ref(0);
const form = useForm({
    lead: { name: '', email: '', phone: '', type: 'pf' },
    // Armazena { question_id: option_id }
    answers: {}
});

// Lógica de Navegação
const totalSteps = computed(() => props.questions.length + 1);
const progress = computed(() => (step.value / (totalSteps.value - 1)) * 100);

// Atalho para a pergunta atual (ajusta o índice pois o step 0 é o lead)
const currentQuestion = computed(() => props.questions[step.value - 1]);

const next = () => {
    if (step.value < totalSteps.value - 1) step.value++;
};
const back = () => {
    if (step.value > 0) step.value--;
};

const selectOption = (questionId, optionId) => {
    form.answers[questionId] = optionId;
    // Pequeno delay para o usuário ver o feedback visual antes de passar
    setTimeout(() => {
        next();
    }, 300);
};

const submit = () => {
    form.post(route('diagnostic.store'), {
        preserveScroll: true,
        onSuccess: () => console.log('Diagnóstico enviado com sucesso!')
    });
};
</script>

<template>
    <Head title="Diagnóstico Financeiro" />

    <div class="min-h-screen w-full bg-slate-100 flex flex-col items-center justify-center p-4">

        <div class="w-full max-w-2xl mb-6">
            <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full bg-indigo-600 transition-all duration-500 ease-out" :style="`width: ${progress}%`"></ div>
            </div>
        </div>

        <div class="w-full max-w-2xl bg-white rounded-[2.5rem] shadow-2xl p-8 md:p-14 border border-slate-200">
            <form @submit.prevent>

                <div v-if="step === 0" class="animate-in fade-in slide-in-from-right-8 duration-500">
                    <div class="flex items-center gap-3 mb-6 text-indigo-600">
                        <Wallet class="w-8 h-8" />
                        <span class="font-bold tracking-tighter text-xl">FINANCE_DIAG</span>
                    </div>
                    <h1 class="text-3xl font-extrabold text-slate-900 leading-tight mb-4">
                        Olá! Vamos iniciar sua <span class="text-indigo-600">análise financeira.</span>
                    </h1>
                    <p class="text-slate-500 mb-8">Preencha seus dados para começarmos as perguntas personalizadas.</p>

                    <div class="space-y-4">
                        <div class="group">
                            <label class="text-xs font-bold text-slate-400 uppercase ml-2 mb-1 block">Nome Completo</label>
                            <input v-model="form.lead.name" type="text" placeholder="Ex: João Silva" class="custom-input" />
                        </div>
                        <div class="group">
                            <label class="text-xs font-bold text-slate-400 uppercase ml-2 mb-1 block">E-mail</label>
                            <input v-model="form.lead.email" type="email" placeholder="seu@email.com" class="custom-input" />
                        </div>
                        <div class="group">
                            <label class="text-xs font-bold text-slate-400 uppercase ml-2 mb-1 block">Telefone / WhatsApp</label>
                            <input v-model="form.lead.phone" type="text" placeholder="(00) 00000-0000" class="custom-input" />
                        </div>

                        <div class="flex gap-4 pt-2">
                            <button type="button" @click="form.lead.type = 'pf'" :class="form.lead.type === 'pf' ? 'type-btn-active' : 'type-btn-inactive'">
                                <User class="w-5 h-5" /> Pessoa Física
                            </button>
                            <button type="button" @click="form.lead.type = 'pj'" :class="form.lead.type === 'pj' ? 'type-btn-active' : 'type-btn-inactive'">
                                <Building class="w-5 h-5" /> Empresa / PJ
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="animate-in fade-in slide-in-from-right-8 duration-500">
                    <div class="flex justify-between items-center mb-6">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full font-bold text-xs uppercase tracking-wider">
                            {{ currentQuestion.category?.name || 'Geral' }}
                        </span>
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Questão {{ step }} de {{ questions.length }}</p>
                    </div>

                    <h2 class="text-2xl font-bold text-slate-800 mb-8 leading-snug">
                        {{ currentQuestion.text }}
                    </h2>

                    <div class="grid gap-3">
                        <button
                            v-for="option in currentQuestion.options"
                            :key="option.id"
                            type="button"
                            @click="selectOption(currentQuestion.id, option.id)"
                            :class="form.answers[currentQuestion.id] === option.id ? 'option-btn-active' : 'option-btn-inactive'"
                        >
                            <span class="text-left pr-4">{{ option.text }}</span>
                            <div class="min-w-[24px]">
                                <Check v-if="form.answers[currentQuestion.id] === option.id" class="w-6 h-6" />
                                <div v-else class="w-6 h-6 rounded-full border-2 border-slate-200"></div>
                            </div>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-12 pt-8 border-t border-slate-50">
                    <button type="button" v-if="step > 0" @click="back" class="flex items-center text-slate-400 hover:text-slate-600 font-medium transition-colors">
                        <ChevronLeft class="w-5 h-5 mr-1" /> Voltar
                    </button>
                    <div v-else></div>

                    <button v-if="step === 0" type="button" @click="next" :disabled="!form.lead.name || !form.lead.email" class="primary-btn disabled:opacity-50 disabled:grayscale">
                        Começar Diagnóstico <ChevronRight class="ml-2 w-5 h-5" />
                    </button>

                    <button v-else-if="step === questions.length" type="button" @click="submit" :disabled="form.processing" class="success-btn">
                        <div v-if="form.processing" class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            IA Analisando seus dados...
                        </div>
                        <span v-else class="flex items-center text-lg">Gerar Meu Diagnóstico <Check class="ml-2 w-6 h-6" /></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@reference "../../../css/app.css";

.custom-input { @apply w-full p-4 bg-slate-50 border-2 border-slate-100 rounded-2xl outline-none focus:border-indigo-500 focus:bg-white transition-all text-slate-700 text-lg; }
.primary-btn { @apply bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-bold flex items-center shadow-lg shadow-indigo-200 transition-all active:scale-95; }
.success-btn { @apply bg-emerald-500 hover:bg-emerald-600 text-white px-10 py-5 rounded-2xl font-extrabold flex items-center shadow-xl shadow-emerald-200 transition-all active:scale-95 disabled:opacity-50; }
.type-btn-active { @apply flex-1 flex items-center justify-center gap-2 p-4 bg-indigo-50 border-2 border-indigo-600 text-indigo-700 rounded-xl font-bold transition-all; }
.type-btn-inactive { @apply flex-1 flex items-center justify-center gap-2 p-4 bg-white border-2 border-slate-100 text-slate-400 rounded-xl font-medium hover:border-slate-200 transition-all; }
.option-btn-active { @apply flex items-center justify-between p-6 bg-indigo-600 text-white rounded-2xl font-bold border-2 border-indigo-600 transition-all transform scale-[1.02] shadow-md; }
.option-btn-inactive { @apply flex items-center justify-between p-6 bg-white text-slate-600 rounded-2xl font-medium border-2 border-slate-100 hover:border-indigo-200 hover:bg-slate-50 transition-all; }
</style>
