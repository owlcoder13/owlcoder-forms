<div>
    <label for="{{$field->id}}">{{$field->label}}</label>

    <select id="{{$field->id}}" class="form-control" name="{{$field->name}}">
        @foreach($field->options as $key => $label)
            <option @if($field->value == $key)selected@endif value="<?=$key?>"> <?=$label?>
        @endforeach
    </select>
</div>
