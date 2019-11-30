<?php

{LOOP $SERVICES}
use {$SERVICE_NS};
{/LOOP}

return [

    {LOOP $SERVICES}
    {$SERVICE}::class,

    {/LOOP}
];