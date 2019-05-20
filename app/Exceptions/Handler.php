<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        //Handle Json Responses
        if($request->wantsJson() || $request->is('api/*')) {

            //general fail response format
            $json = [
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ],
            ];
            
            //add errors for validation
            if($e instanceof ValidationException) $json['error']['errors'] = $e->errors();
            if($e instanceof ModelNotFoundException) {
                $json['error'] = [
                    'code' => 404,
                    'message' => __('apiMsg.applicant_not_found'),
                ];
            }

            //return 400 json response
            return response()->json($json, 400); 
        }

        //return render response
        return parent::render($request, $e);
    }
}
