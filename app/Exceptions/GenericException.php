<?php

namespace App\Exceptions;

use Exception;

class GenericException extends Exception
{
    protected $id;

    protected $status;

    protected $title;

    protected $detail;

    public function __construct()
    {
        $message = $this->build(func_get_args());
        parent::__construct($message);
    }

    protected function build(array $args)
    {
        $this->id = array_shift($args);

        $error = config('errors.'.$this->id);

        $this->status = $error['status'];
        $this->title  = $error['title'];
        $this->detail = $error['details'];
        $this->detail = vsprintf($this->detail, $args);

        return $this->title;
    }

    public function getStatus()
    {
        return (int) $this->status;
    }

    public function toArray()
    {
        return [
            'id'     => $this->id,
            'status' => $this->status,
            'title'  => $this->title,
            'detail' => $this->detail,
        ];
    }
}
