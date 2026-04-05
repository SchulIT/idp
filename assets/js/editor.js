import {
    ClassicEditor,
    Bold,
    Italic,
    Link,
    Paragraph,
    Strikethrough,
    Essentials,
    BlockQuote,
    List,
    Code,
    CodeBlock,
    Heading,
    Markdown,
    HorizontalLine,
    Emoji,
    Mention,
    Table,
    TableToolbar
} from 'ckeditor5';

import deTranslation from 'ckeditor5/translations/de.js';
import 'ckeditor5/dist/ckeditor5.min.css';

for(let el of document.querySelectorAll('[data-editor=markdown]')) {
    ClassicEditor.create(
        el,
        {
            licenseKey: 'GPL',
            plugins: [
                Bold,
                Italic,
                Link,
                Paragraph,
                Strikethrough,
                Essentials,
                BlockQuote,
                List,
                Code,
                CodeBlock,
                Heading,
                Markdown,
                HorizontalLine,
                Emoji,
                Mention,
                Table,
                TableToolbar
            ],
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'emoji', '|',
                'blockquote', 'insertTable', 'code', 'codeBlock', 'horizontalLine', '|',
                'undo', 'redo'
            ],
            table: {
                defaultHeadings: { rows: 1 },
                contentToolbar: [ 'tableColumn', 'tableRow' ]
            },
            translations: [
                deTranslation
            ]
        }
    );
}