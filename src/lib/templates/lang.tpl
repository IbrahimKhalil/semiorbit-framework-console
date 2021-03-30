<?php
return [

    "__index" => "{$INDEX_TITLE}",

    "__edit" => "Edit Item",

    "__create" => "Add Item",

    "__delete" => "Remove Item",


    {LOOP $FIELDS}
    "{$NAME}" => "{$TRANS}",

    {/LOOP}

];