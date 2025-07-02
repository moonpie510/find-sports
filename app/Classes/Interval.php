<?php

namespace App\Classes;

use Illuminate\Support\Carbon;

/**
 * Класс для работы с датой.
 */
class Interval
{
    public Carbon $start;
    public Carbon $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        if ($start->gt($end)) {
            throw new \Exception('Время начала не может быть больше времени конца');
        }

        if ($start->eq($end)) {
            throw new \Exception('Время начала не может быть равно времени конца');
        }

        if ($start->lt(Carbon::now())) {
            throw new \Exception('Время начала не может быть меньше текущего времени');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Создать новый интервал.
     *
     * Проверяет, что время начала и конца указаны в верном диапазоне.
     */
    public static function create(string $start, string $end): static
    {
        return new static(Carbon::create($start), Carbon::create($end));
    }
}
