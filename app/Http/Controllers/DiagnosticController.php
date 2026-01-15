<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Diagnosis;
use App\Models\Lead;
use App\Models\Question;
use App\Services\DiagnosticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DiagnosticController extends Controller
{
    public function index()
    {
        $questions = Question::with(['category', 'options' => function ($q) {
            $q->orderBy('id', 'asc'); // Ou qualquer ordem que faça sentido
        }])->where('is_active', true)->orderBy('order')->get();

        return Inertia::render('Diagnostic/Form', [
            'questions' => $questions,
        ]);
    }

    public function store(Request $request, DiagnosticService $service)
    {
        return DB::transaction(function () use ($request, $service) {
            // 1. Salva ou atualiza o Lead
            $lead = Lead::updateOrCreate(
                ['email' => $request->lead['email']],
                [
                    'name' => $request->lead['name'],
                    'phone' => $request->lead['phone'],
                    'type' => $request->lead['type'],
                ]
            );

            // 2. Salva as respostas
            foreach ($request->answers as $questionId => $optionId) {
                Answer::create([
                    'lead_id' => $lead->id,
                    'question_id' => $questionId,
                    'option_id' => $optionId,
                ]);
            }

            // 3. Gera o Diagnóstico Inteligente
            $report = $service->generate($request->answers);

            // 4. Salva o registro do diagnóstico
            $diagnostic = Diagnosis::create([
                'lead_id' => $lead->id,
                'results' => json_encode($report),
                'total_score' => $report['score_total'],
            ]);

            // 5. Retorna para o Front com os dados
            return redirect()->route('diagnostic.show', $diagnostic->id);
        });
    }

    public function show($id)
    {
        $diagnostic = Diagnosis::with('lead')->findOrFail($id);

        return Inertia::render('Diagnostic/Result', [
            'report' => json_decode($diagnostic->results),
            'lead' => $diagnostic->lead,
        ]);
    }
}
