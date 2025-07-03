<?php

namespace App\Swagger\Controllers;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0', title: 'Booking')]
class BookingController extends Controller
{

    #[OA\Get(
        path: '/api/v1/bookings',
        summary: 'Список бронирований пользователя',
        tags: ['Bookings'],
        parameters: [
            new OA\Parameter(
                name: 'User-Token',
                description: 'Токен пользователя для аутентификации',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список бронирований пользователя',
                content: [
                    new OA\JsonContent(properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Запрос прошел успешно'),
                        new OA\Property(property: 'data', properties: [new OA\Property(property: 'slots', type: "array", items: new OA\Items(ref: "#/components/schemas/BookingResource", type: "object"))], type: 'object'),
                        new OA\Property(property: 'user', properties: [new OA\Property(property: 'id', type: 'integer', example: 1), new OA\Property(property: 'name', type: 'string', example: 'Иванов Иван')], type: 'object',),
                    ])
                ]
            )
        ]
    )]
    public function index(): void
    {}

    #[OA\Post(
        path: '/api/v1/bookings',
        summary: 'Создать бронирование с несколькими слотами',
        requestBody: new OA\RequestBody(
            content: [
                new OA\JsonContent(ref: '#/components/schemas/CreateSlotRequest')
            ]
        ),
        tags: ['Bookings'],
        parameters: [
            new OA\Parameter(
                name: 'User-Token',
                description: 'Токен пользователя для аутентификации',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Список бронирований пользователя',
                content: [
                    new OA\JsonContent(properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Запрос прошел успешно'),
                        new OA\Property(property: 'data', default: []),
                    ])
                ]
            )
        ]
    )]
    public function store(): void
    {}

    #[OA\Patch(
        path: '/api/v1/bookings/{booking}/slots/{slot}',
        summary: 'Обновить конкретный слот',
        requestBody: new OA\RequestBody(
            content: [
                new OA\JsonContent(ref: '#/components/schemas/UpdateSlotRequest')
            ]
        ),
        tags: ['Bookings'],
        parameters: [
            new OA\Parameter(
                name: 'User-Token',
                description: 'Токен пользователя для аутентификации',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(name: 'booking', description: 'ID бронирования', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'slot', description: 'ID слота', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Обновить конкретный слот',
                content: [
                    new OA\JsonContent(properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Слот успешно обновлен'),
                        new OA\Property(property: 'data', default: []),
                    ])
                ]
            )
        ]
    )]
    public function update(): void
    {}

    #[OA\Post(
        path: '/api/v1/bookings/{booking}/slots}',
        summary: 'Добавить новый слот к существующему заказу',
        requestBody: new OA\RequestBody(
            content: [
                new OA\JsonContent(ref: '#/components/schemas/UpdateSlotRequest')
            ]
        ),
        tags: ['Bookings'],
        parameters: [
            new OA\Parameter(
                name: 'User-Token',
                description: 'Токен пользователя для аутентификации',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(name: 'booking', description: 'ID бронирования', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\JsonContent(ref: '#/components/schemas/AddSlotRequest')
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Добавить новый слот к существующему заказу',
                content: [
                    new OA\JsonContent(properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Слот успешно добавлен'),
                        new OA\Property(property: 'data', default: []),
                    ])
                ]
            )
        ]
    )]
    public function add(): void
    {}

    #[OA\Delete(
        path: '/api/v1/bookings/{booking}}',
        summary: 'Удалить весь заказ',
        tags: ['Bookings'],
        parameters: [
            new OA\Parameter(
                name: 'User-Token',
                description: 'Токен пользователя для аутентификации',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(name: 'booking', description: 'ID бронирования', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Удалить весь заказ',
                content: [
                    new OA\JsonContent(properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Заказ успешно удален'),
                        new OA\Property(property: 'data', default: []),
                    ])
                ]
            )
        ]
    )]
    public function delete(): void
    {}
}
