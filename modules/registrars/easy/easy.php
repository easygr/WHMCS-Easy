<?php
Class EasyApi
{
	public static function execute($resource, $query)
	{
		if( !is_array($query) )
			return false;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.easy.gr/' . $resource);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query, '', '&'));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		$response = curl_exec ($ch);
		if( $response === false )
			return false;
		return json_decode($response, true);
	}
	
	public static function from($value)
	{
		global $CONFIG;
		return iconv($CONFIG['Charset'], 'UTF-8', $value);
	}
	
	public static function to($value)
	{
		global $CONFIG;
		return iconv('UTF-8', $CONFIG['Charset'], $value);
	}
}
function easy_getConfigArray() 
{
	$configarray = array(
		'Username' => array(
			'Type' => 'text', 
			'Size' => '20', 
			'Description' => 'Enter your Easy.gr loginname',
		),
		'Password' => array(
			'Type' => 'password', 
			'Size' => '32', 
			'Description' => 'Enter your Easy.gr password',
		),
	);
	return $configarray;
}
function easy_GetEPPCode($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
	);
	$res = EasyApi::execute('getpassword', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['eppcode'] = $res['response'];
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_GetNameservers($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
	);
	$res = EasyApi::execute('getnameservers', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values = $res['response'];
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_SaveNameservers($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'ns1' => $params['ns1'],
		'ns2' => $params['ns2'],
		'ns3' => $params['ns3'],
		'ns4' => $params['ns4'],
	);
	$res = EasyApi::execute('setnameservers', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_RegisterDomain($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'ns1' => $params['ns1'],
		'ns2' => $params['ns2'],
		'ns3' => $params['ns3'],
		'ns4' => $params['ns4'],
		'years' => $params['regperiod'],
		/* registrant */
		'name1' => ( str_replace(' ','',$params['companyname']) !== '' ? EasyApi::from($params['companyname']) : EasyApi::from($params['firstname'].' '.$params['lastname'])),
		'street11' => EasyApi::from($params['address1']),
		'street21' => EasyApi::from($params['address2']),
		'city1' => EasyApi::from($params['city']),
		'pc1' => EasyApi::from($params['postcode']),
		'sp1' => EasyApi::from($params['state']),
		'cc1' => EasyApi::from($params['country']),
		'voice1' => $params['fullphonenumber'],
		'fax1' => EasyApi::from($params['fax']),
		'email1' => EasyApi::from($params['email']),
		/* admin */
		'name2' => ( str_replace(' ','',$params['admincompanyname']) !== '' ? EasyApi::from($params['admincompanyname']) : EasyApi::from($params['adminfirstname'].' '.$params['adminlastname'])),
		'street12' => EasyApi::from($params['adminaddress1']),
		'street22' => EasyApi::from($params['adminaddress2']),
		'city2' => EasyApi::from($params['admincity']),
		'pc2' => EasyApi::from($params['adminpostcode']),
		'sp2' => EasyApi::from($params['adminstate']),
		'cc2' => EasyApi::from($params['admincountry']),
		'voice2' => $params['adminfullphonenumber'],
		'fax2' => EasyApi::from($params['adminfax']),
		'email2' => EasyApi::from($params['adminemail']),
		/* tech */
		'name3' => ( str_replace(' ','',$params['admincompanyname']) !== '' ? EasyApi::from($params['admincompanyname']) : EasyApi::from($params['adminfirstname'].' '.$params['adminlastname'])),
		'street13' => EasyApi::from($params['adminaddress1']),
		'street23' => EasyApi::from($params['adminaddress2']),
		'city3' => EasyApi::from($params['admincity']),
		'pc3' => EasyApi::from($params['adminpostcode']),
		'sp3' => EasyApi::from($params['adminstate']),
		'cc3' => EasyApi::from($params['admincountry']),
		'voice3' => $params['adminfullphonenumber'],
		'fax3' => EasyApi::from($params['adminfax']),
		'email3' => EasyApi::from($params['adminemail']),
		/* billing */
		'name4' => ( str_replace(' ','',$params['admincompanyname']) !== '' ? EasyApi::from($params['admincompanyname']) : EasyApi::from($params['adminfirstname'].' '.$params['adminlastname'])),
		'street14' => EasyApi::from($params['adminaddress1']),
		'street24' => EasyApi::from($params['adminaddress2']),
		'city4' => EasyApi::from($params['admincity']),
		'pc4' => EasyApi::from($params['adminpostcode']),
		'sp4' => EasyApi::from($params['adminstate']),
		'cc4' => EasyApi::from($params['admincountry']),
		'voice4' => $params['adminfullphonenumber'],
		'fax4' => EasyApi::from($params['adminfax']),
		'email4' => EasyApi::from($params['adminemail']),
	);
	$res = EasyApi::execute('register', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_TransferDomain($params)
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'pw' => $params['transfersecret'],
	);
	$res = EasyApi::execute('transfer', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_RenewDomain($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'years' => $params['regperiod'],
	);
	$res = EasyApi::execute('renew', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = EasyApi::to(rtrim($values['error'],', '));
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_GetContactDetails($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
	);
	$res = EasyApi::execute('getcontacts', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					foreach( $res['response'] as $k => $contact )
						foreach( array('street1', 'street2', 'city', 'sp', 'pc') as $k1)
							$res['response'][$k][$k1] = EasyApi::to($v);

					$values = array(
						'Registrant' => array(
							'First Name' => $res['response']['registrant']['fname'],
							'Last Name' => $res['response']['registrant']['lname'],
							'Organization' => $res['response']['registrant']['org'],
							'Address 1' => $res['response']['registrant']['street1'],
							'Address 2' => $res['response']['registrant']['street2'],
							'City' => $res['response']['registrant']['city'],
							'Postal Code' => $res['response']['registrant']['pc'],
							'Region' => $res['response']['registrant']['sp'],
							'Phone' => $res['response']['registrant']['voice'],
							'Phone Code' => $res['response']['registrant']['phone_code'],
							'Fax' => $res['response']['registrant']['fax'],
							'Email' => $res['response']['registrant']['email'],
							'Country' => $res['response']['registrant']['cc'],
						),
						'Admin' => array(
							'First Name' => $res['response']['admin']['fname'],
							'Last Name' => $res['response']['admin']['lname'],
							'Organization' => $res['response']['admin']['org'],
							'Address 1' => $res['response']['admin']['street1'],
							'Address 2' => $res['response']['admin']['street2'],
							'City' => $res['response']['admin']['city'],
							'Postal Code' => $res['response']['admin']['pc'],
							'Region' => $res['response']['admin']['sp'],
							'Phone' => $res['response']['admin']['voice'],
							'Phone Code' => $res['response']['admin']['phone_code'],
							'Fax' => $res['response']['admin']['fax'],
							'Email' => $res['response']['admin']['email'],
							'Country' => $res['response']['admin']['cc'],
						),
						'Tech' => array(
							'First Name' => $res['response']['tech']['fname'],
							'Last Name' => $res['response']['tech']['lname'],
							'Organization' => $res['response']['tech']['org'],
							'Address 1' => $res['response']['tech']['street1'],
							'Address 2' => $res['response']['tech']['street2'],
							'City' => $res['response']['tech']['city'],
							'Postal Code' => $res['response']['tech']['pc'],
							'Region' => $res['response']['tech']['sp'],
							'Phone' => $res['response']['tech']['voice'],
							'Phone Code' => $res['response']['tech']['phone_code'],
							'Fax' => $res['response']['tech']['fax'],
							'Email' => $res['response']['tech']['email'],
							'Country' => $res['response']['tech']['cc'],
						),
					);
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_SaveContactDetails($params) 
{
	global $CONFIG;
	$values = array();
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		/* registrant */
		'name1' => ( str_replace(' ','',$params['contactdetails']['Registrant']['Organization']) !== '' ? EasyApi::from($params['contactdetails']['Registrant']['Organization']) : EasyApi::from($params['contactdetails']['Registrant']['First Name'].' '.$params['contactdetails']['Registrant']['Last Name'])),
		'name1' => EasyApi::from($params['contactdetails']['Registrant']['Organization']),
		'street11' => EasyApi::from($params['contactdetails']['Registrant']['Address 1']),
		'street21' => EasyApi::from($params['contactdetails']['Registrant']['Address 2']),
		'city1' => EasyApi::from($params['contactdetails']['Registrant']['City']),
		'pc1' => EasyApi::from(isset($params['contactdetails']['Registrant']['Postal Code']) ? $params['contactdetails']['Registrant']['Postal Code'] : $params['contactdetails']['Registrant']['Postcode']),
		'sp1' => EasyApi::from($params['contactdetails']['Registrant']['Region']),
		'cc1' => EasyApi::from($params['contactdetails']['Registrant']['Country']),
		'voice1' => EasyApi::from(isset($params['contactdetails']['Registrant']['Phone Code']) ? '+'.$params['contactdetails']['Registrant']['Phone Code'].'.'.$params['contactdetails']['Registrant']['Phone'] : $params['contactdetails']['Registrant']['Phone Number']),
		'fax1' => EasyApi::from($params['contactdetails']['Registrant']['Fax']),
		'email1' => EasyApi::from($params['contactdetails']['Registrant']['Email']),
		/* admin */
		'name2' => ( str_replace(' ','',$params['contactdetails']['Admin']['Organization']) !== '' ? EasyApi::from($params['contactdetails']['Admin']['Organization']) : EasyApi::from($params['contactdetails']['Admin']['First Name'].' '.$params['contactdetails']['Admin']['Last Name'])),
		'street12' => EasyApi::from($params['contactdetails']['Admin']['Address 1']),
		'street22' => EasyApi::from($params['contactdetails']['Admin']['Address 2']),
		'city2' => EasyApi::from($params['contactdetails']['Admin']['City']),
		'pc2' => EasyApi::from(isset($params['contactdetails']['Admin']['Postal Code']) ? $params['contactdetails']['Admin']['Postal Code'] : $params['contactdetails']['Admin']['Postcode']),
		'sp2' => EasyApi::from($params['contactdetails']['Admin']['Region']),
		'cc2' => EasyApi::from($params['contactdetails']['Admin']['Country']),		
		'voice2' => EasyApi::from(isset($params['contactdetails']['Admin']['Phone Code']) ? '+'.$params['contactdetails']['Admin']['Phone Code'].'.'.$params['contactdetails']['Admin']['Phone'] : $params['contactdetails']['Admin']['Phone Number']),
		'fax2' => EasyApi::from($params['contactdetails']['Admin']['Fax']),
		'email2' => EasyApi::from($params['contactdetails']['Admin']['Email']),
		/* tech */
		'name3' => ( str_replace(' ','',$params['contactdetails']['Tech']['Organization']) !== '' ? EasyApi::from($params['contactdetails']['Tech']['Organization']) : EasyApi::from($params['contactdetails']['Tech']['First Name'].' '.$params['contactdetails']['Tech']['Last Name'])),
		'street13' => EasyApi::from($params['contactdetails']['Tech']['Address 1']),
		'street23' => EasyApi::from($params['contactdetails']['Tech']['Address 2']),
		'city3' => EasyApi::from($params['contactdetails']['Tech']['City']),
		'pc3' => EasyApi::from(isset($params['contactdetails']['Tech']['Postal Code']) ? $params['contactdetails']['Tech']['Postal Code'] : $params['contactdetails']['Tech']['Postcode']),
		'sp3' => EasyApi::from($params['contactdetails']['Tech']['Region']),
		'cc3' => EasyApi::from($params['contactdetails']['Tech']['Country']),
		'voice3' => EasyApi::from(isset($params['contactdetails']['Tech']['Phone Code']) ? '+'.$params['contactdetails']['Tech']['Phone Code'].'.'.$params['contactdetails']['Tech']['Phone'] : $params['contactdetails']['Tech']['Phone Number']),
		'fax3' => EasyApi::from($params['contactdetails']['Tech']['Fax']),
		'email3' => EasyApi::from($params['contactdetails']['Tech']['Email']),
	);

	$res = EasyApi::execute('setcontacts', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_RegisterNameserver($params) 
{
	global $CONFIG;
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'ns' => $params['nameserver'],
		'ip' => $params['ipaddress'],
	);
	$res = EasyApi::execute('sethost', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_ModifyNameserver($params) 
{
	global $CONFIG;
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'ns' => $params['nameserver'],
		'ip' => $params['ipaddress'],
	);
	$res = EasyApi::execute('sethost', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}
function easy_DeleteNameserver($params) 
{
	global $CONFIG;
	$query = array(
		'username' => $params['Username'],
		'password' => $params['Password'],
		'domain' => $params['sld'].'.'.$params['tld'],
		'ns' => $params['nameserver'],
	);
	$res = EasyApi::execute('delhost', $query);
	if( isset($res['done']) ) {
		switch( (int)$res['done'] ) {
			case 1:
					$values['success'] = true;
				break;
			case 0:
					$values['error'] = '';
					foreach( $res['errors'] as $k => $v )
						$values['error'] .= $k . ' - ' . $v . ', ';
					$values['error'] = rtrim(EasyApi::to($values['error']),', ');
				break;
		}
	} else
		$values['error'] = 'Connection Problem';
	return $values;
}