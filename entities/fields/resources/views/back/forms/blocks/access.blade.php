@inject('rolesService', 'InetStudio\ACL\Roles\Contracts\Services\Back\ItemsServiceContract')

@php
    $item = $value;
    $accessConfig = config($name.'.access.fields', []);
@endphp

@if ($accessConfig)
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">
                <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseAccess" aria-expanded="false" class="collapsed">Доступ</a>
            </h5>
        </div>
        <div id="collapseAccess" class="collapse" aria-expanded="false">
            <div class="panel-body">
                @foreach ($accessConfig as $field => $title)
                    {!! Form::dropdown('access[fields]['.$field.'][roles][]', $item->getFieldAccessByKey($field, 'roles'), [
                        'label' => [
                            'title' => $title,
                        ],
                        'field' => [
                            'class' => 'select2-drop form-control',
                            'data-placeholder' => 'Выберите роли',
                            'style' => 'width: 100%',
                            'multiple' => 'multiple',
                            'data-source' => route('back.acl.roles.utility.suggestions'),
                        ],
                        'options' => [
                            'values' => (old('access.fields'.$field.'.roles')) ? $rolesService->getItemById(old('access.fields'.$field.'.roles'))->pluck('display_name', 'id')->toArray() : $rolesService->getItemById($item->getFieldAccessByKey($field, 'roles'))->pluck('display_name', 'id')->toArray(),
                        ],
                    ]) !!}
                @endforeach
            </div>
        </div>
    </div>
@endif
