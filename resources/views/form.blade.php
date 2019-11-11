<div id="{{$form->id}}">
    @foreach($form->fields as $field)
        <div class="form-group">
            {!! $field->render() !!}
        </div>
    @endforeach
</div>
