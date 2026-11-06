<?php
/**
 * D · Assembly Order: genuinely sequential setup. Each step depends on the one
 * before it, except where marked, and every step carries an id so a reader can
 * enter mid-guide from a link.
 */
$this->layout('partials::layout', [
    'title' => 'Getting started · flat-file',
    'description' => 'Static-build quick start for flat-file: composer.json, pages, a shared layout, the build output, and the PHP version that decides whether it installs.',
    'path' => '/getting-started/',
    'notFound' => false,
]);
?>
<section class="intro intro--compact" aria-labelledby="guide-title">
    <p class="t-smallcaps">flat-file · assembly order</p>
    <h1 id="guide-title">Getting <span class="t-accent">started.</span></h1>
    <p class="t-lead">Seven steps to a static site: one dependency, one page, one layout, one
        build. The example is the one this page was verified against — PHP 8.5.11 and Composer
        2.10.3 — not a sketch of it.</p>
    <p class="t-meta guide-source">Commands below were run in a throwaway project while
        writing this page, against the revision this microsite itself pins
        (<code>dev-code-highlight</code>, <code>3ba5285</code>). The two commands that would
        start a server or deploy were not run, and they say so where they appear.</p>
</section>

<div class="assembly">
    <section class="assembly__step" id="step-1" aria-labelledby="step-1-title">
        <span class="assembly__num" aria-hidden="true">01</span>
        <h2 id="step-1-title" class="t-headline-sm">Check the two versions that matter</h2>
        <div class="prose">
            <p>You need PHP and Composer, and nothing else. PHP runs at build time; the
                output is plain HTML, so the host that serves the site does not need PHP at
                all.</p>
            <p>The versions used here: <strong>PHP 8.5.11</strong> (Homebrew, CLI) and
                <strong>Composer 2.10.3</strong>. PHP 8.3 or later is a safe floor for what
                follows, because the pinned revision pulls Symfony Console 7 and CommonMark 2.</p>
            <p><strong>The one caveat that decides the install.</strong> The published tags
                <code>1.0.0</code>–<code>1.0.3</code> and the <code>master</code> branch — the
                branch the source README's sample <code>composer.json</code> names as
                <code>dev-master</code> — declare <code>php ~7.1</code>. On current PHP,
                Composer refuses them outright:</p>
            <pre><code>- slogsdon/flat-file[1.0.0, ..., 1.0.3] require php ~7.1
  -&gt; your php version (8.5.11) does not satisfy that requirement.</code></pre>
            <p>The same applies to <code>composer create-project
                slogsdon/flat-file-starter</code>: the starter requires <code>^1.0</code>, so
                it fails the same way. The working constraint is the
                <code>dev-code-highlight</code> branch, pinned to a commit in step
                2.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-2" aria-labelledby="step-2-title">
        <span class="assembly__num" aria-hidden="true">02</span>
        <h2 id="step-2-title" class="t-headline-sm">Write composer.json, then install</h2>
        <div class="prose">
            <p>This is the README's sample file with one substitution: the dependency is the
                branch that installs on current PHP, not <code>dev-master</code>. The
                <code>scripts</code> and <code>config</code> sections are optional — they only
                give the two commands shorter names, and <code>process-timeout</code> lets the
                server script outlive Composer's 300-second default.</p>
            <pre><code>{
    "name": "user/site",
    "description": "",
    "require": {
        "slogsdon/flat-file": "dev-code-highlight"
    },
    "scripts": {
        "build": "flat-file build",
        "start": "flat-file serve"
    },
    "config": {
        "process-timeout": 0
    }
}</code></pre>
            <p>Then pull down dependencies:</p>
            <pre><code>composer install</code></pre>
            <p>That resolves 22 packages and installs <code>slogsdon/flat-file
                (dev-code-highlight 3ba5285)</code> into <code>vendor/</code>. Commit the
                generated <code>composer.lock</code>; without it a later install can resolve
                a different commit of the same branch.</p>
            <p>If you drop the <code>scripts</code> section, call the binary directly:
                <code>vendor/bin/flat-file build</code> and <code>vendor/bin/flat-file
                serve</code>.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-3" aria-labelledby="step-3-title">
        <span class="assembly__num" aria-hidden="true">03</span>
        <h2 id="step-3-title" class="t-headline-sm">Add pages</h2>
        <div class="prose">
            <p>Create a <code>pages</code> directory next to <code>composer.json</code>. This
                is the whole homepage:</p>
            <pre><code>&lt;?php /* pages/index.php */ ?&gt;
&lt;h1&gt;Hello World&lt;/h1&gt;</code></pre>
            <p>A second page in Markdown needs no front matter and no wrapper:</p>
            <pre><code>&lt;!-- pages/about.md --&gt;
# About

flat-file reads this file and writes /about.</code></pre>
            <p>Routing is the file system. The filename, minus its extension, is the URL; a
                nested directory is a nested path. These are the paths this example produced:</p>
        </div>
        <div class="table-wrap">
            <table>
                <caption class="t-smallcaps">file to URL to artifact</caption>
                <thead>
                    <tr><th scope="col">Source file</th><th scope="col">URL</th><th scope="col">Build output</th></tr>
                </thead>
                <tbody>
                    <tr><th scope="row"><code>pages/index.php</code></th><td><code>/</code></td><td><code>dist/index.html</code></td></tr>
                    <tr><th scope="row"><code>pages/about.md</code></th><td><code>/about</code></td><td><code>dist/about/index.html</code></td></tr>
                    <tr><th scope="row"><code>pages/notes/hello.php</code></th><td><code>/notes/hello</code></td><td><code>dist/notes/hello/index.html</code></td></tr>
                    <tr><th scope="row"><code>pages/404.html.php</code></th><td><code>/404.html</code></td><td><code>dist/404.html</code></td></tr>
                    <tr><th scope="row"><code>public/assets/site.css</code></th><td><code>/assets/site.css</code></td><td><code>dist/assets/site.css</code></td></tr>
                </tbody>
            </table>
        </div>
        <div class="prose">
            <p>The last two rows are the two exceptions to the
                <code>&lt;directory&gt;/index.html</code> rule. A page whose filename already
                carries an extension is written to that exact filename, which is how a
                <code>404.html.php</code> page produces a root <code>404.html</code>; and
                <code>public/</code> is copied through verbatim, keeping its own paths.</p>
            <p><strong>Print, don't return.</strong> The README says a PHP page may return its
                content as a string (<code>&lt;?php return 'hello world';</code>). In the
                revision pinned here that page builds to a zero-byte file: PHP pages are
                rendered through the template engine, which captures output and discards a
                return value. Use markup or <code>echo</code>.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-4" aria-labelledby="step-4-title">
        <span class="assembly__num" aria-hidden="true">04</span>
        <h2 id="step-4-title" class="t-headline-sm">Wrap the pages in a layout</h2>
        <div class="prose">
            <p>Without a layout every page has to carry its own
                <code>&lt;html&gt;</code>, <code>&lt;head&gt;</code>, and navigation. Put the
                shared shell in <code>resources/partials/</code>, which the generator registers
                as a template folder named <code>partials</code>:</p>
            <pre><code>&lt;?php /* resources/partials/layout.php */ ?&gt;
&lt;!doctype html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
    &lt;meta charset="utf-8"&gt;
    &lt;title&gt;&lt;?= $this-&gt;e($title) ?&gt;&lt;/title&gt;
    &lt;meta name="description" content="&lt;?= $this-&gt;e($description) ?&gt;"&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;main&gt;&lt;?= $this-&gt;section('content') ?&gt;&lt;/main&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
            <p>A PHP page then opens by declaring the layout and the values it needs. Everything
                after that call is captured as the <code>content</code> section the layout
                prints:</p>
            <pre><code>&lt;?php /* pages/index.php */ ?&gt;
&lt;?php $this-&gt;layout('partials::layout', [
    'title' =&gt; 'Home',
    'description' =&gt; 'A flat-file example.',
]); ?&gt;
&lt;h1&gt;Hello World&lt;/h1&gt;
&lt;p&gt;Rendered by flat-file through a shared layout.&lt;/p&gt;</code></pre>
            <p>Markdown pages reach the same mechanism through front matter, with one
                difference in the layout: a Markdown page is handed its converted body as a
                <code>$content</code> variable, not as a section. So a Markdown layout prints
                <code>&lt;?= $content ?&gt;</code>, and the page names it by filename:</p>
            <pre><code>&lt;!-- pages/about.md --&gt;
---
layout: partials::doc
title: About
description: About this example.
---
# About

Written in Markdown, wrapped by the same layout.</code></pre>
            <p>Two layouts are fine, and so is one layout that prints both
                <code>&lt;?= $this-&gt;section('content') ?&gt;</code> and
                <code>&lt;?= $content ?&gt;</code>. What costs something is mixing them up: a
                Markdown page pointed at a layout that prints only the <code>content</code>
                section builds successfully and emits an empty <code>&lt;main&gt;</code>. That
                was verified here, and it is silent — nothing in the build output warns.</p>
            <p>A layout may also name a partial for repeated fragments, e.g.
                <code>&lt;?= $this-&gt;insert('partials::nav') ?&gt;</code>; that form was not
                exercised here.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-5" aria-labelledby="step-5-title">
        <span class="assembly__num" aria-hidden="true">05</span>
        <h2 id="step-5-title" class="t-headline-sm">Build the static site</h2>
        <div class="prose">
            <pre><code>composer build</code></pre>
            <p>or, without the Composer script, <code>vendor/bin/flat-file build</code>. The
                output is four lines, and the last one is the only success signal:</p>
            <pre><code>Discovering routes...
Generating files...
Copying public files...
Finished</code></pre>
            <p>The default destination is <code>dist/</code> in the project root. Pass a
                different directory as the first argument — <code>composer build --
                dist/site</code> — which is the form this microsite's own build script uses.
                Then upload that directory. It is the entire site.</p>
            <p>Two things the generator does not do for you: it does not render a page that no
                file matches, and it does not invent redirects. A URL with no source file is
                simply absent from the artifact, so a static host's own 404 handling applies
                unless you build a <code>404.html</code> page as in step 3.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-6" aria-labelledby="step-6-title">
        <span class="assembly__num" aria-hidden="true">06</span>
        <h2 id="step-6-title" class="t-headline-sm">Look at it before you build</h2>
        <div class="prose">
            <p><code>composer start</code> runs PHP's built-in server on
                <code>http://localhost:3000</code>, with <code>public/</code> as the document
                root — or your own <code>public/index.php</code> if you write one, and the
                package's <code>src/router.php</code> otherwise. The README's custom-entrypoint
                example is the file to copy if you need one:</p>
            <pre><code>&lt;?php declare(strict_types=1);

$uri = $_SERVER['REQUEST_URI'];

if ($uri !== '/' &amp;&amp; file_exists(getcwd() . '/public' . $uri)) {
    return false;
}

require getcwd() . '/vendor/autoload.php';
new FlatFile\Application;</code></pre>
            <p><strong>Not verified here:</strong> the server was not started while writing this
                page. <code>flat-file serve --dry-run</code> prints the command it would run —
                <code>php -S localhost:3000 -t &lt;project&gt;/public
                &lt;project&gt;/vendor/slogsdon/flat-file/src/router.php</code> — and that is
                all that was checked. The same tree, served by a web server pointed at
                <code>public/</code>, is what the README calls the flat-file CMS mode.</p>
        </div>
    </section>

    <section class="assembly__step" id="step-7" aria-labelledby="step-7-title">
        <span class="assembly__num" aria-hidden="true">07</span>
        <h2 id="step-7-title" class="t-headline-sm">Deploy the artifact, not the project</h2>
        <div class="prose">
            <p>There is nothing project-specific to deploy. <code>dist/</code> is HTML, CSS,
                and whatever else you put in <code>public/</code>, so any static host or
                object-storage origin will serve it; PHP is only needed in the build step.
                That is also the shape used here: this microsite and the collection hub both
                build with <code>composer build</code> and publish the output as static assets
                under a path prefix.</p>
            <p>The starter's README documents a Now (Vercel) Docker deployment. It is
                historical: <code>now.json</code>, a <code>composer:latest</code> image, and
                <code>^1.0</code> dependencies that no longer install on current PHP. Read it
                as an example of the file layout, not as a deployment recipe, and don't copy
                it.</p>
            <p><strong>Not verified here:</strong> no deploy, preview, or Cloudflare command was
                run while writing this page.</p>
        </div>
    </section>
</div>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">after the build</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/flat-file/">Project introduction<span aria-hidden="true"> →</span></a>
            <p>What flat-file is, the two repositories, and the status of the tagged
                releases.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/php-flat-file">Source on GitHub<span aria-hidden="true"> ↗</span></a>
            <p>The README this quick start is derived from, plus the parsers, commands, and
                router.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/php-flat-file-starter">flat-file-starter<span aria-hidden="true"> ↗</span></a>
            <p>The example project. Historical deployment notes; not a current install
                path.</p>
        </li>
    </ul>
</section>
