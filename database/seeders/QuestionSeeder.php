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
            'Fluxo de Caixa e Organização' => [
                'Qual frase melhor descreve o fechamento do seu mês financeiro?' => [
                    'Gasto mais do que ganho e entro no crédito/cheque especial' => 1, // -> Gasta mais do que ganha
                    'Gasto tudo o que ganho, não sobra nada (equilíbrio no zero)' => 2, // -> Sem capacidade de poupar
                    'Gasto menos do que ganho e sempre sobra para investir' => 3,     // -> Gasta menos do que ganha
                ],
                'Como você realiza o acompanhamento e o registro da sua vida financeira hoje?' => [
                    'Não utilizo nenhuma ferramenta ou acompanhamento' => 1,          // -> Desorganização financeira
                    'Acompanho apenas pelo extrato bancário ou anotações vagas' => 2,
                    'Utilizo ferramentas dedicadas como planilhas, apps ou sistemas' => 3, // -> Organização financeira
                ],
                'Qual o nível de detalhamento do seu controle de gastos diários?' => [
                    'Não faço ideia para onde o dinheiro vai' => 1,
                    'Tenho uma noção geral, mas sem categorização clara' => 2,
                    'Registro e categorizo 100% das minhas entradas e saídas' => 3,
                ],
                'Com que frequência você utiliza o cartão de crédito para despesas básicas (mercado/contas)?' => [
                    'Sempre, e muitas vezes parcelo a fatura ou pago o mínimo' => 1, // -> Quitação de Dívidas (Prioridade)
                    'Uso para acumular pontos, mas às vezes perco o controle' => 2,
                    'Uso estrategicamente e pago sempre o valor integral' => 3,
                ],
            ],

            'Capacidade de Poupança e Reserva' => [
                'Atualmente, qual percentual da sua renda líquida você consegue poupar?' => [
                    '0% (Não consigo poupar nada)' => 1,                  // -> Sem capacidade de poupar
                    'Entre 5% e 15%' => 2,                                // -> Baixo potencial de poupar
                    'Acima de 20% da minha renda' => 3,                   // -> Alto potencial de poupar
                ],
                'Se sua principal fonte de renda parasse hoje, por quanto tempo sua reserva manteria seu padrão?' => [
                    'Menos de 1 mês (Sobrevivência imediata em risco)' => 1, // -> Baixa Liquidez
                    'De 3 a 5 meses (Reserva em formação)' => 2,
                    'Mais de 6 meses (Reserva de emergência sólida)' => 3,   // -> Alta Liquidez Patrimonial
                ],
            ],

            'Perfil e Experiência em Investimentos' => [
                'Onde está concentrada a maior parte do seu dinheiro hoje?' => [
                    'Apenas na Poupança ou conta corrente' => 1,             // -> Baixa experiência com investimentos
                    'CDBs, Tesouro Direto ou Previdência Privada' => 2,      // -> Experiência básica
                    'Ações (Bolsa de Valores), FIIs ou Criptoativos' => 3,  // -> Experiência com investimentos
                ],
                'Qual o seu nível de conhecimento sobre o mercado financeiro?' => [
                    'Totalmente leigo, não entendo as siglas e produtos' => 1,         // -> Educação Financeira (Prioridade)
                    'Entendo o básico de Renda Fixa e títulos públicos' => 2,
                    'Domino análise de ativos e diversificação de risco' => 3, // -> Educação financeira (Ponto forte)
                ],
                'Você sente que sua carteira de investimentos atual está correta para seus objetivos?' => [
                    'Não sei avaliar (Provavelmente inadequada)' => 1,      // -> Carteira de Investimento Inadequada
                    'Sinto que poderia render mais ou ser mais segura' => 2,
                    'Sim, está otimizada e revisada constantemente' => 3,
                ],
            ],

            'Aposentadoria e Longevidade' => [
                'Qual a sua estratégia atual para garantir sua renda na terceira idade?' => [
                    'Conto apenas com o INSS/Previdência Social' => 1,      // -> Planejamento da Aposentadoria (Prioridade)
                    'Tenho uma reserva, mas sem cálculo de rentabilidade futura' => 2,
                    'Possuo ativos geradores de renda passiva (aluguel/dividendos)' => 3,
                ],
                'Você sabe exatamente quanto precisa ter acumulado para parar de trabalhar?' => [
                    'Não tenho a menor ideia do valor necessário' => 1,
                    'Tenho uma estimativa vaga' => 2,
                    'Sim, tenho o cálculo da minha independência financeira' => 3,
                ],
            ],

            'Gestão de Dívidas' => [
                'Qual o peso das parcelas de dívidas (empréstimos/financiamentos) no seu orçamento?' => [
                    'Compromete mais de 30% da minha renda' => 1,          // -> Quitação de Dívidas
                    'Compromete entre 10% e 30%' => 2,
                    'Não possuo dívidas ou comprometimento irrelevante' => 3,
                ],
            ],

            'Proteção e Gestão de Riscos' => [
                'No caso de um imprevisto grave de saúde ou necessidade de ausência profissional, como ficaria o equilíbrio financeiro da sua família?' => [
                    'Impacto devastador (não temos proteção estruturada)' => 1, // -> Baixa cobertura de riscos
                    'Impacto moderado (conseguiríamos lidar por pouco tempo)' => 2,
                    'Baixo impacto (temos coberturas e seguros adequados)' => 3, // -> Boa cobertura de riscos
                ],
                'Seus seguros e ferramentas de proteção são revisados anualmente por um especialista?' => [
                    'Nunca revisei ou não possuo proteção' => 1,                    // -> Proteção Familiar e cobertura de Riscos
                    'Reviso apenas pontualmente' => 2,
                    'Reviso estrategicamente conforme meu patrimônio evolui' => 3,
                ],
            ],

            'Patrimônio e Sucessão' => [
                'Como está a divisão do seu patrimônio entre bens imóveis e bens financeiros?' => [
                    'Quase tudo em imóveis/carros (Baixa liquidez)' => 1,  // -> Realocação Patrimonial (Prioridade)
                    'Equilíbrio entre bens de uso e investimentos' => 2,
                    'Maior parte em ativos financeiros com boa liquidez' => 3,     // -> Acumulo Patrimonial
                ],
                'Você já possui uma estratégia formalizada para a sucessão do seu patrimônio aos herdeiros?' => [
                    'Não possuo nenhuma estratégia (vai para inventário)' => 1,             // -> Planejamento Sucessório (Prioridade)
                    'Já tratei sobre o assunto, mas não formalizei legalmente' => 2,
                    'Já possuo estrutura jurídica e sucessória montada' => 3,
                ],
                'Você busca formas de otimizar legalmente o pagamento de impostos sobre seus bens e rendimentos?' => [
                    'Não faço nenhum tipo de planejamento tributário' => 1,        // -> Planejamento Tributário (Prioridade)
                    'Busco apenas a declaração completa, mas sem estratégia' => 2,
                    'Faço planejamento tributário recorrente com especialistas' => 3,
                ],
            ],

            'Projetos e Objetivos de Vida' => [
                'Quantas metas de alto valor você planeja realizar nos próximos 2 anos?' => [
                    'Muitas metas sem capital provisionado' => 1,      // -> Muito Fluxo de Projetos
                    'Algumas metas com planejamento em andamento' => 2,
                    'Poucas metas ou todas já devidamente provisionadas' => 3,         // -> Pouco Fluxo de Projetos
                ],
                'Qual o valor total estimado dos seus projetos de curto prazo em relação à sua renda?' => [
                    'Um valor muito alto que exige grande esforço ou dívida' => 1, // -> Alto Valor dos Projetos
                    'Um valor desafiador, mas dentro do fluxo normal' => 2,
                    'Um valor baixo que não impacta o patrimônio consolidado' => 3,    // -> Baixo Valor de Projetos
                ],
            ],

            'Consistência e Educação' => [
                'Você dedica tempo para estudar sobre o mercado ou analisar o relatório da sua carteira?' => [
                    'Não tenho o hábito de estudar ou acompanhar' => 1,           // -> Educação Financeira
                    'Leio de forma esporádica' => 2,
                    'Acompanho ativamente e busco constante aprendizado' => 3, // -> Educação financeira
                ],
                'O que você faz quando recebe uma renda extra significativa (bônus/herança/venda de bem)?' => [
                    'Gasto com desejos imediatos ou quitação de imprevistos' => 1,
                    'Utilizo parte para o consumo e guardo o restante' => 2,
                    'Direciono 100% conforme meu planejamento estratégico' => 3,
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
