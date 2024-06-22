<?php

namespace App\Services;

use Illuminate\Http\Response;
use App\Repositories\TagsRepository;
use App\Repositories\UsersRepository;
use App\Models\Tag;

class TagsService {

    /**
     * @return Response
     */
    public static function getAllTags() {
        return new Response([
            'status' => 'success',
            'tags' => TagsRepository::getAllTags(UsersRepository::getCurrentUser()),
        ], 200);
    }

    /**
     * @param string $tagName
     * @return Response
     */
    public static function save(string $tagName) {
        if(!$tagName) {
            return new Response([
                'status' => 'error',
                'message' => 'Please enter a tag to save it.',
            ], 401);
        }

        $tagName = self::prepareTag($tagName);
        if (strlen($tagName) < 3 || strlen($tagName) > 25) {
            return new Response([
                'status' => 'error',
                'message' => 'The tag must be between 3 and 25 characters. Special symbols are not allowed.',
            ], 401);
        }

        return new Response([
            'status' => 'success',
            'tag' => TagsRepository::save(UsersRepository::getCurrentUser(), $tagName),
        ]);
    }

    /**
     * Clears the tag from any non-characters and duplicate spaces.
     * @param $tagName
     * @return string
     */
    private static function prepareTag($tagName) {
        $tagName = preg_replace('/[^a-zA-Z0-9\s]/i', '', $tagName);
        $tagName = preg_replace('/\s+/', ' ', $tagName);
        $tagName = trim($tagName);
        $tagName = ucwords($tagName);
        return $tagName;
    }

    /**
     * @param Tag $tag
     * @return Response
     */
    public static function delete(Tag $tag) {
        TagsRepository::delete($tag);

        return Response([
            'status' => 'success',
            'message' => 'Tag has been deleted',
        ], 200);
    }

    /**
     * @param Tag $tag
     * @param $details
     * @return Response
     */
    public static function update(Tag $tag, $details) {
        if(isset($details['name'])) TagsRepository::updateAttribute($tag, 'name', $details['name']);
        
        return new Response([
            'status' => 'success',
            'tag' => TagsRepository::details($tag),
        ]);
    }

    /**
     * Saves the default Demo tags for the current user.
     * @return void
     */
    public static function createDemoTags($user) {
        $demoTags = [
            "General Inquiry",
            "Feedback",
            "Support Request",
            "Bug Report",
            "Feature Request",
            "Customer Satisfaction",
            "Product Question",
            "Account Issue",
            "Event Registration",
            "Survey Response",
            "Application Error",
            "Marketing Interest",
            "Partnership Inquiry",
            "Training Request",
            "Billing Concern",
            "Refund Request",
            "Product Demo Request",
            "Job Application",
            "Technical Support",
            "Subscription Inquiry",
        ];
        foreach($demoTags as $tag) {
            TagsRepository::save($user, $tag);
        }
    }

}

?>