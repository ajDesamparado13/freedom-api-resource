<?php

namespace Freedom\ApiResource\Criterias;

/**
 * Class WithCriteria.
 *
 * @package namespace App\Criteria;
 */
class HasCriteria extends BaseResourceCriteria
{
    public function getFields() : array {
        return ['*'];
    }

    public function handle($model)
    {
        if($this->shouldSkipCriteria()){
            return $model;
        }
        return $this->newQuery($model);
    }


    protected function newQuery($model){
        return $this->buildQuery($model);
    }

    protected function buildQuery($query){
        foreach($this->inputs as $index => $key){
            $field = is_numeric($index) ? $key : $index;
            if($this->shouldSkipField($field,$key)){
                continue;
            }
            $query = $this->$field($query,$field);
        }
        return $query;
    }

    protected function buildDefaultQuery($query,$field){
        return $query->whereHas($field);
    }

    public function getRequestField() : string {
        return 'has';
    }

    protected function shouldApply($model, $repository): bool
    {
        return $repository->getMethod() != 'count';
    }

}
