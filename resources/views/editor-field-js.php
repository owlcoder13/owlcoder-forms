<script>
    // decode editor config from php controller data
    let editorConfig = <?= json_encode((object) $field->editorConfig) ?>;

    // if element has 'disabled' attribute
    if ($(el).attr('disabled')) {
        // convert ckeditor to read only mode
        Object.assign(editorConfig, {
            readOnly: true
        })
    }

    CKEDITOR.replace($(el)[0], editorConfig);
</script>