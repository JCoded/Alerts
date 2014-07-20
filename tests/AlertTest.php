<?php

/**
 * Description of AlertTest
 *
 * @author James Smith © Copyright 2014
 */
class AlertTest extends PHPUnit_framework_TestCase
{
	protected $alert;
	protected $type;
	protected $message;
	protected $dismissible;
	protected $id;
	
	protected function setUp()
	{
		$this->type = "test type";
		$this->message = "test message";
		$this->dismissible = true;
		$this->dismissed = false;
		$this->id = 10;
		$this->alert = new JCoded\Alerts\Alert($this->type,$this->message,$this->dismissible,$this->id);
	}
	
	public function testSetId()
	{
		$this->alert->setId(9);
		$this->assertEquals($this->alert->getId(), 9);
	}
	
	public function testSetType()
	{
		$this->alert->setType("new type");
		$this->assertEquals($this->alert->getType(), "new type");
	}
	
	public function testSetMessage()
	{
		$this->alert->setMessage("new message");
		$this->assertEquals($this->alert->getMessage(), "new message");
	}
	
	public function testSetDismissible()
	{
		$this->alert->setDismissible(false);
		$this->assertEquals($this->alert->dismissible(), false);
	}
	
	public function testDismiss()
	{
		$this->alert->dismiss();
		$this->assertTrue($this->alert->dismissed());
	}
	
	
	public function testGetId()
	{
		$this->assertEquals($this->alert->getId(), $this->id);
	}
	
	public function testGetType()
	{
		$this->assertEquals($this->alert->getType(), $this->type);
	}
	
	public function testGetMessage()
	{
		$this->assertEquals($this->alert->getMessage(), $this->message);
	}
	
	public function testDismissible()
	{
		$this->assertEquals($this->alert->dismissible(), $this->dismissible);
	}
	
	public function testDismissed()
	{
		$this->assertEquals($this->alert->dismissed(), $this->dismissed);
	}
}
