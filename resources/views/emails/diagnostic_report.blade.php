<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto; padding: 0; color: #1e293b; background-color: #ffffff; border: 1px solid #e2e8f0;">

    <div style="background-color: #f8fafc; padding: 30px; text-align: center; border-bottom: 4px solid #4f46e5;">
        <img src="{{ $message->embed(public_path('assets/images/plannfinn.png')) }}" alt="Plannfinn Logo" style="width: 120px; height: auto;">
    </div>

    <div style="padding: 40px 30px;">
        <h2 style="color: #0f172a; font-size: 22px; margin-top: 0;">Olá, {{ $lead->name }}!</h2>

        @if($lead->type === 'pj')
            {{-- TEXTO FOCADO EM EMPRESA (PJ) --}}
            <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                É um prazer acompanhar a evolução do seu negócio. Transformar dados operacionais em <strong>inteligência estratégica</strong> é o caminho mais seguro para aumentar a lucratividade e garantir a perenidade da sua empresa.
            </p>

            <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                Conforme solicitado, anexamos o seu <strong>Diagnóstico de Saúde Financeira Empresarial</strong>. Este documento apresenta uma análise técnica dos processos financeiros do seu negócio e os pontos de melhoria para destravar o seu crescimento.
            </p>

            <div style="background: #f1f5ff; padding: 25px; border-radius: 12px; margin: 30px 0; border-left: 5px solid #4f46e5;">
                <p style="margin: 0; font-size: 14px; color: #1e1b4b; font-weight: 500;">
                    <strong>Qual o próximo nível da sua gestão?</strong><br>
                    Um diagnóstico aponta os gargalos; a <strong>Gestão Estratégica</strong> é a solução. Otimizamos seu fluxo de caixa, revisamos sua precificação e preparamos sua operação para escalar com lucro real.
                </p>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="https://wa.me/5598984068970?text=Olá! Recebi o diagnóstico da minha empresa e quero agendar uma reunião sobre Gestão Estratégica."
                   style="background-color: #4f46e5; color: #ffffff; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block;">
                    Falar com Consultor de Negócios
                </a>
            </div>
        @else
            {{-- TEXTO FOCADO EM PESSOA FÍSICA (PF) --}}
            <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                É um prazer acompanhar você nesta jornada rumo à clareza e solidez financeira. Transformar números em estratégia é o primeiro passo para a construção de um patrimônio resiliente.
            </p>

            <p style="font-size: 15px; line-height: 1.6; color: #475569;">
                Conforme solicitado, anexamos o seu <strong>Diagnóstico de Saúde Financeira</strong>. Este documento contém uma análise técnica dos seus pilares atuais e as recomendações iniciais de nossa equipe.
            </p>

            <div style="background: #f1f5ff; padding: 25px; border-radius: 12px; margin: 30px 0; border-left: 5px solid #4f46e5;">
                <p style="margin: 0; font-size: 14px; color: #1e1b4b; font-weight: 500;">
                    <strong>O que vem a seguir?</strong><br>
                    Um diagnóstico identifica os sintomas; o <strong>Planejamento Financeiro Completo</strong> é a cura. Ele organiza seus investimentos, protege sua família e acelera sua independência.
                </p>
            </div>

            <div style="text-align: center; margin: 40px 0;">
                <a href="https://wa.me/5598984068970?text=Olá! Recebi meu diagnóstico e quero agendar meu planejamento financeiro completo."
                   style="background-color: #4f46e5; color: #ffffff; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px; display: inline-block;">
                    Agendar Planejamento Completo
                </a>
            </div>
        @endif

        <p style="font-size: 14px; color: #64748b; text-align: center;">
            Se tiver qualquer dúvida sobre os dados apresentados, nossa equipe está à disposição para uma breve reunião de alinhamento.
        </p>
    </div>

    <div style="background-color: #0f172a; padding: 30px; color: #94a3b8; font-size: 12px; text-align: center;">
        <strong style="color: #ffffff; font-size: 14px;">Plannfinn Family Office</strong><br>
        Negócios e Finanças<br><br>
        (98) 98406-8970 | plannfinn@gmail.com<br>
        Tech Office, Sala 1125 - Ponta do Farol | São Luís - MA
        <div style="margin-top: 20px; border-top: 1px solid #334155; padding-top: 20px;">
            Este é um e-mail gerado automaticamente com base nas informações fornecidas pelo usuário.
        </div>
    </div>
</div>
