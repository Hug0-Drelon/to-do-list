<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorsHandler {

    private $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    public function sendValidationErrors(ConstraintViolationListInterface $violations)
    {
        $errorsArray = [
            'error' => [
                'code' => 422,
                'message' => 'Unprocessable entity',
                'detail' => [],
            ]
        ];
        
        foreach ($violations as $violation) {
            $errorsArray['error']['list'][] = [$violation->getPropertypath() => $violation->getMessage()];
        }

        $this->response
            ->setContent(json_encode($errorsArray))
            ->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->send();
    }
}