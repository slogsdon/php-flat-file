# flat-file — microsite

Static microsite for [flat-file](https://github.com/slogsdon/php-flat-file), served at
`https://projects.logsdon.io/flat-file/`.

Source revisions this site derives from:

- `d5b976a554a778b93cf8122f9c930cff3b4055ba` — the `feature/website` worktree's HEAD
  (`master`, 2026-05-08). The description, the feature list, the `composer.json`
  sample, the custom-entrypoint example, and the routing/parsers come from this
  revision: `README.md`, `composer.json`, `src/Application.php`,
  `src/Command/{BuildCommand,ServeCommand}.php`, `src/FileParser/*`,
  `src/Functions/content.php`, `src/router.php`. `site.json`'s `lastmod` is this
  revision's date, per the fan-out rule.
- `3ba5285e424aa376af324db0424f53c33a2c07a5` — `dev-code-highlight` (2026-07-30).
  The only revision that installs on current PHP, the one this microsite itself pins
  in `composer.lock` (and the collection hub pins in its own), and therefore the one
  the quick start was executed against.

`php-flat-file-starter` was read at its current HEAD for the example-project
description only. Nothing was written there.

## Build

Node 26.12.0, PHP 8.3+, and Composer are required.

```sh
cd website
composer install --no-interaction --prefer-dist
node scripts/build-site.ts          # SITE_NOINDEX=1 by default: preview, noindex
SITE_NOINDEX=0 node scripts/build-site.ts   # indexable production artifact
```

Output is a physical subtree at `website/dist/flat-file/`, plus `dist/_headers` in
preview builds. Nothing else is written at the `dist/` root: the hub owns
`/robots.txt`, `/sitemap.xml`, and the slashless `/flat-file` redirect.

Packaging (local only; neither command deploys):

```sh
npm install
npx --no-install cf build --mode microsite
npx --no-install cf build --mode microsite-production
```

## One file drives the site

`site.json` holds the values the build and the pages need — `slug`, `name`,
`origin`, `description`, `lastmod` — plus the `source` URL. `scripts/build-site.ts`
validates them, exports them to the PHP layout as `SITE_SLUG`, `SITE_NAME`,
`SITE_ORIGIN`, `SITE_SOURCE` and `SITE_NOINDEX`, and mounts every emitted URL under
`/flat-file/`. The layout throws if they are missing, so nothing falls back to a
hard-coded project name.

A second input decides the content: the flat-file pin in `composer.json`
(`slogsdon/flat-file`, `dev-code-highlight`, locked at `3ba5285`). PHP pages are
rendered by that generator at build time; there is no PHP at request time.

## What the build asserts

`scripts/build-site.ts` fails the build unless all of the following hold:

- `index.html`, `404.html`, `sitemap.xml`, `assets/site.css`, `assets/tokens.css`,
  `assets/fonts/*` exist and are non-empty.
- No `_redirects`, `robots.txt`, `sitemap.xml`, `index.html`, or `404.html` at the
  `dist/` root; the root holds only `flat-file/` (and `_headers` when
  `SITE_NOINDEX=1`).
- Every HTML page has exactly one `<h1>`, a unique `<title>` containing the project
  name, a meta description, the skip link, the `main` landmark, and the primary
  `nav`.
- Canonical and `og:url` equal `https://projects.logsdon.io/flat-file/<path>`; the
  404 carries neither and is always noindex.
- The `noindex` meta matches `SITE_NOINDEX`, and the footer links both
  `https://projects.logsdon.io/` and `https://shane.logsdon.io/`.
- No `href`/`src` is root-relative outside `/flat-file/`, and every local link and
  asset target exists in the artifact.
- The sitemap lists exactly the canonical pages, with `lastmod` set to
  `site.json`'s `lastmod`.

The Google Fonts `@import` is stripped from the copied `tokens.css` (fonts are
served locally) and `/assets/` inside the copied CSS is rewritten to
`/flat-file/assets/`.

## Content and design provenance

- Pages: `pages/index.php` is F · Frontispiece (the single entry point, no figure).
  `pages/getting-started.php` is D · Assembly Order (seven numbered steps).
  `pages/404.html.php` is G · Recovery.
- `public/assets/tokens.css`, the fonts, `assets/fonts/LICENSES.txt`, and the first
  part of `public/assets/site.css` are copied from the collection hub
  (`projects.logsdon.io`) at its `main`; `design/provenance.json` is copied with
  them and records the personal-site hashes (its paths are hub-relative). The
  appended section of `site.css` covers what a project page needs and the hub's two
  pages do not: prose sections, code, tables, and step blocks. No rules were added
  for this site.
- `composer.lock` pins `slogsdon/flat-file` to the same reference as the hub
  (`dev-code-highlight`, `3ba5285e424aa376af324db0424f53c33a2c07a5`); only the
  lock's `content-hash` differs, regenerated for this package name.
- `package.json` pins the hub's `cf`, `@cloudflare/vite-plugin`, and `vite` versions
  exactly. Lint, formatting, and browser test tooling from the hub are not
  included; this site has no client JavaScript.
- The site quotes `flat-file`'s own README for the `composer.json` shape, the page
  files, the custom entrypoint, and `composer start`. Every quoted command is
  reproduced below with its verification status.

## Verified here

Environment: **PHP 8.5.11** (Homebrew CLI) with **Composer 2.10.3**, Node 26.12.0,
macOS. Proving the quick start used a throwaway project at
`website/.runtime/quickstart/` (gitignored; disposable, not part of the site).

1. `website/`: `composer install --no-interaction --prefer-dist` → 22 packages,
   `slogsdon/flat-file (dev-code-highlight 3ba5285)`. Composer warns that
   `spatie/commonmark-highlighter` is abandoned; that package backs fenced-code
   highlighting in the pinned branch.
2. `website/`: `node scripts/build-site.ts` (preview) →
   `Microsite build: flat-file — 2 page(s), noindex preview`, and
   `SITE_NOINDEX=0 node scripts/build-site.ts` →
   `Microsite build: flat-file — 2 page(s), indexable production`. Artifact:
   `dist/flat-file/{index.html,404.html,sitemap.xml,getting-started/index.html}`
   plus `assets/{site.css,tokens.css,fonts/*}`, and `dist/_headers` only in the
   preview build. No `<meta name="robots">` in either indexable page in the
   production build; the 404 keeps it.
3. `website/`: `npm install` (59 packages), then
   `npx --no-install cf build --mode microsite` and
   `npx --no-install cf build --mode microsite-production` both exit 0 and package
   all ten site files under
   `.cloudflare/output/v0/workers/default/assets/flat-file/`.
4. Quick start, `website/.runtime/quickstart/`, `composer.json` requiring
   `slogsdon/flat-file: dev-code-highlight`:
   `composer install` → 22 packages; `composer build` → `Discovering routes... /
   Generating files... / Copying public files... / Finished`, exit 0. Artifact:
   `dist/404.html`, `dist/about/index.html`, `dist/assets/site.css`, `dist/index.html`,
   `dist/notes/hello/index.html`. The `composer.lock` there resolved
   `dev-code-highlight 3ba5285e424aa376af324db0424f53c33a2c07a5`.
5. Quick start layout, both forms: a PHP page using
   `$this->layout('partials::layout', [...])` with `<?= $this->section('content') ?>`
   in the layout renders its markup; a Markdown page using front matter
   `layout: partials::doc` with `<?= $content ?>` renders its converted body.
6. Route/output mapping exactly as the getting-started table states:
   `pages/index.php` → `dist/index.html`; `pages/about.md` →
   `dist/about/index.html`; `pages/notes/hello.php` →
   `dist/notes/hello/index.html`; `pages/404.html.php` → `dist/404.html`;
   `public/assets/site.css` → `dist/assets/site.css`.
7. The compatibility claim on the page, checked in scratch projects on PHP 8.5.11:
   - `"slogsdon/flat-file": "^1.0"` → Composer refuses: `slogsdon/flat-file[1.0.0,
     ..., 1.0.3] require php ~7.1 -> your php version (8.5.11) does not satisfy that
     requirement.`
   - `"slogsdon/flat-file": "dev-master"` (the constraint the source README shows) →
     same refusal, `dev-master requires php ~7.1`.
   - `composer create-project slogsdon/flat-file-starter` → same class of failure
     (`flat-file v1.0.1 requires php ~7.1`, plus the locked Symfony 4.1 and
     illuminate 5.6 packages).
8. The `return` caveat on the page: a page containing only
   `<?php return '<h1>returned</h1>';` built to a **0-byte**
   `dist/returned/index.html`, while the same markup printed outside `<?php ?>`
   rendered normally.
9. The Markdown/section pitfall on the page: the same Markdown page pointed at a
   layout that prints only `$this->section('content')` built without error and
   emitted `<main></main>`.
10. The absence of out-of-namespace root-relative URLs, and the
    preview/production indexing difference (`grep` over `dist/` returns nothing for
    root-relative paths outside `/flat-file/`).

## Not verified here (Unverified)

- `composer start` — from the source `README.md` ("Running the Development Server").
  Not run: no server was started in this environment. `flat-file serve --dry-run`
  only printed the command it would run
  (`php -S localhost:3000 -t <project>/public <project>/vendor/slogsdon/flat-file/src/router.php`),
  and the page says so.
- `vendor/bin/flat-file build` and `vendor/bin/flat-file serve` — from the same
  README. The `build` form was not invoked directly; `composer build` calls it. The
  `serve` form was only dry-run.
- `composer build -- dist/site` — from the README's optional destination argument.
  The same form was exercised as `composer build -- dist/flat-file` by
  `scripts/build-site.ts`; the literal `dist/site` path was not run.
- The custom entrypoint (`public/index.php`) quoted on the page — from the README
  ("Using a Custom Entrypoint"). Read, not exercised; this microsite uses the
  default router.
- Flat-file CMS mode, i.e. serving `pages/` live from a PHP web server with the web
  root at `public/` — from the README ("Running as a Flat-File CMS"). Not exercised.
- `$this->insert('partials::nav')` for shared partials — Plates API, mentioned on
  the page as not exercised.
- Deployment of any kind: no `cf deploy`, no `cf previews deploy`, no Cloudflare API
  call. The two `cf build` modes only produce a local artifact.

## Deployment

Not performed from this repository. Publication is the collection's own step and
runs through the hub's workflow; this repository only produces the artifact and the
Worker configuration. The hub owns the hostname (`projects.logsdon.io`), the sitemap
index, and the slashless redirect; this Worker claims only
`projects.logsdon.io/flat-file/*`.
