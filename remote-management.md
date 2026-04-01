# Remote management

## Deployment

1. Copy the `free-backlinks-generator` folder to `wp-content/themes/` on the server (or deploy via CI).
2. In **Appearance → Themes**, activate **Free Backlinks Generator**.
3. On activation, the theme creates core pages (Home, Register, Login, Dashboard, etc.) and flushes rewrite rules.
4. Visit **Settings → Permalinks** and click **Save** once if `/community/` or custom URLs 404.

## Updates

- Bump `Version` in `style.css` and the `FBG_VERSION` constant in `functions.php` together.
- Clear server and CDN caches after CSS/JS changes.
- Run database backups before major WordPress or PHP upgrades.

## Configuration checklist

- Set **Settings → Reading** to a static front page if the Home template is not already assigned (activation sets `home` when created).
- Configure **SMTP** or a transactional email provider so registration, reset, and moderation emails deliver reliably.
- Harden **wp-admin** (2FA, limited login attempts at the server or plugin level) in addition to theme login throttling.

## Headless / API

- REST user enumeration is restricted for users who cannot `list_users`; adjust `inc/security.php` if you rely on public user endpoints.
