<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        
        // Define realistic category names and descriptions
        $categories = [
            [
                'title' => 'Littérature Classique',
                'description' => 'Explorez les chefs-d\'œuvre intemporels de la littérature mondiale, des grands romans aux pièces de théâtre emblématiques.'
            ],
            [
                'title' => 'Science-Fiction et Fantasy',
                'description' => 'Découvrez des univers imaginaires fascinants, des mondes futuristes aux royaumes magiques.'
            ],
            [
                'title' => 'Littérature Contemporaine',
                'description' => 'Les œuvres marquantes des dernières décennies qui reflètent notre société moderne et ses questionnements.'
            ],
        ];
        
        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setTitle($categoryData['title'])
                     ->setDescription($categoryData['description'])
            ;
            $manager->persist($category);

            // Create realistic articles based on the category
            $articlesData = $this->getArticlesForCategory($categoryData['title']);
            
            foreach ($articlesData as $index => $articleData) {
                $article = new Article();
                
                $article->setTitle($articleData['title'])
                    ->setContent($articleData['content'])
                    ->setImage($this->getImageUrlForArticle($categoryData['title'], $articleData['title']))
                    ->setCreatedAt($faker->dateTimeBetween('-8 months'))
                    ->setAuthor($this->getReference('user-' . mt_rand(1, 10), User::class))
                    ->setGenreLitteraire($articleData['genre'])
                    ->setCategory($category)
                ;
                $manager->persist($article);

                // Create comments with user references
                for ($k = 1, $kMax = mt_rand(4, 8); $k <= $kMax; ++$k) {
                    $comment = new Comment();
                    
                    // Get a realistic comment from our collection
                    $commentContent = $this->getRealisticComment();
                    $now = new \DateTime();
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;
                    
                    // Set a random user as the author of the comment
                    $userReference = $this->getReference('user-' . mt_rand(1, 10), User::class);
                    
                    $comment->setAuthor($userReference)
                             ->setContent($commentContent)
                             ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-'.$days.' days')))
                             ->setArticle($article);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
    
    /**
     * Returns a realistic literary comment for article fixtures
     * 
     * @return string A thoughtful literary comment
     */
    private function getRealisticComment(): string
    {
        $comments = [
            // General literary appreciation comments
            "Cette analyse est particulièrement éclairante sur les aspects sociologiques de l'œuvre. J'apprécie la façon dont vous avez mis en lumière les tensions sous-jacentes.",
            "Une interprétation rafraîchissante qui ouvre de nouvelles perspectives sur un texte que je croyais connaître. Merci pour cette relecture stimulante !",
            "Votre article capture parfaitement l'essence de l'œuvre tout en proposant un angle d'analyse original. J'ai beaucoup appris de votre lecture.",
            "Je suis particulièrement touché par votre analyse du développement des personnages, qui apporte une dimension humaine souvent négligée par la critique.",
            "Un point de vue audacieux qui bouscule les interprétations canoniques ! C'est précisément ce type de lecture dont la littérature a besoin pour rester vivante.",
            
            // Comments for classical literature
            "La résonance entre les thèmes abordés par l'auteur et notre société contemporaine est frappante. Certains classiques ne vieillissent décidément pas.",
            "J'ai toujours été fasciné par la modernité de cette œuvre malgré son ancrage historique. Votre article articule parfaitement cette tension temporelle.",
            "La richesse symbolique de ce texte continue de m'émerveiller à chaque relecture. Votre décryptage des motifs récurrents est particulièrement pertinent.",
            "Cette œuvre, bien que canonique, me semble souvent mal comprise. Merci d'avoir souligné sa dimension subversive pour l'époque.",
            "Le style de l'auteur, à la fois classique dans sa structure et révolutionnaire dans son propos, mérite effectivement l'analyse détaillée que vous proposez.",
            
            // Comments for sci-fi & fantasy
            "La dimension prophétique de cette œuvre de science-fiction est troublante. L'auteur avait anticipé des évolutions technologiques et sociales avec une précision remarquable.",
            "Au-delà de son univers imaginaire foisonnant, ce qui me touche dans cette œuvre est la profondeur psychologique des personnages, aspect que votre analyse met brillamment en valeur.",
            "La construction d'un monde secondaire aussi cohérent et détaillé est un exploit littéraire en soi. Votre article rend justice à ce travail monumental de l'auteur.",
            "Les questions philosophiques soulevées par cette dystopie restent d'une actualité brûlante. Merci pour cette analyse qui dépasse le simple commentaire littéraire.",
            "J'apprécie particulièrement votre lecture politique de cette œuvre de fantasy, genre trop souvent réduit à son aspect divertissant au détriment de sa dimension critique.",
            
            // Comments for contemporary literature
            "Cette œuvre contemporaine dialogue subtilement avec la tradition littéraire tout en proposant une voix singulière. Votre article saisit parfaitement cette dialectique.",
            "La fragmentation narrative, caractéristique de notre époque, trouve dans ce roman une expression particulièrement réussie. Merci pour cette analyse fine des procédés d'écriture.",
            "Ce qui me frappe dans cette œuvre, c'est la façon dont elle parvient à renouveler des thèmes universels à travers un prisme résolument contemporain.",
            "La dimension autobiographique, même voilée, apporte une authenticité touchante à ce texte. Votre article explore avec justesse ce jeu entre fiction et réalité.",
            "L'hybridation des genres caractéristique de la littérature contemporaine atteint ici un équilibre rare que votre analyse met parfaitement en lumière.",
            
            // Deep critical analysis comments
            "Votre lecture intertextuelle ouvre des perspectives fascinantes. Les échos avec d'autres œuvres que vous mentionnez enrichissent considérablement l'interprétation.",
            "La dimension politique de cette œuvre, souvent reléguée au second plan, mérite effectivement l'attention particulière que vous lui accordez dans votre article.",
            "L'approche psychanalytique que vous proposez offre des clés de lecture stimulantes, notamment concernant la construction du désir chez les personnages principaux.",
            "Votre analyse de la structure narrative révèle une complexité que je n'avais pas pleinement saisie lors de ma lecture. Je vais revisiter l'œuvre avec ce nouvel éclairage.",
            "La mise en contexte historique que vous proposez est indispensable pour comprendre les enjeux de l'œuvre. Un travail d'érudition impressionnant qui n'alourdit jamais l'analyse."
        ];
        
        return $comments[array_rand($comments)];
    }
    
    /**
     * Get a collection of realistic articles for a specific category
     */
    private function getArticlesForCategory(string $categoryTitle): array
    {
        $articles = [];
        
        if ($categoryTitle === 'Littérature Classique') {
            $articles = [
                [
                    'title' => 'Les Misérables : Chef-d\'œuvre intemporel de Victor Hugo',
                    'content' => '<p>Dans ce roman monumental, Victor Hugo nous plonge dans le Paris du XIXe siècle à travers l\'histoire de Jean Valjean, ancien bagnard en quête de rédemption. À travers des personnages inoubliables comme Fantine, Cosette, Marius et l\'implacable inspecteur Javert, Hugo dresse un portrait saisissant de la misère sociale et des injustices de son époque.</p>
                    <p>"Tant qu\'il existera, par le fait des lois et des mœurs, une damnation sociale créant artificiellement, en pleine civilisation, des enfers, et compliquant d\'une fatalité humaine la destinée qui est divine ; tant que les trois problèmes du siècle, la dégradation de l\'homme par le prolétariat, la déchéance de la femme par la faim, l\'atrophie de l\'enfant par la nuit, ne seront pas résolus ; tant que, dans de certaines régions, l\'asphyxie sociale sera possible ; en d\'autres termes, et à un point de vue plus étendu encore, tant qu\'il y aura sur la terre ignorance et misère, des livres de la nature de celui-ci pourront ne pas être inutiles."</p>
                    <p>Cette œuvre majeure de la littérature française reste d\'une actualité troublante par ses réflexions sur la justice, la religion et la politique.</p>',
                    'genre' => 'Roman historique'
                ],
                [
                    'title' => 'L\'Odyssée d\'Homère : Le voyage éternel',
                    'content' => '<p>Composée au VIIIe siècle avant J.-C., l\'Odyssée raconte le retour d\'Ulysse vers son royaume d\'Ithaque après la guerre de Troie. Ce périple de dix années, parsemé d\'épreuves et de rencontres extraordinaires, constitue l\'un des récits fondateurs de la civilisation occidentale.</p>
                    <p>"Dis-moi, Muse, cet homme subtil qui erra si longtemps, après qu\'il eut renversé la citadelle sacrée de Troie. Il vit les cités de peuples nombreux et il connut leur esprit ; et dans son cœur, il endura de nombreuses douleurs, sur la mer, pour sa propre vie et le retour de ses compagnons."</p>
                    <p>L\'Odyssée n\'a jamais cessé d\'inspirer artistes, écrivains et cinéastes, et ses thèmes - l\'exil, le retour, la fidélité - résonnent encore profondément en nous aujourd\'hui.</p>',
                    'genre' => 'Épopée'
                ],
                [
                    'title' => 'Madame Bovary : Le chef-d\'œuvre de Flaubert',
                    'content' => '<p>Publié en 1857, ce roman de Gustave Flaubert dépeint la vie d\'Emma Bovary, épouse d\'un médecin de campagne, qui cherche à échapper à la médiocrité de son existence à travers des liaisons adultères et des dépenses extravagantes. Roman du désenchantement, Madame Bovary est aussi une critique acérée de la bourgeoisie provinciale.</p>
                    <p>"Elle se répétait : \'J\'ai un amant ! un amant !\' se délectant à cette idée comme à celle d\'une autre puberté qui lui serait survenue. Elle allait donc posséder enfin ces joies de l\'amour, cette fièvre du bonheur dont elle avait désespéré. Elle entrait dans quelque chose de merveilleux où tout serait passion, extase, délire."</p>
                    <p>Chef-d\'œuvre de réalisme psychologique, ce roman nous fascine encore par la précision de son style et la modernité de son héroïne.</p>',
                    'genre' => 'Roman réaliste'
                ],
            ];
        } elseif ($categoryTitle === 'Science-Fiction et Fantasy') {
            $articles = [
                [
                    'title' => 'Dune de Frank Herbert : L\'épopée écologique et politique',
                    'content' => '<p>Publié en 1965, Dune est considéré comme l\'un des plus grands chefs-d\'œuvre de la science-fiction. Frank Herbert y crée un univers complexe centré sur la planète désertique Arrakis, seule source de l\'Épice, substance la plus précieuse de l\'univers.</p>
                    <p>"La peur tue l\'esprit. La peur est la petite mort qui conduit à l\'oblitération totale. J\'affronterai ma peur. Je lui permettrai de passer sur moi, au travers de moi. Et lorsqu\'elle sera passée, je tournerai mon œil intérieur sur son chemin. Et là où elle sera passée, il n\'y aura plus rien. Rien que moi."</p>
                    <p>À travers le destin de Paul Atréides, Herbert tisse une réflexion profonde sur l\'écologie, le pouvoir, la religion et le destin de l\'humanité, qui résonne particulièrement avec nos préoccupations contemporaines.</p>',
                    'genre' => 'Science-Fiction'
                ],
                [
                    'title' => 'Le Seigneur des Anneaux : La quête épique de Tolkien',
                    'content' => '<p>Cette œuvre magistrale de J.R.R. Tolkien, publiée entre 1954 et 1955, a redéfini le genre de la fantasy. À travers la quête de Frodo Baggins pour détruire l\'Anneau unique, Tolkien a créé un monde d\'une richesse incomparable, avec ses langues, ses mythologies et ses civilisations.</p>
                    <p>"Même la plus petite personne peut changer le cours de l\'avenir." Cette phrase emblématique résume l\'esprit de cette saga où le courage des êtres les plus humbles s\'avère décisif face aux forces du mal.</p>
                    <p>Au-delà de l\'aventure, Le Seigneur des Anneaux explore des thèmes comme le pouvoir et la corruption, l\'amitié et le sacrifice, la mort et l\'immortalité, qui continuent de captiver des millions de lecteurs à travers le monde.</p>',
                    'genre' => 'Fantasy épique'
                ],
                [
                    'title' => 'Neuromancien : Le roman fondateur du cyberpunk',
                    'content' => '<p>Publié en 1984, ce roman visionnaire de William Gibson a inventé le terme "cyberespace" et posé les fondations du mouvement cyberpunk. Dans un futur dominé par les corporations et la technologie, Case, un hacker déchu, se voit offrir une dernière chance de retrouver ses capacités en échange d\'une mission périlleuse.</p>
                    <p>"Le ciel au-dessus du port avait la couleur d\'une télévision réglée sur un canal hors-service." Cette première phrase emblématique donne immédiatement le ton d\'un univers où la frontière entre le réel et le virtuel s\'estompe.</p>
                    <p>Avec son style nerveux et ses visions d\'un futur technologique dystopique, Neuromancien a influencé non seulement la littérature mais aussi le cinéma, les jeux vidéo et notre façon même de concevoir l\'internet et la réalité virtuelle.</p>',
                    'genre' => 'Cyberpunk'
                ],
            ];
        } elseif ($categoryTitle === 'Littérature Contemporaine') {
            $articles = [
                [
                    'title' => 'La Promesse de l\'aube : L\'hommage de Romain Gary à sa mère',
                    'content' => '<p>Dans ce récit autobiographique publié en 1960, Romain Gary retrace son enfance en Pologne puis à Nice, sous l\'égide d\'une mère aimante et exigeante qui place en lui tous ses espoirs. C\'est l\'histoire d\'un amour maternel absolu et parfois étouffant, mais aussi celle d\'une promesse tenue.</p>
                    <p>"- Tu seras un héros, tu seras général, Gabriele D\'Annunzio, Ambassadeur de France - tous ces voyous ne savent pas qui tu es ! Je promettais, je jurais. Ma vie ne m\'a pas laissé beaucoup de temps pour tenir mes promesses."</p>
                    <p>Ce livre bouleversant, drôle et poignant, est considéré comme l\'un des chefs-d\'œuvre de la littérature française du XXe siècle, explorant avec une sensibilité rare la relation mère-fils et le poids du destin.</p>',
                    'genre' => 'Autobiographie'
                ],
                [
                    'title' => 'L\'Élégance du hérisson : Une révélation philosophique',
                    'content' => '<p>Ce roman de Muriel Barbery, publié en 2006, nous présente Renée, concierge cultivée qui cache sa passion pour la littérature et la philosophie, et Paloma, jeune fille surdouée qui planifie son suicide. Leur rencontre avec Kakuro Ozu va bouleverser leurs existences et les amener à révéler leur véritable nature.</p>
                    <p>"Le thé comprend tout ce qu\'il faut savoir : la vertu d\'ouverture, de partage et de politesse. Et par le rituel à quoi il oblige, il donne un sens à la parenthèse temporelle qu\'il occupe. Car le plus important n\'est pas de boire mais de s\'arrêter pour le faire."</p>
                    <p>Mêlant humour et profondeur philosophique, ce roman explore des thèmes comme les préjugés sociaux, la beauté cachée des choses et la quête de sens dans l\'existence. Son succès international témoigne de sa capacité à toucher les lecteurs par sa délicatesse et son intelligence.</p>',
                    'genre' => 'Roman philosophique'
                ],
                [
                    'title' => 'Trois jours et une vie : Le drame psychologique de Pierre Lemaitre',
                    'content' => '<p>Dans ce roman publié en 2016, Pierre Lemaitre nous plonge dans la petite ville de Beauval, où Antoine, 12 ans, tue accidentellement un enfant du voisinage. Le roman suit les conséquences de cet acte sur trois périodes : décembre 1999, 2011 et 2015.</p>
                    <p>"À la fin de l\'après-midi, la pluie se mit à tomber plus dru et Antoine rentra. Sur le chemin, l\'enfant comprit que rien ne serait plus jamais comme avant, que, d\'une certaine façon, sa vie venait de prendre fin."</p>
                    <p>À travers une narration maîtrisée qui tient le lecteur en haleine, Lemaitre explore les thèmes de la culpabilité, du hasard et du destin, créant un portrait saisissant des ravages psychologiques causés par un acte irréparable commis dans l\'enfance.</p>',
                    'genre' => 'Roman noir'
                ],
            ];
        }
        
        return $articles;
    }
    
    /**
     * Returns a real image URL based on the category and article title
     * 
     * @param string $categoryTitle The category title
     * @param string $articleTitle The article title
     * @return string A real image URL that matches the theme
     */
    /**
     * Returns a real image URL based on the category and article title
     * 
     * @param string $categoryTitle The category title
     * @param string $articleTitle The article title
     * @return string A real image URL that matches the theme
     */
    private function getImageUrlForArticle(string $categoryTitle, string $articleTitle): string
    {
        // Collection d'URLs d'images par catégorie avec des paramètres optimisés (w=800&q=80)
        $imageUrls = [
            'Littérature Classique' => [
                'Les Misérables : Chef-d\'œuvre intemporel de Victor Hugo' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=800&q=80',
                'L\'Odyssée d\'Homère : Le voyage éternel' => 'https://images.unsplash.com/photo-1558901357-ca41e027e43a?w=800&q=80',
                'Madame Bovary : Le chef-d\'œuvre de Flaubert' => 'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=800&q=80',
                // Images par défaut pour cette catégorie
                'default' => [
                    'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=800&q=80',
                    'https://images.unsplash.com/photo-1558901357-ca41e027e43a?w=800&q=80',
                    'https://images.unsplash.com/photo-1535905557558-afc4877a26fc?w=800&q=80'
                ]
            ],
            'Science-Fiction et Fantasy' => [
                'Dune de Frank Herbert : L\'épopée écologique et politique' => 'https://images.unsplash.com/photo-1486746290722-483e8f1e44d2?w=800&q=80',
                'Le Seigneur des Anneaux : La quête épique de Tolkien' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80',
                'Neuromancien : Le roman fondateur du cyberpunk' => 'https://images.unsplash.com/photo-1551269901-5c5e14c25df7?w=800&q=80',
                // Images par défaut pour cette catégorie
                'default' => [
                    'https://images.unsplash.com/photo-1486746290722-483e8f1e44d2?w=800&q=80',
                    'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80',
                    'https://images.unsplash.com/photo-1551269901-5c5e14c25df7?w=800&q=80'
                ]
            ],
            'Littérature Contemporaine' => [
                'La Promesse de l\'aube : L\'hommage de Romain Gary à sa mère' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=80',
                'L\'Élégance du hérisson : Une révélation philosophique' => 'https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=800&q=80',
                'Trois jours et une vie : Le drame psychologique de Pierre Lemaitre' => 'https://images.unsplash.com/photo-1495640388908-05fb178b2a2e?w=800&q=80',
                // Images par défaut pour cette catégorie
                'default' => [
                    'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=80',
                    'https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=800&q=80',
                    'https://images.unsplash.com/photo-1495640388908-05fb178b2a2e?w=800&q=80'
                ]
            ],
            // Images par défaut au cas où la catégorie n'existe pas
            'default' => [
                'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=800&q=80',
                'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&q=80',
                'https://images.unsplash.com/photo-1486746290722-483e8f1e44d2?w=800&q=80'
            ]
        ];
        if (isset($imageUrls[$categoryTitle]['default'])) {
            $defaultImages = $imageUrls[$categoryTitle]['default'];
            return $defaultImages[array_rand($defaultImages)];
        }
        
        // Si la catégorie n'existe pas, utiliser une image générale par défaut
        $defaultImages = $imageUrls['default'];
        return $defaultImages[array_rand($defaultImages)];
    }
}
