<?php /* Template Name: TestPart1 */ ?>
<?php
/**
 * Pearson English language test - Part 1 questionnaire
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */

require '/home/c/cn10180/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create('/home/c/cn10180/', 'pearson.env');
$dotenv->load();
$dotenv = Dotenv\Dotenv::create('/home/c/cn10180/', 'pearson_part1.env');
$dotenv->load();

require 'test_questions.php';
?>
