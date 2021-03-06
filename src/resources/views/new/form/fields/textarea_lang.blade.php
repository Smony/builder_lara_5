<section class="{{$field->getClassName()}}">
    <div class="tab-pane active">

        <ul class="nav nav-tabs tabs-pull-right">
            <label class="label pull-left" style="line-height: 32px;">{{$field->getName()}}</label>
            @foreach ($field->getLanguage() as $tab)
                <li class="{{$loop->first ? 'active' : ''}}">
                    <a href="#{{$definition->getNameDefinition() . $field->getNameField() . $tab['postfix']}}" data-toggle="tab">{{__cms($tab['caption'])}}</a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content padding-5">
            @foreach ($field->getLanguage() as $tab)
                <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{$definition->getNameDefinition() . $field->getNameField() . $tab['postfix']}}">
                    <div style="position: relative;">
                        <label class="textarea">
                            <textarea rows="{{$rows ?? '3'}}"
                              class="custom-scroll"
                              id="{{ $field->getNameField() . $tab['postfix']}}"
                              name="{{ $field->getNameField() . $tab['postfix']}}">{{$field->getValueLanguage($tab['postfix'])}}</textarea>
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
