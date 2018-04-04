<?php

	class CMessage {

		var $m_Index 	= -1;

		var $m_Messages = array("SUCCESS",

								"CONNECTION FAILURE",		//(1)	Database connection failure

								"DATABASE NOT FOUND",		//(2)	Database name not found

								"DATABASE CONNECTED",		//(3)	Database connected successfully

								"DB QUERY ERROR",			//(4)	Query execution error

								"DB QUERY EXECUTED",		//(5)	Query executed successfully

								"DB TRANS BEGIN",			//(6)	Begin a database transaction 

								"DB TRANS ROLLBACK",		//(7)	Rollback a database transaction

								"DB TRANS COMMIT",			//(8)	Commit a database transaction

								"INVALID UID PWD",			//(9)	Invalid user id or password/ Security failure

								"ALREADY LOGGED IN", 		//(10)	A user is already logged into the system

								"NULL ENTRY", 				//(11)	Variable or Field is not set. 

								"PERMISSION DENIED", 		//(12)	Permission denied for Authorised operations. 

								"UNKNOWN ERROR",		 	//(13)	UnKnown error occuring on CATCH block

								"FILE OPEN FAILED",			//(14)	Can not open file stream

								"FILE CLOSE FAILED",		//(15)	Can not close opened file stream

								"FILE READ FAILED",			//(16)	Can not read the file

								"FILE WRITE FAILED",		//(17)	Can not write the file

								"MAIL SEND FAILED",			//(18)	Can not send the mail

								"DUPLICATE ENTRY"			//(19)	Can not send the mail

								);

			

		///----------------------------------------------------------------------

		/// Returns message description according to message num.

		///----------------------------------------------------------------------

		function GetMessage($num) {

			try {

				// returning error

				return $this->m_Messages[$num];

			}	catch(Exception $ex) {

				return "NOT FOUND";

			}

		}	// end of GetMessage()



		///----------------------------------------------------------------------

		/// Set current message index.

		///----------------------------------------------------------------------

		function SetMessage($num) {

			try {

				// setting index

				$this->m_Index = $num;

			}	catch(Exception $ex) {

				$this->m_Index = -1;

			}

		}	// end of SetMessage()



		///----------------------------------------------------------------------

		/// Returns current message description.

		///----------------------------------------------------------------------

		function GetCurrentMessage() {

			try {

				// returning error

				return $this->m_Messages[$this->m_Index];

			}	catch(Exception $ex) {

				return "NOT FOUND";

			}

		}	// end of GetCurrentMessage()

	}





	/*

	define("SECRET_CODE", "MATRIX", TRUE);

	define("COOKIE_DEFAULT", "", TRUE);

	define("COOKIE_CLIENT", "2", TRUE);

	*/



?>