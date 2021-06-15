<script>
    CKEDITOR.replace($(el)[0], <?= json_encode((object) $field->editorConfig) ?>);
</script>
