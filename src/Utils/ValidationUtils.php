<?php

namespace App\Utils;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationUtils
{
    public static function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $formattedErrors = [];

        foreach ($errors as $error) {
            $formattedErrors[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        return $formattedErrors;
    }

    public static function createValidationErrorArray(string $property, string $message, ?int $id = null): array
    {
        $error =  [
            'property' => $property,
            'message' => $message,
        ];

        if (!is_null($id)) {
            $error['id'] = $id;
        }

        return $error;
    }
}