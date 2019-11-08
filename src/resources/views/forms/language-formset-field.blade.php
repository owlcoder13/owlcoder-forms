<div class="nav-tabs-custom">
    <ul class="nav nav-tabs" role="tablist">
        <?php $i = 0 ?>

        @foreach($field->forms as $lang => $form)
            <li class="{{ $i++==0 ? 'active' : '' }}">
                <a href="#tab_{{$field->id}}_{{$lang}}">{{$lang}}</a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        <?php $i = 0 ?>

        @foreach($field->forms as $lang => $form)
            <div role="tabpanel" class="tab-pane {{ $i++==0 ? 'active' : '' }}" id="tab_{{$field->id}}_{{$lang}}">
                {!! $form->render() !!}
            </div>
        @endforeach
    </div>
</div>
