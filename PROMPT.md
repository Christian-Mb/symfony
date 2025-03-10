# Symfony Migration Guide

This document provides guidance for making changes to the database schema and entity relationships in this Symfony project. Following these steps will help ensure smooth migrations and maintain data integrity.

## Table of Contents

1. [Entity Updates](#entity-updates)
2. [Creating Migrations](#creating-migrations)
3. [Handling Fixtures](#handling-fixtures)
4. [Common Migration Issues](#common-migration-issues)
5. [Documentation](#documentation)

## Entity Updates

When updating entity structures, follow these guidelines:

### Adding New Properties

1. Add the property to the entity class with appropriate type and visibility
2. Add ORM mapping attributes/annotations
3. Generate getter and setter methods
4. Update relevant form types if needed

Example:
```php
// Add to User entity
#[ORM\Column(length: 255, nullable: true)]
private ?string $phoneNumber = null;

public function getPhoneNumber(): ?string
{
    return $this->phoneNumber;
}

public function setPhoneNumber(?string $phoneNumber): self
{
    $this->phoneNumber = $phoneNumber;
    return $this;
}
```

### Changing Existing Properties

Be careful when changing property types, especially if there is existing data. When changing from:
- `string` to `integer`: Ensure all existing values can be converted
- `nullable` to `non-nullable`: Provide default values for existing records
- `single value` to `collection`: Create appropriate migration steps

### Entity Relationships

When modifying entity relationships:

1. Define the relationship on both sides (for bidirectional relationships)
2. Use appropriate cascade options 
3. Consider orphanRemoval for parent-child relationships
4. Create a migration that preserves existing data

Example for changing from a string property to an entity relation:
```php
// Old
#[ORM\Column(length: 255)]
private string $author;

// New
#[ORM\ManyToOne(targetEntity: User::class)]
#[ORM\JoinColumn(nullable: false)]
private User $author;
```

### Entity String Representation

When entities are used in templates, form choices, logging, or string concatenation, PHP will try to convert objects to strings. Without a proper string representation, this results in the error:
```
Object of class [Entity Class] could not be converted to string
```

To prevent this, always implement a `__toString()` method in your entities, especially those used in relationships:

```php
// In User entity
public function __toString(): string
{
    return $this->username ?? $this->email ?? 'User #'.$this->id;
}
```

Benefits of implementing `__toString()`:
- Prevents errors when entities are rendered in templates or form select fields
- Works with Doctrine proxy objects (critical for lazy-loaded entities)
- Simplifies debugging by providing meaningful object representation
- Allows entities to be used directly in string contexts

Remember to return a non-empty string, and to handle cases where properties might be null.

## Creating Migrations

### Generate a Migration

After updating entity classes, generate a migration:

```bash
php bin/console make:migration
```

### Review the Migration

Always review the generated migration file before applying it:
- Check that it correctly captures your entity changes
- Ensure it handles data conversion appropriately
- Add data transformation logic if needed

### Custom Migration Logic

For complex changes (like converting a string column to a relationship):

1. Make the generated migration file nullable initially
2. Add data transformation logic
3. Then make it non-nullable if required

Example:
```php
// For converting a string author to a User entity relation
public function up(Schema $schema): void
{
    // First add a nullable column
    $this->addSql('ALTER TABLE article ADD author_id INT DEFAULT NULL');
    
    // Create a temporary index/foreign key
    $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    
    // Data migration - match by username or create default
    $this->addSql('UPDATE article a SET a.author_id = (SELECT u.id FROM users u WHERE u.username = a.author LIMIT 1)');
    
    // Set default for any missing values
    $this->addSql('UPDATE article a SET a.author_id = 1 WHERE a.author_id IS NULL');
    
    // Make the column non-nullable after data is migrated
    $this->addSql('ALTER TABLE article MODIFY author_id INT NOT NULL');
    
    // Drop the old column
    $this->addSql('ALTER TABLE article DROP author');
}
```

### Apply the Migration

Apply the migration to update the database schema:

```bash
php bin/console doctrine:migrations:migrate
```

## Handling Fixtures

When entity structures change, fixtures must be updated to match:

### Update Fixture Classes

1. Modify fixture classes to match new entity structure
2. Use the `DependentFixtureInterface` to manage dependencies between fixtures
3. For relationships, use references to link entities

Example:
```php
// UserFixtures
class UserFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        // ...
        $manager->persist($user);
        $manager->flush();
        
        $this->addReference('user_admin', $user, User::class);
    }
}

// ArticleFixtures with dependency
class ArticleFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $article = new Article();
        $article->setTitle('Example Title');
        // Use reference for relationship
        $article->setAuthor($this->getReference('user_admin', User::class));
        // ...
        $manager->persist($article);
    }
    
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
```

### Load Updated Fixtures

After updating fixtures, load them with:

```bash
php bin/console doctrine:fixtures:load
```

Add `--append` flag to keep existing data (use with caution).

## Common Migration Issues

### Tables Already Exist

If you see "table already exists" errors:

```bash
# Mark all migrations as executed without running them
php bin/console doctrine:migrations:version --add --all --no-interaction

# Generate a new migration for just your changes
php bin/console make:migration

# Apply only the new migration
php bin/console doctrine:migrations:migrate
```

### Foreign Key Constraints

When deleting or modifying columns with foreign key constraints:

1. Drop the constraint first
2. Make the changes
3. Re-add the constraint (possibly modified)

### Type Mismatches

For type conversion issues:

1. Add a temporary column with the new type
2. Copy and convert data from old column to new
3. Drop the old column
4. Rename the new column to the original name

## Documentation

### Update CHANGELOG.md

Always document changes in CHANGELOG.md under the appropriate section:

```markdown
## [Unreleased]

### Added
- Added phone number field to User entity
- Added relationship between Article and Category entities

### Changed
- Changed Article author from string to User entity relationship

### Fixed
- Fixed data integrity issues in Comment entity
```

### Database Schema Documentation

Consider keeping an updated ERD (Entity Relationship Diagram) in your project documentation. Tools like [Doctrine ERD Generator](https://github.com/doctrine/doctrine2-orm-graphviz) or export features from database tools can help generate these.

### Comment Your Migrations

Add detailed comments to complex migrations explaining the transformation logic, especially for data migrations.

