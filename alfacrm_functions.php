<?
/**
 * Functions for sending results to AlfaCRM
 *
 * @author     Mikhail Glagolev <mikhail.glagolev@gmail.com>
 * @copyright  2018 for dear Anita
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html  GNU General Public License 3.0
 * @link       https://github.com/mglagolev/quiz
 */
function alfa_api_request($url, $load){

    $curl=curl_init();
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_URL,$url);
    if ( !empty($load) ){
	    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
	    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($load));
	    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    }
    curl_setopt($curl,CURLOPT_HEADER,false);
    $out=curl_exec($curl);
}

function alfa_api_request2($url, $load){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($load));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$out = curl_exec($ch);
	curl_close($ch);
}
?>
