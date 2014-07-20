<?php namespace JCoded\Alerts;

/**
 * Description of Alert
 *
 * @author James Smith © Copyright 2014
 */
class Alert 
{
	private $id;
	private $type;
	private $message;
	private $dismissible;
	private $dismissed = false;
	
	public function __construct($type=null,$message=null,$dismissible=false,$id=null) 
	{
		$this->id = $id;
		$this->type = $type;
		$this->message = $message;
		$this->dismissible = $dismissible;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function setMessage($message)
	{
		$this->message = $message;
	}
	
	public function setDismissible($dismissible)
	{
		$this->dismissible = $dismissible;
	}
	
	public function dismiss()
	{
		$this->dismissed = true;
	}
	
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	public function dismissible()
	{
		return $this->dismissible;
	}
	
	public function dismissed()
	{
		return $this->dismissed;
	}
}
