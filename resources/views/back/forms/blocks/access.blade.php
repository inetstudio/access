@inject('rolesService', 'InetStudio\ACL\Roles\Contracts\Services\Back\RolesServiceContract')

@php
    $item = $value;
    $accessConfig = config($name.'.access');
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
                        'options' => [
                            'values' => (old('access.'.$field.'.roles')) ? $rolesService->getRolesByIDs(old('access.'.$field.'.roles'), true)->pluck('display_name', 'id')->toArray() : $rolesService->getRolesByIDs($item->getFieldAccessByKey($field, 'roles'), true)->pluck('display_name', 'id')->toArray(),
                        ],
                    ]) !!}
                @endforeach
            </div>
        </div>
    </div>
@endif
