<?php

$value = $field->getValue();

?>

<div>
    <label for="">{{$field->label}}</label>

    <select id="{{$field->id}}" class="form-control" name="{{$field->name}}">
        @if($field->showEmpty)
            <option value=""> Не выбрано</option>
        @endif
        @foreach($field->options as $key => $label)
            <option @if($value == $key) selected @endif value="<?=$key?>"> <?=$label?></option>
        @endforeach
    </select>
</div>
