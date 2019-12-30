<?php

$formStyle = "margin: 10px 0 10px 40px; padding: 20px; border: 1px dashed gray;";

?>

<div class="field" id="<?=$field->id?>">
    <?=$field->label?> (<?=count($field->forms)?>)

    <div class="hidden-form" style="display: none;">
        <div class="form" style="<?=$formStyle?>">
            <?=$field->hiddenForm->render()?>

            <div style="float: right;" class="delete-button btn btn-xs btn-danger">Удалить</div>
        </div>
    </div>

    <div class="forms forms-container">
        <?php foreach ($field->forms as $form) { ?>
            <div class="form" style="<?=$formStyle?>">
                <?=$form->render()?>
                <div style="float: right;" class="delete-button btn btn-xs btn-danger">Удалить</div>
            </div>
        <?php } ?>
    </div>

    <button type="button" class="btn btn-xs append">Добавить</button>
</div>
