<?php
/**
 * Questionnaire
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2017-2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */

require 'english_test_functions.php';

getPageHeader($_SERVER['SERVER_NAME']);

$user = readUserData();

$user['totalQuestions'] += 50;

if ( ! $user['formSubmitted'] || ! isset($user['formsSubmitted']) ) {
	if (($user['src'] == "form") && ( $user['firstName'] != 'Test' || $user['lastName'] != 'Test' )){
		sendEmailAndDropbox($_ENV['EMAIL_TO'], $_ENV['DROPBOX_PATH'], $user, $_ENV['TEST_NAME'], 0, '', '', $_ENV['DROPBOX_TOKEN'], explode(',',$_ENV['MUTE_KEYS']), $_ENV['EMAIL_FROM']);
		$updateResponse = appendTestResultsToGoogleSpreadsheet($_ENV['GOOGLE_CREDENTIALS_FILE'], $_ENV['GOOGLE_SPREADSHEET_ID'], $_ENV['TEST_NAME'] . " Overview", createGoogleSheetsArray($user));
		if ( $updateResponse['updates']['updatedRows'] == 1 ){
			$user['formsSubmitted'] = True;
		}
	}
}
echo "<div class=\"paddingDiv\">";
echo "<form class=\"questions\" action=\"". $_ENV['SUBMIT_ACTION'] . "\" method=\"post\" id=\"quiz\">";
foreach($user as $key => $value)
{
	echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\">\n";
}

$questions = readQuestions($_ENV['QUESTIONS_FILE']);
$options = readOptions($_ENV['OPTIONS_FILE']);
$answers = readAnswers($_ENV['ANSWERS_FILE']);

for ($iQuestion=0; $iQuestion < count($questions); $iQuestion++){
	composeQuestionText($iQuestion, $questions[$iQuestion], $options[$iQuestion]);
}
echo "<br>\n<p class=\"submit_button\">";
echo "<input class=\"submit_button\" type=\"submit\" value=\"" . $_ENV['SUBMIT_BUTTON_TEXT'] . "\" />\n</p>\n</form>\n</div>";
getPageFooter($_SERVER['SERVER_NAME']);
?>
