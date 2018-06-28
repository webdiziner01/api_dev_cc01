<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTopicRequest;

use App\Topic;
use App\Post;
use App\Transformers\TopicTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;


class TopicController extends Controller
{


	public function index(){
		$topics = Topic::orderBy('created_at', 'latest')->paginate(3);
		$topicsCollection = $topics->getCollection();


			return fractal()->collection($topicsCollection)->parseIncludes(['user'])
			->transformWith(new TopicTransformer)
			->paginateWith(new IlluminatePaginatorAdapter($topics))
			->toArray();
	}
    
	public function store(StoreTopicRequest $request){


		$topic = new Topic;
		$topic->title = $request->title;
		$topic->user()->associate($request->user());


		$post = new Post;
		$post->body = $request->body;
		$post->user()->associate($request->user());

		$topic->save();
		$topic->posts()->save($post);

		return fractal()->parseIncludes(['user','posts','posts.user'])->item($topic)->transformWith(new TopicTransformer)->toArray();


	}

}