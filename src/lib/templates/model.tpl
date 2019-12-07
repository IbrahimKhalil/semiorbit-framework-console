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
{LOOP $FIELDS}
{IF $NS}use {$NS};{/IF}
{/LOOP}

/**
 * {$CLASS_NAME} model
 *
{LOOP $FIELDS}
{IF $BASIC} * @property \Semiorbit\Field\{$CONTROL} {$NAME}{/IF}
{IF $PROP_DEF} * @property {$PROP_DEF} {$NAME}{/IF}
{/LOOP}
 */

class {$CLASS_NAME} extends DataSet
{

    const TABLE = "{$TABLE_NAME}";



    public function __construct()
    {


        {LOOP $FIELDS}
        $this->{$NAME} = {IF $BASIC}Field::{/IF}{$CONTROL}({IF $DEFINE_FIELD_NAME}"{$FIELD_NAME}"{/IF}){$PROPS};  {$COMMENT}

        {/LOOP}
        ## _____________________________________________________________________________________________________________

    }

}