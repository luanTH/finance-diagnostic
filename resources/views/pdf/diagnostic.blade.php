<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Relatório Estratégico - {{ $lead->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #1e293b; line-height: 1.5; margin: 0; padding: 0; font-size: 11px; }

        @page { margin: 120px 50px 80px 50px; }

        .header { position: fixed; top: -90px; left: 0; right: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; text-align: right; color: #94a3b8; font-size: 9px; text-transform: uppercase;}

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

        .intro-phrase-text { font-size: 14px; color: #1e1b4b; font-style: italic; }

        .section-title { font-size: 13px; font-weight: bold; margin-top: 25px; margin-bottom: 10px; color: #1e293b; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; padding-bottom: 3px;page-break-after: avoid;}

        .card { padding: 15px; border-radius: 10px; margin-bottom: 10px;page-break-inside: avoid;display: block; }
        .card-strong { background-color: #ecfdf5; color: #065f46; border: 1px solid #d1fae5; }
        .card-weak { background-color: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table tr { page-break-inside: avoid; }
        th { background-color: #f8fafc; text-align: left; padding: 10px; font-size: 9px; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px 10px; border-bottom: 1px solid #f8fafc; vertical-align: top; }
        thead { display: table-header-group; }

        .p-name { font-weight: bold; color: #1e293b; font-size: 11px; }
        .p-msg { font-size: 10px; color: #475569; }
        .p-action { font-size: 10px; font-weight: bold; color: #4f46e5; }

        /* NOVO DESIGN: Veredicto / Conclusão */
        .section-divider {
            border-top: 1px solid #e2e8f0;
            margin: 40px 0;
            width: 100%;
        }

        .veredicto-container {
            margin-top: 20px;
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #065f46;
            border-radius: 4px;
            position: relative;
            page-break-inside: avoid;
        }

        .veredicto-header {
            position: absolute;
            top: -12px;
            left: 20px;
            background: #ffffff;
            padding: 0 10px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            color: #065f46;
        }

        .veredicto-content {
            font-size: 13px;
            line-height: 1.6;
            color: #1e293b;
            text-align: center;
            font-style: italic; /* Diferencia da frase inicial do topo */
        }

        /* CTA - Focado em Espaço e Botão */
        .cta-container {
            page-break-inside: avoid;
            text-align: center;
            padding: 20px 0;
        }

        .cta-title {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 15px;
        }

        .cta-text {
            font-size: 13px;
            color: #64748b;
            max-width: 80%;
            margin: 0 auto 25px auto;
            line-height: 1.5;
        }

        .cta-button {
            display: inline-block;
            padding: 14px 35px;
            background-color: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .bloco-analise-grupo {
            page-break-inside: avoid;
        }
        /* Classe utilitária para forçar quebra se a lógica do Blade decidir */
        .page-break {
            page-break-before: always;
        }

        .keep-together { page-break-inside: avoid; }

        /* Força quebra de página antes do elemento */
        .force-page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
   @php
    // Pesos aproximados para o DomPDF (ajuste conforme sua fonte)
    $pesoTitulo = 3;
    $pesoLinha = 1.2;
    $limitePagina1 = 22; // Capacidade total de "unidades" da sua primeira página

    // Cálculo do Bloco A (Fortes + Prioridades)
    $itensFortes = count($report['pontos_fortes']) ?: 1;
    $itensPrioridades = count($report['pontos_melhorar']) ?: 1;
    $pesoBlocoA = ($itensFortes * $pesoLinha) + ($itensPrioridades * $pesoLinha) + ($pesoTitulo * 2);

    // Cálculo do Bloco B (Desenvolver)
    $itensDesenvolver = count($report['pontos_desenvolver']);
    $pesoBlocoB = $itensDesenvolver > 0 ? ($itensDesenvolver * $pesoLinha) + $pesoTitulo : 0;

    // DECISÃO INTELIGENTE
    // 1. O Bloco B deve mudar de página? (Se o Bloco A + B estourarem o limite)
    $blocoBVaiParaPagina2 = ($pesoBlocoA + $pesoBlocoB) > $limitePagina1;

    // 2. A Tabela deve mudar de página?
    // Ela SEMPRE muda se o conteúdo for "pouco" (para ficar limpa na pag 2)
    // OU se o Bloco B já foi pra lá.
    $tabelaVaiParaPagina2 = true;
@endphp
    <div class="header">RELATÓRIO FINANCEIRO {{$lead->type == 'pf' ? " PF " : " PJ "}} | {{ $lead->name }}</div>

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
        <span class="intro-phrase-text">"{!! $report['frase_atual'] !!}"</span>
    </div>

    <div class="section-title">Resumo de Performance</div>


    <div class="card card-strong">
        <strong style="font-size: 12px;"><span>&#10003;</span> PONTOS FORTES</strong>
            <ul style="margin: 8px 0 0 15px; padding: 0;">
                @if (count($report['pontos_fortes']))
                    @foreach($report['pontos_fortes'] as $ponto)
                        <li style="margin-bottom: 5px;">{!! $ponto !!}</li>
                    @endforeach
                @else
                    <li style="margin-bottom: 5px;">Nenhum ponto forte encontrado. Converse com um especialista para regulizarizar suas finanças o quanto antes.</li>
                @endif
            </ul>
    </div>

    <div class="card card-weak">
        <strong style="font-size: 12px;">⚠️ ÊNFASES DE PRIORIDADES</strong>
            <ul style="margin: 8px 0 0 15px; padding: 0;">
                @if (count($report['pontos_melhorar']))
                    @foreach($report['pontos_melhorar'] as $ponto)
                        <li style="margin-bottom: 5px;">{!! $ponto !!}</li>
                    @endforeach
                @else
                    <li style="margin-bottom: 5px;">Nenhuma prioridade detectada por hora. Converse com um especialista para se manter estável e crescer ainda mais.</li>
                @endif
            </ul>
    </div>

{{-- Aqui decidimos se este bloco começa na página atual ou na próxima --}}
{{-- INÍCIO DO BLOCO INTELIGENTE --}}
<div class="{{ $blocoBVaiParaPagina2 ? 'page-break' : '' }}">
    @if(count($report['pontos_desenvolver']) > 0)
         <div class="card" style="background-color: #fffbeb; color: #92400e; border: 1px solid #fef3c7; margin-bottom: 10px;">
            <div style="display: block; margin-bottom: 8px;">
                <strong style="font-size: 12px; color: #92400e;"> ➔ PONTOS A DESENVOLVER</strong>
            </div>
            <ul style="margin: 5px 0 0 25px; padding: 0; list-style-type: none;">
                @foreach($report['pontos_desenvolver'] as $ponto)
                    <li style="margin-bottom: 5px; position: relative;">
                        <span style="color: #f59e0b; position: absolute; left: -15px;">•</span> {!! $ponto !!}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Se o Bloco B NÃO mudou de página, mas queremos a tabela na 2, forçamos aqui --}}
    @if(!$blocoBVaiParaPagina2 && $tabelaVaiParaPagina2)
        <div class="page-break"></div>
    @endif

    <div class="section-title">Análise Detalhada por Pilar</div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f8fafc;">
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
    </div>
</div>

<div class="veredicto-container">
    <div class="veredicto-header">Veredito Estratégico</div>
    <div class="veredicto-content">
        {!! $report['geral'] !!}
    </div>
</div>

<div class="cta-container">
    @if($lead->type === 'pj')
        {{-- Versão PJ --}}
        <div class="cta-title">Como levar sua empresa ao próximo nível?</div>
        <p class="cta-text">
            Este diagnóstico é o ponto de partida. O <strong>Planejamento Financeiro Empresarial</strong> é o que separa empresas que apenas sobrevivem daquelas que escalam com lucro real.
        </p>
        <a href="https://wa.me/5598984068970?text=Olá! Gostaria de falar sobre o Planejamento Estratégico para minha empresa." class="cta-button">
            Falar com um Consultor de Empresas
        </a>
    @else
        {{-- Versão PF --}}
        <div class="cta-title">O que falta para sua liberdade financeira?</div>
        <p class="cta-text">
            Um diagnóstico sem ação é apenas papel. O <strong>Planejamento Completo</strong> é o mapa que organiza seus investimentos e protege o futuro da sua família.
        </p>
        <a href="https://wa.me/5598984068970?text=Olá! Gostaria de saber mais sobre o Planejamento Financeiro Completo." class="cta-button">
            Quero meu Planejamento Personalizado
            </a>
    @endif
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
