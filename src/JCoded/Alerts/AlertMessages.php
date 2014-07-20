<?php namespace JCoded\Alerts;

use BadMethodCallException;
use BadFunctionCallException;

/**
 * Description of AlertMessages
 *
 * @author James Smith © Copyright 2014
 */
class AlertMessages 
{
	private $temporary = array();
	
	private $permanent = array();
	
	/**
	 * Alert class map
	 * @var array 
	 */
	protected $alert_class = array(
		'Bootstrap' => array(
			'success' => 'success', 
			'info' => 'info', 
			'warning' => 'warning', 
			'error' => 'danger'
		),
		'Foundation' => array(
			'success' => 'success', 
			'info' => 'info', 
			'warning' => 'warning', 
			'error' => 'alert'
		)
	);
	
	public function __construct(
		 \Illuminate\Session\Store $session, 
		 \Illuminate\Config\Repository $config, 
		 \Illuminate\Routing\Router $route, 
		 \Illuminate\View\Factory $view
		 )
	{
		$this->session = $session;
		$this->config = $config;
		$this->route = $route;
		$this->view = $view;
	}
	
	/**
	 * Add a new Alert to the session messages array
	 * @param string $type success|info|warning|error
	 * @param string $message
	 * @param boolean $dismissible
	 * @param boolean $flash
	 * @return integer|null
	 */
	public function add($type,$message,$dismissible=false,$flash=false)
	{
		if( $flash ) {
			
			$this->temporary = $this->session->pull($this->sessionKey().'_temp',array());
			
			//Temporary messages - no need for an ID
			array_push($this->temporary,new Alert($type,$message,$dismissible));
			
			$this->session->put($this->sessionKey().'_temp',$this->temporary);
			
		} else {
			
			$this->permanent = $this->session->pull($this->sessionKey().'_perm',array());
			
			//Permanent message
			$id = $this->nextId();
			
			array_push($this->permanent,new Alert($type,$message,$dismissible,$id));
			
			$this->session->put($this->sessionKey().'_perm',$this->permanent);
			
			return $id;
		}
	}
	
	/**
	 * Set a specific message to not be displayed
	 * the message remains in the messages array so it is not reset
	 * @param integer $id
	 */
	public function dismissMessage($id)
	{
		//Load from session
		$permanent = $this->session->pull($this->sessionKey().'_perm',array());
		
		foreach ($permanent as $alert) {
			if ( $alert->getId() == $id ) $alert->dismiss();
		}
		
		//Save to session
		$this->session->put($this->sessionKey().'_perm',$permanent);
	}
	
	/**
	 * Returns the box classes from the config.
	 * @return string
	 */
	protected function divClasses()
	{
		return $this->config->get('alerts::div_classes');
	}
	
	/**
	 * Returns the framework from the config.
	 * @return string
	 */
	protected function framework()
	{
		return $this->config->get('alerts::template_framework');
	}
	
	/**
	 * Get an array of all the alert messages
	 * @param string $type success|info|warning|error
	 * @param boolean $clearTemporary
	 * @return array
	 */
	public function getAlerts($type=null,$clearTemporary=false)
	{
		//Load from session
		$this->permanent = $this->session->get($this->sessionKey().'_perm',array());
		$this->temporary = $this->session->pull($this->sessionKey().'_temp',array());
		
		$alerts = array();
		
		foreach( $this->permanent as $alert ) {
			
			if( !is_null($type) && $alert->getType() == $type && !$alert->dismissed() ) {
				
				array_push($alerts,$alert);
				
			} else if( is_null($type) && !$alert->dismissed() ) {
				
				array_push($alerts,$alert);
				
			}
		}
		
		foreach( $this->temporary as $index=>$alert ) {
			
			if( !is_null($type) && $alert->getType() == $type ) {
				
				array_push($alerts,$alert);
				
				if ( $clearTemporary ) unset ( $this->temporary[$index] );
				
			} else if( is_null($type) ) {
				
				array_push($alerts,$alert);
				
				if ( $clearTemporary ) unset ( $this->temporary[$index] );
				
			}
		}
		
		$this->session->put($this->sessionKey().'_temp',$this->temporary);
		
		return $alerts;
	}
	
	
	/**
	 * Checks the permanent alert message array for a matching alert
	 * @param string $type success|info|warning|error
	 * @param string $message
	 * @return boolean
	 */
	public function has($type,$message) {
		
		$this->permanent = $this->session->get($this->sessionKey().'_perm',array());
		
		foreach( $this->permanent as $alert ) {
			
			if( $alert->getType() == $type && $alert->getMessage() == $message ) 
				return true;
			
		}
		
		return false;
	}
	
	/**
	 * Returns HTML of the visible alerts
	 * @return string
	 */
	public function html() 
	{
		$html = '';
		
		$hasDismissible = false;
		
		foreach( $this->getAlerts(null,true) as $alert ) {
			
			if( $alert->dismissed() ) continue;
			
			if( $alert->dismissible() ) {
				
				$hasDismissible = true;
				$view ='dismissible';
				
			} else {
				
				$view ='standard';
			}
			
			$alert_class = $this->alert_class[$this->framework()][$alert->getType()];
			
			$div_classes = $this->divClasses();
			
			$message = $alert->getMessage();
			
			$id = $alert->getId();
			
			$html .= $this->view->make(
							'alerts::'.$this->framework().'.'.$view,
							compact('message','alert_class','div_classes','id')
						)->render();
		}
		
		$route = $this->route->getRoutes()->getByAction('JCoded\Alerts\AlertController@rememberDismiss');
		
		if( $hasDismissible && !is_null($route) ) {
			
			$html .= $this->view->make('alerts::dismiss-js')->render();
		}
		
		return $html;
	}
	
	/**
	 * Returns the next available alert ID
	 * @return int
	 */
	private function nextId()
	{
		$ids = array();
		
		foreach ($this->permanent as $alert) {
			
			array_push($ids,$alert->getId());
			
		}
		
		if ( empty($ids) ) return 1;
		
		return intval(max($ids))+1;
	}
	
	/**
	 * Returns the session key from the config
	 * @return string
	 */
	protected function sessionKey()
	{
		return $this->config->get('alerts::session_key');
	}
	
	/**
	 * Handle the calls to alert types by name
	 * @param string $method
	 * @param array $args
	 * @return void
	 * @throws BadFunctionCallException
	 * @throws BadMethodCallException
	 */
	public function __call($method, $args)
	{
		$dismissible = false;
		
		if (strpos($method,'Dismissible') !== false) {
			
			$dismissible = true;
			
			$method = str_replace('Dismissible','',$method);
		}
		
		// Check if the method is in the allowed alert levels array.
		if ( in_array($method, array('success', 'info', 'warning', 'error'))) {
			
			if (isset($args[0]))	{
				
				$messages = $args[0];
				
				if (!is_array($messages)) {
					
					$messages = array($messages);
				}

				foreach ($messages as $message) {
					
					$this->add($method, $message, $dismissible, true);
				}
				
				return;
			}

			throw new BadFunctionCallException("No message set to alert");
		}

		throw new BadMethodCallException("Method {$method} does not exist. Did you mean to append Dismissible?");
	}
}
