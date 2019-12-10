<?php
/**
 * Functions for sending results to Google Sheets
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2018 - 2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */
define("GOOGLE_API_CLIENT", '/home/c/cn10180/google-api-php-client-2.2.2/vendor/autoload.php');

function createGoogleSheetsArray($user, $testPartNumber = 0)
{	
	$sheetsArray = array();
	$sheetsArray[] = $user['uniqueId'];
	$sheetsArray[] = $user['fillTime'];
	$sheetsArray[] = $user['firstName'];
	$sheetsArray[] = $user['lastName'];
	if ( $testPartNumber == 0 ){
		$sheetsArray[] = $user['email'];
		$sheetsArray[] = "=indirect(\"'" . $_ENV['TEST_NAME'] . " Part 1'!E\"&match(indirect(address(row(),1)),'" . $_ENV['TEST_NAME'] . " Part 1'!A:A, 0))";
		$sheetsArray[] = "=indirect(\"'" . $_ENV['TEST_NAME'] . " Part 2'!E\"&match(indirect(address(row(),1)),'" . $_ENV['TEST_NAME'] . " Part 2'!A:A, 0))";
		$sheetsArray[] = "=sumif(indirect(address(row(),column()-2)):indirect(address(row(),column()-1)),\"<>#N/A\")";
		$sheetsArray[] = "=IF(indirect(address(row(),column()-1))<21,\"Below Elementary\", IF(AND(indirect(address(row(),column()-1))>20,indirect(address(row(),column()-1))<36), \"Elementary\", IF(AND(indirect(address(row(),column()-1))>35,indirect(address(row(),column()-1))<61), \"Pre-Intermediate\", IF(AND(indirect(address(row(),column()-1))>60,indirect(address(row(),column()-1))<86), \"Intermediate\", IF(indirect(address(row(),column()-1))>85, \"Upper Intermediate\")))))";
		$sheetsArray[] = $user['company'];
		$sheetsArray[] = "'" . $user['phone'];
		$sheetsArray[] = $user['message'];
	} else {
		$sheetsArray[] = '';
	}
	return $sheetsArray;
}


function appendTestResultsToGoogleSpreadsheet($credentialsFile, $spreadsheetId, $sheetName, $resultsArray)
{

	require GOOGLE_API_CLIENT;
	/*
	Instructions on setting up Google API are taken from here:

	https://www.fillup.io/post/read-and-write-google-sheets-from-php/

	Authentication process (creating/updating the token) is copied from here (omitting the ServiceRequestFactory):

	https://www.twilio.com/blog/2017/03/google-spreadsheets-and-php.html

	*/
	putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsFile);
	$client = new Google_Client;
	$client->useApplicationDefaultCredentials();

	$client->setApplicationName("Spreadsheet trial");
	$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);

	if ($client->isAccessTokenExpired()) {
    		$client->refreshTokenWithAssertion();
	}

	$accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];

	$sheets = new \Google_Service_Sheets($client);

	$range = '\'' . $sheetName . '\'!A:BF';

	$appendBody = new Google_Service_Sheets_ValueRange([
		'majorDimension' => 'ROWS',
		'values' => [$resultsArray],
		]);

	$response = $sheets->spreadsheets_values->append($spreadsheetId, $range, $appendBody, [ 'valueInputOption' => "USER_ENTERED" ]);

	return $response;
}

?>
