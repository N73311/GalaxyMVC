<?php

class Home extends Controller
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = $this->getModelDefinition('Post');

    }

    public function index()
    {

        $postsData = $this->postModel->getPosts();
        $this->getViewDefinition('index', true, $postsData);


    }

    public function about()
    {
        $this->getViewDefinition('about', true, ['title' => 'Welcome']);
    }

}
