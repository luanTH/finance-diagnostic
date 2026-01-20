<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório Estratégico - {{ $lead->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; font-size: 11px; }

        @page { margin: 120px 50px 80px 50px; }

        .header { position: fixed; top: -90px; left: 0; right: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; text-align: right; color: #94a3b8; font-size: 9px; text-transform: uppercase;}

        /* Footer com links clicáveis */
        .footer { position: fixed; bottom: -50px; left: 0; right: 0; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; color: #64748b; font-size: 9px; line-height: 1.3; }
        .footer a { text-decoration: none; color: #64748b; }

        .logo-container { text-align: center; margin-bottom: 20px; width: 100%; }
        .logo-placeholder { width: 80px; height: auto; }

        .title-section { text-align: center; margin-bottom: 20px; }
        .main-title { font-size: 20px; font-weight: bold; color: #0f172a; margin: 0; text-transform: uppercase; }
        .sub-title { font-size: 12px; color: #4f46e5; margin-top: 3px; font-weight: bold; }

        .intro-phrase-box {
            background: #f1f5ff;
            border-left: 6px solid #4f46e5;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            border-radius: 0 8px 8px 0;
        }
        .intro-phrase-text { font-size: 14px; font-weight: bold; color: #1e1b4b; font-style: italic; }

        .section-title { font-size: 13px; font-weight: bold; margin-top: 25px; margin-bottom: 10px; color: #1e293b; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; padding-bottom: 3px; }

        .card { padding: 15px; border-radius: 10px; margin-bottom: 10px; }
        .card-strong { background-color: #ecfdf5; color: #065f46; border: 1px solid #d1fae5; }
        .card-weak { background-color: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8fafc; text-align: left; padding: 10px; font-size: 9px; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px 10px; border-bottom: 1px solid #f8fafc; vertical-align: top; }
        .p-name { font-weight: bold; color: #1e293b; font-size: 11px; }
        .p-msg { font-size: 10px; color: #475569; }
        .p-action { font-size: 10px; font-weight: bold; color: #4f46e5; }

        /* NOVO DESIGN: Veredicto / Conclusão */
        .veredicto-container {
            margin-top: 30px;
            padding: 20px;
            background: linear-gradient(to right, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0;
            border-left: 5px solid #0f172a;
            border-radius: 8px;
        }
        .veredicto-header { color: #4f46e5; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .veredicto-content { font-size: 13px; color: #1e293b; line-height: 1.6; font-weight: 500; }

        /* SEÇÃO DE CHAMADA PARA AÇÃO (CTA) */
        .cta-section {
            margin-top: 40px;
            text-align: center;
            padding: 30px;
            background-color: #fdfeff;
            border: 2px dashed #cbd5e1;
            border-radius: 15px;
        }
        .cta-title { font-size: 16px; font-weight: bold; color: #4f46e5; margin-bottom: 10px; }
        .cta-text { font-size: 12px; color: #475569; margin-bottom: 15px; }
        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            font-size: 12px;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    <div class="header">RELATÓRIO FINANCEIRO | {{ $lead->name }}</div>

    <div class="footer">
        <strong>Plannfinn Family Office Negócios e Finanças</strong><br>
        <a href="mailto:plannfinn@gmail.com">plannfinn@gmail.com</a> |
        <a href="https://wa.me/5598984068970?text=Olá! Acabei de ler meu Diagnóstico Financeiro e gostaria de agendar um Planejamento Completo.">(98) 98406-8970</a><br>
        <a href="https://maps.app.goo.gl/9HhZyrAjfrerWFYCA">
            Prédio comercial Tech office - sala 1125 - Ponta do Farol | São Luís - MA
        </a>
    </div>

    <div class="logo-container">
        <img src="{{ public_path('assets/images/plannfinn.png') }}" class="logo-placeholder" alt="Logo Empresa">
    </div>

    <div class="title-section">
        <h1 class="main-title">Diagnóstico de Saúde Financeira</h1>
        <div class="sub-title">Análise Estratégica Individualizada</div>
    </div>

    <div class="intro-phrase-box">
        <span class="intro-phrase-text">"{{ $report['frase_atual'] }}"</span>
    </div>

    <div class="section-title">Resumo de Performance</div>

    <div class="card card-strong">
        <strong style="font-size: 12px;"><span>&#10003;</span> Seus Pontos Fortes</strong>
        <ul style="margin: 8px 0 0 15px; padding: 0;">
            @foreach($report['pontos_fortes'] as $ponto)
                <li style="margin-bottom: 5px;">{!! $ponto !!}</li>
            @endforeach
        </ul>
    </div>

    <div class="card card-weak">
        <strong style="font-size: 12px;">⚠️ Pontos de Atenção</strong>
        <ul style="margin: 8px 0 0 15px; padding: 0;">
            @foreach($report['pontos_desenvolver'] as $ponto)
                <li style="margin-bottom: 5px;">{!! $ponto !!}</li>
            @endforeach
        </ul>
    </div>

    @if(count($report['pontos_melhorar']) > 0)
        <div class="card" style="background-color: #fffbeb; color: #92400e; border: 1px solid #fef3c7; margin-bottom: 10px;">
            <div style="display: block; margin-bottom: 8px;">
                <strong style="font-size: 12px; color: #92400e;"> ➔ Oportunidades de Melhoria</strong>
            </div>
            <ul style="margin: 5px 0 0 25px; padding: 0; list-style-type: none;">
                @foreach($report['pontos_melhorar'] as $ponto)
                    <li style="margin-bottom: 5px; position: relative;">
                        <span style="color: #f59e0b; position: absolute; left: -15px;">•</span> {!! $ponto !!}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

<div class="page-break"></div>
    <div class="section-title">Análise Detalhada por Pilar</div>
    <table>
        <thead>
            <tr>
                <th width="25%">Pilar Analisado</th>
                <th width="45%">Diagnóstico Situacional</th>
                <th width="30%">Ação Recomendada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['diagnosticos_especificos'] as $diag)
            <tr>
                <td>
                    <span class="p-name">{{ $diag['tema'] }}</span><br>
                    <small style="color: #94a3b8; font-size: 8px;">Score: {{ $diag['percent'] }}%</small>
                </td>
                <td class="p-msg">{{ $diag['mensagem'] }}</td>
                <td class="p-action">{{ $diag['acao'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="veredicto-container">
        <div class="veredicto-header">Conclusão Estratégica</div>
        <div class="veredicto-content">
            {{ $report['geral'] }}
        </div>
    </div>

    <div class="cta-section">
        <div class="cta-title">Qual o próximo passo para sua liberdade?</div>
        <p class="cta-text">
            Este diagnóstico é apenas o primeiro passo. Um <strong>Planejamento Financeiro Completo</strong> organiza seus investimentos, protege seu patrimônio e acelera sua independência.
        </p>
        <a href="https://wa.me/5598984068970?text=Olá! Gostaria de saber mais sobre o Planejamento Financeiro Completo." class="cta-button">
            Quero meu Planejamento Completo
        </a>
    </div>

    <div class="page-break"></div>

    <div class="section-title">Apêndice: Base de Dados</div>
    <p style="color: #64748b; font-size: 10px; margin-bottom: 15px;">Abaixo constam as respostas exatas que serviram de base para esta análise técnica.</p>

    @foreach($lead->answers as $answer)
        <div style="margin-bottom: 12px; border-left: 2px solid #e2e8f0; padding-left: 12px;">
            <div style="font-size: 11px; font-weight: bold; color: #1e293b;">{{ $answer->question->text }}</div>
            <div style="font-size: 11px; color: #4f46e5; margin-top: 2px;">R: {{ $answer->option->text }}</div>
        </div>
    @endforeach

</body>
</html>
