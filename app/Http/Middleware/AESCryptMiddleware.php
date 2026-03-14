<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

class AESCryptMiddleware
{
    private $app;
    protected $secret = '599bed6acc1f2e2a2751321fc671e213';
    private static $_encrypt = true;
    protected $except = [];

    public function __construct(Application $app)
    {
        $this->app = $app;

        if (isset(getallheaders()['Accept-C']) && getallheaders()['Accept-C'] ==="false"){
            static::$_encrypt = false;
        }else{
            static::$_encrypt = true;
        }

    }

    public static function getLibrary()
    {
        $plugins = array(
            app_path('lib/aes/Legierski/AES/AES.php'),
        );
        foreach ($plugins AS $plugin) {
            if (!is_file($plugin)) {
                die("NO FILE: " . $plugin);
            }
            require_once $plugin;
        }
        return true;
    }


    /**
     * @throws \JsonException
     */
    public function handle($request, Closure $next)
    {

        $newRequest = $request;

        if($request->ajax() && self::$_encrypt === true) {
            if ($request->has('encrypt') || $request->has('encryptParams')) {
                $params = $request->query->all();
                $content = $request->getContent();

                if($request->has('encrypt')){
                    $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
                    $content = $this->myDecrypt($content['encrypt']);
                }

                if($request->has('encryptParams')){
                    $params = json_decode($this->myDecrypt($params['encryptParams']), true, 512, JSON_THROW_ON_ERROR);
                }


                $baseRequest = new SymfonyRequest();
                $baseRequest->initialize(
                    $params,
                    $request->request->all(),
                    $request->attributes->all(),
                    $request->cookies->all(),
                    $request->files->all(),
                    $request->server->all(),
                    $content
                );
                $newRequest = Request::createFromBase($baseRequest);
                $this->app->instance(Request::class, $newRequest);
            }

            $response = $next($newRequest);


            return $this->myEncrypted($response);

        }

        return $next($newRequest);
    }


    public  function myDecrypt($value)
    {
        self::getLibrary();
        $aes = new \Legierski\AES\AES();
        $hash = $this->secret;

        $decode = $aes->decrypt($value, $hash);
        if ($decode === false) {
            return new JsonResponse(['error' => 'Invalid data encoding.'], 415);
        }
        return $decode;
    }


    public function myEncrypted($response)
    {

        if ($response->getStatusCode() === 200){
            if($response->getContent()){
                $content = $response->content();
                $response->headers->set('Content-Length', strlen($content));

                self::getLibrary();
                $aes = new \Legierski\AES\AES();
                $hash = $this->secret;

                $encryptor = $aes->encrypt($content, $hash);
                if ($encryptor === false) {
                    return new JsonResponse(['error' => 'Invalid data encoding.'], 415);
                }
                $response->setContent($encryptor);
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Content-Length', strlen($encryptor));
            }
        }


        return $response;
    }
}
