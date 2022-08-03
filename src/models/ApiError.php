<?php
namespace boundstate\mailchimp\models;

use craft\base\Model;

class ApiError extends Model
{
    public ?string $type = null;
    public ?string $title = null;
    public ?int $status = null;
    public ?string $detail = null;
    public ?string $instance = null;
    public ?array $errors = null;

    static function fromApiResult(array $result): ApiError {
        $apiError = new ApiError();
        $apiError->type = $result['type'];
        $apiError->title = $result['title'];
        $apiError->status = $result['status'];
        $apiError->detail = $result['detail'];
        $apiError->instance = $result['instance'];
        $apiError->errors = $result['errors'] ?? [];

        return $apiError;
    }
}
