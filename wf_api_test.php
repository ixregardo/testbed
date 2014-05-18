<html>
<head>
<title>Wufoo API Test</title>
<link rel="stylesheet" type="text/css" href="http://www.dianem.hostei.com/stylesheet.css">
</head>
<body>

<h1>Wufoo API Test</h1>

<form id="acct_info" name="acct_info" action="" method="post">
Your Wufoo subdomain: <br><input type="text" name="subdomain" id="subdomain"><br><br>
Your Wufoo API key: <br><input type="text" name="api_key" id="api_key"><br>
<input type="submit" name="submit" id="submit" value="Submit">
</form>
<br><br>

<?php

$subdomain = $_POST[subdomain];
$api_key = $_POST[api_key];

//Remove warnings
error_reporting(E_ERROR | E_PARSE);

if (empty($subdomain)) { echo "<p>Please enter your account info above</p>";}

else {

//Call setup (formInfo)
$curl = curl_init('https://' . $subdomain . '.wufoo.com/api/v3/forms.xml?includeTodayCount=true');       
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);                         
curl_setopt($curl, CURLOPT_USERPWD, $api_key . ':randomvariable');   
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                     
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                          
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);                           
curl_setopt($curl, CURLOPT_USERAGENT, 'Diane is screwing around with the Wufoo API');            

//Call (formInfo)
$formInfo = curl_exec($curl);                                           
$formInfoStatus = curl_getinfo($curl);  

if ($formInfoStatus['http_code'] == 200) {
                   
	echo "Request successful! You got:<br><br>";

	//Display test
	$parsedFormInfo = new SimpleXMLElement($formInfo);	

	echo "<table><tr class=\"tableHeader\">
		<td class=\"formName\">Name</td>
		<td>Public</td>
		<td>Entries<br>(Today)</td>
		<td>Entries<br>(All-Time)</td>
		</tr>";
	foreach ($parsedFormInfo->Form as $Form) {
		echo "<tr>";
		echo "<td class=\"formName\">" . $Form->Name . "</td>";
		if ($Form->IsPublic == 1) {
			echo "<td>Yes</td>";
		} else {echo "<td>No</td>";}
		echo "<td>" . $Form->EntryCountToday . "</td>";

		//Call setup (entryCount)
		$curl = curl_init('https://' . $subdomain . '.wufoo.com/api/v3/forms/' . $Form->Hash . '/entries/count.xml');       
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);                         
		curl_setopt($curl, CURLOPT_USERPWD, $api_key . ':randomvariable');   
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);                     
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);                          
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);                           
		curl_setopt($curl, CURLOPT_USERAGENT, 'Diane is screwing around with the Wufoo API');            

		//Call & parse (entryCount)
		$entryCount = curl_exec($curl);                                           
		$entryCountStatus = curl_getinfo($curl);
		$parsedEntryCount = new SimpleXMLElement($entryCount);

		echo "<td>" . $parsedEntryCount . "</td>";

		echo "</tr>";
	}

	echo "</table>";

	//Original XML
	//echo "<br><br>";
	//echo "<h3>Here's the original XML output:</h3><pre>" . print_r($parsedFormInfo, true) . "</pre>";


} else { echo 'Call Failed '.print_r($formInfoStatus);}

}

?>

</body>
</html>