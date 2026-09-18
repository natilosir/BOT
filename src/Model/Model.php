<?php

namespace natilosir\bot\Model;

use Exception;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel {
    public function __construct( array $attributes = [] ) {
        try {
            Database::boot();
        } catch ( Exception $e ) {
            dd($e);
        }

        parent::__construct($attributes);
    }

    public function lg(): static {
        lg([
            'model' => static::class,
            'table' => $this->getTable(),
            'data'  => $this->attributesToArray(),
        ]);
        return $this;
    }

    public function log(): static {
        return $this->lg();
    }
}
