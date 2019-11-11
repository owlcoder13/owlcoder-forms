<label for="{{$field->id}}">{{$field->label}}</label>
<input {!! $inputAttributes !!} type="file"
       value="{{$field->getValue()}}">

@if(($value = $field->getValue()) != null)
    <div>
        already uploaded:
        <a href="{{$value}}">{{$value}}</a>
    </div>
@endif


