<?php

$formStyle = "margin: 10px 0 10px 40px; padding: 20px; border: 1px dashed gray;";

?>

<div class="field" id="<?=$field->id?>" style="border-left: 2px solid #56d06f; padding-left: 20px;">
    <?=$field->label?>

    <div class="form-group-container">
        <div class="form" style="{{$formStyle}}">
            <?=$field->nestedForm->render()?>
        </div>
    </div>
</div>
