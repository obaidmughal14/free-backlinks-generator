# SEO notes — Free Backlinks Generator

- **Title tags:** Provided by WordPress `title-tag` theme support; customize per page in the editor or with an SEO plugin.
- **Meta descriptions:** Use the excerpt / meta description field on guest posts; static pages should include an introduction in content or plugin fields.
- **Open Graph:** `inc/seo.php` outputs default OG tags when no SEO plugin is active. Default share image: `assets/images/og-default.png` (1200×630).
- **Schema:** `WebSite` JSON-LD on the front page; `Article` JSON-LD on single `fbg_post` views.
- **Robots:** `robots.txt` filter disallows `/dashboard/`, `/login/`, `/register/`, `/forgot-password/`, and `/submit-post/`.
- **Private templates:** Auth and dashboard page templates emit `noindex, nofollow` in `wp_head`.
- **Sitemaps:** WordPress core sitemaps include public post types; keep pending `fbg_post` out of the index (they are not publicly viewable).
- **Backlinks:** Filter `fbg_backlink_rel` adjusts `rel` on single-post backlink URLs (default `nofollow ugc`).

After launch, run a crawl (e.g. Screaming Frog) and validate structured data at [schema.org validator](https://validator.schema.org/).
