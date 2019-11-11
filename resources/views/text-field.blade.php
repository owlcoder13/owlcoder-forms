<label for="{{$field->id}}">{{$field->label}}</label>
<input {!! $inputAttributes !!} type="text"
       value="{{$field->getValue()}}">

@if(count($field->errors)>0)

<div class="error">
    {{join(', ',$field->errors)}}
</div>

@endif


