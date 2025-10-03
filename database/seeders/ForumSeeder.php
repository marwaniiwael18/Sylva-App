<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ForumPost;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques utilisateurs de test
        $alice = User::firstOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name' => 'Alice Martin',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        $bob = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name' => 'Bob Dupont',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        $charlie = User::firstOrCreate(
            ['email' => 'charlie@example.com'],
            [
                'name' => 'Charlie Rousseau',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );

        // Créer un événement de test
        $event = Report::where('status', 'validated')->first();

        // Créer des posts de forum
        $post1 = ForumPost::create([
            'title' => 'Comment entretenir les oliviers plantés la semaine dernière ?',
            'content' => "Bonjour à tous,

J'ai participé à la plantation d'oliviers la semaine dernière dans le parc de la ville. C'était une expérience formidable ! 

Maintenant je me demande : quels sont les meilleurs conseils pour l'entretien de ces jeunes arbres ? À quelle fréquence faut-il les arroser ? Y a-t-il des signes particuliers à surveiller ?

J'aimerais contribuer à leur bon développement. Merci d'avance pour vos conseils !",
            'author_id' => $alice->id,
            'related_event_id' => $event ? $event->id : null,
        ]);

        $post2 = ForumPost::create([
            'title' => 'Proposition : Création d\'un potager communautaire',
            'content' => "Hello la communauté !

J'ai une idée que j'aimerais partager avec vous : que pensez-vous de créer un potager communautaire dans le quartier ?

On pourrait :
- Cultiver des légumes de saison
- Partager les récoltes
- Organiser des ateliers de jardinage pour les enfants
- Créer un espace de convivialité

J'ai repéré un terrain qui pourrait convenir. Qui serait intéressé pour rejoindre ce projet ?",
            'author_id' => $bob->id,
            'related_event_id' => null,
        ]);

        $post3 = ForumPost::create([
            'title' => 'Retour d\'expérience : Compostage en appartement',
            'content' => "Salut tout le monde !

Ça fait maintenant 6 mois que j'ai installé un lombricomposteur dans mon appartement. Je voulais partager mon expérience avec vous.

Les + :
✓ Réduction significative des déchets organiques
✓ Production d'un excellent compost
✓ Pas d'odeurs (contrairement à ce qu'on pourrait penser)
✓ Les enfants adorent observer les vers !

Les - :
✗ Il faut être régulier dans l'alimentation
✗ Attention à l'équilibre humidité
✗ Investissement initial un peu élevé

Je recommande vivement ! Des questions ?",
            'author_id' => $charlie->id,
            'related_event_id' => null,
        ]);

        // Ajouter des commentaires
        Comment::create([
            'content' => "Super initiative Alice ! Pour l'arrosage des oliviers, il faut être particulièrement attentif les premières semaines. Un arrosage abondant mais peu fréquent est préférable. Évitez l'eau stagnante autour des racines.",
            'author_id' => $bob->id,
            'forum_post_id' => $post1->id,
        ]);

        Comment::create([
            'content' => "Merci Bob pour ces conseils ! J'ai aussi entendu dire qu'il fallait surveiller les feuilles jaunissantes. Est-ce un signe de sur-arrosage ou de maladie ?",
            'author_id' => $alice->id,
            'forum_post_id' => $post1->id,
        ]);

        Comment::create([
            'content' => "Excellent projet Bob ! Je suis totalement partant. J'ai déjà de l'expérience en permaculture. On pourrait prévoir une réunion pour en discuter ?",
            'author_id' => $charlie->id,
            'forum_post_id' => $post2->id,
        ]);

        Comment::create([
            'content' => "Je suis intéressée aussi ! Ma fille de 8 ans adorerait participer aux ateliers. Où se trouve le terrain que tu as repéré ?",
            'author_id' => $alice->id,
            'forum_post_id' => $post2->id,
        ]);

        Comment::create([
            'content' => "Charlie, ton retour d'expérience est très intéressant ! J'hésite depuis longtemps à me lancer. Quel modèle de lombricomposteur recommandes-tu pour débuter ?",
            'author_id' => $bob->id,
            'forum_post_id' => $post3->id,
        ]);
    }
}
