<?php

namespace App\Repositories;

use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    /**
     * Формирует query.
     */
    private function getUserQuery(
        ?string $token = null
    ): Builder
    {
        $query = User::query();

        if ($token !== null) {
            $query->where('api_token', $token);
        }

        return $query;
    }

    /**
     * Получить пользователя по токену.
     */
    public function getByToken(string $token): ?User
    {
        return $this->getUserQuery(token: $token)->first();
    }
}
