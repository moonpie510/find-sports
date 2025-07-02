<?php

namespace App\Classes;


/**
 * Класс для работы с коллекцией интервалов.
 */
class IntervalCollection
{
    public array $intervals;

    public function __construct(array $intervals, string $startKey, string $endKey)
    {
        $intervalCollection = [];

        foreach ($intervals as $interval) {
            $intervalCollection[] = Interval::create($interval[$startKey], $interval[$endKey]);
        }

        $this->intervals = $intervalCollection;
    }

    /**
     * Создать массив интервалов (объектов Interval) из массива.
     *
     * @param array $intervals массив интервалов.
     * @param string $startKey ключ даты начала.
     * @param string $endKey ключ даты конца.
     */
    public static function create(array $intervals, string $startKey = 'start_time', string $endKey = 'end_time'): static
    {
        return new static($intervals, $startKey, $endKey);
    }
}
