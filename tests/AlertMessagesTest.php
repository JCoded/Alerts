<?php

use Mockery as m;

/**
 * Description of AlertMessagesTest
 *
 * @author James Smith © Copyright 2014
 */
class AlertMessagesTest extends Orchestra\Testbench\TestCase
{
	public function setUp()
	{
		parent::setUp();
		
		$this->sessionKey = "test_session";
		
		$this->session = m::mock('\Illuminate\Session\Store');
		$this->config = m::mock('\Illuminate\Config\Repository');
		$this->app['view']->addNamespace('alerts',realpath(dirname(__FILE__)).'/../src/views');
		
		$this->alertMessages = new \JCoded\Alerts\AlertMessages(
			 $this->session,
			 $this->config,
			 $this->app['router'],
			 $this->app['view']);
	}
	
	public function tearDown()
	{
		m::close();
	}
	
	private function setupConfigSessionKey()
	{
		$this->config->shouldReceive('get')->with('alerts::session_key')->andReturn($this->sessionKey);
	}
	
	public function testAddTemp()
	{
		//Arrange
		$dummyAlert = new \JCoded\Alerts\Alert('info','test message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyAlert));
		
		//Act
		$result = $this->alertMessages->add('info','test message',false,true);
		
		//Assert
		$this->assertNull($result,'Result not null');
	}
	
	public function testAddTempDismissible()
	{
		//Arrange
		$dummyAlert = new \JCoded\Alerts\Alert('info','test message',true);
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyAlert));
		
		//Act
		$result = $this->alertMessages->add('info','test message',true,true);
		
		//Assert
		$this->assertNull($result,'Result not null');
	}
	
	public function testAddPerm()
	{
		//Arrange
		$dummyAlert = new \JCoded\Alerts\Alert('info','test message');
		$dummyAlert->setId(1);
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_perm',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_perm',array($dummyAlert));
		
		//Act
		$result = $this->alertMessages->add('info','test message');
		
		//Assert
		$this->assertEquals($result,1,'Returned alert ID incorrect');
	}
	
	public function testAddPermDismissible()
	{
		//Arrange
		$dummyAlert = new \JCoded\Alerts\Alert('info','test message',true);
		$dummyAlert->setId(1);
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_perm',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_perm',array($dummyAlert));
		
		//Act
		$result = $this->alertMessages->add('info','test message',true);
		
		//Assert
		$this->assertEquals($result,1,'Returned alert ID incorrect');
	}
	
	public function testDismissMessage()
	{
		//Arrange
		$dummyAlert = new \JCoded\Alerts\Alert('info','test message');
		$dummyAlert->setId(1);
		$dissmissedDummyAlert = clone $dummyAlert;
		$dissmissedDummyAlert->dismiss();
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_perm',array($dissmissedDummyAlert));
		
		//Act
		$this->alertMessages->dismissMessage(1);
	}
	
	public function testGetAlertsPreserve()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyTempAlert));
		
		//Act
		$result = $this->alertMessages->getAlerts();
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array($dummyPermAlert,$dummyTempAlert),'Result does not contain expected alerts');
	}
	
	public function testGetAlertsPreserveDismissed()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyPermAlert->dismiss();
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyTempAlert));
		
		//Act
		$result = $this->alertMessages->getAlerts();
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array($dummyTempAlert),'Result does not contain expected alerts');
	}
	
	public function testGetAlertsTypePreserve()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyTempAlert));
		
		//Act
		$result = $this->alertMessages->getAlerts('warning');
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array($dummyPermAlert),'Result does not contain expected alerts');
	}
	
	public function testGetAlertsTypePreserveDismissed()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyPermAlert->dismiss();
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyTempAlert));
		
		//Act
		$result = $this->alertMessages->getAlerts('warning');
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array(),'Result does not contain expected alerts');
	}
	
	public function testGetAlerts()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array());
		
		//Act
		$result = $this->alertMessages->getAlerts(null,true);
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array($dummyPermAlert,$dummyTempAlert),'Result does not contain expected alerts');
	}
	
	public function testGetAlertsDismissed()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyPermAlert->dismiss();
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array());
		
		//Act
		$result = $this->alertMessages->getAlerts(null,true);
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array($dummyTempAlert),'Result does not contain expected alerts');
	}
	
	public function testGetAlertsType()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyTempAlert));
		
		//Act
		$result = $this->alertMessages->getAlerts('warning',true);
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array($dummyPermAlert),'Result does not contain expected alerts');
	}
	
	public function testGetAlertsTypeDismissed()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyPermAlert->dismiss();
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($dummyTempAlert));
		
		//Act
		$result = $this->alertMessages->getAlerts('warning',true);
		
		//Assert
		$this->assertTrue(is_array($result),'Result is not an array');
		$this->assertEquals($result,array(),'Result does not contain expected alerts');
	}
	
	public function testHas()
	{
		//Arrange
		$dummyAlert = new \JCoded\Alerts\Alert('warning','test message');
		
		$this->setupConfigSessionKey();
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyAlert));
		
		//Act
		$positive = $this->alertMessages->has('warning','test message');
		$negativeType = $this->alertMessages->has('success','test message');
		$negativeMessage = $this->alertMessages->has('warning','test');
		
		//Assert
		$this->assertTrue($positive,'Failed positive has');
		$this->assertFalse($negativeType,'Failed negative type');
		$this->assertFalse($negativeMessage,'Failed negative message');
	}
	
	public function testHtmlFoundationNoClass()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		
		$this->config->shouldReceive('get')->with('alerts::template_framework')->andReturn('Foundation');
		$this->config->shouldReceive('get')->with('alerts::div_classes')->andReturn('');
		
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array());
		
		
		//Act
		$expectedHtml = '<div class="alert-box warning ">test perm message</div><div class="alert-box success ">test temp message</div>';
		
		$html = $this->alertMessages->html();
		
		//Assert
		$this->assertEquals($expectedHtml,$html);
	}
	
	public function testHtmlFoundationClass()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		
		$this->config->shouldReceive('get')->with('alerts::template_framework')->andReturn('Foundation');
		$this->config->shouldReceive('get')->with('alerts::div_classes')->andReturn('rounded');
		
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array());
		
		
		//Act
		$expectedHtml = '<div class="alert-box warning rounded">test perm message</div><div class="alert-box success rounded">test temp message</div>';
		
		$html = $this->alertMessages->html();
		
		//Assert
		$this->assertEquals($expectedHtml,$html);
	}
	
	public function testHtmlBootstrapNoClass()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		
		$this->config->shouldReceive('get')->with('alerts::template_framework')->andReturn('Bootstrap');
		$this->config->shouldReceive('get')->with('alerts::div_classes')->andReturn('');
		
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array());
		
		
		//Act
		$expectedHtml = '<div class="alert alert-warning " role="alert">test perm message</div><div class="alert alert-success " role="alert">test temp message</div>';
		
		$html = $this->alertMessages->html();
		
		//Assert
		$this->assertEquals($expectedHtml,$html);
	}
	
	public function testHtmlBootstrapClass()
	{
		//Arrange
		$dummyPermAlert = new \JCoded\Alerts\Alert('warning','test perm message');
		$dummyPermAlert->setId(1);
		$dummyTempAlert = new \JCoded\Alerts\Alert('success','test temp message');
		
		$this->setupConfigSessionKey();
		
		$this->config->shouldReceive('get')->with('alerts::template_framework')->andReturn('Bootstrap');
		$this->config->shouldReceive('get')->with('alerts::div_classes')->andReturn('extra');
		
		$this->session->shouldReceive('get')->with($this->sessionKey.'_perm',array())->andReturn(array($dummyPermAlert));
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array($dummyTempAlert));
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array());
		
		
		//Act
		$expectedHtml = '<div class="alert alert-warning extra" role="alert">test perm message</div><div class="alert alert-success extra" role="alert">test temp message</div>';
		
		$html = $this->alertMessages->html();
		
		//Assert
		$this->assertEquals($expectedHtml,$html);
	}
	/**
		$info = \JCoded\Alerts\Alert::info('call info');
		$warning = \JCoded\Alerts\Alert::warning("call warning");
		$error = \JCoded\Alerts\Alert::error("call error");
	 */
	public function testCallSuccess()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('success','call success');
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->success("call success");
	}
	
	public function testCallSuccessDismiss()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('success','call success',true);
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->successDismissible("call success");
	}
	
	public function testCallInfo()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('info','call info');
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->info("call info");
	}
	
	public function testCallInfoDismiss()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('info','call info',true);
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->infoDismissible("call info");
	}
	
	public function testCallWarning()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('warning','call warning');
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->warning("call warning");
	}
	
	public function testCallWarningDismiss()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('warning','call warning',true);
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->warningDismissible("call warning");
	}
	
	public function testCallError()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('error','call error');
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->error("call error");
	}
	
	public function testCallErrorDismiss()
	{
		//Arrange
		$alert = new \JCoded\Alerts\Alert('error','call error',true);
		
		$this->setupConfigSessionKey();
		
		$this->session->shouldReceive('pull')->with($this->sessionKey.'_temp',array())->andReturn(array());
		$this->session->shouldReceive('put')->with($this->sessionKey.'_temp',array($alert));
		
		//Act
		$this->alertMessages->errorDismissible("call error");
	}
	
	public function testCallMethodException()
	{
		//Arrange
		$this->setExpectedException('BadMethodCallException');
		
		//Act
		$this->alertMessages->err("this doesn't exist");
	}
	
	public function testCallMessageException()
	{
		//Arrange
		$this->setExpectedException('BadFunctionCallException');
		
		//Act
		$this->alertMessages->info();
	}
}
