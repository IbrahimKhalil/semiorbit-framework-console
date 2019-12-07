<?php


namespace SemiorbitFwkLibrary;


use Semiorbit\Field\FieldBuilder;

class ModelCustomFieldDefiner
{

    protected $_Control;

    protected $_Props = [];

    protected $_Comment;

    protected $_PropDef;

    protected $Namespace;

    protected $DefineFieldName = true;

    /**
     * @param mixed $control
     * @return ModelCustomFieldDefiner
     */
    public function setControl($control)
    {
        $this->_Control = $control;
        return $this;
    }

    /**
     * @return mixed
     */
    public function Control()
    {
        return $this->_Control;
    }

    /**
     * @param mixed $props
     * @return ModelCustomFieldDefiner
     */
    public function setProps(...$props)
    {
        $this->_Props = $props;
        return $this;
    }

    /**
     * @return mixed
     */
    public function Props()
    {
        return $this->_Props;
    }

    /**
     * @param mixed $comment
     * @return ModelCustomFieldDefiner
     */
    public function setComment($comment)
    {
        $this->_Comment = $comment;
        return $this;
    }

    /**
     * @return mixed
     */
    public function Comment()
    {
        return $this->_Comment;
    }


    /**
     * @param mixed $prop_def
     * @return ModelCustomFieldDefiner
     */
    public function setPropDef($prop_def)
    {
        $this->_PropDef = $prop_def;
        return $this;
    }

    /**
     * @return mixed
     */
    public function PropDef()
    {
        return $this->_PropDef;
    }

    /**
     * @param mixed $namespace
     * @return ModelCustomFieldDefiner
     */
    public function setNamespace($namespace)
    {
        $this->Namespace = $namespace;
        return $this;
    }

    /**
     * @return mixed
     */
    public function Namespace()
    {
        return $this->Namespace;
    }

    /**
     * @param bool $define_field_name
     * @return ModelCustomFieldDefiner
     */
    public function setDefineFieldName(bool $define_field_name = true)
    {
        $this->DefineFieldName = $define_field_name;
        return $this;
    }

    /**
     * @return bool
     */
    public function DefineFieldName(): bool
    {
        return $this->DefineFieldName;
    }

    public function IsCustom()
    {
        return !((!$this->Control()) ?: method_exists(FieldBuilder::class, $this->Control()));
    }


}