<?php

namespace App\Services;

use App\Models\Option;

class DiagnosticService
{
    const TYPE_PF = 'pf';
    const TYPE_PJ = 'pj';

    const LEVEL_STRONG  = 'strong';
    const LEVEL_AVERAGE = 'average';
    const LEVEL_WEAK    = 'weak';

    private int $thresholdStrong = 75;
    private int $thresholdWeak   = 45;

    /* =========================
       ENTRY POINT
    ========================== */

    public function generate(array $selectedOptionIds, string $type = self::TYPE_PF): array
    {
        $options = Option::with('question.category')
            ->whereIn('id', $selectedOptionIds)
            ->get();

        $categoryScores = $this->calculateCategoryScores($options);
        $totalScore     = $options->sum('points');

        // Lógica de Filtragem Refinada
        $strengths    = $categoryScores->filter(fn($c) => $c['percent'] >= $this->thresholdStrong);
        $weaknesses   = $categoryScores->filter(fn($c) => $c['percent'] <= $this->thresholdWeak);

        // Pontos de melhoria: o que está entre o fraco e o forte
        $improvements = $categoryScores->filter(
            fn($c) =>
            $c['percent'] > $this->thresholdWeak && $c['percent'] < $this->thresholdStrong
        )
            ->sortByDesc('percent') // Opcional: Ordena pelos que estão quase virando "pontos fortes"
            ->take(3);

        return [
            'tipo'                     => $type,
            'frase_atual'              => $this->getCurrentSituationPhrase($categoryScores, $type),
            'pontos_fortes'            => $this->formatStrengths($strengths, $type),
            'pontos_melhorar'          => $this->formatImprovements($improvements, $type), // Nova linha
            'pontos_desenvolver'       => $this->formatWeaknesses($weaknesses, $type),
            'diagnosticos_especificos' => $this->getSpecificDiagnostics($categoryScores, $type),
            'geral'                    => $this->getGeneralSolution($categoryScores, $type),
            'score_total'              => $totalScore,
        ];
    }

    private function formatStrengths($strengths, string $type): array
    {
        if ($strengths->isEmpty()) {
            return match ($type) {
                self::TYPE_PJ => [
                    'Nenhuma área financeira consolidada ainda. A empresa precisa ganhar estrutura antes de acelerar.'
                ],
                default => [
                    'Nenhum ponto forte consolidado ainda. Primeiro é preciso estabilizar.'
                ],
            };
        }

        $templatesPF = [
            "Você demonstra força em <b>:name</b>, estando acima da média da maioria das pessoas.",
            "O pilar <b>:name</b> é um dos seus maiores ativos financeiros hoje.",
            "Sua maturidade em <b>:name</b> permite decisões mais seguras nesta área.",
            "A área de <b>:name</b> está bem estruturada e sustenta sua estabilidade financeira.",
            "Você construiu um domínio consistente em <b>:name</b>, trazendo segurança para suas decisões."
        ];

        $templatesPJ = [
            "A empresa demonstra excelência em <b>:name</b>, acima da média do mercado.",
            "O pilar <b>:name</b> está bem estruturado e sustenta decisões estratégicas.",
            "A maturidade em <b>:name</b> permite que a empresa opere com mais previsibilidade.",
            "A área de <b>:name</b> é um dos principais ativos financeiros do negócio.",
            "Existe domínio sólido em <b>:name</b>, fortalecendo a base da empresa."
        ];

        $templates = $type === self::TYPE_PJ ? $templatesPJ : $templatesPF;

        return $strengths->map(function ($s) use ($templates) {
            $msg = $this->random($templates);
            return str_replace(':name', $s['name'], $msg);
        })->values()->all();
    }

    private function formatWeaknesses($weaknesses, string $type): array
    {
        if ($weaknesses->isEmpty()) {
            return match ($type) {
                self::TYPE_PJ => [
                    'Nenhum ponto crítico imediato identificado. A empresa apresenta boa estabilidade financeira.'
                ],
                default => [
                    'Nenhum ponto crítico imediato identificado. Continue monitorando.'
                ],
            };
        }

        $templatesPF = [
            "A área de <b>:name</b> exige atenção imediata para evitar perdas financeiras.",
            "Identificamos gargalos relevantes em <b>:name</b> que comprometem sua estabilidade.",
            "Sua maior vulnerabilidade hoje está em <b>:name</b>, exigindo correção rápida.",
            "O pilar <b>:name</b> apresenta sinais de descontrole que drenam seus recursos.",
            "Sem ajustes em <b>:name</b>, seu crescimento financeiro ficará comprometido."
        ];

        $templatesPJ = [
            "A área de <b>:name</b> representa um risco financeiro relevante para a empresa.",
            "Identificamos fragilidades em <b>:name</b> que limitam o crescimento do negócio.",
            "O pilar <b>:name</b> está comprometendo a saúde financeira da empresa.",
            "Sem correções em <b>:name</b>, a empresa ficará exposta a riscos operacionais.",
            "A fragilidade em <b>:name</b> impede decisões estratégicas mais seguras."
        ];

        $templates = $type === self::TYPE_PJ ? $templatesPJ : $templatesPF;

        return $weaknesses->map(function ($w) use ($templates) {
            $msg = $this->random($templates);
            return str_replace(':name', $w['name'], $msg);
        })->values()->all();
    }

    private function formatImprovements($improvements, string $type): array
    {
        if ($improvements->isEmpty()) {
            return []; // Geralmente não precisa de mensagem de erro aqui, para não poluir
        }

        $templatesPF = [
            "A área de <b>:name</b> está estável, mas possui espaço para otimização de rentabilidade.",
            "Seu desempenho em <b>:name</b> é regular, contudo, pequenos ajustes elevarão seu patamar.",
            "Existe equilíbrio em <b>:name</b>, mas falta um plano de longo prazo para este pilar.",
            "Você já domina o básico de <b>:name</b>, agora o foco deve ser a sofisticação técnica.",
            "O pilar <b>:name</b> não corre riscos, mas pode performar muito melhor com acompanhamento."
        ];

        $templatesPJ = [
            "A empresa mantém <b>:name</b> sob controle, mas há margem para ganho de eficiência operacional.",
            "O setor de <b>:name</b> está funcional, porém ainda não opera em sua capacidade máxima.",
            "Identificamos que <b>:name</b> é uma área madura, mas que precisa de inovação nos processos.",
            "Existe estabilidade em <b>:name</b>, permitindo que agora o foco seja a escalabilidade.",
            "O desempenho em <b>:name</b> é satisfatório, mas pode ser otimizado para reduzir custos."
        ];

        $templates = $type === self::TYPE_PJ ? $templatesPJ : $templatesPF;

        return $improvements->map(function ($i) use ($templates) {
            $msg = $this->random($templates);
            return str_replace(':name', $i['name'], $msg);
        })->values()->all();
    }



    /* =========================
       CÁLCULO
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

    /* =========================
       NARRATIVE BANK
    ========================== */

    private function narrativeBank(string $type): array
    {
        return match ($type) {
            self::TYPE_PJ => $this->narrativeBankPJ(),
            default       => $this->narrativeBankPF(),
        };
    }

    /* ========= PF ========= */

    private function narrativeBankPF(): array
    {
        return [
            self::LEVEL_STRONG => [
                'Organização Financeira' => [
                    'Você construiu uma base financeira sólida e confiável.',
                    'Seu controle financeiro demonstra maturidade.',
                    'Aqui existe método, não improviso.',
                ],
                'Consciência de Gastos' => [
                    'Você sabe exatamente onde seu dinheiro atua.',
                    'Poucos desperdícios passam despercebidos.',
                    'Seu dinheiro trabalha com intenção.',
                ],
                'Controle de Dívidas' => [
                    'Suas dívidas não controlam suas decisões.',
                    'Você usa crédito com inteligência.',
                    'Aqui não há sufoco financeiro.',
                ],
                'Reserva e Segurança' => [
                    'Você está protegido contra imprevistos.',
                    'Sua reserva garante tranquilidade.',
                    'Existe segurança financeira real.',
                ],
                'Planejamento' => [
                    'Você toma decisões olhando o futuro.',
                    'Planejamento consistente guia suas escolhas.',
                    'Existe direção clara.',
                ],
                'Mentalidade Financeira' => [
                    'Você age com racionalidade financeira.',
                    'Decisões estratégicas prevalecem.',
                    'Você lidera o dinheiro.',
                ],
                'Crescimento e Futuro' => [
                    'Você pensa além da sobrevivência.',
                    'Seu foco é expansão.',
                    'Há visão de longo prazo.',
                ],
            ],

            self::LEVEL_AVERAGE => [
                'Organização Financeira' => [
                    'Existe controle, mas ainda falha.',
                    'Funciona, mas depende de esforço.',
                    'Há espaço para sistema.',
                ],
                'Consciência de Gastos' => [
                    'Alguns vazamentos passam despercebidos.',
                    'Ainda falta domínio total.',
                    'O controle não é constante.',
                ],
                'Controle de Dívidas' => [
                    'As dívidas limitam decisões.',
                    'Existe risco latente.',
                    'Ainda pesa.',
                ],
                'Reserva e Segurança' => [
                    'A reserva existe, mas não protege totalmente.',
                    'Um imprevisto causaria aperto.',
                    'Ainda falta blindagem.',
                ],
                'Planejamento' => [
                    'Planeja, mas não executa sempre.',
                    'Falta acompanhamento.',
                    'Metas não são consistentes.',
                ],
                'Mentalidade Financeira' => [
                    'Emoção ainda interfere.',
                    'Decisões oscilam.',
                    'Há espaço para maturidade.',
                ],
                'Crescimento e Futuro' => [
                    'O crescimento é desejado.',
                    'Ainda não é estruturado.',
                    'Falta estratégia.',
                ],
            ],

            self::LEVEL_WEAK => [
                'Organização Financeira' => [
                    'Sem organização, qualquer imprevisto vira crise.',
                    'A vida financeira opera no improviso.',
                    'Esse é um gargalo grave.',
                ],
                'Consciência de Gastos' => [
                    'Você não sabe para onde o dinheiro vai.',
                    'O descontrole drena sua renda.',
                    'Esse ponto é crítico.',
                ],
                'Controle de Dívidas' => [
                    'As dívidas sufocam suas escolhas.',
                    'Você opera no limite.',
                    'Há risco real.',
                ],
                'Reserva e Segurança' => [
                    'Você está vulnerável.',
                    'Não existe proteção mínima.',
                    'Esse ponto é crítico.',
                ],
                'Planejamento' => [
                    'Você reage, não planeja.',
                    'O futuro é decidido no improviso.',
                    'Não há direção.',
                ],
                'Mentalidade Financeira' => [
                    'Decisões emocionais custam caro.',
                    'Esse é um bloqueio central.',
                    'O dinheiro dita suas escolhas.',
                ],
                'Crescimento e Futuro' => [
                    'Hoje o foco é sobreviver.',
                    'Sem base, não há crescimento.',
                    'O avanço está travado.',
                ],
            ],
        ];
    }

    /* ========= PJ ========= */

    private function narrativeBankPJ(): array
    {
        return [
            self::LEVEL_STRONG => [
                'Gestão de Caixa' => [
                    'O caixa é controlado com precisão.',
                    'Existe previsibilidade financeira.',
                    'O caixa sustenta decisões estratégicas.',
                ],
                'Previsibilidade de Receita' => [
                    'A receita é previsível.',
                    'O faturamento não depende de sorte.',
                    'Existe recorrência clara.',
                ],
                'Margem e Precificação' => [
                    'A margem é conhecida e protegida.',
                    'A empresa cobra corretamente.',
                    'Preço e lucro caminham juntos.',
                ],
                'Controle de Custos' => [
                    'Custos são monitorados.',
                    'Não há desperdícios relevantes.',
                    'Existe eficiência operacional.',
                ],
                'Endividamento Empresarial' => [
                    'As dívidas são estratégicas.',
                    'Crédito é ferramenta, não muleta.',
                    'O risco é controlado.',
                ],
                'Separação PF x PJ' => [
                    'As finanças estão bem separadas.',
                    'Existe governança financeira.',
                    'A empresa é saudável.',
                ],
                'Gestão e Indicadores' => [
                    'Decisões são guiadas por dados.',
                    'Indicadores orientam o crescimento.',
                    'Existe gestão real.',
                ],
                'Risco e Segurança' => [
                    'A empresa suporta imprevistos.',
                    'Existe reserva de caixa.',
                    'O risco é administrado.',
                ],
                'Dependência do Dono' => [
                    'A empresa não depende do dono.',
                    'Processos estão bem definidos.',
                    'Existe autonomia operacional.',
                ],
                'Crescimento e Escala' => [
                    'O crescimento é planejado.',
                    'A estrutura suporta escala.',
                    'Existe visão estratégica.',
                ],
            ],

            self::LEVEL_AVERAGE => [
                'Gestão de Caixa' => [
                    'O controle existe, mas é frágil.',
                    'O caixa ainda oscila.',
                    'Falta projeção.',
                ],
                'Previsibilidade de Receita' => [
                    'Parte da receita é instável.',
                    'Existe dependência pontual.',
                    'Ainda há incerteza.',
                ],
                'Margem e Precificação' => [
                    'A margem não é totalmente clara.',
                    'Alguns preços estão desalinhados.',
                    'Existe risco silencioso.',
                ],
                'Controle de Custos' => [
                    'Custos nem sempre são revisados.',
                    'Há desperdícios ocultos.',
                    'Falta disciplina.',
                ],
                'Endividamento Empresarial' => [
                    'As dívidas limitam decisões.',
                    'O crédito pesa.',
                    'O risco é moderado.',
                ],
                'Separação PF x PJ' => [
                    'Existe mistura financeira.',
                    'O caixa sofre interferência.',
                    'A governança é parcial.',
                ],
                'Gestão e Indicadores' => [
                    'Poucos indicadores são acompanhados.',
                    'Decisões nem sempre usam dados.',
                    'Gestão reativa.',
                ],
                'Risco e Segurança' => [
                    'Um imprevisto causaria impacto.',
                    'A reserva é insuficiente.',
                    'Existe vulnerabilidade.',
                ],
                'Dependência do Dono' => [
                    'O dono ainda centraliza decisões.',
                    'Processos são informais.',
                    'A empresa depende demais.',
                ],
                'Crescimento e Escala' => [
                    'O crescimento é desejado.',
                    'A estrutura limita expansão.',
                    'Falta planejamento.',
                ],
            ],

            self::LEVEL_WEAK => [
                'Gestão de Caixa' => [
                    'O caixa está desorganizado.',
                    'A empresa opera no escuro.',
                    'Existe risco imediato.',
                ],
                'Previsibilidade de Receita' => [
                    'A receita é imprevisível.',
                    'O faturamento oscila perigosamente.',
                    'Não há recorrência.',
                ],
                'Margem e Precificação' => [
                    'A empresa não sabe quanto ganha.',
                    'Os preços não sustentam o negócio.',
                    'A margem é crítica.',
                ],
                'Controle de Custos' => [
                    'Os custos estão fora de controle.',
                    'Existe desperdício constante.',
                    'A eficiência é baixa.',
                ],
                'Endividamento Empresarial' => [
                    'As dívidas sufocam o negócio.',
                    'O crédito tapa buracos.',
                    'O risco é alto.',
                ],
                'Separação PF x PJ' => [
                    'As finanças estão misturadas.',
                    'O caixa é usado sem critério.',
                    'Isso compromete a empresa.',
                ],
                'Gestão e Indicadores' => [
                    'Não existem indicadores.',
                    'As decisões são reativas.',
                    'A gestão é frágil.',
                ],
                'Risco e Segurança' => [
                    'Qualquer imprevisto ameaça o negócio.',
                    'Não há reserva.',
                    'A empresa está exposta.',
                ],
                'Dependência do Dono' => [
                    'Sem o dono, a empresa para.',
                    'Não existem processos.',
                    'O risco operacional é alto.',
                ],
                'Crescimento e Escala' => [
                    'Crescer agora seria perigoso.',
                    'A base não sustenta expansão.',
                    'O crescimento está travado.',
                ],
            ],
        ];
    }

    /* =========================
       HELPERS
    ========================== */

    private function random(array $items): string
    {
        return $items[array_rand($items)];
    }

    private function getSpecificDiagnostics($categoryScores, string $type): array
    {
        $bank = $this->narrativeBank($type);

        return $categoryScores->map(function ($data) use ($bank) {
            $level = $this->resolveLevel($data['percent']);

            return [
                'tema'     => $data['name'],
                'nivel'    => $level,
                'mensagem' => $this->random($bank[$level][$data['name']] ?? ['Diagnóstico indisponível.']),
                'acao'     => $this->suggestAction($level),
                'percent'  => round($data['percent'], 1),
            ];
        })->values()->all();
    }

    private function suggestAction(string $level): string
    {
        $actions = [
            self::LEVEL_STRONG => [
                'Mantenha a disciplina para que este pilar continue sustentando sua base.',
                'Use a segurança desta área para alavancar novos projetos com risco calculado.',
                'Consolide os ganhos e proteja este ativo contra variações de mercado.',
                'Excelente base. O foco aqui agora é apenas manter a eficiência atual.',
                'Use essa solidez para servir de exemplo na reestruturação dos outros pilares.'
            ],
            self::LEVEL_AVERAGE => [
                'Ajustes finos aqui podem destravar um novo patamar de rentabilidade.',
                'Otimize processos para transformar este pilar em uma força absoluta.',
                'Busque sofisticação técnica para reduzir desperdícios nesta área.',
                'Foque em pequenos ajustes para elevar este pilar ao nível de excelência.',
                'A área está funcional, mas a eficiência pode ser dobrada com atenção aos detalhes.'
            ],
            self::LEVEL_WEAK => [
                'Prioridade absoluta: estanque o sangramento financeiro nesta área imediatamente.',
                'Implemente um plano de contenção de danos para evitar a erosão do seu patrimônio.',
                'Não ignore este sinal. A falta de ação aqui compromete todos os seus outros ganhos.',
                'Intervenção urgente necessária para restaurar a viabilidade deste pilar.',
                'Foque 100% da sua energia em corrigir este gargalo antes de tentar expandir.'
            ],
        ];

        // Seleciona as opções com base no nível ou retorna uma padrão se não encontrar
        $options = $actions[$level] ?? ['Ajuste estratégico necessário.'];

        return $this->random($options);
    }

    private function getCurrentSituationPhrase($categoryScores, string $type): string
    {
        $avg = $categoryScores->avg('percent');

        if ($type === self::TYPE_PJ) {
            return match (true) {
                $avg >= 80 => 'A empresa está financeiramente preparada para crescer.',
                $avg >= 55 => 'A empresa opera, mas com riscos silenciosos.',
                default    => 'A prioridade é estabilizar o financeiro antes de crescer.',
            };
        }

        return match (true) {
            $avg >= 80 => 'Você está preparado para crescer com segurança.',
            $avg >= 55 => 'Você saiu do caos, mas ainda vive no limite do controle.',
            default    => 'Hoje sua prioridade é recuperar controle e estabilidade.',
        };
    }

    private function getGeneralSolution($categoryScores, string $type): string
    {
        $weakCount = $categoryScores->filter(fn($c) => $c['percent'] <= $this->thresholdWeak)->count();

        if ($type === self::TYPE_PJ) {
            if ($weakCount >= 3) return 'Antes de crescer, é preciso estancar os vazamentos financeiros.';
            if ($weakCount === 0) return 'A empresa tem base sólida para escalar.';
            return 'Com ajustes estratégicos, a empresa ganha fôlego.';
        }

        if ($weakCount >= 3) return 'O foco agora não é investir, é parar o vazamento de dinheiro.';
        if ($weakCount === 0) return 'Você tem estrutura. O próximo passo é crescimento.';
        return 'Com ajustes pontuais, você sai do modo sobrevivência.';
    }
}
