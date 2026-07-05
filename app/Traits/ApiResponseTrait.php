<?php

namespace App\Traits;

use Illuminate\Validation\Validator;

trait ApiResponseTrait
{
    public function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message ?? __('Success'),
            'data' => $data,
        ], $code);
    }

    public function successWithoutData($message = null, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], $code);
    }

    public function error($message = null, $code = 422, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public function validationToError(Validator $validator, $code = 422)
    {
        $errors = makeValidationException($validator->errors()->all());

        return $this->error($errors->getMessage(), $code, $errors->errors());
    }
}
