<?php

namespace Freedom\ApiResource\Traits;
use Freedom\ApiResource\Exceptions\ApiControllerException;
use Freedom\ApiResource\Contracts\JsonResourceInterface;

trait HasTransformer {

    /**
     * The Transformer instance
     *
     * @var \App\Http\Resources\Contracts\JsonResourceInterface;
     */
    protected $transformer;

    public function makeTransformer(){
        $transformer = $this->transformer();

        if(is_string($transformer)){
            $transformer = app()->make($transformer);
        }

        if (!( $transformer instanceof JsonResourceInterface) ) {
            throw new ApiControllerException("Class ". get_class($transformer) . " must be an instance of ". JsonResourceInterface::class);
        }

        return $this->transformer = $transformer;
    }

    protected function hasTransformer() : bool
    {
        return !empty($this->transformer) && $this->transformer instanceof JsonResourceInterface;
    }

    /*
    * Set the Controller and IoC binding for ResourceInterface
    *
    * @return void
    */
    protected function setTransformer(JsonResourceInterface $transformer)
    {
        app()->bind(JsonResourceInterface::class,$transformer);
        $this->transformer = $transformer;
    }

    protected function getTransformer() {
        return empty($this->transformer) ? $this->makeTransformer() : $this->transformer ;
    }

    protected function getTransformerColumns() { 
        $transformer = $this->getTransformer();
        return property_exists($transformer,'columns') ? $transformer->columns : ['*'];
    }

    abstract public function transformer();
}

