<?php
	class Constants {

		
	function __construct($clientURL)
        {
                $this->HOSTNAME=$clientURL;
        }

		public $HOSTNAME = "";
		public $KEY = '3c6d390a-3b5a-4117-b64c-a7fc20284e41';
		public $SECRET = 'XwVHIDPNn0zVmkYiIHxfOfKkmebzJ1jN';

		public $AUTH_PATH = '/learn/api/public/v1/oauth2/token';
		public $DSK_PATH = '/learn/api/public/v1/dataSources';
		public $TERM_PATH = '/learn/api/public/v1/terms';
		public $COURSE_PATH = '/learn/api/public/v1/courses';
		public $USER_PATH = '/learn/api/public/v1/users';

		public $ssl_verify_peer = FALSE;
		public $ssl_verify_host =  FALSE;
	}
?>
