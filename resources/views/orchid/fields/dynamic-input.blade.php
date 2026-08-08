@if (isset($title))
    <label for="{{ $id }}" class="form-label">{{ $title }}</label>
@endif

<div id="{{ $id }}-container">
    @if (isset($values) && $values)
        @foreach ($values as $valueKey => $value)
            @php
                $index = $valueKey;
                $fieldName = $valueKey;
                $fieldValue = $value;

                if (isset($useNames) && $useNames && is_array($value)) {
                    $fieldName = $value['name'] ?? '';
                    $fieldValue = $value['value'] ?? '';
                }

                $fieldName = is_scalar($fieldName) ? $fieldName : '';
                $fieldValue = is_scalar($fieldValue) ? $fieldValue : '';
            @endphp
            <div class="row form-group align-items-end" data-field-index="{{ $index }}">
                @if (isset($useNames) && $useNames)
                    @include('orchid.fields.input-with-name', [
                        'name' => $name,
                        'index' => $index,
                        'fieldName' => $fieldName,
                        'fieldValue' => $fieldValue,
                        'lableName' => $lableName,
                        'lableValue' => $lableValue,
                    ])
                @else
                    <div class="col-12 col-md form-group mb-md-0 pe-md-0">
                        <div data-controller="input" data-input-mask="{{ $mask ?? '' }}">
                            <input class="form-control" type="text" name="{{ $name }}[]"
                                value="{{ $fieldValue }}">
                        </div>
                    </div>
                @endif
                <div class="col-12 col-md form-group mb-md-0">
                    <button type="button" class="btn btn-danger"
                        data-field-remove="{{ $index }}">Удалить</button>
                </div>
            </div>
        @endforeach
    @else
        <div class="row form-group align-items-end" data-field-index="0" style="display: none">
            @if (isset($useNames) && $useNames)
                @include('orchid.fields.input-with-name', [
                    'name' => $name,
                    'index' => 0,
                    'fieldName' => '',
                    'fieldValue' => '',
                ])
            @else
                <div class="col-12 col-md form-group mb-md-0 pe-md-0">
                    <div data-controller="input" data-input-mask="{{ $mask ?? '' }}">
                        <input class="form-control" type="text" name="{{ $name }}[]">
                    </div>
                </div>
            @endif
            <div class="col-12 col-md form-group mb-md-0">
                <button type="button" class="btn btn-danger" data-field-remove="0">Удалить</button>
            </div>
        </div>
    @endif
</div>

<div class="col-12 col-md form-group mb-md-0 py-3">
    <button id="{{ $id }}-add" type="button" class="btn btn-primary">Добавить</button>
</div>

<script>
    (() => {
        const worker = {
            name: '{{ $name }}',
            useNames: {{ isset($useNames) ? 1 : 0 }},
            container: document.getElementById('{{ $id }}-container'),
            addButton: document.getElementById('{{ $id }}-add'),
            template: null,

            init() {
                this.template = this.container.querySelector('.form-group').cloneNode(true);
                this.container.addEventListener('click', this.handleClick.bind(this));
                this.addButton.addEventListener('click', this.addNewField.bind(this));
            },

            handleClick(event) {
                if (event.target.dataset.fieldRemove) {
                    this.container.removeChild(this.container.querySelector(
                        `[data-field-index="${event.target.dataset.fieldRemove}"]`));
                }
            },

            getRandomNumber() {
                return Math.round(Math.random() * 1000);
            },

            getNewFieldIndex() {
                let index = -1;
                if (this.container.lastElementChild) {
                    index = +this.container.lastElementChild.dataset.fieldIndex;
                }
                return ++index;
            },

            addNewField() {
                let index = this.getNewFieldIndex();
                const newField = this.template.cloneNode(true);
                newField.dataset.fieldIndex = index;
                newField.setAttribute('style', '');
                const button = newField.querySelector('[data-field-remove]');
                button.dataset.fieldRemove = index;
                this.setInputsAttributes(newField, index);
                this.container.appendChild(newField);
            },

            setInputsAttributes(field, index) {
                if (this.useNames) {
                    const inputElementName = field.querySelector('[data-input-type="name"]');
                    inputElementName.setAttribute('name', `${this.name}[${index}]['name']`);
                    inputElementName.setAttribute('value', '');

                    const inputElementValue = field.querySelector('[data-input-type="value"]');
                    inputElementValue.setAttribute('name', `${this.name}[${index}]['value']`);
                    inputElementValue.setAttribute('value', '');
                } else {
                    const inputElement = field.querySelector('input');
                    inputElement.setAttribute('name', `${this.name}[]`);
                    inputElement.setAttribute('value', '');
                }
            }
        }.init();
    })();
</script>
