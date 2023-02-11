<? 
	/*********************************************************************************
		File name		:		db_config.php
		Date created	:		15 June 2010
		Last modify		:		15 June 2010
		Description		:		Database + LDAP configuration
	**********************************************************************************/

	require_once('sys_constants.php');
	
	//db config
	$db_config['dbms']				= "mysql"; // DBMS
	$db_config['host_name']			= "localhost"; // server name
	$db_config['user_fullname']		= "cims"; // database's user name
	$db_config['password']			= "cimsjkkp2011"; // database's user password 
	$db_config['database']			= "jkkp_eInventory"; // database name
	
	/*** include database layer ***/
	switch($db_config['dbms'])
	{
		case 'mysqli':
			require_once($sys_config['classes_path']. 'dbs/mysqli.php');
			break;
		case 'mysql':
			require_once($sys_config['classes_path']. 'dbs/mysql.php');
			break;
		case 'mysql4':
			require_once($sys_config['classes_path']. 'dbs/mysql4.php');
			break;
		case 'postgres':
			require_once($sys_config['classes_path']. 'dbs/postgres7.php');
			break;
		case 'mssql':
			require_once($sys_config['classes_path']. 'dbs/mssql.php');
			break;
		case 'oracle':
			require_once($sys_config['classes_path']. 'dbs/oracle.php');
			break;
		case 'msaccess':
			require_once($sys_config['classes_path']. 'dbs/msaccess.php');
			break;
		case 'mssql-odbc':
			require_once($sys_config['classes_path']. 'dbs/mssql-odbc.php');
			break;
	}
	
	//=====================================================================================
	$db = new	sql_db(	$db_config['host_name'],
						$db_config['user_fullname'],
						$db_config['password'],
						$db_config['database'],
						false);	// create database object
	if(!$db->db_connect_id)
	   die("Unable to establish database connection_1b");

	// $db->sql_query("SET NAMES 'latin1';",END_TRANSACTION);	// set connection character set
	//=====================================================================================
	ini_set('date.timezone','UTC');
?>