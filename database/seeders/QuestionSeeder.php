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
        $data = [
            'Organização Financeira' => [
                'Você controla suas finanças de alguma forma?' => [
                    'Não faço nenhum controle' => 1,
                    'Controle informal ou esporádico' => 2,
                    'Uso app, planilha ou sistema' => 3,
                ],
                'Você sabe exatamente quanto ganha por mês?' => [
                    'Não sei ao certo' => 1,
                    'Tenho uma estimativa' => 2,
                    'Sei exatamente' => 3,
                ],
            ],

            'Consciência de Gastos' => [
                'Você sabe para onde vai a maior parte do seu dinheiro?' => [
                    'Não faço ideia' => 1,
                    'Tenho uma noção geral' => 2,
                    'Tenho total clareza' => 3,
                ],
                'Você revisa seus gastos mensalmente?' => [
                    'Nunca' => 1,
                    'Às vezes' => 2,
                    'Todo mês' => 3,
                ],
            ],

            'Endividamento' => [
                'Quanto da sua renda está comprometida com dívidas?' => [
                    'Mais de 50%' => 1,
                    'Entre 20% e 50%' => 2,
                    'Menos de 20% ou nada' => 3,
                ],
                'Você depende de crédito para fechar o mês?' => [
                    'Sempre' => 1,
                    'Às vezes' => 2,
                    'Raramente ou nunca' => 3,
                ],
            ],

            'Reserva e Segurança' => [
                'Você possui reserva de emergência?' => [
                    'Não possuo' => 1,
                    'Tenho menos de 3 meses' => 2,
                    'Tenho 6 meses ou mais' => 3,
                ],
                'Se perdesse sua renda hoje, por quanto tempo sobreviveria?' => [
                    'Menos de 1 mês' => 1,
                    'De 1 a 3 meses' => 2,
                    'Mais de 6 meses' => 3,
                ],
            ],

            'Planejamento' => [
                'Você possui metas financeiras definidas?' => [
                    'Não tenho metas' => 1,
                    'Tenho metas vagas' => 2,
                    'Tenho metas claras e escritas' => 3,
                ],
                'Você planeja seus gastos futuros?' => [
                    'Não planejo' => 1,
                    'Planejo parcialmente' => 2,
                    'Planejo com antecedência' => 3,
                ],
            ],

            'Mentalidade Financeira' => [
                'Como você toma decisões financeiras importantes?' => [
                    'Por impulso ou emoção' => 1,
                    'Analiso um pouco antes' => 2,
                    'Analiso com estratégia' => 3,
                ],
                'Você se sente no controle da sua vida financeira?' => [
                    'Não, me sinto perdido' => 1,
                    'Às vezes no controle' => 2,
                    'Sim, totalmente no controle' => 3,
                ],
            ],

            'Crescimento e Futuro' => [
                'Você investe pensando no longo prazo?' => [
                    'Não invisto' => 1,
                    'Invisto sem estratégia' => 2,
                    'Invisto com foco no futuro' => 3,
                ],
                'Você busca aumentar sua renda?' => [
                    'Não penso nisso' => 1,
                    'Penso, mas não ajo' => 2,
                    'Busco ativamente' => 3,
                ],
            ],
        ];

        foreach ($data as $catName => $questions) {
            $category = Category::create(['name' => $catName]);
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
