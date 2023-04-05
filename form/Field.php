<?php


namespace app\core\form;


use app\core\Model;

class Field{
    public Model $model;
    public string $attribute;

    /**
     * Field constructor.
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString()
    {
       return sprintf('
       <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label d-flex">%s</label>
        <input type="text"  name="%s" id="" value="%s" class="form-control %s">
        <div class="invalid-feedback">%s</div>
        </div>',
           $this->attribute,
           $this->attribute,
           $this->model->{$this->attribute},
           $this->model->hasError($this->attribute)?'is-invalid':'',
           ($this->model->hasError($this->attribute)?$this->model->errors[$this->attribute][0]:'')
       );
    }

}