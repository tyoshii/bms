<?php

class Controller_Auth extends Controller
{
    /**
     * oauth index.
     */
    public function action_oauth($provider = null)
    {
        // bail out if we don't have an OAuth provider to call
        if ($provider === null) {
            Log::error('login-no-provider-specified');

            return \Response::redirect_back();
        }

        // load Opauth, it will load the provider strategy and redirect to the provider
        \Auth_Opauth::forge();
    }

    /**
     * logout.
     */
    public function action_logout()
    {
        // remove the remember-me cookie, we logged-out on purpose
        \Auth::dont_remember_me();

        // logout
        \Auth::logout();

        // and go back to where you came from (or the application
        // homepage if no previous page can be determined)
        \Response::redirect_back();
    }

    /**
     * callback from OpenID.
     */
    public function action_callback()
    {
        // Opauth can throw all kinds of nasty bits, so be prepared
        try {
            // get the Opauth object
            $opauth = \Auth_Opauth::forge(false);

            // and process the callback
            $status = $opauth->login_or_register();

            // fetch the provider name from the opauth response so we can display a message
            $provider = $opauth->get('auth.provider', '?');

            // deal with the result of the callback process
            switch ($status) {
                // a local user was logged-in, the provider has been linked to this user
                case 'linked':
                    // inform the user the link was succesfully made
                    // and set the redirect url for this status

                    $url = Common::get_url_redirect_after_login();
                    Session::set_flash('info', 'ログインに成功しました。');
                break;

                // the provider was known and linked, the linked account as logged-in
                case 'logged_in':
                    // inform the user the login using the provider was succesful
                    // and set the redirect url for this status

                    $url = Common::get_url_redirect_after_login();
                    Session::set_flash('info', 'ログインに成功しました。');
                break;

                // we don't know this provider login, ask the user to create a local account first
                case 'register':
                    // inform the user the login using the provider was succesful, but we need a local account to continue
                    // and set the redirect url for this status
                    $url = 'auth/register';
                break;

                // we didn't know this provider login, but enough info was returned to auto-register the user
                case 'registered':
                    // inform the user the login using the provider was succesful, and we created a local account
                    // and set the redirect url for this status
                    $url = '/';
                break;

                default:
                    throw new \FuelException('Auth_Opauth::login_or_register() has come up with a result that we dont know how to handle.');
            }

            // redirect to the url set
            return \Response::redirect($url);
        }

        // deal with Opauth exceptions
        catch (\OpauthException $e) {
            Log::error($e->getMessage());
            \Response::redirect_back();
        }

        // catch a user cancelling the authentication attempt (some providers allow that)
        catch (\OpauthCancelException $e) {
            // you should probably do something a bit more clean here...
            exit('It looks like you canceled your authorisation.'.\Html::anchor('users/oath/'.$provider, 'Click here').' to try again.');
        }
    }

    /**
     * register user for oauth.
     */
    public function action_register()
    {
        // get the auth-strategy data from the session (created by the callback)
        $user_hash = \Session::get('auth-strategy.user', array());

        $fullname = \Arr::get($user_hash, 'name');
        $email = \Arr::get($user_hash, 'email');

        // 既にメールアドレスが登録されている場合
        // リクエストのあったProviderでもログイン出来るようにするかどうかをさせる
        if (Model_User::find_by_email($email)) {
            $authentication = \Session::get('auth-strategy.authentication', array());
            $provider = $authentication['provider'];

            $crypt = Crypt::encode($provider."\t".$email);

            return Response::redirect('/auth/multiple_confirm?c='.$crypt);
        }

        if ($user_id = Model_User::regist($fullname, $email)) {
            $this->link_provider($user_id);

            // login
            Auth::force_login($user_id);

            // success
            Session::set_flash('info', 'ユーザー登録が完了しました');
        }

        return Response::redirect('/');
    }

    /**
     * 複数のOpenID認証をひもづけるかどうかの確認画面.
     */
    public function action_multiple_confirm()
    {
        // crypt encode
        try {
            $crypt = Input::param('c');
            list($provider, $email) = explode("\t", Crypt::decode($crypt));
        } catch (Exception $e) {
            Session::set_flash('error', '不正なURLです。');

            return Response::redirect('/');
        }

        // 連携する
        if (Input::method() === 'POST') {
            if (Input::post('email') !== $email) {
                Session::set_flash('error', '不正なリクエストです。');

                return Response::redirect('/');
            }

            $user_id = Model_User::find_by_email($email)->id;

            $this->link_provider($user_id);

            // login
            Auth::force_login($user_id);

            // success
            Session::set_flash('info', $provider.' IDとの連携が完了しました。');

            return Response::redirect('/');
        }

        $view = View::forge('auth/multiple_confirm.twig');
        $view->crypt = $crypt;
        $view->provider = $provider;
        $view->email = $email;

        return Response::forge($view);
    }

    /**
     * link_provider.
     *
     * @param string user_id
     */
    protected function link_provider($userid)
    {
        // do we have an auth strategy to match?
        if ($authentication = \Session::get('auth-strategy.authentication', array())) {
            // don't forget to pass false, we need an object instance, not a strategy call
            $opauth = \Auth_Opauth::forge(false);

            // call Opauth to link the provider login with the local user
            $insert_id = $opauth->link_provider(array(
                'parent_id' => $userid,
                'provider' => $authentication['provider'],
                'uid' => $authentication['uid'],
                'access_token' => $authentication['access_token'],
                'secret' => $authentication['secret'],
                'refresh_token' => $authentication['refresh_token'],
                'expires' => $authentication['expires'],
                'created_at' => time(),
            ));
        }
    }
}
