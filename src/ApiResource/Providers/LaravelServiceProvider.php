<?php

namespace Freedom\ApiResource\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Freedom\ApiResource\Contracts\JsonResourceInterface;
use Freedom\ApiResource\Middleware\ParseResourceRequest;
use Freedom\ApiResource\Resources\JsonResource;

class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMiddlewares();
        $this->loadResponseMacro();
        $this->app->bind(JsonResourceInterface::class,JsonResource::class);
    }

    protected function loadMiddlewares()
    {
        $this->app['router']->aliasMiddleware('resource.parse-request',ParseResourceRequest::class);

    }

    protected function loadResponseMacro()
    {
        Response::macro('resource',function($data,JsonResourceInterface $transformer, array $response=[]){
            $transformer->resource = $data;
            if( is_a($data,'Illuminate\Pagination\LengthAwarePaginator') || is_a($data,'Illuminate\Database\Eloquent\Collection')){
                return $transformer->collection($data)->additional($response);
            }

            if (is_a($data,'Illuminate\Database\Eloquent\Model')){
                return $transformer->make($data)->additional($response);
            }

            $response['data'] = $data;
            return Response::json($response,200);
        });

        Response::macro('unauthorized', function ($message="Not Authenticated") {
            $message = !is_array($message) ? [$message] : $message;
            $response = array_merge(['type' => 'error'],$message);

            return Response::json([
                'error' => $response
            ], 401);
        });

        Response::macro('error', function ($message, $status = 500) {
            $message = !is_array($message) ? [$message] : $message;
            $response = array_merge(['message' => 'Server Error','type' => 'error'],$message);

            return Response::json([
                'error'  => $response
            ], $status);
        });

    }


}
