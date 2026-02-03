<?php

namespace App\Exceptions\Accounting;

use RuntimeException;
use App\Models\Coa;

class InvalidCoaException extends RuntimeException
{
    protected ?Coa $coa = null;
    protected ?string $expectedType = null;
    protected ?string $action = null;

    public function __construct(
        string $message,
        ?Coa $coa = null,
        ?string $expectedType = null,
        ?string $action = null
    ) {
        parent::__construct($message);

        $this->coa = $coa;
        $this->expectedType = $expectedType;
        $this->action = $action;
    }

    /**
     * Context untuk logging / debugging
     */
    public function context(): array
    {
        return [
            'coa_id'        => $this->coa?->id,
            'coa_code'      => $this->coa?->code,
            'coa_name'      => $this->coa?->name,
            'expected_type' => $this->expectedType,
            'action'        => $this->action,
        ];
    }
}
