<?php 

/**
 * Class to create a PDO Database connection
 *
 * @author Talwinder
 */

class Database
{

	protected $config = array(
		'host'      => '',
		'db_name'   => '',
		'username' => '',
		'password'  => '',
		'driver'    => 'mysql'
	);
	
	protected $dbh;

	protected $sth;

	protected $is_connected = false;

	public function __construct( $config )
	{
		$this->connect( $config );
	}

	private function connect( $config )
	{
		$this->setConfig($config);

		$dsn = "{$this->config['driver']}:host={$this->config['host']};dbname={$this->config['db_name']}";

		try {

			if( $this->is_connected == false )
			{
				// Conenct to  database
				$this->dbh = new PDO($dsn, $this->config['username'], $this->config['password']);

				//  Enable error logging
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				//  Disable emulation of prepared statements
				$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

				$this->is_connected = true;
			}

		}
		catch(PDOException $e)
		{

		    die( $e->getMessage() );
		}
	}

	private function init( $sql, $params = '' )
	{

		try {

			$this->sth = $this->dbh->prepare($sql);

			if( !empty($params) )
			{

				foreach ($params as $param => $value)
				{
					$param_type = PDO::PARAM_STR;

                    // switch ($value)
                    // {
                    //     case is_int($value):
                    //         $param_type = PDO::PARAM_INT;
                    //     break;
                    //     case is_bool($value):
                    //         $param_type = PDO::PARAM_BOOL;
                    //     break;
                    //     case is_null($value):
                    //         $param_type = PDO::PARAM_NULL;
                    //     break;
                    // }

                    $this->sth->bindValue( $param, $value, $param_type );
				}
			}

			$this->sth->execute();
			

		} 
		catch (Exception $e)
		{
			
			die( $e->getMessage() );
		}

		return $this->sth;
	}


	public function runQuery( $sql, $params = null, $fetch_type = null, $fetch_mode  = PDO::FETCH_ASSOC )
	{
		
		$sql = trim(str_replace("\r", " ", $sql));
        
        $this->init( $sql, $params );
        
        $raw_sql = explode(" ", preg_replace("/\s+|\t+|\n+/", " ", $sql));
        
        // Which SQL statement is used 
        $statement = strtolower($raw_sql[0]);

        if( $statement === 'select' )
        {

        	$this->sth->setFetchMode( $fetch_mode );

			if( $fetch_type == 'row' )
        	{
	        	return $this->sth->fetch();
			}
			elseif( $fetch_type == 'column' )
        	{
	        	return $this->sth->fetchColumn();
			}
			else
        	{
	        	return array(
					'count'  => $this->sth->rowCount(),
					'rows' => $this->sth->fetchAll(),
				);

			}

        }
        elseif( $statement === 'insert' )
        {
        	return array( 'last_id' => $this->getLastInsertId() );
        }
        else
        {
        	return array( 'total_rows' => $this->sth->rowCount() );
        }
	}

	private function exec( $sql, $array = null, $fetch_type = null )
	{
		
		$result =  $this->runQuery($sql, $array, $fetch_type);	

		return $result;
	}

	private function getParamsForQuery( $params = array() )
	{
		if( !empty($params) )
		{
			$fields = array_keys( $params );

			return array( '`'.implode('`,`', $fields ).'`', ':'.implode(',:', $fields) );
		}

		return $params;
	}

	public function insert( $insert_arr, $table )
	{

		if( !empty($insert_arr) && $table )
		{
			$fields_values = $this->getParamsForQuery($insert_arr);

			$q = "INSERT INTO `{$table}` ({$fields_values[0]}) VALUES ({$fields_values[1]})";

			$result = $this->exec($q, $insert_arr);

			return $result['last_id'];
		}

		return false;
	}

	public function update( $update_arr, $table, $condition = '' )
	{

		if( !empty($update_arr) &&  $table )
		{

			$str = '';

			foreach ($update_arr as $key => $val) $str .= "`{$key}` = :{$key},";

			$str = trim($str,', ');

			$sql = "UPDATE `{$table}` SET {$str} {$condition}";

			$result = $this->exec( $sql, $update_arr );

		    return $result['total_rows'];
		}

		return false;
	}

	public function delete( $sql )
	{

		if( !empty($sql) )
		{
			$result = $this->exec( $sql, null );

		    return $result['total_rows'];
		}

		return false;
	}

	public function fetchAll( $sql )
	{

		return $this->exec($sql, null, 'all');
	}

	public function fetchRow($sql)
	{
		return $this->exec($sql, null, 'row');
	}

	public function fetchValue($sql)
	{
		return $this->exec($sql, null, 'column');
	}

	public function getLastInsertId()
	{
		return $this->dbh->lastInsertId();
	}
	
	private function setConfig($config = array())
	{
		if( is_array($config) )
		{
			
			$this->config = array_merge($this->config, $config);
		}
		
		return $this->config;
	}

	public function closeConnection()
	{
		$this->dbh = null;
	}

}// End of database class


/* End of file database.php */
/* Location: /classes/database.php */

?>