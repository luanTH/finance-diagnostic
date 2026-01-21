<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Collection;

class DiagnosticServicePJ
{
    const LEVEL_STRONG  = 'strong';
    const LEVEL_AVERAGE = 'average';
    const LEVEL_WEAK    = 'weak';

    private int $thresholdStrong = 75;
    private int $thresholdWeak   = 45;

    public function generate(array $selectedOptionIds): array
    {
        $options = Option::with('question.category')
            ->whereIn('id', $selectedOptionIds)
            ->get();

        $categoryScores = $this->calculateCategoryScores($options);
        $totalScore     = $options->sum('points');

        // 1. FORTES: Acima ou igual ao limite de força (ex: 75%)
        $strengths = $categoryScores->filter(fn($c) => $c['percent'] >= $this->thresholdStrong);

        // 2. RUINS (Melhorar): Abaixo ou igual ao limite de fraqueza (ex: 45%)
        $weaknesses = $categoryScores->filter(fn($c) => $c['percent'] <= $this->thresholdWeak);

        // 3. MEDIANOS (Desenvolver): O que está no "limbo" entre os dois
        $averages = $categoryScores->filter(
            fn($c) =>
            $c['percent'] > $this->thresholdWeak &&
                $c['percent'] < $this->thresholdStrong
        );

        return [
            'tipo'                     => 'pj',
            'frase_atual'              => $this->getCurrentSituationPhrase($categoryScores),
            'pontos_fortes'            => $this->mapExactPoints($strengths, 'strong'),
            'pontos_desenvolver'          => $this->mapExactPoints($weaknesses, 'weak'), // Os "ruins"
            'pontos_melhorar'       => $this->mapPriorities($averages),           // Os "medianos"
            'diagnosticos_especificos' => $this->getSpecificDiagnostics($categoryScores),
            'geral'                    => $this->getGeneralSolution($categoryScores),
            'score_total'              => $totalScore,
        ];
    }

    /**
     * Termos técnicos para o relatório da Empresa
     */
    private function mapExactPoints(Collection $categories, string $level): array
    {
        $map = [
            'strong' => [
                'Gestão de Caixa'           => ['Fluxo de Caixa Saudável', 'Previsibilidade Financeira'],
                'Previsibilidade de Receita' => ['Receita Recorrente', 'Estabilidade de Faturamento'],
                'Margem e Precificação'     => ['Margens de Lucro Preservadas', 'Precificação Eficiente'],
                'Controle de Custos'        => ['Eficiência Operacional', 'Baixo Desperdício'],
                'Endividamento Empresarial' => ['Alavancagem Saudável', 'Crédito Estratégico'],
                'Separação PF x PJ'         => ['Governança Corporativa', 'Blindagem Patrimonial'],
                'Gestão e Indicadores'      => ['Cultura de Dados (BI)', 'Gestão por Indicadores'],
                'Risco e Segurança'         => ['Resiliência Financeira', 'Reserva de Oportunidade'],
                'Dependência do Dono'       => ['Autonomia Operacional', 'Processos Maduros'],
                'Crescimento e Escala'      => ['Prontidão para Escala', 'Crescimento Planejado'],
            ],
            'weak' => [
                'Gestão de Caixa'           => ['Gargalos no Fluxo de Caixa', 'Falta de Projeção'],
                'Previsibilidade de Receita' => ['Instabilidade de Receita', 'Risco de Faturamento'],
                'Margem e Precificação'     => ['Margens Corrompidas', 'Erro de Precificação'],
                'Controle de Custos'        => ['Descontrole de Despesas', 'Ineficiência de Processos'],
                'Endividamento Empresarial' => ['Endividamento Nocivo', 'Uso de Crédito para Capital de Giro'],
                'Separação PF x PJ'         => ['Mistura de Contas (PF/PJ)', 'Confusão Patrimonial'],
                'Gestão e Indicadores'      => ['Gestão Reativa/Intuitiva', 'Falta de Dashboards'],
                'Risco e Segurança'         => ['Vulnerabilidade a Crises', 'Dependência de Grandes Clientes'],
                'Dependência do Dono'       => ['Centralização Excessiva', 'Falta de Processos'],
                'Crescimento e Escala'      => ['Crescimento Desordenado', 'Falta de Capital de Giro para Escala'],
            ]
        ];

        $results = [];
        foreach ($categories as $cat) {
            if (isset($map[$level][$cat['name']])) {
                foreach ($map[$level][$cat['name']] as $point) {
                    $results[] = $point;
                }
            }
        }

        return array_unique($results);
    }

    private function mapPriorities(Collection $categories): array
    {
        $priorityMap = [
            'Gestão de Caixa'           => 'Equilíbrio do Fluxo de Caixa',
            'Previsibilidade de Receita' => 'Recorrência de Faturamento',
            'Margem e Precificação'     => 'Otimização de Lucratividade',
            'Controle de Custos'        => 'Redução de Custos e Desperdícios',
            'Endividamento Empresarial' => 'Reestruturação de Dívidas',
            'Separação PF x PJ'         => 'Governança e Separação Patrimonial',
            'Gestão e Indicadores'      => 'Implementação de Indicadores (KPIs)',
            'Risco e Segurança'         => 'Criação de Reserva de Contingência',
            'Dependência do Dono'       => 'Documentação de Processos e Delegação',
            'Crescimento e Escala'      => 'Planejamento de Expansão',
        ];

        return $categories->map(fn($c) => $priorityMap[$c['name']] ?? null)->filter()->unique()->values()->all();
    }

    /* =========================
       DIAGNÓSTICOS E AÇÕES (PJ)
    ========================== */

    private function getSpecificDiagnostics($categoryScores): array
    {
        $bank = $this->narrativeBankPJ();

        return $categoryScores->map(function ($data) use ($bank) {
            $level = $this->resolveLevel($data['percent']);
            $catName = $data['name'];

            return [
                'tema'     => $catName,
                'nivel'    => $level,
                'mensagem' => $this->random($bank[$level][$catName] ?? ['Análise empresarial em processamento.']),
                'acao'     => $this->getCustomAction($catName, $level),
                'percent'  => round($data['percent'], 1),
            ];
        })->values()->all();
    }

    private function getCustomAction(string $category, string $level): string
    {
        $actions = [
            'Gestão de Caixa' => [
                'weak'   => 'Urgente: A falta de projeção pode causar quebras repentinas. Implemente um fluxo de caixa diário com o apoio de um especialista para prever buracos antes que aconteçam.',
                'average' => 'Você já monitora o básico, mas falta antecipar o futuro. Um planejador ajudará a criar projeções de médio prazo para que você nunca seja pego de surpresa.',
                'strong'  => 'Seu caixa é sólido. Use essa previsibilidade para negociar melhores prazos com fornecedores e otimizar seu capital de giro junto ao seu planejador.',
            ],
            'Previsibilidade de Receita' => [
                'weak'   => 'Seu faturamento é instável e perigoso. É vital sentar com um consultor para criar estratégias de recorrência e reduzir a dependência de vendas pontuais.',
                'average' => 'Existe alguma previsibilidade, mas o risco ainda é alto. O apoio profissional ajudará a estabilizar suas entradas e trazer mais calma para a gestão.',
                'strong'  => 'Excelente previsibilidade. Com essa segurança, seu planejador pode ajudar a direcionar os lucros para investimentos que acelerem a expansão do negócio.',
            ],
            'Margem e Precificação' => [
                'weak'   => 'Você pode estar pagando para trabalhar. É vital revisar sua planilha de custos e impostos com um especialista para garantir que cada venda traga lucro real.',
                'average' => 'Suas margens estão no limite. Um ajuste fino na precificação, guiado por um profissional, pode aumentar seu lucro final sem espantar seus clientes.',
                'strong'  => 'Sua margem é saudável e competitiva. Foque agora em escala para maximizar o retorno sobre esse lucro unitário com o suporte de uma consultoria.',
            ],
            'Controle de Custos' => [
                'weak'   => 'Seus custos estão drenando o lucro da empresa. É necessária uma auditoria para identificar desperdícios e otimizar a operação o quanto antes.',
                'average' => 'Os custos são conhecidos, mas raramente otimizados. Uma revisão estratégica com seu planejador pode liberar recursos para novos investimentos no negócio.',
                'strong'  => 'Operação eficiente. Mantenha o monitoramento constante com seu planejador para evitar que a estrutura de custos cresça mais que o faturamento.',
            ],
            'Endividamento Empresarial' => [
                'weak'   => 'O endividamento está sufocando o negócio. Procure um planejador para renegociar taxas e criar um plano de quitação que salve a saúde da empresa.',
                'average' => 'As dívidas estão sob controle, mas limitam seu crescimento. Um especialista pode ajudar a trocar dívidas caras por opções de crédito mais baratas e saudáveis.',
                'strong'  => 'Uso de crédito exemplar. Suas dívidas são ferramentas de crescimento (alavancagem). Continue com o suporte profissional para manter esse equilíbrio.',
            ],
            'Separação PF x PJ' => [
                'weak'   => 'Risco Jurídico Alto: Misturar contas é o caminho mais rápido para problemas fiscais. Defina um pró-labore fixo com ajuda profissional para blindar seu patrimônio.',
                'average' => 'A separação existe, mas ainda há confusão. Organizar essa governança com um consultor trará clareza real sobre quanto a empresa realmente lucra.',
                'strong'  => 'Excelente governança. Isso facilita auditorias e valoriza muito o preço da sua empresa (valuation) caso você decida vendê-la ou atrair investidores.',
            ],
            'Gestão e Indicadores' => [
                'weak'   => 'Gerir sem dados é como dirigir no escuro. É fundamental implementar indicadores básicos (KPIs) com o auxílio de um profissional para guiar sua empresa.',
                'average' => 'Você tem dados, mas não os usa para decidir. Um planejador transformará esses números em um painel de controle estratégico para você governar o negócio.',
                'strong'  => 'Gestão baseada em dados. Continue usando seus indicadores para manter a vantagem competitiva e antecipar movimentos de mercado com sua consultoria.',
            ],
            'Risco e Segurança' => [
                'weak'   => 'A empresa está muito vulnerável. É urgente formar um caixa de contingência e diversificar sua carteira de clientes com apoio de um plano estratégico.',
                'average' => 'Você tem alguma reserva, mas ela não suportaria crises longas. Um especialista ajudará a calcular e formar o caixa de segurança ideal para seu setor.',
                'strong'  => 'Resiliência alta. Sua reserva de oportunidade permite inclusive aproveitar crises para comprar concorrentes ou expandir enquanto outros recuam.',
            ],
            'Dependência do Dono' => [
                'weak'   => 'A empresa depende demais de você e isso impede a escala. Comece a documentar processos básicos com ajuda especializada para poder delegar com segurança.',
                'average' => 'Você ainda é o motor principal, mas já delega algumas tarefas. Criar manuais de operação com apoio profissional dará a liberdade que você procura.',
                'strong'  => 'Parabéns. Sua empresa é um ativo real que funciona sem sua presença constante, permitindo que você foque apenas no crescimento estratégico.',
            ],
            'Crescimento e Escala' => [
                'weak'   => 'Tentar crescer agora pode quebrar o negócio. É preciso organizar a casa e o capital de giro com um consultor antes de dar qualquer passo ousado.',
                'average' => 'O negócio tem potencial, mas falta um plano de expansão. Um planejador ajudará a simular cenários para crescer de forma sustentável e sem sustos.',
                'strong'  => 'Estrutura pronta para escala. Com o apoio da sua consultoria, você pode acelerar o crescimento, pois o financeiro já sustenta a nova operação.',
            ],
        ];

        return $actions[$category][$level] ?? 'Este pilar exige uma análise técnica detalhada junto ao seu planejador financeiro empresarial.';
    }

    private function narrativeBankPJ(): array
    {
        return [
            self::LEVEL_STRONG => [
                'Gestão de Caixa'           => ['O controle de caixa é rigoroso, permitindo investimentos no negócio com total segurança.'],
                'Previsibilidade de Receita' => ['O faturamento recorrente dá ao negócio uma estabilidade invejável para planejar o futuro.'],
                'Margem e Precificação'     => ['A precificação está correta e a empresa captura valor de forma eficiente em cada venda.'],
                'Controle de Custos'        => ['Existe uma cultura de eficiência onde cada custo é monitorado e otimizado constantemente.'],
                'Endividamento Empresarial' => ['O uso de capital de terceiros é estratégico e impulsiona o crescimento sem sufocar o lucro.'],
                'Separação PF x PJ'         => ['A governança financeira é impecável, garantindo a saúde jurídica e financeira do negócio.'],
                'Gestão e Indicadores'      => ['A gestão é guiada por dados, permitindo decisões rápidas e ajustes precisos de rota.'],
                'Risco e Segurança'         => ['A empresa possui reservas e proteções que garantem a operação mesmo em cenários de crise.'],
                'Dependência do Dono'       => ['A operação é madura e os processos permitem que o negócio funcione sem sua presença constante.'],
                'Crescimento e Escala'      => ['A estrutura financeira é sólida e a empresa está pronta para crescer de forma ordenada.'],
            ],

            self::LEVEL_AVERAGE => [
                'Gestão de Caixa'           => ['Você tem uma noção do caixa, mas sem projeção futura, qualquer surpresa pode desestabilizar o mês.'],
                'Previsibilidade de Receita' => ['A receita existe, mas a falta de previsibilidade impede passos mais ousados na expansão.'],
                'Margem e Precificação'     => ['Sua margem está no limite; pequenos aumentos nos custos podem zerar sua lucratividade rapidamente.'],
                'Controle de Custos'        => ['Os custos são acompanhados, mas ainda existem "ralos" que impedem uma margem maior de lucro.'],
                'Endividamento Empresarial' => ['As dívidas estão pagas em dia, mas o custo do crédito está limitando sua capacidade de reinvestir.'],
                'Separação PF x PJ'         => ['Existe alguma separação, mas retiradas eventuais ainda atrapalham a visão real do lucro da empresa.'],
                'Gestão e Indicadores'      => ['A empresa coleta dados, mas eles ainda não são usados para tomar decisões estratégicas reais.'],
                'Risco e Segurança'         => ['A empresa sobrevive a pequenos sustos, mas não está preparada para a perda de um grande pilar de receita.'],
                'Dependência do Dono'       => ['Você já delega algumas funções, mas a empresa ainda exige sua supervisão para não perder o ritmo.'],
                'Crescimento e Escala'      => ['O negócio tem potencial de escala, mas o financeiro ainda é um gargalo que trava esse movimento.'],
            ],

            self::LEVEL_WEAK => [
                'Gestão de Caixa'           => ['A falta de acompanhamento do caixa coloca a operação em risco constante de falta de liquidez.'],
                'Previsibilidade de Receita' => ['A instabilidade nas vendas impede qualquer planejamento de longo prazo para a empresa.'],
                'Margem e Precificação'     => ['Há indícios de que o lucro está sendo corroído por custos mal calculados ou impostos ignorados.'],
                'Controle de Custos'        => ['A empresa opera com custos elevados e sem controle, o que torna o negócio ineficiente e frágil.'],
                'Endividamento Empresarial' => ['O endividamento está em níveis críticos e a empresa trabalha apenas para pagar juros bancários.'],
                'Separação PF x PJ'         => ['A mistura de contas dificulta saber se o negócio é realmente lucrativo ou se está apenas pagando contas pessoais.'],
                'Gestão e Indicadores'      => ['A gestão é baseada apenas na intuição, o que torna o crescimento uma questão de sorte, não de plano.'],
                'Risco e Segurança'         => ['A empresa está exposta e vulnerável; qualquer imprevisto grave pode comprometer a continuidade do negócio.'],
                'Dependência do Dono'       => ['A centralização excessiva impede a escala e torna o negócio totalmente dependente da sua saúde e presença.'],
                'Crescimento e Escala'      => ['A desorganização financeira atual torna qualquer tentativa de crescimento um risco alto de colapso.'],
            ],
        ];
    }

    /* =========================
       FRASES DE INÍCIO E FIM (O Toque Especial)
    ========================== */

    private function getCurrentSituationPhrase($categoryScores): string
    {
        $avg = $categoryScores->avg('percent');

        return match (true) {
            $avg >= 85 => "<b>Liderança e Robustez:</b> Sua empresa apresenta uma maturidade financeira de mercado. O foco agora é eficiência tributária e expansão de mercado.",
            $avg >= 70 => "<b>Prontidão para Escala:</b> O negócio está saudável e equilibrado. É o momento de ajustar os processos para crescer sem perder a lucratividade.",
            $avg >= 50 => "<b>Estabilidade Operacional:</b> A empresa sobrevive bem, mas ainda 'apaga incêndios'. Faltam indicadores para você ter liberdade como dono.",
            $avg >= 30 => "<b>Alerta de Gestão:</b> Existem gargalos que estão limitando o potencial do negócio. É hora de profissionalizar o financeiro para evitar estagnação.",
            default    => "<b>Risco de Continuidade:</b> O negócio precisa de uma reestruturação financeira imediata para garantir a operação e proteger o seu patrimônio."
        };
    }

    private function getGeneralSolution($categoryScores): string
    {
        $avg = $categoryScores->avg('percent');
        $weakCount = $categoryScores->filter(fn($c) => $c['percent'] <= $this->thresholdWeak)->count();

        if ($weakCount >= 3) {
            return "<b>Plano de Recuperação:</b> Identificamos falhas críticas na base do negócio. O foco agora não é vender mais, mas gerir melhor o que já entra. <b>Agendar uma consultoria financeira empresarial ajudará você a organizar a casa antes que o crescimento se torne um problema.</b>";
        }

        if ($avg >= 75) {
            return "<b>Foco em Valor (Valuation):</b> Sua empresa é um ativo valioso. O próximo passo é otimizar cada centavo e preparar o negócio para escala ou sucessão. <b>Um planejador financeiro especializado pode ajudar a maximizar o valor real da sua empresa no mercado.</b>";
        }

        return "<b>Gestão Estratégica:</b> Você tem um bom negócio nas mãos, mas falta o 'ajuste fino' nos números para ganhar fôlego. <b>O apoio de um planejador financeiro transformará seu financeiro reativo em um financeiro estratégico para o seu crescimento.</b>";
    }

    /* =========================
       HELPERS
    ========================== */

    private function calculateCategoryScores($options)
    {
        return $options->groupBy('question.category.name')->map(function ($items) {
            $score = $items->sum('points');
            $max   = $items->count() * 3;
            return [
                'name'    => $items->first()->question->category->name,
                'score'   => $score,
                'max'     => $max,
                'percent' => ($score / $max) * 100,
            ];
        });
    }

    private function resolveLevel(float $percent): string
    {
        if ($percent >= $this->thresholdStrong) return self::LEVEL_STRONG;
        if ($percent <= $this->thresholdWeak)   return self::LEVEL_WEAK;
        return self::LEVEL_AVERAGE;
    }

    private function random(array $items): string
    {
        return $items[array_rand($items)];
    }
}
