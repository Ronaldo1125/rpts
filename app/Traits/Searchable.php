<?php

namespace App\Traits;

trait Searchable
{
    /**
     * Scope a query to search across the model's searchable fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        $searchableFields = $this->searchable ?? [];

        if (empty($searchableFields)) {
            return $query;
        }

        return $query->where(function ($q) use ($term, $searchableFields) {
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
        });
    }
}
