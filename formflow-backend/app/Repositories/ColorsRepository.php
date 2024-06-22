<?php

namespace App\Repositories;

use App\Models\Color;

class ColorsRepository {

    /**
     * Returns the color with the provided id, or generates & returns random color when invalid or missing id.
     * @param $colorId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getColor($colorId) {
        if(!$colorId) {
            return self::getRandomColor();
        }
        $color = Color::query()->where('id', '=', $colorId)->first();
        if(!$color) {
            $color = self::getRandomColor();
        }
        return $color;
    }

    /**
     * Returns random color from the system.
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getRandomColor() {
        return Color::query()->inRandomOrder()->first();
    }

}
