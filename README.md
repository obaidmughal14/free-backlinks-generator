# Free Backlinks Generator

WordPress theme for a **community guest-post / backlink exchange**: members submit posts with contextual links, browse others’ content, and use a member dashboard. Includes **affiliate attribution**, **read-to-unlock guest-post slots**, and **responsive** front-end CSS.

**Current version:** `1.1.1` (see `style.css` header and `FBG_VERSION` in `functions.php`)

**Repository:** [github.com/obaidmughal14/free-backlinks-generator](https://github.com/obaidmughal14/free-backlinks-generator)

## Requirements

- WordPress **6.0+**
- PHP **8.0+**

## Install

1. Clone or copy this repo into `wp-content/themes/free-backlinks-generator` (folder name must match if you rely on default paths).
2. Activate the theme under **Appearance → Themes**.
3. The theme creates required pages on activation (home, auth, dashboard, submit, legal/affiliate pages, etc.).

## Key URLs (typical slugs)

| Area | Path (default) |
|------|----------------|
| Community post archive | `/community/` |
| Member dashboard | `/dashboard/` |
| Submit guest post | `/submit-post/` |
| Register / login | `/register/`, `/login/` |
| Affiliate program (public) | `/affiliate-program/` |

Exact slugs depend on your **Settings → Permalinks** and created pages.

## Features (high level)

- **Custom post type** `fbg_post` with niches, content types, backlinks meta, moderation (pending / approved / rejected), view counts.
- **Role** `fbg_member` with registration, login, forgot password, GDPR export hook.
- **Dashboard** tabs: overview, posts, links, profile, notifications, settings.
- **Guest-post slot limits:** new members start with **1** slot; **~2 minutes active reading** per other members’ posts (tracked on single posts) unlocks **+1 slot per 2 posts** completed. **Pro** / **Administrator** bypass the cap.
- **Affiliate program:** `?fbg_ref={user_id}` cookie (90 days); referred **views** (published posts) and **sidebar contact** can credit the referrer. **Organic search** referrers stack toward **$2 per 1,000** milestones; **all** attributed engagements stack toward **+10 bonus slots per 1,000**. Only **admin-approved** partners earn new credit (see below); legacy users with existing referral totals may still count until marked removed.
- **WP Admin → Affiliates:** manage **active partners**, **applications** (from public form), **add/remove**, **warnings** (shown on member dashboard + notification).
- **Sidebar ads** CPT + settings for single-post sidebar (stack / slider / random).
- **SEO / robots** helpers (`inc/seo.php`); see `seo.md`.
- **Responsive:** `assets/css/fbg-responsive.css` loads after other theme CSS bundles.

## Important files

| Path | Role |
|------|------|
| `functions.php` | Bootstrap, enqueues, page creation, version constant |
| `inc/custom-post-types.php` | `fbg_post`, taxonomies, admin meta |
| `inc/ajax-handlers.php` | Auth, submit, archive, dashboard, affiliate apply, read progress, contact |
| `inc/helpers.php` | Tiers, notifications, emails, related posts helpers |
| `inc/reading-affiliate.php` | Read tracking meta, affiliate cookie + engagement, partner check |
| `inc/admin-affiliates.php` | Admin UI: Affiliates menu |
| `inc/security.php` | Member restrictions, media scope |
| `assets/css/main.css`, `blog.css`, `dashboard.css`, `auth.css`, `fbg-marketing.css`, `fbg-responsive.css` | Front styles |
| `assets/js/reading-tracker.js` | Active-tab reading time for slot unlocks |

## Documentation in repo

- `CHANGELOG.md` — version history
- `seo.md` — SEO notes
- `remote-management.md` — deploy / server notes
- `theme-build-instructions.md` — build / packaging notes

## Maintainers: keep this README current

When you ship meaningful changes:

1. Bump **`Version`** in `style.css` and **`FBG_VERSION`** in `functions.php`.
2. Update **this README** (version line, new features, new admin menus, new slugs or requirements).
3. Add a line to **`CHANGELOG.md`** if you use it for releases.

---

*Theme text domain: `free-backlinks-generator`.*
