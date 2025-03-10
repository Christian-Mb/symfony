# Guide de Migration et Maintenance Symfony

Ce document fournit un guide complet pour la migration vers de nouvelles versions de Symfony, la mise à jour des entités, la gestion des relations, et les bonnes pratiques de maintenance. Il est conçu pour être applicable à n'importe quel projet Symfony et inclut des solutions aux problèmes les plus fréquemment rencontrés.

## Table des matières

1. [Migration vers une nouvelle version](#migration-vers-une-nouvelle-version)
   - [Préparation](#préparation)
   - [Mise à jour des dépendances](#mise-à-jour-des-dépendances)
   - [Corrections de compatibilité](#corrections-de-compatibilité)
   - [Mise à jour de la configuration](#mise-à-jour-de-la-configuration)

2. [Mise à jour des entités et relations](#mise-à-jour-des-entités-et-relations)
   - [Modification des entités](#modification-des-entités)
   - [Gestion des relations entre entités](#gestion-des-relations-entre-entités)
   - [Création et application des migrations](#création-et-application-des-migrations)
   - [Mise à jour des fixtures](#mise-à-jour-des-fixtures)

3. [Authentification et sécurité](#authentification-et-sécurité)
   - [Authentificateur personnalisé](#authentificateur-personnalisé)
   - [Auto-login après inscription](#auto-login-après-inscription)
   - [Formulaires de connexion et inscription](#formulaires-de-connexion-et-inscription)

4. [Tests et validation](#tests-et-validation)
   - [Tests unitaires](#tests-unitaires)
   - [Tests fonctionnels](#tests-fonctionnels)
   - [Tests d'intégration](#tests-dintégration)

5. [Documentation et maintenance](#documentation-et-maintenance)
   - [Mise à jour du CHANGELOG](#mise-à-jour-du-changelog)
   - [Documentation du schéma](#documentation-du-schéma)
   - [Commentaires du code](#commentaires-du-code)

6. [Résolution des problèmes courants](#résolution-des-problèmes-courants)
   - [Problèmes de migration](#problèmes-de-migration)
   - [Problèmes d'entités et relations](#problèmes-dentités-et-relations)
   - [Problèmes de formulaires](#problèmes-de-formulaires)
   - [Problèmes d'authentification](#problèmes-dauthentification)
   - [Problèmes avec les DataFixtures](#problèmes-avec-les-datafixtures)

---

## Migration vers une nouvelle version

### Préparation

Avant de commencer la migration vers une nouvelle version de Symfony, vérifiez les prérequis et la compatibilité :

```bash
# Vérifier la version de PHP (ex: PHP 8.2+ pour Symfony 7.2)
php -v

# Vérifier la version de Composer
composer -V

# Vérifier les dépendances actuelles
composer show symfony/symfony
```

Consultez la documentation officielle pour connaître les exigences spécifiques de la version cible : https://symfony.com/doc/current/setup.html

### Mise à jour des dépendances

Mettez à jour votre fichier `composer.json` pour cibler la nouvelle version de Symfony :

```bash
# Exemple pour Symfony 7.2
composer require symfony/symfony:^7.2 --no-update

# Mettre à jour les dépendances connexes
composer require symfony/webpack-encore-bundle:^2.0 --no-update
composer require symfony/debug-bundle:^7.2 --no-update
composer require symfony/maker-bundle:^1.50 --no-update
composer require symfony/monolog-bundle:^3.8 --no-update
composer require doctrine/doctrine-bundle:^2.10 --no-update
composer require doctrine/orm:^2.16 --no-update
composer require twig/extra-bundle:^3.7 --no-update

# Installer les outils de test si nécessaire
composer require --dev symfony/test-pack symfony/browser-kit symfony/css-selector --no-update

# Effectuer la mise à jour
composer update --with-all-dependencies

# Nettoyer le cache
php bin/console cache:clear
```

### Corrections de compatibilité

#### Namespaces Doctrine

Les namespaces Doctrine ont changé dans les versions récentes, mettez à jour vos repositories :

```bash
# Remplacer les imports obsolètes dans tous les fichiers Repository
find src/Repository -name "*.php" -exec sed -i 's/Doctrine\\Common\\Persistence\\ManagerRegistry/Doctrine\\Persistence\\ManagerRegistry/g' {} \;
```

#### Correction des templates Twig

Vérifiez et corrigez les noms de routes dans vos templates :

```bash
# Exemple : corriger une route renommée
find templates -name "*.twig" -exec sed -i 's/path('\'ancien_nom\'')/path('\'nouveau_nom\'')/g' {} \;
```

#### Correction des DataFixtures

Adaptez les fixtures pour les changements de syntaxe ou d'API :

```php
// Exemple : fonction join() (ordre des paramètres)
// Avant
$content = '<p>'.join($faker->paragraphs(6), '<p></p>').'</p>';
// Après
$content = '<p>'.join('<p></p>', $faker->paragraphs(6)).'</p>';

// Exemple : conversion DateTime en DateTimeImmutable
// Avant
->setCreatedAt($faker->dateTimeBetween('-30 days'))
// Après
->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 days')))
```

### Mise à jour de la configuration

Mettez à jour vos fichiers de configuration pour les adapter à la nouvelle version :

#### Security.yaml

```yaml
# Exemple pour Symfony 7.x
security:
    password_hashers:
        App\Entity\User: 'auto'
    
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
    
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            custom_authenticator: App\Security\LoginFormAuthenticator
            logout:
                path: security_logout
                target: home
                
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/profile, roles: ROLE_USER }
```

## Mise à jour des entités et relations

### Modification des entités

Suivez ces conseils pour modifier vos entités en toute sécurité :

#### Ajout de nouvelles propriétés

```php
// Exemple d'ajout d'une propriété
#[ORM\Column(length: 255, nullable: true)]
private ?string $newProperty = null;

public function getNewProperty(): ?string
{
    return $this->newProperty;
}

public function setNewProperty(?string $newProperty): self
{
    $this->newProperty = $newProperty;
    return $this;
}
```

#### Modification de propriétés existantes

Soyez prudent lors du changement de type d'une propriété :
- `string` vers `integer` : assurez-vous que toutes les valeurs peuvent être converties
- `nullable` vers `non-nullable` : fournissez des valeurs par défaut
- `single value` vers `collection` : créez des étapes de migration appropriées

### Gestion des relations entre entités

Lorsque vous modifiez les relations entre entités :

#### Conversion d'une propriété simple en relation

```php
// Avant
#[ORM\Column(length: 255)]
private string $author;

// Après
#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: false)]
private User $author;
```

#### Configuration des relations

```php
// Relation OneToMany
#[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class, cascade: ['persist', 'remove'])]
private Collection $articles;

// Relation ManyToOne
#[ORM\ManyToOne(inversedBy: 'articles', targetEntity: User::class)]
#[ORM\JoinColumn(nullable: false)]
private User $author;

// Relation ManyToMany
#[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'articles')]
private Collection $tags;
```

### Création et application des migrations

#### Générer une migration

```bash
php bin/console make:migration
```

#### Réviser la migration générée

Vérifiez toujours la migration générée avant de l'appliquer :

```php
// Exemple de migration complexe pour convertir une chaîne en relation
public function up(Schema $schema): void
{
    // 1. Ajouter une colonne nullable temporairement
    $this->addSql('ALTER TABLE article ADD author_id INT DEFAULT NULL');
    
    // 2. Créer la contrainte de clé étrangère
    $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    
    // 3. Migration des données
    $this->addSql('UPDATE article a SET a.author_id = (SELECT u.id FROM users u WHERE u.username = a.author LIMIT 1)');
    
    // 4. Valeurs par défaut si nécessaire
    $this->addSql('UPDATE article a SET a.author_id = 1 WHERE a.author_id IS NULL');
    
    // 5. Rendre la colonne non-nullable
    $this->addSql('ALTER TABLE article MODIFY author_id INT NOT NULL');
    
    // 6. Supprimer l'ancienne colonne
    $this->addSql('ALTER TABLE article DROP author');
}
```

#### Appliquer la migration

```bash
php bin/console doctrine:migrations:migrate
```

### Mise à jour des fixtures

Adaptez vos fixtures pour refléter les changements d'entités :

#### Utilisez DependentFixtureInterface pour gérer les dépendances

```php
// UserFixtures.php
class UserFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@example.com');
        // Hashage du mot de passe...
        
        $manager->persist($user);
        $manager->flush();
        
        // Pour Doctrine Fixtures 2.x
        $this->addReference('user_admin', $user, User::class);
        // Pour versions antérieures
        // $this->addReference('user_admin', $user);
    }
}

// ArticleFixtures.php avec dépendance
class ArticleFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $article = new Article();
        $article->setTitle('Exemple d\'article');
        
        // Pour Doctrine Fixtures 2.x
        $article->setAuthor($this->getReference('user_admin', User::class));
        // Pour versions antérieures
        // $article->setAuthor($this->getReference('user_admin'));
        
        $manager->persist($article);
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
```

#### Charger les fixtures

```bash
php bin/console doctrine:fixtures:load

# Pour conserver les données existantes (avec précaution)
php bin/console doctrine:fixtures:load --append
```

## Authentification et sécurité

### Authentificateur personnalisé

Créez un authentificateur personnalisé pour gérer la connexion :

```php
// src/Security/LoginFormAuthenticator.php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security_login';

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                $user = $this->entityManager
                    ->getRepository(User::class)
                    ->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new AuthenticationException('Email could not be found.');
                }

                return $user;
            }),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $

