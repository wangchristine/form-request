<?php

namespace CHHW\FormRequest;

use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Http\Request;
use CHHW\FormRequest\FormRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class FormRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRequestValidation();
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving(ValidatesWhenResolved::class, function ($resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function ($request, $app) {
            $request = FormRequest::createFrom($app['request'], $request);

            $request->setContainer($app);
        });
    }

    /**
     * Register the "validate" macro on the request.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registerRequestValidation()
    {
        Request::macro('validate', function (array $rules, ...$params) {
            return validator()->validate($this->all(), $rules, ...$params);
        });

        Request::macro('validateWithBag', function (string $errorBag, array $rules, ...$params) {
            try {
                return $this->validate($rules, ...$params);
            } catch (ValidationException $e) {
                $e->errorBag = $errorBag;

                throw $e;
            }
        });
    }
}
