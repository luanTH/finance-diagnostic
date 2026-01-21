<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Collection;

class DiagnosticServicePF
{
    const LEVEL_STRONG  = 'strong';
    const LEVEL_AVERAGE = 'average';
    const LEVEL_WEAK    = 'weak';

    private int $thresholdStrong = 70; // Ajustado para ser mais criterioso
    private int $thresholdWeak   = 40;

    public function generate(array $selectedOptionIds): array
    {
        $options = Option::with('question.category')
            ->whereIn('id', $selectedOptionIds)
            ->get();

        $categoryScores = $this->calculateCategoryScores($options);
        $totalScore     = $options->sum('points');

        // Filtros de Nível
        $strengths   = $categoryScores->filter(fn($c) => $c['percent'] >= $this->thresholdStrong);
        $weaknesses  = $categoryScores->filter(fn($c) => $c['percent'] <= $this->thresholdWeak);
        $priorities  = $categoryScores->filter(fn($c) => $c['percent'] < $this->thresholdStrong);

        return [
            'tipo'                     => 'pf',
            'frase_atual'              => $this->getCurrentSituationPhrase($categoryScores),

            // Usando os termos exatos que você solicitou
            'pontos_fortes'            => $this->mapExactPoints($strengths, 'strong'),
            'pontos_desenvolver'       => $this->mapExactPoints($weaknesses, 'weak'),
            'pontos_melhorar'    => $this->mapPriorities($priorities),

            'diagnosticos_especificos' => $this->getSpecificDiagnostics($categoryScores),
            'geral'                    => $this->getGeneralSolution($categoryScores),
            'score_total'              => $totalScore,
        ];
    }

    /**
     * MAPEAMENTO DE PONTOS EXATOS (PF)
     * Transforma categorias em seus termos técnicos correspondentes
     */
    private function mapExactPoints(Collection $categories, string $level): array
    {
        $map = [
            'strong' => [
                'Fluxo de Caixa e Organização'  => ['Gasta menos do que ganha', 'Organização financeira'],
                'Capacidade de Poupança e Reserva' => ['Alto potencial de poupar', 'Alta Liquidez Patrimonial'],
                'Perfil e Experiência em Investimentos' => ['Experiência com investimentos'],
                'Proteção e Gestão de Riscos'   => ['Boa cobertura de riscos'],
                'Patrimônio e Sucessão'         => ['Acumulo Patrimonial'],
                'Projetos e Objetivos de Vida'  => ['Pouco Fluxo de Projetos', 'Baixo Valor de Projetos'],
                'Consistência e Educação'       => ['Educação financeira'],
            ],
            'weak' => [
                'Fluxo de Caixa e Organização'  => ['Gasta mais do que ganha', 'Desorganização financeira'],
                'Capacidade de Poupança e Reserva' => ['Sem capacidade de poupar'],
                'Perfil e Experiência em Investimentos' => ['Baixa experiência com investimentos', 'Carteira de Investimento Inadequada'],
                'Proteção e Gestão de Riscos'   => ['Baixa cobertura de riscos'],
                'Patrimônio e Sucessão'         => ['Baixo Acumulo Patrimonial'],
                'Projetos e Objetivos de Vida'  => ['Muito Fluxo de Projetos', 'Alto Valor dos Projetos'],
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

    /**
     * MAPEAMENTO DE ÊNFASES DE PRIORIDADE
     */
    private function mapPriorities(Collection $categories): array
    {
        $priorityMap = [
            'Fluxo de Caixa e Organização'  => 'Organização do Orçamento e fluxo de caixa',
            'Capacidade de Poupança e Reserva' => 'Acúmulo Patrimonial',
            'Perfil e Experiência em Investimentos' => 'Educação Financeira',
            'Aposentadoria e Longevidade'   => 'Planejamento da Aposentadoria',
            'Gestão de Dívidas'             => 'Quitação de Dividas',
            'Proteção e Gestão de Riscos'   => 'Proteção Familiar e cobertura de Riscos',
            'Patrimônio e Sucessão'         => 'Planejamento Sucessório', // Poderia incluir Tributário/Realocação
            'Consistência e Educação'       => 'Educação Financeira',
        ];

        return $categories->map(fn($c) => $priorityMap[$c['name']] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /* =========================
       DIAGNÓSTICOS PERSONALIZADOS
    ========================== */

    private function getSpecificDiagnostics($categoryScores): array
    {
        $bank = $this->narrativeBankPF();

        return $categoryScores->map(function ($data) use ($bank) {
            $level = $this->resolveLevel($data['percent']);
            $catName = $data['name'];

            return [
                'tema'     => $catName,
                'nivel'    => $level,
                'mensagem' => $this->random($bank[$level][$catName] ?? ['Análise em processamento.']),
                'acao'     => $this->getCustomAction($catName, $level),
                'percent'  => round($data['percent'], 1),
            ];
        })->values()->all();
    }

    /**
     * AÇÕES PERSONALIZADAS POR CATEGORIA E NÍVEL
     */
    private function getCustomAction(string $category, string $level): string
    {
        $actions = [
            'Fluxo de Caixa e Organização' => [
                'weak'    => 'A desorganização está custando caro. O apoio de um planejador é fundamental para implementar um método de controle que estanque as perdas imediatamente.',
                'average' => 'Você já iniciou o controle, mas um olhar especializado pode identificar "ralos financeiros" invisíveis que uma planilha comum não mostra.',
                'strong'  => 'Excelente base. Um planejador pode ajudar a automatizar ainda mais esse processo para que você foque apenas na estratégia de crescimento.',
            ],
            'Capacidade de Poupança e Reserva' => [
                'weak'    => 'Sua segurança está em risco. É urgente traçar uma estratégia de reserva com um profissional para evitar que imprevistos destruam seu patrimônio.',
                'average' => 'Você tem potencial, mas a velocidade de acúmulo pode dobrar com uma estratégia de alocação de sobra mensal desenhada por um especialista.',
                'strong'  => 'Com a reserva sólida, o próximo passo é a sofisticação. Um consultor pode ajudar a direcionar esse excesso para ativos de maior valor agregado.',
            ],
            'Perfil e Experiência em Investimentos' => [
                'weak'    => 'O mercado financeiro pode ser traiçoeiro sem guia. Antes de investir, valide seus primeiros passos com um especialista para evitar erros clássicos.',
                'average' => 'Para sair do básico e diversificar com segurança, contar com uma análise profissional da sua carteira evitará riscos desnecessários.',
                'strong'  => 'Patrimônios desse nível exigem sofisticação. Um planejador pode abrir portas para ativos exclusivos e estratégias de proteção internacional.',
            ],
            'Aposentadoria e Longevidade' => [
                'weak'    => 'O risco de depender apenas do Estado é alto. Um cálculo técnico de longevidade feito por um planejador é o primeiro passo para sua liberdade.',
                'average' => 'Seu plano está em curso, mas revisões periódicas com um profissional garantem que a inflação não corroa seu poder de compra no futuro.',
                'strong'  => 'Na fase de consolidação, a eficiência tributária é tudo. Um especialista pode ajudar a estruturar o usufruto do capital com o menor custo possível.',
            ],
            'Gestão de Dívidas' => [
                'weak'    => 'Não tente resolver sozinho. Um planejador financeiro tem as ferramentas para renegociar taxas e estruturar uma quitação que não asfixie seu padrão de vida.',
                'average' => 'Existem formas de reduzir o custo das suas dívidas atuais. Uma consultoria focada em crédito pode liberar um fôlego extra no seu orçamento.',
                'strong'  => 'Parabéns. Sua saúde financeira permite que um consultor foque 100% em multiplicar seu capital em vez de apenas gerir passivos.',
            ],
            'Proteção e Gestão de Riscos' => [
                'weak'    => 'Você está exposto. Um diagnóstico de riscos com um especialista é urgente para escolher as apólices corretas e não apenas "comprar seguros".',
                'average' => 'Suas proteções podem ter "buracos" contratuais. Uma revisão técnica das suas apólices garantirá que a indenização realmente chegue quando necessário.',
                'strong'  => 'Blindagem eficiente exige revisão constante. Um planejador deve auditar suas coberturas anualmente para alinhá-las à evolução do seu patrimônio.',
            ],
            'Patrimônio e Sucessão' => [
                'weak'    => 'Inventários podem consumir até 20% do que você construiu. É vital iniciar um planejamento sucessório com apoio profissional para proteger seus herdeiros.',
                'average' => 'O excesso de bens imóveis pode travar sua vida financeira. Um consultor pode ajudar na estratégia de realocação para ativos mais líquidos e rentáveis.',
                'strong'  => 'Estruturas complexas (como Holdings ou Trusts) exigem acompanhamento especializado para garantir a proteção jurídica e a eficiência tributária.',
            ],
            'Projetos e Objetivos de Vida' => [
                'weak'    => 'Muitos sonhos sem plano viram pesadelos financeiros. Um planejador ajudará a priorizar o que realmente importa e a viabilizar cada conquista.',
                'average' => 'Para objetivos de médio prazo, o uso de "baldes financeiros" estratégicos, orientados por um profissional, evitará que um sonho atropele o outro.',
                'strong'  => 'Com metas em dia, um consultor pode ajudar a elevar o nível desses projetos, transformando metas em experiências de vida inesquecíveis.',
            ],
            'Consistência e Educação' => [
                'weak'    => 'Educação financeira é um processo contínuo. Ter um mentor ou planejador como parceiro de jornada acelerará seu aprendizado em anos.',
                'average' => 'Para atingir a maestria nos investimentos, o acompanhamento de um especialista servirá como uma mentoria prática para suas decisões diárias.',
                'strong'  => 'Nesse nível, o papel do planejador é ser seu "braço direito" na curadoria de informações, filtrando o que realmente impacta sua estratégia global.',
            ],
        ];

        return $actions[$category][$level] ?? 'Este pilar exige uma análise técnica detalhada junto ao seu planejador financeiro.';
    }

    /* =========================
       NARRATIVE BANK PF (Novo)
    ========================== */

    private function narrativeBankPF(): array
    {
        return [
            self::LEVEL_STRONG => [
                'Fluxo de Caixa e Organização' => ['Sua gestão de caixa é profissional, garantindo sobra constante.'],
                'Capacidade de Poupança e Reserva' => ['Você possui uma reserva robusta que traz paz para investir.'],
                'Perfil e Experiência em Investimentos' => ['Você já compreende o risco e sabe selecionar bons ativos.'],
                'Aposentadoria e Longevidade' => ['Seu plano de futuro está trilhando o caminho da independência financeira.'],
                'Gestão de Dívidas' => ['Você domina o uso do crédito e não possui passivos onerosos.'],
                'Proteção e Gestão de Riscos' => ['Sua família e bens estão devidamente blindados contra fatalidades.'],
                'Patrimônio e Sucessão' => ['Seu patrimônio é sólido e você já olha para a transferência de legado.'],
                'Projetos e Objetivos de Vida' => ['Seus projetos estão dimensionados corretamente para sua realidade.'],
                'Consistência e Educação' => ['Seu compromisso com o aprendizado financeiro é um diferencial competitivo.'],
            ],
            self::LEVEL_AVERAGE => [
                'Fluxo de Caixa e Organização' => ['O controle existe, mas faltam ferramentas para ganhar eficiência.'],
                'Capacidade de Poupança e Reserva' => ['Sua reserva existe, mas pode ser insuficiente para crises longas.'],
                'Perfil e Experiência em Investimentos' => ['Você já saiu da poupança, mas ainda falta diversificação estratégica.'],
                'Aposentadoria e Longevidade' => ['Você iniciou o planejamento, mas o plano ainda é vago para garantir liberdade real.'],
                'Gestão de Dívidas' => ['Suas dívidas estão controladas, mas ainda limitam parte do seu potencial de acúmulo.'],
                'Proteção e Gestão de Riscos' => ['Você possui seguros, mas eles podem estar desatualizados para seu custo de vida.'],
                'Patrimônio e Sucessão' => ['Você já possui bens, mas a falta de uma estratégia sucessória gera insegurança jurídica.'],
                'Projetos e Objetivos de Vida' => ['Seus planos são claros, mas a execução simultânea pode sobrecarregar seu caixa futuro.'],
                'Consistência e Educação' => ['Você busca conhecimento esporadicamente, mas falta método para transformar isso em lucro.'],
            ],
            self::LEVEL_WEAK => [
                'Fluxo de Caixa e Organização' => ['A desorganização está drenando sua riqueza silenciosamente.'],
                'Capacidade de Poupança e Reserva' => ['Você vive no limite, sem margem de segurança para qualquer erro.'],
                'Perfil e Experiência em Investimentos' => ['Seu capital está perdendo valor real devido à falta de estratégia.'],
                'Aposentadoria e Longevidade' => ['Sua futura independência está em risco crítico por falta de provisões e visão de longo prazo.'],
                'Gestão de Dívidas' => ['O custo do capital está asfixiando sua capacidade de gerar patrimônio.'],
                'Proteção e Gestão de Riscos' => ['Um único imprevisto de saúde pode destruir anos de acúmulo financeiro.'],
                'Patrimônio e Sucessão' => ['Seu patrimônio está exposto a inventários caros e tributação excessiva.'],
                'Projetos e Objetivos de Vida' => ['Seus desejos de vida carecem de lastro financeiro, tornando-os planos sem viabilidade prática.'],
                'Consistência e Educação' => ['A falta de educação financeira básica impede você de enxergar riscos e oportunidades reais.'],
            ],
        ];
    }

    /* =========================
       CÁLCULO E HELPERS
    ========================== */

    private function calculateCategoryScores($options)
    {
        return $options
            ->groupBy('question.category.name')
            ->map(function ($items) {
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

    private function getCurrentSituationPhrase($categoryScores): string
    {
        $avg = $categoryScores->avg('percent');

        return match (true) {
            $avg >= 85 => "<b>Segurança Máxima:</b> Parabéns! Suas finanças estão em um nível de organização raro. O desafio agora é proteger o que você conquistou e garantir o futuro da sua família.",

            $avg >= 70 => "<b>No Caminho Certo:</b> Você tem uma base sólida e já consegue planejar o futuro. É o momento ideal para fazer o dinheiro trabalhar mais para você com investimentos melhores.",

            $avg >= 50 => "<b>Equilíbrio Sensível:</b> Você já tem algum controle, mas ainda vive no limite. Falta pouco para parar de apenas 'pagar contas' e começar a construir patrimônio de verdade.",

            $avg >= 30 => "<b>Sinal Amarelo:</b> Suas finanças têm falhas que podem trazer problemas em breve. É hora de ajustar as contas para que um imprevisto não vire uma bola de neve.",

            default    => "<b>Atenção Urgente:</b> Sua saúde financeira está em perigo. A prioridade agora é organizar a casa imediatamente para recuperar o fôlego e proteger seu padrão de vida."
        };
    }

    private function getGeneralSolution($categoryScores): string
    {
        $weakCount = $categoryScores->filter(fn($c) => $c['percent'] <= $this->thresholdWeak)->count();
        $avg = $categoryScores->avg('percent');

        // Cenário 1: Pontos de atenção críticos
        if ($weakCount >= 3) {
            return "<b>Organizar para Respirar:</b> Você precisa parar os 'vazamentos' de dinheiro agora. Antes de pensar em investir, o foco deve ser organizar suas dívidas e sobras. <b>Conversar com o seu planejador financeiro agora será o passo decisivo para priorizar o que é urgente e devolver a paz às suas finanças.</b>";
        }

        // Cenário 2: Situação de destaque/excelência
        if ($avg >= 75) {
            return "<b>Multiplicar com Estratégia:</b> Com tudo em ordem, seu foco agora deve ser investir com mais inteligência e proteger seus bens. Um profissional vai te ajudar a encontrar as melhores oportunidades e evitar custos desnecessários. <b>Que tal marcar um papo com seu planejador para descobrir como levar seu patrimônio para o próximo nível?</b>";
        }

        // Cenário 3: Intermediário (Ajustes necessários)
        return "<b>Ajustar e Crescer:</b> Você já tem as peças do quebra-cabeça, mas elas ainda não se encaixam perfeitamente. Com alguns ajustes e um plano bem feito, você sairá desse equilíbrio instável para ver seu dinheiro crescer de verdade. <b>O seu planejador financeiro é o parceiro ideal para te ajudar a montar essa estratégia com total segurança.</b>";
    }
}
