<div id="{{$field->id}}">
    <label for="">{{$field->label}}</label>

    @foreach($options as $key => $item)
        <div>
            <input {{in_array($key, $field->value) ? 'checked' : ''}} type="checkbox" name="{{$field->name}}"
                   type="text"
                   value="<?=$key?>"> <?=$item?>
        </div>
    @endforeach
</div>
