# Theme build instructions (summary)

This repository ships the **Free Backlinks Generator** theme (`free-backlinks-generator/`). The authoritative product specification is the master “Theme Build Instructions” document you used to generate this project; this file is a short operational summary.

## What ships in 1.0.0

- Templates and page templates listed in the project file structure.
- `inc/` modules: CPT, roles, AJAX, security helpers, SEO hooks, shared helpers.
- Assets: `main.css`, `auth.css`, `dashboard.css`, `blog.css`, `critical.css`, matching JS, SVGs, `screenshot.png`, `assets/images/og-default.png`.
- `demo-content.xml`: one sample published `fbg_post` for import testing.

## After install

1. Activate the theme; confirm pages exist and the front page is **Home**.
2. Create a menu if you want custom footer/header links beyond defaults.
3. Import `demo-content.xml` via **Tools → Import** if you want a sample community post.
4. Complete the functional and security checklist from the master specification before production.

## Coding conventions

- Text domain: `free-backlinks-generator`.
- AJAX actions: `wp_ajax_` / `wp_ajax_nopriv_` handlers in `inc/ajax-handlers.php`.
- Guest posts: post type `fbg_post`; archive slug `community`.

For full UI copy, email templates, and QA matrices, refer to the original v1.0.0 build specification document.
