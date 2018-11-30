# ThemeBrowserTesting

Installation:

Installation:

```bash
composer install
chmod +x run.sh
```

Usage:

```bash
./run.sh /path/to/theme
```

Rebuild the app:

```bash
docker-compose build
```

Run the app and jump into terminal:

```bash
docker-compose run --entrypoint bash app
```

Updating `circle-github-bot`:

- Download release and extract into `third-party`
- Run `npm run-script build`
- Remove `dist` from `circle-github-bot`'s `.gitignore` file.
- Update `comment.js` to require the `circle-github-bot` folder, if its name has changed.

Wish list:

- Backstop.js
- Automatically generated and deployed style guides
- Accessibility testing using a headless browser