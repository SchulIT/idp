<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/js/app.js',
        'entrypoint' => true,
    ],
    'editor' => [
        'path' => './assets/js/editor.js',
        'entrypoint' => true,
    ],
    'collection' => [
        'path' => './assets/js/collection.js',
        'entrypoint' => true,
    ],
    'choices.js' => [
        'version' => '11.2.1',
    ],
    'typo-js' => [
        'version' => '1.2.5',
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '7.2.0',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/all.css' => [
        'version' => '7.2.0',
        'type' => 'css',
    ],
    'es-module-shims' => [
        'version' => '2.8.0',
    ],
    'ckeditor5' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-ui/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-adapter-ckfinder/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-alignment/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-autoformat/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-autosave/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-basic-styles/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-block-quote/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-bookmark/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-ckbox/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-ckfinder/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-clipboard/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-cloud-services/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-code-block/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-core/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-easy-image/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-editor-balloon/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-editor-classic/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-editor-decoupled/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-editor-inline/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-editor-multi-root/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-emoji/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-engine/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-enter/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-essentials/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-find-and-replace/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-font/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-fullscreen/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-heading/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-highlight/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-horizontal-line/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-html-embed/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-html-support/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-icons/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-image/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-indent/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-language/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-link/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-list/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-markdown-gfm/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-media-embed/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-mention/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-minimap/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-page-break/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-paragraph/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-paste-from-office/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-remove-format/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-restricted-editing/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-select-all/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-show-blocks/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-source-editing/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-special-characters/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-style/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-table/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-typing/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-undo/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-upload/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-utils/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-watchdog/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-widget/dist/index.js' => [
        'version' => '48.0.0',
    ],
    '@ckeditor/ckeditor5-word-count/dist/index.js' => [
        'version' => '48.0.0',
    ],
    'es-toolkit/compat' => [
        'version' => '1.45.1',
    ],
    'color-parse' => [
        'version' => '2.0.2',
    ],
    'color-convert' => [
        'version' => '3.1.0',
    ],
    'vanilla-colorful/lib/entrypoints/hex' => [
        'version' => '0.7.2',
    ],
    'blurhash' => [
        'version' => '2.0.5',
    ],
    'fuzzysort' => [
        'version' => '3.1.0',
    ],
    'unified' => [
        'version' => '11.0.5',
    ],
    'remark-gfm' => [
        'version' => '4.0.1',
    ],
    'remark-parse' => [
        'version' => '11.0.0',
    ],
    'remark-rehype' => [
        'version' => '11.1.2',
    ],
    'remark-breaks' => [
        'version' => '4.0.0',
    ],
    'rehype-dom-stringify' => [
        'version' => '4.0.2',
    ],
    'unist-util-visit' => [
        'version' => '5.0.0',
    ],
    'hast-util-to-html' => [
        'version' => '9.0.5',
    ],
    'hast-util-from-dom' => [
        'version' => '5.0.1',
    ],
    'rehype-dom-parse' => [
        'version' => '5.0.2',
    ],
    'rehype-remark' => [
        'version' => '10.0.1',
    ],
    'remark-stringify' => [
        'version' => '11.0.0',
    ],
    'hastscript' => [
        'version' => '9.0.1',
    ],
    'color-name' => [
        'version' => '2.0.0',
    ],
    'bail' => [
        'version' => '2.0.2',
    ],
    'extend' => [
        'version' => '3.0.2',
    ],
    'devlop' => [
        'version' => '1.1.0',
    ],
    'is-plain-obj' => [
        'version' => '4.1.0',
    ],
    'trough' => [
        'version' => '2.2.0',
    ],
    'vfile' => [
        'version' => '6.0.1',
    ],
    'mdast-util-gfm' => [
        'version' => '3.1.0',
    ],
    'micromark-extension-gfm' => [
        'version' => '3.0.0',
    ],
    'mdast-util-from-markdown' => [
        'version' => '2.0.0',
    ],
    'mdast-util-to-hast' => [
        'version' => '13.2.0',
    ],
    'mdast-util-newline-to-break' => [
        'version' => '2.0.0',
    ],
    'hast-util-to-dom' => [
        'version' => '4.0.0',
    ],
    'unist-util-visit-parents' => [
        'version' => '6.0.1',
    ],
    'html-void-elements' => [
        'version' => '3.0.0',
    ],
    'property-information' => [
        'version' => '7.0.0',
    ],
    'zwitch' => [
        'version' => '2.0.4',
    ],
    'stringify-entities' => [
        'version' => '4.0.4',
    ],
    'ccount' => [
        'version' => '2.0.1',
    ],
    'comma-separated-tokens' => [
        'version' => '2.0.3',
    ],
    'space-separated-tokens' => [
        'version' => '2.0.2',
    ],
    'hast-util-whitespace' => [
        'version' => '3.0.0',
    ],
    'web-namespaces' => [
        'version' => '2.0.1',
    ],
    'hast-util-to-mdast' => [
        'version' => '10.1.2',
    ],
    'mdast-util-to-markdown' => [
        'version' => '2.1.0',
    ],
    'hast-util-parse-selector' => [
        'version' => '4.0.0',
    ],
    'vfile-message' => [
        'version' => '4.0.2',
    ],
    'vfile/do-not-use-conditional-minpath' => [
        'version' => '6.0.1',
    ],
    'vfile/do-not-use-conditional-minproc' => [
        'version' => '6.0.1',
    ],
    'vfile/do-not-use-conditional-minurl' => [
        'version' => '6.0.1',
    ],
    'mdast-util-gfm-autolink-literal' => [
        'version' => '2.0.1',
    ],
    'mdast-util-gfm-footnote' => [
        'version' => '2.1.0',
    ],
    'mdast-util-gfm-strikethrough' => [
        'version' => '2.0.0',
    ],
    'mdast-util-gfm-table' => [
        'version' => '2.0.0',
    ],
    'mdast-util-gfm-task-list-item' => [
        'version' => '2.0.0',
    ],
    'micromark-util-combine-extensions' => [
        'version' => '2.0.0',
    ],
    'micromark-extension-gfm-autolink-literal' => [
        'version' => '2.0.0',
    ],
    'micromark-extension-gfm-footnote' => [
        'version' => '2.0.0',
    ],
    'micromark-extension-gfm-strikethrough' => [
        'version' => '2.0.0',
    ],
    'micromark-extension-gfm-table' => [
        'version' => '2.0.0',
    ],
    'micromark-extension-gfm-tagfilter' => [
        'version' => '2.0.0',
    ],
    'micromark-extension-gfm-task-list-item' => [
        'version' => '2.0.1',
    ],
    'mdast-util-to-string' => [
        'version' => '4.0.0',
    ],
    'micromark' => [
        'version' => '4.0.0',
    ],
    'micromark-util-decode-numeric-character-reference' => [
        'version' => '2.0.0',
    ],
    'micromark-util-decode-string' => [
        'version' => '2.0.0',
    ],
    'micromark-util-normalize-identifier' => [
        'version' => '2.0.0',
    ],
    'decode-named-character-reference' => [
        'version' => '1.0.2',
    ],
    'unist-util-stringify-position' => [
        'version' => '4.0.0',
    ],
    'micromark-util-sanitize-uri' => [
        'version' => '2.0.0',
    ],
    'unist-util-position' => [
        'version' => '5.0.0',
    ],
    'trim-lines' => [
        'version' => '3.0.1',
    ],
    '@ungap/structured-clone' => [
        'version' => '1.3.0',
    ],
    'mdast-util-find-and-replace' => [
        'version' => '3.0.0',
    ],
    'unist-util-is' => [
        'version' => '6.0.0',
    ],
    'unist-util-visit-parents/do-not-use-color' => [
        'version' => '6.0.1',
    ],
    'character-entities-legacy' => [
        'version' => '3.0.0',
    ],
    'character-entities-html4' => [
        'version' => '2.1.0',
    ],
    'rehype-minify-whitespace' => [
        'version' => '6.0.2',
    ],
    'hast-util-to-text' => [
        'version' => '4.0.2',
    ],
    'trim-trailing-lines' => [
        'version' => '2.1.0',
    ],
    'hast-util-phrasing' => [
        'version' => '3.0.1',
    ],
    'mdast-util-phrasing' => [
        'version' => '4.0.0',
    ],
    'longest-streak' => [
        'version' => '3.1.0',
    ],
    'micromark-util-character' => [
        'version' => '2.0.0',
    ],
    'markdown-table' => [
        'version' => '3.0.3',
    ],
    'micromark-util-chunked' => [
        'version' => '2.0.0',
    ],
    'micromark-core-commonmark' => [
        'version' => '2.0.0',
    ],
    'micromark-factory-space' => [
        'version' => '2.0.0',
    ],
    'micromark-util-classify-character' => [
        'version' => '2.0.0',
    ],
    'micromark-util-resolve-all' => [
        'version' => '2.0.0',
    ],
    'micromark-util-encode' => [
        'version' => '2.0.0',
    ],
    'micromark-util-subtokenize' => [
        'version' => '2.0.0',
    ],
    'escape-string-regexp' => [
        'version' => '5.0.0',
    ],
    'hast-util-minify-whitespace' => [
        'version' => '1.0.1',
    ],
    'unist-util-find-after' => [
        'version' => '5.0.0',
    ],
    'hast-util-is-element' => [
        'version' => '3.0.0',
    ],
    'hast-util-embedded' => [
        'version' => '3.0.0',
    ],
    'hast-util-has-property' => [
        'version' => '3.0.0',
    ],
    'hast-util-is-body-ok-link' => [
        'version' => '3.0.0',
    ],
    'micromark-factory-destination' => [
        'version' => '2.0.0',
    ],
    'micromark-factory-label' => [
        'version' => '2.0.0',
    ],
    'micromark-factory-title' => [
        'version' => '2.0.0',
    ],
    'micromark-factory-whitespace' => [
        'version' => '2.0.0',
    ],
    'micromark-util-html-tag-name' => [
        'version' => '2.0.0',
    ],
    'ckeditor5/dist/ckeditor5.min.css' => [
        'version' => '48.0.0',
        'type' => 'css',
    ],
    'ckeditor5/translations/de.js' => [
        'version' => '48.0.0',
    ],
];
