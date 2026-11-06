<?php
/**
 * F · Frontispiece: the single entry point to this project's set.
 * It names the work, states what it does, and indexes what follows.
 * No figure: a frontispiece carries no argument.
 */
$this->layout('partials::layout', [
    'title' => 'flat-file · pages are files, routes are paths',
    'description' => 'flat-file is a small PHP static-site generator and flat-file CMS: Markdown, HTML, or PHP pages in a pages directory, routed by file path, exported to plain HTML.',
    'path' => '/',
    'notFound' => false,
]);
?>
<section class="intro" aria-labelledby="project-title">
    <p class="t-smallcaps">shane logsdon · php · static sites</p>
    <h1 id="project-title">Pages are files.<br>Routes are <span class="t-accent">paths.</span></h1>
    <p class="t-lead">flat-file turns a directory of Markdown, HTML, and PHP into a website.
        A file in <code>pages/</code> is a URL; a subdirectory is a section. One command
        exports the result as plain HTML for a static host. The same tree can also run as a
        flat-file CMS on a PHP server.</p>
    <a class="btn-arrow" href="/flat-file/getting-started/">Build a site</a>
</section>

<section class="page-section" aria-labelledby="what-title">
    <header class="index-head">
        <h2 id="what-title" class="t-smallcaps-lg">what it is</h2>
        <span class="t-dateline">zero configuration</span>
    </header>
    <div class="prose">
        <p>The whole configuration is the directory layout. There is no database, no admin
            screen, and no build manifest: the generator reads <code>pages/</code>, finds a
            parser for each file, renders it, and writes the matching path into
            <code>dist/</code>.</p>
    </div>
    <dl class="facts">
        <div class="fact">
            <dt>pages/</dt>
            <dd>The site. Markdown (<code>.md</code>, <code>.markdown</code>), plain HTML, or
                PHP. <code>pages/index.php</code> becomes <code>/</code>,
                <code>pages/about.md</code> becomes <code>/about</code>, and a subdirectory
                becomes a deeper path.</dd>
        </div>
        <div class="fact">
            <dt>resources/partials/</dt>
            <dd>The layout. Registered as a Plates template folder named
                <code>partials</code>, so a page can wrap itself in one shared HTML
                shell.</dd>
        </div>
        <div class="fact">
            <dt>public/</dt>
            <dd>Static files — CSS, images, fonts — copied through to the build output
                unchanged, and served as-is by the development server.</dd>
        </div>
        <div class="fact">
            <dt>dist/</dt>
            <dd>The artifact: one directory per page with an <code>index.html</code>, plus the
                copied <code>public/</code> tree. Nothing to run at the other end.</dd>
        </div>
    </dl>
    <div class="prose">
        <p>Markdown is converted with CommonMark, so the syntax is CommonMark rather than a
            site-specific dialect. Layouts come from <code>league/plates</code>, which is why
            a page is written the way a PHP template is written. The README sums the intent
            up as: PHP is easy to install, runs nearly everywhere, and is flexible — the
            generator is an attempt to see how far PHP reaches in this problem
            domain.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="family-title">
    <header class="index-head">
        <h2 id="family-title" class="t-smallcaps-lg">the family</h2>
        <span class="t-dateline">two repositories</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="https://github.com/slogsdon/php-flat-file">flat-file<span aria-hidden="true"> ↗</span></a>
            <p>The generator: <code>flat-file build</code> and <code>flat-file serve</code>,
                the file parsers, the Plates wiring, and the optional router for running the
                same pages as a CMS.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/php-flat-file-starter">flat-file-starter<span aria-hidden="true"> ↗</span></a>
            <p>An example project: two pages, a <code>composer.json</code>, and a Docker
                build. Read it to see the shape of a flat-file site. Its deployment notes
                describe a historical Now/Docker setup, and its <code>^1.0</code> dependency
                does not install on current PHP — see <a href="/flat-file/getting-started/#step-1">requirements</a>.</p>
        </li>
        <li>
            <a href="https://projects.logsdon.io/">The project collection<span aria-hidden="true"> ↗</span></a>
            <p>This microsite is itself built by flat-file, from the same pinned revision the
                collection hub uses.</p>
        </li>
    </ul>
</section>

<section class="page-section" aria-labelledby="status-title">
    <header class="index-head">
        <h2 id="status-title" class="t-smallcaps-lg">status</h2>
        <span class="t-dateline">1.0.3 · mit</span>
    </header>
    <div class="prose">
        <p>Four tags exist: <code>1.0.0</code> through <code>1.0.3</code>. All four declare
            <code>php ~7.1</code> and CommonMark <code>^0.17.5</code>, which is also what the
            README means by “PHP 7.1+”. None of them installs on PHP 8.3 or later.</p>
        <p>The revision this site runs is not one of those tags. It is the
            <code>dev-code-highlight</code> branch, which drops the PHP version pin, moves to
            CommonMark 2 and Symfony Console 7, and adds syntax highlighting to fenced code
            blocks. It is the revision pinned in this site's own
            <code>composer.lock</code> and in the collection hub's, and it is the only
            revision whose commands are verified on this site. A tagged release on current
            PHP has not been made.</p>
        <p>This site makes no performance claim, publishes no download count, and does not
            claim that a 1.0.x install works.</p>
    </div>
</section>

<section class="page-section" aria-labelledby="next-title">
    <header class="index-head">
        <h2 id="next-title" class="t-smallcaps-lg">where to go</h2>
        <span class="t-dateline">starting point</span>
    </header>
    <ul class="link-list">
        <li>
            <a href="/flat-file/getting-started/">Getting started<span aria-hidden="true"> →</span></a>
            <p>The static-build quick start: a <code>composer.json</code>, two pages, the
                shared layout, the build output, and the one PHP-version caveat that decides
                whether the install works at all.</p>
        </li>
        <li>
            <a href="https://github.com/slogsdon/php-flat-file">Source on GitHub<span aria-hidden="true"> ↗</span></a>
            <p>The README, the commands, the parsers, and the test suite.</p>
        </li>
        <li>
            <a href="https://packagist.org/packages/slogsdon/flat-file">Packagist<span aria-hidden="true"> ↗</span></a>
            <p>Published versions and their declared requirements.</p>
        </li>
    </ul>
</section>
