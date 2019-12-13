<?php

/**
 * @var \Owlcoder\Forms\Fields\CheckBoxField $field
 */

?>

<div id="<?=$field->id?>">
    <?php $field->renderLabel() ?>

    <?php foreach ($options as $key => $item) { ?>
        <div>
            <input <?=in_array($key, $field->getValue()) ? 'checked' : ''?>
                   type="checkbox"
                   name="<?=$field->name?>"
                   value="<?=$key?>"> <?=$item?>
        </div>
    <?php } ?>

    <?php $field->renderErrors() ?>
</div>
