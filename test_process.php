<?php
/**
 * Processing and sending the test results
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2017-2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */

require 'english_test_functions.php';

getPageHeader($_SERVER['SERVER_NAME']);
echo '<div class="paddingDiv">';

$emailText = '';
$user = readUserData();
$sheetsArray = createGoogleSheetsArray($user, $_ENV['TEST_PART_NUMBER']);

$questions = readQuestions($_ENV['QUESTIONS_FILE']);
$options = readOptions($_ENV['OPTIONS_FILE']);
$answers = readAnswers($_ENV['ANSWERS_FILE']);

$totalCorrectAtStart = $user['totalCorrect'];

for ($iQuestion = 0; $iQuestion < count($questions); $iQuestion++){
	$question = $questions[$iQuestion];
	$currentOptions = $options[$iQuestion];
	$answerParamName = 'question-' . ($iQuestion + 1) . '-answers';
	$userAnswer = $_POST[$answerParamName];
	if (isset($userAnswer)){
		$correctAnswers = $answers[$iQuestion];
		if ( in_array($userAnswer, $correctAnswers)) {
			$user['totalCorrect'] += 1;
		}
		$emailText = $emailText . reconstructAnswerText($question, $currentOptions, $correctAnswers, $userAnswer) . "<br>\r\n\n";
		array_push($sheetsArray, $currentOptions[$userAnswer-1]);
	} else {
		array_push($sheetsArray, '');
	}
}

$user['currentCorrect'] =  $user['totalCorrect'] - $totalCorrectAtStart;
$sheetsArray[4] = $user['currentCorrect'];

if ($user['src'] == "form") {
	if (($user['src'] == "form") && ( $user['firstName'] != 'Test' || $user['lastName'] != 'Test' )){
		sendEmailAndDropbox($_ENV['EMAIL_TO'], $_ENV['DROPBOX_PATH'], $user, $_ENV['TEST_NAME'], $_ENV['TEST_PART_NUMBER'], $emailText, get_level_name_description($user['totalCorrect'] + $user['skippedQuestions'], $_ENV['LEVEL_DATA_FILE'])['levelName'], $_ENV['DROPBOX_TOKEN'], explode(',',$_ENV['MUTE_KEYS']), $_ENV['EMAIL_FROM']);
		appendTestResultsToGoogleSpreadsheet($_ENV['GOOGLE_CREDENTIALS_FILE'], $_ENV['GOOGLE_SPREADSHEET_ID'], $_ENV['GOOGLE_SHEET_NAME'], $sheetsArray);
	}
}
if ( $user['src'] != "" ) {
	echo "<div class=\"results\"><p class=\"numAnswers\">" . $_ENV['RESULTS_STRING_1'] . $user['totalCorrect'] . $_ENV['RESULTS_STRING_2'] . $user['totalQuestions'] . ".</p></div>";
	if($user['totalCorrect'] >= $_ENV['NEXT_PART_THRESHOLD']){ 
		$formAction = $_ENV['NEXT_PART_URL'];
		$formButtonText = $_ENV['NEXT_PART_BUTTON_TEXT'];
	} else {
		$formAction = $_ENV['RESULTS_URL'];
		$formButtonText = $_ENV['RESULTS_BUTTON_TEXT'];
	}
	echo '<div style="buttonPadding">
	<div style="margin: 0 auto; display:table;">
	<form class="buttonForm" action="' . $formAction . '" method="post" id="proceed">';
	foreach($user as $key => $value )
	{
		echo '<input type="hidden" name="' . $key . '" value="' . $value . '">' . "\n";
	}
	echo '<input class="submit_button" type="submit" value="' . $formButtonText . '" />';
	echo '</form>
	<script type="text/javascript">
		document.getElementById("proceed").submit();
	</script>
	</div>';
} else {
	echo '<div style="margin:0 auto; display:table">
		<form class="buttonForm" action="' . $_ENV['UNREGISTERED_ACTION'] . '" method="post" id="quiz">
		<input class="results_button" type="submit" value="' . $_ENV['UNREGISTERED_BUTTON_TEXT'] .'" />
		</form>
	</div>';
}
echo '</div>';
getPageFooter($_SERVER['SERVER_NAME']);
?>
