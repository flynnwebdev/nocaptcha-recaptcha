<?php

	Class extension_recaptcha extends Extension{

		public function about(){
			return array('name' => 'reCAPTCHA',
						 'version' => '1.0',
						 'release-date' => '2008-05-07',
						 'author' => array(	'name' => 'Symphony Team',
											'website' => 'http://symphony21.com',
											'email' => 'team@symphony21.com'),
						 'description' => 'This is an event that uses the reCAPTCHA service to help prevent spam.'
				 		);
		}
		
		public function getSubscribedDelegates(){
			return array(
						array(
							'page' => '/blueprints/events/new/',
							'delegate' => 'AppendEventFilter',
							'callback' => 'addFilterToEventEditor'
						),
						
						array(
							'page' => '/blueprints/events/edit/',
							'delegate' => 'AppendEventFilter',
							'callback' => 'addFilterToEventEditor'
						),
						
						array(
							'page' => '/blueprints/events/new/',
							'delegate' => 'AppendEventFilterDocumentation',
							'callback' => 'addFilterDocumentationToEvent'
						),
											
						array(
							'page' => '/blueprints/events/edit/',
							'delegate' => 'AppendEventFilterDocumentation',
							'callback' => 'addFilterDocumentationToEvent'
						),
						
						array(
							'page' => '/system/preferences/',
							'delegate' => 'AddCustomPreferenceFieldsets',
							'callback' => 'appendPreferences'
						),
						
						array(
							'page' => '/frontend/',
							'delegate' => 'EventPreSaveFilter',
							'callback' => 'processEventData'
						),					
			);
		}
		
		public function addFilterToEventEditor($context){
			$context['options'][] = array('recaptcha', @in_array('recaptcha', $context['selected']) ,'reCAPTCHA Verification');		
		}
		
		public function appendPreferences($context){
			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(new XMLElement('legend', 'reCAPTCHA Verification'));

			$div = new XMLElement('div', NULL, array('class' => 'group'));
			$label = Widget::Label('Public Key');
			$pub_key = General::Sanitize(Symphony::Configuration()->get('public-key', 'recaptcha'));
			$priv_key = General::Sanitize(Symphony::Configuration()->get('private-key', 'recaptcha'));
			$label->appendChild(Widget::Input('settings[recaptcha][public-key]', $pub_key));		
			$div->appendChild($label);

			$label = Widget::Label('Private Key');
			$label->appendChild(Widget::Input('settings[recaptcha][private-key]', $priv_key));		
			$div->appendChild($label);
			
			$group->appendChild($div);
			
			$group->appendChild(new XMLElement('p', 'Get a reCAPTCHA API public/private key pair from the <a href="http://recaptcha.net/whyrecaptcha.html">reCAPTCHA site</a>.', array('class' => 'help')));
			
			$context['wrapper']->appendChild($group);
						
		}
		
		public function addFilterDocumentationToEvent($context){
			if(!in_array('recaptcha', $context['selected'])) return;
			
			$context['documentation'][] = new XMLElement('h3', 'reCAPTCHA Verification');
			
			$context['documentation'][] = new XMLElement('p', 'Each entry will be passed to the <a href="http://recaptcha.net/whyrecaptcha.html">reCAPTCHA filtering service</a> before saving. Should the challenge words not match, Symphony will terminate execution of the Event, thus preventing the entry from being saved. You will receive notification in the Event XML. <strong>Note: Be sure to set your reCAPTCHA public and private API keys in the <a href="'.URL.'/symphony/system/preferences/">Symphony Preferences</a>.</strong>');
			
			$context['documentation'][] = new XMLElement('p', 'The following is an example of the XML returned form this filter:');
			$code = '<filter type="recaptcha" status="passed" />
<filter type="recaptcha" status="failed">Challenge words entered were invalid.</filter>';

			$context['documentation'][] = contentBlueprintsEvents::processDocumentationCode($code);

		}
		
		public function processEventData($context){

			//print_r($context['event']->eParamFILTERS); die();

			if(!in_array('recaptcha', $context['event']->eParamFILTERS)) return;

			//echo $this->getPrivateKey();
						
			//print_r($_POST); die();
			
			include_once(EXTENSIONS . '/recaptcha/lib/recaptchalib.php');
			$resp = recaptcha_check_answer($this->getPrivateKey(), 
			                                $_SERVER['REMOTE_ADDR'],
			                                $_POST['g-recaptcha-response']);

			$context['messages'][] = array('recaptcha', $resp->is_valid, (!$resp->is_valid ? 'Recaptcha is invalid.' : NULL));

		}
		
		public function uninstall(){
			//ConfigurationAccessor::remove('recaptcha');	
			Symphony::Configuration()->remove('recaptcha');
			//$this->_Parent->saveConfig();
		}

		public function getPublicKey(){
			//return ConfigurationAccessor::get('public-key', 'recaptcha');
			return Symphony::Configuration()->get('public-key', 'recaptcha');
		}	
		
		public function getPrivateKey(){
			//return ConfigurationAccessor::get('private-key', 'recaptcha');
			return Symphony::Configuration()->get('private-key', 'recaptcha');
		}			
		
	}

?>