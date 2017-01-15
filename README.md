## Webisan ##

Webisan is a simple Web Interface for Laravel Artisan yet pretty complete.
Webisan is based on Bestmomo work with NiceArtisan, adding some key features:
- Auto updates available commands (using Artisan command)
- Has a built-in feature to hide some commands from the interface (link the DOWN command.)
- Has an easier way to customize routes
Bestmomo is the guy who really started this, without is project, this didn't happen. Cheers to him!

### Installation ###

Run: 
```
    composer require marcop93/webisan
```

The next required step is to add the service provider to config/app.php :
```
    Marcop93\Webisan\WebisanServiceProvider::class,
```


Now it must work with this url :
```
    /webisan
```

### Security ###

If you want to use this package on a production application you should have your own routes defined.

Next are the default routes, you should edit as you want, specially to apply some security to them.

Remember: Webisan for itself is BIG security breach, it's your job to protect your app on your own!
```
Route::group(['prefix' => '/webisan'], function () {
    Route::get('/settings', '\Marcop93\Webisan\WebisanController@settings');
    Route::post('/settings', '\Marcop93\Webisan\WebisanController@settingsSave');
    Route::post('/command/{class}', '\Marcop93\Webisan\WebisanController@run');
    Route::get('/{option?}/{search?}', '\Marcop93\Webisan\WebisanController@show');
    Route::post('/search', '\Marcop93\Webisan\WebisanController@search');
});
```

After setting your own routes, you should go to Settings page and set "Use custom routes".