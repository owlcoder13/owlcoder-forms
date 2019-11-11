<div>
    <label for="">{{$field->label}}</label>

    @foreach($options as $key => $text)
        <div>
            <input {{$field->getValue() == $key ? 'checked' : ''}} type="radio" name="{{$field->name}}" type="text"
                   value="<?=$key?>"> <?=$text?>
        </div>
    @endforeach
</div>
