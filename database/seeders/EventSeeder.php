<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user as organizer (or create one if none exists)
        $organizer = \App\Models\User::first();
        
        if (!$organizer) {
            $organizer = \App\Models\User::create([
                'name' => 'Event Organizer',
                'email' => 'organizer@sylva.com',
                'password' => bcrypt('password'),
                'is_admin' => true
            ]);
        }

        $events = [
            [
                'title' => 'Journée de plantation d\'arbres - Parc Belvedère',
                'description' => 'Rejoignez-nous pour une journée dédiée à la plantation d\'arbres fruitiers et d\'espèces locales dans le parc Belvedère. Activité familiale avec ateliers éducatifs pour les enfants sur l\'importance de la biodiversité urbaine.',
                'date' => now()->addDays(7)->setTime(9, 0),
                'location' => 'Parc Belvedère, Tunis - 36.8064, 10.1818',
                'type' => 'Tree Planting',
                'status' => 'active',
                'organized_by_user_id' => $organizer->id,
                'max_participants' => 50,
                'current_participants' => 12
            ],
            [
                'title' => 'Maintenance de la forêt urbaine de Carthage',
                'description' => 'Opération de maintenance et d\'entretien des espaces verts de Carthage. Au programme: élagage, nettoyage des sentiers, installation de panneaux informatifs et réparation des systèmes d\'irrigation.',
                'date' => now()->addDays(14)->setTime(8, 30),
                'location' => 'Site archéologique de Carthage - 36.8534, 10.3229',
                'type' => 'Maintenance',
                'status' => 'active',
                'organized_by_user_id' => $organizer->id,
                'max_participants' => 30,
                'current_participants' => 8
            ],
            [
                'title' => 'Campagne de sensibilisation: "Tunis Verte 2025"',
                'description' => 'Campagne de sensibilisation sur l\'importance de la végétalisation urbaine. Distribution de plants, stands informatifs, démonstrations de techniques de jardinage urbain et conférences sur l\'impact environnemental.',
                'date' => now()->addDays(21)->setTime(10, 0),
                'location' => 'Avenue Habib Bourguiba, Tunis Centre - 36.8008, 10.1864',
                'type' => 'Awareness',
                'status' => 'active',
                'organized_by_user_id' => $organizer->id,
                'max_participants' => 100,
                'current_participants' => 25
            ],
            [
                'title' => 'Atelier: Jardinage urbain et permaculture',
                'description' => 'Atelier pratique sur les techniques de jardinage urbain, la permaculture en milieu urbain, et la création de jardins communautaires. Formation théorique et pratique avec remise de certificats.',
                'date' => now()->addDays(10)->setTime(14, 0),
                'location' => 'Centre culturel de La Marsa - 36.8784, 10.3247',
                'type' => 'Workshop',
                'status' => 'active',
                'organized_by_user_id' => $organizer->id,
                'max_participants' => 25,
                'current_participants' => 15
            ]
        ];

        foreach ($events as $eventData) {
            \App\Models\Event::create($eventData);
        }

        $this->command->info('4 mock events created successfully!');
    }
}
