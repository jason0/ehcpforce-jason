<?php

include_once("classapp.php"); # real application class

/* this is jason's attempt at class inheritance.
 *
 *
 */
class EroiApp extends Application {


	function syncDns(){// for daemon mode
		# stub function to override parent.  we don't do dns management here.
		$this->requireCommandLine(__FUNCTION__);
		echo "\n\nsyncdns ignored: we don't do this here...: \n";
		return True;
		
	}

}