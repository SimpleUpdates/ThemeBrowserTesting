# ThemeBrowserTesting

## Installation

```bash
composer install
chmod +x run.sh
```

## Usage

### Build diff summary

```bash
docker-compose build
./run.sh /path/to/theme
```

### Run style guide

```bash
php -S localhost:3333
```

## Etc

Run the app and jump into terminal:

```bash
docker-compose run --entrypoint bash app
```

Wish list:

- Backstop.js
- Automatically generated and deployed style guides
- Accessibility testing using a headless browser