<?php
namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class BaseFilter
{
    /**
    *   @param null|string $cookie          -- Имя кук, если есть записываются при фильтрации;
    *   @param string $label                -- Лэйбл используется для заголовка фильтра;
    *   @param string $name                 -- Имя фильтра соответствовует имени view шаблона, а также имени переменной в запросе;
    *   @param string $field                -- Поле в таблице, которое соответствующее фильтру;
    *   @param array $attributes            -- HTML атрибуты;
    *   @param string|null $related         -- Связь основной таблицы фильтра с другой (имя связанной таблицы);
    */

    protected string $label;
    protected string $name;
    protected string $field;
    protected array $attributes;
    protected array $request;

    public function __construct(
        string $name,
        string $label,
        string $field,
        array $attributes = [],
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->field = $field;
        $this->attributes = $attributes;
        $this->request = app(Request::class)->all();
    }

    // Получить заголовок фильтра
    public function getLabel(): string
    {
        return $this->label;
    }

    // Получить имя фильтра
    public function getName(): string
    {
        return $this->name;
    }

    // Получить HTML атрибут по названию
    public function getAttribute(string $name): string
    {
        if (isset($this->attributes[$name])) return $this->attributes[$name];
        else return '';
    }

    // Отрисовка фильтра
    public function render(int|null $cityID = null)
    {
        return view('filters.' . $this->getName(), ['filter' => $this, 'request' => $this->request, 'city_id' => $cityID]);
    }

    // Ответ сервера в формате tex/html. Возвращается вёрстка.
    public function responseRender(array|int|null $params = null): Response
    {
        $request = app(Request::class)->all();
        $filter = $this;
        $view = 'filters.response.' . $this->getName();
        return response()->view($view, compact('filter', 'params', 'request'));
    }

    // Фильтрация
    public function apply(Builder $query): Builder
    {
        $value = $this->request[$this->name] ?? false;
        if (is_string($value)) return $query->where($this->field, $value);
        if (is_array($value)) return $query->whereIn($this->field, $value);
        return $query;
    }
}

