<?php
/**
 * Displaying the test results
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2017-2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */
require 'english_test_functions.php';
getPageHeader($_SERVER['SERVER_NAME']);

echo "<div class=\"paddingResults\">\n<div class=\"results\">";

$user = readUserData();

if ($user['src'] != "") {
	$levelNameDesc = get_level_name_description($user['totalCorrect'] + $user['skippedQuestions'], $_ENV['LEVEL_DATA_FILE']);

	echo "<p>" . $_ENV['RESULTS_STRING_1'] . $user['totalCorrect'] . $_ENV['RESULTS_STRING_2'] . $user['totalQuestions'] . "</p>";
	echo "<p>" . get_foreword($_SERVER['SERVER_NAME'], $_ENV['LEVEL_DATA_FILE']) . $levelNameDesc['levelName'] . "</p>";
	echo "<p>" . $levelNameDesc['levelDesc'] . "</p>";
	echo "<p>" . get_results_text($_SERVER['SERVER_NAME'], $_ENV['LEVEL_DATA_FILE']) . "</p>";
	if(array_key_exists('SHOW_ANSWERS', $_ENV) && ($_ENV['SHOW_ANSWERS'] == "True")){
		echo '<p>' . urldecode($_POST['answers']) . '</p>' . "\n";
	}
} else { 
	echo "<div style=\"margin:0 auto; display:table\">";
	echo "<form class=\"buttonForm\" action=\"" . $_ENV['UNREGISTERED_ACTION'] . "\" method=\"post\" id=\"quiz\">";
	echo "<input class=\"results_button\" type=\"submit\" value=\"" . $_ENV['UNREGISTERED_BUTTON_TEXT'] . "\" />";
	echo "</form>\n</div>";
}
echo "</div>\n</div>";
getPageFooter($_SERVER['SERVER_NAME']);
?>
