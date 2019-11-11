<label for="{{$field->id}}">{{$field->label}}</label>
<input {!! $inputAttributes !!} type="file"
       value="{{$field->getValue()}}">

@if(($value = $field->getValue()) != null)
    <div>
        already uploaded: <br>
        <img src="{{$value}}" style="max-height: 200px;"/>
    </div>
@endif


