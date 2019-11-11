<script>
    @foreach($field->forms as $form)
        {!! $form->js() !!}
    @endforeach
</script>
