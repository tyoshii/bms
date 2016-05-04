<?php

class Controller_Mock extends Controller_Base
{
    public function action_index()
    {
        $path = $this->param('path', 'index');
        $view = Theme::instance()->view('mock/'.$path.'.twig');

        return Response::forge($view);
    }
}
