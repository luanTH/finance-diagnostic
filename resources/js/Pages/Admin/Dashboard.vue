<script setup>
import { ref, watch } from 'vue';
import { router, Link, Head } from '@inertiajs/vue3';
import { Search, User, Building, Eye, LogOut } from 'lucide-vue-next';
import debounce from 'lodash/debounce';
console.log('opa')
const props = defineProps({ leads: Object, filters: Object });

const search = ref(props.filters.search);
const type = ref(props.filters.type);

// Filtro em tempo real com debounce para não sobrecarregar o banco
watch([search, type], debounce(() => {
    router.get(route('admin.dashboard'), { search: search.value, type: type.value }, { preserveState: true, replace: true });
}, 300));

const logout = () => router.post(route('logout'));

console.log('leads', props.leads.links)
</script>

<template>
    <Head title="Dashboard Admin" />
    <div class="min-h-screen bg-slate-50 flex">
        <main class="flex-1 p-8">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <h1 class="text-3xl font-black text-slate-900">Dashboard</h1>
                        <p class="text-slate-500">Gerencie seus {{ leads.total }} leads capturados</p>
                    </div>
                    <button @click="logout" class="flex items-center gap-2 text-rose-500 font-bold hover:bg-rose-50 p-3 rounded-xl transition-all cursor-pointer">
                        <LogOut class="w-5 h-5" /> Sair
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="relative col-span-2">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 w-5 h-5" />
                        <input v-model="search" type="text" placeholder="Buscar por nome ou e-mail..." class="w-full pl-12 p-4 rounded-2xl border-none shadow-sm focus:ring-2 focus:ring-indigo-500" />
                    </div>
                    <select v-model="type" class="p-4 rounded-2xl border-none shadow-sm focus:ring-2 focus:ring-indigo-500">
                        <option :value="undefined">Todos os tipos</option>
                        <option value="pf">Pessoa Física</option>
                        <option value="pj">Empresa / PJ</option>
                    </select>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-wider">Lead</th>
                                <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-wider">Tipo</th>
                                <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-wider">Último Score</th>
                                <th class="p-6 text-xs font-bold uppercase text-slate-400 tracking-wider text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="lead in leads.data" :key="lead.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-6">
                                    <div class="font-bold text-slate-800">{{ lead.name }}</div>
                                    <div class="text-sm text-slate-500">{{ lead.email }}</div>
                                    <div class="text-sm text-slate-500">{{ lead.phone }}</div>
                                </td>
                                <td class="p-6">
                                    <span :class="lead.type === 'pf' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'" class="px-3 py-1 rounded-full text-xs font-black uppercase">
                                        {{ lead.type }}
                                    </span>
                                </td>
                                <td class="p-6">
                                    <div v-if="lead.diagnoses.length" class="flex items-center gap-2">
                                        <div class="h-2 w-16 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-500" :style="`width: ${ (lead.diagnoses[0].total_score / 60) * 100 }%`"></div>
                                        </div>
                                        <span class="font-bold text-indigo-600">{{ lead.diagnoses[0].total_score }} pts</span>
                                    </div>
                                    <span v-else class="text-slate-300 text-sm italic">Nenhum teste</span>
                                </td>
                                <td class="p-6 text-right">
                                    <Link :href="route('admin.leads.show', lead.id)" class="inline-flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-800 transition-all">
                                        <Eye class="w-4 h-4" /> Detalhes
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-center gap-2">
                    <Link v-for="link in leads.links" :key="link.label" :href="link.url || '#'" v-html="link.label" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-sm transition-all" :class="{ 'bg-indigo-600 text-black border-indigo-600': link.active, 'opacity-50 cursor-not-allowed': !link.url }" />
                </div>
            </div>
        </main>
    </div>
</template>
