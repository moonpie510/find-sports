<?php

namespace App\Http\Controllers\V1;

use App\Classes\Interval;
use App\Classes\IntervalCollection;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddSlotRequest;
use App\Http\Requests\CreateSlotRequest;
use App\Http\Requests\UpdateSlotRequest;
use App\Http\Resources\BookingResource;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly BookingRepository $bookingRepository,
    )
    {}

    public function index(): JsonResponse
    {
        try {
            $user = $this->userRepository->getByToken(request()->header(User::TOKEN));

            if (!$user) {
                throw new \Exception('Пользователь не найден по такому токену');
            }

            $slots = $this->bookingRepository->getSlotsByUser($user);

            return ApiResponseHelper::success([
                'slots' => BookingResource::collection($slots),
                'user' => $user,
            ]);

        } catch (\Throwable $th) {
            return ApiResponseHelper::error(message: $th->getMessage());
        }
    }

    public function store(CreateSlotRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->getByToken(request()->header(User::TOKEN));

            if (!$user) {
                throw new \Exception('Пользователь не найден по такому токену');
            }

            $this->bookingRepository->createSlots($user, IntervalCollection::create($request->slots));

            return ApiResponseHelper::success();
        } catch (\Throwable $th) {
            return ApiResponseHelper::error(message: $th->getMessage());
        }
    }

    public function update(int $booking, int $slot, UpdateSlotRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->getByToken(request()->header(User::TOKEN));

            if (!$user) {
                throw new \Exception('Пользователь не найден по такому токену');
            }

            $booking = $this->bookingRepository->getBookingById($booking, $user->id);

            if (!$booking) {
                throw new \Exception('Заказ не найден или принадлежит другому пользователю');
            }

            $slot = $this->bookingRepository->getSlotById($slot, $booking->id);

            if (!$slot) {
                throw new \Exception('Слот не найден');
            }

            $this->bookingRepository->updateSlot($slot, Interval::create($request->start_time, $request->end_time));

            return ApiResponseHelper::success(message: 'Слот успешно обновлен');
        } catch (\Throwable $th) {
            return ApiResponseHelper::error(message: $th->getMessage());
        }
    }

    public function add(int $booking, AddSlotRequest $request): JsonResponse
    {
        try {
            $user = $this->userRepository->getByToken(request()->header(User::TOKEN));

            if (!$user) {
                throw new \Exception('Пользователь не найден по такому токену');
            }

            $booking = $this->bookingRepository->getBookingById($booking, $user->id);

            if (!$booking) {
                throw new \Exception('Заказ не найден или принадлежит другому пользователю');
            }

            $this->bookingRepository->addSlot($booking, Interval::create($request->start_time, $request->end_time));

            return ApiResponseHelper::success(message: 'Слот успешно добавлен');
        } catch (\Throwable $th) {
            return ApiResponseHelper::error(message: $th->getMessage());
        }
    }

    public function delete(int $booking): JsonResponse
    {
        try {
            $user = $this->userRepository->getByToken(request()->header(User::TOKEN));

            if (!$user) {
                throw new \Exception('Пользователь не найден по такому токену');
            }

            $booking = $this->bookingRepository->getBookingById($booking, $user->id);

            if (!$booking) {
                throw new \Exception('Заказ не найден или принадлежит другому пользователю');
            }

            $this->bookingRepository->deleteBooking($booking);

            return ApiResponseHelper::success(message: 'Заказ успешно удален');
        } catch (\Throwable $th) {
            return ApiResponseHelper::error(message: $th->getMessage());
        }
    }
}
