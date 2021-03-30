<?php
namespace {$CTRL_NS};

use Semiorbit\Http\{$CTRL_TYPE}Controller;
{IF $DATASET}use {$DATASET_NS};{/IF}

/**
*Controller {$NAME}
*
{IF $DATASET}*@property {$DATASET} $DataSet{/IF}
*@package {$PACKAGE}
*/

class {$CLASS_NAME} extends {$CTRL_TYPE}Controller
{

    {IF $DATASET}
    const DataSet = {$DATASET}::class;

    {/IF}

    public function Index()
    {
        {IF $BASE}
        parent::TableView();
        {/IF}
        {IF $DEFAULT}
        $this->View->Render();
        {/IF}
        {IF $REST}
        $data = [];

        $this->Response->Json($data)->Send();
        {/IF}
    }


}