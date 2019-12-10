<?php /* Template Name: OutcomesProfilePart2 */ ?>
<?php
/**
 * Outcomes test personal data submission - page 2
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */

require 'english_test_functions.php';
getPageHeader($_SERVER['SERVER_NAME']);
$user = readUserData();
?>
<html>
<meta http-equiv="content-language" content="ru-RU">
<meta charset="UTF-8"> 
<head>
<link rel="stylesheet" type="text/css" href="/wp-content/themes/TheFox/english_test.css">
<title>Learning Profile Survey</title>
</head>
<body>
<div class="paddingDiv">
<form action="/outcomes-test" method="post" class="participantInfo">
<div style="display: none;">
<?php
foreach($user as $key => $value)
{
	echo "<input type=\"hidden\" name=\"" . $key . "\" value=\"" . $value . "\">\n";
}
?>
</div>
<p class="credentials">Other languages you speak:<span class="req_field"></span><br />
    <span class="credentials"><input type="text" name="Other languages you speak" value="" size="40" aria-invalid="false"/></span>
</p>
<p class="credentials">What type of materials do you like to read in your language:<span class="req_field"></span><br />
    <span class="credentials"><input type="text" name="What type of materials do you like to read in your language" value="" size="40" aria-invalid="false"/></span>
</p>
<p class="credentials">What do you write in your language:<span class="req_field"></span><br />
	<input type="radio" name="What do you write in your language" value="Emails">Emails<br>
	<input type="radio" name="What do you write in your language" value="Stories">Stories<br>
	<input type="radio" name="What do you write in your language" value="Lists"> Lists <br> 
	<input type="radio" name="What do you write in your language" value="Notes"> Notes <br>
	<input type="radio" name="What do you write in your language" value="Other"> Other: <br>
    	<span class="credentials"><input type="text" name="What other stuff do you write" value="" size="40" aria-invalid="false"/></span>
</p>
<p class="credentials">Have you studied English before?<span class="req_field">*</span><br />
	<input type="radio" name="Have you studied English before" value="yes" required>Yes<br>
	<input type="radio" name="Have you studied English before" value="no" required>No<br>
	<p class="credentials">Where?<span class="req_field"></span><br />	
		<span class="credentials"><input type="text" name="Where have you studied English before" value="" size="40" aria-invalid="false"/></span>
	</p>
</p>
<p class="credentials">Describe your ability in English: (choose the best description)<span class="req_field"></span><br />
<p class="credentials">Understanding is:<span class="req_field">*</span><br />
	<input type="radio" name="Understanding is" value="always difficult" required>always difficult<br>
	<input type="radio" name="Understanding is" value="sometimes difficult" required>sometimes difficult<br>
	<input type="radio" name="Understanding is" value="never difficult" required>never difficult<br>
</p>
<p class="credentials">Speaking is:<span class="req_field">*</span><br />
	<input type="radio" name="Speaking is" value="always difficult" required>always difficult<br>
	<input type="radio" name="Speaking is" value="sometimes difficult" required>sometimes difficult<br>
	<input type="radio" name="Speaking is" value="never difficult" required>never difficult<br>
</p>
<p class="credentials">Reading is:<span class="req_field">*</span><br />
	<input type="radio" name="Reading is" value="always difficult" required>always difficult<br>
	<input type="radio" name="Reading is" value="sometimes difficult" required>sometimes difficult<br>
	<input type="radio" name="Reading is" value="never difficult" required>never difficult<br>
</p>
<p class="credentials">Writing is:<span class="req_field">*</span><br />
	<input type="radio" name="Writing is" value="always difficult" required>always difficult<br>
	<input type="radio" name="Writing is" value="sometimes difficult" required>sometimes difficult<br>
	<input type="radio" name="Writing is" value="never difficult" required>never difficult<br>
</p>
</p>
<p class="credentials">Why do you want to learn English?<span class="req_field"></span><br />
    <span class="credentials"><input type="text" name="Why do you want to learn English" value="" size="40" aria-invalid="false"/></span> </p>
<p class="credentials">What is the most important thing you want to learn?<span class="req_field"></span><br />
    <span class="credentials"><input type="text" name="What is the most important thing you want to learn" value="" size="40" aria-invalid="false"/></span> </p>
<p class="credentials">What questions do you have?<span class="req_field"></span><br />
    <span class="credentials"><input type="text" name="What questions do you have" value="" size="40" aria-invalid="false"/></span> </p>
<p><input class="submit_button" type="submit" value="Начать тест" /></p>
</form>
</div>
</body>
</html>
<?php getPageFooter($_SERVER['SERVER_NAME']); ?>

