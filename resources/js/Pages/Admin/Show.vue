<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, Calendar, Phone, Mail } from 'lucide-vue-next';

defineProps({ lead: Object });
</script>

<template>
    <Head :title="`Respostas: ${lead.name}`" />
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-4xl mx-auto">
            <Link :href="route('admin.dashboard')" class="inline-flex items-center text-slate-500 hover:text-indigo-600 font-bold mb-8 transition-colors">
                <ChevronLeft class="w-5 h-5 mr-1" /> Voltar para a lista
            </Link>

            <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-200 mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 mb-2">{{ lead.name }}</h1>
                        <div class="flex flex-wrap gap-4 text-slate-500">
                            <span class="flex items-center gap-1"><Mail class="w-4 h-4" /> {{ lead.email }}</span>
                            <span class="flex items-center gap-1"><Phone class="w-4 h-4" /> {{ lead.phone }}</span>
                            <span class="flex items-center gap-1"><Calendar class="w-4 h-4" /> {{ new Date(lead.created_at).toLocaleDateString() }} às {{ new Date(lead.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex justify-between">
                    <h3 class="text-xl font-bold text-slate-800 ml-2">Detalhamento das Respostas</h3>
                    <a class="bg-gray-800 text-white py-2 px-8 rounded-xl" :href="route('admin.pdfView',lead.id)" target="_blank">Visualizar relatório</a>
                </div>
                <div v-for="answer in lead.answers" :key="answer.id" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-xs font-black text-indigo-500 uppercase tracking-widest mb-2">{{ answer.question.category.name }}</p>
                    <h4 class="text-lg font-bold text-slate-800 mb-4">{{ answer.question.text }}</h4>
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 text-indigo-900 font-medium">
                        Resposta: <span class="font-bold">{{ answer.option.text }}</span>
                        <span class="ml-2 text-indigo-400 text-sm">({{ answer.option.points }} pts)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
