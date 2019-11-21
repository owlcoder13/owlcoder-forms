<script>
    // ====== form js ======
    $(document).ready(function () {
        <?php foreach($form->fields as $field){?>
        (function (el) {
            <?=$field->js()?>
        })($('#<?=$field->id?>'));
        <?php } ?>
    });
</script>
