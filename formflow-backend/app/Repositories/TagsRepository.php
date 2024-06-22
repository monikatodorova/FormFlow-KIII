<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Tag;

class TagsRepository {

    /**
     * @param User $user
     * @return mixed
     */
    public static function getAllTags(User $user) {
        return $user->tags;
    }

    /**
     * @param User $user
     * @param string $tagName
     * @return mixed
     */
    public static function save(User $user, string $tagName) {
        return Tag::create([
            'name' => $tagName,
            'user_id' => $user->getId(),
        ]);
    }

    /**
     * @param Tag $tag
     * @return void
     */
    public static function delete(Tag $tag) {
        $tag->delete();
    }

    /**
     * @param Tag $tag
     * @param string $attribute
     * @param $value
     * @return void
     */
    public static function updateAttribute(Tag $tag, string $attribute, $value) {
        $tag->setAttribute($attribute, $value);
        $tag->save();
    }

    /**
     * @param Tag $tag
     * @return Tag
     */
    public static function details(Tag $tag) {
        return Tag::query()->where('id', '=', $tag->id)->first();
    }

    /**
     * Returns the tag with the provided ID that is associated to this user.
     * @param $tagId
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getTagByIdAndUser($tagId, User $user) {
        return Tag::query()->where('id', '=', $tagId)->where('user_id', '=', $user->getId())->first();
    }
}

?>