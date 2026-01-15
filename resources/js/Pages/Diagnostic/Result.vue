<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    CheckCircle2, AlertCircle, TrendingUp, Target,
    ArrowRight, Download, RefreshCcw, Lightbulb
} from 'lucide-vue-next';

const props = defineProps({
    report: Object,
    lead: Object
});

// Helper para cores dinâmicas
const getScoreColor = (percent) => {
    if (percent >= 70) return 'text-emerald-500 bg-emerald-50 border-emerald-100';
    if (percent >= 40) return 'text-amber-500 bg-amber-50 border-amber-100';
    return 'text-rose-500 bg-rose-50 border-rose-100';
};
</script>

<template>
    <Head title="Seu Resultado" />

    <div class="min-h-screen bg-slate-50 py-12 px-4">
        <div class="max-w-4xl mx-auto">

            <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-xl border border-slate-200 mb-8 text-center">
                <div class="inline-flex p-3 rounded-2xl bg-indigo-50 text-indigo-600 mb-6">
                    <TrendingUp class="w-8 h-8" />
                </div>
                <h1 class="text-3xl font-black text-slate-900 mb-4">
                    Pronto, {{ lead.name.split(' ')[0] }}! <br/> Aqui está seu diagnóstico.
                </h1>
                <p class="text-xl text-slate-600 italic leading-relaxed">
                    "{{ report.frase_atual }}"
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-emerald-50 border border-emerald-100 p-8 rounded-[2rem]">
                    <div class="flex items-center gap-3 text-emerald-700 font-bold mb-4">
                        <CheckCircle2 class="w-6 h-6" /> Pontos Fortes
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(ponto, index) in report.pontos_fortes" :key="index" class="text-emerald-900 leading-relaxed flex gap-2">
                            <span class="text-emerald-400">•</span> <span v-html="ponto"></span>
                        </li>
                    </ul>
                </div>

                <div class="bg-amber-50 border border-amber-100 p-8 rounded-[2rem]">
                    <div class="flex items-center gap-3 text-amber-700 font-bold mb-4">
                        <AlertCircle class="w-6 h-6" /> Pontos de Atenção
                    </div>
                    <ul class="space-y-3">
                        <li v-for="(ponto, index) in report.pontos_desenvolver" :key="index" class="text-amber-900 leading-relaxed flex gap-2">
                            <span class="text-amber-400">•</span> <span v-html="ponto"></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="space-y-6 mb-8">
                <h3 class="text-xl font-bold text-slate-800 ml-4">Análise por Pilares</h3>
                <div v-for="diag in report.diagnosticos_especificos" :key="diag.tema"
                    class="bg-white p-6 md:p-8 rounded-[2rem] shadow-sm border border-slate-200">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex-1">
                            <span class="text-xs font-black uppercase tracking-widest text-indigo-500 mb-2 block">
                                {{ diag.tema }}
                            </span>
                            <h4 class="text-lg font-bold text-slate-800 mb-2">{{ diag.diag }}</h4>
                            <div class="flex items-start gap-2 text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <Lightbulb class="w-5 h-5 text-amber-500 shrink-0" />
                                <p class="text-sm"><strong>Ação Recomendada:</strong> {{ diag.solucao }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-900 rounded-[3rem] p-10 md:p-14 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <span class="bg-indigo-500/30 text-indigo-100 px-4 py-1 rounded-full text-xs font-bold uppercase mb-6 inline-block">
                        Veredito Geral
                    </span>
                    <h2 class="text-3xl font-bold mb-6 leading-tight">
                        {{ report.geral }}
                    </h2>

                    <div class="flex flex-wrap gap-4 mt-8">
                        <button @click="window.print()" class="flex items-center gap-2 bg-white text-indigo-900 px-8 py-4 rounded-2xl font-bold hover:bg-indigo-50 transition-all">
                            <Download class="w-5 h-5" /> Baixar PDF
                        </button>
                        <Link :href="route('diagnostic.index')" class="flex items-center gap-2 bg-indigo-700 text-white px-8 py-4 rounded-2xl font-bold hover:bg-indigo-600 transition-all">
                            <RefreshCcw class="w-5 h-5" /> Refazer Teste
                        </Link>
                    </div>
                </div>

                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl"></div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Adicione estilos para impressão */
@media print {
    .no-print { display: none; }
    .bg-slate-50 { background: white !important; }
}
</style>
