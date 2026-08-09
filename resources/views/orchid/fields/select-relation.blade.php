@component($typeForm, get_defined_vars())
    <div id={{ $id }} data-controller="{{ $controller }}" data-{{ $controller }}-rows={{ $rows }}>
        @if ($sorting)
            <div class="form-group mb-3">
                <label class="form-label">{{ $sorting['label'] }}</label>
                <select class="form-control" data-sort-mode>
                    @foreach ($sorting['options'] as $value => $option)
                        <option value="{{ $value }}" {{ $value === $sorting['default'] ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <template data-template>
            @foreach ($inputsGroups[0] as $input)
                <div class="form-group mb-3">
                    @if (isset($input['title']))
                        <label class="form-label">{{ $input['title'] }}</label>
                    @endif
                    @if (!empty($input['enhanced']))
                        <div data-enhanced-select data-controller="select" data-select-allow-add="false" data-select-allow-empty="true"
                            data-select-message-notfound="Ничего не найдено">
                    @endif
                        <select class="form-control" {{ isset($input['default']) ? '' : 'disabled' }} name="{{ $input['name'] }}"
                            data-id="{{ $input['id'] }}" autocomplete="off" {{ isset($input['multiple']) ? 'multiple' : '' }}
                            @if (!empty($input['enhanced'])) placeholder="{{ $input['placeholder'] }}" @endif>
                            @if (empty($input['multiple']))
                                <option value="" {{ empty($input['current']) ? 'selected="selected"' : '' }} disabled>{{ $input['placeholder'] }}</option>
                            @endif
                            @if (isset($input['options']))
                                @foreach ($input['options'] as $option)
                                    <option value="{{ $option['value'] }}" {{ !empty($option['selected']) ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    @if (!empty($input['enhanced']))
                        </div>
                    @endif
                </div>
            @endforeach
            @if ($rows)
                <button class="btn btn-danger mt-3" data-remove type="button">Удалить</button>
            @endif
        </template>
        <div data-container>
            @foreach ($inputsGroups as $inputs)
                <div data-row class="mb-3">
                    @foreach ($inputs as $input)
                        <div class="form-group mb-3">
                            @if (isset($input['title']))
                                <label class="form-label">{{ $input['title'] }}</label>
                            @endif
                            @if (!empty($input['enhanced']))
                                <div data-enhanced-select data-controller="select" data-select-allow-add="false" data-select-allow-empty="true"
                                    data-select-message-notfound="Ничего не найдено">
                            @endif
                                <select class="form-control" {{ isset($input['default']) ? '' : 'disabled' }}
                                    name="{{ $input['name'] }}" data-id="{{ $input['id'] }}" autocomplete="off"
                                    {{ isset($input['multiple']) ? 'multiple' : '' }}
                                    @if (!empty($input['enhanced'])) placeholder="{{ $input['placeholder'] }}" @endif
                                    @if (isset($input['current'])) data-current="{{ $input['current'] }}" @endif
                                    @if (!empty($input['hydrated'])) data-server-hydrated @endif>
                                    @if (empty($input['multiple']))
                                        <option value="" {{ empty($input['current']) ? 'selected="selected"' : '' }} disabled>{{ $input['placeholder'] }}</option>
                                    @endif
                                    @if (isset($input['options']))
                                        @foreach ($input['options'] as $option)
                                            <option value="{{ $option['value'] }}" {{ !empty($option['selected']) ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @if (!empty($input['enhanced']))
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @if ($rows)
                        <button class="btn btn-danger mt-3" data-remove type="button">Удалить</button>
                    @endif
                </div>
            @endforeach
        </div>
        @if ($rows)
            <button class="btn btn-primary mt-3" data-add type="button">Добавить</button>
        @endif
    </div>
    <style>
        .form-control[multiple] {
            height: min-content !important;
        }

        .form-control[disabled] {
            opacity: .4 !important;
        }
    </style>
@endcomponent
