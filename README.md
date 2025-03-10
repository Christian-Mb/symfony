# Symfony Blog Platform

A feature-rich blog platform built with Symfony 7.2, allowing users to create accounts, write articles, categorize content, and engage through comments.

## 📋 Features

- **User Management**: Registration, authentication, and profile management
- **Article Management**: Create, read, update, and delete blog articles
- **Categorization**: Organize articles by categories
- **Comments**: Allow readers to comment on articles
- **Image Uploads**: Support for image uploads in articles
- **Responsive Design**: Mobile-friendly interface

## 🔧 Technologies

- **PHP 8.2+**
- **Symfony 7.2**: PHP web application framework
- **Doctrine ORM**: Object-relational mapping for database interactions
- **Twig**: Template engine for view rendering
- **pnpm**: Package manager for frontend dependencies
- **React**: Frontend JavaScript library
- **Bootstrap**: Frontend CSS framework

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL
- Node.js (v22+ recommended)
- pnpm package manager

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/symfony-blog.git
   cd symfony-blog
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Setup environment variables**
   ```bash
   # Copy the .env file and adjust settings for your environment
   cp .env .env.local
   
   # Configure your DATABASE_URL in .env.local:
   # Example: DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/symfony_blog?serverVersion=8.0"
   ```

4. **Create the database and schema**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:create
   ```

5. **Install frontend dependencies**
   ```bash
   pnpm install
   ```

6. **Build frontend assets**
   ```bash
   pnpm run build
   ```

7. **Load fixtures (optional)**
   ```bash
   php bin/console doctrine:fixtures:load
   ```

8. **Run the application**
   ```bash
   symfony server:start --no-tls
   ```

9. **Visit the application**
   Open your browser and navigate to `http://localhost:8000`

## 🎮 Usage

### User Registration

1. Visit the registration page at `/register`
2. Create an account with a username, email, and password
3. Login with your credentials

### Creating Articles

1. Log in to your account
2. Navigate to the "New Article" page
3. Fill in the article details:
   - Title
   - Content
   - Category (select existing or create new)
   - Featured image (optional)
4. Submit the article

### Managing Categories

1. Log in with admin privileges
2. Navigate to the Categories section
3. Create, edit, or delete categories

### Commenting

1. Navigate to an article page
2. Scroll to the comments section at the bottom
3. If logged in, you can leave a comment
4. You can also reply to existing comments

## 🛠️ Development

### Running Tests

```bash
php bin/phpunit
```

### Code Quality Tools

```bash
# Run PHP CS Fixer
php vendor/bin/php-cs-fixer fix src

# Run PHPStan
php vendor/bin/phpstan analyse src
```

### Conventions

- Follow PSR-12 coding standard
- Use type hints for function parameters and return types
- Document your code with PHPDoc annotations
- Follow SOLID principles

## 🤝 Contributing

1. Fork the repository
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests to ensure everything works
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

Please make sure to update tests as appropriate and adhere to the existing coding standards.

## 📜 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgements

- [Symfony](https://symfony.com/)
- [Doctrine](https://www.doctrine-project.org/)
- [Bootstrap](https://getbootstrap.com/)
- [React](https://reactjs.org/)

