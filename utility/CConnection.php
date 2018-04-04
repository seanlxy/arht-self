<?php
	/*
	This class is for connecting to database[MySql] and execute operations.
	
	Author  : Ton Jo Immanuel
	*/
	@include_once("CMessage.php");

	class CConnection {
		var $m_ServerName 	= "";
		var $m_DatabaseName 	= "";
		var $m_UserName 	= "";
		var $m_Password 	= "";

		var $c_ConnectStat	= NULL;
		var $c_Message 		= NULL;
		
		var $AutoIncrementId= 0;
		var $AffectedRows=0;

		function CConnection() {
			$this->c_Message = new CMessage();
		}

		// Function to set the database server details
		function Configure($ServerName, $DatabaseName, $UserName, $Password) {
			$this->m_ServerName		= $ServerName;
			$this->m_DatabaseName 		= $DatabaseName;
			$this->m_UserName 		= $UserName;
			$this->m_Password 		= $Password;
		}

		// Function to connect to the database server
		// Returns TRUE if connected successfully
		// Otherwise returns FALSE
		function Connect() {
			try {
				$this->m_ConnectStat = @mysql_connect($this->m_ServerName, $this->m_UserName, $this->m_Password);
								
				if(!$this->m_ConnectStat) {
					// CONNECTION FAILURE
					$this->c_Message->SetMessage(1);
					return false;
				}	else {
					if (!@mysql_select_db($this->m_DatabaseName)) {
						// DATABASE NOT FOUND
						$this->c_Message->SetMessage(2);
						return false;
					}	else {
						$this->c_Message->SetMessage(3);
						// DATABASE CONNECTED
						return true;
					}
				}
			}	catch (Exception $ex) {
				return false;
			}
		}
		
		// Close existing connection
		function Close() {
			try {
				if($this->m_ConnectStat) {
					@mysql_close($this->m_ConnectStat);
				}
			}	catch(Exception $ex) {}
		}
		
		// Excuting query
		function ExecuteQuery($query) {
			try {
				$result = @mysql_query($query);
				$this->AutoIncrementId = mysql_insert_id();
				$this->AffectedRows = mysql_affected_rows();
				$query  = "";
				
				// checking the result of the execution
				if( !$result ) {
					// DB QUERY ERROR
					$this->c_Message->SetMessage(4);
					return false;
				}	else {
					// DB QUERY EXECUTED
					$this->c_Message->SetMessage(5);
					return true;
				}
			}	catch (Exception $ex) {
				return false;
			}
		}
		
		// Executing SELECT query and returns recordset if executed successfully else returns FALSE
		function GetRecordSet($query) {
			try {
				$ResultSet = @mysql_query($query) or die(mysql_error());
				// checking the result of the execution
				if( mysql_errno() ) {
					// DB QUERY ERROR
					$this->c_Message->SetMessage(4);
					return false;
				}	else {
					// DB QUERY EXECUTED
					$this->c_Message->SetMessage(5);
					return $ResultSet;
				}
			}	catch (Exception $ex)  {
				return false;
			}
		}
		
		// Executing and returning first cell result
		function ExecuteScalar($query) {
			try {
				$result = $this->GetRecordSet($query);
										
				while($recordSetRow = mysql_fetch_array($result)) {
					// DB QUERY EXECUTED
					$this->c_Message->SetMessage(5);
					return $recordSetRow[0];
					break;
				}
			}	catch (Exception $ex)  {
				// DB QUERY ERROR
				$this->c_Message->SetMessage(4);
				return false;
			}
		}
		
		// Begin a new transaction on server
		function BeginTransaction() {
			@mysql_query("BEGIN");
			$this->c_Message->SetMessage(6);
		}
		
		// Commit a transaction
		function CommitTransaction() {
			@mysql_query("COMMIT");
			$this->c_Message->SetMessage(7);
		}
		
		// Roll back a trasaction
		function RollbackTransaction() {
			@mysql_query("ROLLBACK");
			$this->c_Message->SetMessage(7);
		}
		
		// This will execute a series of queries and will perform COMMIT and ROLLBACK automatically
		function ExecuteTransaction( $queryList ) {
			try {
				for($i=0; $i<=count($queryList); $i++) {
					if( $i == 0 ) {
						// DB TRANS BEGIN
						$this->BeginTransaction();
					}
					if(!ExecuteQuery($queryList[$i]) ) {
						// DB TRANS ROLLBACK
						$this->RollbackTransaction();
						return false;
					}
				}
				// DB TRANS COMMIT
				$this->CommitTransaction();
				return true;
			}	catch (Exception $ex)  {
				// DB TRANS ROLLBACK
				$this->RollbackTransaction();
				return false;
			}
		}
		
		//Get the Identity ID(AUTO_INCREMENT ID)
		function GetAutoIncrementId(){
			// Returns the value generated for an AUTO_INCREMENT column by the previous INSERT
			return mysql_insert_id();
		}
		
		// Return current message of the class
		function GetMessage() {
			return $this->c_Message;
		}
	}
?>