<?php
namespace boundstate\mailchimp\models;

use craft\base\Model;

class ApiError extends Model
{
    /**
     * @var string|null
     */
    public $type;

    /**
     * @var string|null
     */
    public $title;

    /**
     * @var int|null
     */
    public $status;

    /**
     * @var string|null
     */
    public $detail;

    /**
     * @var string|null
     */
    public $instance;

    /**
     * @var array|null
     */
    public $errors;

    /**
     * @param array $result
     * @return ApiError
     */
    static function fromApiResult($result): ApiError {
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
