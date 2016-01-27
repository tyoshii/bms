<?php

class Controller_Error extends Controller
{
    /**
     * error common function
     */
    public function action_index()
    {
        $code = $this->param('status_code');
        $view = View::forge('errors/index.twig');

        return Response::forge($view, $code);
    }

    /**
     * 404 specify error
     */
    public function action_error404()
    {
        return Response::forge(View::forge('errors/index.twig'), 404);
    }
}
