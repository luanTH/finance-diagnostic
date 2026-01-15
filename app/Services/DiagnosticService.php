<?php

namespace App\Services;

use App\Models\Option;

class DiagnosticService
{
    // Definimos os limiares de nota (0 a 100)
    private $thresholdStrong = 75; // Acima de 75% é Ponto Forte

    private $thresholdWeak = 45;   // Abaixo de 45% é Ponto para Desenvolver

    public function generate($selectedOptionIds)
    {
        $options = Option::with('question.category')->whereIn('id', $selectedOptionIds)->get();

        $totalScore = $options->sum('points');
        $categoryScores = $this->calculateCategoryScores($options);

        // Filtramos os grupos por performance
        $strengths = $categoryScores->filter(fn ($c) => $c['percent'] >= $this->thresholdStrong);
        $weaknesses = $categoryScores->filter(fn ($c) => $c['percent'] <= $this->thresholdWeak);

        return [
            'frase_atual' => $this->getCurrentSituationPhrase($categoryScores),
            'pontos_fortes' => $this->formatStrengths($strengths),
            'pontos_desenvolver' => $this->formatWeaknesses($weaknesses),
            'diagnosticos_especificos' => $this->getSpecificDiagnostics($categoryScores),
            'geral' => $this->getGeneralSolution($totalScore, $categoryScores),
            'score_total' => $totalScore,
        ];
    }

    private function calculateCategoryScores($options)
    {
        return $options->groupBy('question.category.name')->map(function ($items) {
            $score = $items->sum('points');
            $max = $items->count() * 3; // Cada pergunta vale no máximo 3

            return [
                'name' => $items->first()->question->category->name,
                'score' => $score,
                'max' => $max,
                'percent' => ($score / $max) * 100,
            ];
        });
    }

    private function formatStrengths($strengths)
    {
        if ($strengths->isEmpty()) {
            return ['Nenhum ponto forte identificado no momento. Precisamos blindar sua base urgentemente.'];
        }

        return $strengths->map(function ($s) {
            return "Excelente domínio em **{$s['name']}**, você demonstra maturidade acima da média nesta área.";
        })->values()->all();
    }

    private function formatWeaknesses($weaknesses)
    {
        if ($weaknesses->isEmpty()) {
            return ['Parabéns! Você não possui pontos críticos imediatos, foque em otimização.'];
        }

        return $weaknesses->map(function ($w) {
            return "A área de **{$w['name']}** requer atenção imediata para evitar perdas ou estagnação.";
        })->values()->all();
    }

    private function getSpecificDiagnostics($categoryScores)
    {
        return $categoryScores->map(function ($data) {
            $diag = '';
            $solucao = '';

            if ($data['percent'] >= $this->thresholdStrong) {
                $diag = 'Sólido e eficiente.';
                $solucao = 'Automatizar e buscar novos limites de rentabilidade.';
            } elseif ($data['percent'] <= $this->thresholdWeak) {
                $diag = 'Zona de risco. Falta de processos e clareza.';
                $solucao = 'Parar tudo e organizar o fluxo de caixa manualmente até retomar o controle.';
            } else {
                $diag = 'Estável, mas com vazamentos.';
                $solucao = 'Ajustar os pequenos gastos e otimizar a reserva.';
            }

            return [
                'tema' => $data['name'],
                'diag' => $diag,
                'solucao' => $solucao,
                'percent' => $data['percent'],
            ];
        })->values()->all();
    }

    private function getCurrentSituationPhrase($categoryScores)
    {
        $avg = $categoryScores->avg('percent');
        if ($avg >= 80) {
            return 'Sua saúde financeira é de elite. Você está pronto para grandes saltos.';
        }
        if ($avg >= 50) {
            return 'Você está no caminho certo, mas o motor ainda falha em alguns momentos.';
        }

        return 'Alerta: você está operando em modo de sobrevivência financeira.';
    }

    private function getGeneralSolution($total, $categoryScores)
    {
        // Aqui você pode criar regras de cruzamento complexas
        return 'Seu foco principal nos próximos 90 dias deve ser eliminar as falhas estruturais antes de pensar em novos investimentos.';
    }
}
