<?php foreach ($options as $key => $text) { ?>
    <div>
        <input <?=$field->getValue() == $key ? 'checked' : ''?> type="radio" name="<?=$field->name?>"
               value="<?=$key?>"> <?=$text?>
    </div>
<?php } ?>
