<script>
    @foreach($field->forms as $form)

    (function (el) {
        {!! $form->js() !!}
    })($('#{{$form->id }}'));

    @endforeach

    el.find('> .forms-container').find(' .form > .delete-button').on('click', function () {
        console.log('click delete button');
        $(this).closest('.form').remove();
    });

    var index = {{count($field->forms)}};

    el.find('> .append').on('click', function (e) {
        console.log('click append');

        e.preventDefault();

        var fc = el.find('> .forms-container');
        var newForm = el.find('> .hidden-form > div').clone();

        console.log(fc);

        var newIndex = ++index;
        console.log('new index', newIndex);

        newForm.find('*').each(function () {
            let tag = $(this);

            $(['id', 'name', 'for']).each(function (i, attrName) {
                let attrValue = tag.attr(attrName);
                if (attrValue) {
                    tag.attr(attrName, attrValue.replace(/__index__/, newIndex));
                }
            });
        });

        newForm.find(' > .delete-button').on('click', function () {
            $(this).closest('.form').remove();
        });

        newForm.appendTo(fc);

        // nested form scripts here
        @foreach($field->hiddenForm->fields as $field)
        (function (el) {
            {{ $field->js() }}
        })($('#{{$field->id }}'.replace(/__index__/, newIndex)));
        @endforeach
    });
</script>
