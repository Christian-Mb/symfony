# Changelog

Toutes les modifications notables apportées à ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Non publié]

### Ajouté
- Photos de profil pour les utilisateurs
  - Ajout du champ `profilePicture` à l'entité `User`
  - Affichage des photos de profil dans les commentaires des articles
  - Affichage des photos de profil dans l'interface utilisateur

- Utilisateur de test avec mot de passe prédéfini
  - Email: test@example.com
  - Nom d'utilisateur: test
  - Mot de passe: "password"
  - Rôle: ROLE_USER

### Modifié
- Optimisation des images d'articles
  - Remplacement des liens générés par Faker par des URLs d'images réelles et fiables
  - Utilisation d'images d'Unsplash avec paramètres d'optimisation (redimensionnement et qualité)
  - Images thématiques selon la catégorie de l'article (Littérature Classique, Science-Fiction et Fantasy, Littérature Contemporaine)

# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Added UserFixtures to generate fake users with proper credentials for testing purposes
- Comprehensive README.md file with detailed project documentation, installation instructions, and usage guidelines

### Changed
- Optimized User entity to better follow SOLID principles with proper type-hinting and return types
- Improved SecurityController with better password handling and error management
- Enhanced RegistrationType form with improved validation and user experience

### Security
- Strengthened User entity with improved password handling mechanisms
- Added additional security measures in SecurityController for authentication
|- Optimized form submission validation in RegistrationType

### Fixed
- Fixed registration form template issue where form fields were incorrectly referenced, causing "Neither the property 'password' nor one of the methods 'password()' exist" error
- Fixed registration issue by setting plainPassword field as non-mapped in RegistrationType
- Modified Article entity to change author property from string to ManyToOne relationship with User entity
- Created database migration to convert article author column to foreign key relationship
- Fixed ArticleFixtures to use User entity references as authors instead of string values
- Added __toString() method to User entity to fix "Object of class Proxies\__CG__\App\Entity\User could not be converted to string" error

## [1.0.0] - 2024-08-19

### Added
- Node.js version management using NVM
- pnpm as the primary package manager
- `.npmrc` configuration with engine-strict mode and Node.js v22.14.0
- `pnpm-workspace.yaml` for better frontend asset organization
- Comprehensive CHANGELOG.md file to track project changes

### Changed
- Upgraded from Symfony 6.4 to Symfony 7.2
- Updated PHP version requirement from ^8.0 to ^8.2
- Converted all Doctrine annotations to PHP 8 attributes
- Updated entity classes to implement new interfaces required by Symfony 7.2
- Modified routing configuration to use attributes instead of annotations
- Updated form types with proper return type declarations
- Updated security configuration to use newer syntax (password_hashers, lazy authentication)
- Replaced deprecated Doctrine configuration with new format
- Modernized Kernel.php to use RoutingConfigurator instead of RouteCollectionBuilder
- Enhanced .gitignore file with more comprehensive exclusions

### Removed
- Removed yarn.lock and Yarn-related configuration
- Removed sensio/framework-extra-bundle (abandoned package)
- Removed support for deprecated anonymous authentication
- Removed deprecated route annotation configuration

### Fixed
- Updated User entity to implement required Symfony 7.2 security interfaces
- Fixed repository class references in entity attributes
- Updated error controller routing configuration

### Security
- Updated .gitignore to exclude security-sensitive files
- Implemented more secure password hashing configuration
- Updated error handling configuration for better security

