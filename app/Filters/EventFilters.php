<?php

namespace App\Filters;

class EventFilters extends QueryFilter
{
    public function search($text)
    {
        return $this->builder->where('title', 'like', "%$text%");
    }
}
