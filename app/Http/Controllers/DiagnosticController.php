<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Diagnosis;
use App\Models\Lead;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Mail\DiagnosticReportMail;
use App\Services\DiagnosticServicePF;
use App\Services\DiagnosticServicePJ;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Rules\Recaptcha;
use Illuminate\Validation\Rule;

class DiagnosticController extends Controller
{
    public function __construct(private DiagnosticServicePF $servicePF, private DiagnosticServicePJ $servicePJ) {}

    public function index(Request $request)
    {
        $type = str_ends_with($request->url(), '/pj') ? 'pj' : 'pf';

        $questions = Question::with([
            'category',
            'options' => function ($q) {
                $q->orderBy('id', 'asc');
            }
        ])
            ->where('is_active', true)
            ->whereHas('category', function ($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('order')
            ->get();

        return Inertia::render('Diagnostic/Form', [
            'questions' => $questions,
            'type' => $type
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validação do Lead
            'lead.name' => ['required', 'string', 'min:3', 'max:255'],
            'lead.email' => ['required', 'email:rfc,dns'], // Valida formato e existência do domínio
            'lead.phone' => ['required', 'string', 'min:14', 'max:15'], // Valida a máscara (00) 00000-0000
            'lead.type' => ['required', Rule::in(['pf', 'pj'])], // Garante que o tipo seja um dos dois permitidos
            'lead.consent' => ['required', 'accepted'], // Garante que o checkbox foi marcado (true/1/on)
            'lead.captcha_token' => ['required', new Recaptcha],

            // Validação das Respostas
            'answers' => ['required', 'array', 'min:1'], // Garante que é um array e não está vazio
            'answers.*' => ['required', 'integer', 'exists:options,id'], // Garante que cada opção selecionada existe no banco
        ]);

        return DB::transaction(function () use ($request) {
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

            if ($lead->type == 'pf') {
                $report = $this->servicePF->generate($request->answers, $lead->type);
            } else {
                $report = $this->servicePJ->generate($request->answers, $lead->type);
            }

            // 4. Salva o registro do diagnóstico
            $diagnostic = Diagnosis::create([
                'lead_id' => $lead->id,
                'results' => json_encode($report),
                'total_score' => $report['score_total'],
            ]);

            $lead->load(['answers.question.category', 'answers.option']);

            // Passamos o report e o lead para a view
            $pdf = Pdf::loadView('pdf.diagnostic', [
                'lead' => $lead,
                'report' => $report
            ]);

            $pdfBinary = $pdf->output();

            Mail::to($lead->email)->send(new DiagnosticReportMail($lead, $pdfBinary));

            // 5. Retorna para o Front com os dados
            return back()->with('success', 'Diagnóstico enviado com sucesso!');
            // return redirect()->route('diagnostic.show', $diagnostic->id);
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

    public function pdfView(int $id)
    {
        $lead = Lead::with(['answers.question.category', 'answers.option'])->findOrFail($id);

        // 2. Extrai apenas os IDs das opções selecionadas para o serviço
        $optionIds = $lead->answers->pluck('option_id')->toArray();

        if ($lead->type == 'pf') {
            $report = $this->servicePF->generate($optionIds, $lead->type);
        } else {
            $report = $this->servicePJ->generate($optionIds, $lead->type);
        }

        // 4. Renderiza o PDF para visualização no navegador (stream)
        return Pdf::loadView('pdf.diagnostic', compact('lead', 'report'))
            ->setPaper('a4', 'portrait')
            ->stream("diagnostico_{$lead->name}.pdf");
    }

    public function reenviarEmail(int $id)
    {
        $lead = Lead::with(['answers.question.category', 'answers.option'])->findOrFail($id);

        // 2. Extrai apenas os IDs das opções selecionadas para o serviço
        $optionIds = $lead->answers->pluck('option_id')->toArray();

        if ($lead->type == 'pf') {
            $report = $this->servicePF->generate($optionIds, $lead->type);
        } else {
            $report = $this->servicePJ->generate($optionIds, $lead->type);
        }

        $lead->load(['answers.question.category', 'answers.option']);

        // Passamos o report e o lead para a view
        $pdf = Pdf::loadView('pdf.diagnostic', [
            'lead' => $lead,
            'report' => $report
        ]);

        $pdfBinary = $pdf->output();

        Mail::to($lead->email)->send(new DiagnosticReportMail($lead, $pdfBinary));

        return response()->json('Email reenviado');
    }
}
