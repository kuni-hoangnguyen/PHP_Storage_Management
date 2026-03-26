# QLKhoDH - Pure PHP Structure

## Folder Structure

- `public/`: Web root (index.php, assets, .htaccess)
- `app/Core/`: Core classes (App, Controller)
- `app/Controllers/`: Controllers
- `app/Models/`: Models
- `app/Views/`: Views and layouts
- `routes/`: Route definitions
- `config/`: App and database configs
- `bootstrap/`: App bootstrapping and autoload
- `storage/`: Logs and writable runtime files

## Run Locally

1. Point Apache virtual host document root to `public/`.
2. Or put project in `htdocs` and open:
   - `http://localhost/QLKhoDH/public`
3. Ensure Apache `mod_rewrite` is enabled.

## Add New Route

Edit `routes/web.php`:

```php
'GET' => [
    '/products' => ['ProductController', 'index'],
],
```

Create corresponding controller in `app/Controllers`.
