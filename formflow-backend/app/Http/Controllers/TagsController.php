<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TagsService;
use App\Models\Tag;

class TagsController extends Controller {

    public function index() {
        return TagsService::getAllTags();
    }

    public function store(Request $request) {
        return TagsService::save($request->post('name'));
    }

    public function delete(Tag $tag) {
        return TagsService::delete($tag);
    }

    public function update(Tag $tag, Request $request) {
        return TagsService::update($tag, $request->all());
    }
}