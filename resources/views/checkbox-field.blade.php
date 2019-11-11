<input value="0" type="hidden" name="{{$field->name}}">
<input id="{{$field->id}}" value="1" type="checkbox" name="{{$field->name}}" {{$field->getValue() ? 'checked' : ''}}>
<label for="{{$field->attribute}}">{{$field->label}}</label>
{{get_class($field)}}

