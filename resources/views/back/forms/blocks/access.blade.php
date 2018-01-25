@php
    $item = $value;
    $accessConfig = config($name.'.access');
@endphp

@if ($accessConfig)
    <div class="panel-group float-e-margins" id="accessAccordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accessAccordion" href="#collapseAccess" aria-expanded="false" class="collapsed">Доступ</a>
                </h5>
            </div>
            <div id="collapseAccess" class="panel-collapse collapse" aria-expanded="false">
                <div class="panel-body">
                    @foreach ($accessConfig as $field => $title)
                        {!! Form::dropdown('access['.$field.'][roles][]', $item->getFieldAccessByKey($field, 'roles'), [
                            'label' => [
                                'title' => $title,
                            ],
                            'field' => [
                                'class' => 'select2 form-control',
                                'data-placeholder' => 'Выберите роли',
                                'style' => 'width: 100%',
                                'multiple' => 'multiple',
                                'data-source' => route('back.acl.roles.getSuggestions'),
                            ],
                            'options' => (old('access.'.$field.'.roles')) ? \App\Role::whereIn('id', old('access.'.$field.'.roles'))->pluck('display_name', 'id')->toArray() : \App\Role::whereIn('id', $item->getFieldAccessByKey($field, 'roles'))->pluck('display_name', 'id')->toArray(),
                        ]) !!}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
