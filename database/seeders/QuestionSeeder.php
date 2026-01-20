<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/DiagnosticSeeder.php

    // database/seeders/DiagnosticSeeder.php

    public function run()
    {
        $dataPF = [
            'Organização Financeira' => [
                'Você controla suas finanças de alguma forma?' => [
                    'Não faço nenhum controle' => 1,
                    'Controle informal ou esporádico' => 2,
                    'Uso app, planilha ou sistema' => 3,
                ],
                'Você sabe exatamente quanto ganha por mês?' => [
                    'Não sei ao certo' => 1,
                    'Tenho uma estimativa' => 2,
                    'Sei exatamente' => 3,
                ],
            ],

            'Consciência de Gastos' => [
                'Você sabe para onde vai a maior parte do seu dinheiro?' => [
                    'Não faço ideia' => 1,
                    'Tenho uma noção geral' => 2,
                    'Tenho total clareza' => 3,
                ],
                'Você revisa seus gastos mensalmente?' => [
                    'Nunca' => 1,
                    'Às vezes' => 2,
                    'Todo mês' => 3,
                ],
            ],

            'Controle de Dívidas' => [
                'Quanto da sua renda está comprometida com dívidas?' => [
                    'Mais de 50%' => 1,
                    'Entre 20% e 50%' => 2,
                    'Menos de 20% ou nada' => 3,
                ],
                'Você depende de crédito para fechar o mês?' => [
                    'Sempre' => 1,
                    'Às vezes' => 2,
                    'Raramente ou nunca' => 3,
                ],
            ],

            'Reserva e Segurança' => [
                'Você possui reserva de emergência?' => [
                    'Não possuo' => 1,
                    'Tenho menos de 3 meses' => 2,
                    'Tenho 6 meses ou mais' => 3,
                ],
                'Se perdesse sua renda hoje, por quanto tempo sobreviveria?' => [
                    'Menos de 1 mês' => 1,
                    'De 1 a 3 meses' => 2,
                    'Mais de 6 meses' => 3,
                ],
            ],

            'Planejamento' => [
                'Você possui metas financeiras definidas?' => [
                    'Não tenho metas' => 1,
                    'Tenho metas vagas' => 2,
                    'Tenho metas claras e escritas' => 3,
                ],
                'Você planeja seus gastos futuros?' => [
                    'Não planejo' => 1,
                    'Planejo parcialmente' => 2,
                    'Planejo com antecedência' => 3,
                ],
            ],

            'Mentalidade Financeira' => [
                'Como você toma decisões financeiras importantes?' => [
                    'Por impulso ou emoção' => 1,
                    'Analiso um pouco antes' => 2,
                    'Analiso com estratégia' => 3,
                ],
                'Você se sente no controle da sua vida financeira?' => [
                    'Não, me sinto perdido' => 1,
                    'Às vezes no controle' => 2,
                    'Sim, totalmente no controle' => 3,
                ],
            ],

            'Crescimento e Futuro' => [
                'Você investe pensando no longo prazo?' => [
                    'Não invisto' => 1,
                    'Invisto sem estratégia' => 2,
                    'Invisto com foco no futuro' => 3,
                ],
                'Você busca aumentar sua renda?' => [
                    'Não penso nisso' => 1,
                    'Penso, mas não ajo' => 2,
                    'Busco ativamente' => 3,
                ],
            ],

            'Proteção Financeira' => [
                'Você possui seguros adequados à sua realidade (vida, saúde, patrimônio)?' => [
                    'Não possuo nenhum seguro' => 1,
                    'Possuo alguns, mas sem revisão' => 2,
                    'Tenho seguros adequados e revisados' => 3,
                ],
                'Você já passou por um imprevisto financeiro grave nos últimos anos?' => [
                    'Sim, e não estava preparado' => 1,
                    'Sim, mas consegui lidar' => 2,
                    'Não, estou bem protegido' => 3,
                ],
            ],

            'Eficiência Financeira' => [
                'Você sente que seu dinheiro rende pouco em relação ao esforço que faz?' => [
                    'Sim, parece que nunca é suficiente' => 1,
                    'Às vezes sinto isso' => 2,
                    'Não, meu dinheiro é bem direcionado' => 3,
                ],
                'Você revisa contratos, taxas e serviços financeiros que utiliza?' => [
                    'Nunca reviso' => 1,
                    'Reviso raramente' => 2,
                    'Reviso e otimizo regularmente' => 3,
                ],
            ],

            'Disciplina e Consistência' => [
                'Você consegue manter hábitos financeiros saudáveis por longos períodos?' => [
                    'Não, sempre abandono' => 1,
                    'Consigo por algum tempo' => 2,
                    'Sim, é parte da minha rotina' => 3,
                ],
                'Quando surge uma renda extra, você costuma:' => [
                    'Gastar sem planejamento' => 1,
                    'Dividir entre gasto e responsabilidade' => 2,
                    'Direcionar com estratégia' => 3,
                ],
            ],
        ];

        $dataPJ = [
            'Gestão de Caixa' => [
                'Você acompanha o fluxo de caixa do negócio?' => [
                    'Não acompanho' => 1,
                    'Acompanho de forma básica' => 2,
                    'Acompanho diariamente e com projeção' => 3,
                ],
                'Sua empresa sabe quanto pode gastar sem comprometer o caixa?' => [
                    'Não sabe' => 1,
                    'Tem uma noção' => 2,
                    'Sabe exatamente' => 3,
                ],
            ],

            'Previsibilidade de Receita' => [
                'Sua empresa consegue prever faturamento futuro?' => [
                    'Não consegue prever' => 1,
                    'Consegue prever parcialmente' => 2,
                    'Consegue prever com boa precisão' => 3,
                ],
                'A receita é recorrente ou previsível?' => [
                    'Não, é instável' => 1,
                    'Parcialmente previsível' => 2,
                    'Majoritariamente recorrente' => 3,
                ],
            ],

            'Margem e Precificação' => [
                'Você sabe a margem real de lucro dos seus produtos ou serviços?' => [
                    'Não sei' => 1,
                    'Tenho uma estimativa' => 2,
                    'Sei com precisão' => 3,
                ],
                'Sua precificação considera todos os custos e impostos?' => [
                    'Não considera' => 1,
                    'Considera parcialmente' => 2,
                    'Considera tudo corretamente' => 3,
                ],
            ],

            'Controle de Custos' => [
                'Você sabe quais custos são fixos e quais são variáveis?' => [
                    'Não sei diferenciar' => 1,
                    'Sei parcialmente' => 2,
                    'Sei claramente' => 3,
                ],
                'Você revisa custos e despesas regularmente?' => [
                    'Nunca reviso' => 1,
                    'Reviso ocasionalmente' => 2,
                    'Reviso de forma contínua' => 3,
                ],
            ],

            'Endividamento Empresarial' => [
                'O nível de endividamento da empresa é saudável?' => [
                    'Não, é alto e preocupante' => 1,
                    'É controlado, mas limita' => 2,
                    'É saudável ou inexistente' => 3,
                ],
                'As dívidas da empresa ajudam no crescimento?' => [
                    'Não, só tapam buracos' => 1,
                    'Algumas ajudam' => 2,
                    'Sim, são estratégicas' => 3,
                ],
            ],

            'Separação PF x PJ' => [
                'As finanças pessoais e da empresa são separadas?' => [
                    'Não são separadas' => 1,
                    'Separadas parcialmente' => 2,
                    'Totalmente separadas' => 3,
                ],
                'Você retira pró-labore ou distribuição de forma organizada?' => [
                    'Retiro quando preciso' => 1,
                    'Tenho algum padrão' => 2,
                    'Existe regra clara' => 3,
                ],
            ],

            'Gestão e Indicadores' => [
                'A empresa acompanha indicadores financeiros?' => [
                    'Não acompanha' => 1,
                    'Acompanha poucos indicadores' => 2,
                    'Acompanha indicadores-chave' => 3,
                ],
                'As decisões financeiras são baseadas em dados?' => [
                    'Não, são reativas' => 1,
                    'Às vezes' => 2,
                    'Sim, são estratégicas' => 3,
                ],
            ],

            'Risco e Segurança' => [
                'A empresa conseguiria operar se perdesse um grande cliente?' => [
                    'Não sobreviveria' => 1,
                    'Sobreviveria com dificuldade' => 2,
                    'Conseguiria se adaptar' => 3,
                ],
                'A empresa possui reserva de caixa?' => [
                    'Não possui' => 1,
                    'Possui pequena reserva' => 2,
                    'Possui reserva adequada' => 3,
                ],
            ],

            'Dependência do Dono' => [
                'A empresa depende diretamente de você para funcionar?' => [
                    'Depende totalmente' => 1,
                    'Depende em partes' => 2,
                    'Funciona sem minha presença constante' => 3,
                ],
                'Processos financeiros estão documentados?' => [
                    'Não existem processos' => 1,
                    'Existem informalmente' => 2,
                    'Estão documentados e claros' => 3,
                ],
            ],

            'Crescimento e Escala' => [
                'A empresa tem capacidade financeira para crescer?' => [
                    'Não tem capacidade' => 1,
                    'Tem capacidade limitada' => 2,
                    'Tem estrutura para escalar' => 3,
                ],
                'O crescimento é planejado financeiramente?' => [
                    'Não é planejado' => 1,
                    'Planejado parcialmente' => 2,
                    'Planejado com estratégia' => 3,
                ],
            ],

        ];

        foreach ($dataPF as $catName => $questions) {
            $category = Category::create(['name' => $catName, 'type' => 'pf']);
            $count = 0;
            foreach ($questions as $qText => $options) {
                $count++;
                $question = Question::create([
                    'category_id' => $category->id,
                    'text' => $qText,
                    'order' => $count,
                ]);

                foreach ($options as $oText => $points) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => $oText,
                        'points' => $points,
                    ]);
                }
            }
        }

        foreach ($dataPJ as $catName => $questions) {
            $category = Category::create(['name' => $catName, 'type' => 'pj']);
            $count = 0;
            foreach ($questions as $qText => $options) {
                $count++;
                $question = Question::create([
                    'category_id' => $category->id,
                    'text' => $qText,
                    'order' => $count,
                ]);

                foreach ($options as $oText => $points) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => $oText,
                        'points' => $points,
                    ]);
                }
            }
        }
    }
}
