@extends('larrock::admin.main')
@section('title') {{ $app->name }} admin @endsection

@section('content')
    <div class="container-head uk-margin-bottom">
        <div class="uk-grid">
            <div class="uk-width-expand">
                {!! Breadcrumbs::render('admin.'. $app->name .'.index') !!}
                <a class="link-blank" href="/{{ $app->name }}/">/{{ $app->name }}/</a>
            </div>
            <div class="uk-width-auto">
                @if(isset($allowCreate))
                    <a class="uk-button uk-button-primary uk-width-1-1 uk-width-auto@s" href="/admin/{{ $app->name }}/create">Добавить материал</a>
                @endif
            </div>
        </div>
    </div>

    @if(isset($data))
        <div class="uk-margin-large-bottom ibox-content">
            <table class="uk-table uk-table-striped uk-form">
                <thead>
                <tr>
                    <th width="55"></th>
                    @if(isset($app->rows['title']))
                        <th>{{ $app->rows['title']->title }}</th>
                    @endif
                    @foreach($app->rows as $row)
                        @if($row->in_table_admin || $row->in_table_admin_ajax_editable)
                            <th style="width: 90px" class="uk-visible@s">{{ $row->title }}</th>
                        @endif
                    @endforeach
                    @include('larrock::admin.admin-builder.additional-rows-th')
                </tr>
                </thead>
                <tbody class="uk-sortable" data-uk-sortable="{handleClass:'uk-sortable-handle'}">
                <tr class="tr-massiveAction">
                    <td colspan="6">
                        @include('larrock::admin.admin-builder.massive-action', ['data' => $data, 'app' => $app->name, 'formId' => 'massiveAction'])
                    </td>
                </tr>
                @foreach($data as $data_value)
                    <tr>
                        <td width="55">
                            <div class="actionSelect" data-target="massiveAction" data-id="{{ $data_value->id }}">
                                @if($app->plugins_backend && array_key_exists('images', $app->plugins_backend)
                                && $image = $data_value->getMedia('images')->sortByDesc('order_column')->first())
                                    <img src="{{ $image->getUrl('110x110') }}">
                                @else
                                    <i uk-icon="icon: image; ratio: 2" title="Фото не прикреплено"></i>
                                @endif
                            </div>
                        </td>
                        @if(isset($app->rows['title']))
                            <td>
                                <a class="uk-h4" href="/admin/{{ $app->name }}/{{ $data_value->id }}/edit">{{ $data_value->title }}</a>
                                <br/>
                                <a class="link-to-front" target="_blank" href="{{ $data_value->full_url }}" title="ссылка на элемент на сайте">
                                    {{ str_limit($data_value->full_url, 35, '...') }}
                                </a>
                            </td>
                        @endif
                        @foreach($app->rows as $row)
                            @if($row->in_table_admin_ajax_editable)
                                @if($row instanceof \Larrock\Core\Helpers\FormBuilder\FormCheckbox)
                                    <td class="row-active uk-visible@s">
                                        <div class="uk-button-group btn-group_switch_ajax" role="group" style="width: 100%">
                                            <button type="button" class="uk-button uk-button-primary uk-button-small
                                                    @if($data_value->{$row->name} === 0) uk-button-outline @endif"
                                                    data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="{{ $app->table }}"
                                                    data-row="active" data-value="1" style="width: 50%">on</button>
                                            <button type="button" class="uk-button uk-button-danger uk-button-small
                                                    @if($data_value->{$row->name} === 1) uk-button-outline @endif"
                                                    data-row_where="id" data-value_where="{{ $data_value->id }}" data-table="{{ $app->table }}"
                                                    data-row="active" data-value="0" style="width: 50%">off</button>
                                        </div>
                                    </td>
                                @elseif($row instanceof \Larrock\Core\Helpers\FormBuilder\FormInput)
                                    <td class="uk-visible@s">
                                        <input type="text" value="{{ $data_value->{$row->name} }}" name="{{ $row->name }}"
                                               class="ajax_edit_row form-control uk-input uk-form-small"
                                               data-row_where="id" data-value_where="{{ $data_value->id }}"
                                               data-table="{{ $app->table }}">
                                        @if($row->name === 'position')
                                            <i class="uk-sortable-handle uk-icon uk-icon-bars uk-margin-small-right" title="Перенести материал по весу"></i>
                                        @endif
                                    </td>
                                @elseif($row instanceof \Larrock\Core\Helpers\FormBuilder\FormSelect)
                                    <td class="uk-visible@s">
                                        <select class="ajax_edit_row form-control uk-select uk-form-small"
                                                data-row_where="id" data-value_where="{{ $data_value->id }}"
                                                data-table="{{ $app->table }}" data-row="{{ $row->name }}">
                                            @foreach($row->options as $option)
                                                <option @if($option === $data_value->{$row->name}) selected @endif value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endif
                            @endif
                            @if($row->in_table_admin)
                                <td class="uk-visible@s">
                                    {{ $data_value->{$row->name} }}
                                </td>
                            @endif
                        @endforeach
                        @include('larrock::admin.admin-builder.additional-rows-td')
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if(count($data) === 0)
                <div class="uk-alert uk-alert-warning">Данных еще нет</div>
            @endif
            @if(method_exists($data, 'total'))
                {!! $data->links('larrock::admin.pagination.uikit3') !!}
            @endif
        </div>
    @endif

    @if(isset($categories))
        <div class="uk-margin-large-bottom">
            <table class="uk-table uk-table-striped uk-form">
                <thead>
                <tr>
                    <th width="55"></th>
                    <th>Разделы:</th>
                    @include('larrock::admin.admin-builder.additional-rows-th')
                </tr>
                </thead>
                <tbody>
                @include('larrock::admin.category.include-create-easy', ['parent' => $categories->first()->id, 'component' => $app->name])
                @if(count($categories) === 0)
                    <div class="uk-alert uk-alert-warning">Разделов еще нет</div>
                @else
                    @include('larrock::admin.category.include-list-categories', ['data' => $categories])
                @endif
                </tbody>
            </table>
            @if(method_exists($categories, 'total'))
                {!! $categories->links('larrock::admin.pagination.uikit3') !!}
            @endif
        </div>
    @endif
@endsection