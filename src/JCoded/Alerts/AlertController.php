<?php namespace JCoded\Alerts;

use Controller;
use Input;

/**
 * Description of AlertController
 *
 * @author James Smith © Copyright 2014
 */
class AlertController extends Controller
{
	public function rememberDismiss()
	{
		if( Input::has('id') ) Facades\Alert::dismissMessage(Input::get('id'));
	}
}
