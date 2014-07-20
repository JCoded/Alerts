<?php namespace JCoded\Alerts;

use Illuminate\Support\ServiceProvider;

/**
 * Description of AlertsServiceProvider
 *
 * @author James Smith © Copyright 2014
 */
class AlertsServiceProvider extends ServiceProvider 
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('jcoded/alerts');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register config file.
		$this->app['config']->package('jcoded/alerts', __DIR__.'/../../config');
		
		// Register the AlertMessageBag class.
		$this->app['alerts'] = $this->app->share(function() {
			
			return new AlertMessages(
				 $this->app['session.store'],
				 $this->app['config'],
				 $this->app['router'],
				 $this->app['view']
				 );
			
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('alerts');
	}

}
