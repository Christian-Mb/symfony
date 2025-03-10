# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

