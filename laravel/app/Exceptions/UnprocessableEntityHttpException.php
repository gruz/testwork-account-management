<?php

namespace App\Exceptions;

class UnprocessableEntityHttpException extends \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException
{
    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $statusCode = 400;
        $response = response()->json();
        $response->setStatusCode($statusCode);

        // Laravel validation errors will return JSON string
        $decoded = json_decode($this->getMessage(), true);
        // Message was not valid JSON
        // This occurs when we throw UnprocessableEntityHttpExceptions
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Mimick the structure of Laravel validation errors
            $decoded = [[$this->getMessage()]];
        }

        // Laravel errors are formatted as {"field": [/*errors as strings*/]}
        $data = array_reduce($decoded, function ($carry, $item) {
            return array_merge($carry, array_map(function ($current) {
                return [
                    'status' => $this->getStatusCode(),
                    'code' => $this->getCode(),
                    'title' => 'Validation error',
                    'detail' => $current
                ];
            }, $item));
        }, []);

        $response->setData([
            'errors' => $data
        ]);

        $headers = $this->getHeaders();
        if (empty($headers['Description'])) {
            $headers['Description'] = 'Mandatory body parameters missing or have incorrect type.';
        }

        $response->withHeaders($headers);

        return $response;
    }
}
