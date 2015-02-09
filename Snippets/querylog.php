<?php // Place this in e.g. app/start/global.php

/*
|--------------------------------------------------------------------------
| Database logging
|--------------------------------------------------------------------------
|
| Set up database logging if it has been enabled in the config file
|
*/

if (Config::get('database.log', false))
{           
    Event::listen('illuminate.query', function($query, $bindings, $time, $name)
    {
        $data = compact('bindings', 'time', 'name');

        // Format binding data for sql insertion
        foreach ($bindings as $i => $binding)
        {   
            if ($binding instanceof \DateTime)
            {   
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            }
            else if (is_string($binding))
            {   
                $bindings[$i] = "'$binding'";
            }   
        }       

        // Insert bindings into query
        $query = str_replace(array('%', '?'), array('%%', '%s'), $query);
        $query = vsprintf($query, $bindings);
        $query = Request::path() . ' ' . $query;

        Log::info($query, $data);
    });
}