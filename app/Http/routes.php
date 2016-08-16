<?php

use Illuminate\Http\Request;

Route::group(
    ['middleware' => ['web']], function () {
        Route::get(
            '/', function () {
                return 'Employee Directory Laravel';
            }
        );
        Route::post(
            '/directory/search/',
            ['uses' => 'DirectoryController@search',
            'as' => 'directory.search']
        );
    }
);
