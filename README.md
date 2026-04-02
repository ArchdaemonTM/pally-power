# Paladin Profile v5 — LUMINOUS Engine
**Rose Ministries · GoldHat Consulting LLC**
*Per Silvam et Iter*

---

## Repo-Wide Unique File Convention

All web-accessible directories follow a single naming rule:

> **Every directory that receives web traffic has exactly one entry point, named `<key>_index.php`.**

This convention exists for LLM compatibility and operational clarity. When an AI assistant reads this repo, every file name is globally unique — there is no ambiguity about which `index.php` is being referenced.

| Directory | Key File | Route | Web-Accessible |
|---|---|---|---|
| `/` | `index.html` | `/` | ✅ SPA root |
| `/api/` | `api_index.php` | `/api/?action=*` | ✅ API only |
| `/p/` | `p_index.php` | `/p/{slug}` | ✅ Character Armory |
| `/orders/` | `orders_index.php` | `/orders/` | ✅ Orders Manual |
| `/install/` | `install_index.php` | `/install/` | ⚠️ Delete after install |
| `/patches/` | `patches_index.php` | `/patches/` | ⚠️ Delete after each patch |
| `/includes/` | `db.php` | — | 🚫 PHP-internal only |
| `/public/` | *(static assets)* | `/public/css/` `/public/js/` | ✅ Static files only |

---

## Directory Structure

```
paladin-profile-v5/
├── .htaccess                  ← Master router — all rewrites defined here
├── index.html                 ← SPA (Heart Song, Builder, Gallery, Journal, etc.)
├── README.md
├── paladin-cultural-icons.md
│
├── api/
│   ├── .htaccess              ← Locks /api/ to api_index.php only
│   └── api_index.php          ← All API endpoints (?action=gamedata|character|...)
│
├── p/
│   ├── .htaccess              ← Locks /p/ to p_index.php only
│   └── p_index.php            ← Public character Armory view (/p/{slug})
│
├── orders/
│   ├── .htaccess              ← Locks /orders/ to orders_index.php only
│   └── orders_index.php       ← The Color Orders reference manual
│
├── install/
│   ├── .htaccess              ← Locks /install/ to install_index.php + blocks schema.sql
│   ├── install_index.php      ← Web installer (DELETE DIRECTORY AFTER USE)
│   └── schema.sql             ← Database schema + seed data (not web-accessible)
│
├── patches/
│   ├── .htaccess              ← Locks /patches/ to patches_index.php only
│   └── patches_index.php      ← DB Patch 1: content expansion (DELETE AFTER USE)
│                                 Self-locks via .patch1.lock after first run
│
├── includes/
│   ├── .htaccess              ← HARD DENY — no web access whatsoever
│   └── db.php                 ← PDO connection, utilities (require_once only)
│
└── public/
    ├── .htaccess              ← No directory listing, no PHP execution
    ├── css/
    │   ├── paladin.css        ← Main stylesheet (LUMINOUS aesthetic)
    │   └── heartsong.css      ← Heart Song quiz styles
    └── js/
        └── heartsong.js       ← Heart Song quiz engine
```

---

## Installation

### First Deploy

1. Upload all files to your IONOS subdomain root
2. Verify `.htaccess` is uploaded (hidden file — check your FTP client settings)
3. Navigate to `https://your-domain.com/install/`
4. Enter your MariaDB credentials and click **Install**
5. The installer will:
   - Create `config.php` one level above the web root (or in root if permissions allow)
   - Run `schema.sql` with the corrected SQL parser (handles semicolons in quoted strings)
   - Seed all 10 Orders, alignments, attributes, talent trees, callings, and feats
6. **DELETE the `/install/` directory immediately after success**

### Content Patch (Batch 1)

After install is confirmed working:

1. Navigate to `https://your-domain.com/patches/`
2. The patch runner executes and creates a `.patch1.lock` file to prevent re-runs
3. **DELETE `patches/patches_index.php`** — the `.htaccess` remains as a deny-all fallback
4. Verify expanded content in the Builder (160 callings, 100 specs, 6 talent trees)

---

## Routing Reference

All routing is defined in the **root `.htaccess`**. Per-directory `.htaccess` files are secondary enforcement only.

```
GET  /                          → index.html (SPA)
GET  /p/{slug}                  → /p/p_index.php?slug={slug}
GET  /orders/                   → /orders/orders_index.php
GET  /orders.html               → 301 redirect → /orders/
GET  /install/                  → /install/install_index.php
GET  /patches/                  → /patches/patches_index.php
GET  /api/?action=*             → /api/api_index.php
GET  /public/css/*.css          → served directly
GET  /public/js/*.js            → served directly
GET  /includes/*                → 404 (blocked at root + directory level)
GET  /*/config.php              → 404 (blocked everywhere)
```

---

## API Endpoints

All via `/api/?action=<action>`:

| Method | Action | Description |
|---|---|---|
| GET | `gamedata` | All orders, alignments, attributes, callings, specs, talents, feats |
| GET | `character&slug=XX` | Public character by share slug |
| GET | `character&id=UUID` | Character by UUID (owner with edit token) |
| POST | `character` | Create new character |
| PUT | `character&id=UUID` | Update character (requires `X-Edit-Token` header) |
| POST | `levelup&id=UUID` | Level up with journal entry |
| POST | `suggestion` | Submit community suggestion |
| GET | `export&id=UUID` | Export character as JSON |
| POST | `import` | Import character from JSON |
| GET | `gallery` | Public character browser |

---

## Known Fixes Applied in v5

| # | File | Bug | Fix |
|---|---|---|---|
| 1 | `install/install_index.php` | Naive `explode(';', $sql)` broke FK-dependent CREATE TABLE order | Replaced with char-by-char SQL parser that respects quoted strings |
| 2 | `api/api_index.php` | `build_json` double-encoded in `getCharacter()` response | Null `build_json` after decoding to `build_data` |
| 3 | `p/p_index.php` | No `/p/` PHP handler — slug never read, index.html served instead | Created `p_index.php` with server-side slug injection |
| 4 | `public/css/paladin.css` | `.custom-toggle` overlapped `<select>` native dropdown arrow | Replaced absolute positioning with flexbox row layout |
| 5 | `api/api_index.php` | Order color missing from character select display | `renderOrders()` now injects `--order-color` CSS var + color-tinted name |

---

## Security Notes

- `config.php` is blocked at the root `.htaccess` level (404 for any `*config.php` path)
- `/includes/` is hard-denied via both root rewrite and directory-level `Require all denied`
- `/install/` should not exist post-installation
- `/patches/` self-locks via `.patch1.lock` after first run; `patches_index.php` should be deleted
- Edit tokens are `hash_equals()` compared — timing-safe
- Rate limiting on suggestions: 10/hour per IP hash

---

## Version History

| Version | Notes |
|---|---|
| v5 | Repo-wide unique file convention (`<key>_index.php`). Full `.htaccess` coverage. Armory view. Orders manual promoted to `/orders/`. |
| v4 | Added Orders page, fixed custom-select overlap, order color display |
| v3 | Installer SQL parser fix, batch content expansion |
| v2 | Initial SPA + API + Heart Song quiz |

---

*GoldHat™ 98925168 · ArchDaemon™ 98940257 · LUMINOUS Engine v5.0*
*© 2026 David William Sylvester · All rights reserved*
