<?php
/**
 * Functions for sending results to Dropbox
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2017 - 2019 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */
function upload_string_contents_to_dropbox($remote_filename, $file_contents, $token)
{

	$test_results_dir = __DIR__.'/tmp_english_test_results';
	$test_results_prefix = 'test_results_';

	ignore_user_abort(true);
	
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/Auth.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/FileProperties.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/FileRequests.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/Files.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/Misc.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/Paper.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/Sharing.php';
	require __DIR__.'/Dropbox-v2-PHP-SDK-master/sdk/Dropbox/Users.php';


	$tmp_filename = tempnam($test_results_dir, $test_results_prefix);

	file_put_contents($tmp_filename, $file_contents);

	// Initialize Dropbox client
	$dropbox = new Dropbox\Dropbox($token);

	// Upload a file, overwriting if the file already exists in Dropbox
	$dropbox->files->upload($remote_filename, $tmp_filename, "overwrite");

	unlink($tmp_filename);

}
?>
