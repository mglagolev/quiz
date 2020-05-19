<?php
/**
 * Essential functions for the quiz itself
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2017-2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */

function readUserData(){
// Read the user credentials from POST request and initialize the values if necessary
	$user = array();
	foreach ($_POST as $key => $value) {
		if ( substr($key, 0, 9)  != 'question-' ) {
			$user["$key"] = $value;
		}
	}
	if ( empty($user["uniqueId"]) ){
		$user["uniqueId"] = uniqid();
	}
	if ( empty($user["totalCorrect"]) ){
		$user["totalCorrect"] = 0;
	}
	if ( empty($user["fillTime"]) ){
		$dt = new DateTime("now", new DateTimeZone($_ENV['TIMEZONE']));	
		$user["fillTime"] = $dt->format('d-m-Y H:i:s');
	}
	if ( empty($user["email"]) ){
		$user["email"] = $_POST['your-email'];
	}
	if ( empty($user["src"]) ){
		$user["src"] = $_POST['usersrc'];
	}
	return $user;
}


function sendEmailAndDropbox($to, $dropboxPath, $user, $testName, $testPartNumber = '', $emailText = '', $levelName = '', $dropboxToken, $muteKeys, $from = $_ENV['EMAIL_FROM']){
	if ( isset($user['email']) && !empty($user['email']) ) {
		$reply_to = $user['email'];
	} else {
		$reply_to = $_ENV['EMAIL_FROM'];
	}
	if ( $testPartNumber == 0 ){
		$user['currentCorrect'] = '';
		$testPartName = $testName . ' form';
		$emailText = '';
		$levelName = '';
	} else {
		$testPartName = $testName . ' part ' . $testPartNumber;
	}
	$message_contents = make_message_contents2($user, $testPartNumber, $emailText, $levelName, $muteKeys);
	$subject = $user['firstName'] . ' ' . $user['lastName'] . ' - ' . $testPartName . ' - ' . $user['fillTime'];
	if ( !empty($to) ) {
		mail_attachment($to, $subject, $message_contents, $from, $reply_to, $message_contents);
	}
	$dropbox_filename = $dropboxPath . $subject . '.html';
	upload_string_contents_to_dropbox($dropbox_filename, $message_contents, $dropboxToken);
}


function get_level_name_description($correctAnswers, $levelDataFile){
	$levelData = json_decode(file_get_contents($levelDataFile), true);
	foreach ( $levelData["level"] as $level ){
		if (( !array_key_exists("min", $level) || ($level["min"] <= $correctAnswers) ) && ( !array_key_exists("max", $level) || ($level["max"] >= $correctAnswers) )) {
			return array("levelName" => $level["name"], "levelDesc" => $level["description"]);
		}
	}
}


function get_results_text($server_name, $levelDataFile){
	$resultsTextData = json_decode(file_get_contents($levelDataFile), true);
	return $resultsTextData["text"]["$server_name"];
}

function get_foreword($server_name, $levelDataFile){
	$resultsTextData = json_decode(file_get_contents($levelDataFile), true);
	return $resultsTextData["foreword"]["$server_name"];
}

//https://stackoverflow.com/questions/4586557/php-send-email-with-attachment
//http://www.openspf.org/Best_Practices/Webgenerated
function mail_attachment($to, $subject, $message, $sender_address, $user_address, $file_contents) {
  $eol = PHP_EOL;
  $filename = md5(uniqid(time())) . '.html';
  $file_size = filesize($file);
  $content = chunk_split(base64_encode($file_contents));
  $uid = md5(uniqid(time()));
  $user_address = str_replace(array("\r", "\n"), '', $user_address); // to prevent email injection
  $header = "Sender: ".$sender_address.$eol
      ."From: ".$user_address.$eol
      ."Reply-To: ".$user_address.$eol
      ."MIME-Version: 1.0".$eol
      ."Content-Type: multipart/mixed; boundary=\"".$uid."\"".$eol;
  $emessage = "--".$uid.$eol
      ."Content-type:text/html; charset=utf-8".$eol
      ."Content-Transfer-Encoding: 7bit".$eol
      .$message.$eol
      ."--".$uid.$eol;
   if(!empty($file_contents)){
   $message = $message
      ."Content-Type: application/octet-stream; name=\"".$filename."\"".$eol
      ."Content-Transfer-Encoding: base64".$eol
      ."Content-Disposition: attachment; filename=\"".$filename."\"".$eol
      .$content.$eol
      ."--".$uid."--";
   }
  return mail($to, $subject, $emessage, $header, "-f " . $user_address);
}

function make_message_contents2($user, $testPart, $emailText, $levelName, $muteKeys){
	$style = "<style>\n.field {\nfont-style: italic;\n}\n.value {\nfont-weight: bold;\n}\n</style>\n";
	$message_header = "<!DOCTYPE html><html><meta http-equiv=\"content-language\" content=\"ru-RU\"><meta charset=\"UTF-8\"><head><title>English placement test results email</title>\n". $style . "</head><body>\n";
	$message_footer = "</body></html>";
	$message_contents = $message_header;
	foreach ( $user as $key => $value ) {
		if ( !empty($value) && !in_array($key, $muteKeys)) { $message_contents .= "\n<p class=\"field\">" . $key . ":</p>\n<p class=\"value\">" . $value . "</p>\n"; }
	}
	$message_contents .= "<br><br>\n";
	if (!empty($emailText)){
		$message_contents .= "Correct answers (part ".$testPart."): " . $user['currentCorrect'] . " (<strong>" . $levelName . "</strong>)\n<br><br>\nAnswers:" . "\n" . "\n<br><br>\n" . $emailText . $message_footer;
	}
	return $message_contents;
}


function readQuestions($questionsFile)
{
	$questionDelimiter = '///'; // Example question: 1. /// ______ /// name is robert.
	$fileLines = file($questionsFile);
	$questions = array();
	foreach ($fileLines as $line) {
		array_push($questions, explode ( $questionDelimiter, trim($line)));
	}
	return $questions;
}

function readOptions($optionsFile)
{
	$optionDelimiter = '///';
	$fileLines = file($optionsFile);
	$options = array();
	foreach ($fileLines as $line) {
		array_push($options, explode ( $optionDelimiter, trim($line)));
	}
	return $options;
}

function readAnswers($answersFile)
{
	$answersDelimiter = ' ';
	$fileLines = file($answersFile);
	$answers = array();
	foreach ($fileLines as $line) {
		array_push($answers, explode ( $answersDelimiter, trim($line)));
	}
	return $answers;
}

function reconstructAnswerText($question, $currentOptions, $correctAnswers, $userAnswer)
{
	$answerPlaceholder = '______';
	$answerTextHighlightStart = array(
		"correct" => '<span style="font-weight:bold; color:#00FF00;">',
		"incorrect" => '<span style="font-weight:bold; color:#FF0000;">',
		);
	$answerTextHighlightEnd = array(
		"correct" => '</span>',
		"incorrect" => '</span>',
		);
	if ( in_array($userAnswer, $correctAnswers)){
		$answerStatus = "correct";
	} else {
		$answerStatus = "incorrect";
	}
	$optionInternalDelimiter = '//';
	$userOption = $currentOptions[$userAnswer - 1];
	$userOptionArray = explode( $optionInternalDelimiter, trim($userOption));
	for ($iWord = 0; $iWord < count($userOptionArray); $iWord++){
		$userOptionArray[$iWord] = $answerTextHighlightStart[$answerStatus] . $userOptionArray[$iWord] . $answerTextHighlightEnd[$answerStatus];
	}
	$iWord = 0;
	for ($iAnswerTextPart = 0; $iAnswerTextPart < count($question); $iAnswerTextPart++){
		if ($question[$iAnswerTextPart] == $answerPlaceholder){
			$question[$iAnswerTextPart] = $userOptionArray[$iWord];
			$iWord++;
		}
	}
	return implode(' ',  $question);	
}


function composeQuestionText($iQuestion, $question, $currentOptions){
	echo("<br>\r\n");
	echo("<h3 class=\"question\">");
	echo(implode(' ', $question));
	echo("</h3>\r\n");
	for($iOption = 0; $iOption < count($currentOptions); $iOption++){
		echo("<div>\r\n");
		echo("<input type=\"radio\" name=\"question-" . ($iQuestion + 1) . "-answers\" id=\"question-" . ($iQuestion + 1) . "-answers-" . ($iOption+1) . "\" value=\"" . ($iOption + 1) . "\" />\r\n");
		echo("<label class=\"option\" for=\"question-" . ($iQuestion + 1) . "-answers-" . ($iOption + 1) . "\">" . $currentOptions[$iOption] . "</label>\r\n");
		echo("</div>\r\n");
	}
	echo("\r\n");	
}


function getPageHeader($serverName){
	if ( strpos($serverName, WORDPRESS_SITE) !== false ){
		get_header();
		$css_link = '/wp-content/themes/TheFox/english_test.css';
	} else {
		get_standalone_header();
		$css_link = './outcomes_test.css';
	}
	echo("<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $css_link . "\">\n");
}


function getPageFooter($serverName){
	if ( strpos($serverName, WORDPRESS_SITE) !== false ){
		get_footer();
	} else {
		get_standalone_footer();
	}
}
?>
