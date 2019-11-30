<?php
/*
 *------------------------------------------------------------------------------------------------
 * SEMIORBIT FRAMEWORK                                                     framework.semiorbit.com
 *------------------------------------------------------------------------------------------------
 */

namespace {$MODEL_NS};



use Semiorbit\Data\DataSet;
{IF $FIELDS}use Semiorbit\Field\Field;{/IF}
{IF $USE_DATA_TYPE}use Semiorbit\Field\DataType;{/IF}


/**
 * {$CLASS_NAME} model
 *
{LOOP $FIELDS}
 * @property \Semiorbit\Field\{$CONTROL} {$NAME}
{/LOOP}
 */

class {$CLASS_NAME} extends DataSet
{

    const TABLE = "{$TABLE_NAME}";



    public function __construct()
    {


        {LOOP $FIELDS}
        $this->{$NAME} = Field::{$CONTROL}("{$FIELD_NAME}"){$PROPS};  {$COMMENT}

        {/LOOP}
        ## _____________________________________________________________________________________________________________

    }

}